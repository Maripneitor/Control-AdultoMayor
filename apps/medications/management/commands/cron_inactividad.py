from datetime import datetime, timedelta
from django.core.management.base import BaseCommand
from django.utils import timezone
from apps.medications.models import Medicamento, TomasHistorial
from apps.core.models import NotificacionAlerta

class Command(BaseCommand):
    help = 'Marca tomas olvidadas como omitidas y evalúa inventarios críticos (Reemplazo de bin/cron_inactividad.php).'

    def handle(self, *args, **options):
        now = timezone.localtime()
        hoy = now.date()
        limite_hora = (now - timedelta(minutes=60)).time()

        self.stdout.write("Evaluando tomas vencidas por más de 60 minutos...")
        tomas_olvidadas = TomasHistorial.objects.filter(
            fecha_programada__lte=hoy,
            estado=TomasHistorial.EstadoToma.PENDIENTE,
            hora_programada__lt=limite_hora
        )
        omitidas_count = 0
        for toma in tomas_olvidadas:
            toma.estado = TomasHistorial.EstadoToma.OMITIDO
            toma.nota_incidencia = "Marcada automáticamente como omitida tras 60 minutos de inactividad."
            toma.save()

            NotificacionAlerta.objects.create(
                paciente=toma.paciente,
                tipo='toma_omitida',
                mensaje=f"Toma omitida: {toma.medicamento.nombre_comercial} de las {toma.hora_programada.strftime('%H:%M')}.",
                estado_envio='pendiente'
            )
            omitidas_count += 1

        self.stdout.write(self.style.WARNING(f"Tomas omitidas actualizadas: {omitidas_count}"))

        # Evaluación de stock bajo (< 4 días)
        self.stdout.write("Evaluando alertas predictivas de stock (< 4 días restantes)...")
        meds = Medicamento.objects.filter(activo=True)
        alertas_stock = 0
        for med in meds:
            dosis_diarias = 24 / max(med.frecuencia_horas, 1) * med.dosis_por_toma
            dias_restantes = med.stock_actual / max(dosis_diarias, 1)

            if dias_restantes <= 4 or med.stock_actual <= med.stock_minimo_alerta:
                NotificacionAlerta.objects.create(
                    paciente=med.paciente,
                    tipo='stock_bajo',
                    mensaje=f"Alerta predictiva de stock: {med.nombre_comercial} tiene {med.stock_actual} unidades restantes (~{round(dias_restantes, 1)} días).",
                    estado_envio='pendiente'
                )
                alertas_stock += 1

        self.stdout.write(self.style.SUCCESS(f"Evaluación completada. Alertas de inventario generadas: {alertas_stock}"))
