<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<style>
    /* Estilos del Stepper para Adultos Mayores y Cuidadores */
    .step-container { display: none; }
    .step-container.active { display: block; animation: slideUpFade 0.3s cubic-bezier(0.16, 1, 0.3, 1); }
    @keyframes slideUpFade {
        from { opacity: 0; transform: translateY(8px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .emoji-radio:checked + label {
        background-color: #eff6ff;
        border-color: #2563eb;
        transform: scale(1.04);
        box-shadow: 0 4px 12px -2px rgba(37, 99, 235, 0.2);
    }
    .touch-min {
        min-height: 48px;
        min-width: 48px;
    }
</style>

<div class="max-w-2xl mx-auto px-4 sm:px-6 py-6 sm:py-10">
    <div class="bg-white p-6 sm:p-8 md:p-10 rounded-3xl shadow-card border border-slate-200/80">
        
        <!-- Encabezado del Stepper -->
        <div class="mb-8">
            <div class="flex justify-between items-center mb-3">
                <div class="flex items-center space-x-2">
                    <span class="w-8 h-8 rounded-xl bg-blue-600 text-white font-black text-sm flex items-center justify-center shadow-xs">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </span>
                    <div>
                        <h1 class="text-lg sm:text-xl font-black text-slate-800 tracking-tight">Registro de Signos</h1>
                        <p class="text-xs text-slate-500 font-medium"><?= e($paciente['nombre'] ?? 'Adulto Mayor') ?></p>
                    </div>
                </div>
                <a href="<?= APP_URL ?>/dashboard" class="tap-bounce touch-min px-3 py-2 text-slate-400 hover:text-slate-700 text-sm font-bold flex items-center justify-center rounded-xl bg-slate-50 hover:bg-slate-100 border border-slate-200/80 transition">
                    ✕ Cancelar
                </a>
            </div>

            <!-- Barra de progreso -->
            <div class="w-full bg-slate-100 rounded-full h-2.5 p-0.5 border border-slate-200/60">
                <div id="progress-bar" class="bg-blue-600 h-1.5 rounded-full transition-all duration-300 shadow-xs" style="width: 25%"></div>
            </div>
            <div class="flex justify-between items-center mt-2 text-xs font-bold text-slate-400">
                <span id="step-counter">Paso 1 de 4: Presión Arterial</span>
                <span id="step-percent">25%</span>
            </div>
        </div>

        <form action="<?= APP_URL ?>/salud/registrar" method="POST" id="salud-form" novalidate>
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <input type="hidden" name="paciente_id" value="<?= e((string)($paciente['id'] ?? 0)) ?>">

            <!-- PASO 1: Presión Arterial -->
            <div class="step-container active" id="step-1">
                <div class="text-center mb-6">
                    <div class="w-14 h-14 bg-rose-50 text-rose-600 rounded-2xl flex items-center justify-center mx-auto mb-3 shadow-xs border border-rose-100">
                        <svg class="w-7 h-7 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/>
                        </svg>
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-black text-slate-800 tracking-tight">Presión Arterial</h2>
                    <p class="text-sm text-slate-500 mt-1 max-w-sm mx-auto">
                        Ingresa la sistólica y diastólica separadas por barra (ej: <strong class="text-slate-700 font-mono">120/80</strong>).
                    </p>
                </div>

                <div class="flex justify-center mb-8">
                    <div class="relative w-full max-w-xs">
                        <input type="text" 
                               id="input-presion" 
                               name="presion" 
                               inputmode="numeric" 
                               placeholder="120/80" 
                               pattern="[0-9]{2,3}/[0-9]{2,3}"
                               autocomplete="off"
                               class="text-4xl sm:text-5xl font-mono font-black text-center p-4 sm:p-5 border-2 border-slate-300 rounded-2xl w-full focus:border-blue-600 focus:ring-4 focus:ring-blue-100 transition-all text-slate-900 placeholder:text-slate-300 tracking-wider">
                        <span class="block text-center text-xs font-bold text-slate-400 mt-2">mmHg (Milímetros de mercurio)</span>
                    </div>
                </div>

                <div class="flex justify-end pt-4 border-t border-slate-100">
                    <button type="button" onclick="nextStep(2)" class="tap-bounce touch-min w-full sm:w-auto px-8 py-4 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white text-base sm:text-lg font-bold rounded-2xl shadow-md shadow-blue-600/20 flex items-center justify-center space-x-2 cursor-pointer">
                        <span>Siguiente: Glucosa</span>
                        <span class="text-xl">→</span>
                    </button>
                </div>
            </div>

            <!-- PASO 2: Glucosa en Sangre -->
            <div class="step-container" id="step-2">
                <div class="text-center mb-6">
                    <div class="w-14 h-14 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center mx-auto mb-3 shadow-xs border border-amber-100">
                        <svg class="w-7 h-7 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 2.25c-3.75 5.25-6.75 9.75-6.75 13.5a6.75 6.75 0 0013.5 0c0-3.75-3-8.25-6.75-13.5z"/>
                        </svg>
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-black text-slate-800 tracking-tight">Nivel de Glucosa</h2>
                    <p class="text-sm text-slate-500 mt-1 max-w-sm mx-auto">
                        Medición de azúcar capilar en sangre (mg/dL).
                    </p>
                </div>

                <div class="flex justify-center mb-6">
                    <div class="relative w-full max-w-xs">
                        <input type="number" 
                               step="1" 
                               id="input-glucosa" 
                               name="glucosa" 
                               inputmode="numeric" 
                               placeholder="100" 
                               autocomplete="off" 
                               class="text-4xl sm:text-5xl font-mono font-black text-center p-4 sm:p-5 border-2 border-slate-300 rounded-2xl w-full focus:border-amber-500 focus:ring-4 focus:ring-amber-100 transition-all text-slate-900 placeholder:text-slate-300">
                        <span class="block text-center text-xs font-bold text-slate-400 mt-2">mg/dL</span>
                    </div>
                </div>

                <div class="mb-8">
                    <p class="text-center text-xs font-bold uppercase tracking-wider text-slate-500 mb-3">Momento de la toma</p>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 max-w-md mx-auto">
                        <label class="tap-bounce touch-min flex items-center sm:flex-col justify-center p-3 border-2 border-slate-200 rounded-2xl cursor-pointer hover:bg-slate-50 transition-all text-center">
                            <input type="radio" name="momento_glucosa" value="ayunas" class="w-5 h-5 text-blue-600 mr-2 sm:mr-0 sm:mb-1" checked>
                            <span class="text-xs font-bold text-slate-700">En Ayunas</span>
                        </label>
                        <label class="tap-bounce touch-min flex items-center sm:flex-col justify-center p-3 border-2 border-slate-200 rounded-2xl cursor-pointer hover:bg-slate-50 transition-all text-center">
                            <input type="radio" name="momento_glucosa" value="antes_comer" class="w-5 h-5 text-blue-600 mr-2 sm:mr-0 sm:mb-1">
                            <span class="text-xs font-bold text-slate-700">Antes de Comer</span>
                        </label>
                        <label class="tap-bounce touch-min flex items-center sm:flex-col justify-center p-3 border-2 border-slate-200 rounded-2xl cursor-pointer hover:bg-slate-50 transition-all text-center">
                            <input type="radio" name="momento_glucosa" value="despues_comer" class="w-5 h-5 text-blue-600 mr-2 sm:mr-0 sm:mb-1">
                            <span class="text-xs font-bold text-slate-700">Postprandial (2h)</span>
                        </label>
                    </div>
                </div>

                <div class="flex flex-col-reverse sm:flex-row justify-between gap-3 pt-4 border-t border-slate-100">
                    <button type="button" onclick="prevStep(1)" class="tap-bounce touch-min px-6 py-3.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-2xl transition text-sm flex items-center justify-center">
                        ← Atrás
                    </button>
                    <button type="button" onclick="nextStep(3)" class="tap-bounce touch-min px-8 py-3.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-2xl shadow-md transition text-base flex items-center justify-center space-x-2">
                        <span>Siguiente: Pulso & Dolor</span>
                        <span>→</span>
                    </button>
                </div>
            </div>

            <!-- PASO 3: Frecuencia Cardíaca, Sueño y Dolor -->
            <div class="step-container" id="step-3">
                <div class="text-center mb-6">
                    <div class="w-14 h-14 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center mx-auto mb-3 shadow-xs border border-emerald-100">
                        <svg class="w-7 h-7 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                        </svg>
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-black text-slate-800 tracking-tight">Pulso, Sueño y Dolor</h2>
                    <p class="text-sm text-slate-500 mt-1 max-w-sm mx-auto">
                        Registra cómo se siente el paciente el día de hoy.
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                    <div class="bg-slate-50/70 p-4 rounded-2xl border border-slate-200/80 text-center">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">Frecuencia (lpm)</label>
                        <input type="number" 
                               name="ritmo_cardiaco" 
                               inputmode="numeric" 
                               placeholder="75" 
                               class="text-3xl font-mono font-black text-center p-3 border-2 border-slate-300 rounded-xl w-full focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200">
                        <span class="block text-[11px] text-slate-400 mt-1 font-semibold">Latidos por minuto</span>
                    </div>
                    <div class="bg-slate-50/70 p-4 rounded-2xl border border-slate-200/80 text-center">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">Horas de Sueño</label>
                        <input type="number" 
                               step="0.5" 
                               name="horas_sueno" 
                               inputmode="decimal" 
                               placeholder="7.5" 
                               class="text-3xl font-mono font-black text-center p-3 border-2 border-slate-300 rounded-xl w-full focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200">
                        <span class="block text-[11px] text-slate-400 mt-1 font-semibold">Horas descansadas</span>
                    </div>
                </div>

                <!-- Nivel de Dolor -->
                <div class="mb-8">
                    <p class="text-center text-xs font-bold uppercase tracking-wider text-slate-600 mb-3">Nivel de Dolor / Malestar</p>
                    <div class="grid grid-cols-5 gap-1 sm:gap-2 max-w-md mx-auto">
                        <!-- 0: Sin Dolor -->
                        <div>
                            <input type="radio" name="nivel_dolor" value="0" id="dolor-0" class="hidden emoji-radio" checked>
                            <label for="dolor-0" class="tap-bounce touch-min flex flex-col items-center justify-center p-1.5 sm:p-2.5 border-2 border-slate-200 rounded-xl sm:rounded-2xl cursor-pointer hover:bg-slate-50 transition-all text-center">
                                <svg class="w-7 h-7 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75">
                                    <circle cx="12" cy="12" r="9"/>
                                    <path stroke-linecap="round" d="M9 10h.01M15 10h.01M9.5 15a3.5 3.5 0 005 0"/>
                                </svg>
                                <span class="text-[9px] sm:text-[10px] font-bold text-slate-700 mt-1 truncate w-full">Cero</span>
                            </label>
                        </div>
                        <!-- 1: Leve -->
                        <div>
                            <input type="radio" name="nivel_dolor" value="1" id="dolor-1" class="hidden emoji-radio">
                            <label for="dolor-1" class="tap-bounce touch-min flex flex-col items-center justify-center p-1.5 sm:p-2.5 border-2 border-slate-200 rounded-xl sm:rounded-2xl cursor-pointer hover:bg-slate-50 transition-all text-center">
                                <svg class="w-7 h-7 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75">
                                    <circle cx="12" cy="12" r="9"/>
                                    <path stroke-linecap="round" d="M9 10h.01M15 10h.01M10 15h4"/>
                                </svg>
                                <span class="text-[9px] sm:text-[10px] font-bold text-slate-700 mt-1 truncate w-full">Leve</span>
                            </label>
                        </div>
                        <!-- 2: Moderado -->
                        <div>
                            <input type="radio" name="nivel_dolor" value="2" id="dolor-2" class="hidden emoji-radio">
                            <label for="dolor-2" class="tap-bounce touch-min flex flex-col items-center justify-center p-1.5 sm:p-2.5 border-2 border-slate-200 rounded-xl sm:rounded-2xl cursor-pointer hover:bg-slate-50 transition-all text-center">
                                <svg class="w-7 h-7 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75">
                                    <circle cx="12" cy="12" r="9"/>
                                    <path stroke-linecap="round" d="M9 10h.01M15 10h.01M9 15h6"/>
                                </svg>
                                <span class="text-[9px] sm:text-[10px] font-bold text-slate-700 mt-1 truncate w-full">Medio</span>
                            </label>
                        </div>
                        <!-- 3: Fuerte -->
                        <div>
                            <input type="radio" name="nivel_dolor" value="3" id="dolor-3" class="hidden emoji-radio">
                            <label for="dolor-3" class="tap-bounce touch-min flex flex-col items-center justify-center p-1.5 sm:p-2.5 border-2 border-slate-200 rounded-xl sm:rounded-2xl cursor-pointer hover:bg-slate-50 transition-all text-center">
                                <svg class="w-7 h-7 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75">
                                    <circle cx="12" cy="12" r="9"/>
                                    <path stroke-linecap="round" d="M9 10h.01M15 10h.01M14.5 15.5a3.5 3.5 0 00-5 0"/>
                                </svg>
                                <span class="text-[9px] sm:text-[10px] font-bold text-slate-700 mt-1 truncate w-full">Fuerte</span>
                            </label>
                        </div>
                        <!-- 4: Intenso -->
                        <div>
                            <input type="radio" name="nivel_dolor" value="4" id="dolor-4" class="hidden emoji-radio">
                            <label for="dolor-4" class="tap-bounce touch-min flex flex-col items-center justify-center p-1.5 sm:p-2.5 border-2 border-slate-200 rounded-xl sm:rounded-2xl cursor-pointer hover:bg-slate-50 transition-all text-center">
                                <svg class="w-7 h-7 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75">
                                    <circle cx="12" cy="12" r="9"/>
                                    <path stroke-linecap="round" d="M8.5 9.5l2 1m3-1l-2 1M14.5 16a3.5 3.5 0 00-5 0"/>
                                </svg>
                                <span class="text-[9px] sm:text-[10px] font-bold text-slate-700 mt-1 truncate w-full">Severo</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col-reverse sm:flex-row justify-between gap-3 pt-4 border-t border-slate-100">
                    <button type="button" onclick="prevStep(2)" class="tap-bounce touch-min px-6 py-3.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-2xl transition text-sm flex items-center justify-center">
                        ← Atrás
                    </button>
                    <button type="button" onclick="nextStep(4)" class="tap-bounce touch-min px-8 py-3.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-2xl shadow-md transition text-base flex items-center justify-center space-x-2">
                        <span>Siguiente: Peso & Altura</span>
                        <span>→</span>
                    </button>
                </div>
            </div>

            <!-- PASO 4: Peso, Altura e IMC (Cálculo Inmediato) -->
            <div class="step-container" id="step-4">
                <div class="text-center mb-6">
                    <div class="w-14 h-14 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-3 shadow-xs border border-indigo-100">
                        <svg class="w-7 h-7 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v17.25m0 0c-1.472 0-2.882.265-4.185.75M12 20.25c1.472 0 2.882.265 4.185.75M18.75 4.97A48.416 48.416 0 0012 4.5c-2.291 0-4.545.16-6.75.47m13.5 0c1.01.143 2.01.317 3 .52m-16.5-.52c-1.01.143-2.01.317-3 .52m16.5.52l-2.25 6.75a4.5 4.5 0 01-4.26 3.08h-.02a4.5 4.5 0 01-4.26-3.08L6 5.49"/>
                        </svg>
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-black text-slate-800 tracking-tight">Peso, Altura e IMC</h2>
                    <p class="text-sm text-slate-500 mt-1 max-w-sm mx-auto">
                        Calculamos automáticamente el Índice de Masa Corporal (IMC).
                    </p>
                </div>Corporal (IMC).
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                    <div class="bg-slate-50/70 p-4 rounded-2xl border border-slate-200/80 text-center">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">Peso Corporal (kg)</label>
                        <input type="number" 
                               step="0.1" 
                               id="input-peso" 
                               name="peso" 
                               inputmode="decimal" 
                               placeholder="68.5" 
                               oninput="calcularImcEnVivo()"
                               class="text-3xl font-mono font-black text-center p-3 border-2 border-slate-300 rounded-xl w-full focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200">
                        <span class="block text-[11px] text-slate-400 mt-1 font-semibold">Kilogramos</span>
                    </div>

                    <div class="bg-slate-50/70 p-4 rounded-2xl border border-slate-200/80 text-center">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">Estatura / Altura (cm)</label>
                        <input type="number" 
                               step="1" 
                               id="input-altura" 
                               name="altura" 
                               inputmode="numeric" 
                               placeholder="165" 
                               oninput="calcularImcEnVivo()"
                               class="text-3xl font-mono font-black text-center p-3 border-2 border-slate-300 rounded-xl w-full focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200">
                        <span class="block text-[11px] text-slate-400 mt-1 font-semibold">Centímetros (ej: 165)</span>
                    </div>
                </div>

                <!-- Previsualización reactiva de IMC -->
                <div id="box-imc-preview" class="hidden mb-8 p-4 rounded-2xl bg-indigo-50 border border-indigo-200 text-center transition-all">
                    <span class="text-xs font-bold uppercase tracking-wider text-indigo-700">Índice de Masa Corporal (IMC)</span>
                    <div class="text-3xl font-black text-indigo-900 my-1">
                        <span id="imc-resultado">--</span> <span class="text-sm font-normal text-indigo-600">kg/m²</span>
                    </div>
                    <span id="imc-categoria" class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-white text-indigo-700 shadow-xs">
                        Calculando...
                    </span>
                </div>

                <div class="flex flex-col-reverse sm:flex-row justify-between gap-3 pt-4 border-t border-slate-100">
                    <button type="button" onclick="prevStep(3)" class="tap-bounce touch-min px-6 py-3.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-2xl transition text-sm flex items-center justify-center">
                        ← Atrás
                    </button>
                    <button type="submit" class="tap-bounce touch-min px-8 py-4 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-black text-base sm:text-lg rounded-2xl shadow-lg shadow-emerald-600/25 flex items-center justify-center space-x-2 cursor-pointer">
                        <span>✓ Guardar Signos Vitales</span>
                    </button>
                </div>
            </div>

            <!-- PASO INTERMEDIO DE CONFIRMACIÓN EN EL CLIENTE -->
            <div class="step-container" id="step-confirm">
                <div class="text-center py-4">
                    <div class="w-16 h-16 bg-amber-50 text-amber-600 rounded-3xl flex items-center justify-center mx-auto mb-4 shadow-sm border border-amber-200">
                        <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-black text-slate-800 mb-2">Revisión de Lectura</h2>
                    <p class="text-base text-slate-600 mb-6 max-w-md mx-auto" id="confirm-message">
                        El valor registrado parece estar fuera de los rangos habituales.
                    </p>
                    
                    <div class="flex flex-col gap-3 max-w-xs mx-auto">
                        <button type="button" onclick="confirmAndProceed()" class="tap-bounce touch-min py-4 px-6 bg-emerald-600 hover:bg-emerald-700 text-white font-black rounded-2xl text-base shadow-md">
                            Sí, el valor es correcto
                        </button>
                        <button type="button" onclick="goBackToEdit()" class="tap-bounce touch-min py-3.5 px-6 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-2xl text-sm">
                            Quiero corregirlo
                        </button>
                    </div>
                </div>
            </div>

        </form>
    </div>
</div>

<script>
    const totalSteps = 4;
    const stepTitles = [
        'Paso 1 de 4: Presión Arterial',
        'Paso 2 de 4: Glucosa',
        'Paso 3 de 4: Pulso & Dolor',
        'Paso 4 de 4: Peso & Altura'
    ];

    let pendingNextStep = null;
    let currentEditStep = 1;

    function updateProgress(step) {
        const percent = Math.round((step / totalSteps) * 100);
        document.getElementById('step-counter').innerText = stepTitles[step - 1] || `Paso ${step}`;
        document.getElementById('step-percent').innerText = `${percent}%`;
        document.getElementById('progress-bar').style.width = `${percent}%`;
    }

    function showStep(step) {
        document.querySelectorAll('.step-container').forEach(el => el.classList.remove('active'));
        if (typeof step === 'number') {
            const stepEl = document.getElementById(`step-${step}`);
            if (stepEl) {
                stepEl.classList.add('active');
                updateProgress(step);
                // Enfocar primer input del paso
                const firstInput = stepEl.querySelector('input:not([type="hidden"]):not([type="radio"])');
                if (firstInput) {
                    setTimeout(() => firstInput.focus(), 100);
                }
            }
        } else {
            const el = document.getElementById(step);
            if (el) el.classList.add('active');
        }
    }

    function nextStep(step) {
        // Validaciones clínicas previas al siguiente paso
        if (step === 2) {
            const presionInput = document.getElementById('input-presion');
            const presion = presionInput ? presionInput.value.trim() : '';
            if (presion) {
                const parts = presion.split('/');
                if (parts.length === 2) {
                    const sis = parseInt(parts[0], 10);
                    const dia = parseInt(parts[1], 10);
                    if (sis >= 180 || dia >= 120 || sis <= 80 || dia <= 50) {
                        pendingNextStep = step;
                        currentEditStep = 1;
                        document.getElementById('confirm-message').innerHTML = `Escribiste una presión de <strong>${sis}/${dia} mmHg</strong>. Parece un valor inusual o crítico. ¿Estás seguro?`;
                        showStep('step-confirm');
                        return;
                    }
                }
            }
        } else if (step === 3) {
            const glucosaInput = document.getElementById('input-glucosa');
            const gluc = glucosaInput ? parseFloat(glucosaInput.value) : 0;
            if (gluc > 0 && (gluc < 60 || gluc > 250)) {
                pendingNextStep = step;
                currentEditStep = 2;
                document.getElementById('confirm-message').innerHTML = `Escribiste un nivel de glucosa de <strong>${gluc} mg/dL</strong>. Es un valor extremo. ¿Deseas confirmarlo?`;
                showStep('step-confirm');
                return;
            }
        }

        showStep(step);
    }

    function confirmAndProceed() {
        if (pendingNextStep) {
            showStep(pendingNextStep);
            pendingNextStep = null;
        }
    }

    function goBackToEdit() {
        if (currentEditStep) {
            showStep(currentEditStep);
            pendingNextStep = null;
        }
    }

    function prevStep(step) {
        showStep(step);
    }

    function calcularImcEnVivo() {
        const peso = parseFloat(document.getElementById('input-peso').value);
        const alturaRaw = parseFloat(document.getElementById('input-altura').value);
        const previewBox = document.getElementById('box-imc-preview');
        const resultadoEl = document.getElementById('imc-resultado');
        const categoriaEl = document.getElementById('imc-categoria');

        if (!peso || !alturaRaw || peso <= 0 || alturaRaw <= 0) {
            previewBox.classList.add('hidden');
            return;
        }

        const alturaM = alturaRaw > 3.0 ? (alturaRaw / 100.0) : alturaRaw;
        if (alturaM <= 0.4) {
            previewBox.classList.add('hidden');
            return;
        }

        const imc = (peso / (alturaM * alturaM)).toFixed(1);
        resultadoEl.innerText = imc;

        let categoria = 'Normal';
        let badgeClass = 'bg-emerald-100 text-emerald-800';

        if (imc < 18.5) {
            categoria = 'Bajo peso (Riesgo geriátrico)';
            badgeClass = 'bg-amber-100 text-amber-800';
        } else if (imc <= 24.9) {
            categoria = 'Peso Normal';
            badgeClass = 'bg-emerald-100 text-emerald-800';
        } else if (imc <= 29.9) {
            categoria = 'Sobrepeso';
            badgeClass = 'bg-blue-100 text-blue-800';
        } else if (imc <= 34.9) {
            categoria = 'Obesidad Grado I';
            badgeClass = 'bg-amber-100 text-amber-800';
        } else {
            categoria = 'Obesidad Severa';
            badgeClass = 'bg-rose-100 text-rose-800';
        }

        categoriaEl.innerText = categoria;
        categoriaEl.className = `inline-block px-3 py-1 rounded-full text-xs font-bold shadow-xs ${badgeClass}`;
        previewBox.classList.remove('hidden');
    }
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
