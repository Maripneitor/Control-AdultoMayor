from django.urls import path, re_path
from apps.patients import views as patient_views
from . import views

app_name = 'core'

urlpatterns = [
    path('', views.home_view, name='home'),
    path('dashboard/', views.dashboard_view, name='dashboard'),
    path('sos/activar/', views.sos_activar_view, name='sos_activar'),
    path('sos/', patient_views.FichaEmergenciaPublicaView.as_view(), name='sos_buscador'),
    re_path(r'^sos/(?P<token>[A-Za-z0-9\-_]+)/?$', patient_views.FichaEmergenciaPublicaView.as_view(), name='sos_publico_directo'),
]
