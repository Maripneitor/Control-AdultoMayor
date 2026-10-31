import io
from pathlib import Path

import qrcode
from django.conf import settings
from django.urls import reverse

from .models import Paciente


def obtener_codigo_sos_corto(paciente: Paciente) -> str:
    """
    Retorna el código corto legible de 8 caracteres para ingreso manual (ej. 'a1b2c3d4')
    impreso debajo del QR para casos donde la cámara del primer respondiente falle.
    """
    return paciente.codigo_sos_corto


def construir_url_sos_publica(request, paciente: Paciente) -> str:
    """
    Construye la URL directa y pública que se codifica dentro del QR:
    Ejemplo: https://vitalsenior.com/sos/a1b2c3d4
    """
    codigo = obtener_codigo_sos_corto(paciente)
    path = reverse('core:sos_publico_directo', kwargs={'token': codigo})
    if request is not None:
        return f"{request.scheme}://{request.get_host()}{path}"
    base_url = getattr(settings, 'APP_URL', 'http://localhost:8000').rstrip('/')
    return f"{base_url}{path}"


def asegurar_imagen_qr_sos(paciente: Paciente, request=None, forzar_regeneracion: bool = False) -> tuple[Path, str]:
    """
    Genera automáticamente la imagen PNG en alta resolución del código QR vinculada
    a la URL pública de emergencia del paciente (`/sos/<codigo>`) y la guarda en `/media/qr_sos/`.
    Retorna `(ruta_absoluta_png, url_media_relativa)`.
    """
    qr_dir = Path(settings.MEDIA_ROOT) / 'qr_sos'
    qr_dir.mkdir(parents=True, exist_ok=True)

    filename = f"qr_sos_{paciente.uuid}.png"
    file_path = qr_dir / filename
    media_url = f"{settings.MEDIA_URL.rstrip('/')}/qr_sos/{filename}"

    if not file_path.exists() or forzar_regeneracion:
        url_sos = construir_url_sos_publica(request, paciente)
        qr = qrcode.QRCode(
            version=2,
            error_correction=qrcode.constants.ERROR_CORRECT_H,  # Alta tolerancia a desgaste en credenciales impresas
            box_size=12,  # Alta resolución para descarga e impresión a 300 DPI
            border=3,
        )
        qr.add_data(url_sos)
        qr.make(fit=True)
        img = qr.make_image(fill_color="#0F172A", back_color="#FFFFFF")
        img.save(str(file_path), format="PNG")

    return file_path, media_url


def generar_qr_png_bytes(url_sos: str) -> bytes:
    """Genera los bytes PNG en alta resolución en memoria para descarga inmediata."""
    qr = qrcode.QRCode(
        version=2,
        error_correction=qrcode.constants.ERROR_CORRECT_H,
        box_size=14,
        border=4,
    )
    qr.add_data(url_sos)
    qr.make(fit=True)
    img = qr.make_image(fill_color="#0F172A", back_color="#FFFFFF")
    buf = io.BytesIO()
    img.save(buf, format="PNG")
    return buf.getvalue()


def buscar_paciente_por_codigo_sos(token_raw: str) -> Paciente | None:
    """
    Busca un paciente a partir del código escaneado en el QR o escrito manualmente:
    - UUID completo (`qr_token`)
    - `token_emergencia_qr` exacto (case-insensitive)
    - Prefijo corto de 8 caracteres del UUID (`a1b2c3d4`) o sufijo corto de `token_emergencia_qr`
    """
    import uuid as uuid_lib

    if not token_raw:
        return None

    limpio = token_raw.strip().lower()
    if not limpio:
        return None

    # 1. Intentar UUID completo
    try:
        uuid_obj = uuid_lib.UUID(limpio)
        paciente = Paciente.objects.filter(qr_token=uuid_obj).first()
        if paciente:
            return paciente
    except (ValueError, TypeError):
        pass

    # 2. Intentar coincidencia exacta en token_emergencia_qr
    paciente = Paciente.objects.filter(token_emergencia_qr__iexact=limpio).first()
    if paciente:
        return paciente

    # 3. Intentar coincidencia por código corto de 6-12 caracteres (primer segmento de UUID o token_emergencia_qr)
    if 4 <= len(limpio) <= 16 and all(c.isalnum() or c in '-_' for c in limpio):
        for p in Paciente.objects.all():
            uuid_prefix = str(p.qr_token).split('-')[0].lower()
            token_corto = obtener_codigo_sos_corto(p).lower()
            if limpio in (uuid_prefix, token_corto) or (
                p.token_emergencia_qr and p.token_emergencia_qr.lower().endswith(limpio)
            ):
                return p

    return None
