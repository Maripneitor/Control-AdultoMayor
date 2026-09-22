<!DOCTYPE html>
<html class="light h-full bg-slate-50" lang="es">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport"/>
    <title>VitalSenior - Recuperar Contraseña</title>
    
    <!-- Fuentes Google -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;600;700&display=swap" rel="stylesheet"/>
    
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
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
        }
        
        .tap-bounce {
            transition: transform 0.15s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.15s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .tap-bounce:active {
            transform: scale(0.98);
        }

        .touch-min {
            min-height: 48px;
            min-width: 48px;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen antialiased selection:bg-blue-600/20 selection:text-blue-700 flex items-center justify-center p-4 sm:p-6 lg:p-8">

    <main class="w-full max-w-[960px] bg-white rounded-3xl shadow-xl border border-slate-200/80 overflow-hidden flex flex-col md:flex-row min-h-[540px]">
        
        <!-- Columna Izquierda: Brand Showcase -->
        <section class="hidden md:flex md:w-1/2 bg-gradient-to-br from-blue-700 via-blue-600 to-indigo-800 text-white relative flex-col justify-between p-10 lg:p-12 overflow-hidden select-none">
            <div class="absolute -top-24 -left-24 w-80 h-80 bg-white/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute bottom-0 right-0 w-96 h-96 bg-indigo-900/40 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute inset-0 opacity-15 pointer-events-none" style="background-image: radial-gradient(circle at 20% 30%, rgba(255,255,255,0.5) 0%, transparent 60%);"></div>
            
            <div class="relative z-10">
                <a href="<?php echo APP_URL; ?>/" class="tap-bounce inline-flex items-center gap-3 mb-6 group">
                    <div class="w-10 h-10 rounded-2xl bg-white text-blue-600 flex items-center justify-center shadow-md shadow-blue-900/30 group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5 text-blue-600" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                        </svg>
                    </div>
                    <span class="text-2xl font-black text-white tracking-tight">Vital<span class="text-blue-200">Senior</span></span>
                </a>
                <h2 class="text-2xl font-extrabold text-white mb-3 leading-tight tracking-tight">
                    Recuperación Segura de Cuenta
                </h2>
                <p class="text-xs sm:text-sm text-blue-100/90 leading-relaxed font-medium">
                    Tu bienestar y privacidad son nuestra prioridad. Restablece el acceso a tus expedientes clínicos y recordatorios en sencillos pasos.
                </p>
            </div>

            <!-- Illustration Area -->
            <div class="relative z-10 w-full rounded-2xl overflow-hidden shadow-lg border border-white/20 aspect-[16/9] mt-6 group">
                <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" alt="Soporte y recuperación de cuenta médica" src="https://images.unsplash.com/photo-1576091160550-2173ff9e5eb3?q=80&w=800&auto=format&fit=crop"/>
                <div class="absolute inset-0 bg-gradient-to-t from-blue-950/70 to-transparent"></div>
            </div>
        </section>

        <!-- Columna Derecha: Formulario de Recuperación -->
        <section class="w-full md:w-1/2 p-6 sm:p-10 lg:p-12 flex flex-col justify-center bg-white">
            
            <!-- Mobile Logo -->
            <div class="md:hidden flex items-center gap-2 mb-6 justify-center">
                <div class="w-8 h-8 rounded-xl bg-blue-600 text-white flex items-center justify-center shadow-xs">
                    <svg class="w-4 h-4 text-white" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                    </svg>
                </div>
                <span class="text-xl font-black text-slate-900">Vital<span class="text-blue-600">Senior</span></span>
            </div>

            <div class="max-w-sm w-full mx-auto">
                <div class="mb-6 text-center md:text-left">
                    <h1 class="text-xl sm:text-2xl font-black text-slate-900 mb-1.5 tracking-tight">
                        ¿Olvidaste tu contraseña?
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 leading-relaxed font-medium">
                        Ingresa tu correo registrado y te enviaremos un enlace seguro para restablecerla.
                    </p>
                </div>

                <form action="<?php echo APP_URL; ?>/recovery" class="space-y-4" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                    
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5" for="email">
                            Correo Electrónico
                        </label>
                        <div class="relative">
                            <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                                <svg class="w-5 h-5 text-slate-400" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
                                </svg>
                            </div>
                            <input 
                                class="w-full h-12 pl-11 pr-4 text-xs sm:text-sm font-medium text-slate-900 bg-slate-50/70 hover:bg-slate-50 focus:bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl outline-none transition-all placeholder:text-slate-400" 
                                id="email" 
                                name="email" 
                                placeholder="ejemplo@correo.com" 
                                required 
                                type="email"
                                autocomplete="email"
                            />
                        </div>
                    </div>

                    <button class="tap-bounce touch-min w-full h-12 bg-blue-600 hover:bg-blue-700 active:scale-[0.98] text-white font-extrabold text-xs sm:text-sm rounded-2xl shadow-md shadow-blue-600/25 transition-all duration-200 inline-flex items-center justify-center gap-2 cursor-pointer mt-2" type="submit">
                        <span>Enviar Instrucciones</span>
                        <svg class="w-4 h-4" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                        </svg>
                    </button>
                </form>

                <div class="mt-8 text-center">
                    <a class="tap-bounce inline-flex items-center gap-1.5 text-xs font-bold text-blue-600 hover:text-blue-700 hover:underline group" href="<?php echo APP_URL; ?>/login">
                        <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
                        </svg>
                        <span>Volver al inicio de sesión</span>
                    </a>
                </div>
            </div>
        </section>
    </main>
    
    <?php require_once dirname(__DIR__) . '/components/toast.php'; ?>
</body>
</html>