from django.urls import path
from . import views

app_name = 'patients'

urlpatterns = [
    path('expediente/<int:pk>/', views.ExpedienteDetailView.as_view(), name='expediente'),
    path('seleccionar/', views.SeleccionarPacienteView.as_view(), name='seleccionar'),
    path('emergencia/', views.FichaEmergenciaPublicaView.as_view(), name='emergencia_publica'),
]
