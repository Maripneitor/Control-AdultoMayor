<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>VitalSenior - Recuperar Contraseña</title>
    
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
        .form-input-styled {
            width: 100%;
            height: 48px;
            padding: 10px 16px;
            font-size: 15px;
            color: #0f172a;
            background-color: #f1f5f9;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            transition: all 0.2s ease;
        }
        .form-input-styled:focus {
            background-color: #ffffff;
            border-color: #004ac6;
            outline: none;
            box-shadow: 0 0 0 3px rgba(0, 74, 198, 0.12);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 sm:p-6 antialiased text-slate-900">
    <main class="w-full max-w-[960px] bg-white rounded-3xl shadow-xl border border-slate-200/80 overflow-hidden flex flex-col md:flex-row min-h-[540px]">
        <!-- Left Column: Brand Showcase -->
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
                    Cuidamos de tu salud. Gestión sencilla para ti y tu familia.
                </h2>
                <p class="text-sm text-blue-100/90 leading-relaxed">
                    Tu bienestar y tranquilidad son nuestra prioridad. Recupera el acceso a tu expediente médico de forma segura en unos sencillos pasos.
                </p>
            </div>

            <!-- Illustration Area -->
            <div class="relative z-10 w-full rounded-2xl overflow-hidden shadow-lg border border-white/20 aspect-[16/9] mt-6">
                <img class="w-full h-full object-cover" alt="Soporte y recuperación de cuenta" src="https://images.unsplash.com/photo-1576091160550-2173ff9e5eb3?q=80&w=800&auto=format&fit=crop"/>
                <div class="absolute inset-0 bg-gradient-to-t from-blue-950/70 to-transparent"></div>
            </div>
        </section>

        <!-- Right Column: Recovery Form -->
        <section class="w-full md:w-1/2 p-8 sm:p-10 lg:p-12 flex flex-col justify-center bg-white">
            <!-- Mobile Logo -->
            <div class="md:hidden flex items-center gap-2.5 mb-6 justify-center">
                <div class="w-9 h-9 rounded-xl bg-[#004ac6] flex items-center justify-center text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                </div>
                <span class="text-2xl font-bold text-[#004ac6]">VitalSenior</span>
            </div>

            <div class="max-w-sm w-full mx-auto">
                <div class="mb-6 text-center md:text-left">
                    <h2 class="text-2xl font-extrabold text-slate-900 mb-2">¿Olvidaste tu contraseña?</h2>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Ingresa tu correo electrónico registrado y te enviaremos un enlace seguro para restablecerla.
                    </p>
                </div>

                <form action="<?php echo APP_URL; ?>/recovery" class="space-y-4" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                    
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5" for="email">Correo Electrónico</label>
                        <div class="relative">
                            <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </div>
                            <input class="form-input-styled pl-11" id="email" name="email" placeholder="ejemplo@correo.com" required type="email"/>
                        </div>
                    </div>

                    <button class="w-full h-12 bg-[#004ac6] hover:bg-[#003ea8] text-white font-bold text-sm rounded-xl shadow-md shadow-blue-600/30 hover:shadow-lg hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2 mt-4" type="submit">
                        <span>Enviar Instrucciones</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </form>

                <div class="mt-8 text-center">
                    <a class="inline-flex items-center gap-1.5 text-sm font-semibold text-[#004ac6] hover:underline group" href="<?php echo APP_URL; ?>/login">
                        <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        <span>Volver al inicio de sesión</span>
                    </a>
                </div>
            </div>
        </section>
    </main>
    <?php require_once dirname(__DIR__) . '/components/toast.php'; ?>
</body>
</html>