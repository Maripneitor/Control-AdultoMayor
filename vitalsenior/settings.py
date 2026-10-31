"""
Django settings for vitalsenior project.
Migración desde PHP MVC a Django MVT.
"""

from pathlib import Path
import os
import environ

# Build paths inside the project like this: BASE_DIR / 'subdir'.
BASE_DIR = Path(__file__).resolve().parent.parent

# Inicializar django-environ
env = environ.Env(
    DEBUG=(bool, False),
    APP_KEY=(str, 'django-insecure-vitalsenior-dev-key-change-in-prod-1234567890'),
    DB_HOST=(str, '127.0.0.1'),
    DB_PORT=(int, 5432),
    DB_NAME=(str, 'vitalsenior'),
    DB_USER=(str, 'postgres'),
    DB_PASS=(str, ''),
    APP_TIMEZONE=(str, 'UTC'),
)

# Leer archivo .env si existe en la raíz
env_file = BASE_DIR / '.env'
if env_file.exists():
    environ.Env.read_env(str(env_file))

# SECURITY WARNING: keep the secret key used in production secret!
SECRET_KEY = env('APP_KEY')

# SECURITY WARNING: don't run with debug turned on in production!
DEBUG = env('APP_DEBUG', default=False) if 'APP_DEBUG' in os.environ else env('DEBUG')

ALLOWED_HOSTS = ['*']

LOGIN_URL = '/cuentas/login/'
LOGIN_REDIRECT_URL = '/dashboard/'
LOGOUT_REDIRECT_URL = '/cuentas/login/'


# Application definition
INSTALLED_APPS = [
    'django.contrib.admin',
    'django.contrib.auth',
    'django.contrib.contenttypes',
    'django.contrib.sessions',
    'django.contrib.messages',
    'django.contrib.staticfiles',

    # Autenticación 2FA, OTP y Rate Limiting (Zero Trust)
    'django_otp',
    'django_otp.plugins.otp_static',
    'django_otp.plugins.otp_totp',
    'two_factor',
    'axes',

    # Apps modulares de VitalSenior (Clean Architecture)
    'apps.core',
    'apps.accounts',
    'apps.patients',
    'apps.medications',
    'apps.health',
    'apps.appointments',
    'apps.doctors',
]

MIDDLEWARE = [
    'django.middleware.security.SecurityMiddleware',
    'django.contrib.sessions.middleware.SessionMiddleware',
    'django.middleware.common.CommonMiddleware',
    'django.middleware.csrf.CsrfViewMiddleware',
    'django.contrib.auth.middleware.AuthenticationMiddleware',
    'django_otp.middleware.OTPMiddleware',  # Soporte 2FA TOTP / Static
    'apps.core.middlewares.SingleSessionMiddleware',  # Middleware de Sesión Única
    'django.contrib.messages.middleware.MessageMiddleware',
    'django.middleware.clickjacking.XFrameOptionsMiddleware',
    'axes.middleware.AxesMiddleware',  # Protección contra Fuerza Bruta (debe ir al final)
]

ROOT_URLCONF = 'vitalsenior.urls'

TEMPLATES = [
    {
        'BACKEND': 'django.template.backends.django.DjangoTemplates',
        'DIRS': [BASE_DIR / 'templates'],
        'APP_DIRS': True,
        'OPTIONS': {
            'context_processors': [
                'django.template.context_processors.request',
                'django.contrib.auth.context_processors.auth',
                'django.contrib.messages.context_processors.messages',
                'apps.core.context_processors.patient_context',  # Paciente activo en sesión
            ],
        },
    },
]

WSGI_APPLICATION = 'vitalsenior.wsgi.application'

# Database: Conexión con PostgreSQL existente
DATABASES = {
    'default': {
        'ENGINE': 'django.db.backends.postgresql',
        'NAME': env('DB_NAME'),
        'USER': env('DB_USER'),
        'PASSWORD': env('DB_PASS'),
        'HOST': env('DB_HOST'),
        'PORT': env('DB_PORT'),
        'OPTIONS': {
            'client_encoding': 'UTF8',
            'connect_timeout': 3,
        },
    }
}

# Modelo de Usuario personalizado
AUTH_USER_MODEL = 'accounts.Usuario'

# Backends de Autenticación (django-axes primero para interceptar fuerza bruta)
AUTHENTICATION_BACKENDS = [
    'axes.backends.AxesStandaloneBackend',
    'django.contrib.auth.backends.ModelBackend',
]

# Configuración Zero Trust de django-axes: 5 intentos fallidos -> bloqueo de 15 minutos
AXES_FAILURE_LIMIT = 5
AXES_COOLOFF_TIME = 0.25  # 0.25 horas = 15 minutos exactos
AXES_LOCKOUT_PARAMETERS = [['username', 'ip_address']]
AXES_RESET_ON_SUCCESS = True
AXES_ENABLE_ACCESS_FAILURE_LOG = True

# Password Hashers compatibles con Bcrypt (heredados de PHP) y Argon2/PBKDF2 de Django
PASSWORD_HASHERS = [
    'django.contrib.auth.hashers.BCryptSHA256PasswordHasher',
    'django.contrib.auth.hashers.BCryptPasswordHasher',
    'django.contrib.auth.hashers.PBKDF2PasswordHasher',
    'django.contrib.auth.hashers.PBKDF2SHA1PasswordHasher',
    'django.contrib.auth.hashers.Argon2PasswordHasher',
]

AUTH_PASSWORD_VALIDATORS = [
    {'NAME': 'django.contrib.auth.password_validation.UserAttributeSimilarityValidator'},
    {'NAME': 'django.contrib.auth.password_validation.MinimumLengthValidator'},
    {'NAME': 'django.contrib.auth.password_validation.CommonPasswordValidator'},
    {'NAME': 'django.contrib.auth.password_validation.NumericPasswordValidator'},
]

# Internationalization
LANGUAGE_CODE = 'es-mx'
TIME_ZONE = env('APP_TIMEZONE')
USE_I18N = True
USE_TZ = True

# Static files (CSS, JavaScript, Images)
STATIC_URL = '/static/'
STATICFILES_DIRS = [BASE_DIR / 'static']
STATIC_ROOT = BASE_DIR / 'staticfiles'

# Media uploads (Expedientes, fotos de perfil, recetas)
MEDIA_URL = '/media/'
MEDIA_ROOT = BASE_DIR / 'storage' / 'uploads'

# Configuración de Autenticación de Dos Factores (2FA / OTP)
OTP_TOTP_ISSUER = 'VitalSenior'
TWO_FACTOR_PATCH_ADMIN = False

# Configuración de Correo Electrónico (SMTP / Consola en Desarrollo)
EMAIL_BACKEND = env(
    'EMAIL_BACKEND',
    default='django.core.mail.backends.smtp.EmailBackend'
    if os.environ.get('EMAIL_HOST_USER')
    else 'django.core.mail.backends.console.EmailBackend'
)
EMAIL_HOST = env('EMAIL_HOST', default='smtp.gmail.com')
EMAIL_PORT = env.int('EMAIL_PORT', default=587)
EMAIL_USE_TLS = env.bool('EMAIL_USE_TLS', default=True)
EMAIL_HOST_USER = env('EMAIL_HOST_USER', default='')
EMAIL_HOST_PASSWORD = env('EMAIL_HOST_PASSWORD', default='')
DEFAULT_FROM_EMAIL = env('DEFAULT_FROM_EMAIL', default='VitalSenior Seguridad <no-reply@vitalsenior.com>')

# Tiempo de expiración del Código Mágico por Correo (5 minutos = 300 segundos)
MAGIC_CODE_EXPIRY_SECONDS = 300

# ==============================================================================
# SEGURIDAD ZERO TRUST EN COOKIES, SESIONES Y CABECERAS HTTP
# ==============================================================================
SESSION_COOKIE_HTTPONLY = True
CSRF_COOKIE_HTTPONLY = True
SESSION_COOKIE_SECURE = env.bool('SESSION_SECURE', default=not DEBUG)
CSRF_COOKIE_SECURE = env.bool('SESSION_SECURE', default=not DEBUG)
SESSION_COOKIE_SAMESITE = env('SESSION_SAMESITE', default='Lax')
CSRF_COOKIE_SAMESITE = env('SESSION_SAMESITE', default='Lax')
SESSION_COOKIE_AGE = env.int('SESSION_LIFETIME_MINUTES', default=120) * 60
SESSION_EXPIRE_AT_BROWSER_CLOSE = True

SECURE_BROWSER_XSS_FILTER = True
SECURE_CONTENT_TYPE_NOSNIFF = True
X_FRAME_OPTIONS = 'DENY'

DEFAULT_AUTO_FIELD = 'django.db.models.BigAutoField'
