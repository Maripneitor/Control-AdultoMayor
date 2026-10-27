import uuid
from django.db import models
from django.conf import settings
from django.utils import timezone
from apps.patients.models import Paciente

class Doctor(models.Model):
    id = models.AutoField(primary_key=True)
    usuario = models.OneToOneField(
        settings.AUTH_USER_MODEL,
        on_delete=models.SET_NULL,
        null=True,
        blank=True,
        related_name='perfil_doctor',
        db_column='usuario_id'
    )
    paciente_vinculado_inicial = models.ForeignKey(
        Paciente,
        on_delete=models.CASCADE,
        null=True,
        blank=True,
        db_column='paciente_id'
    )
    nombre = models.CharField(max_length=150, verbose_name="Nombre Completo")
    cedula_profesional = models.CharField(max_length=50, blank=True, null=True, verbose_name="Cédula Profesional")
    especialidad = models.CharField(max_length=100, blank=True, null=True, verbose_name="Especialidad")
    telefono_contacto = models.CharField(max_length=25, blank=True, null=True)
    telefono_consultorio = models.CharField(max_length=25, blank=True, null=True)
    direccion_consultorio = models.TextField(blank=True, null=True)
    hospital_clinica = models.CharField(max_length=150, blank=True, null=True)
    horario_atencion = models.CharField(max_length=100, blank=True, null=True)
    created_at = models.DateTimeField(default=timezone.now)

    pacientes_asignados = models.ManyToManyField(
        Paciente,
        through='PacienteDoctor',
        related_name='doctores_tratantes'
    )

    class Meta:
        db_table = 'doctores'
        verbose_name = 'Doctor'
        verbose_name_plural = 'Doctores'

    def __str__(self):
        esp = f" - {self.especialidad}" if self.especialidad else ""
        return f"Dr(a). {self.nombre}{esp}"

class PacienteDoctor(models.Model):
    id = models.AutoField(primary_key=True)
    paciente = models.ForeignKey(Paciente, on_delete=models.CASCADE, db_column='paciente_id')
    doctor = models.ForeignKey(Doctor, on_delete=models.CASCADE, db_column='doctor_id')
    fecha_vinculacion = models.DateTimeField(default=timezone.now)
    estado = models.CharField(max_length=20, default='activo')
    notas_medicas = models.TextField(blank=True, null=True)

    class Meta:
        db_table = 'paciente_doctor'
        unique_together = ('paciente', 'doctor')
        verbose_name = 'Vínculo Paciente - Doctor'
        verbose_name_plural = 'Vínculos Pacientes - Doctores'

class RecetaMedica(models.Model):
    id = models.AutoField(primary_key=True)
    doctor = models.ForeignKey(Doctor, on_delete=models.SET_NULL, null=True, blank=True, db_column='doctor_id')
    paciente = models.ForeignKey(Paciente, on_delete=models.CASCADE, related_name='recetas', db_column='paciente_id')
    diagnostico = models.TextField(blank=True, null=True)
    indicaciones_generales = models.TextField(blank=True, null=True)
    fecha_emision = models.DateField(default=timezone.now)
    archivo_receta_url = models.CharField(max_length=255, blank=True, null=True)
    created_at = models.DateTimeField(default=timezone.now)

    class Meta:
        db_table = 'recetas_medicas'
        verbose_name = 'Receta Médica'
        verbose_name_plural = 'Recetas Médicas'
        ordering = ['-fecha_emision']

    def __str__(self):
        return f"Receta #{self.id} - {self.paciente.nombre} ({self.fecha_emision})"

class TokenAccesoMedico(models.Model):
    id = models.UUIDField(primary_key=True, default=uuid.uuid4, editable=False)
    paciente = models.ForeignKey(Paciente, on_delete=models.CASCADE, db_column='paciente_id')
    generado_por = models.ForeignKey(settings.AUTH_USER_MODEL, on_delete=models.CASCADE, db_column='generado_por_usuario_id')
    pin_seguridad = models.CharField(max_length=8)
    token_url = models.CharField(max_length=64, unique=True)
    medico_nombre = models.CharField(max_length=150, blank=True, null=True)
    hospital_institucion = models.CharField(max_length=150, blank=True, null=True)
    duracion_horas = models.IntegerField(default=24)
    expira_at = models.DateTimeField()
    max_usos = models.IntegerField(default=20)
    usos_actuales = models.IntegerField(default=0)
    activo = models.BooleanField(default=True)
    ultimo_acceso_at = models.DateTimeField(null=True, blank=True)
    ip_ultimo_acceso = models.CharField(max_length=45, blank=True, null=True)
    created_at = models.DateTimeField(default=timezone.now)

    class Meta:
        db_table = 'tokens_acceso_medico'
        verbose_name = 'Token de Acceso Médico'
        verbose_name_plural = 'Tokens de Acceso Médico'
