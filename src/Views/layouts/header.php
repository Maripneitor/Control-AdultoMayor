<!DOCTYPE html>
<html class="light scroll-smooth" lang="es">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>VitalSenior - El cuidado y la salud de quienes más quieres</title>
    <!-- Tailwind CSS desde CDN -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script src="<?php echo APP_URL; ?>/assets/js/tailwind-config.js"></script>
    <script src="<?php echo APP_URL; ?>/assets/js/accessibility.js"></script>
    
    <!-- Fuentes Google -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 1, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        
        /* Animaciones para scroll (Fade In / Slide Up) */
        .animate-on-scroll {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.6s ease-out, transform 0.6s ease-out;
            will-change: opacity, transform;
        }
        .animate-on-scroll.is-visible {
            opacity: 1;
            transform: translateY(0);
        }
        
        /* Soporte Alto Contraste con Variables CSS */
        :root {
            --bg-body: #f8fafc;
            --text-body: #0f172a;
            --bg-card: #ffffff;
            --border-card: #e2e8f0;
            --btn-border: transparent;
        }

        html.high-contrast {
            filter: contrast(125%);
            --bg-body: #000000;
            --text-body: #ffffff;
            --bg-card: #111111;
            --border-card: #ffff00;
            --btn-border: #ffff00;
        }
        
        body {
            background-color: var(--bg-body);
            color: var(--text-body);
        }
        .bg-white {
            background-color: var(--bg-card);
            border-color: var(--border-card);
            color: var(--text-body);
        }
        button, a.btn {
            border: 2px solid var(--btn-border);
        }
    </style>
</head>
<body class="font-body-md selection:bg-primary-container selection:text-on-primary-container transition-opacity duration-500 text-base">

<!-- 1. TopNavBar -->
<nav class="bg-surface-container-lowest/80 backdrop-blur-xl dark:bg-inverse-surface/80 docked full-width top-0 sticky shadow-sm z-50">
    <div class="flex justify-between items-center w-full px-margin-mobile md:px-gutter max-w-container-max mx-auto h-20">
        <!-- Brand Logo -->
        <a class="text-headline-md font-headline-md font-bold text-primary dark:text-inverse-primary flex items-center gap-2" href="<?php echo APP_URL; ?>/">
            VitalSenior
        </a>
        <!-- Navigation Links -->
        <div class="hidden md:flex gap-gutter items-center Labels: font-label-md text-label-md. Body: font-body-md text-body-md">
            <a class="text-on-surface-variant hover:text-primary transition-colors hover:opacity-90 transition-opacity" href="<?php echo APP_URL; ?>/#modulos">Módulos</a>
            <a class="text-on-surface-variant hover:text-primary transition-colors hover:opacity-90 transition-opacity" href="<?php echo APP_URL; ?>/#emergencia">Ficha de Emergencia</a>
            <a class="text-on-surface-variant hover:text-primary transition-colors hover:opacity-90 transition-opacity" href="<?php echo APP_URL; ?>/#como-funciona">Cómo Funciona</a>
            <a class="text-on-surface-variant hover:text-primary transition-colors hover:opacity-90 transition-opacity" href="<?php echo APP_URL; ?>/#testimonios">Testimonios</a>
            
            <!-- Controles de Accesibilidad -->
            <div class="flex items-center gap-1 border-l border-slate-300 pl-4 ml-2">
                <button onclick="Accessibility.changeFontSize(-1)" class="p-1 rounded-md hover:bg-slate-200 text-sm font-bold" aria-label="Reducir texto">A-</button>
                <button onclick="Accessibility.changeFontSize(1)" class="p-1 rounded-md hover:bg-slate-200 text-lg font-bold" aria-label="Aumentar texto">A+</button>
                <button onclick="Accessibility.toggleHighContrast()" class="p-1 rounded-md hover:bg-slate-200" title="Alto Contraste">
                    <span class="material-symbols-outlined text-xl align-middle">contrast</span>
                </button>
            </div>
        </div>
        <!-- Trailing Primary Action -->
        <a href="<?php echo APP_URL; ?>/login" class="bg-primary hover:opacity-90 transition-opacity text-on-primary font-label-md text-label-md rounded-full px-6 h-[56px] flex items-center justify-center shadow-md hover:shadow-none hover:translate-y-px transition-all cursor-pointer">
            Crear Expediente Gratis
        </a>
    </div>
</nav>

<!-- Main Content Area -->
<main id="accessible-content">

<?php if (!empty($flash)): ?>
    <?php 
        $colors = [
            'success' => 'bg-emerald-100 border-emerald-500 text-emerald-800',
            'warning' => 'bg-amber-100 border-amber-500 text-amber-800',
            'danger'  => 'bg-rose-100 border-rose-500 text-rose-800',
            'info'    => 'bg-blue-100 border-blue-500 text-blue-800'
        ];
        $alertStyle = $colors[$flash['type']] ?? $colors['info'];
    ?>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
        <div class="border-l-4 p-4 rounded-r-lg shadow-sm flex items-center justify-between <?= $alertStyle; ?>" role="alert">
            <span class="text-base font-medium"><?= htmlspecialchars($flash['message']); ?></span>
            <button onclick="this.parentElement.remove()" class="text-lg font-bold ml-4 p-1 hover:opacity-75 focus:outline-none">&times;</button>
        </div>
    </div>
<?php endif; ?>
