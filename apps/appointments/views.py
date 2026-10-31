from django.shortcuts import render, redirect, get_object_or_404
from django.contrib.auth.decorators import login_required
from django.contrib import messages
from apps.doctors.models import Doctor
from apps.core.utils import registrar_auditoria, obtener_paciente_autorizado
from .models import CitaLaboratorio, VisitaMedica


@login_required
def cita_lista(request):
    paciente = obtener_paciente_autorizado(request, obligatorio=False)
    citas = CitaLaboratorio.objects.none()
    visitas = VisitaMedica.objects.none()

    if paciente:
        citas = paciente.citas.all().order_by('fecha_hora')
        visitas = paciente.visitas_clinicas.all().order_by('-fecha_visita')

    return render(request, 'appointments/cita_list.html', {
        'paciente': paciente,
        'citas': citas,
        'visitas': visitas,
    })


@login_required
def cita_crear(request):
    paciente = obtener_paciente_autorizado(request, requiere_edicion=True, obligatorio=False)
    if not paciente:
        messages.warning(request, "Selecciona un paciente autorizado antes de programar una cita.")
        return redirect('patients:lista')

    doctores = Doctor.objects.all()

    if request.method == 'POST':
        titulo = request.POST.get('titulo', '').strip()[:200]
        tipo_evento = request.POST.get('tipo_evento', 'consulta')[:50]
        fecha_hora = request.POST.get('fecha_hora')
        lugar = request.POST.get('lugar', '').strip()[:255]
        ubicacion_mapa = request.POST.get('ubicacion_mapa', '').strip()[:500]
        preparacion = request.POST.get('preparacion_previa', '').strip()[:1500]
        notas = request.POST.get('notas', '').strip()[:1500]
        doctor_id = request.POST.get('doctor_id')

        if not titulo or not fecha_hora:
            messages.error(request, "Título y fecha/hora son obligatorios.")
            return render(request, 'appointments/cita_form.html', {'paciente': paciente, 'doctores': doctores})

        doctor = Doctor.objects.filter(id=doctor_id).first() if (doctor_id and str(doctor_id).isdigit()) else None

        CitaLaboratorio.objects.create(
            paciente=paciente,
            familiar_responsable=request.user,
            doctor=doctor,
            titulo=titulo,
            tipo_evento=tipo_evento,
            fecha_hora=fecha_hora,
            lugar=lugar,
            ubicacion_mapa=ubicacion_mapa,
            preparacion_previa=preparacion,
            notas=notas,
            estado='programada'
        )

        registrar_auditoria(request, 'CITA_PROGRAMADA', paciente=paciente, detalles=f"{titulo} ({fecha_hora})")
        messages.success(request, f"Cita '{titulo}' programada exitosamente.")
        return redirect('appointments:lista')

    return render(request, 'appointments/cita_form.html', {
        'paciente': paciente,
        'doctores': doctores,
        'titulo_form': 'Programar Cita o Estudio'
    })


@login_required
def cita_editar(request, pk):
    cita = get_object_or_404(CitaLaboratorio, pk=pk)
    # Zero Trust Anti-IDOR: verificar que request.user tenga permisos sobre el paciente de la cita
    paciente = obtener_paciente_autorizado(request, identificador=cita.paciente.uuid, requiere_edicion=True)
    doctores = Doctor.objects.all()

    if request.method == 'POST':
        cita.titulo = request.POST.get('titulo', cita.titulo).strip()[:200]
        cita.tipo_evento = request.POST.get('tipo_evento', cita.tipo_evento)[:50]
        fecha_hora = request.POST.get('fecha_hora')
        if fecha_hora:
            cita.fecha_hora = fecha_hora
        cita.lugar = request.POST.get('lugar', cita.lugar).strip()[:255]
        cita.ubicacion_mapa = request.POST.get('ubicacion_mapa', cita.ubicacion_mapa).strip()[:500]
        cita.preparacion_previa = request.POST.get('preparacion_previa', cita.preparacion_previa).strip()[:1500]
        cita.notas = request.POST.get('notas', cita.notas).strip()[:1500]
        estado = request.POST.get('estado', cita.estado)
        if estado in ('programada', 'completada', 'cancelada'):
            cita.estado = estado
        cita.save()

        registrar_auditoria(request, 'CITA_ACTUALIZADA', paciente=paciente, detalles=f"Cita {cita.titulo}")
        messages.success(request, f"Cita '{cita.titulo}' actualizada.")
        return redirect('appointments:lista')

    return render(request, 'appointments/cita_form.html', {
        'cita': cita,
        'paciente': paciente,
        'doctores': doctores,
        'titulo_form': f'Editar Cita: {cita.titulo}'
    })


@login_required
def visita_crear(request):
    paciente = obtener_paciente_autorizado(request, requiere_edicion=True, obligatorio=False)
    if not paciente:
        messages.warning(request, "Selecciona un paciente autorizado antes de registrar una consulta médica.")
        return redirect('patients:lista')

    if request.method == 'POST':
        motivo = request.POST.get('motivo_consulta', '').strip()[:255]
        diagnostico = request.POST.get('diagnostico_notas', '').strip()[:2000]
        doctor_nombre = request.POST.get('doctor_nombre', '').strip()[:150]
        lugar = request.POST.get('lugar_atencion', '').strip()[:255]
        requiere_seguimiento = request.POST.get('requiere_seguimiento') == 'on'
        fecha_seguimiento = request.POST.get('fecha_seguimiento') or None
        instrucciones = request.POST.get('instrucciones_seguimiento', '').strip()[:1500]

        if not motivo:
            messages.error(request, "El motivo de consulta es obligatorio.")
            return render(request, 'appointments/visita_form.html', {'paciente': paciente})

        VisitaMedica.objects.create(
            paciente=paciente,
            usuario_registra=request.user,
            motivo_consulta=motivo,
            diagnostico_notas=diagnostico,
            doctor_nombre=doctor_nombre,
            lugar_atencion=lugar,
            requiere_seguimiento=requiere_seguimiento,
            fecha_seguimiento=fecha_seguimiento,
            instrucciones_seguimiento=instrucciones
        )

        registrar_auditoria(request, 'VISITA_MEDICA_REGISTRADA', paciente=paciente, detalles=f"Consulta con {doctor_nombre}")
        messages.success(request, "Bitácora médica y diagnóstico guardados exitosamente.")
        return redirect('appointments:lista')

    return render(request, 'appointments/visita_form.html', {
        'paciente': paciente
    })
