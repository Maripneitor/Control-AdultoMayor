# VitalSenior (Control Adulto Mayor) - Django MVT en Docker

Plataforma integral de gestión médica y monitoreo para el cuidado de adultos mayores desarrollada en **Python 3.12 y Django 6.x** bajo el patrón **MVT (Modelo-Vista-Template)** y orquestada con **Docker y Docker Compose**.

Permite a familiares, cuidadores y médicos tratantes mantener control de medicamentos con tolerancia horaria, signos vitales, citas médicas, portal médico con enlaces efímeros protegidos por PIN y ficha médica SOS accesible por código QR para primeros respondientes.

---

## 🚀 Despliegue Rápido con Docker (Sin Instalar Nada en Local)

No necesitas tener instalado Python, PostgreSQL, PHP ni ningún gestor de paquetes en tu computadora. Todo corre dentro de contenedores Docker preconfigurados.

### Requisito Único
- Tener instalado y abierto [Docker Desktop](https://www.docker.com/products/docker-desktop/).

### 1. Iniciar la Plataforma en un Solo Comando
En la terminal, dentro de la carpeta del proyecto:
```bash
docker compose up -d --build
```

Esto levantará automáticamente:
1. **`db` (PostgreSQL 15):** Inicializa la base de datos `vitalsenior`, importa el esquema DDL y los datos iniciales desde `docker/postgres/init.sql`.
2. **`web` (VitalSenior Django 6.x MVT en Python 3.12):** Espera automáticamente a que la base de datos esté lista, aplica migraciones (`--fake-initial`), genera el cronograma de tomas de hoy e inicia el servidor.

### 2. Acceder a las Vistas
- **Aplicación VitalSenior:** [http://localhost:8000](http://localhost:8000)
- **Inicio de Sesión:** [http://localhost:8000/cuentas/login/](http://localhost:8000/cuentas/login/)
- **Ficha de Emergencia QR Pública:** [http://localhost:8000/pacientes/emergencia/](http://localhost:8000/pacientes/emergencia/)
- **Consola Django Admin:** [http://localhost:8000/admin/](http://localhost:8000/admin/)

### 3. Cuentas de Prueba Preconfiguradas
| Rol | Correo Electrónico | Contraseña |
| :--- | :--- | :--- |
| **Familiar Administrador / Cuidador** | `cuidador.principal.test@vitalsenior.com` | `Cuidador123!` |
| **Adulto Mayor / Paciente** | `paciente.titular.test@vitalsenior.com` | `Paciente123!` |
| **Super Administrador** | `mariomoguel05@gmail.com` | `Admin12345!` |

### 4. Tareas Automatizadas en Contenedor (Cron Jobs)
Para ejecutar manualmente los chequeos de farmacovigilancia e inventario:
```bash
# Generar tomas del día para todos los pacientes
docker compose exec web python manage.py cron_medicamentos

# Marcar tomas omitidas (>60 min) y calcular alertas de stock bajo
docker compose exec web python manage.py cron_inactividad
```

### 5. Detener los Contenedores
```bash
docker compose down
```

---

## 🏗 Arquitectura del Proyecto (Django MVT)

```text
Control-AdultoMayor/
├── Dockerfile                   # Imagen Docker oficial en Python 3.12-slim
├── docker-compose.yml           # Orquestación de Django MVT + PostgreSQL
├── docker/
│   ├── entrypoint.sh            # Script de arranque: espera a DB, migra y arranca
│   └── postgres/init.sql        # Esquema DDL y catálogo de datos iniciales
├── manage.py                    # CLI principal de Django
├── requirements.txt             # Dependencias de Python (Django 6, Psycopg 3, etc.)
├── vitalsenior/                 # Configuración central del proyecto Django
│   ├── settings.py              # Ajustes, base de datos, hashers bcrypt
│   ├── urls.py                  # Enrutador principal de URLs
│   └── wsgi.py                  # Interfaz WSGI
├── apps/                        # Módulos limpios desacoplados (Clean Architecture)
│   ├── accounts/                # Autenticación, roles, rate-limit, perfiles
│   ├── core/                    # Home, router de dashboards, logs de auditoría, SOS
│   ├── patients/                # Expediente clínico, multitenant, QR público
│   ├── medications/             # Catálogo de fármacos, tomas diarias, tolerancia ±45m
│   ├── health/                  # Monitoreo de signos vitales, glucosa, presión, IMC
│   ├── appointments/            # Citas de laboratorio y bitácora médica
│   └── doctors/                 # Portal médico, tokens PIN efímeros, reportes PDF
├── templates/                   # Plantillas MVT con diseño Tailwind CSS corporativo
│   ├── base.html                # Layout base responsivo con navbar y contexto activo
│   ├── accounts/                # Vistas de login, registro, recuperar y perfil
│   ├── core/                    # Vistas de home y dashboards por rol
│   ├── patients/                # Fichas clínicas, listados y QR público
│   ├── medications/             # Catálogo y timeline de tomas
│   ├── health/                  # Listado de signos y formulario con validación
│   ├── appointments/            # Agenda de citas y notas de consulta
│   └── doctors/                 # Portal, generación de PIN y reporte imprimible
├── static/                      # Recursos estáticos (JS, imágenes clínicas)
└── legacy_php/                  # Respaldo archivado de código PHP heredado
```
