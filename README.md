# VitalSenior (Control Adulto Mayor)

Plataforma integral de gestión de salud diseñada para el cuidado de adultos mayores. Permite a familiares, cuidadores y médicos tratantes mantener un control estricto de medicamentos, signos vitales, citas médicas, y provee una ficha de emergencia rápida accesible mediante un código QR.

---

## 🚀 Despliegue Rápido con Docker (Recomendado)

El proyecto incluye un entorno contenerizado completo con **Docker y Docker Compose**. Si descargas este repositorio, no necesitas instalar XAMPP, Apache, PHP ni PostgreSQL en tu máquina local.

### Requisitos Previos
- [Docker Desktop](https://www.docker.com/products/docker-desktop/) (o Docker Engine con plugin Compose).

### 1. Iniciar Todo en un Solo Comando
En la raíz del proyecto, ejecuta:
```bash
docker compose up -d --build
```

Esto levantará automáticamente 3 servicios interconectados y listos para usar:
1. **`db` (PostgreSQL 15):** Inicializa la base de datos `vitalsenior` e importa automáticamente todo el esquema DDL y tablas desde `docker/postgres/init.sql`.
2. **`app_php` (VitalSenior PHP 8.2 + Apache):** Reemplazo directo de XAMPP con todas las extensiones médicas necesarias (`pdo_pgsql`, `gd`, `zip`, `opcache`).
3. **`app_django` (VitalSenior Django 6.x MVT):** Servidor backend Django listo para pruebas y desarrollo.

### 2. Acceder a las Aplicaciones
- **VitalSenior PHP (App Principal):** [http://localhost:8080](http://localhost:8080)
- **VitalSenior Django (MVT Backend):** [http://localhost:8000](http://localhost:8000)
- **Base de Datos PostgreSQL:** Puerto `5432` (Usuario: `postgres`, Password: `postgres`, BD: `vitalsenior`).

### 3. Detener los Servicios
```bash
docker compose down
```

---

## 🛠 Arquitectura y Tecnologías

*   **Backend Clásico:** PHP 8.2+ Puro (Patrón MVC personalizado con PDO).
*   **Backend MVT (Migración):** Python 3.12+ / Django 6.x con Django ORM.
*   **Base de Datos:** PostgreSQL 13+ con UUIDs criptográficos (`pgcrypto`).
*   **Frontend:** HTML5 semántico, Tailwind CSS (CDN y utilidades), Alpine.js y SVG Outline.
*   **Contenerización:** Docker Compose multi-servicio (PHP Apache + Django + PostgreSQL).

---

## 📁 Estructura del Proyecto

```text
Control-AdultoMayor/
├── docker/                  # Configuración y Dockerfiles para contenedores
│   ├── php/                 # Dockerfile y vhost.conf para PHP Apache
│   ├── django/              # Dockerfile para Django MVT
│   └── postgres/            # init.sql para auto-inicializar la BD
├── docker-compose.yml       # Orquestación de servicios locales
├── config/                  # Configuración PHP (PDO Singleton, seguridad)
├── public/                  # Document Root público de Apache (index.php)
├── src/                     # Código fuente PHP MVC
│   ├── Controllers/         # Controladores (Dashboard, Medicamentos, Doctor, etc.)
│   ├── Models/              # Modelos de datos
│   ├── Middlewares/         # AuthMiddleware (Multitenant & Seguridad)
│   ├── Security/            # SingleSessionManager (Sesión única)
│   └── Views/               # Vistas PHP DTL
├── apps/                    # Apps modulares de Django MVT
│   ├── accounts/            # Usuarios y autenticación
│   ├── patients/            # Pacientes y expediente clínico
│   ├── medications/         # Medicamentos y tomas
│   ├── health/              # Signos vitales e IMC
│   ├── appointments/        # Citas y visitas médicas
│   └── doctors/             # Doctores y recetas
├── templates/               # Plantillas HTML de Django MVT
├── manage.py                # CLI de Django
└── requirements.txt         # Dependencias Python
```

---

## ⚙️ Opción Alternativa: Instalación Local Tradicional (Sin Docker)

Si prefieres ejecutar sin Docker usando tu entorno local (XAMPP / PHP CLI):

1. **Configurar Base de Datos**:
   * En PostgreSQL, crea la base de datos `vitalsenior`.
   * Restaura el archivo `docker/postgres/init.sql` o `database_schema_v2.sql`.
2. **Variables de Entorno**:
   * Copia `.env.example` a `.env` y coloca tus credenciales de PostgreSQL.
3. **Servidor PHP**:
   ```bash
   php -S localhost:8000 -t public
   ```
4. **Servidor Django**:
   ```bash
   pip install -r requirements.txt
   python manage.py runserver
   ```
