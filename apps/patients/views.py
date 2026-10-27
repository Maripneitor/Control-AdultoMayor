from django.db import models
from django.views.generic import DetailView, View
from django.shortcuts import get_object_or_404, redirect, render
from django.contrib.auth.mixins import LoginRequiredMixin
from django.http import Http404, JsonResponse
from django.contrib import messages
from apps.core.mixins import PatientAccessRequiredMixin
from .models import Paciente, PacienteCuidador, ContactoEmergencia

class ExpedienteDetailView(LoginRequiredMixin, PatientAccessRequiredMixin, DetailView):
    """
    Vista MVT para el Expediente Clínico Integral del Paciente.
    Aplica seguridad multitenant con ofuscación de seguridad (retorna 404 ante accesos no autorizados).
    """
    model = Paciente
    template_name = 'patients/expediente.html'
    context_object_name = 'paciente'

    def get_context_data(self, **kwargs):
        context = super().get_context_data(**kwargs)
        paciente = self.object

        # Actualizar sesión con el paciente activo actual (php-view-flow-guardian)
        self.request.session['paciente_activo_id'] = paciente.id
        self.request.session['paciente_activo_nombre'] = paciente.nombre_completo

        # Inyectar datos clínicos transversales
        context['permisos'] = self.permisos
        context['contactos_emergencia'] = paciente.contactos_emergencia.order_by('-es_prioritario', 'nombre')
        context['medicamentos_activos'] = paciente.medicamentos.filter(activo=True).order_by('nombre_comercial')
        context['ultimos_signos'] = paciente.signos_vitales.order_by('-created_at')[:7]
        context['doctores_asignados'] = paciente.doctores_tratantes.all()
        
        return context

class SeleccionarPacienteView(LoginRequiredMixin, View):
    """
    Permite al familiar o cuidador cambiar de paciente activo en la sesión.
    Garantiza la consistencia del flujo de datos entre pestañas.
    """
    def post(self, request, *args, **kwargs):
        paciente_id = request.POST.get('paciente_id')
        if not paciente_id:
            messages.warning(request, "Debe seleccionar un paciente válido.")
            return redirect('patients:expediente', pk=request.session.get('paciente_activo_id', 1))

        # Validar que el usuario tenga acceso legítimo a este paciente
        user = request.user
        paciente = None
        if user.rol in ['administrador', 'admin']:
            paciente = Paciente.objects.filter(id=paciente_id).first()
        elif user.rol in ['doctor', 'medico']:
            doctor = getattr(user, 'perfil_doctor', None)
            if doctor:
                paciente = doctor.pacientes_asignados.filter(id=paciente_id).first()
        else:
            # Dueño directo o cuidador vinculado
            paciente = Paciente.objects.filter(
                id=paciente_id
            ).filter(
                models.Q(usuario=user) | models.Q(pacientecuidador__usuario_cuidador=user)
            ).first()

        if not paciente:
            raise Http404("El paciente seleccionado no está disponible.")

        # Establecer en sesión
        request.session['paciente_activo_id'] = paciente.id
        request.session['paciente_activo_nombre'] = paciente.nombre_completo
        messages.success(request, f"Se ha cambiado el contexto al adulto mayor: {paciente.nombre_completo}")

        return redirect('patients:expediente', pk=paciente.id)

class FichaEmergenciaPublicaView(View):
    """
    Ficha de Emergencia QR pública accesible por primeros respondientes.
    No requiere autenticación; valida mediante token seguro.
    """
    def get(self, request, *args, **kwargs):
        token = request.GET.get('token')
        if not token:
            raise Http404("Ficha médica de emergencia no válida o token ausente.")

        paciente = Paciente.objects.filter(
            models.Q(token_emergencia_qr=token) | models.Q(qr_token=token)
        ).first()

        if not paciente:
            raise Http404("Ficha médica de emergencia no encontrada o expirada.")

        contactos = paciente.contactos_emergencia.order_by('-es_prioritario', 'nombre')
        medicamentos = paciente.medicamentos.filter(activo=True)

        return render(request, 'patients/emergencia_publica.html', {
            'paciente': paciente,
            'contactos': contactos,
            'medicamentos': medicamentos,
        })
