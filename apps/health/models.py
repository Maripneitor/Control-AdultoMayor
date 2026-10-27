from django.db import models
from django.conf import settings
from django.utils import timezone
from apps.patients.models import Paciente

class RegistroSaludDiario(models.Model):
    id = models.AutoField(primary_key=True)
    paciente = models.ForeignKey(Paciente, on_delete=models.CASCADE, related_name='signos_vitales', db_column='paciente_id')
    presion_sistolica = models.IntegerField(null=True, blank=True, verbose_name="Presión Sistólica (mmHg)")
    presion_diastolica = models.IntegerField(null=True, blank=True, verbose_name="Presión Diastólica (mmHg)")
    ritmo_cardiaco = models.IntegerField(null=True, blank=True, verbose_name="Ritmo Cardíaco (ppm)")
    glucosa = models.DecimalField(max_digits=5, decimal_places=2, null=True, blank=True, verbose_name="Glucosa (mg/dL)")
    momento_glucosa = models.CharField(max_length=50, default='ayunas')
    horas_sueno = models.DecimalField(max_digits=4, decimal_places=2, null=True, blank=True, verbose_name="Horas de Sueño")
    nivel_dolor = models.IntegerField(null=True, blank=True, verbose_name="Nivel de Dolor (0-10)")
    peso = models.DecimalField(max_digits=5, decimal_places=2, null=True, blank=True, verbose_name="Peso (kg)")
    altura = models.DecimalField(max_digits=5, decimal_places=2, null=True, blank=True, verbose_name="Altura (m)")
    imc = models.DecimalField(max_digits=4, decimal_places=2, null=True, blank=True, verbose_name="IMC")
    alerta_emitida = models.BooleanField(default=False)
    confirmado_por_usuario = models.BooleanField(default=False)
    registrado_por = models.ForeignKey(
        settings.AUTH_USER_MODEL,
        on_delete=models.SET_NULL,
        null=True,
        blank=True,
        db_column='registrado_por_usuario_id'
    )
    notas = models.TextField(blank=True, null=True)
    created_at = models.DateTimeField(default=timezone.now)

    class Meta:
        db_table = 'registro_salud_diario'
        verbose_name = 'Registro de Salud Diario'
        verbose_name_plural = 'Registros de Salud Diarios'
        ordering = ['-created_at']

    def __str__(self):
        return f"Signos {self.paciente.nombre} ({self.created_at.strftime('%Y-%m-%d %H:%M')})"

    def save(self, *args, **kwargs):
        # Cálculo automático de IMC si se suministran peso y altura
        if self.peso and self.altura and self.altura > 0:
            self.imc = round(float(self.peso) / (float(self.altura) ** 2), 2)
        super().save(*args, **kwargs)
