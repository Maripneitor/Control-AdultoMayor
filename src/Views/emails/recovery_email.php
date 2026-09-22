<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>VitalSenior - Correo de Recuperación de Contraseña</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
            sans: ['Outfit', 'sans-serif'],
          }
        }
      }
    }
  </script>
</head>
<body class="bg-[#f0f4f9] text-[#1e293b] font-sans antialiased min-h-screen py-8 px-4 sm:px-6 lg:px-8 flex flex-col items-center justify-center">

  <div class="w-full max-w-2xl">
    
    <!-- Meta Info de Correo Preview -->
    <div class="bg-white/70 backdrop-blur-md rounded-t-2xl border border-b-0 border-slate-200/80 px-6 py-3.5 flex items-center justify-between text-xs text-slate-500 shadow-sm">
      <div class="flex items-center gap-2">
        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-blue-100 text-blue-700 font-semibold text-[11px]">VS</span>
        <span>De: <strong class="text-slate-700 font-medium">VitalSenior Seguridad</strong> &lt;seguridad@vitalsenior.com&gt;</span>
      </div>
      <div class="hidden sm:flex items-center gap-3">
        <span>Para: <strong><?php echo htmlspecialchars($emailDestino ?? ''); ?></strong></span>
        <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span>
        <span>Hoy, <?php echo date('h:i A'); ?></span>
      </div>
    </div>

    <!-- Tarjeta Principal del Email -->
    <div class="bg-white rounded-b-2xl sm:rounded-2xl sm:rounded-t-none border border-slate-200/90 shadow-xl overflow-hidden">
      
      <!-- Header -->
      <div class="bg-gradient-to-r from-[#1d4ed8] via-[#2563eb] to-[#3b82f6] px-8 py-7 text-white flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="w-11 h-11 bg-white/15 backdrop-blur-sm rounded-xl flex items-center justify-center border border-white/20 shadow-inner">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
          </div>
          <div>
            <h1 class="text-2xl font-bold tracking-tight text-white">
              VitalSenior
            </h1>
            <p class="text-xs text-blue-100 font-normal">Cuidado y Salud al Alcance</p>
          </div>
        </div>
        <div class="inline-flex items-center gap-1.5 bg-white/15 backdrop-blur-sm border border-white/25 px-3 py-1 rounded-full text-xs font-medium text-white shadow-sm">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
          <span>Seguridad de Cuenta</span>
        </div>
      </div>

      <!-- Cuerpo del Correo -->
      <div class="p-8 sm:p-10 space-y-7">
        
        <div>
          <span class="text-xs uppercase tracking-wider font-semibold text-blue-600 bg-blue-50 px-2.5 py-1 rounded-md">Notificación de Seguridad</span>
          <h2 class="text-2xl sm:text-3xl font-bold text-slate-800 mt-3 tracking-tight">
            Restablece tu contraseña
          </h2>
          <p class="text-base text-slate-600 mt-2 leading-relaxed">
            Hola, <strong class="text-slate-800"><?php echo htmlspecialchars($nombreUsuario ?? 'Usuario'); ?></strong>. Recibimos una solicitud para restablecer la contraseña de tu cuenta en VitalSenior.
          </p>
        </div>

        <!-- Tarjeta de Instrucción y Botón Principal -->
        <div class="bg-gradient-to-br from-blue-50/60 to-slate-50 border border-blue-100 rounded-2xl p-6 sm:p-7 text-center">
          <p class="text-slate-700 text-sm sm:text-base font-normal mb-5 max-w-md mx-auto">
            Haz clic en el botón azul para elegir una nueva contraseña segura y volver a acceder a tu historial médico y calendario.
          </p>

          <a href="<?php echo htmlspecialchars($urlRecuperacion ?? '#'); ?>" class="inline-flex items-center justify-center gap-2 bg-[#004ac6] hover:bg-[#003ea8] text-white text-base font-semibold px-8 py-3.5 rounded-xl shadow-lg shadow-blue-500/25 transition-all text-center w-full sm:w-auto" style="text-decoration:none; display:inline-block; padding: 12px 24px;">
            <span>Restablecer Mi Contraseña</span>
          </a>

          <div class="flex items-center justify-center gap-1.5 mt-4 text-xs text-slate-500">
            <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>Este enlace es válido durante <strong>15 minutos</strong> por seguridad.</span>
          </div>
        </div>

        <!-- Fallback URL -->
        <div class="pt-1">
          <p class="text-xs text-slate-500 mb-2">
            ¿Tienes problemas con el botón? También puedes copiar y pegar el siguiente enlace directo en tu navegador:
          </p>
          <div class="bg-slate-50 p-3 rounded-lg border border-slate-200/80 font-mono text-xs text-blue-600 break-all select-all flex items-center justify-between gap-2">
            <span class="truncate"><?php echo htmlspecialchars($urlRecuperacion ?? '#'); ?></span>
          </div>
        </div>

        <!-- Warning Box -->
        <div class="bg-amber-50/80 border border-amber-200/80 rounded-xl p-4 flex items-start gap-3.5">
          <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-800 flex items-center justify-center shrink-0 mt-0.5">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0118 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
          </div>
          <div class="text-xs sm:text-sm text-amber-900 leading-relaxed">
            <strong class="font-semibold block mb-0.5 text-amber-950">¿No solicitaste este cambio?</strong>
            Puedes ignorar este mensaje con total tranquilidad; tu clave actual continuará siendo segura. Si crees que alguien intenta acceder sin autorización, te recomendamos contactar a soporte de inmediato.
          </div>
        </div>
      </div>

      <!-- Footer -->
      <div class="bg-[#0f172a] text-slate-400 px-8 py-8 border-t border-slate-800 text-xs">
        <div class="text-center sm:text-left space-y-2 text-slate-500 text-[11px] leading-relaxed">
          <p>
            Este es un correo electrónico de seguridad generado automáticamente por el sistema de VitalSenior. No respondas directamente a este mensaje.
          </p>
          <p>
            © <?php echo date('Y'); ?> VitalSenior. Todos los derechos reservados.
          </p>
        </div>
      </div>

    </div>

  </div>

</body>
</html>

