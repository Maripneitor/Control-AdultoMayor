<?php
$isLoggedIn = !empty($_SESSION['user_id']);
$userRole = $_SESSION['user_role'] ?? '';
$pacienteActivoId = $_SESSION['paciente_activo_id'] ?? null;
$currentUri = $_SERVER['REQUEST_URI'] ?? '';

// Identificar pestaña activa
$isDashboard = strpos($currentUri, '/dashboard') !== false;
$isSalud = strpos($currentUri, '/salud') !== false;
$isMedicamentos = strpos($currentUri, '/medicamentos') !== false;
$isCitas = strpos($currentUri, '/citas') !== false;
$isExpediente = strpos($currentUri, '/expediente') !== false;
?>

<?php if ($isLoggedIn): ?>
<!-- Barra de Navegación Inferior Móvil Fija (Solo Móviles / < lg) -->
<nav class="lg:hidden fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-md border-t border-slate-200/90 px-1 py-1.5 shadow-lg shadow-slate-900/10 transition-all safe-area-pb" aria-label="Navegación Móvil Principal">
    <div class="grid grid-cols-5 gap-0.5 max-w-lg mx-auto items-center">
        
        <!-- Tab 1: Inicio / Dashboard -->
        <a href="<?= APP_URL ?>/dashboard" 
           class="tap-bounce flex flex-col items-center justify-center min-h-[48px] min-w-[48px] py-1 px-1 rounded-2xl transition-all <?= $isDashboard ? 'text-blue-600 bg-blue-50/80 font-bold' : 'text-slate-500 hover:text-slate-900 font-medium' ?>"
           aria-label="Inicio">
            <svg class="w-5 h-5 mb-0.5 shrink-0" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/>
            </svg>
            <span class="text-[10px] sm:text-[11px] leading-tight">Inicio</span>
        </a>

        <!-- Tab 2: Signos Vitales -->
        <a href="<?= APP_URL ?>/salud" 
           class="tap-bounce flex flex-col items-center justify-center min-h-[48px] min-w-[48px] py-1 px-1 rounded-2xl transition-all <?= $isSalud ? 'text-rose-600 bg-rose-50/80 font-bold' : 'text-slate-500 hover:text-slate-900 font-medium' ?>"
           aria-label="Signos Vitales">
            <svg class="w-5 h-5 mb-0.5 shrink-0" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/>
            </svg>
            <span class="text-[10px] sm:text-[11px] leading-tight">Signos</span>
        </a>

        <!-- Tab 3: Medicinas / Medicamentos -->
        <a href="<?= APP_URL ?>/medicamentos" 
           class="tap-bounce flex flex-col items-center justify-center min-h-[48px] min-w-[48px] py-1 px-1 rounded-2xl transition-all <?= $isMedicamentos ? 'text-teal-600 bg-teal-50/80 font-bold' : 'text-slate-500 hover:text-slate-900 font-medium' ?>"
           aria-label="Medicinas">
            <svg class="w-5 h-5 mb-0.5 shrink-0" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a4.5 4.5 0 119 9l-9 9a4.5 4.5 0 01-9-9l9-9z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 13.5l9-9"/>
            </svg>
            <span class="text-[10px] sm:text-[11px] leading-tight">Medicinas</span>
        </a>

        <!-- Tab 4: Citas Médicas -->
        <a href="<?= APP_URL ?>/citas" 
           class="tap-bounce flex flex-col items-center justify-center min-h-[48px] min-w-[48px] py-1 px-1 rounded-2xl transition-all <?= $isCitas ? 'text-amber-600 bg-amber-50/80 font-bold' : 'text-slate-500 hover:text-slate-900 font-medium' ?>"
           aria-label="Citas Médicas">
            <svg class="w-5 h-5 mb-0.5 shrink-0" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
            </svg>
            <span class="text-[10px] sm:text-[11px] leading-tight">Citas</span>
        </a>

        <!-- Tab 5: Expediente Clínico -->
        <a href="<?= APP_URL ?>/expediente" 
           class="tap-bounce flex flex-col items-center justify-center min-h-[48px] min-w-[48px] py-1 px-1 rounded-2xl transition-all <?= $isExpediente ? 'text-indigo-600 bg-indigo-50/80 font-bold' : 'text-slate-500 hover:text-slate-900 font-medium' ?>"
           aria-label="Expediente">
            <svg class="w-5 h-5 mb-0.5 shrink-0" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z"/>
            </svg>
            <span class="text-[10px] sm:text-[11px] leading-tight">Expediente</span>
        </a>

    </div>
</nav>

<!-- Espaciador inferior para no tapar contenido en dispositivos móviles -->
<div class="lg:hidden h-20 w-full" aria-hidden="true"></div>
<?php endif; ?>
