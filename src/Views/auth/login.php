<!DOCTYPE html>
<html class="light h-full bg-slate-50" lang="es">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport"/>
    <title>VitalSenior - Iniciar Sesión / Registro</title>
    
    <!-- Fuentes Google -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
    
    <!-- Tailwind CSS desde CDN -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'system-ui', '-apple-system', 'sans-serif'],
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

        .spinner-loader {
            border: 2.5px solid rgba(255, 255, 255, 0.3);
            border-top: 2.5px solid #ffffff;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            animation: spin 0.6s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen antialiased selection:bg-blue-600/20 selection:text-blue-700">

<div class="min-h-screen grid grid-cols-1 lg:grid-cols-2">

    <!-- ========================================== -->
    <!-- COLUMNA IZQUIERDA (Branding & Showcase) -->
    <!-- ========================================== -->
    <div class="hidden lg:flex flex-col justify-between bg-gradient-to-br from-blue-700 via-blue-600 to-indigo-800 text-white p-12 xl:p-16 relative overflow-hidden select-none">
        
        <!-- Efectos de Iluminación Suave y Gradientes Radiales -->
        <div class="absolute -top-32 -left-32 w-96 h-96 bg-white/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-0 right-0 w-[500px] h-[500px] bg-indigo-900/40 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute inset-0 opacity-15 pointer-events-none" style="background-image: radial-gradient(circle at 30% 25%, rgba(255,255,255,0.6) 0%, transparent 60%);"></div>

        <!-- Header / Logo -->
        <div class="relative z-10">
            <a href="<?php echo APP_URL; ?>/" class="tap-bounce inline-flex items-center gap-3 group">
                <div class="w-11 h-11 rounded-2xl bg-white text-blue-600 flex items-center justify-center font-black text-xl shadow-lg shadow-blue-900/30 group-hover:scale-105 transition-transform">
                    ✚
                </div>
                <span class="text-2xl xl:text-3xl font-black tracking-tight text-white">Vital<span class="text-blue-200">Senior</span></span>
            </a>
            <p class="text-base xl:text-lg text-blue-100/90 font-medium max-w-md leading-relaxed mt-3">
                Cuidamos de la salud de quienes más amas con tecnología simple y humana.
            </p>
        </div>

        <!-- Showcase Central / Tarjeta con Imagen y Badges -->
        <div class="relative z-10 my-auto py-8">
            <div class="w-full max-w-md mx-auto">
                <div class="relative rounded-3xl overflow-hidden shadow-2xl border border-white/20 bg-blue-900/40 backdrop-blur-sm aspect-[4/3] group">
                    <img 
                        alt="Atención médica y cuidado familiar de adultos mayores" 
                        class="object-cover w-full h-full transform group-hover:scale-105 transition-transform duration-700" 
                        src="https://images.unsplash.com/photo-1576765608535-5f04d1e3f289?q=80&w=900&auto=format&fit=crop"
                        loading="lazy"
                    />
                    <div class="absolute inset-0 bg-gradient-to-t from-blue-950/80 via-blue-950/20 to-transparent"></div>
                    
                    <!-- Floating Overlay Badge -->
                    <div class="absolute bottom-4 left-4 right-4 bg-white/95 backdrop-blur-md rounded-2xl p-3.5 border border-white/60 text-slate-800 shadow-xl flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center text-lg flex-shrink-0 shadow-md shadow-blue-600/30">
                            🛡️
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-extrabold text-slate-900 truncate">Historial Médico Protegido</p>
                            <p class="text-[11px] text-slate-500 font-medium truncate">Acceso seguro para familiares y médicos</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Highlights Inferiores -->
        <div class="relative z-10 pt-6 border-t border-white/15 text-xs text-blue-100 flex items-center justify-between font-semibold">
            <div class="flex items-center gap-2">
                <span class="w-5 h-5 rounded-full bg-emerald-400/20 text-emerald-300 flex items-center justify-center text-xs">✓</span>
                <span>Recordatorios automáticos</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-5 h-5 rounded-full bg-emerald-400/20 text-emerald-300 flex items-center justify-center text-xs">✓</span>
                <span>Ficha QR de Emergencias</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-5 h-5 rounded-full bg-emerald-400/20 text-emerald-300 flex items-center justify-center text-xs">✓</span>
                <span>Portal Médico Seguro</span>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- COLUMNA DERECHA (Formularios Auth) -->
    <!-- ========================================== -->
    <div class="min-h-screen flex flex-col justify-center items-center px-4 sm:px-8 md:px-12 lg:px-16 py-8 sm:py-12 bg-white lg:bg-slate-50/60 relative">
        
        <!-- Envoltorio Centrado y Acotado (Anti-Espacio Blanco Vacío) -->
        <div class="w-full max-w-md mx-auto bg-white lg:p-8 lg:rounded-3xl lg:border lg:border-slate-200/80 lg:shadow-card">
            
            <!-- Botón Volver al Inicio -->
            <div class="mb-5 flex items-center justify-between">
                <a href="<?php echo APP_URL; ?>/" class="tap-bounce inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-blue-600 transition-colors group">
                    <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Volver al inicio</span>
                </a>

                <!-- Isotipo en móvil -->
                <div class="lg:hidden flex items-center gap-2">
                    <div class="w-7 h-7 rounded-xl bg-blue-600 text-white flex items-center justify-center text-xs font-black shadow-xs">
                        ✚
                    </div>
                    <span class="text-sm font-black text-slate-900">Vital<span class="text-blue-600">Senior</span></span>
                </div>
            </div>

            <!-- Título y Subtítulo -->
            <div class="mb-6">
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight mb-1">
                    ¡Bienvenido a VitalSenior!
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 leading-relaxed font-medium">
                    Ingresa a tu cuenta o regístrate para comenzar a gestionar los cuidados.
                </p>
            </div>

            <?php 
            $activeTab = $tab ?? ($_GET['tab'] ?? (str_contains($_SERVER['REQUEST_URI'] ?? '', 'register') ? 'register' : 'login'));
            ?>

            <!-- Tabs Segmentados Tipo Pastilla (Emil Design Style) -->
            <div class="bg-slate-100/90 p-1.5 rounded-2xl flex gap-1 mb-6 border border-slate-200/80" role="tablist">
                <button type="button" 
                        id="tab-login" 
                        onclick="switchTab('login')" 
                        role="tab" 
                        aria-selected="<?= $activeTab === 'login' ? 'true' : 'false' ?>"
                        class="tap-bounce touch-min flex-1 py-2.5 px-4 text-xs font-bold rounded-xl transition-all duration-200 text-center cursor-pointer <?= $activeTab === 'login' ? 'bg-white text-blue-600 shadow-sm font-extrabold' : 'text-slate-500 hover:text-slate-900' ?>">
                    Iniciar Sesión
                </button>
                <button type="button" 
                        id="tab-register" 
                        onclick="switchTab('register')" 
                        role="tab" 
                        aria-selected="<?= $activeTab === 'register' ? 'true' : 'false' ?>"
                        class="tap-bounce touch-min flex-1 py-2.5 px-4 text-xs font-bold rounded-xl transition-all duration-200 text-center cursor-pointer <?= $activeTab === 'register' ? 'bg-white text-blue-600 shadow-sm font-extrabold' : 'text-slate-500 hover:text-slate-900' ?>">
                    Regístrate
                </button>
            </div>

            <!-- Inline Flash & Alertas -->
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
                $cardClass = $bgColors[$nType] ?? $bgColors['info'];
            ?>
                <div id="inline-flash-alert" class="mb-5 p-3.5 rounded-2xl border <?= $cardClass; ?> shadow-subtle flex items-start gap-2.5 transition-all text-xs">
                    <span class="text-sm shrink-0">
                        <?= in_array($nType, ['danger', 'error']) ? '🚨' : ($nType === 'warning' ? '⚠️' : ($nType === 'success' ? '✅' : 'ℹ️')) ?>
                    </span>
                    <div class="flex-1 font-medium leading-relaxed">
                        <?php if (!empty($notification['title'])): ?>
                            <p class="font-extrabold uppercase tracking-wider text-[10px] mb-0.5"><?= htmlspecialchars($notification['title']); ?></p>
                        <?php endif; ?>
                        <p><?= htmlspecialchars($notification['message']); ?></p>
                    </div>
                    <button type="button" onclick="document.getElementById('inline-flash-alert').remove()" class="text-slate-400 hover:text-slate-600 p-0.5 rounded-lg transition" aria-label="Cerrar aviso">
                        ✕
                    </button>
                </div>
            <?php endif; ?>

            <!-- ========================================== -->
            <!-- 1. FORMULARIO DE INICIO DE SESIÓN -->
            <!-- ========================================== -->
            <form id="form-login" method="POST" action="<?php echo APP_URL; ?>/login" onsubmit="handleFormSubmit(this, 'btn-submit-login')" class="space-y-4 <?= $activeTab === 'login' ? 'block' : 'hidden' ?>">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                
                <!-- Email Field -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5" for="login-email">
                        Correo Electrónico
                    </label>
                    <div class="relative">
                        <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-base">
                            ✉️
                        </div>
                        <input 
                            class="w-full h-12 pl-11 pr-4 text-xs sm:text-sm font-medium text-slate-900 bg-slate-50/70 hover:bg-slate-50 focus:bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl outline-none transition-all placeholder:text-slate-400" 
                            id="login-email" 
                            name="email" 
                            placeholder="ejemplo@correo.com" 
                            type="email" 
                            required 
                            autocomplete="email"
                        />
                    </div>
                </div>

                <!-- Password Field -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5" for="login-password">
                        Contraseña
                    </label>
                    <div class="relative">
                        <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-base">
                            🔒
                        </div>
                        <input 
                            class="w-full h-12 pl-11 pr-11 text-xs sm:text-sm font-medium text-slate-900 bg-slate-50/70 hover:bg-slate-50 focus:bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl outline-none transition-all placeholder:text-slate-400" 
                            id="login-password" 
                            name="password" 
                            placeholder="••••••••" 
                            type="password" 
                            required 
                            autocomplete="current-password"
                        />
                        <button 
                            class="touch-min absolute right-0 top-0 bottom-0 px-3.5 text-slate-400 hover:text-slate-600 focus:outline-none transition-colors flex items-center justify-center cursor-pointer" 
                            type="button" 
                            onclick="togglePasswordVisibility('login-password', this)" 
                            aria-label="Mostrar u ocultar contraseña">
                            <span class="eye-icon text-xs">👁️</span>
                        </button>
                    </div>
                </div>

                <!-- Opciones: Recordarme & Recuperación -->
                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500/20" type="checkbox"/>
                        <span class="text-xs font-semibold text-slate-600">Recordarme</span>
                    </label>
                    <a class="text-xs font-bold text-blue-600 hover:text-blue-700 hover:underline" href="<?php echo APP_URL; ?>/recovery">
                        ¿Olvidaste tu contraseña?
                    </a>
                </div>

                <!-- Submit Button -->
                <button 
                    id="btn-submit-login" 
                    class="tap-bounce touch-min w-full h-12 bg-blue-600 hover:bg-blue-700 active:scale-[0.98] text-white font-extrabold text-xs sm:text-sm rounded-2xl shadow-lg shadow-blue-600/25 transition-all duration-200 flex items-center justify-center gap-2 cursor-pointer mt-2" 
                    type="submit">
                    <span>Iniciar sesión</span>
                    <span>→</span>
                </button>
            </form>

            <!-- ========================================== -->
            <!-- 2. FORMULARIO DE REGISTRO -->
            <!-- ========================================== -->
            <form id="form-register" method="POST" action="<?php echo APP_URL; ?>/register" onsubmit="handleFormSubmit(this, 'btn-submit-register')" class="space-y-4 <?= $activeTab === 'register' ? 'block' : 'hidden' ?>">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                
                <!-- Nombre Completo -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5" for="reg-name">
                        Nombre Completo
                    </label>
                    <div class="relative">
                        <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-base">
                            👤
                        </div>
                        <input 
                            class="w-full h-12 pl-11 pr-4 text-xs sm:text-sm font-medium text-slate-900 bg-slate-50/70 hover:bg-slate-50 focus:bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl outline-none transition-all placeholder:text-slate-400" 
                            id="reg-name" 
                            name="nombre" 
                            placeholder="Ej. María Elena Garza" 
                            type="text" 
                            required 
                            autocomplete="name"
                        />
                    </div>
                </div>

                <!-- Tipo de Rol -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5" for="reg-rol">
                        Tipo de Cuenta
                    </label>
                    <div class="relative">
                        <select 
                            class="w-full h-12 px-4 text-xs sm:text-sm font-medium text-slate-900 bg-slate-50/70 hover:bg-slate-50 focus:bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl outline-none transition-all cursor-pointer" 
                            id="reg-rol" 
                            name="rol" 
                            required>
                            <option value="familiar" selected>Familiar / Cuidador Principal</option>
                            <option value="adulto_mayor">Adulto Mayor (Para mí)</option>
                        </select>
                    </div>
                </div>

                <!-- Teléfono WhatsApp -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5" for="reg-phone">
                        Teléfono (WhatsApp de Alertas)
                    </label>
                    <div class="relative">
                        <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-base">
                            📱
                        </div>
                        <input 
                            class="w-full h-12 pl-11 pr-4 text-xs sm:text-sm font-medium text-slate-900 bg-slate-50/70 hover:bg-slate-50 focus:bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl outline-none transition-all placeholder:text-slate-400" 
                            id="reg-phone" 
                            name="telefono" 
                            placeholder="+52 999 123 4567" 
                            type="tel"
                            inputmode="tel"
                            autocomplete="tel"
                        />
                    </div>
                </div>

                <!-- Correo Electrónico -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5" for="reg-email">
                        Correo Electrónico
                    </label>
                    <div class="relative">
                        <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-base">
                            ✉️
                        </div>
                        <input 
                            class="w-full h-12 pl-11 pr-4 text-xs sm:text-sm font-medium text-slate-900 bg-slate-50/70 hover:bg-slate-50 focus:bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl outline-none transition-all placeholder:text-slate-400" 
                            id="reg-email" 
                            name="email" 
                            placeholder="ejemplo@correo.com" 
                            type="email" 
                            required 
                            autocomplete="email"
                        />
                    </div>
                </div>

                <!-- Contraseña Segura con Medidor -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5" for="reg-password">
                        Contraseña Segura
                    </label>
                    <div class="relative mb-2">
                        <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-base">
                            🔒
                        </div>
                        <input 
                            class="w-full h-12 pl-11 pr-11 text-xs sm:text-sm font-medium text-slate-900 bg-slate-50/70 hover:bg-slate-50 focus:bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl outline-none transition-all placeholder:text-slate-400" 
                            id="reg-password" 
                            name="password" 
                            placeholder="Mínimo 8 caracteres" 
                            type="password" 
                            required 
                            minlength="8" 
                            autocomplete="new-password"
                            onkeyup="checkPasswordStrength(this.value)"
                        />
                        <button 
                            class="touch-min absolute right-0 top-0 bottom-0 px-3.5 text-slate-400 hover:text-slate-600 focus:outline-none transition-colors flex items-center justify-center cursor-pointer" 
                            type="button" 
                            onclick="togglePasswordVisibility('reg-password', this)" 
                            aria-label="Mostrar u ocultar contraseña">
                            <span class="eye-icon text-xs">👁️</span>
                        </button>
                    </div>

                    <!-- Indicador de Fortaleza -->
                    <div class="space-y-1">
                        <div class="flex gap-1.5 h-1.5">
                            <div id="strength-1" class="flex-1 bg-slate-200 rounded-full transition-colors"></div>
                            <div id="strength-2" class="flex-1 bg-slate-200 rounded-full transition-colors"></div>
                            <div id="strength-3" class="flex-1 bg-slate-200 rounded-full transition-colors"></div>
                            <div id="strength-4" class="flex-1 bg-slate-200 rounded-full transition-colors"></div>
                        </div>
                        <span id="strength-text" class="text-[11px] font-semibold text-slate-400 block"></span>
                    </div>
                </div>

                <!-- Checkbox Términos -->
                <div class="pt-1">
                    <label class="flex items-start gap-2.5 cursor-pointer select-none">
                        <input class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500/20 mt-0.5" type="checkbox" required/>
                        <span class="text-xs text-slate-600 leading-tight font-medium">
                            Acepto los Términos de Servicio y la Política de Privacidad de VitalSenior.
                        </span>
                    </label>
                </div>

                <!-- Submit Button Register -->
                <button 
                    id="btn-submit-register" 
                    class="tap-bounce touch-min w-full h-12 bg-blue-600 hover:bg-blue-700 active:scale-[0.98] text-white font-extrabold text-xs sm:text-sm rounded-2xl shadow-lg shadow-blue-600/25 transition-all duration-200 flex items-center justify-center gap-2 cursor-pointer mt-2" 
                    type="submit">
                    <span>Crear cuenta familiar</span>
                    <span>→</span>
                </button>
            </form>

        </div>
    </div>

</div>

<?php require_once dirname(__DIR__) . '/components/toast.php'; ?>

<!-- Scripts de Interacción y Validaciones -->
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
            
            loginTab.className = 'tap-bounce touch-min flex-1 py-2.5 px-4 text-xs font-extrabold rounded-xl transition-all duration-200 text-center cursor-pointer bg-white text-blue-600 shadow-sm';
            loginTab.setAttribute('aria-selected', 'true');
            
            registerTab.className = 'tap-bounce touch-min flex-1 py-2.5 px-4 text-xs font-bold rounded-xl transition-all duration-200 text-center cursor-pointer text-slate-500 hover:text-slate-900';
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
            
            registerTab.className = 'tap-bounce touch-min flex-1 py-2.5 px-4 text-xs font-extrabold rounded-xl transition-all duration-200 text-center cursor-pointer bg-white text-blue-600 shadow-sm';
            registerTab.setAttribute('aria-selected', 'true');
            
            loginTab.className = 'tap-bounce touch-min flex-1 py-2.5 px-4 text-xs font-bold rounded-xl transition-all duration-200 text-center cursor-pointer text-slate-500 hover:text-slate-900';
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
            icon.innerText = '🙈';
        } else {
            input.type = 'password';
            icon.innerText = '👁️';
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
            text.textContent = 'Contraseña Débil (mínimo 8 caracteres)';
            text.className = 'text-[11px] font-semibold text-rose-600 block';
        } else if (strength === 2) {
            bars[0].className = 'flex-1 bg-amber-500 rounded-full transition-colors';
            bars[1].className = 'flex-1 bg-amber-500 rounded-full transition-colors';
            text.textContent = 'Contraseña Regular';
            text.className = 'text-[11px] font-semibold text-amber-600 block';
        } else if (strength === 3) {
            bars[0].className = 'flex-1 bg-blue-600 rounded-full transition-colors';
            bars[1].className = 'flex-1 bg-blue-600 rounded-full transition-colors';
            bars[2].className = 'flex-1 bg-blue-600 rounded-full transition-colors';
            text.textContent = 'Contraseña Buena';
            text.className = 'text-[11px] font-semibold text-blue-600 block';
        } else if (strength >= 4) {
            bars[0].className = 'flex-1 bg-emerald-500 rounded-full transition-colors';
            bars[1].className = 'flex-1 bg-emerald-500 rounded-full transition-colors';
            bars[2].className = 'flex-1 bg-emerald-500 rounded-full transition-colors';
            bars[3].className = 'flex-1 bg-emerald-500 rounded-full transition-colors';
            text.textContent = 'Contraseña Muy Segura';
            text.className = 'text-[11px] font-semibold text-emerald-600 block';
        }
    }

    function handleFormSubmit(form, btnId) {
        const btn = document.getElementById(btnId);
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-loader"></span> <span>Procesando...</span>';
            btn.classList.add('opacity-80', 'cursor-not-allowed');
        }
        return true;
    }
</script>
</body>
</html>