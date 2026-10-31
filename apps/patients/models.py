import uuid
from django.db import models
from django.conf import settings
from django.utils import timezone

class Paciente(models.Model):
    id = models.AutoField(primary_key=True)
    usuario = models.OneToOneField(
        settings.AUTH_USER_MODEL,
        on_delete=models.CASCADE,
        null=True,
        blank=True,
        related_name='perfil_paciente_directo',
        db_column='usuario_id'
    )
    nombre = models.CharField(max_length=100)
    apellidos = models.CharField(max_length=100)
    fecha_nacimiento = models.DateField(verbose_name="Fecha de Nacimiento")
    genero = models.CharField(max_length=20, blank=True, null=True)
    tipo_sangre = models.CharField(max_length=5, default='N/D')
    alergias = models.TextField(blank=True, null=True)
    enfermedades_cronicas = models.TextField(blank=True, null=True)
    notas_cognitivas = models.TextField(blank=True, null=True)
    foto_perfil = models.CharField(max_length=255, blank=True, null=True)
    
    # Tokens de Seguridad y Ficha SOS
    qr_token = models.UUIDField(default=uuid.uuid4, unique=True, editable=False)
    token_emergencia_qr = models.CharField(max_length=64, unique=True, null=True, blank=True)
    codigo_vinculacion = models.CharField(max_length=10, unique=True, null=True, blank=True)
    codigo_expira_en = models.DateTimeField(null=True, blank=True)
    codigo_activo = models.BooleanField(default=True)
    zona_horaria = models.CharField(max_length=50, default='America/Mexico_City')
    
    created_at = models.DateTimeField(default=timezone.now)
    updated_at = models.DateTimeField(auto_now=True)

    cuidadores = models.ManyToManyField(
        settings.AUTH_USER_MODEL,
        through='PacienteCuidador',
        related_name='pacientes_a_cargo'
    )

    class Meta:
        db_table = 'pacientes'
        verbose_name = 'Paciente'
        verbose_name_plural = 'Pacientes'

    def __str__(self):
        return f"{self.nombre} {self.apellidos}"

    @property
    def nombre_completo(self):
        return f"{self.nombre} {self.apellidos}".strip()

    @property
    def uuid(self):
        """Identificador UUID público no secuencial para prevenir ataques IDOR en URLs."""
        return self.qr_token

    @property
    def codigo_sos_corto(self):
        """Código corto de 8 caracteres para ingreso manual debajo del QR (ej. 'a1b2c3d4')."""
        return str(self.qr_token).split('-')[0].lower()

class PacienteCuidador(models.Model):
    class NivelAcceso(models.TextChoices):
        COMPLETO = 'completo', 'Acceso Completo'
        SOLO_LECTURA = 'solo_lectura', 'Solo Lectura'
        LECTURA = 'lectura', 'Lectura'
        TOMAS_Y_SIGNOS = 'tomas_y_signos', 'Tomas y Signos'
        FAMILIAR_ADMIN = 'familiar_admin', 'Familiar Administrador'
        ADMIN = 'admin', 'Administrador'
        EDICION_COMPLETA = 'edicion_completa', 'Edición Completa'

    id = models.AutoField(primary_key=True)
    paciente = models.ForeignKey(Paciente, on_delete=models.CASCADE, db_column='paciente_id')
    usuario_cuidador = models.ForeignKey(
        settings.AUTH_USER_MODEL,
        on_delete=models.CASCADE,
        db_column='usuario_cuidador_id',
        related_name='vinculos_cuidador'
    )
    parentesco = models.CharField(max_length=50, default='Familiar')
    nivel_acceso = models.CharField(
        max_length=50,
        choices=NivelAcceso.choices,
        default=NivelAcceso.COMPLETO
    )
    created_at = models.DateTimeField(default=timezone.now)

    class Meta:
        db_table = 'paciente_cuidador'
        unique_together = ('paciente', 'usuario_cuidador')
        verbose_name = 'Vínculo Paciente - Cuidador'
        verbose_name_plural = 'Vínculos Pacientes - Cuidadores'

class ContactoEmergencia(models.Model):
    id = models.AutoField(primary_key=True)
    paciente = models.ForeignKey(Paciente, on_delete=models.CASCADE, related_name='contactos_emergencia', db_column='paciente_id')
    nombre = models.CharField(max_length=150)
    parentesco = models.CharField(max_length=50, default='Familiar')
    telefono = models.CharField(max_length=25)
    email = models.CharField(max_length=255, blank=True, null=True)
    es_prioritario = models.BooleanField(default=False)
    created_at = models.DateTimeField(default=timezone.now)

    class Meta:
        db_table = 'contactos_emergencia'
        verbose_name = 'Contacto de Emergencia'
        verbose_name_plural = 'Contactos de Emergencia'

    def __str__(self):
        return f"{self.nombre} ({self.parentesco}) - {self.telefono}"


from django.db.models.signals import post_save
from django.dispatch import receiver


@receiver(post_save, sender=Paciente)
def generar_qr_sos_automatico(sender, instance, created, **kwargs):
    """
    Al crear o actualizar un paciente, genera automáticamente la imagen PNG
    de su código QR SOS vinculada a `/sos/<codigo_corto>` en la carpeta `/media/qr_sos/`.
    """
    try:
        from .services import asegurar_imagen_qr_sos
        asegurar_imagen_qr_sos(instance)
    except Exception:
        pass
