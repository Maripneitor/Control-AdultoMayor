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

ALLOWED_HOSTS = ['localhost', '127.0.0.1', '[::1]']

# Application definition
INSTALLED_APPS = [
    'django.contrib.admin',
    'django.contrib.auth',
    'django.contrib.contenttypes',
    'django.contrib.sessions',
    'django.contrib.messages',
    'django.contrib.staticfiles',

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
    'apps.core.middlewares.SingleSessionMiddleware',  # Middleware de Sesión Única
    'django.contrib.messages.middleware.MessageMiddleware',
    'django.middleware.clickjacking.XFrameOptionsMiddleware',
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

DEFAULT_AUTO_FIELD = 'django.db.models.BigAutoField'
