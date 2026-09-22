<?php
declare(strict_types=1);

$dashboardUrl = defined('APP_URL') ? APP_URL . '/dashboard' : '/dashboard';
$homeUrl = defined('APP_URL') ? APP_URL . '/' : '/';
?>
<!DOCTYPE html>
<html lang="es" class="h-full overflow-x-hidden">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Página No Encontrada | VitalSenior</title>
    <!-- Tailwind CSS CDN para renderizado ágil y responsive -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        vital: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            500: '#22c55e',
                            600: '#16a34a',
                            700: '#15803d',
                            800: '#166534',
                            900: '#14532d'
                        },
                        primary: {
                            600: '#0284c7',
                            700: '#0369a1'
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-slate-50 text-slate-800 h-full flex flex-col justify-between overflow-x-hidden font-sans antialiased selection:bg-vital-100 selection:text-vital-800">

    <!-- Header Accesible -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-30 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <a href="<?= htmlspecialchars($homeUrl) ?>" class="flex items-center gap-2 group focus:outline-none focus:ring-2 focus:ring-vital-600 rounded-lg p-1" aria-label="Volver al inicio de VitalSenior">
                <div class="w-10 h-10 rounded-xl bg-vital-600 flex items-center justify-center text-white font-bold text-xl shadow-md group-hover:bg-vital-700 transition">
                    VS
                </div>
                <span class="text-xl font-extrabold tracking-tight text-slate-900">
                    Vital<span class="text-vital-600">Senior</span>
                </span>
            </a>

            <div class="flex items-center gap-4">
                <a href="tel:911" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-red-50 text-red-700 hover:bg-red-100 text-sm font-bold border border-red-200 transition focus:outline-none focus:ring-2 focus:ring-red-500">
                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                    SOS 911
                </a>
            </div>
        </div>
    </header>

    <!-- Contenido Central 404 -->
    <main class="flex-1 flex items-center justify-center p-6 md:p-12">
        <div class="max-w-xl w-full bg-white rounded-3xl shadow-xl border border-slate-100 p-8 md:p-12 text-center relative overflow-hidden">
            
            <!-- Decoración sutil de fondo -->
            <div class="absolute -top-12 -right-12 w-40 h-40 bg-vital-50 rounded-full blur-2xl pointer-events-none"></div>
            <div class="absolute -bottom-12 -left-12 w-40 h-40 bg-blue-50 rounded-full blur-2xl pointer-events-none"></div>

            <!-- Código e Ilustración accesibles -->
            <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-vital-100 text-vital-700 mb-6 font-extrabold text-4xl shadow-inner">
                404
            </div>

            <h1 class="text-3xl md:text-4xl font-extrabold text-slate-900 tracking-tight mb-4">
                Página No Encontrada
            </h1>

            <p class="text-lg text-slate-600 mb-8 leading-relaxed">
                La sección médica o expediente que buscas no existe o ha sido reubicada por motivos de seguridad. No te preocupes, tus datos clínicos están a salvo.
            </p>

            <!-- Acciones de Navegación Segura -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="<?= htmlspecialchars($dashboardUrl) ?>" 
                   class="w-full sm:w-auto inline-flex items-center justify-center gap-3 px-8 py-3.5 rounded-xl bg-vital-600 hover:bg-vital-700 active:bg-vital-800 text-white font-bold text-base shadow-lg shadow-vital-600/20 transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-vital-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Regresar al Dashboard
                </a>

                <a href="<?= htmlspecialchars($homeUrl) ?>" 
                   class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl bg-slate-100 hover:bg-slate-200 active:bg-slate-300 text-slate-700 font-semibold text-base transition-colors focus:outline-none focus:ring-2 focus:ring-slate-400">
                    Ir al Inicio
                </a>
            </div>

            <!-- Soporte telefónico de asistencia -->
            <div class="mt-10 pt-6 border-t border-slate-100 text-sm text-slate-500 flex flex-col sm:flex-row items-center justify-center gap-2">
                <span>¿Necesitas asistencia técnica o médica?</span>
                <a href="mailto:soporte@vitalsenior.com" class="text-vital-700 font-semibold hover:underline">
                    soporte@vitalsenior.com
                </a>
            </div>
        </div>
    </main>

    <!-- Footer Simple y Responsivo -->
    <footer class="bg-white border-t border-slate-200 py-4 text-center text-xs text-slate-500">
        <p>&copy; <?= date('Y') ?> VitalSenior - Plataforma de Acompañamiento Integral para Adultos Mayores.</p>
    </footer>

</body>
</html>
