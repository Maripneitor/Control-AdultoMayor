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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
    
    <style>
        body {
            font-family: 'Outfit', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: var(--bg-body, #f8fafc);
            color: var(--text-body, #0f172a);
        }
        
        /* Animaciones para scroll (Fade In / Slide Up) */
        .animate-on-scroll {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.6s cubic-bezier(0.16, 1, 0.3, 1), transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
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
<body class="selection:bg-primary/20 selection:text-primary transition-opacity duration-500 text-base antialiased">

<!-- 1. TopNavBar -->
<?php 
$isLoggedIn = !empty($_SESSION['user_id']);
$userRole = $_SESSION['user_role'] ?? '';
?>
<nav class="bg-white/90 backdrop-blur-md sticky top-0 z-50 border-b border-slate-200/80 shadow-sm transition-all">
    <div class="flex justify-between items-center w-full px-4 md:px-8 max-w-7xl mx-auto h-20">
        <!-- Brand Logo -->
        <a class="flex items-center gap-2.5 text-2xl font-bold text-[#004ac6] tracking-tight group" href="<?php echo APP_URL; ?>/<?= $isLoggedIn ? 'dashboard' : '' ?>">
            <div class="w-10 h-10 rounded-xl bg-[#004ac6] flex items-center justify-center text-white shadow-md shadow-blue-500/20 group-hover:scale-105 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
            </div>
            <span>Vital<span class="text-slate-800">Senior</span></span>
        </a>

        <!-- Navigation Links -->
        <div class="hidden md:flex items-center gap-8 text-[15px] font-semibold text-slate-600">
            <?php if (!$isLoggedIn): ?>
                <a class="hover:text-[#004ac6] transition-colors" href="<?php echo APP_URL; ?>/#modulos">Módulos</a>
                <a class="hover:text-[#004ac6] transition-colors" href="<?php echo APP_URL; ?>/#emergencia">Ficha de Emergencia</a>
                <a class="hover:text-[#004ac6] transition-colors" href="<?php echo APP_URL; ?>/#como-funciona">Cómo Funciona</a>
                <a class="hover:text-[#004ac6] transition-colors" href="<?php echo APP_URL; ?>/#testimonios">Testimonios</a>
            <?php else: ?>
                <a class="hover:text-[#004ac6] transition-colors font-bold text-[#004ac6]" href="<?php echo APP_URL; ?>/dashboard">Panel de Control</a>
                <?php if (in_array($userRole, ['cuidador_principal', 'medico', 'admin'])): ?>
                    <a class="hover:text-[#004ac6] transition-colors" href="<?php echo APP_URL; ?>/medicamentos">Medicamentos</a>
                    <a class="hover:text-[#004ac6] transition-colors" href="<?php echo APP_URL; ?>/citas">Citas</a>
                <?php endif; ?>
                <a class="hover:text-[#004ac6] transition-colors" href="<?php echo APP_URL; ?>/perfil">Mi Perfil</a>
            <?php endif; ?>
            
            <!-- Controles de Accesibilidad -->
            <div class="flex items-center gap-1.5 border-l border-slate-200 pl-4 py-1">
                <button onclick="Accessibility.changeFontSize(-1)" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs flex items-center justify-center transition-colors" title="Disminuir tamaño de texto" aria-label="Disminuir tamaño de texto">A-</button>
                <button onclick="Accessibility.changeFontSize(1)" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-sm flex items-center justify-center transition-colors" title="Aumentar tamaño de texto" aria-label="Aumentar tamaño de texto">A+</button>
                <button onclick="Accessibility.toggleHighContrast()" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center transition-colors" title="Alternar alto contraste" aria-label="Alternar alto contraste">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                        <path d="M10 2a8 8 0 100 16 8 8 0 000-16zm0 14V4a6 6 0 010 12z" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Trailing Primary Action -->
        <div class="flex items-center gap-3">
            <?php if (!$isLoggedIn): ?>
                <a href="<?php echo APP_URL; ?>/login" class="bg-[#004ac6] hover:bg-[#003ea8] text-white font-semibold text-sm rounded-full px-6 py-3.5 shadow-md shadow-blue-600/20 hover:shadow-lg hover:shadow-blue-600/30 hover:-translate-y-0.5 transition-all flex items-center justify-center">
                    Crear Expediente Gratis
                </a>
            <?php else: ?>
                <a href="<?php echo APP_URL; ?>/logout" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm rounded-full px-6 py-3.5 transition-all flex items-center justify-center">
                    Cerrar Sesión
                </a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<!-- Main Content Area -->
<main id="accessible-content">

<?php require_once dirname(__DIR__) . '/components/toast.php'; ?>


