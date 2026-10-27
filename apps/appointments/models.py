import uuid
from django.db import models
from django.conf import settings
from django.utils import timezone
from apps.patients.models import Paciente

class CitaLaboratorio(models.Model):
    id = models.AutoField(primary_key=True)
    paciente = models.ForeignKey(Paciente, on_delete=models.CASCADE, related_name='citas', db_column='paciente_id')
    doctor = models.ForeignKey('doctors.Doctor', on_delete=models.SET_NULL, null=True, blank=True, db_column='doctor_id')
    familiar_responsable = models.ForeignKey(
        settings.AUTH_USER_MODEL,
        on_delete=models.SET_NULL,
        null=True,
        blank=True,
        db_column='familiar_responsable_id'
    )
    tipo_evento = models.CharField(max_length=50, default='consulta')
    titulo = models.CharField(max_length=255)
    lugar = models.CharField(max_length=255, blank=True, null=True)
    ubicacion_mapa = models.CharField(max_length=500, blank=True, null=True)
    fecha_hora = models.DateTimeField()
    preparacion_previa = models.TextField(blank=True, null=True)
    estado = models.CharField(max_length=20, default='programada')
    notas = models.TextField(blank=True, null=True)
    created_at = models.DateTimeField(default=timezone.now)

    class Meta:
        db_table = 'citas_laboratorios'
        verbose_name = 'Cita o Estudio de Laboratorio'
        verbose_name_plural = 'Citas y Estudios de Laboratorio'
        ordering = ['fecha_hora']

    def __str__(self):
        return f"{self.titulo} - {self.paciente.nombre} ({self.fecha_hora.strftime('%Y-%m-%d %H:%M')})"

class VisitaMedica(models.Model):
    id = models.UUIDField(primary_key=True, default=uuid.uuid4, editable=False)
    paciente = models.ForeignKey(Paciente, on_delete=models.CASCADE, related_name='visitas_clinicas', db_column='paciente_id')
    usuario_registra = models.ForeignKey(
        settings.AUTH_USER_MODEL,
        on_delete=models.SET_NULL,
        null=True,
        blank=True,
        db_column='usuario_registra_id'
    )
    fecha_visita = models.DateTimeField(default=timezone.now)
    lugar_atencion = models.CharField(max_length=150, blank=True, null=True)
    motivo_consulta = models.TextField()
    diagnostico_notas = models.TextField(blank=True, null=True)
    doctor_nombre = models.CharField(max_length=150, blank=True, null=True)
    requiere_seguimiento = models.BooleanField(default=False)
    fecha_seguimiento = models.DateField(null=True, blank=True)
    instrucciones_seguimiento = models.TextField(blank=True, null=True)
    created_at = models.DateTimeField(default=timezone.now)

    class Meta:
        db_table = 'visitas_medicas'
        verbose_name = 'Visita Médica'
        verbose_name_plural = 'Visitas Médicas'
        ordering = ['-fecha_visita']

    def __str__(self):
        return f"Visita {self.paciente.nombre} con {self.doctor_nombre or 'Médico'} ({self.fecha_visita.strftime('%Y-%m-%d')})"
