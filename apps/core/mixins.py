from django.http import Http404
from django.contrib.auth.mixins import AccessMixin

class PatientAccessRequiredMixin(AccessMixin):
    """
    Mixin de seguridad para aislar el acceso multitenant a pacientes.
    Aplica el principio de ofuscación de seguridad:
    Si el usuario no tiene permisos sobre el paciente, emite un 404 (Not Found)
    en lugar de un 403 (Forbidden) para no revelar la existencia del registro médico.
    """
    paciente_url_kwarg = 'pk'

    def get_paciente_id(self):
        return self.kwargs.get(self.paciente_url_kwarg) or self.request.GET.get('id')

    def check_patient_access(self, paciente):
        user = self.request.user
        if not user.is_authenticated:
            return False, {}

        # 1. Administrador: acceso total
        if user.rol in ['administrador', 'admin']:
            return True, {'puede_ver': True, 'puede_editar': True, 'nivel': 'admin', 'es_doctor': False}

        # 2. Médico / Doctor: verificar paciente_doctor
        if user.rol in ['doctor', 'medico']:
            doctor = getattr(user, 'perfil_doctor', None)
            if doctor and doctor.pacientes_asignados.filter(id=paciente.id, paciente_doctor__estado='activo').exists():
                return True, {'puede_ver': True, 'puede_editar': False, 'puede_suministrar': False, 'nivel': 'doctor', 'es_doctor': True}
            return False, {}

        # 3. Paciente titular (dueño directo)
        if paciente.usuario_id == user.id:
            es_adulto_mayor = user.rol in ['paciente', 'adulto_mayor']
            return True, {
                'puede_ver': True,
                'puede_editar': not es_adulto_mayor,
                'puede_suministrar': True,
                'nivel': 'paciente' if es_adulto_mayor else 'principal',
                'es_propio': True
            }

        # 4. Familiar o Cuidador: verificar paciente_cuidador
        vinculo = paciente.pacientecuidador_set.filter(usuario_cuidador=user).first()
        if vinculo:
            es_solo_lectura = vinculo.nivel_acceso in ['solo_lectura', 'lectura']
            return True, {
                'puede_ver': True,
                'puede_editar': not es_solo_lectura,
                'puede_suministrar': not es_solo_lectura,
                'nivel': 'solo_lectura' if es_solo_lectura else 'completo',
                'parentesco': vinculo.parentesco,
                'es_propio': False
            }

        return False, {}

    def dispatch(self, request, *args, **kwargs):
        if not request.user.is_authenticated:
            return self.handle_no_permission()

        paciente = self.get_object()
        has_access, permisos = self.check_patient_access(paciente)

        if not has_access:
            # Ofuscación de seguridad: 404 en vez de 403
            raise Http404("Expediente clínico no encontrado.")

        self.permisos = permisos
        return super().dispatch(request, *args, **kwargs)
