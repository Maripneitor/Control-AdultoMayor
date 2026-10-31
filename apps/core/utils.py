import uuid
from django.db import models
from django.http import Http404
from apps.core.models import LogAuditoria


def registrar_auditoria(request, accion, paciente=None, detalles=None):
    """
    Registra de forma inmutable una acción clínica o de seguridad en logs_auditoria.
    """
    try:
        user = request.user if request and request.user.is_authenticated else None
        ip = None
        user_agent = None

        if request:
            x_forwarded_for = request.META.get('HTTP_X_FORWARDED_FOR')
            if x_forwarded_for:
                ip = x_forwarded_for.split(',')[0].strip()
            else:
                ip = request.META.get('REMOTE_ADDR')
            user_agent = request.META.get('HTTP_USER_AGENT', '')[:255]

        LogAuditoria.objects.create(
            usuario=user,
            paciente=paciente,
            accion=accion,
            detalles=detalles,
            ip_origen=ip,
            user_agent=user_agent
        )
    except Exception:
        # Fallo silencioso en auditoría para no interrumpir el flujo del usuario
        pass


def verificar_permisos_paciente(user, paciente):
    """
    Verifica en el servidor (Zero Trust) que `user` tenga relación directa y legítima
    con `paciente`. Retorna `(tiene_acceso: bool, permisos: dict)`.
    """
    if not user or not user.is_authenticated or not paciente:
        return False, {}

    # 1. Administrador del sistema
    if user.rol in ['administrador', 'admin']:
        return True, {
            'puede_ver': True,
            'puede_editar': True,
            'puede_suministrar': True,
            'nivel': 'admin',
            'es_doctor': False,
        }

    # 2. Médico / Especialista vinculado activamente
    if user.rol in ['doctor', 'medico']:
        doctor = getattr(user, 'perfil_doctor', None)
        if doctor and doctor.pacientes_asignados.filter(id=paciente.id, paciente_doctor__estado='activo').exists():
            return True, {
                'puede_ver': True,
                'puede_editar': False,
                'puede_suministrar': False,
                'nivel': 'doctor',
                'es_doctor': True,
            }
        return False, {}

    # 3. Paciente titular (dueño directo de su expediente)
    if paciente.usuario_id == user.id:
        es_adulto_mayor = user.rol in ['paciente', 'adulto_mayor']
        return True, {
            'puede_ver': True,
            'puede_editar': not es_adulto_mayor,
            'puede_suministrar': True,
            'nivel': 'paciente' if es_adulto_mayor else 'principal',
            'es_propio': True,
        }

    # 4. Familiar o Cuidador vinculado en `paciente_cuidador`
    vinculo = paciente.pacientecuidador_set.filter(usuario_cuidador=user).first()
    if vinculo:
        es_solo_lectura = vinculo.nivel_acceso in ['solo_lectura', 'lectura']
        return True, {
            'puede_ver': True,
            'puede_editar': not es_solo_lectura,
            'puede_suministrar': not es_solo_lectura,
            'nivel': 'solo_lectura' if es_solo_lectura else 'completo',
            'parentesco': vinculo.parentesco,
            'es_propio': False,
        }

    return False, {}


def obtener_paciente_autorizado(request, identificador=None, requiere_edicion=False, obligatorio=True):
    """
    Resuelve un paciente por UUID (`qr_token`) o desde la sesión del servidor (`paciente_activo_id`),
    y valida obligatoriamente que `request.user` tenga autorización sobre ese expediente.
    Lanza `Http404` si el paciente no existe o el usuario intenta un ataque IDOR.
    """
    from apps.patients.models import Paciente

    user = getattr(request, 'user', None)
    if not user or not user.is_authenticated:
        if obligatorio:
            raise Http404("Expediente clínico no encontrado.")
        return None

    paciente = None
    target = identificador if identificador is not None else request.session.get('paciente_activo_id')

    if target is not None:
        if isinstance(target, uuid.UUID):
            paciente = Paciente.objects.filter(qr_token=target).first()
        else:
            target_str = str(target).strip()
            try:
                uuid_obj = uuid.UUID(target_str)
                paciente = Paciente.objects.filter(qr_token=uuid_obj).first()
            except (ValueError, TypeError):
                if target_str.isdigit():
                    paciente = Paciente.objects.filter(id=int(target_str)).first()

    if not paciente and identificador is None and user.rol in ['paciente', 'adulto_mayor']:
        paciente = getattr(user, 'perfil_paciente_directo', None)

    if not paciente:
        if obligatorio and identificador is not None:
            raise Http404("Expediente clínico no encontrado.")
        return None

    tiene_acceso, permisos = verificar_permisos_paciente(user, paciente)
    if not tiene_acceso:
        registrar_auditoria(
            request,
            'INTENTO_IDOR_BLOQUEADO',
            paciente=paciente,
            detalles=f"Usuario {user.email} intentó acceder sin permiso al paciente {paciente.qr_token}",
        )
        if obligatorio or identificador is not None:
            raise Http404("Expediente clínico no encontrado.")
        request.session.pop('paciente_activo_id', None)
        request.session.pop('paciente_activo_nombre', None)
        return None

    if requiere_edicion and not (permisos.get('puede_editar') or permisos.get('puede_suministrar')):
        registrar_auditoria(
            request,
            'INTENTO_EDICION_NO_AUTORIZADA',
            paciente=paciente,
            detalles=f"Usuario {user.email} sin permisos de edición sobre {paciente.qr_token}",
        )
        raise Http404("No tienes permisos de modificación sobre este expediente.")

    return paciente
