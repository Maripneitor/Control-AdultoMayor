# Auditoría de Proyecto - Control Adulto Mayor

## 1. Exposición de Credenciales y Archivos Sensibles
- **Severidad:** Crítica
- **Archivo:** `.env` (Líneas 1-8)
- **Descripción:** El archivo `.env` se encuentra presente en el directorio de trabajo con credenciales reales (contraseña de base de datos `DB_PASS=110905`) expuestas.
- **Sugerencia técnica:** Eliminar el archivo `.env` del control de versiones (usar `git rm --cached .env`), mantener solo `.env.example` y rotar las contraseñas de la base de datos inmediatamente.

## 2. Exposición de Información por Modo Debug (Display Errors)
- **Severidad:** Crítica
- **Archivo:** `public/index.php` (Líneas 4-6)
- **Descripción:** Las directivas `display_errors` y `display_startup_errors` están activadas forzosamente en el código (`ini_set('display_errors', '1');`), lo cual expone rutas internas y configuraciones del servidor a los usuarios finales en caso de fallos.
- **Sugerencia técnica:** Cambiar a `ini_set('display_errors', '0');` en entornos de producción y usar `error_log()` para registrar los errores internamente.

## 3. Rate Limiting Basado en Sesiones (Bypass Fácil)
- **Severidad:** Media
- **Archivo:** `src/Controllers/AuthController.php` (Líneas 87-101) y `src/Controllers/RecoveryController.php` (Líneas 28-40)
- **Descripción:** El control de límite de intentos (rate limiting) para el login y recuperación de contraseña se basa únicamente en variables de sesión (`$_SESSION`). Un atacante puede evadir esta protección (fuerza bruta) simplemente eliminando su cookie de sesión en cada intento.
- **Sugerencia técnica:** Implementar el rate limiting basado en la dirección IP (`$_SERVER['REMOTE_ADDR']`) y/o el correo electrónico objetivo, almacenando los intentos en la base de datos o en caché (Redis/Memcached).

## 4. Exposición de Detalles Internos en Errores de Base de Datos
- **Severidad:** Media
- **Archivo:** `config/Database.php` (Líneas 31-47)
- **Descripción:** En la captura de excepciones PDO (`catch(\Throwable $e)`), se inyecta y muestra el mensaje técnico del error directamente en la vista HTML (`$e->getMessage()`). Esto revela detalles de la infraestructura, usuarios o posibles esquemas de BD.
- **Sugerencia técnica:** Registrar el detalle del error en un archivo de logs y mostrar al usuario únicamente un mensaje genérico ("Ocurrió un problema de conexión").

## 5. Problema de Rendimiento: Consultas N+1 (N+1 Query Problem)
- **Severidad:** Media
- **Archivo:** `src/Models/Medicamento.php` (Líneas 57-100)
- **Descripción:** En el método `generarTomasDiarias`,## Fase 6: Arquitectura Integral, Concurrencia y Steppers Médicos (COMPLETADA)
* **Objetivo:** Refactorizar módulos faltantes, implementar control de concurrencia (transacciones), mejorar el registro clínico para UX Senior y endurecer seguridad (SOS persistente, Cron jobs).
* **Estado:** Integración arquitectónica finalizada.
* **Componentes:**
  * **Servicios (SRP):** `SaludEvaluatorService` creado para verificar límites fisiológicos (Two-Step Verification).
  * **Concurrencia DB:** Las tomas en `MedicamentoController` ahora emplean transacciones transaccionales, evitando descuentos dobles en el inventario de medicamentos.
  * **UX Senior (Steppers):** Formulario escalonado para glucosa, presión y dolor con botones gigantes en `stepper.php`. Control de tomas dividido en Mañana, Tarde, Noche (`control_tomas.php`).
  * **Emergencias:** Botón global flotante añadido al layout (`header.php`) con modal seguro (contador de 3 segundos) y retención del token SOS.
  * **Jobs Automáticos:** Cron job (`bin/cron_inactividad.php`) habilitado para buscar tomas no realizadas y constantes omitidas durante 24h, enviando alertas por email a cuidadores.
  * **Reportes (PDF):** Se migró a la API de QuickChart para generar resúmenes con gráficos 100% compatibles con Dompdf usando únicamente CSS 2.1 estándar y tablas HTML.
* **Estado Base de Datos:** Base de datos Postgres refactorizada con éxito mediante `refactor_db_fase6.php` (ampliando `registro_salud_diario` y `medicamentos`, creando `tomas_historial` y `codigos_vinculacion`). múltiple (`INSERT INTO ... VALUES (...), (...), ...`).

## 6. Archivo Composer Vacío
- **Severidad:** Baja
- **Archivo:** `composer.json` (Línea 1)
- **Descripción:** El manifiesto `composer.json` existe en la raíz del proyecto pero está completamente vacío (0 bytes), lo que romperá cualquier intento de ejecución de herramientas de PHP o integraciones.


