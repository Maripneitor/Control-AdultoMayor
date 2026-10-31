from django.urls import path
from . import views

app_name = 'doctors'

urlpatterns = [
    path('portal/', views.doctor_portal, name='portal'),
    path('token/generar/', views.generar_token, name='generar_token'),
    path('acceso/<str:token_url>/', views.acceso_temporal_medico, name='acceso_temporal'),
    # URL con UUID no secuencial (Anti-IDOR)
    path('pacientes/<uuid:uuid>/reporte/', views.reporte_clinico_imprimible, name='reporte_imprimible'),
]
