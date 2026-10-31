"""
URL configuration for vitalsenior project.
Ecosistema Clínico Integral Django MVT.
"""
from django.contrib import admin
from django.urls import path, include
from django.conf import settings
from django.conf.urls.static import static
from two_factor.urls import urlpatterns as tf_urls

urlpatterns = [
    path('admin/', admin.site.urls),
    path('', include('apps.core.urls')),
    path('cuentas/', include('apps.accounts.urls')),
    path('2fa/', include(tf_urls)),
    path('pacientes/', include('apps.patients.urls')),
    path('medicamentos/', include('apps.medications.urls')),
    path('salud/', include('apps.health.urls')),
    path('citas/', include('apps.appointments.urls')),
    path('doctores/', include('apps.doctors.urls')),
]

if settings.DEBUG:
    urlpatterns += static(settings.MEDIA_URL, document_root=settings.MEDIA_ROOT)
    urlpatterns += static(settings.STATIC_URL, document_root=settings.STATIC_ROOT)
