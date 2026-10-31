from django.urls import path
from . import views

app_name = 'appointments'

urlpatterns = [
    path('', views.cita_lista, name='lista'),
    path('nueva/', views.cita_crear, name='crear'),
    path('<int:pk>/editar/', views.cita_editar, name='editar'),
    path('visitas/nueva/', views.visita_crear, name='visita_crear'),
]
