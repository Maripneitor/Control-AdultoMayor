<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<style>
    /* Estilos específicos para el Stepper (Tercera edad) */
    .step-container { display: none; animation: fadeIn 0.5s; }
    .step-container.active { display: block; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    .emoji-radio:checked + label { background-color: #fef08a; border-color: #ca8a04; transform: scale(1.05); }
</style>

<div class="max-w-3xl mx-auto mt-8 mb-16">
    <div class="bg-white p-8 rounded-2xl shadow-lg border border-slate-200">
        <!-- Indicador de progreso -->
        <div class="mb-8">
            <div class="flex justify-between items-center mb-2">
                <span class="text-sm font-bold text-slate-500" id="step-counter">Paso 1 de 4</span>
                <button type="button" onclick="window.location.href='<?= APP_URL ?>/dashboard'" class="text-slate-400 hover:text-slate-600 font-bold text-xl">&times; Cancelar</button>
            </div>
            <div class="w-full bg-slate-200 rounded-full h-3">
                <div id="progress-bar" class="bg-blue-600 h-3 rounded-full transition-all duration-300" style="width: 25%"></div>
            </div>
        </div>

        <form action="<?= APP_URL ?>/salud/registrar" method="POST" id="salud-form">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

            <!-- PASO 1: Presión Arterial -->
            <div class="step-container active" id="step-1">
                <h2 class="text-3xl font-bold text-slate-800 mb-6 text-center">Presión Arterial</h2>
                <p class="text-center text-lg text-slate-600 mb-8">Escribe tu presión. Ejemplo: 120/80</p>
                <div class="flex justify-center mb-10">
                    <input type="text" name="presion" inputmode="numeric" placeholder="120/80" 
                           class="text-4xl text-center p-4 border-2 border-slate-300 rounded-xl w-64 focus:border-blue-500 focus:ring-4 focus:ring-blue-200 transition-all">
                </div>
                <div class="flex justify-end">
                    <button type="button" onclick="nextStep(2)" class="px-8 py-4 bg-blue-600 text-white text-xl font-bold rounded-xl hover:bg-blue-700 shadow-md">Siguiente &rarr;</button>
                </div>
            </div>

            <!-- PASO 2: Glucosa -->
            <div class="step-container" id="step-2">
                <h2 class="text-3xl font-bold text-slate-800 mb-6 text-center">Nivel de Glucosa</h2>
                <div class="flex justify-center mb-6">
                    <input type="number" step="0.1" name="glucosa" inputmode="numeric" placeholder="mg/dL" 
                           class="text-4xl text-center p-4 border-2 border-slate-300 rounded-xl w-64 focus:border-blue-500 focus:ring-4 focus:ring-blue-200 transition-all">
                </div>
                <p class="text-center text-lg text-slate-600 mb-4 font-bold">¿En qué momento tomaste la lectura?</p>
                <div class="flex flex-col gap-3 mb-10 max-w-sm mx-auto">
                    <label class="flex items-center p-4 border-2 border-slate-200 rounded-xl cursor-pointer hover:bg-slate-50 transition-all text-xl">
                        <input type="radio" name="momento_glucosa" value="ayunas" class="w-6 h-6 mr-4" checked> En Ayunas
                    </label>
                    <label class="flex items-center p-4 border-2 border-slate-200 rounded-xl cursor-pointer hover:bg-slate-50 transition-all text-xl">
                        <input type="radio" name="momento_glucosa" value="antes_comer" class="w-6 h-6 mr-4"> Antes de comer
                    </label>
                    <label class="flex items-center p-4 border-2 border-slate-200 rounded-xl cursor-pointer hover:bg-slate-50 transition-all text-xl">
                        <input type="radio" name="momento_glucosa" value="despues_comer" class="w-6 h-6 mr-4"> 2h después de comer
                    </label>
                </div>
                <div class="flex justify-between">
                    <button type="button" onclick="prevStep(1)" class="px-8 py-4 bg-slate-200 text-slate-700 text-xl font-bold rounded-xl hover:bg-slate-300">&larr; Atrás</button>
                    <button type="button" onclick="nextStep(3)" class="px-8 py-4 bg-blue-600 text-white text-xl font-bold rounded-xl hover:bg-blue-700 shadow-md">Siguiente &rarr;</button>
                </div>
            </div>

            <!-- PASO 3: Ritmo y Sueño -->
            <div class="step-container" id="step-3">
                <h2 class="text-3xl font-bold text-slate-800 mb-6 text-center">Ritmo Cardíaco y Sueño</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-10">
                    <div class="text-center">
                        <label class="block text-xl font-bold text-slate-700 mb-4">Pulsaciones (lpm)</label>
                        <input type="number" name="ritmo_cardiaco" inputmode="numeric" placeholder="Ej: 75" 
                               class="text-4xl text-center p-4 border-2 border-slate-300 rounded-xl w-full focus:border-blue-500">
                    </div>
                    <div class="text-center">
                        <label class="block text-xl font-bold text-slate-700 mb-4">Horas de sueño</label>
                        <input type="number" step="0.5" name="horas_sueno" inputmode="decimal" placeholder="Ej: 7.5" 
                               class="text-4xl text-center p-4 border-2 border-slate-300 rounded-xl w-full focus:border-blue-500">
                    </div>
                </div>
                <div class="flex justify-between">
                    <button type="button" onclick="prevStep(2)" class="px-8 py-4 bg-slate-200 text-slate-700 text-xl font-bold rounded-xl hover:bg-slate-300">&larr; Atrás</button>
                    <button type="button" onclick="nextStep(4)" class="px-8 py-4 bg-blue-600 text-white text-xl font-bold rounded-xl hover:bg-blue-700 shadow-md">Siguiente &rarr;</button>
                </div>
            </div>

            <!-- PASO 4: Dolor -->
            <div class="step-container" id="step-4">
                <h2 class="text-3xl font-bold text-slate-800 mb-6 text-center">Nivel de Dolor</h2>
                <p class="text-center text-lg text-slate-600 mb-8">¿Cómo te sientes físicamente hoy?</p>
                
                <div class="flex justify-between items-center gap-2 mb-10 max-w-xl mx-auto">
                    <!-- Nivel 0 -->
                    <div class="text-center">
                        <input type="radio" name="nivel_dolor" value="0" id="dolor-0" class="hidden emoji-radio" checked>
                        <label for="dolor-0" class="block cursor-pointer p-3 border-2 border-transparent rounded-2xl transition-all">
                            <div class="text-5xl mb-2">😀</div>
                            <div class="text-sm font-bold text-slate-600">Sin dolor</div>
                        </label>
                    </div>
                    <!-- Nivel 1 -->
                    <div class="text-center">
                        <input type="radio" name="nivel_dolor" value="1" id="dolor-1" class="hidden emoji-radio">
                        <label for="dolor-1" class="block cursor-pointer p-3 border-2 border-transparent rounded-2xl transition-all">
                            <div class="text-5xl mb-2">🙂</div>
                            <div class="text-sm font-bold text-slate-600">Leve</div>
                        </label>
                    </div>
                    <!-- Nivel 2 -->
                    <div class="text-center">
                        <input type="radio" name="nivel_dolor" value="2" id="dolor-2" class="hidden emoji-radio">
                        <label for="dolor-2" class="block cursor-pointer p-3 border-2 border-transparent rounded-2xl transition-all">
                            <div class="text-5xl mb-2">😐</div>
                            <div class="text-sm font-bold text-slate-600">Moderado</div>
                        </label>
                    </div>
                    <!-- Nivel 3 -->
                    <div class="text-center">
                        <input type="radio" name="nivel_dolor" value="3" id="dolor-3" class="hidden emoji-radio">
                        <label for="dolor-3" class="block cursor-pointer p-3 border-2 border-transparent rounded-2xl transition-all">
                            <div class="text-5xl mb-2">🙁</div>
                            <div class="text-sm font-bold text-slate-600">Fuerte</div>
                        </label>
                    </div>
                    <!-- Nivel 4 -->
                    <div class="text-center">
                        <input type="radio" name="nivel_dolor" value="4" id="dolor-4" class="hidden emoji-radio">
                        <label for="dolor-4" class="block cursor-pointer p-3 border-2 border-transparent rounded-2xl transition-all">
                            <div class="text-5xl mb-2">😣</div>
                            <div class="text-sm font-bold text-slate-600">Insoportable</div>
                        </label>
                    </div>
                </div>

                <div class="flex justify-between mt-8">
                    <button type="button" onclick="prevStep(3)" class="px-8 py-4 bg-slate-200 text-slate-700 text-xl font-bold rounded-xl hover:bg-slate-300">&larr; Atrás</button>
                    <button type="submit" class="px-8 py-4 bg-green-600 text-white text-xl font-bold rounded-xl hover:bg-green-700 shadow-md">✓ Guardar Registro</button>
                </div>
            </div>

        </form>
    </div>
</div>

<script>
    const totalSteps = 4;
    function updateProgress(step) {
        document.getElementById('step-counter').innerText = `Paso ${step} de ${totalSteps}`;
        document.getElementById('progress-bar').style.width = `${(step / totalSteps) * 100}%`;
    }

    function showStep(step) {
        document.querySelectorAll('.step-container').forEach(el => el.classList.remove('active'));
        document.getElementById(`step-${step}`).classList.add('active');
        updateProgress(step);
    }

    function nextStep(step) {
        // En una app real podríamos validar campos aquí antes de avanzar
        showStep(step);
    }

    function prevStep(step) {
        showStep(step);
    }
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
