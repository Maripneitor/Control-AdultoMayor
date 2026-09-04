<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VitalSenior - Expediente de <?= e($paciente['nombre']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body { background: white !important; }
            header, .no-print { display: none !important; }
            main { padding: 0 !important; grid-template-columns: 1fr !important; }
            .border-slate-200 { border: 1px solid #ccc !important; }
            .shadow-sm { box-shadow: none !important; }
        }
    </style>
</head>
<body id="dashboard-body" class="bg-slate-50 text-slate-900 font-sans min-h-screen transition-opacity duration-700 opacity-0">
    <!-- Barra Superior -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-30 px-6 py-4 flex justify-between items-center shadow-sm">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center text-white font-bold text-xl shadow-md shadow-blue-500/20">+</div>
            <div>
                <h1 class="text-lg font-bold text-slate-800 leading-tight">Expediente Clínico</h1>
                <?php if (count($todosLosPacientes) > 1): ?>
                    <form action="<?= APP_URL ?>/dashboard" method="GET" class="inline-block mt-1">
                        <select name="seleccionar_paciente" onchange="this.form.submit()" class="text-xs font-semibold text-blue-700 bg-blue-50 border border-blue-200 rounded-md py-1 pl-2 pr-6 focus:ring-blue-500 focus:border-blue-500 cursor-pointer">
                            <?php foreach ($todosLosPacientes as $p): ?>
                                <option value="<?= e((string)$p['id']) ?>" <?= $p['id'] == $pacienteActivoId ? 'selected' : '' ?>>
                                    Paciente: <?= e($p['nombre'] . ' ' . $p['apellidos']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                <?php else: ?>
                    <p class="text-xs text-slate-500">Paciente: <span class="font-semibold text-blue-700"><?= e($paciente['nombre'] . ' ' . $paciente['apellidos']) ?></span></p>
                <?php endif; ?>
            </div>
        </div>
        <div class="flex items-center space-x-4 no-print">
            <button onclick="window.print()" class="px-3 py-1.5 bg-slate-100 text-slate-700 text-xs font-semibold rounded-lg border border-slate-300 hover:bg-slate-200 transition">🖨️ Imprimir</button>
            <a href="<?= APP_URL ?>/emergencia?token=<?= e((string)($paciente['qr_token'] ?? '')) ?>" target="_blank" class="px-3 py-1.5 bg-blue-50 text-blue-700 text-xs font-semibold rounded-lg border border-blue-200 hover:bg-blue-100 transition">Ver Ficha QR</a>
            <a href="<?= APP_URL ?>/logout" class="px-3 py-1.5 bg-red-50 text-red-600 text-xs font-semibold rounded-lg border border-red-200 hover:bg-red-100 transition">Cerrar Sesión</a>
        </div>
    </header>

    <main id="accessible-content" class="max-w-7xl mx-auto p-6 grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <?php if (!empty($flash)): ?>
            <?php 
                $colors = [
                    'success' => 'bg-emerald-100 border-emerald-500 text-emerald-800',
                    'warning' => 'bg-amber-100 border-amber-500 text-amber-800',
                    'danger'  => 'bg-rose-100 border-rose-500 text-rose-800',
                    'info'    => 'bg-blue-100 border-blue-500 text-blue-800'
                ];
                $alertStyle = $colors[$flash['type']] ?? $colors['info'];
            ?>
            <div class="lg:col-span-3 mb-4 no-print">
                <div class="border-l-4 p-4 rounded-r-lg shadow-sm flex items-center justify-between <?= $alertStyle; ?>" role="alert">
                    <span class="text-base font-medium"><?= htmlspecialchars($flash['message']); ?></span>
                    <button onclick="this.parentElement.remove()" class="text-lg font-bold ml-4 p-1 hover:opacity-75 focus:outline-none">&times;</button>
                </div>
            </div>
        <?php endif; ?>

        <!-- Columna 1: Información Vital, Contactos y Salud Diaria -->
        <section class="space-y-6">
            <!-- Registro de Salud Diario -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-4">
                <h2 class="text-sm font-bold text-slate-700 uppercase tracking-wide">Registro de Salud Diario</h2>
                <p class="text-xs text-slate-500 mb-2">Presión arterial, glucosa, sueño y dolor.</p>
                <a href="<?= APP_URL ?>/salud/stepper" class="block w-full py-3 bg-blue-600 text-white rounded-xl text-center font-bold text-lg hover:bg-blue-700 shadow-md transition-all">
                    + Registrar Signos de Hoy
                </a>
            </div>

            <!-- Tarjeta Datos Vitales -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-4">
                <h2 class="text-sm font-bold text-slate-700 uppercase tracking-wide">Datos Vitales</h2>
                <div class="grid grid-cols-2 gap-3">
                    <div class="bg-blue-50/50 p-3 rounded-xl border border-blue-100">
                        <span class="text-xs text-blue-600 font-medium block">Tipo de Sangre</span>
                        <span class="text-xl font-black text-blue-900"><?= e($paciente['tipo_sangre'] ?: 'N/D') ?></span>
                    </div>
                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-200">
                        <span class="text-xs text-slate-500 font-medium block">Nacimiento</span>
                        <span class="text-sm font-bold text-slate-800"><?= e($paciente['fecha_nacimiento']) ?></span>
                    </div>
                </div>
                <div>
                    <span class="text-xs font-semibold text-slate-500 block mb-1">Alergias Conocidas</span>
                    <p class="text-sm text-red-600 font-medium bg-red-50 p-2.5 rounded-lg border border-red-100">
                        <?= e($paciente['alergias'] ?: 'Sin alergias registradas') ?>
                    </p>
                </div>
                <div>
                    <span class="text-xs font-semibold text-slate-500 block mb-1">Padecimientos Crónicos</span>
                    <p class="text-sm text-slate-700 bg-slate-50 p-2.5 rounded-lg border border-slate-200">
                        <?= e($paciente['enfermedades_cronicas'] ?: 'Ninguno registrado') ?>
                    </p>
                </div>
            </div>

            <!-- Contactos de Emergencia -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                <h2 class="text-sm font-bold text-slate-700 uppercase tracking-wide mb-3">Contactos Prioritarios</h2>
                <div class="space-y-2">
                    <?php if (empty($contactosEmergencia)): ?>
                        <p class="text-xs text-slate-400 italic">No hay contactos registrados.</p>
                    <?php else: ?>
                        <?php foreach ($contactosEmergencia as $c): ?>
                            <div class="flex justify-between items-center p-3 bg-slate-50 rounded-xl border border-slate-100">
                                <div>
                                    <p class="text-sm font-bold text-slate-800"><?= e($c['nombre']) ?></p>
                                    <span class="text-xs text-slate-500"><?= e($c['parentesco']) ?></span>
                                </div>
                                <a href="tel:<?= e($c['telefono']) ?>" class="px-3 py-1 bg-green-600 text-white rounded-lg text-xs font-semibold hover:bg-green-700">Llamar</a>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- Columna 2: Medicación y Tomas del Día -->
        <section class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-sm font-bold text-slate-700 uppercase tracking-wide">Tomas Programadas (Hoy)</h2>
                    <span class="text-xs font-medium text-blue-600 bg-blue-50 px-2 py-1 rounded-md"><?= date('d/m/Y') ?></span>
                </div>

                <div class="space-y-3">
                    <?php if (empty($tomasDelDia)): ?>
                        <p class="text-sm text-slate-400 text-center py-8">No hay tomas programadas para hoy.</p>
                    <?php else: ?>
                        <?php foreach ($tomasDelDia as $toma): ?>
                            <?php include __DIR__ . '/../medicamentos/partials/card_toma.php'; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 mt-4 flex flex-col gap-2">
                <a href="<?= APP_URL ?>/medicamentos/control" class="block text-center py-2 bg-slate-100 text-sm font-semibold text-slate-700 rounded-lg hover:bg-slate-200">Ver Control por Horarios</a>
                <a href="<?= APP_URL ?>/medicamentos" class="block text-center text-xs font-semibold text-blue-600 hover:underline">Gestionar Catálogo de Medicamentos →</a>
            </div>
        </section>

        <!-- Columna 3: Próximas Citas Médicas y Laboratorios -->
        <section class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-sm font-bold text-slate-700 uppercase tracking-wide">Agenda y Laboratorios</h2>
                    <a href="<?= APP_URL ?>/citas" class="text-xs text-blue-600 font-semibold hover:underline">+ Agendar</a>
                </div>

                <div class="space-y-3">
                    <?php if (empty($proximasCitas)): ?>
                        <p class="text-sm text-slate-400 text-center py-8">No hay eventos ni citas próximas.</p>
                    <?php else: ?>
                        <?php foreach ($proximasCitas as $cita): ?>
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                                <div class="flex justify-between items-start mb-1">
                                    <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded <?= $cita['tipo_evento'] === 'laboratorio' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' ?>">
                                        <?= e($cita['tipo_evento']) ?>
                                    </span>
                                    <span class="text-xs font-medium text-slate-500"><?= date('d M, H:i', strtotime($cita['fecha_hora'])) ?></span>
                                </div>
                                <h3 class="text-sm font-bold text-slate-800"><?= e($cita['titulo']) ?></h3>
                                <p class="text-xs text-slate-600">Lugar: <?= e($cita['lugar']) ?></p>
                                <?php if ($cita['doctor_nombre']): ?>
                                    <p class="text-xs text-slate-500">Dr: <?= e($cita['doctor_nombre']) ?></p>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 mt-4">
                <a href="<?= APP_URL ?>/citas" class="block text-center text-xs font-semibold text-blue-600 hover:underline">Ver Calendario Completo →</a>
            </div>
        </section>
    </main>
    <script>
        // Transición de Login: Fade in effect al cargar el dashboard
        window.addEventListener('DOMContentLoaded', () => {
            requestAnimationFrame(() => {
                const body = document.getElementById('dashboard-body');
                body.classList.remove('opacity-0');
                body.classList.add('opacity-100');
            });
        });
    </script>

</body>
</html>