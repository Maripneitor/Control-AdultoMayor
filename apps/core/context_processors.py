def patient_context(request):
    """
    Context processor para inyectar los datos del paciente activo
    en todos los templates, preservando las reglas de php-view-flow-guardian.
    """
    if not hasattr(request, 'user') or not request.user.is_authenticated:
        return {'paciente_activo': None}

    paciente_id = request.session.get('paciente_activo_id')
    paciente_nombre = request.session.get('paciente_activo_nombre', 'Sin Paciente')
    
    return {
        'paciente_activo_id': paciente_id,
        'paciente_activo_nombre': paciente_nombre,
    }
