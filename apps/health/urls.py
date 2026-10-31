from django.urls import path
from . import views

app_name = 'health'

urlpatterns = [
    path('', views.salud_lista, name='lista'),
    path('nuevo/', views.salud_nuevo, name='nuevo_registro'),
]
