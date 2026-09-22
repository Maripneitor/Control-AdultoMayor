<!DOCTYPE html>
<html class="light scroll-smooth h-full" lang="es">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport"/>
    <title>VitalSenior - Cuidado y Salud Geriátrica Integral</title>
    <!-- Tailwind CSS desde CDN -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'system-ui', '-apple-system', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    },
                    boxShadow: {
                        'subtle': '0 1px 3px 0 rgba(0, 0, 0, 0.04), 0 1px 2px 0 rgba(0, 0, 0, 0.02)',
                        'card': '0 4px 20px -2px rgba(0, 0, 0, 0.05), 0 0 0 1px rgba(226, 232, 240, 0.8)',
                    }
                }
            }
        }
    </script>
    <script src="<?php echo APP_URL; ?>/assets/js/accessibility.js"></script>
    
    <!-- Fuentes Google -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;600;700&display=swap" rel="stylesheet"/>
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            background-color: var(--bg-body, #f8fafc);
            color: var(--text-body, #0f172a);
        }
        
        .tap-bounce {
            transition: transform 0.15s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.15s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .tap-bounce:active {
            transform: scale(0.98);
        }
        
        /* Touch target mínimo 48px */
        .touch-min {
            min-height: 48px;
            min-width: 48px;
        }

        /* Safe area para dispositivos móviles */
        .safe-area-pb {
            padding-bottom: env(safe-area-inset-bottom, 0.5rem);
        }

        @media print {
            header, nav, #drawer-lateral, #modal-pin-medico, .no-print {
                display: none !important;
            }
            body {
                background: white !important;
            }
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 min-h-full flex flex-col justify-between selection:bg-blue-600/20 selection:text-blue-700 antialiased">

<?php 
$isLoggedIn = !empty($_SESSION['user_id']);
$userRole = $_SESSION['user_role'] ?? '';
$userNameDisplay = $_SESSION['user_name'] ?? 'Usuario';
$pacienteActivoId = $_SESSION['paciente_activo_id'] ?? ($paciente['id'] ?? 0);
$pacienteNombreDisplay = $_SESSION['paciente_activo_nombre'] ?? ($paciente['nombre'] ?? 'Adulto Mayor');
$pacienteQrToken = $_SESSION['paciente_qr_token'] ?? ($paciente['qr_token'] ?? '');
$todosLosPacientes = $todosLosPacientes ?? [];

$roleLabel = match($userRole) {
    'familiar_admin' => 'Familiar Admin',
    'cuidador_auxiliar', 'cuidador' => 'Cuidador',
    'medico' => 'Médico',
    'paciente', 'adulto_mayor' => 'Paciente',
    'admin', 'administrador' => 'Administrador',
    default => 'Familiar'
};
?>

<!-- 1. Top Bar / Navbar Sólida y Unificada -->
<header class="bg-white/90 backdrop-blur-md sticky top-0 z-40 border-b border-slate-200/90 shadow-xs px-4 sm:px-6 lg:px-8 py-3 transition-all no-print">
    <div class="max-w-7xl mx-auto flex items-center justify-between gap-3 sm:gap-4">
        
        <?php if (!$isLoggedIn): ?>
            <!-- Invitados / Landing Page Navbar -->
            <a class="tap-bounce flex items-center gap-2.5 text-xl sm:text-2xl font-black text-blue-600 tracking-tight group" href="<?php echo APP_URL; ?>/">
                <div class="w-10 h-10 rounded-2xl bg-blue-600 flex items-center justify-center text-white shadow-md shadow-blue-500/20 group-hover:scale-105 transition-transform text-xl font-black">
                    ✚
                </div>
                <span>Vital<span class="text-slate-800">Senior</span></span>
            </a>

            <div class="hidden md:flex items-center gap-6 lg:gap-8 text-sm font-bold text-slate-600">
                <a class="hover:text-blue-600 transition-colors py-2 px-1" href="<?php echo APP_URL; ?>/#modulos">Módulos</a>
                <a class="hover:text-blue-600 transition-colors py-2 px-1" href="<?php echo APP_URL; ?>/#emergencia">Ficha de Emergencia</a>
                <a class="hover:text-blue-600 transition-colors py-2 px-1" href="<?php echo APP_URL; ?>/#como-funciona">Cómo Funciona</a>
                <a class="hover:text-blue-600 transition-colors py-2 px-1" href="<?php echo APP_URL; ?>/#testimonios">Testimonios</a>
                
                <div class="flex items-center gap-1.5 border-l border-slate-200 pl-4 py-1">
                    <button onclick="Accessibility?.changeFontSize(-1)" class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs flex items-center justify-center transition-colors cursor-pointer" title="Disminuir tamaño" aria-label="Disminuir tamaño">A-</button>
                    <button onclick="Accessibility?.changeFontSize(1)" class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-sm flex items-center justify-center transition-colors cursor-pointer" title="Aumentar tamaño" aria-label="Aumentar tamaño">A+</button>
                </div>
            </div>

            <div class="flex items-center gap-2 sm:gap-3">
                <a href="<?php echo APP_URL; ?>/login" class="tap-bounce touch-min bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs sm:text-sm rounded-2xl px-5 py-2.5 shadow-md shadow-blue-600/20 transition-all flex items-center justify-center">
                    Iniciar Sesión
                </a>
            </div>

        <?php else: ?>
            <!-- LADO IZQUIERDO: Logo 'VitalSenior' con badge sutil de rol (Desktop) -->
            <div class="hidden md:flex items-center gap-3 shrink-0">
                <a class="tap-bounce flex items-center gap-2.5 group" href="<?php echo APP_URL; ?>/dashboard">
                    <div class="w-10 h-10 rounded-2xl bg-blue-600 flex items-center justify-center text-white shadow-md shadow-blue-500/20 group-hover:scale-105 transition-transform text-lg font-black">
                        ✚
                    </div>
                    <span class="text-lg font-black tracking-tight text-slate-900">Vital<span class="text-blue-600">Senior</span></span>
                </a>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold tracking-wide bg-slate-100 text-slate-600 border border-slate-200">
                    <?= $roleLabel ?>
                </span>
            </div>

            <!-- CENTRO: Chip selector del paciente activo con avatar compacto, nombre y '+ Vincular' -->
            <div class="hidden md:flex items-center gap-2 bg-slate-50/90 border border-slate-200/80 rounded-2xl px-3 py-1.5 shadow-xs max-w-md">
                <div class="w-7 h-7 rounded-full bg-blue-600 text-white flex items-center justify-center text-xs font-black shrink-0 shadow-xs">
                    <?= mb_strtoupper(mb_substr($pacienteNombreDisplay, 0, 1)) ?>
                </div>
                
                <div class="min-w-0 flex items-center gap-1.5 truncate">
                    <span class="text-xs text-slate-400 font-semibold hidden lg:inline">Paciente:</span>
                    <span class="text-xs sm:text-sm font-extrabold text-slate-900 truncate" title="<?= e($pacienteNombreDisplay) ?>">
                        <?= e($pacienteNombreDisplay) ?>
                    </span>
                </div>

                <?php if (!empty($todosLosPacientes) && count($todosLosPacientes) > 1): ?>
                    <form action="<?php echo APP_URL; ?>/dashboard" method="GET" class="m-0 shrink-0">
                        <select name="seleccionar_paciente" onchange="this.form.submit()" 
                                class="text-[11px] font-bold text-slate-700 bg-white hover:bg-slate-100 border border-slate-200 rounded-lg py-1 px-2 cursor-pointer outline-none transition"
                                title="Cambiar paciente">
                            <?php foreach ($todosLosPacientes as $p): ?>
                                <option value="<?= e((string)$p['id']) ?>" <?= ($p['id'] == $pacienteActivoId) ? 'selected' : '' ?>>
                                    👤 <?= e($p['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                <?php endif; ?>

                <a href="<?php echo APP_URL; ?>/vincular" 
                   class="tap-bounce text-[11px] font-bold text-blue-600 hover:text-blue-700 bg-blue-50 hover:bg-blue-100/80 px-2 py-1 rounded-lg transition shrink-0 flex items-center gap-0.5"
                   title="Vincular otro adulto mayor">
                    <span>+</span>
                    <span class="hidden xl:inline">Vincular</span>
                </a>
            </div>

            <!-- LADO DERECHO: Grupo de acciones ordenadas en una sola fila (Desktop) -->
            <div class="hidden md:flex items-center gap-2.5 shrink-0">
                <!-- Botón primario: PIN Médico -->
                <button type="button" onclick="abrirModalPinMedico()" 
                        class="tap-bounce px-3.5 py-2 bg-blue-600 hover:bg-blue-700 active:scale-95 text-white text-xs font-bold rounded-xl shadow-xs transition flex items-center gap-1.5 cursor-pointer">
                    <span>🔑</span>
                    <span>PIN Médico</span>
                </button>

                <!-- Botón secundario: Ficha QR -->
                <a href="<?php echo APP_URL; ?>/emergencia?token=<?= e((string)$pacienteQrToken) ?>" target="_blank" 
                   class="tap-bounce px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl border border-slate-200 transition flex items-center gap-1.5 shadow-xs">
                    <span>📱</span>
                    <span>Ficha QR</span>
                </a>

                <!-- Botón secundario: Imprimir -->
                <button type="button" onclick="window.print()" 
                        class="tap-bounce px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl border border-slate-200 transition flex items-center gap-1.5 shadow-xs cursor-pointer">
                    <span>🖨️</span>
                    <span>Imprimir</span>
                </button>

                <!-- Menú Usuario Dropdown -->
                <div class="relative" id="user-menu-wrapper">
                    <button type="button" onclick="toggleUserDropdownMenu()" id="user-menu-toggle-btn"
                            class="tap-bounce flex items-center gap-2 p-1.5 rounded-xl hover:bg-slate-100 border border-slate-200 transition cursor-pointer">
                        <div class="w-7 h-7 rounded-lg bg-slate-800 text-white flex items-center justify-center text-xs font-bold">
                            <?= mb_strtoupper(mb_substr($userNameDisplay, 0, 1)) ?>
                        </div>
                        <span class="text-xs font-bold text-slate-700 hidden lg:inline max-w-[110px] truncate">
                            <?= e($userNameDisplay) ?>
                        </span>
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>

                    <div id="user-dropdown-popover" 
                         class="hidden absolute right-0 mt-2 w-48 bg-white rounded-2xl shadow-xl border border-slate-100 py-1.5 z-50 text-xs font-medium">
                        <div class="px-3 py-2 border-b border-slate-100">
                            <p class="font-bold text-slate-800 truncate"><?= e($userNameDisplay) ?></p>
                            <p class="text-[10px] text-slate-400"><?= $roleLabel ?></p>
                        </div>
                        <a href="<?php echo APP_URL; ?>/perfil" class="flex items-center gap-2 px-3 py-2 text-slate-700 hover:bg-slate-50 transition">
                            <span>⚙️</span>
                            <span>Mi Perfil</span>
                        </a>
                        <a href="<?php echo APP_URL; ?>/logout" class="flex items-center gap-2 px-3 py-2 text-rose-600 hover:bg-rose-50 transition border-t border-slate-100 font-bold">
                            <span>🚪</span>
                            <span>Cerrar Sesión</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- ENCABEZADO EN MÓVIL (< md): Ultra-limpio en una sola barra fija -->
            <div class="flex md:hidden items-center justify-between w-full">
                <!-- Izquierda: Isotipo + Nombre del paciente activo -->
                <div class="flex items-center gap-2 min-w-0 pr-2">
                    <a href="<?php echo APP_URL; ?>/dashboard" class="tap-bounce shrink-0">
                        <div class="w-8 h-8 rounded-xl bg-blue-600 flex items-center justify-center text-white shadow-xs text-sm font-black">
                            ✚
                        </div>
                    </a>
                    <div class="flex items-center gap-1.5 bg-blue-50 border border-blue-200/80 px-2.5 py-1 rounded-xl min-w-0 truncate">
                        <div class="w-5 h-5 rounded-full bg-blue-600 text-white flex items-center justify-center text-[10px] font-black shrink-0">
                            <?= mb_strtoupper(mb_substr($pacienteNombreDisplay, 0, 1)) ?>
                        </div>
                        <span class="text-xs font-bold text-blue-950 truncate">
                            <?= e($pacienteNombreDisplay) ?>
                        </span>
                    </div>
                </div>

                <!-- Derecha: Botón de Menú 3 Puntos / Hamburguesa de 48x48px -->
                <button type="button" onclick="abrirDrawerLateral()" 
                        class="tap-bounce touch-min w-11 h-11 rounded-2xl bg-slate-100 hover:bg-slate-200 active:scale-95 text-slate-800 font-bold flex items-center justify-center text-lg border border-slate-200 shadow-xs cursor-pointer shrink-0"
                        aria-label="Abrir menú de opciones">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
            </div>
        <?php endif; ?>

    </div>
</header>

<?php if ($isLoggedIn): ?>
<!-- 2. Drawer Lateral Deslizable en Móvil (< md) -->
<div id="drawer-lateral" class="fixed inset-0 z-50 hidden transition-all duration-300 no-print" aria-hidden="true">
    <!-- Backdrop desenfocado -->
    <div id="drawer-backdrop" 
         onclick="cerrarDrawerLateral()" 
         class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm opacity-0 transition-opacity duration-300"></div>

    <!-- Panel deslizante -->
    <div id="drawer-panel" 
         class="fixed top-0 right-0 bottom-0 w-[85%] max-w-sm bg-white shadow-2xl flex flex-col translate-x-full transition-transform duration-300 ease-out z-10 border-l border-slate-100">
        
        <!-- Header del Drawer -->
        <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/80">
            <div class="flex items-center gap-2.5 min-w-0">
                <div class="w-9 h-9 rounded-2xl bg-blue-600 text-white flex items-center justify-center text-base font-black shadow-xs shrink-0">
                    ✚
                </div>
                <div class="min-w-0">
                    <h3 class="text-sm font-extrabold text-slate-900 truncate">VitalSenior</h3>
                    <p class="text-[11px] text-slate-500 font-medium truncate"><?= e($userNameDisplay) ?> • <?= $roleLabel ?></p>
                </div>
            </div>
            <button type="button" onclick="cerrarDrawerLateral()" 
                    class="tap-bounce touch-min w-10 h-10 rounded-2xl bg-slate-200/80 hover:bg-slate-300 text-slate-600 font-bold flex items-center justify-center text-base transition cursor-pointer"
                    aria-label="Cerrar menú">
                ✕
            </button>
        </div>

        <!-- Cuerpo del Drawer con Opciones -->
        <div class="p-4 space-y-5 overflow-y-auto flex-1 text-xs">
            
            <!-- Paciente Activo -->
            <div class="p-3 bg-blue-50/70 border border-blue-100 rounded-2xl">
                <span class="text-[10px] font-bold text-blue-600 uppercase tracking-widest block mb-1">Paciente Activo</span>
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-full bg-blue-600 text-white flex items-center justify-center text-xs font-black">
                        <?= mb_strtoupper(mb_substr($pacienteNombreDisplay, 0, 1)) ?>
                    </div>
                    <span class="font-bold text-slate-900 truncate"><?= e($pacienteNombreDisplay) ?></span>
                </div>
            </div>

            <!-- Acciones Clínicas -->
            <div class="space-y-1.5">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block px-1">Acciones Clínicas</span>
                
                <!-- 1. Generar PIN Médico -->
                <button type="button" onclick="cerrarDrawerLateral(); abrirModalPinMedico();" 
                        class="tap-bounce touch-min w-full p-3 bg-blue-50 hover:bg-blue-100 text-blue-900 rounded-2xl font-bold flex items-center gap-3 transition">
                    <span class="text-base">🔑</span>
                    <span class="text-left flex-1 font-bold">Generar PIN Médico</span>
                </button>

                <!-- 2. Imprimir Expediente -->
                <button type="button" onclick="cerrarDrawerLateral(); window.print();" 
                        class="tap-bounce touch-min w-full p-3 bg-slate-50 hover:bg-slate-100 text-slate-800 rounded-2xl font-bold flex items-center gap-3 border border-slate-100 transition">
                    <span class="text-base">🖨️</span>
                    <span class="text-left flex-1">Imprimir Expediente</span>
                </button>

                <!-- 3. Ver Ficha QR -->
                <a href="<?php echo APP_URL; ?>/emergencia?token=<?= e((string)$pacienteQrToken) ?>" target="_blank"
                   class="tap-bounce touch-min w-full p-3 bg-slate-50 hover:bg-slate-100 text-slate-800 rounded-2xl font-bold flex items-center gap-3 border border-slate-100 transition">
                    <span class="text-base">📱</span>
                    <span class="text-left flex-1">Ver Ficha QR</span>
                </a>

                <!-- 4. Descargar Expediente PDF -->
                <a href="<?php echo APP_URL; ?>/paciente/pdf?id=<?= e((string)$pacienteActivoId) ?>" target="_blank"
                   class="tap-bounce touch-min w-full p-3 bg-slate-50 hover:bg-slate-100 text-slate-800 rounded-2xl font-bold flex items-center gap-3 border border-slate-100 transition">
                    <span class="text-base">📄</span>
                    <span class="text-left flex-1">Descargar Expediente PDF</span>
                </a>
            </div>

            <!-- Gestión Familiar -->
            <div class="space-y-1.5 pt-2 border-t border-slate-100">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block px-1">Gestión Familiar</span>

                <!-- 4. Vincular Otro Paciente -->
                <a href="<?php echo APP_URL; ?>/vincular" 
                   class="tap-bounce touch-min w-full p-3 bg-slate-50 hover:bg-slate-100 text-slate-800 rounded-2xl font-bold flex items-center gap-3 border border-slate-100 transition">
                    <span class="text-base">➕</span>
                    <span class="text-left flex-1">Vincular Otro Paciente</span>
                </a>

                <?php if (!empty($todosLosPacientes) && count($todosLosPacientes) > 1): ?>
                    <div class="space-y-1 pt-1">
                        <span class="text-[10px] text-slate-400 font-semibold block px-1">Cambiar paciente activo:</span>
                        <?php foreach ($todosLosPacientes as $p): ?>
                            <a href="<?php echo APP_URL; ?>/dashboard?seleccionar_paciente=<?= e((string)$p['id']) ?>" 
                               class="p-2.5 rounded-xl block font-bold transition text-xs <?= $p['id'] == $pacienteActivoId ? 'bg-blue-600 text-white shadow-xs' : 'bg-slate-50 hover:bg-slate-100 text-slate-700' ?>">
                                👤 <?= e($p['nombre'] . ' ' . ($p['apellidos'] ?? '')) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Cuenta -->
            <div class="space-y-1.5 pt-2 border-t border-slate-100">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block px-1">Cuenta</span>

                <a href="<?php echo APP_URL; ?>/perfil" 
                   class="tap-bounce touch-min w-full p-3 bg-slate-50 hover:bg-slate-100 text-slate-800 rounded-2xl font-bold flex items-center gap-3 border border-slate-100 transition">
                    <span class="text-base">⚙️</span>
                    <span class="text-left flex-1">Mi Perfil y Ajustes</span>
                </a>

                <!-- 5. Cerrar Sesión en rojo suave al fondo -->
                <a href="<?php echo APP_URL; ?>/logout" 
                   class="tap-bounce touch-min w-full p-3 bg-rose-50 hover:bg-rose-100 text-rose-700 rounded-2xl font-bold flex items-center gap-3 border border-rose-100 transition">
                    <span class="text-base">🚪</span>
                    <span class="text-left flex-1">Cerrar Sesión</span>
                </a>
            </div>

        </div>

        <div class="p-3 border-t border-slate-100 text-center text-[11px] text-slate-400 bg-slate-50">
            VitalSenior • Cuidado y Salud Geriátrica
        </div>
    </div>
</div>

<!-- 3. Modal Compartido de Generación de PIN Médico Temporal -->
<div id="modal-pin-medico" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 hidden items-center justify-center p-4 no-print">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl border border-slate-100 text-center relative">
        <button type="button" onclick="cerrarModalPinMedico()" class="absolute top-4 right-4 w-8 h-8 rounded-full bg-slate-100 text-slate-500 font-bold flex items-center justify-center hover:bg-slate-200 transition cursor-pointer">
            &times;
        </button>

        <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-2xl flex items-center justify-center text-2xl mx-auto mb-3">
            🔑
        </div>

        <h3 class="text-lg font-bold text-slate-900 mb-1">Acceso Clínico Temporal</h3>
        <p class="text-xs text-slate-500 mb-6">Genera un PIN seguro de 6 dígitos para que el médico tratante consulte el expediente en consulta.</p>

        <form id="form-generar-pin" onsubmit="ejecutarGeneracionPin(event)" class="space-y-4 text-left">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <input type="hidden" name="paciente_id" value="<?= e((string)$pacienteActivoId) ?>">

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nombre del Médico (Opcional)</label>
                <input type="text" name="medico_nombre" placeholder="Ej. Dr. Carlos Mendoza" 
                       class="w-full rounded-xl border-slate-300 px-3.5 py-2.5 text-xs sm:text-sm bg-slate-50 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10 outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Vigencia del PIN</label>
                <select name="duracion_horas" class="w-full rounded-xl border-slate-300 px-3.5 py-2.5 text-xs sm:text-sm bg-slate-50 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10 outline-none">
                    <option value="12">12 Horas (Consulta del día)</option>
                    <option value="24" selected>24 Horas (Recomendado)</option>
                    <option value="48">48 Horas (Fin de semana)</option>
                </select>
            </div>

            <div id="resultado-pin" class="hidden p-4 bg-emerald-50 rounded-2xl border border-emerald-200 text-center space-y-2">
                <span class="text-xs font-bold text-emerald-800 uppercase tracking-wider block">PIN de Acceso Médico:</span>
                <div id="pin-display" class="font-mono text-3xl font-black text-emerald-700 tracking-widest bg-white py-2 rounded-xl border border-emerald-200 shadow-xs">
                    ------
                </div>
                <p class="text-[11px] text-emerald-700 font-medium leading-tight">
                    Dicta este PIN al médico o comparte el enlace directo:
                </p>
                <button type="button" id="btn-copiar-enlace" onclick="copiarEnlaceMedico()" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-xs transition">
                    📋 Copiar Enlace Directo
                </button>
            </div>

            <div class="pt-2 flex gap-2">
                <button type="button" onclick="cerrarModalPinMedico()" class="flex-1 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition cursor-pointer">
                    Cerrar
                </button>
                <button type="submit" id="btn-submit-pin" class="flex-1 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-xs transition cursor-pointer">
                    Generar PIN
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Scripts de Interacción del Header, Drawer y Modales -->
<script>
    let enlaceMedicoGenerado = '';

    function toggleUserDropdownMenu() {
        const popover = document.getElementById('user-dropdown-popover');
        if (popover) {
            popover.classList.toggle('hidden');
        }
    }

    document.addEventListener('click', function(e) {
        const wrapper = document.getElementById('user-menu-wrapper');
        const popover = document.getElementById('user-dropdown-popover');
        if (wrapper && popover && !wrapper.contains(e.target)) {
            popover.classList.add('hidden');
        }
    });

    function abrirDrawerLateral() {
        const drawer = document.getElementById('drawer-lateral');
        const backdrop = document.getElementById('drawer-backdrop');
        const panel = document.getElementById('drawer-panel');
        if (!drawer || !backdrop || !panel) return;
        
        drawer.classList.remove('hidden');
        requestAnimationFrame(() => {
            backdrop.classList.remove('opacity-0');
            backdrop.classList.add('opacity-100');
            panel.classList.remove('translate-x-full');
            panel.classList.add('translate-x-0');
        });
        document.body.style.overflow = 'hidden';
    }

    function cerrarDrawerLateral() {
        const drawer = document.getElementById('drawer-lateral');
        const backdrop = document.getElementById('drawer-backdrop');
        const panel = document.getElementById('drawer-panel');
        if (!drawer || !backdrop || !panel) return;

        backdrop.classList.remove('opacity-100');
        backdrop.classList.add('opacity-0');
        panel.classList.remove('translate-x-0');
        panel.classList.add('translate-x-full');

        setTimeout(() => {
            drawer.classList.add('hidden');
            document.body.style.overflow = '';
        }, 300);
    }

    function abrirModalPinMedico() {
        const modal = document.getElementById('modal-pin-medico');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    }

    function cerrarModalPinMedico() {
        const modal = document.getElementById('modal-pin-medico');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    }

    async function ejecutarGeneracionPin(e) {
        e.preventDefault();
        const btn = document.getElementById('btn-submit-pin');
        btn.disabled = true;
        btn.innerText = 'Generando...';

        const formData = new FormData(document.getElementById('form-generar-pin'));
        try {
            const res = await fetch('<?php echo APP_URL; ?>/api/medico/generar-pin', {
                method: 'POST',
                body: formData,
                headers: { 'Accept': 'application/json' }
            });
            const data = await res.json();
            if (data.success && data.data) {
                document.getElementById('pin-display').innerText = data.data.pin;
                enlaceMedicoGenerado = data.data.enlace_directo;
                document.getElementById('resultado-pin').classList.remove('hidden');
                btn.innerText = 'Regenerar PIN';
            } else {
                alert(data.message || 'Error al generar PIN');
                btn.innerText = 'Generar PIN';
            }
        } catch (err) {
            alert('Ocurrió un error de conexión');
            btn.innerText = 'Generar PIN';
        } finally {
            btn.disabled = false;
        }
    }

    function copiarEnlaceMedico() {
        if (enlaceMedicoGenerado) {
            navigator.clipboard.writeText(enlaceMedicoGenerado).then(() => {
                const btn = document.getElementById('btn-copiar-enlace');
                btn.innerText = '✓ ¡Enlace Copiado al Portapapeles!';
                setTimeout(() => { btn.innerText = '📋 Copiar Enlace Directo'; }, 3000);
            });
        }
    }
</script>
<?php endif; ?>

<!-- Main Content Area -->
<main id="accessible-content" class="flex-1 w-full">
<?php require_once dirname(__DIR__) . '/components/toast.php'; ?>
