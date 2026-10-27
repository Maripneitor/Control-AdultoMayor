from django.db import models
from django.contrib.auth.models import AbstractBaseUser, PermissionsMixin, BaseUserManager
from django.utils import timezone

class UsuarioManager(BaseUserManager):
    def create_user(self, email, nombre, password=None, **extra_fields):
        if not email:
            raise ValueError('El email es obligatorio para el registro.')
        email = self.normalize_email(email)
        user = self.model(email=email, nombre=nombre, **extra_fields)
        if password:
            user.set_password(password)
        user.save(using=self._db)
        return user

    def create_superuser(self, email, nombre, password=None, **extra_fields):
        extra_fields.setdefault('rol', Usuario.Rol.ADMIN)
        extra_fields.setdefault('is_superuser', True)
        extra_fields.setdefault('is_staff', True)
        return self.create_user(email, nombre, password, **extra_fields)

class Usuario(AbstractBaseUser, PermissionsMixin):
    class Rol(models.TextChoices):
        ADMIN = 'administrador', 'Administrador'
        FAMILIAR_ADMIN = 'familiar_admin', 'Familiar Administrador'
        CUIDADOR_AUXILIAR = 'cuidador_auxiliar', 'Cuidador Auxiliar'
        PACIENTE = 'paciente', 'Adulto Mayor / Paciente'
        DOCTOR = 'doctor', 'Médico / Especialista'
        CUIDADOR = 'cuidador', 'Cuidador'
        FAMILIAR = 'familiar', 'Familiar Secundario'
        EMERGENCIA = 'emergencia', 'Acceso de Emergencia'

    id = models.AutoField(primary_key=True)
    nombre = models.CharField(max_length=255, verbose_name="Nombre Completo")
    email = models.EmailField(max_length=255, unique=True, verbose_name="Correo Electrónico")
    telefono = models.CharField(max_length=20, blank=True, null=True, verbose_name="Teléfono")
    
    # Mapeo a columna existente password_hash de PostgreSQL
    password = models.CharField(max_length=255, db_column='password_hash', verbose_name="Contraseña")
    
    rol = models.CharField(
        max_length=50,
        choices=Rol.choices,
        default=Rol.FAMILIAR_ADMIN,
        verbose_name="Rol en la Plataforma"
    )
    activo = models.BooleanField(default=True, db_column='activo', verbose_name="Cuenta Activa")
    
    is_staff = models.BooleanField(default=False)
    created_at = models.DateTimeField(default=timezone.now, db_column='created_at')
    updated_at = models.DateTimeField(auto_now=True, db_column='updated_at')

    objects = UsuarioManager()

    USERNAME_FIELD = 'email'
    REQUIRED_FIELDS = ['nombre']

    class Meta:
        db_table = 'usuarios'
        verbose_name = 'Usuario'
        verbose_name_plural = 'Usuarios'

    @property
    def is_active(self):
        return self.activo

    @is_active.setter
    def is_active(self, value):
        self.activo = bool(value)

    def __str__(self):
        return f"{self.nombre} ({self.email}) [{self.get_rol_display()}]"
