<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<style>
    .touch-min {
        min-height: 48px;
        min-width: 48px;
    }
</style>

<div class="max-w-xl mx-auto px-4 sm:px-6 py-8 sm:py-12">
    <div class="bg-white p-6 sm:p-8 rounded-3xl shadow-card border border-slate-200/80 text-center">
        
        <!-- Icono de Advertencia Clínica -->
        <div class="w-16 h-16 bg-amber-50 text-amber-600 rounded-3xl flex items-center justify-center text-4xl mx-auto mb-4 shadow-sm">
            ⚠️
        </div>

        <h1 class="text-2xl sm:text-3xl font-black text-slate-800 tracking-tight mb-2">
            Confirmación de Signos Vitales
        </h1>
        
        <p class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-6">
            Paciente: <span class="text-blue-600 font-extrabold"><?= e($paciente['nombre'] ?? 'Adulto Mayor') ?></span>
        </p>

        <!-- Mensaje de Alerta -->
        <div class="bg-amber-50/80 border-2 border-amber-200 rounded-2xl p-4 sm:p-5 mb-6 text-left">
            <div class="flex items-start space-x-3">
                <span class="text-xl">🩺</span>
                <div>
                    <h3 class="text-sm font-bold text-amber-900 mb-1">Aviso Clínico Importante</h3>
                    <p class="text-xs sm:text-sm text-amber-800 leading-relaxed font-medium">
                        <?= e($mensajeAviso) ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Resumen de Datos a Registrar -->
        <div class="bg-slate-50 rounded-2xl p-4 border border-slate-200 text-left space-y-2 mb-8">
            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Valores Ingresados</h4>
            <div class="grid grid-cols-2 gap-2 text-xs">
                <?php if (!empty($datos['presion'])): ?>
                    <div class="p-2 bg-white rounded-xl border border-slate-200">
                        <span class="text-slate-400 font-semibold block">Presión</span>
                        <strong class="text-sm font-mono text-slate-800"><?= e($datos['presion']) ?> mmHg</strong>
                    </div>
                <?php endif; ?>

                <?php if (!empty($datos['glucosa'])): ?>
                    <div class="p-2 bg-white rounded-xl border border-slate-200">
                        <span class="text-slate-400 font-semibold block">Glucosa</span>
                        <strong class="text-sm font-mono text-slate-800"><?= e((string)$datos['glucosa']) ?> mg/dL</strong>
                    </div>
                <?php endif; ?>

                <?php if (!empty($datos['ritmo_cardiaco'])): ?>
                    <div class="p-2 bg-white rounded-xl border border-slate-200">
                        <span class="text-slate-400 font-semibold block">Pulso</span>
                        <strong class="text-sm font-mono text-slate-800"><?= e((string)$datos['ritmo_cardiaco']) ?> lpm</strong>
                    </div>
                <?php endif; ?>

                <?php if (!empty($datos['peso'])): ?>
                    <div class="p-2 bg-white rounded-xl border border-slate-200">
                        <span class="text-slate-400 font-semibold block">Peso</span>
                        <strong class="text-sm font-mono text-slate-800"><?= e((string)$datos['peso']) ?> kg</strong>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Formulario de Confirmación -->
        <form action="<?= APP_URL ?>/salud/registrar" method="POST" class="space-y-3">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <input type="hidden" name="confirmado_por_usuario" value="1">
            
            <?php foreach ($datos as $key => $value): ?>
                <?php if (!in_array($key, ['csrf_token', 'confirmado_por_usuario'], true)): ?>
                    <input type="hidden" name="<?= e((string)$key) ?>" value="<?= e((string)$value) ?>">
                <?php endif; ?>
            <?php endforeach; ?>

            <div class="flex flex-col-reverse sm:flex-row gap-3 justify-center">
                <a href="<?= APP_URL ?>/salud" class="tap-bounce touch-min w-full sm:w-1/2 py-3.5 px-6 bg-slate-100 hover:bg-slate-200 active:bg-slate-300 text-slate-700 font-bold rounded-2xl text-sm flex items-center justify-center transition border border-slate-200">
                    ← Corregir Valores
                </a>
                <button type="submit" class="tap-bounce touch-min w-full sm:w-1/2 py-3.5 px-6 bg-amber-600 hover:bg-amber-700 active:bg-amber-800 text-white font-black rounded-2xl text-sm shadow-md shadow-amber-600/20 flex items-center justify-center transition">
                    ✓ Confirmar y Guardar
                </button>
            </div>
        </form>

    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
