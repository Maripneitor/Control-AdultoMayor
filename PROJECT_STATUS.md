# Auditoría y Remediación de Proyecto - Control Adulto Mayor

## Fase 7: Remediación de Seguridad, Optimización y Pulido de UI (COMPLETADA)
* **Objetivo:** Cerrar vulnerabilidades críticas de infraestructura, optimizar rendimiento en DB, refactorizar lógica de concurrencia y pulir la experiencia del usuario (UX) para perfiles Senior y Cuidadores.
* **Estado:** Integración y remediación finalizadas.
* **Vulnerabilidades Resueltas:**
  * **Exposición de Credenciales (Crítica):** `.env` eliminado de Git. Se estableció `.env.example` con variables seguras (DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASS, APP_URL).
  * **Fuga de Información (Crítica):** Desactivado `display_errors`. Se implementó un manejador global que muestra una vista HTML de Error 500 y oculta detalles técnicos en `config/Database.php`.
  * **Fuerza Bruta (Media):** Reemplazado rate limiting de sesiones por almacenamiento en DB.
    * *Nuevo esquema:* Tabla `rate_limits (ip_address, action, attempts, last_attempt)`.
    * Limpieza automática (`purgeOldRecords`) de intentos mayores a 24 horas.
  * **Rendimiento N+1 (Media):** Refactorizado `Medicamento::generarTomasDiarias` usando Bulk Insert.

* **Integraciones y Servicios:**
  * Creado `EmailNotificationService` tolerante a fallos para enviar alertas (mock/PHPMailer).
  * Cron de inactividad actualizado para delegar envíos de correo sin trabar la base de datos.
  * `MedicamentoController::posponerToma` actualizado para cambiar formalmente el estado a `pospuesto` y posponer 30 minutos la `hora_programada`.

* **Mapa de Endpoints Actualizado:**
  * `GET /login`, `POST /login`: Controlado por RateLimiter (5 intentos / 15 min).
  * `GET /recovery`, `POST /recovery`: Controlado por RateLimiter (3 intentos / 15 min).
  * `POST /medicamentos/toma`: Marca tomas con validación de antelación/retraso (± 45 min) y actualiza stock transaccionalmente.
  * `POST /medicamentos/posponer`: Pospones tomas por 30 minutos sin descontar inventario.
  * `GET /dashboard`: Enruta automáticamente a vistas específicas de admin, paciente activo (adulto_mayor) o cuidador con selector de pacientes y alertas críticas de stock/omisión.


## 7. Integración de Sistema de Roles, Permisos y Recuperación de Contraseña (COMPLETADA)
* **Objetivo:** Refactorizar la arquitectura para incluir control de acceso basado en roles, inyección de permisos en sesión y un flujo unificado de recuperación de contraseña.
* **Estado:** Integración arquitectónica finalizada.
* **Componentes:**
  * **Modelos y Dominio:** `Usuario.php` ahora define constantes `ROLES` y `PERMISOS` y registra en un archivo de log básico cada operación crítica. `PasswordReset.php` traducido a español (`crearToken`, `validarToken`, `marcarComoUsado`).
  * **Seguridad y Middlewares:** `AuthMiddleware::requirePermission()` implementado para validar sesión y nivel de permisos. Se refactorizó `autorizarAccesoPaciente` basándose en el rol activo del usuario (diferenciando admin, adultos mayores y familiares).
  * **Controladores y UI:**
    * El registro (`AuthController.php`) admite estrictamente roles en lista blanca (`adulto_mayor` o `familiar`). Los intentos ajenos retornan 400.
    * Los Adultos Mayores obtienen de forma transparente su registro en `pacientes` junto con un `token_sos`.
    * Enrutamiento dinámico en `DashboardController` (`admin/dashboard.php`, `dashboard/index.php` para AM, `dashboard/cuidador.php` para familiares).
    * `header.php` renderiza condicionalmente el menú para evitar ruido visual a adultos mayores.
    * Los controladores clínicos bloquean endpoints críticos (`store`, `registrarToma`) validando que el rol cuente con el permiso adecuado.

## 8. Especialización de Roles en Medicamentos y Alertas Predictivas (COMPLETADA)
* **Objetivo:** Separar las acciones de medicamentos según el rol (Adulto Mayor = registrar; Familiar = crear/surtir), implementar notificaciones de adherencia (±45 min, posponer) y stock.
* **Estado:** Integrado y probado.
* **Componentes:**
  * **Motor de Tolerancia:** `MedicamentoController@marcarToma` verifica el registro en relación a `hora_programada` de `tomas_historial` y añade una nota de retraso o antelación si excede los 45 min.
  * **Snooze:** Nuevo método `posponerToma` y acción en el frontend (Adulto Mayor) que cambia el estado a `pospuesto` sin deducir dosis.
  * **Alerta Predictiva & Cron:** El script CLI `bin/cron_inactividad.php` calcula `stock_actual / dosis_diaria` y emite logs ("alertas") si quedan menos de 4 días de dosis. Además, el script marca como `omitido` las tomas olvidadas por más de 60 minutos.
  * **Modificaciones DB Directas:** Se amplió el tipo `enum_estado_toma` y se añadieron `hora_programada` y `nota_incidencia` a `tomas_historial`.
