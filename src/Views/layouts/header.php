<!DOCTYPE html>
<html class="light scroll-smooth" lang="es">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>VitalSenior - El cuidado y la salud de quienes más quieres</title>
    <!-- Tailwind CSS desde CDN -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script src="<?php echo APP_URL; ?>/assets/js/tailwind-config.js"></script>
    
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
    </style>
</head>
<body class="bg-background text-on-background font-body-md selection:bg-primary-container selection:text-on-primary-container transition-opacity duration-500" id="body-content">

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
        </div>
        <!-- Trailing Primary Action -->
        <a href="<?php echo APP_URL; ?>/login" class="bg-primary hover:opacity-90 transition-opacity text-on-primary font-label-md text-label-md rounded-full px-6 h-[56px] flex items-center justify-center shadow-md hover:shadow-none hover:translate-y-px transition-all cursor-pointer">
            Crear Expediente Gratis
        </a>
    </div>
</nav>

<!-- Main Content Area -->
<main>
