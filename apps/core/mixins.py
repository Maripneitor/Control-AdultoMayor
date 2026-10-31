import uuid
from django.http import Http404
from django.shortcuts import get_object_or_404
from django.contrib.auth.mixins import AccessMixin
from apps.core.utils import verificar_permisos_paciente, registrar_auditoria


class PatientAccessRequiredMixin(AccessMixin):
    """
    Mixin de seguridad Zero Trust para aislar el acceso multitenant a pacientes.
    Soporta resolución por UUID (`qr_token`) o `pk` y verifica siempre que `request.user`
    tenga relación directa con el paciente antes de renderizar la plantilla.
    """
    paciente_url_kwarg = 'uuid'

    def get_object(self, queryset=None):
        from apps.patients.models import Paciente

        uuid_val = self.kwargs.get('uuid')
        pk_val = self.kwargs.get('pk')

        if uuid_val:
            return get_object_or_404(Paciente, qr_token=uuid_val)
        if pk_val:
            try:
                uuid_obj = uuid.UUID(str(pk_val))
                return get_object_or_404(Paciente, qr_token=uuid_obj)
            except (ValueError, TypeError):
                return get_object_or_404(Paciente, pk=pk_val)
        raise Http404("Expediente clínico no encontrado.")

    def check_patient_access(self, paciente):
        return verificar_permisos_paciente(self.request.user, paciente)

    def dispatch(self, request, *args, **kwargs):
        if not request.user.is_authenticated:
            return self.handle_no_permission()

        paciente = self.get_object()
        has_access, permisos = self.check_patient_access(paciente)

        if not has_access:
            registrar_auditoria(
                request,
                'INTENTO_IDOR_BLOQUEADO',
                paciente=paciente,
                detalles=f"Acceso no autorizado bloqueado para {request.user.email}",
            )
            raise Http404("Expediente clínico no encontrado.")

        self.object = paciente
        self.permisos = permisos
        return super().dispatch(request, *args, **kwargs)
