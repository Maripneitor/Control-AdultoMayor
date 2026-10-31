import uuid
from django.db import models
from django.conf import settings
from django.utils import timezone

class TimeStampedModel(models.Model):
    created_at = models.DateTimeField(auto_now_add=True)
    updated_at = models.DateTimeField(auto_now=True)

    class Meta:
        abstract = True

class LogAuditoria(models.Model):
    id = models.UUIDField(primary_key=True, default=uuid.uuid4, editable=False)
    usuario = models.ForeignKey(
        settings.AUTH_USER_MODEL,
        on_delete=models.SET_NULL,
        null=True,
        blank=True,
        db_column='usuario_id',
        related_name='logs_auditoria'
    )
    paciente = models.ForeignKey(
        'patients.Paciente',
        on_delete=models.SET_NULL,
        null=True,
        blank=True,
        db_column='paciente_id',
        related_name='logs_auditoria'
    )
    accion = models.CharField(max_length=100)
    detalles = models.TextField(blank=True, null=True)
    ip_origen = models.CharField(max_length=45, blank=True, null=True)
    user_agent = models.CharField(max_length=255, blank=True, null=True)
    fecha_utc = models.DateTimeField(default=timezone.now, db_column='fecha_utc')

    class Meta:
        db_table = 'logs_auditoria'
        verbose_name = 'Log de Auditoría'
        verbose_name_plural = 'Logs de Auditoría'
        ordering = ['-fecha_utc']

    def __str__(self):
        return f"[{self.fecha_utc.strftime('%Y-%m-%d %H:%M')}] {self.accion}"

class NotificacionAlerta(models.Model):
    id = models.AutoField(primary_key=True)
    paciente = models.ForeignKey(
        'patients.Paciente',
        on_delete=models.CASCADE,
        db_column='paciente_id',
        related_name='notificaciones'
    )
    tipo = models.CharField(max_length=50)  # 'sos', 'stock_bajo', 'toma_omitida', 'salud_critica'
    mensaje = models.TextField()
    destinatario_telefono = models.CharField(max_length=20, blank=True, null=True)
    estado_envio = models.CharField(max_length=20, default='pendiente')
    created_at = models.DateTimeField(default=timezone.now)

    class Meta:
        db_table = 'notificaciones_alertas'
        verbose_name = 'Notificación de Alerta'
        verbose_name_plural = 'Notificaciones de Alertas'
        ordering = ['-created_at']

    def __str__(self):
        return f"[{self.tipo.upper()}] {self.paciente.nombre} - {self.mensaje[:30]}"
