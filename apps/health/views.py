from django.shortcuts import render, redirect
from django.contrib.auth.decorators import login_required
from django.contrib import messages
from apps.core.utils import registrar_auditoria, obtener_paciente_autorizado
from apps.core.models import NotificacionAlerta
from .models import RegistroSaludDiario
from .forms import RegistroSaludForm


@login_required
def salud_lista(request):
    paciente = obtener_paciente_autorizado(request, obligatorio=False)
    registros = RegistroSaludDiario.objects.none()
    if paciente:
        registros = paciente.signos_vitales.all().order_by('-created_at')

    return render(request, 'health/salud_list.html', {
        'paciente': paciente,
        'registros': registros,
    })


@login_required
def salud_nuevo(request):
    # Zero Trust: Obtener paciente estrictamente validando permisos de request.user en el servidor
    paciente = obtener_paciente_autorizado(request, requiere_edicion=True, obligatorio=False)
    if not paciente:
        messages.warning(request, "Selecciona un paciente autorizado antes de registrar signos vitales.")
        return redirect('patients:lista')

    if request.method == 'POST':
        form = RegistroSaludForm(request.POST)
        if not form.is_valid():
            messages.error(request, "Verifica que los valores clínicos ingresados estén dentro de rangos fisiológicos válidos.")
            return render(request, 'health/salud_form.html', {'paciente': paciente})

        data = form.cleaned_data
        reg = RegistroSaludDiario(
            paciente=paciente,
            registrado_por=request.user,
            presion_sistolica=data.get('presion_sistolica'),
            presion_diastolica=data.get('presion_diastolica'),
            ritmo_cardiaco=data.get('ritmo_cardiaco'),
            glucosa=data.get('glucosa'),
            momento_glucosa=data.get('momento_glucosa') or 'ayunas',
            horas_sueno=data.get('horas_sueno'),
            nivel_dolor=data.get('nivel_dolor') or 0,
            peso=data.get('peso'),
            altura=data.get('altura'),
            notas=data.get('notas') or '',
        )

        alerta_razon = None
        if reg.presion_sistolica and reg.presion_sistolica >= 140:
            alerta_razon = f"Presión arterial sistólica elevada ({reg.presion_sistolica} mmHg)."
        elif reg.presion_diastolica and reg.presion_diastolica >= 90:
            alerta_razon = f"Presión arterial diastólica elevada ({reg.presion_diastolica} mmHg)."
        elif reg.glucosa and reg.glucosa >= 180:
            alerta_razon = f"Glucosa alta ({reg.glucosa} mg/dL)."
        elif reg.glucosa and reg.glucosa < 70:
            alerta_razon = f"Glucosa en nivel de hipoglucemia crítica ({reg.glucosa} mg/dL)."

        if alerta_razon:
            reg.alerta_emitida = True
            NotificacionAlerta.objects.create(
                paciente=paciente,
                tipo='salud_critica',
                mensaje=f"Alerta de Signos Vitales para {paciente.nombre_completo}: {alerta_razon}",
                estado_envio='pendiente'
            )

        reg.save()
        registrar_auditoria(
            request,
            'SIGNOS_REGISTRADOS',
            paciente=paciente,
            detalles=f"Presión: {reg.presion_sistolica}/{reg.presion_diastolica}, Glucosa: {reg.glucosa}",
        )

        if alerta_razon:
            messages.warning(request, f"Signos registrados con alerta preventiva: {alerta_razon}")
        else:
            messages.success(request, "Signos vitales registrados exitosamente dentro de rangos esperados.")

        return redirect('health:lista')

    return render(request, 'health/salud_form.html', {
        'paciente': paciente,
    })
