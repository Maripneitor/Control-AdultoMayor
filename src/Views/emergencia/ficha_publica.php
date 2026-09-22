<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-900 antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
    <title>SOS Emergencia - Ficha Médica de <?= e($paciente['nombre'] ?? 'Paciente') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'system-ui', '-apple-system', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    },
                    boxShadow: {
                        'hero': '0 20px 40px -15px rgba(225, 29, 72, 0.3)',
                        'card': '0 4px 20px -2px rgba(0, 0, 0, 0.08), 0 0 0 1px rgba(226, 232, 240, 0.8)',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-slate-900 text-slate-100 font-sans min-h-full py-6 px-4 sm:px-6 flex items-center justify-center">

    <div class="max-w-xl w-full bg-white text-slate-900 rounded-3xl shadow-hero overflow-hidden border-2 border-rose-500/80 relative">
        
        <!-- Header Rojo de Emergencia Médica -->
        <header class="bg-gradient-to-br from-rose-600 to-red-700 p-6 sm:p-8 text-center text-white relative">
            <div class="inline-flex items-center justify-center w-14 h-14 bg-white text-rose-600 rounded-2xl text-2xl font-black mb-3 shadow-md">
                ✚
            </div>
            <h1 class="text-xl sm:text-2xl font-black uppercase tracking-wider">
                Ficha Médica de Auxilio
            </h1>
            <p class="text-rose-100 text-xs font-semibold mt-0.5 tracking-wide">
                Protocolo de Atención Prehospitalaria • VitalSenior SOS
            </p>
        </header>

        <main class="p-6 sm:p-8 space-y-6">
            <!-- Datos del Adulto Mayor -->
            <?php 
                $nombreCompleto = trim(($paciente['nombre'] ?? '') . ' ' . ($paciente['apellidos'] ?? ''));
                if (empty($nombreCompleto) || $nombreCompleto === 'Por completar') {
                    $nombreCompleto = 'Adulto Mayor (Identificación en curso)';
                }

                $edad = null;
                if (!empty($paciente['fecha_nacimiento'])) {
                    try {
                        $fn = new \DateTime($paciente['fecha_nacimiento']);
                        $hoy = new \DateTime();
                        $edad = $hoy->diff($fn)->y;
                    } catch (\Throwable $e) {}
                }
            ?>
            <div class="text-center border-b border-slate-100 pb-5">
                <span class="text-xs font-bold text-slate-600 uppercase tracking-wider">Paciente</span>
                <h2 class="text-2xl sm:text-3xl font-black text-slate-900 mt-1 tracking-tight">
                    <?= e($nombreCompleto) ?>
                </h2>
                <?php if ($edad !== null): ?>
                    <p class="text-sm font-bold text-blue-700 mt-1">
                        <?= $edad ?> años de edad
                    </p>
                <?php endif; ?>
            </div>

            <!-- Panel de Criticidad Clínica: Sangre & Alergias -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="bg-rose-50/80 p-4 rounded-2xl border border-rose-200">
                    <span class="text-xs font-bold text-rose-700 uppercase tracking-wider block">Grupo Sanguíneo / Rh</span>
                    <p class="text-2xl sm:text-3xl font-mono font-black text-rose-900 mt-1">
                        <?= e($paciente['tipo_sangre'] ?: 'N/D') ?>
                    </p>
                </div>

                <div class="bg-amber-50/80 p-4 rounded-2xl border border-amber-200">
                    <span class="text-xs font-bold text-amber-800 uppercase tracking-wider block">Alergias & RAM</span>
                    <p class="text-sm font-bold text-amber-950 mt-1 leading-snug">
                        <?= e($paciente['alergias'] ?: 'Sin alergias reportadas') ?>
                    </p>
                </div>

                <?php if (!empty($paciente['enfermedades_cronicas'])): ?>
                    <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 sm:col-span-2">
                        <span class="text-xs font-bold text-slate-600 uppercase tracking-wider block">Patologías Crónicas / Dispositivos</span>
                        <p class="text-sm font-semibold text-slate-800 mt-1 leading-relaxed">
                            <?= e($paciente['enfermedades_cronicas']) ?>
                        </p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Contactos Familiares de Auxilio Inmediato (Red Designada) -->
            <?php if (!empty($contactos)): ?>
                <div>
                    <h3 class="text-xs font-bold text-slate-600 uppercase tracking-wider mb-3 flex items-center justify-between">
                        <span>Red de Auxilio Inmediato</span>
                        <span class="text-[10px] text-emerald-600 font-extrabold uppercase">Contactos Directos</span>
                    </h3>
                    <div class="space-y-3">
                        <?php foreach ($contactos as $c): ?>
                            <?php $isPrioritario = !empty($c['es_prioritario']); ?>
                            <div class="flex items-center justify-between p-4 sm:p-5 rounded-2xl border transition-all <?= $isPrioritario ? 'border-emerald-300 bg-emerald-50/50 shadow-sm' : 'border-slate-200 bg-slate-50' ?>">
                                <div class="pr-2">
                                    <div class="flex items-center space-x-2">
                                        <h4 class="font-black text-slate-900 text-sm sm:text-base"><?= e($c['nombre']) ?></h4>
                                        <?php if ($isPrioritario): ?>
                                            <span class="px-2 py-0.5 text-[9px] font-black rounded-full bg-emerald-600 text-white tracking-wider">PRINCIPAL</span>
                                        <?php endif; ?>
                                    </div>
                                    <p class="text-xs text-slate-600 font-semibold mt-0.5"><?= e($c['parentesco'] ?? 'Familiar / Cuidador') ?></p>
                                    <p class="text-xs font-mono font-bold text-slate-700 mt-1 tracking-wide"><?= e($c['telefono']) ?></p>
                                </div>
                                <a href="tel:<?= e($c['telefono']) ?>" class="min-h-[48px] min-w-[48px] px-5 py-3 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white rounded-2xl font-black text-xs uppercase tracking-wider shadow-md shadow-emerald-600/25 transition-all flex items-center justify-center space-x-2 whitespace-nowrap active:scale-95">
                                    <span class="text-base">📞</span>
                                    <span>Llamar</span>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Medicación Activa Vital -->
            <?php if (!empty($medicamentosActivos)): ?>
                <div>
                    <h3 class="text-xs font-bold text-slate-600 uppercase tracking-wider mb-3">
                        Fármacos en Uso Activo
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                        <?php foreach ($medicamentosActivos as $med): ?>
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                                <p class="font-bold text-slate-900 text-xs"><?= e($med['nombre_comercial']) ?></p>
                                <p class="text-[11px] text-slate-600 font-medium">Dosis: <?= e($med['dosis']) ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </main>

        <footer class="bg-slate-50 p-4 text-center border-t border-slate-100 text-[11px] text-slate-600">
            Ficha médica oficial de soporte VitalSenior • Información confidencial para auxilio clínico.
        </footer>
    </div>

</body>
</html>

