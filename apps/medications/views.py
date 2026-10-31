from datetime import datetime, timedelta, time
from django.shortcuts import render, redirect, get_object_or_404
from django.contrib.auth.decorators import login_required
from django.views.decorators.http import require_POST
from django.contrib import messages
from django.utils import timezone
from apps.core.utils import registrar_auditoria, obtener_paciente_autorizado
from apps.core.models import NotificacionAlerta
from .models import Medicamento, TomasHistorial
from .forms import MedicamentoForm


def generar_tomas_diarias_paciente(paciente, fecha=None):
    """
    Genera las tomas del día para los medicamentos activos del paciente si aún no existen.
    """
    if fecha is None:
        fecha = timezone.localdate()

    medicamentos = paciente.medicamentos.filter(activo=True)
    nuevas_tomas = []

    for med in medicamentos:
        if TomasHistorial.objects.filter(medicamento=med, fecha_programada=fecha).exists():
            continue

        freq = med.frecuencia_horas
        if freq <= 0:
            freq = 8

        hora_actual = 8
        while hora_actual < 24:
            t_obj = time(hour=hora_actual, minute=0)
            nuevas_tomas.append(TomasHistorial(
                medicamento=med,
                paciente=paciente,
                fecha_programada=fecha,
                hora_programada=t_obj,
                estado=TomasHistorial.EstadoToma.PENDIENTE
            ))
            hora_actual += freq

    if nuevas_tomas:
        TomasHistorial.objects.bulk_create(nuevas_tomas)


@login_required
def medicamento_lista(request):
    paciente = obtener_paciente_autorizado(request, obligatorio=False)
    medicamentos = Medicamento.objects.none()
    if paciente:
        medicamentos = paciente.medicamentos.all().order_by('-activo', 'nombre_comercial')

    return render(request, 'medications/medicamento_list.html', {
        'paciente': paciente,
        'medicamentos': medicamentos,
    })


@login_required
def medicamento_crear(request):
    # Zero Trust: Obtener paciente estrictamente cruzando request.user con la sesión en BD
    paciente = obtener_paciente_autorizado(request, requiere_edicion=True, obligatorio=False)
    if not paciente:
        messages.warning(request, "Selecciona o registra un paciente autorizado antes de agregar medicamentos.")
        return redirect('patients:lista')

    if request.method == 'POST':
        form = MedicamentoForm(request.POST)
        if not form.is_valid():
            messages.error(request, "Verifica los datos del medicamento: nombre, dosis y valores numéricos válidos son obligatorios.")
            return render(request, 'medications/medicamento_form.html', {'paciente': paciente, 'titulo': 'Nuevo Medicamento'})

        data = form.cleaned_data
        med = Medicamento.objects.create(
            paciente=paciente,
            nombre_comercial=data['nombre_comercial'],
            dosis=data['dosis'],
            frecuencia_horas=data['frecuencia_horas'],
            instrucciones=data.get('instrucciones') or '',
            stock_actual=data['stock_actual'],
            dosis_por_toma=data['dosis_por_toma'],
            stock_minimo_alerta=data['stock_minimo_alerta'],
            activo=True
        )

        generar_tomas_diarias_paciente(paciente)
        registrar_auditoria(request, 'MEDICAMENTO_CREADO', paciente=paciente, detalles=f"Fármaco {med.nombre_comercial} ({med.dosis})")
        messages.success(request, f"Medicamento '{med.nombre_comercial}' registrado exitosamente.")
        return redirect('medications:lista')

    return render(request, 'medications/medicamento_form.html', {
        'paciente': paciente,
        'titulo': 'Nuevo Medicamento'
    })


@login_required
def medicamento_editar(request, pk):
    med = get_object_or_404(Medicamento, pk=pk)
    # Zero Trust Anti-IDOR: verificar que request.user tenga permisos sobre el paciente dueño del medicamento
    paciente = obtener_paciente_autorizado(request, identificador=med.paciente.uuid, requiere_edicion=True)

    if request.method == 'POST':
        form = MedicamentoForm(request.POST)
        if not form.is_valid():
            messages.error(request, "Datos inválidos. Revisa las cantidades y longitudes permitidas.")
            return render(request, 'medications/medicamento_form.html', {
                'medicamento': med,
                'paciente': paciente,
                'titulo': f'Editar {med.nombre_comercial}'
            })

        data = form.cleaned_data
        med.nombre_comercial = data['nombre_comercial']
        med.dosis = data['dosis']
        med.frecuencia_horas = data['frecuencia_horas']
        med.instrucciones = data.get('instrucciones') or ''
        med.stock_actual = data['stock_actual']
        med.dosis_por_toma = data['dosis_por_toma']
        med.stock_minimo_alerta = data['stock_minimo_alerta']
        med.activo = request.POST.get('activo') == 'on'
        med.save()

        registrar_auditoria(request, 'MEDICAMENTO_ACTUALIZADO', paciente=paciente, detalles=f"Fármaco {med.nombre_comercial}")
        messages.success(request, f"Medicamento '{med.nombre_comercial}' actualizado.")
        return redirect('medications:lista')

    return render(request, 'medications/medicamento_form.html', {
        'medicamento': med,
        'paciente': paciente,
        'titulo': f'Editar {med.nombre_comercial}'
    })


@login_required
def tomas_diarias(request):
    paciente = obtener_paciente_autorizado(request, obligatorio=False)
    hoy = timezone.localdate()
    tomas = []

    if paciente:
        generar_tomas_diarias_paciente(paciente, hoy)
        tomas = TomasHistorial.objects.filter(
            paciente=paciente,
            fecha_programada=hoy
        ).select_related('medicamento').order_by('hora_programada')

    return render(request, 'medications/tomas_diarias.html', {
        'paciente': paciente,
        'tomas': tomas,
        'hoy': hoy,
    })


@login_required
@require_POST
def marcar_toma(request, pk):
    toma = get_object_or_404(TomasHistorial, pk=pk)
    # Zero Trust Anti-IDOR: validar que request.user tiene permiso sobre el paciente de la toma
    paciente = obtener_paciente_autorizado(request, identificador=toma.paciente.uuid, requiere_edicion=True)
    med = toma.medicamento
    now = timezone.localtime()

    prog_datetime = timezone.make_aware(datetime.combine(toma.fecha_programada, toma.hora_programada))
    diff_minutes = (now - prog_datetime).total_seconds() / 60.0

    nota = None
    if diff_minutes > 45:
        nota = f"Toma con retraso ({int(diff_minutes)} min después de lo previsto)"
    elif diff_minutes < -45:
        nota = f"Toma anticipada ({abs(int(diff_minutes))} min antes de lo previsto)"

    toma.estado = TomasHistorial.EstadoToma.TOMADO
    toma.tomado_at = now
    toma.registrado_por = request.user
    if nota:
        toma.nota_incidencia = nota
    toma.save()

    if med.stock_actual > 0:
        med.stock_actual = max(0, med.stock_actual - med.dosis_por_toma)
        med.save()

        if med.stock_actual <= med.stock_minimo_alerta:
            NotificacionAlerta.objects.create(
                paciente=paciente,
                tipo='stock_bajo',
                mensaje=f"Stock bajo para {med.nombre_comercial}: quedan {med.stock_actual} dosis.",
                estado_envio='pendiente'
            )

    registrar_auditoria(request, 'TOMA_REGISTRADA', paciente=paciente, detalles=f"Toma de {med.nombre_comercial}. {nota or 'A tiempo'}")
    messages.success(request, f"¡Excelente! Toma de {med.nombre_comercial} registrada.")

    next_url = request.POST.get('next')
    if next_url and next_url.startswith('/') and not next_url.startswith('//'):
        return redirect(next_url)
    return redirect('core:dashboard')


@login_required
@require_POST
def posponer_toma(request, pk):
    toma = get_object_or_404(TomasHistorial, pk=pk)
    paciente = obtener_paciente_autorizado(request, identificador=toma.paciente.uuid, requiere_edicion=True)

    prog_datetime = datetime.combine(toma.fecha_programada, toma.hora_programada)
    nueva_hora = (prog_datetime + timedelta(minutes=30)).time()

    toma.estado = TomasHistorial.EstadoToma.POSPUESTO
    toma.hora_programada = nueva_hora
    toma.veces_pospuesto += 1
    toma.nota_incidencia = f"Pospuesta 30 minutos (Veces: {toma.veces_pospuesto})"
    toma.save()

    registrar_auditoria(request, 'TOMA_POSPUESTA', paciente=paciente, detalles=f"Toma de {toma.medicamento.nombre_comercial} pospuesta a {nueva_hora.strftime('%H:%M')}")
    messages.warning(request, f"Toma de {toma.medicamento.nombre_comercial} pospuesta 30 minutos.")

    next_url = request.POST.get('next')
    if next_url and next_url.startswith('/') and not next_url.startswith('//'):
        return redirect(next_url)
    return redirect('core:dashboard')


@login_required
@require_POST
def deshacer_toma(request, pk):
    toma = get_object_or_404(TomasHistorial, pk=pk)
    paciente = obtener_paciente_autorizado(request, identificador=toma.paciente.uuid, requiere_edicion=True)
    med = toma.medicamento

    if toma.estado == TomasHistorial.EstadoToma.TOMADO:
        toma.estado = TomasHistorial.EstadoToma.PENDIENTE
        toma.tomado_at = None
        toma.deshecho_at = timezone.now()
        toma.save()

        med.stock_actual += med.dosis_por_toma
        med.save()

        registrar_auditoria(request, 'TOMA_DESHECHA', paciente=paciente, detalles=f"Toma deshecha y stock restaurado para {med.nombre_comercial}")
        messages.info(request, f"Toma de {med.nombre_comercial} deshecha y dosis restaurada en el stock.")

    return redirect('medications:tomas_diarias')
