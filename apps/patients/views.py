import uuid
import random
import string
from django.db import models
from django.views.generic import DetailView, View, ListView
from django.shortcuts import get_object_or_404, redirect, render
from django.contrib.auth.mixins import LoginRequiredMixin
from django.contrib.auth.decorators import login_required
from django.views.decorators.http import require_POST
from django.http import Http404, HttpResponse, JsonResponse
from django.utils.text import slugify
from django.contrib import messages
from apps.core.mixins import PatientAccessRequiredMixin
from apps.core.utils import (
    registrar_auditoria,
    obtener_paciente_autorizado,
)
from .models import Paciente, PacienteCuidador, ContactoEmergencia
from .forms import PacienteForm, ContactoEmergenciaForm
from .services import (
    asegurar_imagen_qr_sos,
    buscar_paciente_por_codigo_sos,
    construir_url_sos_publica,
    obtener_codigo_sos_corto,
)


class ExpedienteDetailView(LoginRequiredMixin, PatientAccessRequiredMixin, DetailView):
    """
    Vista MVT para el Expediente Clínico Integral del Paciente.
    Resuelve por UUID (`qr_token`), verifica relación directa con `request.user`
    y asegura la generación automática del código QR SOS en `/media/qr_sos/`.
    """
    model = Paciente
    template_name = 'patients/expediente.html'
    context_object_name = 'paciente'
    slug_field = 'qr_token'
    slug_url_kwarg = 'uuid'

    def get_context_data(self, **kwargs):
        context = super().get_context_data(**kwargs)
        paciente = self.object

        self.request.session['paciente_activo_id'] = paciente.id
        self.request.session['paciente_activo_nombre'] = paciente.nombre_completo

        # Asegurar que el archivo PNG del QR SOS exista en /media/qr_sos/
        _, qr_media_url = asegurar_imagen_qr_sos(paciente, request=self.request)
        url_sos_publica = construir_url_sos_publica(self.request, paciente)
        codigo_sos_corto = obtener_codigo_sos_corto(paciente)

        context['permisos'] = self.permisos
        context['qr_media_url'] = qr_media_url
        context['url_sos_publica'] = url_sos_publica
        context['codigo_sos_corto'] = codigo_sos_corto
        context['contactos_emergencia'] = paciente.contactos_emergencia.order_by('-es_prioritario', 'nombre')
        context['medicamentos_activos'] = paciente.medicamentos.filter(activo=True).order_by('nombre_comercial')
        context['ultimos_signos'] = paciente.signos_vitales.order_by('-created_at')[:7]
        context['doctores_asignados'] = paciente.doctores_tratantes.all()

        registrar_auditoria(self.request, 'EXPEDIENTE_CONSULTADO', paciente=paciente, detalles="Consulta integral de expediente")
        return context


class SeleccionarPacienteView(LoginRequiredMixin, View):
    """
    Permite al familiar o cuidador cambiar de paciente activo en la sesión.
    Zero Trust: Valida en el servidor que `request.user` tenga relación directa con ese paciente.
    """
    def post(self, request, *args, **kwargs):
        identificador = request.POST.get('paciente_uuid') or request.POST.get('paciente_id')
        if not identificador:
            messages.warning(request, "Debe seleccionar un paciente válido.")
            return redirect('core:dashboard')

        paciente = obtener_paciente_autorizado(request, identificador=identificador, obligatorio=True)

        request.session['paciente_activo_id'] = paciente.id
        request.session['paciente_activo_nombre'] = paciente.nombre_completo
        messages.success(request, f"Contexto activo cambiado a: {paciente.nombre_completo}")

        next_url = request.POST.get('next')
        if next_url and next_url.startswith('/') and not next_url.startswith('//'):
            return redirect(next_url)
        return redirect('core:dashboard')


class FichaEmergenciaPublicaView(View):
    """
    Ficha Médica de Emergencia SOS (Triage para Primeros Respondientes).
    - Soporta lectura directa desde URL pública del QR (`/sos/<token>`) sin requerir app instalada.
    - Soporta ingreso manual de código corto (`a1b2c3d4`) si la cámara falla o el QR está borroso.
    - Soporta respuesta JSON (`?format=json` o `Accept: application/json`) para el escáner nativo en React Native / Expo.
    - Privacidad Zero Trust: Expone ÚNICAMENTE datos de supervivencia (Tipo de sangre, Alergias,
      Padecimientos críticos, Medicación activa de urgencia y Contactos telefónicos SOS).
      Jamás incluye direcciones físicas, correos electrónicos, cuentas ni historial clínico completo.
    - Incluye cabeceras `X-Robots-Tag: noindex, nofollow, noarchive` para impedir indexación en Google.
    """
    def get(self, request, token=None, *args, **kwargs):
        token_raw = (token or request.GET.get('token') or request.GET.get('codigo') or '').strip()

        # Si viene por query param ?codigo= desde el buscador manual, redirigir a la URL limpia /sos/<codigo>
        if not token and request.GET.get('codigo'):
            limpio = ''.join(c for c in token_raw if c.isalnum() or c in '-_').lower()
            if limpio:
                return redirect('core:sos_publico_directo', token=limpio)

        paciente = None
        codigo_no_encontrado = False

        if token_raw:
            paciente = buscar_paciente_por_codigo_sos(token_raw)
            if not paciente:
                codigo_no_encontrado = True
        else:
            if request.user.is_authenticated:
                paciente = obtener_paciente_autorizado(request, obligatorio=False)

        contactos = []
        medicamentos = []
        codigo_corto = ''

        if paciente:
            contactos = list(
                paciente.contactos_emergencia.order_by('-es_prioritario', 'nombre').values(
                    'nombre', 'parentesco', 'telefono', 'es_prioritario'
                )
            )
            medicamentos = list(
                paciente.medicamentos.filter(activo=True).order_by('nombre_comercial').values(
                    'nombre_comercial', 'dosis', 'frecuencia_horas'
                )
            )
            codigo_corto = obtener_codigo_sos_corto(paciente)
            registrar_auditoria(
                request,
                'FICHA_SOS_CONSULTADA',
                paciente=paciente,
                detalles=f"Consulta pública de triage SOS (código: {codigo_corto})",
            )

        # Soporte nativo para escáner de App Móvil (React Native / Expo)
        wants_json = (
            request.GET.get('format') == 'json'
            or 'application/json' in request.headers.get('Accept', '')
        )
        if wants_json:
            if not paciente:
                resp = JsonResponse(
                    {'ok': False, 'error': 'Código SOS no encontrado o inválido.'},
                    status=404,
                )
                resp['X-Robots-Tag'] = 'noindex, nofollow, noarchive'
                return resp

            resp = JsonResponse({
                'ok': True,
                'codigo_sos': codigo_corto,
                'paciente': {
                    'nombre_completo': paciente.nombre_completo,
                    'fecha_nacimiento': paciente.fecha_nacimiento.isoformat(),
                    'tipo_sangre': paciente.tipo_sangre,
                    'alergias_criticas': paciente.alergias or 'Ninguna reportada',
                    'padecimientos_cronicos': paciente.enfermedades_cronicas or 'Sin antecedentes registrados',
                    'consideraciones_cognitivas': paciente.notas_cognitivas or '',
                },
                'medicamentos_activos': medicamentos,
                'contactos_emergencia': contactos,
            })
            resp['X-Robots-Tag'] = 'noindex, nofollow, noarchive'
            return resp

        response = render(request, 'patients/emergencia_publica.html', {
            'paciente': paciente,
            'contactos': contactos,
            'medicamentos': medicamentos,
            'codigo_sos_corto': codigo_corto,
            'token_buscado': token_raw,
            'codigo_no_encontrado': codigo_no_encontrado,
        })
        response['X-Robots-Tag'] = 'noindex, nofollow, noarchive'
        return response


@login_required
def servir_qr_sos_view(request, uuid):
    """Sirve la imagen PNG del código QR SOS almacenada en `/media/qr_sos/`."""
    paciente = obtener_paciente_autorizado(request, identificador=uuid, obligatorio=True)
    file_path, _ = asegurar_imagen_qr_sos(paciente, request=request)
    with open(file_path, 'rb') as f:
        response = HttpResponse(f.read(), content_type='image/png')
    response['Cache-Control'] = 'private, max-age=3600'
    response['X-Robots-Tag'] = 'noindex, nofollow'
    return response


@login_required
def descargar_qr_sos_view(request, uuid):
    """
    1. 'Descargar Imagen': Baja un archivo .png en alta resolución
    para que el familiar lo lleve en su celular.
    """
    paciente = obtener_paciente_autorizado(request, identificador=uuid, obligatorio=True)
    file_path, _ = asegurar_imagen_qr_sos(paciente, request=request, forzar_regeneracion=True)
    safe_name = slugify(paciente.nombre_completo) or str(paciente.codigo_sos_corto)
    filename = f"VitalSenior-QR-SOS-{safe_name}.png"

    with open(file_path, 'rb') as f:
        response = HttpResponse(f.read(), content_type='image/png')
    response['Content-Disposition'] = f'attachment; filename="{filename}"'
    response['X-Robots-Tag'] = 'noindex, nofollow'
    registrar_auditoria(request, 'QR_SOS_DESCARGADO', paciente=paciente, detalles=f"Descarga PNG ({filename})")
    return response


@login_required
def imprimir_tarjeta_sos_view(request, uuid):
    """
    2. 'Imprimir Tarjeta': Abre una nueva pestaña optimizada para impresión (@media print)
    con el tamaño exacto de una tarjeta de crédito / credencial de elector (85.60mm x 53.98mm).
    """
    paciente = obtener_paciente_autorizado(request, identificador=uuid, obligatorio=True)
    asegurar_imagen_qr_sos(paciente, request=request)
    url_sos_publica = construir_url_sos_publica(request, paciente)
    codigo_sos_corto = obtener_codigo_sos_corto(paciente)
    contactos = paciente.contactos_emergencia.order_by('-es_prioritario', 'nombre')[:2]

    registrar_auditoria(request, 'TARJETA_SOS_IMPRESA', paciente=paciente, detalles="Vista de impresión de credencial SOS")

    response = render(request, 'patients/tarjeta_sos_print.html', {
        'paciente': paciente,
        'url_sos_publica': url_sos_publica,
        'codigo_sos_corto': codigo_sos_corto,
        'contactos': contactos,
    })
    response['X-Robots-Tag'] = 'noindex, nofollow'
    return response


class PacienteListView(LoginRequiredMixin, ListView):
    model = Paciente
    template_name = 'patients/paciente_list.html'
    context_object_name = 'pacientes'

    def get_queryset(self):
        user = self.request.user
        if user.rol in ['administrador', 'admin']:
            return Paciente.objects.all().order_by('nombre')
        elif user.rol in ['doctor', 'medico']:
            doctor = getattr(user, 'perfil_doctor', None)
            return doctor.pacientes_asignados.all().order_by('nombre') if doctor else Paciente.objects.none()
        else:
            return Paciente.objects.filter(
                models.Q(usuario=user) | models.Q(cuidadores=user)
            ).distinct().order_by('nombre')


class PacienteCreateView(LoginRequiredMixin, View):
    def get(self, request):
        return render(request, 'patients/paciente_form.html', {'titulo': 'Registrar Nuevo Adulto Mayor'})

    def post(self, request):
        form = PacienteForm(request.POST)
        if not form.is_valid():
            messages.error(request, "Verifica los datos ingresados: nombre, apellidos y fecha de nacimiento válida son obligatorios.")
            return render(request, 'patients/paciente_form.html', {'titulo': 'Registrar Nuevo Adulto Mayor'})

        data = form.cleaned_data
        codigo = ''.join(random.choices(string.ascii_uppercase + string.digits, k=6))

        paciente = Paciente.objects.create(
            nombre=data['nombre'],
            apellidos=data['apellidos'],
            fecha_nacimiento=data['fecha_nacimiento'],
            genero=data.get('genero') or '',
            tipo_sangre=data.get('tipo_sangre') or 'N/D',
            alergias=data.get('alergias') or '',
            enfermedades_cronicas=data.get('enfermedades_cronicas') or '',
            notas_cognitivas=data.get('notas_cognitivas') or '',
            codigo_vinculacion=codigo,
            token_emergencia_qr=str(uuid.uuid4())[:16],
        )

        # Generar imagen QR en /media/qr_sos/ con el host actual
        asegurar_imagen_qr_sos(paciente, request=request, forzar_regeneracion=True)

        PacienteCuidador.objects.create(
            paciente=paciente,
            usuario_cuidador=request.user,
            parentesco='Familiar Administrador',
            nivel_acceso=PacienteCuidador.NivelAcceso.FAMILIAR_ADMIN,
        )

        request.session['paciente_activo_id'] = paciente.id
        request.session['paciente_activo_nombre'] = paciente.nombre_completo

        registrar_auditoria(request, 'PACIENTE_CREADO', paciente=paciente, detalles=f"Creado por {request.user.email}")
        messages.success(request, f"Se ha registrado exitosamente al adulto mayor {paciente.nombre_completo} y generado su credencial QR SOS.")
        return redirect('patients:expediente', uuid=paciente.uuid)


class PacienteUpdateView(LoginRequiredMixin, PatientAccessRequiredMixin, View):
    def get(self, request, uuid):
        paciente = obtener_paciente_autorizado(request, identificador=uuid, requiere_edicion=True)
        return render(request, 'patients/paciente_form.html', {
            'paciente': paciente,
            'titulo': f'Editar Ficha de {paciente.nombre_completo}'
        })

    def post(self, request, uuid):
        paciente = obtener_paciente_autorizado(request, identificador=uuid, requiere_edicion=True)
        form = PacienteForm(request.POST)
        if not form.is_valid():
            messages.error(request, "Datos inválidos. Verifica los campos obligatorios y formatos.")
            return render(request, 'patients/paciente_form.html', {
                'paciente': paciente,
                'titulo': f'Editar Ficha de {paciente.nombre_completo}',
            })

        data = form.cleaned_data
        paciente.nombre = data['nombre']
        paciente.apellidos = data['apellidos']
        paciente.fecha_nacimiento = data['fecha_nacimiento']
        paciente.genero = data.get('genero') or paciente.genero
        paciente.tipo_sangre = data.get('tipo_sangre') or paciente.tipo_sangre
        paciente.alergias = data.get('alergias') or ''
        paciente.enfermedades_cronicas = data.get('enfermedades_cronicas') or ''
        paciente.notas_cognitivas = data.get('notas_cognitivas') or ''
        paciente.save()

        registrar_auditoria(request, 'PACIENTE_ACTUALIZADO', paciente=paciente, detalles="Actualización de datos clínicos")
        messages.success(request, "Datos del paciente actualizados exitosamente.")
        return redirect('patients:expediente', uuid=paciente.uuid)


class VinculacionView(LoginRequiredMixin, View):
    """
    Permite a un cuidador o familiar vincularse a un paciente mediante su código de 6 caracteres.
    """
    def get(self, request):
        return render(request, 'patients/vincular.html')

    def post(self, request):
        codigo = request.POST.get('codigo', '').strip().upper()[:10]
        if not codigo:
            messages.error(request, "Por favor introduce un código de vinculación.")
            return render(request, 'patients/vincular.html')

        paciente = Paciente.objects.filter(codigo_vinculacion=codigo, codigo_activo=True).first()
        if not paciente:
            messages.error(request, "Código inválido o expirado. Verifica con el titular o familiar administrador.")
            return render(request, 'patients/vincular.html')

        PacienteCuidador.objects.get_or_create(
            paciente=paciente,
            usuario_cuidador=request.user,
            defaults={
                'parentesco': 'Familiar',
                'nivel_acceso': PacienteCuidador.NivelAcceso.COMPLETO,
            }
        )

        request.session['paciente_activo_id'] = paciente.id
        request.session['paciente_activo_nombre'] = paciente.nombre_completo

        registrar_auditoria(request, 'VINCULACION_EXITOSA', paciente=paciente, detalles=f"Cuidador {request.user.email} vinculado con código")
        messages.success(request, f"¡Te has vinculado exitosamente al adulto mayor {paciente.nombre_completo}!")
        return redirect('patients:expediente', uuid=paciente.uuid)


@login_required
@require_POST
def contacto_emergencia_crear(request, uuid):
    paciente = obtener_paciente_autorizado(request, identificador=uuid, requiere_edicion=True)
    form = ContactoEmergenciaForm(request.POST)
    if form.is_valid():
        data = form.cleaned_data
        ContactoEmergencia.objects.create(
            paciente=paciente,
            nombre=data['nombre'],
            telefono=data['telefono'],
            parentesco=data.get('parentesco') or 'Familiar',
            es_prioritario=bool(data.get('es_prioritario')),
        )
        registrar_auditoria(request, 'CONTACTO_SOS_CREADO', paciente=paciente, detalles=f"Contacto {data['nombre']} ({data['telefono']})")
        messages.success(request, f"Contacto de emergencia '{data['nombre']}' añadido correctamente.")
    else:
        messages.error(request, "Nombre y teléfono válido son obligatorios.")

    return redirect('patients:expediente', uuid=paciente.uuid)


@login_required
@require_POST
def contacto_emergencia_eliminar(request, pk):
    contacto = get_object_or_404(ContactoEmergencia, pk=pk)
    paciente = obtener_paciente_autorizado(request, identificador=contacto.paciente.uuid, requiere_edicion=True)
    nombre = contacto.nombre
    contacto.delete()
    registrar_auditoria(request, 'CONTACTO_SOS_ELIMINADO', paciente=paciente, detalles=f"Eliminado contacto {nombre}")
    messages.info(request, f"Contacto '{nombre}' eliminado.")
    return redirect('patients:expediente', uuid=paciente.uuid)
