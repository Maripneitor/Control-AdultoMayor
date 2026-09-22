<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Establecer nueva contraseña - VitalSenior</title>
    
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
        <!-- Left Column: Branding -->
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
                    Gestión sencilla para ti y tu familia. Mantén tu cuenta y tu información médica segura.
                </p>
            </div>

            <div class="relative z-10 w-full rounded-2xl overflow-hidden shadow-lg border border-white/20 aspect-[16/9] mt-6">
                <img class="w-full h-full object-cover" alt="Seguridad y cuidado de la salud" src="https://images.unsplash.com/photo-1576765608535-5f04d1e3f289?q=80&w=900&auto=format&fit=crop"/>
                <div class="absolute inset-0 bg-gradient-to-t from-blue-950/70 to-transparent"></div>
            </div>
        </section>

        <!-- Right Column: Reset Form -->
        <section class="w-full md:w-1/2 p-8 sm:p-10 lg:p-12 flex flex-col justify-center bg-white">
            <div class="max-w-sm w-full mx-auto">
                <div class="mb-6 text-center md:text-left">
                    <h2 class="text-2xl font-extrabold text-slate-900 mb-2">Establecer nueva contraseña</h2>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Crea una contraseña segura para proteger tu cuenta y la información médica familiar.
                    </p>
                </div>

                <?php if (!empty($flash)): ?>
                    <div class="mb-4 p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-sm font-medium flex items-center gap-2.5">
                        <svg class="w-5 h-5 text-rose-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0118 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        <span><?php echo htmlspecialchars($flash['message']); ?></span>
                    </div>
                <?php endif; ?>

                <form class="space-y-4" method="POST" action="<?php echo APP_URL; ?>/new-password">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($token ?? ''); ?>">
                    
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5" for="new_password">Nueva Contraseña</label>
                        <div class="relative">
                            <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </div>
                            <input name="password" required class="form-input-styled pl-11 pr-11" id="new_password" placeholder="••••••••" type="password"/>
                            <button class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 focus:outline-none transition-colors" type="button" onclick="togglePasswordVisibility('new_password', this)" aria-label="Mostrar u ocultar contraseña">
                                <svg class="w-5 h-5 eye-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5" for="confirm_password">Confirmar Contraseña</label>
                        <div class="relative">
                            <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </div>
                            <input name="confirm_password" required class="form-input-styled pl-11 pr-11" id="confirm_password" placeholder="••••••••" type="password"/>
                            <button class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 focus:outline-none transition-colors" type="button" onclick="togglePasswordVisibility('confirm_password', this)" aria-label="Mostrar u ocultar contraseña">
                                <svg class="w-5 h-5 eye-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Security Requirements -->
                    <div class="bg-slate-50 rounded-xl p-3.5 border border-slate-200">
                        <p class="text-xs font-bold text-slate-700 mb-1.5">Requisitos:</p>
                        <ul class="space-y-1 text-xs text-slate-600">
                            <li class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-emerald-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                <span>Mínimo 8 caracteres</span>
                            </li>
                            <li class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-emerald-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                <span>Incluye letras y números o símbolos</span>
                            </li>
                        </ul>
                    </div>

                    <button class="w-full h-12 bg-[#004ac6] hover:bg-[#003ea8] text-white font-bold text-sm rounded-xl shadow-md shadow-blue-600/30 hover:shadow-lg hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2 mt-4" type="submit">
                        <span>Actualizar Contraseña</span>
                    </button>
                </form>

                <div class="mt-8 text-center">
                    <a class="inline-flex items-center gap-1.5 text-sm font-semibold text-[#004ac6] hover:underline group" href="<?php echo APP_URL; ?>/login">
                        <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        <span>Volver a iniciar sesión</span>
                    </a>
                </div>
            </div>
        </section>
    </main>

    <?php require_once dirname(__DIR__) . '/components/toast.php'; ?>

    <script>

        function togglePasswordVisibility(inputId, btn) {
            const input = document.getElementById(inputId);
            const icon = btn.querySelector('.eye-icon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />';
            } else {
                input.type = 'password';
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />';
            }
        }
    </script>
</body>
</html>