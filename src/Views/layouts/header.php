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
        .touch-target {
            min-height: 48px;
            min-width: 48px;
        }

        /* Safe area para dispositivos móviles */
        .safe-area-pb {
            padding-bottom: env(safe-area-inset-bottom, 0.5rem);
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 min-h-full flex flex-col justify-between selection:bg-blue-600/20 selection:text-blue-700 antialiased">

<!-- 1. TopNavBar Adaptativo -->
<?php 
$isLoggedIn = !empty($_SESSION['user_id']);
$userRole = $_SESSION['user_role'] ?? '';
?>
<nav class="bg-white/90 backdrop-blur-md sticky top-0 z-40 border-b border-slate-200/80 shadow-xs transition-all">
    <div class="flex justify-between items-center w-full px-4 sm:px-6 md:px-8 max-w-7xl mx-auto h-16 sm:h-20">
        <!-- Brand Logo -->
        <a class="tap-bounce flex items-center gap-2.5 text-xl sm:text-2xl font-black text-blue-600 tracking-tight group" href="<?php echo APP_URL; ?>/<?= $isLoggedIn ? 'dashboard' : '' ?>">
            <div class="w-10 h-10 rounded-2xl bg-blue-600 flex items-center justify-center text-white shadow-md shadow-blue-500/20 group-hover:scale-105 transition-transform text-xl font-black">
                ✚
            </div>
            <span>Vital<span class="text-slate-800">Senior</span></span>
        </a>

        <!-- Desktop Navigation Links (md+) -->
        <div class="hidden md:flex items-center gap-6 lg:gap-8 text-sm font-bold text-slate-600">
            <?php if (!$isLoggedIn): ?>
                <a class="hover:text-blue-600 transition-colors py-2 px-1" href="<?php echo APP_URL; ?>/#modulos">Módulos</a>
                <a class="hover:text-blue-600 transition-colors py-2 px-1" href="<?php echo APP_URL; ?>/#emergencia">Ficha de Emergencia</a>
                <a class="hover:text-blue-600 transition-colors py-2 px-1" href="<?php echo APP_URL; ?>/#como-funciona">Cómo Funciona</a>
                <a class="hover:text-blue-600 transition-colors py-2 px-1" href="<?php echo APP_URL; ?>/#testimonios">Testimonios</a>
            <?php else: ?>
                <a class="hover:text-blue-600 transition-colors py-2 px-1 text-slate-800 font-extrabold" href="<?php echo APP_URL; ?>/dashboard">Dashboard</a>
                <a class="hover:text-blue-600 transition-colors py-2 px-1 text-slate-600 font-bold" href="<?php echo APP_URL; ?>/medicamentos">Medicamentos</a>
                <a class="hover:text-blue-600 transition-colors py-2 px-1 text-slate-600 font-bold" href="<?php echo APP_URL; ?>/expediente">Expediente</a>
                <a class="hover:text-blue-600 transition-colors py-2 px-1 text-slate-600 font-bold" href="<?php echo APP_URL; ?>/citas">Citas</a>
                <a class="hover:text-blue-600 transition-colors py-2 px-1 text-slate-600 font-bold" href="<?php echo APP_URL; ?>/perfil">Mi Perfil</a>
            <?php endif; ?>
            
            <!-- Controles de Accesibilidad -->
            <div class="flex items-center gap-1.5 border-l border-slate-200 pl-4 py-1">
                <button onclick="Accessibility?.changeFontSize(-1)" class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs flex items-center justify-center transition-colors cursor-pointer" title="Disminuir tamaño" aria-label="Disminuir tamaño">A-</button>
                <button onclick="Accessibility?.changeFontSize(1)" class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-sm flex items-center justify-center transition-colors cursor-pointer" title="Aumentar tamaño" aria-label="Aumentar tamaño">A+</button>
            </div>
        </div>

        <!-- Acciones a la Derecha (Login / Logout / Registro) -->
        <div class="flex items-center gap-2 sm:gap-3">
            <?php if (!$isLoggedIn): ?>
                <a href="<?php echo APP_URL; ?>/login" class="tap-bounce touch-target bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs sm:text-sm rounded-2xl px-5 py-3 shadow-md shadow-blue-600/20 transition-all flex items-center justify-center">
                    Iniciar Sesión
                </a>
            <?php else: ?>
                <a href="<?php echo APP_URL; ?>/logout" class="tap-bounce touch-target bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs sm:text-sm rounded-2xl px-4 py-2.5 transition-all flex items-center justify-center border border-slate-200">
                    Cerrar Sesión
                </a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<!-- Main Content Area -->
<main id="accessible-content" class="flex-1 w-full">
<?php require_once dirname(__DIR__) . '/components/toast.php'; ?>


