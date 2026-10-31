import uuid
import secrets
from datetime import timedelta
from django.shortcuts import render, redirect
from django.contrib.auth.decorators import login_required
from django.core.cache import cache
from django.contrib import messages
from django.utils import timezone
from django.http import Http404
from apps.patients.models import Paciente
from apps.core.utils import registrar_auditoria, obtener_paciente_autorizado
from .models import Doctor, TokenAccesoMedico


@login_required
def doctor_portal(request):
    doctor = getattr(request.user, 'perfil_doctor', None)
    if not doctor and request.user.rol not in ['doctor', 'medico', 'administrador', 'admin']:
        messages.error(request, "Solo profesionales médicos acreditados tienen acceso al portal.")
        return redirect('core:dashboard')

    pacientes = doctor.pacientes_asignados.all() if doctor else Paciente.objects.all()

    return render(request, 'doctors/portal.html', {
        'doctor': doctor,
        'pacientes': pacientes,
    })


@login_required
def generar_token(request):
    """
    Genera un token efímero con PIN de 6 dígitos para que un médico externo consulte
    el expediente sin requerir credenciales permanentes.
    Zero Trust: Obtiene el paciente validando relación directa con `request.user`.
    """
    paciente = obtener_paciente_autorizado(request, requiere_edicion=True, obligatorio=False)
    if not paciente:
        messages.warning(request, "Selecciona un paciente autorizado antes de generar un token de acceso médico.")
        return redirect('patients:lista')

    if request.method == 'POST':
        medico_nombre = request.POST.get('medico_nombre', '').strip()[:150]
        institucion = request.POST.get('hospital_institucion', '').strip()[:150]
        try:
            duracion_horas = int(request.POST.get('duracion_horas', 24))
        except (ValueError, TypeError):
            duracion_horas = 24
        duracion_horas = max(1, min(72, duracion_horas))

        if not medico_nombre:
            messages.error(request, "El nombre del médico especialista es obligatorio.")
            return render(request, 'doctors/token_form.html', {'paciente': paciente})

        pin = str(secrets.SystemRandom().randint(100000, 999999))
        token_url = uuid.uuid4().hex

        token_obj = TokenAccesoMedico.objects.create(
            paciente=paciente,
            generado_por=request.user,
            pin_seguridad=pin,
            token_url=token_url,
            medico_nombre=medico_nombre,
            hospital_institucion=institucion,
            duracion_horas=duracion_horas,
            expira_at=timezone.now() + timedelta(hours=duracion_horas),
            max_usos=20,
            activo=True
        )

        registrar_auditoria(request, 'TOKEN_MEDICO_GENERADO', paciente=paciente, detalles=f"Token para {medico_nombre}")

        return render(request, 'doctors/token_generado.html', {
            'paciente': paciente,
            'token_obj': token_obj,
            'pin': pin,
        })

    return render(request, 'doctors/token_form.html', {
        'paciente': paciente,
    })


def acceso_temporal_medico(request, token_url):
    """
    Permite al médico ingresar el PIN de seguridad y acceder al expediente temporalmente.
    Incluye límite de 5 intentos fallidos de PIN con bloqueo de 15 minutos.
    """
    token_obj = TokenAccesoMedico.objects.filter(token_url=token_url, activo=True).first()

    if not token_obj or token_obj.expira_at < timezone.now() or token_obj.usos_actuales >= token_obj.max_usos:
        raise Http404("El enlace de acceso médico ha expirado o no es válido.")

    lock_key = f"medico_pin_lock_{token_obj.id}"
    attempts_key = f"medico_pin_attempts_{token_obj.id}"
    if cache.get(lock_key):
        raise Http404("El enlace de acceso médico ha sido bloqueado temporalmente por seguridad.")

    paciente = token_obj.paciente
    pin_valido = False

    if request.method == 'POST':
        pin = request.POST.get('pin', '').strip()
        if secrets.compare_digest(pin, str(token_obj.pin_seguridad)):
            cache.delete(attempts_key)
            token_obj.usos_actuales += 1
            token_obj.ultimo_acceso_at = timezone.now()
            token_obj.save()

            registrar_auditoria(request, 'ACCESO_MEDICO_PIN_VALIDO', paciente=paciente, detalles=f"Acceso de Dr(a). {token_obj.medico_nombre}")
            pin_valido = True
        else:
            intentos = cache.get(attempts_key, 0) + 1
            cache.set(attempts_key, intentos, timeout=900)
            if intentos >= 5:
                cache.set(lock_key, True, timeout=900)
                token_obj.activo = False
                token_obj.save()
                raise Http404("El enlace ha sido revocado tras 5 intentos fallidos de PIN.")
            messages.error(request, f"El PIN de seguridad es incorrecto. Intentos restantes: {5 - intentos}.")

    if pin_valido:
        return render(request, 'doctors/acceso_medico.html', {
            'paciente': paciente,
            'token_obj': token_obj,
            'medicamentos': paciente.medicamentos.filter(activo=True),
            'ultimos_signos': paciente.signos_vitales.order_by('-created_at')[:10],
            'contactos': paciente.contactos_emergencia.order_by('-es_prioritario', 'nombre'),
            'visitas': paciente.visitas_clinicas.order_by('-fecha_visita')[:5],
        })

    return render(request, 'doctors/verificar_pin.html', {
        'token_obj': token_obj,
        'paciente': paciente,
    })


@login_required
def reporte_clinico_imprimible(request, uuid):
    # Zero Trust Anti-IDOR: resolver por UUID y validar relación con request.user
    paciente = obtener_paciente_autorizado(request, identificador=uuid, obligatorio=True)
    medicamentos = paciente.medicamentos.filter(activo=True)
    signos = paciente.signos_vitales.order_by('-created_at')[:10]
    visitas = paciente.visitas_clinicas.order_by('-fecha_visita')[:5]

    registrar_auditoria(request, 'REPORTE_CLINICO_GENERADO', paciente=paciente, detalles="Vista imprimible de resumen médico")

    return render(request, 'doctors/reporte_pdf.html', {
        'paciente': paciente,
        'medicamentos': medicamentos,
        'signos': signos,
        'visitas': visitas,
        'fecha_emision': timezone.now(),
    })
