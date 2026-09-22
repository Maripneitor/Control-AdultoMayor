<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>VitalSenior - Contraseña Actualizada</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
    
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script src="<?php echo APP_URL; ?>/assets/js/tailwind-config.js"></script>
    
    <style>
        body {
            font-family: 'Outfit', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: #f1f5f9;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 sm:p-6 antialiased text-slate-900">
    <main class="w-full max-w-[960px] bg-white rounded-3xl shadow-xl border border-slate-200/80 overflow-hidden flex flex-col md:flex-row min-h-[540px]">
        <!-- Left Column: Branding Showcase -->
        <section class="hidden md:flex md:w-1/2 bg-[#1e3a8a] text-white relative flex-col justify-between p-10 lg:p-12 overflow-hidden">
            <div class="absolute inset-0 opacity-20 pointer-events-none" style="background-image: radial-gradient(circle at 20% 30%, rgba(255,255,255,0.4) 0%, transparent 60%);"></div>
            
            <div class="relative z-10">
                <a href="<?php echo APP_URL; ?>/" class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-white text-[#1e3a8a] flex items-center justify-center shadow-md">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    </div>
                    <span class="text-2xl font-extrabold text-white">VitalSenior</span>
                </a>
                <h2 class="text-2xl font-bold text-white mb-3 leading-tight">
                    Cuidamos de tu salud.
                </h2>
                <p class="text-sm text-blue-100/90 leading-relaxed">
                    Tu cuenta y tus datos familiares están totalmente protegidos.
                </p>
            </div>

            <div class="relative z-10 w-full rounded-2xl overflow-hidden shadow-lg border border-white/20 aspect-[16/9] mt-6">
                <img class="w-full h-full object-cover" alt="Cuenta médica protegida" src="https://images.unsplash.com/photo-1576765608535-5f04d1e3f289?q=80&w=900&auto=format&fit=crop"/>
                <div class="absolute inset-0 bg-gradient-to-t from-blue-950/70 to-transparent"></div>
            </div>
        </section>

        <!-- Right Column: Success Message -->
        <section class="w-full md:w-1/2 p-8 sm:p-10 lg:p-12 flex flex-col justify-center text-center md:text-left bg-white">
            <div class="max-w-sm w-full mx-auto">
                <div class="w-16 h-16 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto md:mx-0 mb-6 shadow-sm">
                    <svg class="w-9 h-9" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                    </svg>
                </div>

                <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 mb-3 tracking-tight">
                    ¡Contraseña actualizada con éxito!
                </h1>

                <p class="text-sm text-slate-600 leading-relaxed mb-8">
                    Tu cuenta de VitalSenior ahora está protegida con tu nueva clave. Ya puedes acceder a tu expediente y gestionar tus cuidados con total tranquilidad.
                </p>

                <a href="<?php echo APP_URL; ?>/login" class="w-full h-12 bg-[#004ac6] hover:bg-[#003ea8] text-white font-bold text-sm rounded-xl shadow-md shadow-blue-600/30 hover:shadow-lg hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2">
                    <span>Ir al Inicio de Sesión</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>

                <p class="text-xs text-slate-400 mt-6 text-center md:text-left leading-relaxed">
                    Si no realizaste este cambio, por favor <a class="text-[#004ac6] font-semibold hover:underline" href="#">contacta a nuestro equipo de soporte</a> de inmediato.
                </p>
            </div>
        </section>
    </main>
</body>
</html>