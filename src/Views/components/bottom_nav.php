<?php
$isLoggedIn = !empty($_SESSION['user_id']);
$userRole = $_SESSION['user_role'] ?? '';
$pacienteActivoId = $_SESSION['paciente_activo_id'] ?? null;
$currentUri = $_SERVER['REQUEST_URI'] ?? '';

// Identificar tab activa
$isDashboard = strpos($currentUri, '/dashboard') !== false;
$isMedicamentos = strpos($currentUri, '/medicamentos') !== false;
$isExpediente = strpos($currentUri, '/expediente') !== false;
$isSalud = strpos($currentUri, '/salud') !== false;
?>

<?php if ($isLoggedIn): ?>
<!-- Barra de Navegación Inferior Táctil Fija (Solo Móviles / < md) -->
<nav class="md:hidden fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-md border-t border-slate-200/90 px-2 py-1 shadow-lg shadow-slate-900/10 transition-all safe-area-pb" aria-label="Navegación Móvil Principal">
    <div class="grid grid-cols-4 gap-1 max-w-lg mx-auto items-center">
        
        <!-- Tab 1: Inicio / Dashboard -->
        <a href="<?= APP_URL ?>/dashboard" 
           class="tap-bounce flex flex-col items-center justify-center min-h-[48px] py-1 px-1 rounded-2xl transition-all <?= $isDashboard ? 'text-blue-600 bg-blue-50/80 font-bold' : 'text-slate-500 hover:text-slate-900 font-medium' ?>">
            <span class="text-xl mb-0.5">🏠</span>
            <span class="text-[11px] leading-tight">Inicio</span>
        </a>

        <!-- Tab 2: Medicamentos -->
        <a href="<?= APP_URL ?>/medicamentos" 
           class="tap-bounce flex flex-col items-center justify-center min-h-[48px] py-1 px-1 rounded-2xl transition-all <?= $isMedicamentos ? 'text-teal-600 bg-teal-50/80 font-bold' : 'text-slate-500 hover:text-slate-900 font-medium' ?>">
            <span class="text-xl mb-0.5">💊</span>
            <span class="text-[11px] leading-tight">Medicinas</span>
        </a>

        <!-- Tab 3: Expediente Clínico -->
        <a href="<?= APP_URL ?>/expediente" 
           class="tap-bounce flex flex-col items-center justify-center min-h-[48px] py-1 px-1 rounded-2xl transition-all <?= $isExpediente ? 'text-indigo-600 bg-indigo-50/80 font-bold' : 'text-slate-500 hover:text-slate-900 font-medium' ?>">
            <span class="text-xl mb-0.5">📋</span>
            <span class="text-[11px] leading-tight">Expediente</span>
        </a>

        <!-- Tab 4: Botón SOS Rápido -->
        <button type="button" 
                onclick="document.getElementById('btn-sos')?.click() || (window.location.href='<?= APP_URL ?>/dashboard#btn-sos')" 
                class="tap-bounce flex flex-col items-center justify-center min-h-[48px] py-1 px-1 rounded-2xl bg-rose-50 text-rose-600 font-black border border-rose-200 transition-all hover:bg-rose-100 active:scale-95 cursor-pointer">
            <span class="text-xl mb-0.5 animate-pulse">🆘</span>
            <span class="text-[11px] leading-tight text-rose-700">SOS</span>
        </button>

    </div>
</nav>

<!-- Espaciador inferior para no tapar contenido en móviles -->
<div class="md:hidden h-20 w-full" aria-hidden="true"></div>
<?php endif; ?>
