from django.db import models
from apps.patients.models import Paciente
from apps.core.utils import obtener_paciente_autorizado


def patient_context(request):
    """
    Context processor Zero Trust para inyectar los datos del paciente activo
    (validado contra permisos de `request.user`) y la lista de pacientes accesibles.
    """
    if not hasattr(request, 'user') or not request.user.is_authenticated:
        return {
            'paciente_activo': None,
            'paciente_activo_id': None,
            'paciente_activo_uuid': None,
            'paciente_activo_nombre': 'Sin Paciente',
            'pacientes_disponibles': [],
        }

    user = request.user
    pacientes_qs = Paciente.objects.none()

    if user.rol in ['administrador', 'admin']:
        pacientes_qs = Paciente.objects.all()
    elif user.rol in ['doctor', 'medico']:
        doctor = getattr(user, 'perfil_doctor', None)
        if doctor:
            pacientes_qs = doctor.pacientes_asignados.filter(paciente_doctor__estado='activo')
    elif user.rol in ['paciente', 'adulto_mayor']:
        pacientes_qs = Paciente.objects.filter(usuario=user)
    else:
        pacientes_qs = Paciente.objects.filter(
            models.Q(usuario=user) | models.Q(cuidadores=user)
        ).distinct()

    pacientes_list = list(pacientes_qs.order_by('nombre'))

    paciente_activo = obtener_paciente_autorizado(request, obligatorio=False)
    if not paciente_activo and pacientes_list:
        paciente_activo = pacientes_list[0]
        request.session['paciente_activo_id'] = paciente_activo.id
        request.session['paciente_activo_nombre'] = paciente_activo.nombre_completo

    return {
        'paciente_activo': paciente_activo,
        'paciente_activo_id': paciente_activo.id if paciente_activo else None,
        'paciente_activo_uuid': paciente_activo.uuid if paciente_activo else None,
        'paciente_activo_nombre': paciente_activo.nombre_completo if paciente_activo else 'Sin Paciente',
        'pacientes_disponibles': pacientes_list,
    }
