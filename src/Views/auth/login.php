<!DOCTYPE html>

<html class="light" lang="es"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>VitalSenior - Login / Registro</title>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<script id="tailwind-config">
        tailwind.config = {
          darkMode: "class",
          theme: {
            extend: {
              "colors": {
                      "surface-dim": "#d8dadc",
                      "outline-variant": "#c3c6d7",
                      "surface-variant": "#e0e3e5",
                      "on-surface-variant": "#434655",
                      "surface-container-highest": "#e0e3e5",
                      "on-background": "#191c1e",
                      "inverse-primary": "#b4c5ff",
                      "on-tertiary": "#ffffff",
                      "inverse-surface": "#2d3133",
                      "outline": "#737686",
                      "on-primary-fixed": "#00174b",
                      "surface": "#f7f9fb",
                      "tertiary-fixed-dim": "#b7c9d5",
                      "on-tertiary-container": "#e2f4ff",
                      "on-primary": "#ffffff",
                      "secondary": "#4059aa",
                      "secondary-fixed-dim": "#b6c4ff",
                      "primary-container": "#2563eb",
                      "on-tertiary-fixed-variant": "#384953",
                      "tertiary-fixed": "#d3e5f1",
                      "on-secondary": "#ffffff",
                      "error-container": "#ffdad6",
                      "surface-container-lowest": "#ffffff",
                      "on-primary-container": "#eeefff",
                      "on-secondary-fixed-variant": "#264191",
                      "surface-container-high": "#e6e8ea",
                      "secondary-fixed": "#dce1ff",
                      "surface-container": "#eceef0",
                      "surface-bright": "#f7f9fb",
                      "surface-tint": "#0053db",
                      "on-primary-fixed-variant": "#003ea8",
                      "on-error-container": "#93000a",
                      "on-surface": "#191c1e",
                      "on-error": "#ffffff",
                      "inverse-on-surface": "#eff1f3",
                      "primary-fixed-dim": "#b4c5ff",
                      "primary-fixed": "#dbe1ff",
                      "primary": "#004ac6",
                      "tertiary": "#475861",
                      "background": "#f7f9fb",
                      "surface-container-low": "#f2f4f6",
                      "error": "#ba1a1a",
                      "on-tertiary-fixed": "#0c1e26",
                      "tertiary-container": "#5f707a",
                      "on-secondary-fixed": "#00164e",
                      "on-secondary-container": "#1d3989",
                      "secondary-container": "#8fa7fe"
              },
              "borderRadius": {
                      "DEFAULT": "0.25rem",
                      "lg": "0.5rem",
                      "xl": "0.75rem",
                      "full": "9999px"
              },
              "spacing": {
                      "stack-lg": "48px",
                      "container-max": "1280px",
                      "stack-md": "24px",
                      "unit": "8px",
                      "gutter": "24px",
                      "stack-sm": "12px",
                      "margin-mobile": "20px"
              },
              "fontFamily": {
                      "display-lg": [
                              "Outfit"
                      ],
                      "body-lg": [
                              "Outfit"
                      ],
                      "headline-md": [
                              "Outfit"
                      ],
                      "body-md": [
                              "Outfit"
                      ],
                      "label-md": [
                              "Outfit"
                      ],
                      "headline-lg-mobile": [
                              "Outfit"
                      ],
                      "headline-lg": [
                              "Outfit"
                      ]
              },
              "fontSize": {
                      "display-lg": [
                              "48px",
                              {
                                      "lineHeight": "56px",
                                      "letterSpacing": "-0.02em",
                                      "fontWeight": "700"
                              }
                      ],
                      "body-lg": [
                              "20px",
                              {
                                      "lineHeight": "30px",
                                      "fontWeight": "400"
                              }
                      ],
                      "headline-md": [
                              "24px",
                              {
                                      "lineHeight": "32px",
                                      "fontWeight": "600"
                              }
                      ],
                      "body-md": [
                              "18px",
                              {
                                      "lineHeight": "28px",
                                      "fontWeight": "400"
                              }
                      ],
                      "label-md": [
                              "16px",
                              {
                                      "lineHeight": "24px",
                                      "fontWeight": "600"
                              }
                      ],
                      "headline-lg-mobile": [
                              "28px",
                              {
                                      "lineHeight": "36px",
                                      "fontWeight": "600"
                              }
                      ],
                      "headline-lg": [
                              "32px",
                              {
                                      "lineHeight": "40px",
                                      "fontWeight": "600"
                              }
                      ]
              }
      },
          },
        }
      </script>
<style type="text/tailwindcss">
        @layer components {
            .glass-panel {
                background: rgba(255, 255, 255, 0.8);
                backdrop-filter: blur(12px);
                -webkit-backdrop-filter: blur(12px);
                border: 1px solid rgba(255, 255, 255, 0.3);
            }
            .form-input-styled {
                @apply w-full h-[42px] px-[14px] py-[8px] text-[13px] font-body-md text-on-surface bg-surface border-2 border-outline-variant rounded-xl focus:border-primary focus:ring-0 transition-colors;
            }
            .form-label-styled {
                @apply block font-label-md text-[12px] text-on-surface-variant mb-unit whitespace-nowrap overflow-hidden text-ellipsis max-w-full;
            }
            .btn-primary {
                @apply flex items-center justify-center w-full h-[42px] text-[13px] bg-primary text-on-primary font-label-md rounded-xl hover:bg-on-primary-fixed-variant transition-all shadow-[0_4px_14px_0_rgba(0,74,198,0.39)] hover:shadow-[0_6px_20px_rgba(0,74,198,0.23)] hover:-translate-y-[1px] active:translate-y-[1px] active:shadow-none;
            }
            .tab-btn {
                @apply flex-1 py-2 text-[13px] text-center font-label-md text-on-surface-variant/60 border-b-2 border-transparent hover:text-on-surface transition-all cursor-pointer outline-none focus:outline-none focus:ring-0;
            }
            .tab-btn.active {
                @apply text-primary border-primary bg-primary/5 font-bold;
            }
        }
      </style>
</head>
<body id="login-body" class="bg-surface text-on-surface font-body-md min-h-screen flex flex-col md:flex-row transition-opacity duration-500 opacity-100">
<!-- Left Column (Showcase) -->
<div class="hidden md:flex flex-col justify-between w-1/2 bg-[#1e3a8a] text-white p-stack-lg relative overflow-hidden">
<!-- Background Pattern / Blur -->
<div class="absolute inset-0 opacity-20 pointer-events-none" style="background-image: radial-gradient(circle at 20% 30%, rgba(255,255,255,0.4) 0%, transparent 50%);"></div>
<!-- Header -->
<div class="relative z-10">
<div class="flex items-center gap-unit mb-stack-sm">
<span class="material-symbols-outlined text-[32px] text-secondary-fixed" data-icon="medical_services" data-weight="fill" style="font-variation-settings: 'FILL' 1;">medical_services</span>
<h1 class="font-headline-lg text-headline-lg text-white">VitalSenior</h1>
</div>
<p class="font-body-lg text-body-lg text-secondary-fixed max-w-md">
                Cuidamos de tu salud. Gestión sencilla para ti y tu familia.
            </p>
</div>
<!-- Center Image -->
<div class="relative z-10 flex-grow flex items-center justify-center py-stack-lg">
<div class="w-full max-w-lg aspect-square relative">
<div class="absolute inset-0 bg-primary-fixed rounded-full opacity-10 blur-3xl"></div>
<img alt="Cuidado de adultos mayores" class="object-cover w-full h-full relative z-10 drop-shadow-2xl rounded-3xl" src="https://images.unsplash.com/photo-1576091160550-2173ff9e5eb3?q=80&w=800&auto=format&fit=crop"/>
</div>
</div>
<!-- Bottom Carousel/Messages -->
<div class="relative z-10 mt-auto hidden">
<div class="bg-white/10 backdrop-blur-md rounded-2xl p-stack-md border border-white/20">
<div class="flex items-center justify-between">
<button class="p-unit rounded-full hover:bg-white/10 transition-colors text-secondary-fixed">
<span class="material-symbols-outlined" data-icon="chevron_left">chevron_left</span>
</button>
<div class="text-center">
<p class="font-label-md text-label-md text-white mb-1">Control de tomas en tiempo real</p>
<p class="font-body-md text-body-md text-secondary-fixed/80 text-sm">Registra y monitorea medicación fácilmente.</p>
</div>
<button class="p-unit rounded-full hover:bg-white/10 transition-colors text-secondary-fixed">
<span class="material-symbols-outlined" data-icon="chevron_right">chevron_right</span>
</button>
</div>
<div class="flex justify-center gap-2 mt-8">
<div class="w-2 h-2 rounded-full bg-white"></div>
<div class="w-2 h-2 rounded-full bg-white/30"></div>
<div class="w-2 h-2 rounded-full bg-white/30"></div>
</div>
</div>
</div>
</div>
<!-- Right Column (Forms) -->
<div class="w-full md:w-1/2 bg-surface-container-lowest flex flex-col justify-center px-margin-mobile py-8 md:px-8 lg:px-[60px] min-h-screen relative shadow-[-10px_0_30px_rgba(0,0,0,0.05)] z-20">
<!-- Mobile Logo Header (Hidden on Desktop) -->
<div class="md:hidden flex items-center gap-unit mb-6 justify-center">
<span class="material-symbols-outlined text-[32px] text-primary" data-icon="medical_services" data-weight="fill" style="font-variation-settings: 'FILL' 1;">medical_services</span>
<h1 class="font-headline-lg-mobile text-headline-lg-mobile text-primary">VitalSenior</h1>
</div>
<div class="max-w-[380px] w-full mx-auto">
<a href="<?php echo APP_URL; ?>/" class="inline-flex items-center gap-1 text-sm font-label-md text-on-surface-variant hover:text-primary transition-colors mb-4">
    <span class="material-symbols-outlined text-[20px]">arrow_back</span>
    Volver al inicio
</a>
<div class="mb-6 text-center md:text-left">
<h2 class="font-display-lg text-display-lg text-on-surface mb-2 text-[24px] md:text-[28px] leading-tight">¡Bienvenido a VitalSenior!</h2>
<p class="font-body-lg text-body-lg text-on-surface-variant">Accede a tu historial y gestiona tus cuidados o crea tu cuenta familiar.</p>
</div>
<!-- Tabs -->
<div class="flex mb-6 border-b border-surface-variant">
<button class="tab-btn active" id="tab-login" onclick="switchTab('login')">Iniciar Sesión</button>
<button class="tab-btn" id="tab-register" onclick="switchTab('register')">Regístrate</button>
</div>
<!-- Login Form -->
<form class="space-y-4 block" id="form-login" method="POST" action="<?php echo APP_URL; ?>/login">
    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
<div>
<label class="form-label-styled" for="login-email">Correo Electrónico o Teléfono</label>
<div class="relative">
<span class="material-symbols-outlined absolute left-[16px] top-1/2 -translate-y-1/2 text-outline" data-icon="person">person</span>
<input class="form-input-styled pl-[48px]" id="login-email" name="email" placeholder="ejemplo@correo.com" type="text"/>
</div>
</div>
<div>
<label class="form-label-styled" for="login-password">Contraseña</label>
<div class="relative">
<span class="material-symbols-outlined absolute left-[16px] top-1/2 -translate-y-1/2 text-outline" data-icon="lock">lock</span>
<input class="form-input-styled pl-[48px]" id="login-password" name="password" placeholder="••••••••" type="password"/>
<button class="absolute right-[16px] top-1/2 -translate-y-1/2 text-outline hover:text-primary transition-colors" type="button" onclick="togglePasswordVisibility('login-password', this)">
<span class="material-symbols-outlined">visibility</span>
</button>
</div>
</div>
<div class="flex items-center justify-between">
<label class="flex items-center gap-2 cursor-pointer">
<input class="w-5 h-5 rounded border-outline-variant text-primary focus:ring-primary" type="checkbox"/>
<span class="font-body-md text-body-md text-on-surface-variant">Recordarme</span>
</label>
<a class="font-label-md text-label-md text-primary hover:underline" href="#">¿Olvidaste tu contraseña?</a>
</div>
<button class="btn-primary mt-4" type="submit">Iniciar sesión</button>
    <?php if (!empty($error)): ?>
        <p class="text-error text-center mt-2"><?php echo $error; ?></p>
    <?php endif; ?>
</form>
<!-- Register Form (Hidden by default) -->
<form class="space-y-4 hidden" id="form-register" method="POST" action="<?php echo APP_URL; ?>/register">
    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
<div>
<label class="form-label-styled" for="reg-name">Nombre Completo</label>
<input class="form-input-styled" id="reg-name" name="nombre" placeholder="Juan Pérez" type="text" required/>
</div>
<div>
<label class="form-label-styled" for="reg-phone">Teléfono (WhatsApp)</label>
<div class="relative">
<span class="material-symbols-outlined absolute left-[16px] top-1/2 -translate-y-1/2 text-outline" data-icon="call">call</span>
<input class="form-input-styled pl-[48px]" id="reg-phone" name="telefono" placeholder="+34 600 000 000" type="tel"/>
</div>
</div>
<div>
<label class="form-label-styled" for="reg-email">Correo Electrónico</label>
<input class="form-input-styled" id="reg-email" name="email" placeholder="ejemplo@correo.com" type="email" required/>
</div>
<div>
<label class="form-label-styled" for="reg-password">Contraseña Segura</label>
<div class="relative mb-2">
<span class="material-symbols-outlined absolute left-[16px] top-1/2 -translate-y-1/2 text-outline" data-icon="lock">lock</span>
<input class="form-input-styled pl-[48px]" id="reg-password" name="password" placeholder="••••••••" type="password" required minlength="8" onkeyup="checkPasswordStrength(this.value)"/>
<button class="absolute right-[16px] top-1/2 -translate-y-1/2 text-outline hover:text-primary transition-colors" type="button" onclick="togglePasswordVisibility('reg-password', this)">
<span class="material-symbols-outlined">visibility</span>
</button>
</div>
<!-- Strength Indicator -->
<div class="flex gap-1 h-2 mb-1">
<div id="strength-1" class="flex-1 bg-surface-variant rounded-full transition-colors"></div>
<div id="strength-2" class="flex-1 bg-surface-variant rounded-full transition-colors"></div>
<div id="strength-3" class="flex-1 bg-surface-variant rounded-full transition-colors"></div>
<div id="strength-4" class="flex-1 bg-surface-variant rounded-full transition-colors"></div>
</div>
<span id="strength-text" class="text-xs text-on-surface-variant font-body-md"></span>
</div>
<div class="space-y-2">
<label class="hidden items-start gap-2 cursor-pointer">
<input class="w-5 h-5 rounded border-outline-variant text-primary focus:ring-primary mt-1" type="checkbox"/>
<span class="font-body-md text-body-md text-on-surface-variant text-sm leading-tight">Deseo recibir recordatorios de medicación vía WhatsApp.</span>
</label>
<label class="flex items-start gap-2 cursor-pointer">
<input class="w-5 h-5 rounded border-outline-variant text-primary focus:ring-primary mt-1" type="checkbox" required/>
<span class="font-body-md text-body-md text-on-surface-variant text-sm leading-tight">Acepto la Política de Privacidad y Términos de Servicio.</span>
</label>
</div>
<button class="btn-primary mt-4 flex items-center justify-center gap-2" type="submit">
<span class="material-symbols-outlined" data-icon="shield">shield</span>
Crear cuenta
</button>
</form>
<!-- Footer Action -->
<div class="hidden mt-8 pt-6 border-t border-surface-variant text-center">
<a class="inline-flex items-center justify-center gap-2 font-label-md text-label-md text-secondary hover:text-primary transition-colors bg-secondary-fixed/30 py-3 px-6 rounded-xl hover:bg-secondary-fixed/50" href="#">
<span class="material-symbols-outlined" data-icon="qr_code_scanner">qr_code_scanner</span>
                    Consultar Ficha Médica de Emergencia
                </a>
</div>
</div>
</div>
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
                registerTab.classList.remove('active');
            } else {
                registerForm.classList.remove('hidden');
                registerForm.classList.add('block');
                loginForm.classList.add('hidden');
                loginForm.classList.remove('block');
                
                registerTab.classList.add('active');
                loginTab.classList.remove('active');
            }
        }

        function togglePasswordVisibility(inputId, btn) {
            const input = document.getElementById(inputId);
            const icon = btn.querySelector('span');
            if (input.type === 'password') {
                input.type = 'text';
                icon.textContent = 'visibility_off';
            } else {
                input.type = 'password';
                icon.textContent = 'visibility';
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
                bar.className = 'flex-1 bg-surface-variant rounded-full transition-colors';
            });
            
            if (password.length === 0) {
                text.textContent = '';
                return;
            }
            
            if (strength === 1 || strength === 0) {
                bars[0].className = 'flex-1 bg-error rounded-full transition-colors';
                text.textContent = 'Débil';
                text.className = 'text-xs text-error font-body-md';
            } else if (strength === 2) {
                bars[0].className = 'flex-1 bg-yellow-500 rounded-full transition-colors';
                bars[1].className = 'flex-1 bg-yellow-500 rounded-full transition-colors';
                text.textContent = 'Regular';
                text.className = 'text-xs text-yellow-600 font-body-md';
            } else if (strength === 3) {
                bars[0].className = 'flex-1 bg-primary rounded-full transition-colors';
                bars[1].className = 'flex-1 bg-primary rounded-full transition-colors';
                bars[2].className = 'flex-1 bg-primary rounded-full transition-colors';
                text.textContent = 'Buena';
                text.className = 'text-xs text-primary font-body-md';
            } else if (strength >= 4) {
                bars[0].className = 'flex-1 bg-green-500 rounded-full transition-colors';
                bars[1].className = 'flex-1 bg-green-500 rounded-full transition-colors';
                bars[2].className = 'flex-1 bg-green-500 rounded-full transition-colors';
                bars[3].className = 'flex-1 bg-green-500 rounded-full transition-colors';
                text.textContent = 'Muy Segura';
                text.className = 'text-xs text-green-600 font-body-md';
            }
        }

        document.getElementById('form-login').addEventListener('submit', function(e) {
            e.preventDefault();
            const body = document.getElementById('login-body');
            body.classList.remove('opacity-100');
            body.classList.add('opacity-0');
            setTimeout(() => {
                this.submit();
            }, 500);
        });
    </script>
</body></html>