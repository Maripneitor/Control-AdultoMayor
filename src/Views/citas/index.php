<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VitalSenior - Mis Citas</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-900 font-sans min-h-screen">
    <header class="bg-white border-b border-slate-200 sticky top-0 z-30 px-6 py-4 flex justify-between items-center shadow-sm">
        <div class="flex items-center space-x-3">
            <a href="<?= APP_URL ?>/dashboard" class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center text-white font-bold text-xl hover:bg-blue-700 transition">←</a>
            <div>
                <h1 class="text-lg font-bold text-slate-800 leading-tight">Agenda Médica y Laboratorios</h1>
                <p class="text-xs text-slate-500">Paciente: <span class="font-semibold text-blue-700"><?= e($paciente['nombre'] . ' ' . $paciente['apellidos']) ?></span></p>
            </div>
        </div>
        <div class="flex items-center space-x-4">
            <a href="<?= APP_URL ?>/logout" class="px-3 py-1.5 bg-red-50 text-red-600 text-xs font-semibold rounded-lg border border-red-200 hover:bg-red-100 transition">Cerrar Sesión</a>
        </div>
    </header>

    <main class="max-w-7xl mx-auto p-6 grid grid-cols-1 lg:grid-cols-3 gap-6">
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
            <div class="lg:col-span-3 mb-4">
                <div class="border-l-4 p-4 rounded-r-lg shadow-sm flex items-center justify-between <?= $alertStyle; ?>" role="alert">
                    <span class="text-base font-medium"><?= htmlspecialchars($flash['message']); ?></span>
                    <button onclick="this.parentElement.remove()" class="text-lg font-bold ml-4 p-1 hover:opacity-75 focus:outline-none">&times;</button>
                </div>
            </div>
        <?php endif; ?>

        <section class="lg:col-span-2 space-y-6">
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                <h2 class="text-lg font-bold text-slate-800 mb-4">Próximos Eventos Médicos</h2>
                
                <div class="space-y-4">
                    <?php if (empty($citas)): ?>
                        <p class="text-sm text-slate-500 text-center py-8">No tienes citas o laboratorios programados.</p>
                    <?php else: ?>
                        <?php foreach ($citas as $cita): ?>
                            <div class="flex flex-col sm:flex-row p-4 bg-slate-50 rounded-xl border-l-4 <?= $cita['tipo_evento'] === 'laboratorio' ? 'border-purple-500' : 'border-blue-500' ?> border-slate-200 hover:border-slate-300 transition-colors">
                                <div class="flex flex-col justify-center sm:w-1/4 mb-3 sm:mb-0 sm:border-r border-slate-200 sm:pr-4">
                                    <span class="text-xs font-bold uppercase text-slate-500 mb-1 text-center sm:text-left"><?= date('D, d M', strtotime($cita['fecha_hora'])) ?></span>
                                    <span class="text-2xl font-black text-slate-800 text-center sm:text-left"><?= date('H:i', strtotime($cita['fecha_hora'])) ?></span>
                                </div>
                                <div class="sm:w-3/4 sm:pl-4 flex flex-col justify-center">
                                    <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded-full w-max mb-1 <?= $cita['tipo_evento'] === 'laboratorio' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' ?>">
                                        <?= e($cita['tipo_evento']) ?>
                                    </span>
                                    <h3 class="font-bold text-slate-800 text-lg"><?= e($cita['titulo']) ?></h3>
                                    <?php if ($cita['tipo_evento'] === 'laboratorio'): ?>
                                        <p class="text-xs font-semibold text-amber-600 mt-1 mb-1">⚠️ Requiere ayuno previo (8-12 horas)</p>
                                    <?php endif; ?>
                                    <p class="text-sm text-slate-600 mt-1">Lugar: <?= e($cita['lugar']) ?></p>
                                    <?php if (!empty($cita['doctor_nombre'])): ?>
                                        <p class="text-xs text-slate-500 mt-1">Con: Dr(a). <?= e($cita['doctor_nombre']) ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <section class="bg-blue-50 p-6 rounded-2xl border border-blue-100 shadow-sm h-fit sticky top-24">
            <h2 class="text-md font-bold text-blue-900 mb-4">Agendar Nuevo Evento</h2>
            
            <form action="<?= APP_URL ?>/citas/crear" method="POST" class="space-y-4">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Tipo de Evento</label>
                    <select name="tipo_evento" class="w-full rounded-lg border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="cita_medica">Cita Médica General / Especialidad</option>
                        <option value="laboratorio">Análisis de Laboratorio / Imagen</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Título / Motivo</label>
                    <input type="text" name="titulo" required class="w-full rounded-lg border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Ej. Revisión Cardiológica">
                </div>
                
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Lugar / Clínica</label>
                    <input type="text" name="lugar" required class="w-full rounded-lg border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Ej. Hospital Central, Consultorio 5">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Fecha y Hora</label>
                    <input type="datetime-local" name="fecha_hora" required class="w-full rounded-lg border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Médico Tratante (Opcional)</label>
                    <select name="doctor_id" class="w-full rounded-lg border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">-- Sin asignar --</option>
                        <?php foreach ($doctores as $dr): ?>
                            <option value="<?= e((string)$dr['id']) ?>">Dr. <?= e($dr['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Notas adicionales</label>
                    <textarea name="notas" rows="2" class="w-full rounded-lg border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Ej. Llevar resultados anteriores..."></textarea>
                </div>

                <button type="submit" class="w-full py-2.5 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 transition">
                    Guardar en Agenda
                </button>
            </form>
        </section>
    </main>
</body>
</html>
