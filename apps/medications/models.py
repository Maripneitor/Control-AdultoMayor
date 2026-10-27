from django.db import models
from django.conf import settings
from django.utils import timezone
from apps.patients.models import Paciente

class Medicamento(models.Model):
    id = models.AutoField(primary_key=True)
    paciente = models.ForeignKey(Paciente, on_delete=models.CASCADE, related_name='medicamentos', db_column='paciente_id')
    nombre_comercial = models.CharField(max_length=255, verbose_name="Nombre Comercial")
    dosis = models.CharField(max_length=100, verbose_name="Dosis")
    frecuencia_horas = models.IntegerField(default=8, verbose_name="Frecuencia (Horas)")
    instrucciones = models.TextField(blank=True, null=True, verbose_name="Instrucciones")
    stock_actual = models.IntegerField(default=0, verbose_name="Stock Actual")
    dosis_por_toma = models.IntegerField(default=1, verbose_name="Dosis por Toma")
    stock_minimo_alerta = models.IntegerField(default=5, verbose_name="Stock Mínimo para Alerta")
    activo = models.BooleanField(default=True)
    created_at = models.DateTimeField(default=timezone.now)

    class Meta:
        db_table = 'medicamentos'
        verbose_name = 'Medicamento'
        verbose_name_plural = 'Medicamentos'

    def __str__(self):
        return f"{self.nombre_comercial} ({self.dosis})"

    @property
    def stock_bajo(self):
        return self.stock_actual <= self.stock_minimo_alerta

class TomasHistorial(models.Model):
    class EstadoToma(models.TextChoices):
        PENDIENTE = 'pendiente', 'Pendiente'
        TOMADO = 'tomado', 'Tomado'
        OMITIDO = 'omitido', 'Omitido'
        POSPUESTO = 'pospuesto', 'Pospuesto'
        DESHECHO = 'deshecho', 'Deshecho'

    id = models.AutoField(primary_key=True)
    medicamento = models.ForeignKey(Medicamento, on_delete=models.CASCADE, related_name='tomas', db_column='medicamento_id')
    paciente = models.ForeignKey(Paciente, on_delete=models.CASCADE, related_name='tomas_historial', db_column='paciente_id')
    fecha_programada = models.DateField()
    hora_programada = models.TimeField()
    fecha_programada_utc = models.DateTimeField(null=True, blank=True)
    pospuesto_hasta = models.DateTimeField(null=True, blank=True)
    estado = models.CharField(
        max_length=20,
        choices=EstadoToma.choices,
        default=EstadoToma.PENDIENTE
    )
    tomado_at = models.DateTimeField(null=True, blank=True)
    deshecho_at = models.DateTimeField(null=True, blank=True)
    recordatorio_enviado = models.BooleanField(default=False)
    veces_pospuesto = models.IntegerField(default=0)
    nota_incidencia = models.TextField(blank=True, null=True)
    notificacion_enviada_at = models.DateTimeField(null=True, blank=True)
    registrado_por = models.ForeignKey(
        settings.AUTH_USER_MODEL,
        on_delete=models.SET_NULL,
        null=True,
        blank=True,
        db_column='registrado_por_usuario_id'
    )
    created_at = models.DateTimeField(default=timezone.now)

    class Meta:
        db_table = 'tomas_historial'
        verbose_name = 'Historial de Toma'
        verbose_name_plural = 'Historial de Tomas'
        indexes = [
            models.Index(fields=['paciente', 'fecha_programada', 'estado']),
        ]

    def __str__(self):
        return f"{self.medicamento.nombre_comercial} - {self.fecha_programada} {self.hora_programada} [{self.get_estado_display()}]"
