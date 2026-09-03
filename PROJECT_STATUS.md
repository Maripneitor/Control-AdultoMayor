# Estado y Fases del Proyecto: VitalSenior

Este documento es el registro maestro del estado actual del proyecto **VitalSenior (Control Adulto Mayor)**. Aquí desglosamos con precisión técnica qué está construido y probado, y qué áreas están pendientes de refinamiento (Fase 4) para alcanzar el nivel de producción.

## 🏗️ 1. Arquitectura Base y Core (100% Implementado)
La plataforma no usa frameworks, está construida en **PHP Puro (MVC) + PostgreSQL**:
*   **Enrutamiento Inteligente (`src/Core/Router.php`)**: Administra peticiones GET/POST. Soporta Middlewares interceptores.
*   **Autoloader PSR-4 (`public/index.php`)**: Todas las clases del namespace `App\` se autocargan desde `/src`, evitando `require` manuales constantes.
*   **Conexión Database (`src/Config/Database.php`)**: Conexión Singleton por PDO.
*   **Clase Abstracta Model (`src/Core/Model.php`)**: Contiene utilidades genéricas (`all()`, `find()`, `where()`) para agilizar las consultas por PDO e impedir Inyección SQL.
*   **Controlador Base (`src/Core/Controller.php`)**: Posee el motor ligero de renderizado (`$this->render()`) que inyecta variables hacia las plantillas y empaqueta las utilidades.

## 🔒 2. Seguridad y Sesiones (100% Implementado)
La app implementa estrictos controles Anti-OWASP Top 10:
*   **AuthMiddleware**: Las rutas `/dashboard`, `/citas` y `/medicamentos` redirigen a invitados hacia `/login`.
*   **CSRF Protection**: Función global `csrf_token()` e `<input type="hidden">` implementada y validada obligatoriamente en todo formulario POST.
*   **Mitigación XSS / Hijacking**: Función de escape `e()` (htmlspecialchars) para variables. Cookies de sesión forzadas a `HttpOnly` y `SameSite=Strict`.
*   **Validación IDOR**: Los CRUDs (como `marcarTomaComoRealizada` de Medicamentos) validan que el elemento modificado pertenezca legítimamente al `$paciente['id']` extraído desde la sesión, ignorando el input del cliente.

## 👥 3. Módulos de Usuario y Autenticación (100% Implementado)
*   **`AuthController`**: Permite Registro (Hashea con Bcrypt, crea el `Usuario` e inmediatamente el `Paciente` de forma transaccional) y Login. 
*   **`RecoveryController`**: Genera tokens temporales (`bin2hex`) de 15 minutos que se insertan en `tokens_recuperacion` para forzar restablecimiento de clave.

## 🏥 4. Módulos Clínicos (Vistas Integradas y Funcionales)
Toda la **Fase 3 está completada**. Las interfaces HTML (Tailwind CSS) reaccionan a los datos:
*   **Dashboard (`/dashboard`)**: Lee de 5 modelos en tiempo real. Cruza los datos de `registro_tomas` con `medicamentos` y despliega la agenda del día. Muestra datos vitales y botones móviles para familiares (`ContactoEmergencia`).
*   **Módulo Medicamentos (`/medicamentos`)**: Listado general y Formulario de altas. La lógica `marcarTomaComoRealizada` en el dashboard está programada.
*   **Módulo Citas (`/citas`)**: Formulario para agendar visitas categorizando por especialidad (`cita_medica`) o `laboratorio`. 
*   **Ficha de Emergencia (`/emergencia?token=xxx`)**: Vista móvil, pública, libre de navegación, que se alimenta con un identificador (UUID) único del paciente, mostrando alergias, sangre y botones directos de llamada a sus tutores.

---

## 🔎 Áreas de Mejora para la Fase 4 (Refinamiento y Producción)
Con las Fases 1, 2 y 3 finalizadas, el aplicativo funciona lógicamente. Sin embargo, para escalar a un despliegue serio (Producción), se deben aplicar estas mejoras:

- [ ] **Dockerización**: Configurar `Dockerfile` y `docker-compose.yml` para orquestar los contenedores (PHP-FPM, Nginx/Apache, y PostgreSQL). Indispensable para no depender de XAMPP/WAMP local.
- [ ] **Validación de Formularios Avanzada**: Reforzar el backend para manejar errores de input (ej. fechas pasadas en citas, campos vacíos) devolviendo los errores amigablemente a la UI.
- [ ] **UX / Interacciones JS**: Agregar *SweetAlert* o *Toasters* (Notificaciones) usando JavaScript para confirmar acciones (ej. "¡Cita guardada!") sin que el usuario sufra recargas bruscas o se desoriente.
- [ ] **Paginación**: Si el paciente lleva un año usando la app, cargar todas sus citas pasadas volverá lenta la consulta. Hay que paginar el listado de Citas o limitarlo.
- [ ] **Scripts Seeders (Migraciones)**: Automatizar la creación de tablas y datos semilla de doctores y contactos de prueba para que cualquier programador pueda desplegar en segundos.
