# VitalSenior (Control Adulto Mayor)

Plataforma integral de gestión de salud diseñada para el cuidado de adultos mayores. Permite a familiares y cuidadores mantener un control estricto de medicamentos, citas médicas, y provee una ficha de emergencia rápida accesible mediante un código QR.

## 🚀 Características Principales

*   **Gestión de Medicamentos**: Control estricto con recordatorios inteligentes y registro de dosis tomadas.
*   **Expediente Unificado**: Historial médico, alergias, tipo de sangre y condiciones crónicas en un solo lugar.
*   **Ficha de Emergencia (QR)**: Acceso inmediato para primeros respondientes con contactos vitales.
*   **Directorio Médico**: Información de contacto y horarios de doctores y especialistas.
*   **Agenda de Citas**: Registro de citas médicas, laboratorios y estudios clínicos.

## 🛠 Arquitectura y Tecnologías

*   **Backend**: PHP Puro (Arquitectura MVC personalizada)
*   **Base de Datos**: PostgreSQL
*   **Frontend**: HTML, CSS, Tailwind CSS (via CDN)
*   **Diseño**: Componentes generados e importados mediante Stitch AI.

## 📁 Estructura del Proyecto

```text
Control-AdultoMayor/
├── config/              # Configuración (Conexión BD, variables de entorno)
├── src/
│   ├── Core/            # Núcleo MVC (Router, Controller base, Model base)
│   ├── Controllers/     # Controladores (Auth, Dashboard, Home, etc.)
│   ├── Models/          # Modelos de base de datos (Paciente, Doctor, etc.)
│   └── Views/           # Vistas renderizadas y Layouts
├── public/              # Document Root público
│   ├── assets/          # CSS, JS, Imágenes
│   └── index.php        # Front Controller (Punto de entrada)
└── ...
```

## ⚙️ Instalación y Configuración Local

1.  **Clonar el repositorio** e ingresar a la carpeta del proyecto.
2.  **Configurar Base de Datos**:
    *   Crea una base de datos en PostgreSQL (ej. `postgres` o `saludsenior`).
    *   Asegúrate de ejecutar tus scripts de creación de tablas y datos de prueba.
    *   Configura las credenciales en `config/config.php` editando las constantes `DB_NAME`, `DB_USER` y `DB_PASS`.
3.  **Iniciar Servidor Local**:
    Puedes utilizar el servidor integrado de PHP desde la raíz del proyecto apuntando a la carpeta `public`:
    ```bash
    php -S localhost:8000 -t public
    ```
4.  Abre el navegador en `http://localhost:8000`.
