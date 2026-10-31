from django.urls import path
from . import views

app_name = 'patients'

urlpatterns = [
    path('', views.PacienteListView.as_view(), name='lista'),
    path('crear/', views.PacienteCreateView.as_view(), name='crear'),
    path('vincular/', views.VinculacionView.as_view(), name='vincular'),
    path('emergencia/', views.FichaEmergenciaPublicaView.as_view(), name='emergencia_publica'),
    path('seleccionar/', views.SeleccionarPacienteView.as_view(), name='seleccionar_paciente'),
    # URLs con UUID v4 no secuenciales (Prevención Zero Trust contra Ataques IDOR)
    path('<uuid:uuid>/', views.ExpedienteDetailView.as_view(), name='expediente'),
    path('<uuid:uuid>/editar/', views.PacienteUpdateView.as_view(), name='editar'),
    path('<uuid:uuid>/qr/imagen/', views.servir_qr_sos_view, name='servir_qr_sos'),
    path('<uuid:uuid>/qr/descargar/', views.descargar_qr_sos_view, name='descargar_qr_sos'),
    path('<uuid:uuid>/tarjeta-sos/', views.imprimir_tarjeta_sos_view, name='imprimir_tarjeta_sos'),
    path('<uuid:uuid>/contactos/nuevo/', views.contacto_emergencia_crear, name='contacto_crear'),
    path('contactos/<int:pk>/eliminar/', views.contacto_emergencia_eliminar, name='contacto_eliminar'),
]
