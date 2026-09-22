<!DOCTYPE html>
<html class="light" lang="es">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>VitalSenior - Iniciar Sesión / Registro</title>
    
    <!-- Fuentes Google -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
    
    <!-- Tailwind CSS desde CDN -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script src="<?php echo APP_URL; ?>/assets/js/tailwind-config.js"></script>
    
    <style>
        body {
            font-family: 'Outfit', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: #f8fafc;
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
        .form-label-styled {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 6px;
        }
        .btn-primary {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 50px;
            font-size: 15px;
            font-weight: 700;
            background-color: #004ac6;
            color: #ffffff;
            border-radius: 12px;
            transition: all 0.2s ease;
            box-shadow: 0 4px 14px rgba(0, 74, 198, 0.35);
        }
        .btn-primary:hover {
            background-color: #003ea8;
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(0, 74, 198, 0.3);
        }
        .btn-primary:active {
            transform: translateY(1px);
            box-shadow: none;
        }
        .tab-btn {
            flex: 1;
            padding: 12px 16px;
            font-size: 15px;
            font-weight: 600;
            text-align: center;
            color: #64748b;
            border-bottom: 2px solid transparent;
            transition: all 0.2s ease;
            cursor: pointer;
        }
        .tab-btn.active {
            color: #004ac6;
            border-color: #004ac6;
            background-color: rgba(0, 74, 198, 0.04);
            font-weight: 700;
        }
    </style>
</head>
<body id="login-body" class="bg-slate-50 text-slate-900 min-h-screen flex flex-col md:flex-row antialiased">

<!-- Left Column (Showcase / Blue Panel) -->
<div class="hidden md:flex flex-col justify-between w-1/2 bg-[#1e3a8a] text-white p-10 lg:p-14 relative overflow-hidden">
    <!-- Decorative Glow Effects -->
    <div class="absolute inset-0 opacity-20 pointer-events-none" style="background-image: radial-gradient(circle at 20% 30%, rgba(255,255,255,0.4) 0%, transparent 60%);"></div>
    <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-blue-400/20 rounded-full blur-3xl pointer-events-none"></div>

    <!-- Header / Brand -->
    <div class="relative z-10">
        <a href="<?php echo APP_URL; ?>/" class="flex items-center gap-3 group mb-4">
            <div class="w-11 h-11 rounded-xl bg-white text-[#1e3a8a] flex items-center justify-center shadow-md shadow-blue-900/30 group-hover:scale-105 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
            </div>
            <span class="text-3xl font-extrabold tracking-tight text-white">VitalSenior</span>
        </a>
        <p class="text-xl text-blue-100/90 font-medium max-w-md leading-relaxed mt-3">
            Cuidamos de tu salud. Gestión sencilla para ti y tu familia.
        </p>
    </div>

    <!-- Center High-Quality Eldercare Imagery -->
    <div class="relative z-10 flex-grow flex items-center justify-center py-8">
        <div class="w-full max-w-md relative">
            <div class="relative rounded-3xl overflow-hidden shadow-2xl border-2 border-white/20 bg-blue-900/50 backdrop-blur-sm aspect-[4/3]">
                <img 
                    alt="Atención médica y cuidado familiar de adultos mayores" 
                    class="object-cover w-full h-full" 
                    src="https://images.unsplash.com/photo-1576765608535-5f04d1e3f289?q=80&w=900&auto=format&fit=crop"
                />
                <div class="absolute inset-0 bg-gradient-to-t from-blue-950/80 via-transparent to-transparent"></div>
                
                <!-- Overlay Floating Badge -->
                <div class="absolute bottom-4 left-4 right-4 bg-white/90 backdrop-blur-md rounded-2xl p-3.5 border border-white/40 text-slate-800 shadow-lg flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-[#004ac6] text-white flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-900">Historial Médico Protegido</p>
                        <p class="text-[11px] text-slate-500">Acceso seguro para familiares y cuidadores</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Highlights -->
    <div class="relative z-10 pt-4 border-t border-blue-400/20 text-xs text-blue-200 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            <span>Recordatorios automáticos</span>
        </div>
        <div class="flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            <span>Código QR de emergencias</span>
        </div>
    </div>
</div>

<!-- Right Column (Forms) -->
<div class="w-full md:w-1/2 bg-white flex flex-col justify-center px-6 py-10 sm:px-12 md:px-12 lg:px-16 min-h-screen relative shadow-[-10px_0_30px_rgba(0,0,0,0.03)] z-20">
    
    <div class="max-w-[420px] w-full mx-auto">
        
        <!-- Back to Home Link -->
        <a href="<?php echo APP_URL; ?>/" class="inline-flex items-center gap-1.5 text-sm font-semibold text-slate-500 hover:text-[#004ac6] transition-colors mb-6 group">
            <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span>Volver al inicio</span>
        </a>

        <!-- Mobile Brand Header (Visible only on small screens) -->
        <div class="md:hidden flex items-center gap-2.5 mb-6 justify-center">
            <div class="w-9 h-9 rounded-xl bg-[#004ac6] flex items-center justify-center text-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
            </div>
            <span class="text-2xl font-bold text-[#004ac6]">VitalSenior</span>
        </div>

        <!-- Title & Subtitle -->
        <div class="mb-6 text-center md:text-left">
            <h2 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight mb-2">¡Bienvenido a VitalSenior!</h2>
            <p class="text-sm md:text-base text-slate-600 leading-relaxed">Accede a tu historial y gestiona tus cuidados o crea tu cuenta familiar.</p>
        </div>

        <?php 
        $activeTab = $tab ?? ($_GET['tab'] ?? (str_contains($_SERVER['REQUEST_URI'] ?? '', 'register') ? 'register' : 'login'));
        ?>
        <!-- Navigation Tabs -->
        <div class="flex mb-6 border-b border-slate-200" role="tablist">
            <button class="tab-btn <?= $activeTab === 'login' ? 'active' : '' ?>" id="tab-login" onclick="switchTab('login')" type="button" role="tab" aria-selected="<?= $activeTab === 'login' ? 'true' : 'false' ?>">Iniciar Sesión</button>
            <button class="tab-btn <?= $activeTab === 'register' ? 'active' : '' ?>" id="tab-register" onclick="switchTab('register')" type="button" role="tab" aria-selected="<?= $activeTab === 'register' ? 'true' : 'false' ?>">Regístrate</button>
        </div>

        <!-- Inline Flash & Errors Card -->
        <?php 
        $notification = $flash ?? (!empty($error) ? ['type' => 'danger', 'message' => $error] : null);
        if ($notification && !empty($notification['message'])): 
            $nType = $notification['type'] ?? 'info';
            $bgColors = [
                'danger'  => 'bg-rose-50 border-rose-200 text-rose-800',
                'error'   => 'bg-rose-50 border-rose-200 text-rose-800',
                'warning' => 'bg-amber-50 border-amber-200 text-amber-800',
                'success' => 'bg-emerald-50 border-emerald-200 text-emerald-800',
                'info'    => 'bg-blue-50 border-blue-200 text-blue-800'
            ];
            $iconColors = [
                'danger'  => 'text-rose-500',
                'error'   => 'text-rose-500',
                'warning' => 'text-amber-500',
                'success' => 'text-emerald-500',
                'info'    => 'text-blue-500'
            ];
            $cardClass = $bgColors[$nType] ?? $bgColors['info'];
            $iconClass = $iconColors[$nType] ?? $iconColors['info'];
        ?>
            <div id="inline-flash-alert" class="mb-5 p-4 rounded-2xl border <?= $cardClass; ?> shadow-sm flex items-start gap-3 animate-fade-in transition-all">
                <div class="flex-shrink-0 mt-0.5">
                    <?php if (in_array($nType, ['danger', 'error'])): ?>
                        <svg class="w-5 h-5 <?= $iconClass; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <?php elseif ($nType === 'success'): ?>
                        <svg class="w-5 h-5 <?= $iconClass; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <?php elseif ($nType === 'warning'): ?>
                        <svg class="w-5 h-5 <?= $iconClass; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <?php else: ?>
                        <svg class="w-5 h-5 <?= $iconClass; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <?php endif; ?>
                </div>
                <div class="flex-1 text-sm font-medium leading-relaxed">
                    <?php if (!empty($notification['title'])): ?>
                        <p class="font-bold text-xs uppercase tracking-wider mb-0.5 opacity-90"><?= htmlspecialchars($notification['title']); ?></p>
                    <?php endif; ?>
                    <p><?= htmlspecialchars($notification['message']); ?></p>
                </div>
                <button type="button" onclick="document.getElementById('inline-flash-alert').remove()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-black/5 transition" aria-label="Cerrar aviso">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        <?php endif; ?>

        <!-- 1. LOGIN FORM -->
        <form class="space-y-4 <?= $activeTab === 'login' ? 'block' : 'hidden' ?>" id="form-login" method="POST" action="<?php echo APP_URL; ?>/login">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
            
            <!-- Email / Phone Field -->
            <div>
                <label class="form-label-styled" for="login-email">Correo Electrónico</label>
                <div class="relative">
                    <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                        </svg>
                    </div>
                    <input class="form-input-styled pl-11" id="login-email" name="email" placeholder="ejemplo@correo.com" type="email" required />
                </div>
            </div>


            <!-- Password Field -->
            <div>
                <label class="form-label-styled" for="login-password">Contraseña</label>
                <div class="relative">
                    <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <input class="form-input-styled pl-11 pr-11" id="login-password" name="password" placeholder="••••••••" type="password" required />
                    <button class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 focus:outline-none transition-colors" type="button" onclick="togglePasswordVisibility('login-password', this)" aria-label="Mostrar u ocultar contraseña">
                        <!-- Closed / Show Eye SVG Default -->
                        <svg class="w-5 h-5 eye-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Options: Remember Me & Forgot Password -->
            <div class="flex items-center justify-between pt-1">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input class="w-4 h-4 rounded border-slate-300 text-[#004ac6] focus:ring-[#004ac6]" type="checkbox"/>
                    <span class="text-sm font-medium text-slate-600">Recordarme</span>
                </label>
                <a class="text-sm font-semibold text-[#004ac6] hover:underline" href="<?php echo APP_URL; ?>/recovery">¿Olvidaste tu contraseña?</a>
            </div>

            <!-- Submit Button -->
            <button class="btn-primary mt-2" type="submit">Iniciar sesión</button>
        </form>

        <!-- 2. REGISTER FORM -->
        <form class="space-y-4 <?= $activeTab === 'register' ? 'block' : 'hidden' ?>" id="form-register" method="POST" action="<?php echo APP_URL; ?>/register">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
            
            <div>
                <label class="form-label-styled" for="reg-name">Nombre Completo</label>
                <div class="relative">
                    <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <input class="form-input-styled pl-11" id="reg-name" name="nombre" placeholder="Juan Pérez" type="text" required/>
                </div>
            </div>

            <div>
                <label class="form-label-styled" for="reg-rol">Tipo de Cuenta</label>
                <select class="form-input-styled" id="reg-rol" name="rol" required>
                    <option value="" disabled selected>Selecciona una opción</option>
                    <option value="adulto_mayor">Adulto Mayor (Para mí)</option>
                    <option value="familiar">Familiar / Cuidador Principal</option>
                </select>
            </div>

            <div>
                <label class="form-label-styled" for="reg-phone">Teléfono (WhatsApp)</label>
                <div class="relative">
                    <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    </div>
                    <input class="form-input-styled pl-11" id="reg-phone" name="telefono" placeholder="+34 600 000 000" type="tel"/>
                </div>
            </div>

            <div>
                <label class="form-label-styled" for="reg-email">Correo Electrónico</label>
                <div class="relative">
                    <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <input class="form-input-styled pl-11" id="reg-email" name="email" placeholder="ejemplo@correo.com" type="email" required/>
                </div>
            </div>

            <div>
                <label class="form-label-styled" for="reg-password">Contraseña Segura</label>
                <div class="relative mb-2">
                    <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </div>
                    <input class="form-input-styled pl-11 pr-11" id="reg-password" name="password" placeholder="•••••••• (mínimo 8 caracteres)" type="password" required minlength="8" onkeyup="checkPasswordStrength(this.value)"/>
                    <button class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 focus:outline-none transition-colors" type="button" onclick="togglePasswordVisibility('reg-password', this)" aria-label="Mostrar u ocultar contraseña">
                        <svg class="w-5 h-5 eye-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </button>
                </div>

                <!-- Password Strength Meter -->
                <div class="flex gap-1.5 h-1.5 mb-1.5">
                    <div id="strength-1" class="flex-1 bg-slate-200 rounded-full transition-colors"></div>
                    <div id="strength-2" class="flex-1 bg-slate-200 rounded-full transition-colors"></div>
                    <div id="strength-3" class="flex-1 bg-slate-200 rounded-full transition-colors"></div>
                    <div id="strength-4" class="flex-1 bg-slate-200 rounded-full transition-colors"></div>
                </div>
                <span id="strength-text" class="text-xs font-semibold text-slate-500"></span>
            </div>

            <!-- Terms Checkbox -->
            <div class="pt-1">
                <label class="flex items-start gap-2 cursor-pointer">
                    <input class="w-4 h-4 rounded border-slate-300 text-[#004ac6] focus:ring-[#004ac6] mt-0.5" type="checkbox" required/>
                    <span class="text-xs text-slate-600 leading-tight">Acepto la Política de Privacidad y Términos de Servicio de VitalSenior.</span>
                </label>
            </div>

            <!-- Register Button -->
            <button class="btn-primary mt-3 flex items-center justify-center gap-2" type="submit">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                <span>Crear cuenta</span>
            </button>
        </form>

    </div>
</div>

<?php require_once dirname(__DIR__) . '/components/toast.php'; ?>

<script>
    function switchTab(tab) {
        const loginForm = document.getElementById('form-login');
        const registerForm = document.getElementById('form-register');
        const loginTab = document.getElementById('tab-login');
        const registerTab = document.getElementById('tab-register');

        if (tab === 'login') {
            loginForm.classList.remove('hidden');
            loginForm.classList.add('block');
            registerForm.classList.add('hidden');
            registerForm.classList.remove('block');
            
            loginTab.classList.add('active');
            loginTab.setAttribute('aria-selected', 'true');
            registerTab.classList.remove('active');
            registerTab.setAttribute('aria-selected', 'false');

            if (history.pushState) {
                const newUrl = window.location.pathname.replace(/\/register$/, '/login');
                window.history.replaceState({ tab: 'login' }, '', newUrl);
            }
        } else {
            registerForm.classList.remove('hidden');
            registerForm.classList.add('block');
            loginForm.classList.add('hidden');
            loginForm.classList.remove('block');
            
            registerTab.classList.add('active');
            registerTab.setAttribute('aria-selected', 'true');
            loginTab.classList.remove('active');
            loginTab.setAttribute('aria-selected', 'false');

            if (history.pushState) {
                const newUrl = window.location.pathname.replace(/\/login$/, '/register');
                window.history.replaceState({ tab: 'register' }, '', newUrl);
            }
        }
    }

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

    function checkPasswordStrength(password) {
        let strength = 0;
        if (password.length >= 8) strength++;
        if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
        if (password.match(/\d/)) strength++;
        if (password.match(/[^a-zA-Z\d]/)) strength++;
        
        const bars = [
            document.getElementById('strength-1'),
            document.getElementById('strength-2'),
            document.getElementById('strength-3'),
            document.getElementById('strength-4')
        ];
        const text = document.getElementById('strength-text');
        
        bars.forEach(bar => {
            bar.className = 'flex-1 bg-slate-200 rounded-full transition-colors';
        });
        
        if (password.length === 0) {
            text.textContent = '';
            return;
        }
        
        if (strength <= 1) {
            bars[0].className = 'flex-1 bg-rose-500 rounded-full transition-colors';
            text.textContent = 'Débil (mínimo 8 caracteres)';
            text.className = 'text-xs font-semibold text-rose-600';
        } else if (strength === 2) {
            bars[0].className = 'flex-1 bg-amber-500 rounded-full transition-colors';
            bars[1].className = 'flex-1 bg-amber-500 rounded-full transition-colors';
            text.textContent = 'Regular';
            text.className = 'text-xs font-semibold text-amber-600';
        } else if (strength === 3) {
            bars[0].className = 'flex-1 bg-[#004ac6] rounded-full transition-colors';
            bars[1].className = 'flex-1 bg-[#004ac6] rounded-full transition-colors';
            bars[2].className = 'flex-1 bg-[#004ac6] rounded-full transition-colors';
            text.textContent = 'Buena';
            text.className = 'text-xs font-semibold text-[#004ac6]';
        } else if (strength >= 4) {
            bars[0].className = 'flex-1 bg-emerald-500 rounded-full transition-colors';
            bars[1].className = 'flex-1 bg-emerald-500 rounded-full transition-colors';
            bars[2].className = 'flex-1 bg-emerald-500 rounded-full transition-colors';
            bars[3].className = 'flex-1 bg-emerald-500 rounded-full transition-colors';
            text.textContent = 'Muy Segura';
            text.className = 'text-xs font-semibold text-emerald-600';
        }
    }
</script>
</body>
</html>