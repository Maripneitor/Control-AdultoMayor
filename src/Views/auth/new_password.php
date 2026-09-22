<!DOCTYPE html>
<html class="light h-full bg-slate-50" lang="es">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport"/>
    <title>VitalSenior - Establecer Nueva Contraseña</title>
    
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
        
        <!-- Columna Izquierda: Branding Showcase -->
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
                    Cuidamos de tu Salud y Privacidad
                </h2>
                <p class="text-xs sm:text-sm text-blue-100/90 leading-relaxed font-medium">
                    Crea una nueva contraseña robusta para mantener a salvo el historial clínico y los accesos familiares.
                </p>
            </div>

            <!-- Illustration Area -->
            <div class="relative z-10 w-full rounded-2xl overflow-hidden shadow-lg border border-white/20 aspect-[16/9] mt-6 group">
                <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" alt="Seguridad y cuidado de la salud" src="https://images.unsplash.com/photo-1576765608535-5f04d1e3f289?q=80&w=900&auto=format&fit=crop"/>
                <div class="absolute inset-0 bg-gradient-to-t from-blue-950/70 to-transparent"></div>
            </div>
        </section>

        <!-- Columna Derecha: Formulario Nueva Contraseña -->
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
                        Establecer Nueva Contraseña
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 leading-relaxed font-medium">
                        Crea una contraseña segura para proteger tu cuenta y la de tus familiares.
                    </p>
                </div>

                <?php if (!empty($flash)): ?>
                    <div class="mb-4 p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-rose-600 shrink-0" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                        </svg>
                        <span><?php echo htmlspecialchars($flash['message']); ?></span>
                    </div>
                <?php endif; ?>

                <form class="space-y-4" method="POST" action="<?php echo APP_URL; ?>/new-password">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($token ?? ''); ?>">
                    
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5" for="new_password">
                            Nueva Contraseña
                        </label>
                        <div class="relative">
                            <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                                <svg class="w-5 h-5 text-slate-400" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                                </svg>
                            </div>
                            <input 
                                name="password" 
                                required 
                                class="w-full h-12 pl-11 pr-11 text-xs sm:text-sm font-medium text-slate-900 bg-slate-50/70 hover:bg-slate-50 focus:bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl outline-none transition-all placeholder:text-slate-400" 
                                id="new_password" 
                                placeholder="Mínimo 8 caracteres" 
                                type="password"
                                autocomplete="new-password"
                            />
                            <button class="touch-min absolute right-0 top-0 bottom-0 px-3.5 text-slate-400 hover:text-slate-600 focus:outline-none transition-colors flex items-center justify-center cursor-pointer" type="button" onclick="togglePasswordVisibility('new_password', this)" aria-label="Mostrar u ocultar contraseña">
                                <svg class="w-5 h-5 eye-icon" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5" for="confirm_password">
                            Confirmar Contraseña
                        </label>
                        <div class="relative">
                            <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                                <svg class="w-5 h-5 text-slate-400" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                                </svg>
                            </div>
                            <input 
                                name="confirm_password" 
                                required 
                                class="w-full h-12 pl-11 pr-11 text-xs sm:text-sm font-medium text-slate-900 bg-slate-50/70 hover:bg-slate-50 focus:bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl outline-none transition-all placeholder:text-slate-400" 
                                id="confirm_password" 
                                placeholder="Repite tu contraseña" 
                                type="password"
                                autocomplete="new-password"
                            />
                            <button class="touch-min absolute right-0 top-0 bottom-0 px-3.5 text-slate-400 hover:text-slate-600 focus:outline-none transition-colors flex items-center justify-center cursor-pointer" type="button" onclick="togglePasswordVisibility('confirm_password', this)" aria-label="Mostrar u ocultar contraseña">
                                <svg class="w-5 h-5 eye-icon" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Security Requirements -->
                    <div class="bg-slate-50 rounded-2xl p-3.5 border border-slate-200">
                        <p class="text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Requisitos:</p>
                        <ul class="space-y-1 text-xs text-slate-600 font-medium">
                            <li class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-emerald-600 flex-shrink-0" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                                </svg>
                                <span>Mínimo 8 caracteres</span>
                            </li>
                            <li class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-emerald-600 flex-shrink-0" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                                </svg>
                                <span>Combina letras, números y símbolos</span>
                            </li>
                        </ul>
                    </div>

                    <button class="tap-bounce touch-min w-full h-12 bg-blue-600 hover:bg-blue-700 active:scale-[0.98] text-white font-extrabold text-xs sm:text-sm rounded-2xl shadow-md shadow-blue-600/25 transition-all duration-200 inline-flex items-center justify-center gap-2 cursor-pointer mt-2" type="submit">
                        <span>Actualizar Contraseña</span>
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
                        <span>Volver a iniciar sesión</span>
                    </a>
                </div>
            </div>
        </section>
    </main>

    <?php require_once dirname(__DIR__) . '/components/toast.php'; ?>

    <script>
        const SVG_EYE = `<svg class="w-5 h-5 eye-icon" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>`;
        const SVG_EYE_SLASH = `<svg class="w-5 h-5 eye-icon" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>`;

        function togglePasswordVisibility(inputId, btn) {
            const input = document.getElementById(inputId);
            if (input.type === 'password') {
                input.type = 'text';
                btn.innerHTML = SVG_EYE_SLASH;
            } else {
                input.type = 'password';
                btn.innerHTML = SVG_EYE;
            }
        }
    </script>
</body>
</html>