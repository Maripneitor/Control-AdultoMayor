from axes.handlers.proxy import AxesProxyHandler
from django.conf import settings
from django.contrib import messages
from django.contrib.auth import authenticate, login, logout, update_session_auth_hash
from django.contrib.auth.decorators import login_required
from django.core.cache import cache
from django.shortcuts import redirect, render
from django.utils import timezone
from django_otp import login as otp_login
from django_otp.plugins.otp_static.models import StaticDevice
from django_otp.plugins.otp_totp.models import TOTPDevice

from apps.core.utils import registrar_auditoria
from apps.patients.models import Paciente

from .forms import EmailStepForm, OTPCodeForm, PasswordStepForm, RegistroUsuarioForm
from .models import Usuario
from .services import (
    crear_o_obtener_dispositivo_totp_pendiente,
    esta_bloqueado_2fa,
    generar_qr_data_uri,
    generar_y_enviar_codigo_magico,
    limpiar_fallos_2fa,
    obtener_dispositivo_totp_activo,
    regenerar_codigos_respaldo,
    registrar_fallo_2fa,
    validar_codigo_magico,
    verificar_token_2fa_usuario,
)


def _completar_inicio_sesion(request, user: Usuario, metodo_detalle: str, otp_device=None):
    """
    Finaliza la sesión autenticada del usuario, configura su paciente activo si aplica,
    marca el dispositivo OTP en la sesión si utilizó 2FA, y redirige al destino.
    """
    for key in (
        'auth_flow_user_id',
        'auth_flow_email',
        'auth_flow_step',
        'auth_flow_password_verified',
        'auth_flow_next',
        'dev_magic_code_preview',
    ):
        request.session.pop(key, None)

    login(request, user, backend='django.contrib.auth.backends.ModelBackend')
    if otp_device is not None:
        otp_login(request, otp_device)

    request.session['auth_session_token'] = str(timezone.now().timestamp())

    if user.rol in ['paciente', 'adulto_mayor']:
        paciente = getattr(user, 'perfil_paciente_directo', None)
        if paciente:
            request.session['paciente_activo_id'] = paciente.id
            request.session['paciente_activo_nombre'] = paciente.nombre_completo

    registrar_auditoria(request, 'LOGIN_EXITOSO', detalles=f"{metodo_detalle} (rol: {user.rol})")
    messages.success(request, f"Bienvenido de nuevo, {user.nombre}")

    next_url = request.GET.get('next') or request.POST.get('next')
    if next_url and next_url.startswith('/') and not next_url.startswith('//'):
        return redirect(next_url)
    return redirect('core:dashboard')


def _obtener_usuario_en_flujo(request) -> Usuario | None:
    user_id = request.session.get('auth_flow_user_id')
    if not user_id:
        return None
    return Usuario.objects.filter(id=user_id, activo=True).first()


def _extraer_codigo_otp_post(request) -> str:
    """
    Obtiene el código de 6 dígitos ya sea desde el input oculto `otp_code`
    o concatenando directamente las 6 casillas `otp_digit_1` .. `otp_digit_6`.
    """
    codigo_oculto = (request.POST.get('otp_code') or '').strip()
    digitos_oculto = ''.join(ch for ch in codigo_oculto if ch.isdigit())
    if len(digitos_oculto) == 6:
        return digitos_oculto

    casillas = ''.join((request.POST.get(f'otp_digit_{i}') or '').strip() for i in range(1, 7))
    digitos_casillas = ''.join(ch for ch in casillas if ch.isdigit())
    return digitos_casillas or digitos_oculto or codigo_oculto


def login_view(request):
    """
    Flujo de Inicio de Sesión Dual bajo Arquitectura Zero Trust:
      - Rate Limiting de 5 intentos / 15 minutos con `django-axes` + bloqueo 2FA.
      - Anti-Enumeración: jamás revela si un correo electrónico está registrado o no.
      - Expiración estricta de 5 minutos y un solo uso para el código por correo.
    """
    if request.user.is_authenticated:
        return redirect('core:dashboard')

    if request.GET.get('reset') == '1':
        for key in (
            'auth_flow_user_id',
            'auth_flow_email',
            'auth_flow_step',
            'auth_flow_password_verified',
            'dev_magic_code_preview',
        ):
            request.session.pop(key, None)
        return redirect('accounts:login')

    step_param = request.GET.get('step')
    flow_email = request.session.get('auth_flow_email', '')
    flow_user = _obtener_usuario_en_flujo(request)
    if flow_email and step_param in ('method', 'password', 'magic_code'):
        request.session['auth_flow_step'] = step_param
        request.session['auth_flow_password_verified'] = False

    if request.method == 'POST':
        action = request.POST.get('action', '').strip()

        # Compatibilidad con envío directo de email + password en un solo paso
        if not action and request.POST.get('email') and request.POST.get('password'):
            email_directo = request.POST.get('email', '').strip().lower()
            user_directo = Usuario.objects.filter(email__iexact=email_directo, activo=True).first()
            request.session['auth_flow_email'] = email_directo
            request.session['auth_flow_user_id'] = user_directo.id if user_directo else None
            flow_email = email_directo
            flow_user = user_directo
            action = 'login_password'

        # ======================================================================
        # PASO 1: Validación de Formato de Correo (Sin revelar si existe en BD)
        # ======================================================================
        if action in ('validate_email', ''):
            form = EmailStepForm(request.POST)
            if not form.is_valid():
                messages.error(request, "Por favor, escribe un correo electrónico válido para continuar.")
                return render(request, 'accounts/login.html', {'step': 'email'})

            email = form.cleaned_data['email']
            user = Usuario.objects.filter(email__iexact=email, activo=True).first()

            # Zero Trust Anti-Enumeración: avanzamos al Paso 2 sin revelar si el correo existe o no
            request.session['auth_flow_email'] = email
            request.session['auth_flow_user_id'] = user.id if user else None
            request.session['auth_flow_step'] = 'method'
            request.session['auth_flow_password_verified'] = False
            flow_email = email
            flow_user = user

        # ======================================================================
        # PASO 2A: Usuario elige "Ingresar con Contraseña"
        # ======================================================================
        elif action == 'choose_password':
            if not flow_email:
                return redirect('accounts:login')
            request.session['auth_flow_step'] = 'password'

        # ======================================================================
        # PASO 2B / REENVÍO: Usuario elige "Enviar código temporal por correo"
        # ======================================================================
        elif action in ('choose_magic_code', 'resend_magic_code'):
            if not flow_email:
                return redirect('accounts:login')

            if esta_bloqueado_2fa(flow_email):
                messages.error(
                    request,
                    "Demasiados intentos fallidos. Tu acceso está bloqueado temporalmente por 15 minutos."
                )
                return render(
                    request,
                    'accounts/login.html',
                    {'step': 'method', 'flow_user': flow_user, 'email_value': flow_email},
                )

            if flow_user and flow_user.activo:
                try:
                    codigo = generar_y_enviar_codigo_magico(flow_user)
                    if settings.DEBUG or 'console' in getattr(settings, 'EMAIL_BACKEND', ''):
                        request.session['dev_magic_code_preview'] = codigo
                    registrar_auditoria(
                        request,
                        'CODIGO_CORREO_ENVIADO',
                        detalles=f"Código temporal de 6 dígitos enviado a {flow_user.email}",
                    )
                except Exception as exc:
                    registrar_auditoria(
                        request,
                        'ERROR_ENVIO_CODIGO',
                        detalles=f"Fallo al enviar código a {flow_user.email}: {exc}",
                    )

            # Mensaje genérico Anti-Enumeración: idéntico exista o no el correo en la base de datos
            request.session['auth_flow_step'] = 'magic_code'
            messages.success(
                request,
                "Si el correo está registrado, hemos enviado un código de 6 dígitos (expira en 5 minutos)."
            )

        # ======================================================================
        # PASO 3A (Parte 1): Validación de Contraseña (con django-axes)
        # ======================================================================
        elif action == 'login_password':
            email = (flow_email or request.POST.get('email', '')).strip().lower()
            pwd_form = PasswordStepForm(request.POST)
            if not pwd_form.is_valid():
                messages.error(request, "Ingresa tu contraseña para continuar.")
                request.session['auth_flow_step'] = 'password'
            else:
                password = pwd_form.cleaned_data['password']

                # Verificar bloqueo en django-axes (5 intentos fallidos -> 15 min) o en 2FA
                credentials = {'username': email}
                if AxesProxyHandler.is_locked(request, credentials) or esta_bloqueado_2fa(email):
                    registrar_auditoria(
                        request,
                        'CUENTA_BLOQUEADA_AXES',
                        detalles=f"Intento durante bloqueo de 15 min para {email}",
                    )
                    messages.error(
                        request,
                        "Demasiados intentos fallidos. Tu cuenta ha sido bloqueada temporalmente por 15 minutos."
                    )
                    return render(
                        request,
                        'accounts/login.html',
                        {'step': 'password', 'flow_user': flow_user, 'email_value': email},
                    )

                user = authenticate(request, username=email, password=password)
                if user is not None and user.activo:
                    limpiar_fallos_2fa(email)
                    if user.tiene_2fa_activo:
                        request.session['auth_flow_user_id'] = user.id
                        request.session['auth_flow_email'] = user.email
                        request.session['auth_flow_password_verified'] = True
                        request.session['auth_flow_step'] = 'totp_2fa'
                        flow_user = user
                    else:
                        return _completar_inicio_sesion(request, user, "Inicio de sesión con contraseña")
                else:
                    restantes = registrar_fallo_2fa(email)
                    registrar_auditoria(request, 'LOGIN_FALLIDO', detalles=f"Fallo de contraseña para {email}")
                    if AxesProxyHandler.is_locked(request, credentials) or restantes == 0:
                        messages.error(
                            request,
                            "Demasiados intentos fallidos. Tu cuenta ha sido bloqueada temporalmente por 15 minutos."
                        )
                    else:
                        messages.error(request, "Credenciales inválidas. Verifica tu correo y contraseña.")
                    request.session['auth_flow_step'] = 'password'

        # ======================================================================
        # PASO 3A (Parte 2): Validación de Token 2FA (App Autenticadora)
        # ======================================================================
        elif action == 'verify_totp_2fa':
            if not flow_user or not request.session.get('auth_flow_password_verified'):
                messages.error(request, "Por seguridad, valida primero tu contraseña.")
                return redirect('accounts:login')

            if esta_bloqueado_2fa(flow_user.email):
                request.session['auth_flow_password_verified'] = False
                request.session['auth_flow_step'] = 'password'
                messages.error(
                    request,
                    "Demasiados intentos fallidos de 2FA. Tu cuenta ha sido bloqueada temporalmente por 15 minutos."
                )
                return redirect('accounts:login')

            codigo_post = _extraer_codigo_otp_post(request)
            otp_form = OTPCodeForm({'otp_code': codigo_post})
            totp_code = otp_form.cleaned_data['otp_code'] if otp_form.is_valid() else codigo_post

            device = verificar_token_2fa_usuario(flow_user, totp_code)
            if device is not None:
                limpiar_fallos_2fa(flow_user.email)
                return _completar_inicio_sesion(
                    request,
                    flow_user,
                    "Inicio de sesión con Contraseña + 2FA App",
                    otp_device=device,
                )
            else:
                restantes = registrar_fallo_2fa(flow_user.email)
                registrar_auditoria(
                    request,
                    'LOGIN_2FA_FALLIDO',
                    detalles=f"Token 2FA inválido para {flow_user.email}",
                )
                if restantes == 0:
                    request.session['auth_flow_password_verified'] = False
                    request.session['auth_flow_step'] = 'password'
                    messages.error(
                        request,
                        "Has fallado el código 2FA 5 veces seguidas. Cuenta bloqueada temporalmente por 15 minutos."
                    )
                else:
                    messages.error(
                        request,
                        f"El código de 6 dígitos no es válido o expiró. Intentos restantes: {restantes}."
                    )
                    request.session['auth_flow_step'] = 'totp_2fa'

        # ======================================================================
        # PASO 3B: Validación de Código Temporal por Correo (Magic Code 6 dígitos)
        # ======================================================================
        elif action == 'verify_magic_code':
            if not flow_email:
                return redirect('accounts:login')

            codigo_ingresado = _extraer_codigo_otp_post(request)
            es_valido, msg_error = validar_codigo_magico(flow_user, flow_email, codigo_ingresado)
            if es_valido and flow_user:
                return _completar_inicio_sesion(
                    request,
                    flow_user,
                    "Inicio de sesión con Código Temporal por Correo (2FA natural)",
                )
            else:
                registrar_auditoria(
                    request,
                    'CODIGO_CORREO_FALLIDO',
                    detalles=f"Código temporal inválido para {flow_email}",
                )
                messages.error(request, msg_error)
                request.session['auth_flow_step'] = 'magic_code'

    current_step = request.session.get('auth_flow_step', 'email')
    if not flow_email:
        current_step = 'email'
    elif current_step == 'totp_2fa' and (not flow_user or not request.session.get('auth_flow_password_verified')):
        current_step = 'password'

    context = {
        'step': current_step,
        'flow_user': flow_user,
        'email_value': flow_email,
        'dev_magic_code_preview': request.session.get('dev_magic_code_preview') if settings.DEBUG else None,
    }
    return render(request, 'accounts/login.html', context)


def logout_view(request):
    if request.user.is_authenticated:
        registrar_auditoria(request, 'LOGOUT', detalles="Cierre de sesión voluntario")
    logout(request)
    messages.info(request, "Has cerrado sesión correctamente.")
    return redirect('accounts:login')


def registro_view(request):
    if request.user.is_authenticated:
        return redirect('core:dashboard')

    if request.method == 'POST':
        form = RegistroUsuarioForm(request.POST)
        if not form.is_valid():
            for field_errors in form.errors.values():
                for err in field_errors:
                    messages.error(request, err)
            return render(request, 'accounts/registro.html')

        nombre = form.cleaned_data['nombre']
        email = form.cleaned_data['email']
        password = form.cleaned_data['password']
        telefono = form.cleaned_data.get('telefono', '')
        rol = form.cleaned_data['rol']

        if Usuario.objects.filter(email__iexact=email).exists():
            # Mensaje prudente sin exponer detalles sensibles
            messages.error(request, "No fue posible completar el registro con ese correo. Intenta iniciar sesión.")
            return render(request, 'accounts/registro.html')

        user = Usuario.objects.create_user(
            email=email,
            nombre=nombre,
            password=password,
            telefono=telefono,
            rol=rol,
        )

        if rol == 'paciente':
            import random
            import string
            import uuid

            fecha_nac = request.POST.get('fecha_nacimiento') or '1950-01-01'
            codigo = ''.join(random.choices(string.ascii_uppercase + string.digits, k=6))
            paciente = Paciente.objects.create(
                usuario=user,
                nombre=nombre,
                apellidos="Titular",
                fecha_nacimiento=fecha_nac,
                codigo_vinculacion=codigo,
                token_emergencia_qr=str(uuid.uuid4())[:16],
            )
            request.session['paciente_activo_id'] = paciente.id
            request.session['paciente_activo_nombre'] = paciente.nombre_completo

        login(request, user, backend='django.contrib.auth.backends.ModelBackend')
        registrar_auditoria(
            request,
            'REGISTRO_USUARIO',
            detalles=f"Nuevo usuario {user.email} con rol {user.rol}",
        )
        messages.success(request, "¡Tu cuenta ha sido creada exitosamente!")
        return redirect('core:dashboard')

    return render(request, 'accounts/registro.html')


def recuperar_view(request):
    if request.method == 'POST':
        form = EmailStepForm(request.POST)
        if form.is_valid():
            email = form.cleaned_data['email']
            user = Usuario.objects.filter(email__iexact=email, activo=True).first()
            request.session['auth_flow_email'] = email
            request.session['auth_flow_user_id'] = user.id if user else None
            request.session['auth_flow_step'] = 'magic_code'

            if user and not esta_bloqueado_2fa(email):
                try:
                    codigo = generar_y_enviar_codigo_magico(user)
                    if settings.DEBUG or 'console' in getattr(settings, 'EMAIL_BACKEND', ''):
                        request.session['dev_magic_code_preview'] = codigo
                    registrar_auditoria(
                        request,
                        'SOLICITUD_RECUPERACION',
                        detalles=f"Código temporal enviado para recuperación de {email}",
                    )
                except Exception:
                    pass

        # Anti-Enumeración: siempre mostrar exactamente el mismo mensaje
        messages.success(
            request,
            "Si el correo está registrado, hemos enviado un código de acceso temporal."
        )
        return redirect('accounts:login')

    return render(request, 'accounts/recuperar.html')


@login_required
def perfil_view(request):
    user = request.user
    if request.method == 'POST':
        action = request.POST.get('action', 'update_profile')

        if action == 'enable_2fa':
            token_2fa = request.POST.get('otp_code', '').strip().replace(' ', '')
            pending_device = crear_o_obtener_dispositivo_totp_pendiente(user)
            if pending_device.verify_token(token_2fa):
                TOTPDevice.objects.filter(user=user, confirmed=True).exclude(id=pending_device.id).delete()
                pending_device.confirmed = True
                pending_device.save()
                codigos_respaldo = regenerar_codigos_respaldo(user)
                request.session['nuevos_codigos_respaldo'] = codigos_respaldo
                otp_login(request, pending_device)
                registrar_auditoria(
                    request,
                    '2FA_ACTIVADO',
                    detalles="El usuario vinculó su aplicación autenticadora 2FA (TOTP)",
                )
                messages.success(
                    request,
                    "¡Verificación en 2 pasos (2FA) activada correctamente! Guarda tus códigos de respaldo."
                )
            else:
                messages.error(
                    request,
                    "El código de 6 dígitos no coincide. Escanea el código QR en tu app e inténtalo de nuevo."
                )
            return redirect('accounts:perfil')

        elif action == 'disable_2fa':
            TOTPDevice.objects.filter(user=user).delete()
            StaticDevice.objects.filter(user=user).delete()
            request.session.pop('nuevos_codigos_respaldo', None)
            registrar_auditoria(request, '2FA_DESACTIVADO', detalles="El usuario desactivó 2FA")
            messages.info(request, "La verificación con app autenticadora (2FA) fue desactivada.")
            return redirect('accounts:perfil')

        elif action == 'regenerate_backup_codes':
            if user.tiene_2fa_activo:
                request.session['nuevos_codigos_respaldo'] = regenerar_codigos_respaldo(user)
                messages.success(request, "Se generaron nuevos códigos de respaldo de un solo uso.")
            return redirect('accounts:perfil')

        nombre = request.POST.get('nombre', '').strip()[:255]
        telefono = request.POST.get('telefono', '').strip()[:20]
        current_password = request.POST.get('current_password')
        new_password = request.POST.get('new_password')
        confirm_password = request.POST.get('confirm_password')

        if nombre:
            user.nombre = nombre
        user.telefono = telefono

        if current_password and new_password:
            if not user.check_password(current_password):
                messages.error(request, "La contraseña actual es incorrecta.")
                return redirect('accounts:perfil')
            if new_password != confirm_password:
                messages.error(request, "La nueva contraseña y su confirmación no coinciden.")
                return redirect('accounts:perfil')
            if len(new_password) < 8 or len(new_password) > 128:
                messages.error(request, "La nueva contraseña debe tener entre 8 y 128 caracteres.")
                return redirect('accounts:perfil')

            user.set_password(new_password)
            user.save()
            update_session_auth_hash(request, user)
            registrar_auditoria(request, 'PASSWORD_CAMBIADO', detalles="El usuario actualizó su contraseña")
            messages.success(request, "Perfil y contraseña actualizados exitosamente.")
            return redirect('accounts:perfil')

        user.save()
        registrar_auditoria(request, 'PERFIL_ACTUALIZADO', detalles="Datos generales actualizados")
        messages.success(request, "Perfil actualizado correctamente.")
        return redirect('accounts:perfil')

    dispositivo_activo = obtener_dispositivo_totp_activo(user)
    qr_data_uri = None
    secret_key_b32 = None
    if not dispositivo_activo:
        pending_device = crear_o_obtener_dispositivo_totp_pendiente(user)
        qr_data_uri = generar_qr_data_uri(pending_device.config_url)
        import base64

        secret_key_b32 = base64.b32encode(pending_device.bin_key).decode('utf-8')

    codigos_respaldo = request.session.pop('nuevos_codigos_respaldo', None)
    static_device = StaticDevice.objects.filter(user=user, confirmed=True).first()
    cantidad_codigos_restantes = static_device.token_set.count() if static_device else 0

    return render(
        request,
        'accounts/perfil.html',
        {
            'tiene_2fa': bool(dispositivo_activo),
            'qr_data_uri': qr_data_uri,
            'secret_key_b32': secret_key_b32,
            'codigos_respaldo': codigos_respaldo,
            'cantidad_codigos_restantes': cantidad_codigos_restantes,
        },
    )
