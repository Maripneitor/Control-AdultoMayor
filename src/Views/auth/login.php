<!DOCTYPE html>
<html class="light h-full bg-slate-50" lang="es">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport"/>
    <title>VitalSenior - Iniciar Sesión / Registro</title>
    
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
                <div class="w-11 h-11 rounded-2xl bg-white text-blue-600 flex items-center justify-center shadow-lg shadow-blue-900/30 group-hover:scale-105 transition-transform">
                    <svg class="w-6 h-6 text-blue-600" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                    </svg>
                </div>
                <span class="text-2xl xl:text-3xl font-black tracking-tight text-white">Vital<span class="text-blue-200">Senior</span></span>
            </a>
            <p class="text-base xl:text-lg text-blue-100/90 font-medium max-w-md leading-relaxed mt-3">
                Cuidamos de la salud de quienes más amas con tecnología simple, segura y humana.
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
                        <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center flex-shrink-0 shadow-md shadow-blue-600/30">
                            <svg class="w-5 h-5 text-white" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-extrabold text-slate-900 truncate">Historial Médico Protegido</p>
                            <p class="text-[11px] text-slate-500 font-medium truncate">Acceso seguro para familiares y médicos tratantes</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Highlights Inferiores -->
        <div class="relative z-10 pt-6 border-t border-white/15 text-xs text-blue-100 flex items-center justify-between font-semibold">
            <div class="flex items-center gap-2">
                <span class="w-5 h-5 rounded-full bg-emerald-400/20 text-emerald-300 flex items-center justify-center">
                    <svg class="w-3 h-3" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                    </svg>
                </span>
                <span>Recordatorios automáticos</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-5 h-5 rounded-full bg-emerald-400/20 text-emerald-300 flex items-center justify-center">
                    <svg class="w-3 h-3" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                    </svg>
                </span>
                <span>Ficha QR SOS</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-5 h-5 rounded-full bg-emerald-400/20 text-emerald-300 flex items-center justify-center">
                    <svg class="w-3 h-3" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                    </svg>
                </span>
                <span>Portal Clínico Seguro</span>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- COLUMNA DERECHA (Formularios Auth) -->
    <!-- ========================================== -->
    <div class="min-h-screen flex items-center justify-center p-4 sm:p-8 md:p-12 lg:p-16 bg-white lg:bg-slate-50/60 relative">
        
        <!-- Envoltorio Centrado y Acotado (Anti-Espacio Blanco Vacío) -->
        <div class="w-full max-w-md mx-auto bg-white lg:p-8 lg:rounded-3xl lg:border lg:border-slate-200/80 lg:shadow-card">
            
            <!-- Botón Volver al Inicio -->
            <div class="mb-5 flex items-center justify-between">
                <a href="<?php echo APP_URL; ?>/" class="tap-bounce inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-blue-600 transition-colors group">
                    <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
                    </svg>
                    <span>Volver al inicio</span>
                </a>

                <!-- Isotipo en móvil -->
                <div class="lg:hidden flex items-center gap-2">
                    <div class="w-7 h-7 rounded-xl bg-blue-600 text-white flex items-center justify-center shadow-xs">
                        <svg class="w-4 h-4 text-white" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                        </svg>
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
                        class="tap-bounce touch-min flex-1 py-2.5 px-4 text-xs font-bold rounded-xl transition-all duration-200 text-center cursor-pointer <?= $activeTab === 'login' ? 'bg-white text-blue-600 shadow-xs font-extrabold' : 'text-slate-500 hover:text-slate-900' ?>">
                    Iniciar Sesión
                </button>
                <button type="button" 
                        id="tab-register" 
                        onclick="switchTab('register')" 
                        role="tab" 
                        aria-selected="<?= $activeTab === 'register' ? 'true' : 'false' ?>"
                        class="tap-bounce touch-min flex-1 py-2.5 px-4 text-xs font-bold rounded-xl transition-all duration-200 text-center cursor-pointer <?= $activeTab === 'register' ? 'bg-white text-blue-600 shadow-xs font-extrabold' : 'text-slate-500 hover:text-slate-900' ?>">
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
                    <span class="shrink-0 mt-0.5">
                        <?php if (in_array($nType, ['danger', 'error'])): ?>
                            <svg class="w-4 h-4 text-rose-600" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                            </svg>
                        <?php elseif ($nType === 'warning'): ?>
                            <svg class="w-4 h-4 text-amber-600" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9.003 3.376c-.866 1.5-2.072 2.874-3.803 2.874H6.8c-1.73 0-2.937-1.374-3.803-2.874L10.05 3.378c.866-1.5 3.033-1.5 3.9 0l7.053 12.748zM12 15.75h.007v.008H12v-.008z"/>
                            </svg>
                        <?php elseif ($nType === 'success'): ?>
                            <svg class="w-4 h-4 text-emerald-600" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        <?php else: ?>
                            <svg class="w-4 h-4 text-blue-600" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
                            </svg>
                        <?php endif; ?>
                    </span>
                    <div class="flex-1 font-medium leading-relaxed">
                        <?php if (!empty($notification['title'])): ?>
                            <p class="font-extrabold uppercase tracking-wider text-[10px] mb-0.5"><?= htmlspecialchars($notification['title']); ?></p>
                        <?php endif; ?>
                        <p><?= htmlspecialchars($notification['message']); ?></p>
                    </div>
                    <button type="button" onclick="document.getElementById('inline-flash-alert').remove()" class="text-slate-400 hover:text-slate-600 p-0.5 rounded-lg transition" aria-label="Cerrar aviso">
                        <svg class="w-3.5 h-3.5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
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
                        <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                            <svg class="w-5 h-5 text-slate-400" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
                            </svg>
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
                        <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                            <svg class="w-5 h-5 text-slate-400" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                            </svg>
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
                            <svg class="w-5 h-5 eye-icon" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
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
                    class="tap-bounce touch-min w-full h-12 bg-blue-600 hover:bg-blue-700 active:scale-[0.98] text-white font-extrabold text-xs sm:text-sm rounded-2xl shadow-md shadow-blue-600/25 transition-all duration-200 inline-flex items-center justify-center gap-2 cursor-pointer mt-2" 
                    type="submit">
                    <span>Iniciar sesión</span>
                    <svg class="w-4 h-4" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                    </svg>
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
                        <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                            <svg class="w-5 h-5 text-slate-400" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                            </svg>
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
                            class="w-full h-12 px-4 text-xs sm:text-sm font-semibold text-slate-900 bg-slate-50/70 hover:bg-slate-50 focus:bg-white border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl outline-none transition-all cursor-pointer" 
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
                        Teléfono Móvil (WhatsApp)
                    </label>
                    <div class="relative">
                        <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                            <svg class="w-5 h-5 text-slate-400" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3"/>
                            </svg>
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
                        <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                            <svg class="w-5 h-5 text-slate-400" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
                            </svg>
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
                        <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                            <svg class="w-5 h-5 text-slate-400" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                            </svg>
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
                            <svg class="w-5 h-5 eye-icon" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
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
                    class="tap-bounce touch-min w-full h-12 bg-blue-600 hover:bg-blue-700 active:scale-[0.98] text-white font-extrabold text-xs sm:text-sm rounded-2xl shadow-md shadow-blue-600/25 transition-all duration-200 inline-flex items-center justify-center gap-2 cursor-pointer mt-2" 
                    type="submit">
                    <span>Crear cuenta familiar</span>
                    <svg class="w-4 h-4" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                    </svg>
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
            
            loginTab.className = 'tap-bounce touch-min flex-1 py-2.5 px-4 text-xs font-extrabold rounded-xl transition-all duration-200 text-center cursor-pointer bg-white text-blue-600 shadow-xs';
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
            
            registerTab.className = 'tap-bounce touch-min flex-1 py-2.5 px-4 text-xs font-extrabold rounded-xl transition-all duration-200 text-center cursor-pointer bg-white text-blue-600 shadow-xs';
            registerTab.setAttribute('aria-selected', 'true');
            
            loginTab.className = 'tap-bounce touch-min flex-1 py-2.5 px-4 text-xs font-bold rounded-xl transition-all duration-200 text-center cursor-pointer text-slate-500 hover:text-slate-900';
            loginTab.setAttribute('aria-selected', 'false');

            if (history.pushState) {
                const newUrl = window.location.pathname.replace(/\/login$/, '/register');
                window.history.replaceState({ tab: 'register' }, '', newUrl);
            }
        }
    }

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