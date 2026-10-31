from django.core.management.base import BaseCommand
from django.utils import timezone
from apps.patients.models import Paciente
from apps.medications.views import generar_tomas_diarias_paciente

class Command(BaseCommand):
    help = 'Genera las tomas diarias de medicamentos para todos los pacientes activos (Reemplazo de bin/cron_medicamentos.php).'

    def handle(self, *args, **options):
        hoy = timezone.localdate()
        self.stdout.write(f"Iniciando generador de tomas para {hoy}...")
        pacientes = Paciente.objects.all()
        count = 0
        for p in pacientes:
            generar_tomas_diarias_paciente(p, hoy)
            count += 1
        self.stdout.write(self.style.SUCCESS(f"Tomas generadas exitosamente para {count} pacientes."))
