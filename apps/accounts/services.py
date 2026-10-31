import base64
import io
import secrets
from datetime import timedelta

import qrcode
from django.conf import settings
from django.core.cache import cache
from django.core.mail import EmailMultiAlternatives
from django.template.loader import render_to_string
from django.utils import timezone
from django_otp.plugins.otp_static.models import StaticDevice, StaticToken
from django_otp.plugins.otp_totp.models import TOTPDevice

from .models import TokenRecuperacion, Usuario

MAGIC_CODE_TTL_SECONDS = 300  # Expiración estricta de 5 minutos (300 segundos)
LOCKOUT_TTL_SECONDS = 900     # Bloqueo temporal de 15 minutos (900 segundos) tras 5 fallos
MAX_OTP_ATTEMPTS = 5


def _cache_key_code(user_id: int) -> str:
    return f"vitalsenior_magic_code_user_{user_id}"


def _cache_key_attempts(identifier: str) -> str:
    return f"vitalsenior_2fa_attempts_{identifier}"


def _cache_key_lockout(identifier: str) -> str:
    return f"vitalsenior_2fa_lockout_{identifier}"


def esta_bloqueado_2fa(identifier: str) -> bool:
    """Verifica si la cuenta o correo está bloqueado por 15 minutos tras 5 fallos de 2FA/código."""
    return bool(cache.get(_cache_key_lockout(str(identifier).lower())))


def registrar_fallo_2fa(identifier: str) -> int:
    """
    Incrementa el contador de fallos 2FA/OTP. Si alcanza 5 intentos seguidos,
    activa el bloqueo temporal de 15 minutos (900s). Retorna los intentos restantes.
    """
    ident = str(identifier).lower()
    attempts_key = _cache_key_attempts(ident)
    intentos = cache.get(attempts_key, 0) + 1
    cache.set(attempts_key, intentos, timeout=LOCKOUT_TTL_SECONDS)

    if intentos >= MAX_OTP_ATTEMPTS:
        cache.set(_cache_key_lockout(ident), True, timeout=LOCKOUT_TTL_SECONDS)
        return 0
    return max(0, MAX_OTP_ATTEMPTS - intentos)


def limpiar_fallos_2fa(identifier: str) -> None:
    ident = str(identifier).lower()
    cache.delete(_cache_key_attempts(ident))
    cache.delete(_cache_key_lockout(ident))


def generar_y_enviar_codigo_magico(user: Usuario) -> str:
    """
    Genera un código numérico aleatorio de 6 dígitos usando `secrets.SystemRandom().randint(100000, 999999)`,
    lo almacena en la caché del servidor y en PostgreSQL asociado al ID del usuario con expiración
    estricta de 5 minutos (de un solo uso), y lo envía por correo con `EmailMultiAlternatives`.
    """
    codigo_generado = str(secrets.SystemRandom().randint(100000, 999999))
    expira_en = timezone.now() + timedelta(seconds=MAGIC_CODE_TTL_SECONDS)

    # 1. Almacenar en caché del servidor (5 minutos exactos)
    cache.set(_cache_key_code(user.id), codigo_generado, timeout=MAGIC_CODE_TTL_SECONDS)

    # 2. Almacenar respaldo en PostgreSQL invalidando todos los códigos anteriores
    TokenRecuperacion.objects.filter(usuario=user, usado=False).update(usado=True)
    TokenRecuperacion.objects.create(
        usuario=user,
        token=codigo_generado,
        expira_en=expira_en,
        usado=False,
        created_at=timezone.now(),
    )

    # 3. Renderizar template HTML clásico basado en tablas con contexto {'codigo': codigo_generado}
    primer_nombre = user.nombre.split()[0] if user.nombre else ''
    contexto = {
        'codigo': codigo_generado,
        'nombre_usuario': primer_nombre,
        'usuario': user,
    }
    html_content = render_to_string('emails/codigo_acceso.html', contexto)
    text_content = (
        f"Hola {primer_nombre},\n\n"
        f"Aquí tienes tu código de acceso seguro para VitalSenior:\n\n"
        f"    {codigo_generado}\n\n"
        f"Este código expira en 5 minutos. Si no intentaste iniciar sesión, "
        f"puedes ignorar este mensaje de forma segura."
    )

    asunto = f"{codigo_generado} es tu código de acceso a VitalSenior"
    remitente = getattr(settings, 'DEFAULT_FROM_EMAIL', 'VitalSenior <no-reply@vitalsenior.com>')

    msg = EmailMultiAlternatives(
        subject=asunto,
        body=text_content,
        from_email=remitente,
        to=[user.email],
    )
    msg.attach_alternative(html_content, "text/html")
    msg.send(fail_silently=False)

    return codigo_generado


def validar_codigo_magico(user: Usuario | None, email_intento: str, codigo_ingresado: str) -> tuple[bool, str]:
    """
    Valida el código de 6 dígitos con protección Zero Trust:
    - Si falla 5 veces seguidas, bloquea temporalmente por 15 minutos.
    - El código es de un solo uso y caduca exactamente a los 5 minutos.
    - No revela si el correo existe o no en la base de datos.
    """
    ident = user.email if user else (email_intento or 'anon')
    if esta_bloqueado_2fa(ident):
        return False, "Demasiados intentos fallidos. Por seguridad, el acceso está bloqueado temporalmente por 15 minutos."

    codigo_limpio = ''.join(ch for ch in (codigo_ingresado or '') if ch.isdigit())
    if len(codigo_limpio) != 6:
        return False, "Ingresa los 6 números del código enviado a tu correo."

    valido = False
    if user and user.activo:
        ahora = timezone.now()
        codigo_cache = cache.get(_cache_key_code(user.id))
        token_bd = (
            TokenRecuperacion.objects.filter(
                usuario=user,
                usado=False,
                expira_en__gte=ahora,
            )
            .order_by('-created_at')
            .first()
        )

        if codigo_cache and secrets.compare_digest(str(codigo_cache), codigo_limpio):
            valido = True
        elif token_bd and secrets.compare_digest(token_bd.token.strip(), codigo_limpio):
            valido = True

    if valido and user:
        # Código estrictamente de un solo uso: eliminar de caché y marcar como usado en BD
        cache.delete(_cache_key_code(user.id))
        TokenRecuperacion.objects.filter(usuario=user, usado=False).update(usado=True)
        limpiar_fallos_2fa(ident)
        return True, ""

    restantes = registrar_fallo_2fa(ident)
    if restantes == 0:
        if user:
            cache.delete(_cache_key_code(user.id))
            TokenRecuperacion.objects.filter(usuario=user, usado=False).update(usado=True)
        return False, "Has superado los 5 intentos permitidos. Cuenta bloqueada temporalmente por 15 minutos."

    return False, f"El código ingresado es inválido o ha expirado. Intentos restantes: {restantes}."


def obtener_dispositivo_totp_activo(user: Usuario):
    """Retorna el dispositivo TOTP confirmado del usuario si tiene 2FA activado."""
    return TOTPDevice.objects.filter(user=user, confirmed=True).first()


def verificar_token_2fa_usuario(user: Usuario, token_ingresado: str):
    """
    Verifica el código de 6 dígitos de Google Authenticator / Authy (`TOTPDevice`)
    o un código de respaldo (`StaticDevice`) de `django-otp`.
    """
    token_limpio = (token_ingresado or '').strip().replace(' ', '')
    if not token_limpio:
        return None

    for device in TOTPDevice.objects.filter(user=user, confirmed=True):
        if device.verify_token(token_limpio):
            return device

    for static_device in StaticDevice.objects.filter(user=user, confirmed=True):
        if static_device.verify_token(token_limpio):
            return static_device

    return None


def crear_o_obtener_dispositivo_totp_pendiente(user: Usuario) -> TOTPDevice:
    device = TOTPDevice.objects.filter(user=user, confirmed=False).first()
    if not device:
        device = TOTPDevice.objects.create(
            user=user,
            name="Autenticador Principal",
            confirmed=False,
        )
    return device


def generar_qr_data_uri(config_url: str) -> str:
    qr = qrcode.QRCode(
        version=1,
        error_correction=qrcode.constants.ERROR_CORRECT_M,
        box_size=8,
        border=2,
    )
    qr.add_data(config_url)
    qr.make(fit=True)
    img = qr.make_image(fill_color="#0F172A", back_color="#FFFFFF")
    buffer = io.BytesIO()
    img.save(buffer, format="PNG")
    encoded = base64.b64encode(buffer.getvalue()).decode("ascii")
    return f"data:image/png;base64,{encoded}"


def regenerar_codigos_respaldo(user: Usuario, cantidad: int = 6) -> list[str]:
    device, _ = StaticDevice.objects.get_or_create(
        user=user,
        name="Códigos de Respaldo",
        defaults={'confirmed': True},
    )
    device.confirmed = True
    device.save()
    device.token_set.all().delete()
    codigos = []
    for _ in range(cantidad):
        token_str = StaticToken.random_token()
        StaticToken.objects.create(device=device, token=token_str)
        codigos.append(token_str)
    return codigos
