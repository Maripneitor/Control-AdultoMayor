from django.urls import path
from . import views

app_name = 'medications'

urlpatterns = [
    path('', views.medicamento_lista, name='lista'),
    path('nuevo/', views.medicamento_crear, name='crear'),
    path('<int:pk>/editar/', views.medicamento_editar, name='editar'),
    path('tomas/', views.tomas_diarias, name='tomas_diarias'),
    path('tomas/<int:pk>/marcar/', views.marcar_toma, name='marcar_toma'),
    path('tomas/<int:pk>/posponer/', views.posponer_toma, name='posponer_toma'),
    path('tomas/<int:pk>/deshacer/', views.deshacer_toma, name='deshacer_toma'),
]
