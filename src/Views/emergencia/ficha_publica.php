<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emergencia - Ficha Médica de <?= e($paciente['nombre']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-red-50 text-slate-900 font-sans min-h-screen p-4 sm:p-8 flex items-center justify-center">
    
    <div class="max-w-2xl w-full bg-white rounded-3xl shadow-2xl overflow-hidden border-2 border-red-200 relative">
        
        <!-- Red Header Banner -->
        <div class="bg-red-600 p-6 text-center text-white relative">
            <!-- Icon -->
            <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center text-red-600 text-3xl font-black mx-auto mb-3 shadow-lg">
                +
            </div>
            <h1 class="text-2xl font-black uppercase tracking-widest">Alerta Médica</h1>
            <p class="text-red-100 font-semibold mt-1">Ficha de Emergencia Oficial</p>
        </div>

        <!-- Body Data -->
        <div class="p-6 sm:p-8 space-y-8">
            
            <!-- Datos Personales Centrados -->
            <div class="text-center">
                <h2 class="text-3xl font-black text-slate-800"><?= e($paciente['nombre'] . ' ' . $paciente['apellidos']) ?></h2>
                <p class="text-slate-500 font-medium mt-1">Nacido el: <?= date('d/m/Y', strtotime($paciente['fecha_nacimiento'])) ?></p>
            </div>

            <hr class="border-red-100 border-2">

            <!-- Datos Clínicos Críticos -->
            <div>
                <h3 class="text-red-600 font-bold uppercase tracking-wider mb-4 flex items-center gap-2">
                    <span class="text-xl">⚠️</span> Información Crítica
                </h3>
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200">
                        <span class="text-xs font-bold text-slate-500 uppercase">Tipo de Sangre</span>
                        <p class="text-2xl font-black text-red-700"><?= e($paciente['tipo_sangre'] ?: 'N/D') ?></p>
                    </div>
                    <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200">
                        <span class="text-xs font-bold text-slate-500 uppercase">Alergias</span>
                        <p class="text-md font-bold text-slate-800 leading-tight"><?= e($paciente['alergias'] ?: 'Sin alergias registradas') ?></p>
                    </div>
                    <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 col-span-2">
                        <span class="text-xs font-bold text-slate-500 uppercase">Enfermedades Crónicas</span>
                        <p class="text-md font-bold text-slate-800 leading-tight"><?= e($paciente['enfermedades_cronicas'] ?: 'Ninguna') ?></p>
                    </div>
                </div>
            </div>

            <!-- Contactos de Emergencia -->
            <?php if (!empty($contactos)): ?>
            <div>
                <h3 class="text-blue-600 font-bold uppercase tracking-wider mb-4 flex items-center gap-2">
                    <span class="text-xl">📞</span> Llamar a Familiares
                </h3>
                <div class="space-y-3">
                    <?php foreach ($contactos as $c): ?>
                        <div class="flex justify-between items-center p-4 bg-blue-50 rounded-2xl border border-blue-100">
                            <div>
                                <p class="text-md font-black text-slate-800"><?= e($c['nombre']) ?></p>
                                <span class="text-sm text-slate-600 font-semibold"><?= e($c['parentesco']) ?></span>
                            </div>
                            <a href="tel:<?= e($c['telefono']) ?>" class="px-5 py-3 bg-blue-600 text-white rounded-xl font-black hover:bg-blue-700 shadow-md">
                                LLAMAR
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Medicamentos -->
            <?php if (!empty($medicamentosActivos)): ?>
            <div>
                <h3 class="text-emerald-600 font-bold uppercase tracking-wider mb-4 flex items-center gap-2">
                    <span class="text-xl">💊</span> Medicación Actual
                </h3>
                <div class="space-y-2">
                    <?php foreach ($medicamentosActivos as $med): ?>
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                            <p class="font-bold text-slate-800"><?= e($med['nombre_comercial']) ?></p>
                            <p class="text-sm text-slate-600">Dosis: <?= e($med['dosis']) ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

        </div>
        
        <!-- Footer -->
        <div class="bg-slate-100 p-4 text-center border-t border-slate-200">
            <p class="text-xs text-slate-500 font-semibold mb-4">Generado por VitalSenior. Los datos mostrados son referenciales y proporcionados por el paciente o tutor.</p>
            <a href="<?= APP_URL ?>/login" class="inline-block px-4 py-2 bg-slate-300 text-slate-800 rounded-lg font-bold hover:bg-slate-400 text-sm">Ver en VitalSenior</a>
        </div>

    </div>
</body>
</html>
