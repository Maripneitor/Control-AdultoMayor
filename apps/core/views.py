from django.shortcuts import render, redirect
from django.contrib.auth.decorators import login_required
from django.views.decorators.http import require_POST
from django.contrib import messages
from django.utils import timezone
from django.db import models
from django.http import JsonResponse
from apps.accounts.models import Usuario
from apps.patients.models import Paciente
from apps.medications.models import Medicamento, TomasHistorial
from apps.health.models import RegistroSaludDiario
from apps.appointments.models import CitaLaboratorio
from apps.doctors.models import Doctor
from apps.core.models import LogAuditoria, NotificacionAlerta
from apps.core.utils import registrar_auditoria, obtener_paciente_autorizado


def home_view(request):
    """
    Página de inicio y presentación institucional de VitalSenior.
    """
    if request.user.is_authenticated:
        return redirect('core:dashboard')

    return render(request, 'core/home.html', {
        'total_pacientes': Paciente.objects.count(),
        'total_medicamentos': Medicamento.objects.filter(activo=True).count(),
        'total_doctores': Doctor.objects.count(),
        'total_citas': CitaLaboratorio.objects.count(),
    })


@login_required
def dashboard_view(request):
    """
    Enrutador central de Dashboards según el rol activo del usuario (validado en BD).
    """
    user = request.user
    hoy = timezone.localdate()

    # 1. Flujo para Administrador
    if user.rol in ['administrador', 'admin']:
        return render(request, 'core/dashboard_admin.html', {
            'total_usuarios': Usuario.objects.count(),
            'total_pacientes': Paciente.objects.count(),
            'total_medicamentos': Medicamento.objects.count(),
            'ultimos_logs': LogAuditoria.objects.select_related('usuario', 'paciente')[:10],
            'alertas_recientes': NotificacionAlerta.objects.select_related('paciente')[:10],
        })

    # 2. Flujo para Adulto Mayor / Paciente Titular
    if user.rol in ['paciente', 'adulto_mayor']:
        paciente = Paciente.objects.filter(usuario=user).first()
        if not paciente:
            messages.warning(request, "No se encontró un perfil clínico asociado a tu cuenta de paciente.")
            return redirect('accounts:perfil')

        tomas_hoy = TomasHistorial.objects.filter(
            paciente=paciente,
            fecha_programada=hoy
        ).select_related('medicamento').order_by('hora_programada')

        ultimos_signos = RegistroSaludDiario.objects.filter(paciente=paciente).first()

        return render(request, 'core/dashboard_paciente.html', {
            'paciente': paciente,
            'tomas_hoy': tomas_hoy,
            'ultimos_signos': ultimos_signos,
            'hoy': hoy,
        })

    # 3. Flujo para Médico
    if user.rol in ['doctor', 'medico']:
        doctor = getattr(user, 'perfil_doctor', None)
        pacientes_asignados = doctor.pacientes_asignados.all() if doctor else []
        return render(request, 'core/dashboard_doctor.html', {
            'doctor': doctor,
            'pacientes_asignados': pacientes_asignados,
        })

    # 4. Flujo para Familiar Administrador / Cuidador (Zero Trust: validar relación en BD)
    paciente = obtener_paciente_autorizado(request, obligatorio=False)
    if not paciente:
        paciente = Paciente.objects.filter(
            models.Q(usuario=user) | models.Q(cuidadores=user)
        ).first()
        if paciente:
            request.session['paciente_activo_id'] = paciente.id
            request.session['paciente_activo_nombre'] = paciente.nombre_completo

    tomas_hoy = []
    tomas_tomadas = 0
    tomas_pendientes = 0
    tomas_omitidas = 0
    medicamentos_stock_bajo = []
    proximas_citas = []
    ultimos_signos = None

    if paciente:
        tomas_hoy = TomasHistorial.objects.filter(
            paciente=paciente,
            fecha_programada=hoy
        ).select_related('medicamento').order_by('hora_programada')

        tomas_tomadas = tomas_hoy.filter(estado='tomado').count()
        tomas_pendientes = tomas_hoy.filter(estado__in=['pendiente', 'pospuesto']).count()
        tomas_omitidas = tomas_hoy.filter(estado='omitido').count()

        medicamentos_stock_bajo = paciente.medicamentos.filter(
            activo=True,
            stock_actual__lte=models.F('stock_minimo_alerta')
        )

        proximas_citas = CitaLaboratorio.objects.filter(
            paciente=paciente,
            fecha_hora__gte=timezone.now(),
            estado='programada'
        )[:3]

        ultimos_signos = RegistroSaludDiario.objects.filter(paciente=paciente).first()

    return render(request, 'core/dashboard_cuidador.html', {
        'paciente': paciente,
        'tomas_hoy': tomas_hoy,
        'tomas_tomadas': tomas_tomadas,
        'tomas_pendientes': tomas_pendientes,
        'tomas_omitidas': tomas_omitidas,
        'medicamentos_stock_bajo': medicamentos_stock_bajo,
        'proximas_citas': proximas_citas,
        'ultimos_signos': ultimos_signos,
        'hoy': hoy,
    })


@login_required
@require_POST
def sos_activar_view(request):
    """
    Dispara una Alerta Crítica SOS y notifica a los cuidadores y contactos de emergencia.
    Zero Trust: Jamás lee `paciente_id` desde un input oculto del POST sin cruzar con `request.user`.
    """
    paciente = obtener_paciente_autorizado(request, obligatorio=False)

    if not paciente:
        messages.error(request, "No se pudo identificar un paciente autorizado para la alerta SOS.")
        return redirect('core:dashboard')

    NotificacionAlerta.objects.create(
        paciente=paciente,
        tipo='sos',
        mensaje=f"¡ALERTA SOS ACTIVADA! El paciente {paciente.nombre_completo} ha activado el botón de pánico médico.",
        estado_envio='enviado'
    )

    registrar_auditoria(request, 'SOS_ACTIVADO', paciente=paciente, detalles="Botón de pánico presionado")

    if request.headers.get('x-requested-with') == 'XMLHttpRequest':
        return JsonResponse({'status': 'ok', 'mensaje': 'Alerta SOS emitida a todos los contactos de emergencia.'})

    messages.error(request, f"¡ALERTA SOS EMITIDA! Se ha notificado inmediatamente a la red de cuidado de {paciente.nombre_completo}.")
    return redirect('core:dashboard')
