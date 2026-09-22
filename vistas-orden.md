# Mapa de Vistas del Proyecto - VitalSenior

**Stack Tecnológico Oficial:**
* **Arquitectura Backend:** PHP 8+ Puro bajo Patrón MVC (Modelo-Vista-Controlador).
* **Base de Datos & ORM:** PostgreSQL mediante PHP PDO (Sentencias preparadas estrictas, transacciones ACID, tiempos UTC, UUIDs v4 y borrado lógico / *Soft Delete*).
* **Procesamiento de Archivos e Imágenes:** Extensión nativa PHP GD (`imagecreatefromjpeg`, `imagecopyresampled`, `imagewebp`) y validación binaria estricta con `finfo`.
* **Notificaciones & Telecomunicaciones:** Twilio REST API vía cURL nativo (WhatsApp y SMS) con validación de firmas criptográficas `X-Twilio-Signature`.
* **Infraestructura de Automatización:** Tareas programadas en servidor Linux mediante PHP CLI (`php tasks/recordatorios.php`) ejecutadas periódicamente vía Cron (`* * * * *`).
* **Frontend & Estilos:** Tailwind CSS, diseño responsivo móvil-first con menú hamburguesa, cero scroll horizontal (`overflow-x-hidden`) y componentes de feedback accesible (Toasts/Alertas).

---

## 📋 1. Landing Page (Home)
* **Roles con acceso:** Todos (Público no autenticado).
* **Descripción rápida:** Página de inicio institucional y presentación de beneficios del sistema VitalSenior. Sin textos de relleno ("Lorem Ipsum").
* **Estándares UI/UX:** Logo del header con redirección asegurada al inicio (`/`), enlaces telefónicos con protocolo nativo `href="tel:..."` y correos con `href="mailto:..."`.
* **Elementos de Interfaz:**
  - Encabezado con logo, menú responsive y llamada directa a emergencias (`tel:911`).
  - Sección interactiva de beneficios y cómo funciona la plataforma.
* **Botones, Acciones y Redirecciones:**
  - `[Comenzar ahora sin costo]` ➔ Redirige a: **2. Login / Registro**.
  - `[Ver cómo funciona]` ➔ Desplazamiento suave a sección ancla `#como-funciona`.
  - `[Ficha de Emergencia]` ➔ Redirige a: **6. Ficha de Emergencia (Demo / Escaneo QR)**.
  - `[Iniciar Sesión]` ➔ Redirige a: **2. Login / Registro**.
* **Rutas de acceso entrante:** Entrada directa (`/`).

---

## 📋 2. Login / Registro
* **Roles con acceso:** Todos (Público no autenticado).
* **Descripción rápida:** Pantalla unificada con pestañas dinámicas para autenticación o registro diferenciado (Adulto Mayor o Familiar).
* **Elementos de Interfaz:**
  - **Pestaña Iniciar Sesión:**
    - `[Input Text: Correo Electrónico o Teléfono]`
    - `[Input Password: Contraseña]` con botón para alternar visibilidad.
    - `[Input Hidden: CSRF Token]`
  - **Pestaña Regístrate:**
    - `[Input Text: Nombre Completo]`
    - `[Select: Tipo de Cuenta (Adulto Mayor / Familiar)]`
    - `[Input Tel: Teléfono Móvil]`
    - `[Input Email: Correo Electrónico]`
    - `[Input Password: Contraseña Segura]`
    - `[Checkbox Requerido: Acepto los Términos de Servicio y la Política de Privacidad de Datos Clínicos]` (Obligatorio para habilitar el registro).
    - `[Checkbox Opcional: Deseo recibir alertas y recordatorios vía WhatsApp]` (Detona verificación OTP previa a su activación).
* **Botones, Acciones y Redirecciones:**
  - `[Volver al inicio]` ➔ Redirige a: **1. Landing Page**.
  - `[¿Olvidaste tu contraseña?]` ➔ Redirige a: **3. Recuperar Contraseña**.
  - `[Iniciar sesión]` ➔ Envío POST a `/login` ➔ Redirige a: **4. Dashboard Principal**.
  - `[Crear cuenta]` ➔ Valida checkboxes legales y envía POST a `/register` ➔ Si activa WhatsApp, redirige a validación OTP; caso contrario a **4. Dashboard Principal**.

---

## 📋 3. Recuperar Contraseña (Flujo OTP WhatsApp / SMS)
* **Roles con acceso:** Todos (Público no autenticado).
* **Descripción rápida:** Recuperación de credenciales mediante código de un solo uso (OTP) de 6 dígitos enviado por WhatsApp (o SMS como respaldo) mediante la integración de Twilio en PHP.
* **Elementos de Interfaz (3 Pasos):**
  - **Paso 1 (Solicitud):** `[Input Tel: Número de Teléfono registrado]` + `[Input Hidden: CSRF Token]`.
  - **Paso 2 (Verificación OTP):** `[Input Text: Código OTP de 6 dígitos]` + contador regresivo de expiración (15 minutos en UTC).
  - **Paso 3 (Restablecimiento):** `[Input Password: Nueva Contraseña]` y `[Input Password: Confirmar Contraseña]`.
* **Botones, Acciones y Redirecciones:**
  - `[Enviar Código vía WhatsApp]` ➔ POST a `/api/otp/send` ejecutado mediante cURL contra Twilio ➔ Muestra paso 2.
  - `[Verificar Código]` ➔ POST a `/api/otp/verify` validado en PostgreSQL ➔ Habilita paso 3.
  - `[Guardar Nueva Contraseña]` ➔ Actualiza hash con `password_hash` en PostgreSQL ➔ Redirige a: **2. Login** con Toast de confirmación.
  - `[Volver al Login]` ➔ Cancela el proceso ➔ Redirige a: **2. Login**.

---

## 📋 4. Dashboard Principal (Diferenciado por Rol)
* **Roles con acceso:** Usuario autenticado (`familiar`, `adulto_mayor`, `admin`).
* **Descripción rápida:** Centro de mando clínico adaptado al rol del usuario. Garantiza cero scroll horizontal (`overflow-x-hidden`) y componentes visuales Toast para retroalimentación inmediata.
* **Comportamiento por Rol & Empty State Obligatorio:**
  - **Rol FAMILIAR:**
    - **Empty State con Redirección Obligatoria:** Si el familiar no tiene pacientes asignados en la base de datos, el backend intercepta la solicitud y redirige forzosamente a la **Vista 5 (Vinculación)** con mensaje explicativo.
    - Selector de paciente activo (permite alternar entre padres/abuelos si cuida a más de uno).
    - Monitoreo del semáforo de adherencia de medicamentos y citas próximas.
  - **Rol ADULTO MAYOR:**
    - Tipografía grande y contraste accesible (estándar WCAG AA).
    - Indicador prioritario: *"Tu próxima medicina es [Nombre] a las [Hora Local]"*.
    - **Botón SOS de Emergencia Asíncrono:** Dispara una petición `POST /api/sos` al servidor PHP usando `fetch()` nativo sin recargar la página. Al pulsar:
      - Se deshabilita inmediatamente (`disabled = true`).
      - Cambia dinámicamente sus clases de Tailwind a estado *"Enviando..."* con spinner animado (`animate-spin`).
      - Envía las alertas a los tutores familiares vía Twilio (WhatsApp/SMS).
      - Despliega un componente **Toast flotante** (éxito en verde esmeralda o error en rojo carmesí) con temporizador de autocierre.
    - Código de vinculación personal visible para compartir con su cuidador.
* **Botones, Acciones y Redirecciones:**
  - `[Botón SOS]` ➔ Petición POST asíncrona a `/api/sos` vía `fetch()` con estado loading y feedback Toast no bloqueante.
  - `[Ver Expediente Completo]` ➔ Redirige a: **8. Expediente Médico**.
  - `[Gestionar Medicamentos]` ➔ Redirige a: **7. Módulo de Medicamentos**.
  - `[Cerrar Sesión]` ➔ Limpia sesión segura en PHP ➔ Redirige a: **1. Landing Page**.

---

## 📋 5. Vinculación y Gestión de Pacientes
* **Roles con acceso:** Familiar.
* **Descripción rápida:** Espacio donde el cuidador asocia pacientes existentes o crea perfiles administrados.
* **Elementos de Interfaz:**
  - **Modalidad A (Vincular Cuenta Existente):** `[Input Text: Código de Vinculación]` (PIN de 6 dígitos proporcionado por el adulto mayor).
  - **Modalidad B (Crear Perfil Administrado):** Para adultos mayores que no manejan tecnología digital. Formulario con Nombre, Fecha de Nacimiento, Género y Tipo de Sangre.
  - **Lógica "Promover a Cuenta Independiente":**
    - Permite convertir un perfil administrado en una cuenta con credenciales autónomas (teléfono y contraseña).
    - **Regla Clínica y de Tutela Obligatoria:** El sistema otorga credenciales propias al Adulto Mayor, pero el familiar que originó la cuenta **SE MANTIENE AUTOMÁTICAMENTE VINCULADO** en la tabla `vinculaciones` (`tipo_gestion = 'independiente_vinculado'`) conservando permisos de tutoría, lectura y edición sobre el expediente clínico.
* **Botones, Acciones y Redirecciones:**
  - `[Vincular Paciente]` ➔ POST a `/vincular/procesar` ➔ Asocia en tabla `vinculaciones` ➔ Redirige a: **4. Dashboard**.
  - `[Guardar Perfil Administrado]` ➔ Crea registro en `pacientes` dependiente del familiar ➔ Redirige a: **4. Dashboard**.
  - `[Promover a Independiente]` ➔ POST a `/vincular/promover` ➔ Asigna credenciales y preserva el vínculo del familiar ➔ Redirige a: **4. Dashboard**.

---

## 📋 6. Ficha de Emergencia (Vista Pública para Paramédicos)
* **Roles con acceso:** Público general mediante escaneo de Código QR (Sin Login).
* **Descripción rápida:** Hoja médica de auxilio diseñada para lectura ultrarrápida en ambulancias o salas de urgencias.
* **Seguridad Estricta en URLs:** Se accede exclusivamente a través de identificadores **UUID v4** (ej. `/emergencia?id=e7b1a2c3-d4e5-4f6a-8b9c-0d1e2f3a4b5c`). Se prohíbe el uso de IDs numéricos secuenciales para evitar ataques de enumeración y scraping.
* **Elementos Visuales (Solo Lectura):**
  - Nombre completo, edad calculada, tipo de sangre destacado en rojo.
  - Lista de alergias severas y padecimientos crónicos (ej. Marcapasos, Diabético insulinodependiente).
  - Medicamentos actuales de soporte vital.
  - **Contactos Médicos de Cabecera:** Muestra la lista de doctores tratantes capturados en la Vista 8 (Nombre completo, Especialidad y botón de enlace directo `<a href="tel:..." class="...">Llamar al Doctor</a>`).
  - Botón de llamada inmediata al familiar tutor: `<a href="tel:..." class="...">Llamar a Contacto de Emergencia</a>`.

---

## 📋 7. Módulo de Medicamentos y Control de Tomas
* **Roles con acceso:** Familiar (Edición total) y Adulto Mayor (Marcado de tomas y consulta).
* **Descripción rápida:** Gestión integral de tratamientos farmacológicos y bitácora de administración con soporte para anulación de errores y prevención de sobredosis.
* **Elementos de Interfaz:**
  - Listado de medicamentos activos (nombre comercial, dosis, frecuencia, horario).
  - Formulario de alta: Nombre, dosis, frecuencia en horas, fecha inicio/fin e instrucciones.
  - Registro diario de tomas con horario programado convertido a la zona horaria del cliente.
* **Lógica Clínica de Posponer Dosis ("Posponer 30 min"):**
  - **Regla Estricta de Prevención de Sobredosis:** Al presionar `[Posponer 30 min]`, el backend evalúa en UTC la hora resultante (`hora_actual + 30 min`) respecto a la **siguiente dosis programada del mismo medicamento**.
  - Si el margen resultante entre la toma pospuesta y la siguiente dosis es **menor a 2 horas (120 minutos)**, la acción se bloquea en la interfaz y el servidor rechaza la solicitud emitiendo una alerta médica preventiva: *"Riesgo de acumulación o sobredosis. No es posible posponer porque la siguiente toma está a menos de 2 horas"*.
* **Funcionalidad "Deshacer Toma" (Ventana de 15 Minutos):**
  - Si el usuario marca una toma por error con `[Marcar como Tomado]`, aparece el botón `[Deshacer toma]`.
  - El backend en PHP valida con marcas de tiempo en **UTC**: Si `ahora_utc - tomado_at <= 15 minutos`, permite revertir el estado a `deshecha` (o `pendiente`) y devuelve el stock al inventario. Si transcurren más de 15 minutos, la anulación se bloquea por seguridad clínica.
* **Botones, Acciones y Redirecciones:**
  - `[Marcar como Tomado]` ➔ POST a `/medicamentos/toma`.
  - `[Deshacer toma]` ➔ POST a `/medicamentos/deshacer` (Activo en ventana de 15 min).
  - `[Posponer 30 min]` ➔ POST a `/medicamentos/posponer` (Validado con margen $\ge 2$ horas).

---

## 📋 8. Módulo de Expediente Médico y Salud
* **Roles con acceso:** Familiar y Adulto Mayor.
* **Descripción rápida:** Registro clínico integral que unifica la captura manual de datos clínicos, la gestión documental y la generación física/digital del QR de rescate.
* **Formularios de Captura Manual Reintegrados:**
  - **Sección Datos Básicos:**
    - `[Select: Tipo de Sangre]` (`A+`, `A-`, `B+`, `B-`, `AB+`, `AB-`, `O+`, `O-`, `N/D`).
    - `[Input Number: Peso (kg)]` y `[Input Number: Altura (cm o m)]`.
  - **Sección Alergias y Padecimientos:**
    - `[Input Text: Añadir Alergia]` (ej. Penicilina, Sulfas, Látex) + Botón `[Añadir Alergia]`.
    - `[Input Text: Añadir Padecimiento Crónico o Dispositivo]` (ej. Hipertensión Arterial, Marcapasos bicameral) + Botón `[Añadir Padecimiento]`.
  - **Sección Contactos Médicos de Cabecera:**
    - Formulario para registrar: Nombre del Doctor, Especialidad y Teléfono (`href="tel:..."`). Estos contactos se reflejan automáticamente en la **Vista 6 (Ficha de Emergencia)**.
* **Módulo Complementario de Subida de Archivos (Recetas y Laboratorios):**
  - Formulario con `enctype="multipart/form-data"` hacia `POST /expediente/archivos/subir`.
  - **Validación Estricta de Memoria y Tipos MIME:**
    - Límite máximo estricto de **5 MB** evaluado antes de procesar con GD para evitar errores fatales de memoria (*Memory Exhaustion*).
    - Inspección binaria con `finfo` (`image/jpeg`, `image/png`, `image/webp`, `application/pdf`).
  - **Compresión con Extensión Nativa GD:** Las imágenes de recetas se redimensionan a máx. 1600px manteniendo el ratio y se comprimen (calidad 80% o WebP) optimizando almacenamiento.
  - **Almacenamiento Protegido:** Guardado en `storage/uploads/expedientes/` con `.htaccess` (`Deny from all`), servido solo mediante endpoint autenticado `/expediente/archivos/descargar?id=UUID`.
* **Definición Técnica del Código QR (Client-Side Rendering):**
  - `[Generar / Imprimir Código QR de Emergencia]`: Botón prominente en la interfaz que despliega un modal visual con el QR.
  - **Generación en Frontend:** Para ahorrar procesamiento, memoria y dependencias en el backend PHP, el QR se genera 100% en el cliente mediante una librería ligera en JavaScript (ej. `qrcode.min.js`), renderizando la URL pública con UUID v4 (`/emergencia?id=UUIDv4`) directamente en un elemento `<canvas>` al hacer clic.
  - Incluye botón para imprimir el carnet (`window.print()`) o descargarlo para colocarlo en un lugar visible del domicilio del adulto mayor.
* **Botones, Acciones y Redirecciones:**
  - `[Guardar Cambios Clínicos]` ➔ POST a `/expediente/actualizar`.
  - `[Subir Receta/Estudio]` ➔ POST a `/expediente/archivos/subir` (Validación < 5MB).
  - `[Imprimir Carnet QR]` ➔ Abre diálogo de impresión (`window.print()`).

---

## 📋 9. Módulo de Citas y Agenda Médica
* **Roles con acceso:** Familiar y Adulto Mayor.
* **Descripción rápida:** Calendario de consultas médicas, análisis clínicos y recordatorios hospitalarios.
* **Elementos de Interfaz:**
  - Formulario: Especialista/Doctor, Fecha y Hora, Centro Médico, Notas preparatorias (ej. "Ayuno de 8 horas").
  - Las fechas y horas se envían al servidor y se almacenan en PostgreSQL estrictamente en **UTC** (`TIMESTAMP WITH TIME ZONE`). El frontend las renderiza con el huso horario del navegador.
* **Botones, Acciones y Redirecciones:**
  - `[Agendar Cita]` ➔ POST a `/citas/crear`.
  - `[Cancelar / Completar]` ➔ Actualiza estado en la base de datos.

---

## 📋 10. Mi Perfil, Preferencias y Ajustes
* **Roles con acceso:** Familiar y Adulto Mayor.
* **Descripción rápida:** Configuración personal de la cuenta, seguridad y canales de contacto.
* **Elementos de Interfaz:**
  - Datos de contacto: Nombre, teléfono y correo electrónico.
  - **Verificación de WhatsApp:** Switch de activación que exige verificación OTP previa antes de permitir el envío de alertas automáticas.
  - Cambio de contraseña segura (exige validación de contraseña actual).
  - Botón de generación de código PIN para vinculación familiar.
  - **Funcionalidad "Eliminar Cuenta" con Soft Delete:**
    - Botón `[Eliminar Cuenta]` con modal de confirmación por seguridad.
    - El backend ejecuta un **borrado lógico (soft delete)** en PostgreSQL (`activo = FALSE`, `eliminado_en = CURRENT_TIMESTAMP`). Esto preserva la integridad referencial de los expedientes médicos históricos, medicamentos y auditorías clínicas asociadas, inhabilitando inmediatamente el acceso y destruyendo la sesión.
* **Botones, Acciones y Redirecciones:**
  - `[Actualizar Perfil]` ➔ POST a `/perfil/actualizar`.
  - `[Cambiar Contraseña]` ➔ POST a `/perfil/password`.
  - `[Eliminar Cuenta]` ➔ POST a `/perfil/eliminar` (Soft Delete).

---

## 📋 404. Vista de Error Personalizada (Página No Encontrada)
* **Roles con acceso:** Todos (Público y Autenticado).
* **Descripción rápida:** Vista amigable e intuitiva desplegada por el Router principal de PHP cuando se intenta acceder a una ruta inexistente.
* **Estándares de Accesibilidad & UI/UX:**
  - Cero scroll horizontal con clase `overflow-x-hidden` en el elemento raíz.
  - Diseño responsivo y contrastes limpios con Tailwind CSS.
  - Botón prioritario de **Retorno Seguro al Dashboard** (`/dashboard` o `/` si no hay sesión).
  - Enlaces de soporte con `href="mailto:..."` y acceso rápido a emergencias `href="tel:911"`.

---

## 📋 11. Requisitos Globales de Seguridad y Backend (PHP MVC + PostgreSQL PDO)
* **Gestión de Base de Datos con PDO:**
  - Conexión singleton en `config/Database.php` con opciones estrictas: `PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION`, `PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC`, y `PDO::ATTR_EMULATE_PREPARES => false`.
  - Consultas SQL con sentencias preparadas y parámetros vinculados (`bindValue`/`execute`), eliminando riesgos de inyección SQL.
  - Zona horaria de sesión forzada a UTC en cada conexión: `SET TIME ZONE 'UTC'`.
* **Manejo de Sesiones & CSRF:**
  - Cookies de sesión con atributos `HttpOnly`, `Secure` (en HTTPS) y `SameSite=Lax` o `Strict`.
  - Regeneración de ID de sesión (`session_regenerate_id(true)`) al autenticarse o cambiar privilegios.
  - Tokens CSRF obligatorios y validados en todas las peticiones `POST`, `PUT` y `DELETE`.
* **Validación Criptográfica de Twilio:**
  - Validación de la cabecera `X-Twilio-Signature` mediante HMAC-SHA1 en los webhooks entrantes para evitar inyecciones o suplantaciones.
* **Control de Acceso, Aislamiento Multitenant y Ofuscación de Seguridad:**
  - **Middleware Centralizado (`App\Middlewares\AuthMiddleware`):**
    - `requireRole(array $roles)`: Restringe controladores y acciones según el rol (`familiar`, `adulto_mayor`, `admin`).
    - `requirePatientAccess($pacienteId)`: Garantiza que el usuario en sesión sea el propio paciente o un familiar tutor con registro activo en `vinculaciones`.
    - **Ofuscación de Rutas con Error 404 (Anti-Enumeración):** Si un usuario intenta consultar o interactuar con un `paciente_id` ajeno o no vinculado, el middleware aborta inmediatamente con un código **HTTP 404** (en lugar de 403 Forbidden). Esto impide que atacantes o terceros descubran qué identificadores existen en la base de datos.
* **Permisos Granulares en Vinculaciones (`nivel_acceso`):**
  - La tabla `vinculaciones` implementa la columna `nivel_acceso` (`'lectura'` o `'edicion_completa'`).
  - Al crearse un "Perfil Administrado", el familiar cuenta por defecto con `'edicion_completa'`.
  - Si el adulto mayor dispone de una cuenta propia ("Cuenta Independiente"), tiene la potestad clínica de degradar el vínculo a `'lectura'`, impidiendo que el familiar altere dosis, agregue medicamentos o modifique diagnósticos sin su consentimiento.
* **Renderizado Condicional de UI & Prevención de Mass Assignment:**
  - Los controladores calculan el booleano `$puedeEditar = ($rol === 'familiar' && $nivelAcceso === 'edicion_completa') || ($rol === 'admin')` y lo transmiten a las vistas.
  - En la **Vista 7 (Medicamentos)** y **Vista 8 (Expediente)**, los botones de "Añadir Fármaco", "Pausar", "Eliminar", "Posponer Toma" y "Guardar Cambios Clínicos" **NO se renderizan en el DOM/código fuente** si `$puedeEditar` es falso. El adulto mayor únicamente visualiza la lista de prescripciones y el botón de "Marcar como Tomado", evitando intentos de manipulación o inyección de formularios.
* **Auditoría Clínica Obligatoria (`logs_auditoria`):**
  - Todo cambio sensible en el expediente registra obligatoriamente: `usuario_id` (quién ejecutó), `paciente_id` (sobre quién), `accion` (ej. `'ELIMINAR_MEDICAMENTO'`, `'TOMA_POSPUESTA'`), `detalles` clínicos e `ip_origen` con estampa temporal en `fecha_utc`.
* **Protección contra Abusos y Fuerza Bruta (`App\Security\RateLimiter`):**
  - **Ventana Deslizante en Base de Datos (`rate_limits`):** Control de peticiones por IP y acción en PostgreSQL con bloqueo automático.
  - **Límite Estricto:** Máximo **3 solicitudes por IP en una ventana de 15 minutos**.
  - **Endpoints Protegidos:**
    - Solicitud de OTP vía WhatsApp (`/api/otp/send`): Previene consumo desmedido y costos en la API de Twilio.
    - Inicio de Sesión (`/login`): Mitiga ataques de fuerza bruta y adivinación de contraseñas.
  - **Respuesta Preventiva:** Si se sobrepasa el límite, aborta con código **HTTP 429 Too Many Requests** devolviendo JSON (para API) o pantalla amigable con Tailwind CSS y cabecera `Retry-After`. Al completar un login exitoso, el registro de intentos fallidos se reinicia a 0.

---

## 📋 12. Prácticas de Infraestructura, Despliegue y Protección de Datos
* **Aislamiento de Secretos (.env):**
  - Credenciales de PostgreSQL, SID y Token de Twilio almacenados en `.env`. El archivo `.env` está en `.gitignore` y jamás se incluye en el control de versiones.
* **Protección del Almacenamiento Médico:**
  - Los expedientes y recetas no residen en carpetas públicas. Se protegen con `.htaccess` (`Deny from all`) y solo se transmiten vía streaming PHP tras comprobar la sesión del usuario.
* **Optimización de Recursos del Servidor:**
  - Compresión y remuestreo de imágenes mediante GD antes de almacenar en disco, reduciendo hasta un 70% el peso de recetas y optimizando el almacenamiento y ancho de banda en dispositivos móviles.
* **Manejo Seguro de Errores en Producción:**
  - `display_errors = 0` en entornos de producción. Los detalles técnicos se canalizan exclusivamente a `logs/error.log` sin exponer información sensible a los usuarios.

---

## 📋 13. Tareas Programadas y Recordatorios en Segundo Plano (Cron Jobs en PHP)
* **Roles con acceso:** Sistema Operativo / Servicio de Automatización del Servidor (PHP CLI).
* **Descripción de Infraestructura:**
  - Para garantizar que los recordatorios clínicos se entreguen a tiempo sin depender de que el usuario tenga abierta la aplicación web, el servidor Linux configurará un Cron Job que corre cada minuto:
    ```bash
    * * * * * php /var/www/html/Control-AdultoMayor/tasks/recordatorios.php >> /var/www/html/Control-AdultoMayor/logs/cron.log 2>&1
    ```
* **Lógica de Ejecución del Script CLI (`tasks/recordatorios.php`):**
  1. **Evaluación de Tiempo Estricta en UTC:** Compara las marcas de tiempo almacenadas contra el reloj del servidor en UTC (`NOW() AT TIME ZONE 'UTC'`).
  2. **Bloqueo Concurrente Anti-Duplicados:** Emplea `FOR UPDATE SKIP LOCKED` en PostgreSQL para permitir múltiples hilos o ejecuciones concurrentes sin riesgo de enviar dos veces el mismo mensaje.
  3. **Recordatorios de Medicamentos con Consolidación de Polifarmacia:**
     - Identifica tomas con `estado IN ('pendiente', 'pospuesta')` y `recordatorio_enviado = FALSE` cuya hora programada o pospuesta se encuentre dentro de la ventana de disparo (próximos 5 minutos o vencidas en los últimos 60 min).
     - **Agrupación por Paciente/Teléfono:** Si el paciente debe tomar múltiples medicamentos en la misma ventana horaria (ej. 4 pastillas matutinas), el script los consolida en un **ÚNICO mensaje interactivo de WhatsApp**, formateado con viñetas numéricas claras para evitar fatiga por spam y optimizar costos de API en Twilio.
     - Actualiza inmediatamente en bloque `recordatorio_enviado = TRUE` y `notificado_at = CURRENT_TIMESTAMP` para todas las tomas agrupadas.
  4. **Recordatorios de Citas Médicas:**
     - Identifica citas en estado `programada` con 2 horas de anticipación a la consulta.
     - Envía los datos del doctor, centro médico y notas preparatorias (ej. ayuno).
  5. **Escalamiento al Tutor Familiar por Omisión (>60 min):**
     - Si transcurren más de 60 minutos desde la hora programada de un medicamento y el paciente no ha marcado la toma ni la ha pospuesto, el cron job despacha una alerta preventiva al WhatsApp del familiar tutor principal para que intervenga oportunamente.

---

## 📋 14. Resiliencia Offline y Progressive Web App (PWA)
* **Roles con acceso:** Todos los usuarios autenticados y no autenticados en dispositivos móviles y de escritorio.
* **Manifiesto de Aplicación (`public/manifest.json`):**
  - Configurado en modo `display: "standalone"`, permitiendo su instalación como aplicación nativa en iOS (Safari "Añadir a pantalla de inicio") y Android (prompt de instalación WebAPK).
  - Define `theme_color: "#2563eb"` (azul clínico) y `background_color: "#f8fafc"`.
  - Iconos de alta resolución (192x192 y 512x512) con propósito `"any maskable"`.
* **Estrategias de Caché en Service Worker (`public/sw.js`):**
  - **Precache Estático:** Durante la fase `install`, almacena hojas de estilo (Tailwind CDN), fuentes, logo e iconos clave.
  - **Estrategia "Network First, falling back to cache" para `/dashboard`:**
    - Intenta siempre obtener la versión más fresca de los medicamentos y estado del paciente desde la red PostgreSQL/PHP.
    - En caso de interrupción de conectividad (modo avión, sin señal en la calle o corte de internet en casa), el Service Worker sirve automáticamente la última copia almacenada en la caché local del navegador.
    - Garantiza que el adulto mayor pueda consultar qué pastillas le tocan y a qué dosis incluso sin conexión a internet.
  - **Exclusión de Métodos Mutables:** Peticiones `POST`, `PUT` o `DELETE` (como `/api/sos`, `/api/otp/send`, `/medicamentos/toma`) se ejecutan directamente contra la red sin pasar por caché para garantizar consistencia clínica.

