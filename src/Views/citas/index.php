<?php
// Vista principal: Agenda Médica y Línea de Tiempo
$filtroActual = $filtro ?? 'proximas';
$puedeEditar = $puedeEditar ?? false;

// Helper para formato en español
function formatFechaCita(string $fechaHora): array {
    $ts = strtotime($fechaHora);
    $diasSemana = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
    $meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
    
    return [
        'dia_semana' => $diasSemana[(int)date('w', $ts)],
        'dia_numero' => date('d', $ts),
        'mes' => $meses[(int)date('n', $ts) - 1],
        'ano' => date('Y', $ts),
        'hora' => date('H:i', $ts),
        'timestamp' => $ts,
        'es_hoy' => date('Y-m-d', $ts) === date('Y-m-d')
    ];
}

function getTipoBadge(string $tipo): array {
    switch ($tipo) {
        case 'laboratorio_estudio':
        case 'laboratorio':
            return [
                'label' => 'Laboratorio / Estudio',
                'icon' => '🧪',
                'bg' => 'bg-purple-50 text-purple-700 border-purple-200/80',
                'dot' => 'bg-purple-500'
            ];
        case 'terapia':
            return [
                'label' => 'Terapia / Rehabilitación',
                'icon' => '🏃‍♂️',
                'bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200/80',
                'dot' => 'bg-emerald-500'
            ];
        case 'resurtido_receta':
            return [
                'label' => 'Resurtido de Receta',
                'icon' => '💊',
                'bg' => 'bg-amber-50 text-amber-700 border-amber-200/80',
                'dot' => 'bg-amber-500'
            ];
        case 'consulta':
        case 'cita_medica':
        default:
            return [
                'label' => 'Consulta Médica',
                'icon' => '🩺',
                'bg' => 'bg-blue-50 text-blue-700 border-blue-200/80',
                'dot' => 'bg-blue-500'
            ];
    }
}
?>

<div class="w-full max-w-7xl mx-auto px-4 sm:px-6 md:px-8 py-6 sm:py-8">
    
    <!-- Encabezado de la Sección -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 pb-4 border-b border-slate-200">
        <div class="flex items-center gap-3">
            <a href="<?= APP_URL ?>/dashboard" class="tap-bounce touch-target w-11 h-11 bg-white border border-slate-200 hover:bg-slate-100 rounded-2xl flex items-center justify-center text-slate-700 font-bold shadow-xs transition" title="Regresar al Dashboard">
                ←
            </a>
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-xl sm:text-2xl font-black text-slate-800 tracking-tight">Agenda y Citas Médicas</h1>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800">
                        <?= count($citas) ?>
                    </span>
                </div>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                    Paciente activo: <span class="font-bold text-blue-700"><?= e($paciente['nombre'] . ' ' . $paciente['apellidos']) ?></span>
                </p>
            </div>
        </div>

        <?php if ($puedeEditar): ?>
            <button onclick="document.getElementById('modal-crear-cita').classList.remove('hidden')" class="tap-bounce touch-target inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-bold text-sm px-5 py-3 rounded-2xl shadow-md shadow-blue-500/20 transition cursor-pointer">
                <span class="text-lg leading-none">+</span>
                <span>Agendar Nueva Cita</span>
            </button>
        <?php endif; ?>
    </div>

    <!-- Flash Notifications -->
    <?php if (!empty($flash)): ?>
        <?php 
            $colors = [
                'success' => 'bg-emerald-50 border-emerald-500 text-emerald-800',
                'warning' => 'bg-amber-50 border-amber-500 text-amber-800',
                'danger'  => 'bg-rose-50 border-rose-500 text-rose-800',
                'info'    => 'bg-blue-50 border-blue-500 text-blue-800'
            ];
            $alertStyle = $colors[$flash['type']] ?? $colors['info'];
        ?>
        <div class="mb-6">
            <div class="border-l-4 p-4 rounded-r-2xl shadow-subtle flex items-center justify-between <?= $alertStyle; ?>" role="alert">
                <span class="text-sm font-semibold"><?= htmlspecialchars($flash['message']); ?></span>
                <button onclick="this.parentElement.remove()" class="text-lg font-bold ml-4 p-1 hover:opacity-75 focus:outline-none">&times;</button>
            </div>
        </div>
    <?php endif; ?>

    <!-- Layout Principal: Filtros + Timeline + Sidebar rápido -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Columna Izquierda / Principal: Filtros y Timeline (8 cols en desktop) -->
        <div class="lg:col-span-8 space-y-6">
            
            <!-- Barra de Filtros Rápidos (Pill Buttons Táctiles con overflow horizontal suave) -->
            <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none">
                <a href="<?= APP_URL ?>/citas?filtro=proximas" 
                   class="tap-bounce touch-target px-4 py-2.5 rounded-2xl text-xs sm:text-sm font-bold transition-all flex items-center gap-1.5 whitespace-nowrap <?= $filtroActual === 'proximas' ? 'bg-blue-600 text-white shadow-md shadow-blue-600/20' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-100' ?>">
                    <span>📅</span>
                    <span>Próximas</span>
                </a>
                <a href="<?= APP_URL ?>/citas?filtro=estudios" 
                   class="tap-bounce touch-target px-4 py-2.5 rounded-2xl text-xs sm:text-sm font-bold transition-all flex items-center gap-1.5 whitespace-nowrap <?= $filtroActual === 'estudios' ? 'bg-purple-600 text-white shadow-md shadow-purple-600/20' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-100' ?>">
                    <span>🧪</span>
                    <span>Estudios y Lab</span>
                </a>
                <a href="<?= APP_URL ?>/citas?filtro=pasadas" 
                   class="tap-bounce touch-target px-4 py-2.5 rounded-2xl text-xs sm:text-sm font-bold transition-all flex items-center gap-1.5 whitespace-nowrap <?= $filtroActual === 'pasadas' ? 'bg-slate-800 text-white shadow-md shadow-slate-800/20' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-100' ?>">
                    <span>✓</span>
                    <span>Historial / Pasadas</span>
                </a>
                <a href="<?= APP_URL ?>/citas?filtro=todos" 
                   class="tap-bounce touch-target px-4 py-2.5 rounded-2xl text-xs sm:text-sm font-bold transition-all flex items-center gap-1.5 whitespace-nowrap <?= $filtroActual === 'todos' ? 'bg-blue-900 text-white shadow-md shadow-blue-900/20' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-100' ?>">
                    <span>📋</span>
                    <span>Todas</span>
                </a>
            </div>

            <!-- Timeline Container -->
            <?php if (empty($citas)): ?>
                <div class="bg-white rounded-3xl p-8 sm:p-12 text-center border border-slate-200 shadow-card">
                    <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-3xl flex items-center justify-center text-3xl mx-auto mb-4">
                        📅
                    </div>
                    <h3 class="text-lg font-bold text-slate-800 mb-1">No hay eventos para este filtro</h3>
                    <p class="text-xs sm:text-sm text-slate-500 max-w-sm mx-auto mb-6">
                        <?= $filtroActual === 'proximas' ? 'No tienes citas o estudios médicos pendientes por atender.' : 'No se encontraron registros bajo el criterio seleccionado.' ?>
                    </p>
                    <?php if ($puedeEditar): ?>
                        <button onclick="document.getElementById('modal-crear-cita').classList.remove('hidden')" class="tap-bounce touch-target inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs sm:text-sm px-5 py-3 rounded-2xl shadow-md shadow-blue-500/20 transition cursor-pointer">
                            + Agendar Nueva Cita
                        </button>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="relative pl-4 sm:pl-6 space-y-6 before:absolute before:left-[21px] sm:before:left-[29px] before:top-3 before:bottom-3 before:w-0.5 before:bg-slate-200">
                    <?php foreach ($citas as $cita): 
                        $f = formatFechaCita($cita['fecha_hora']);
                        $tipoInfo = getTipoBadge($cita['tipo_evento']);
                        $estado = $cita['estado'] ?? 'programada';
                        $esPasada = $f['timestamp'] < time();
                        $estaCompletada = $estado === 'completada' || !empty($cita['realizado']);
                        $estaCancelada = $estado === 'cancelada';

                        // Generación de Enlace Google Calendar
                        $gStart = gmdate('Ymd\THis\Z', $f['timestamp']);
                        $gEnd = gmdate('Ymd\THis\Z', $f['timestamp'] + 3600);
                        $gTitle = urlencode($cita['titulo'] . ' (VitalSenior)');
                        $gDetails = urlencode("Tipo: {$tipoInfo['label']}\n" . (!empty($cita['preparacion_previa']) ? "Preparación: {$cita['preparacion_previa']}\n" : "") . (!empty($cita['notas']) ? "Notas: {$cita['notas']}\n" : ""));
                        $gLocation = urlencode($cita['lugar']);
                        $gCalUrl = "https://calendar.google.com/calendar/render?action=TEMPLATE&text={$gTitle}&dates={$gStart}/{$gEnd}&details={$gDetails}&location={$gLocation}";

                        // URL de Mapa
                        $mapQuery = !empty($cita['ubicacion_mapa']) ? $cita['ubicacion_mapa'] : 'https://www.google.com/maps/search/?api=1&query=' . urlencode($cita['lugar']);
                    ?>
                        <div class="relative flex items-start gap-4 sm:gap-6 group">
                            
                            <!-- Timeline Indicator Node -->
                            <div class="relative z-10 flex-shrink-0 w-8 h-8 sm:w-10 sm:h-10 rounded-2xl flex items-center justify-center font-bold text-sm shadow-xs transition-transform group-hover:scale-105 <?= $estaCompletada ? 'bg-emerald-600 text-white' : ($estaCancelada ? 'bg-rose-500 text-white' : ($f['es_hoy'] ? 'bg-blue-600 text-white ring-4 ring-blue-100' : 'bg-white border-2 border-blue-500 text-blue-600')) ?>">
                                <?php if ($estaCompletada): ?>
                                    ✓
                                <?php elseif ($estaCancelada): ?>
                                    ✕
                                <?php else: ?>
                                    <?= $f['dia_numero'] ?>
                                <?php endif; ?>
                            </div>

                            <!-- Card de Cita / Evento -->
                            <div class="flex-1 bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/80 shadow-card hover:shadow-md transition-all">
                                
                                <!-- Top Bar: Tipo + Estado + Fecha/Hora Tabular -->
                                <div class="flex flex-wrap items-center justify-between gap-2 mb-3">
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-bold border <?= $tipoInfo['bg'] ?>">
                                            <span><?= $tipoInfo['icon'] ?></span>
                                            <span><?= $tipoInfo['label'] ?></span>
                                        </span>
                                        
                                        <?php if ($estaCompletada): ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">
                                                Completada
                                            </span>
                                        <?php elseif ($estaCancelada): ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-100 text-rose-800">
                                                Cancelada
                                            </span>
                                        <?php elseif ($f['es_hoy']): ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800 animate-pulse">
                                                ¡Hoy!
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Fecha y Hora Tabular Destacada -->
                                    <div class="flex items-center gap-1.5 font-mono text-xs sm:text-sm font-bold text-slate-700 bg-slate-100 px-3 py-1 rounded-xl">
                                        <span>📅 <?= $f['dia_semana'] ?> <?= $f['dia_numero'] ?> <?= $f['mes'] ?></span>
                                        <span class="text-slate-400">|</span>
                                        <span class="text-blue-600 font-extrabold text-sm sm:text-base"><?= $f['hora'] ?></span>
                                    </div>
                                </div>

                                <!-- Título Principal -->
                                <h3 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight <?= $estaCancelada ? 'line-through text-slate-400' : '' ?>">
                                    <?= e($cita['titulo']) ?>
                                </h3>

                                <!-- Indicación de Preparación Previa / Ayuno (Anti-slop notice) -->
                                <?php if (!empty($cita['preparacion_previa']) || $cita['tipo_evento'] === 'laboratorio' || $cita['tipo_evento'] === 'laboratorio_estudio'): ?>
                                    <div class="mt-3 p-3 bg-amber-50/90 border-l-4 border-amber-400 rounded-r-2xl flex items-start gap-2.5">
                                        <span class="text-base leading-none">⚠️</span>
                                        <div>
                                            <p class="text-xs font-bold text-amber-900">Indicaciones de Preparación:</p>
                                            <p class="text-xs text-amber-800 font-medium mt-0.5">
                                                <?= e($cita['preparacion_previa'] ?: 'Requiere ayuno previo de 8 a 12 horas. No ingerir alimentos ni bebidas azucaradas.') ?>
                                            </p>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <!-- Detalles Clínicos: Lugar, Doctor, Acompañante -->
                                <div class="mt-3.5 grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs sm:text-sm text-slate-600">
                                    <div class="flex items-center gap-2">
                                        <span class="text-slate-400 text-sm">📍</span>
                                        <span class="font-medium truncate"><?= e($cita['lugar']) ?></span>
                                    </div>
                                    
                                    <?php if (!empty($cita['doctor_nombre'])): ?>
                                        <div class="flex items-center gap-2">
                                            <span class="text-slate-400 text-sm">👨‍⚕️</span>
                                            <span class="font-medium">Dr(a). <?= e($cita['doctor_nombre']) ?></span>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (!empty($cita['familiar_nombre'])): ?>
                                        <div class="flex items-center gap-2">
                                            <span class="text-slate-400 text-sm">👤</span>
                                            <span class="font-medium text-slate-500">Acompañante: <?= e($cita['familiar_nombre']) ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Notas Adicionales -->
                                <?php if (!empty($cita['notas'])): ?>
                                    <p class="mt-3 text-xs text-slate-500 bg-slate-50 p-2.5 rounded-xl border border-slate-100 italic">
                                        "<?= e($cita['notas']) ?>"
                                    </p>
                                <?php endif; ?>

                                <!-- Acciones Táctiles Inferiores (Design Engineering) -->
                                <div class="mt-5 pt-4 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3">
                                    
                                    <!-- Botones de Calendario y Mapa -->
                                    <div class="flex flex-wrap items-center gap-2">
                                        <a href="<?= e($mapQuery) ?>" target="_blank" rel="noopener noreferrer" class="tap-bounce touch-target inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 transition">
                                            <span>🗺️</span>
                                            <span>Abrir en Mapa</span>
                                        </a>

                                        <!-- Menú Calendario (Google Cal + .ICS) -->
                                        <a href="<?= $gCalUrl ?>" target="_blank" rel="noopener noreferrer" class="tap-bounce touch-target inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold bg-blue-50 hover:bg-blue-100 text-blue-700 transition" title="Agregar a Google Calendar">
                                            <span>📅</span>
                                            <span>Google Cal</span>
                                        </a>

                                        <a href="<?= APP_URL ?>/citas/ics?id=<?= $cita['id'] ?>" class="tap-bounce touch-target inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold bg-slate-50 hover:bg-slate-100 border border-slate-200 text-slate-600 transition" title="Descargar archivo iCalendar (.ics)">
                                            <span>📥</span>
                                            <span>.ICS</span>
                                        </a>
                                    </div>

                                    <!-- Acciones de Estado (Completar / Cancelar) -->
                                    <?php if ($puedeEditar && $estado === 'programada'): ?>
                                        <div class="flex items-center gap-2">
                                            <!-- Botón Completar -->
                                            <form action="<?= APP_URL ?>/citas/completar" method="POST" onsubmit="return confirm('¿Confirmas que esta cita médica o estudio fue realizada?')">
                                                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                                <input type="hidden" name="cita_id" value="<?= $cita['id'] ?>">
                                                <button type="submit" class="tap-bounce touch-target inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm transition cursor-pointer">
                                                    <span>✓</span>
                                                    <span>Completar</span>
                                                </button>
                                            </form>

                                            <!-- Botón Cancelar -->
                                            <form action="<?= APP_URL ?>/citas/cancelar" method="POST" onsubmit="return confirm('¿Estás seguro de cancelar esta cita médica?')">
                                                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                                <input type="hidden" name="cita_id" value="<?= $cita['id'] ?>">
                                                <button type="submit" class="tap-bounce touch-target px-3 py-2 rounded-xl text-xs font-bold bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 transition cursor-pointer" title="Cancelar Cita">
                                                    <span>✕</span>
                                                </button>
                                            </form>
                                        </div>
                                    <?php endif; ?>

                                </div>

                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </div>

        <!-- Columna Derecha: Panel de Resumen Rápido y Doctores (4 cols en desktop) -->
        <div class="lg:col-span-4 space-y-6">
            
            <!-- Tarjeta Informativa de Preparación Médica -->
            <div class="bg-gradient-to-br from-blue-900 to-indigo-900 rounded-3xl p-6 text-white shadow-card">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-2xl bg-white/10 flex items-center justify-center text-xl">
                        💡
                    </div>
                    <div>
                        <h3 class="font-bold text-sm sm:text-base">Guía para el Cuidador</h3>
                        <p class="text-xs text-blue-200">Recomendaciones para citas</p>
                    </div>
                </div>
                <ul class="text-xs space-y-2 text-blue-100/90 leading-relaxed list-disc list-inside">
                    <li>Verifica si el estudio requiere <strong>ayuno estricto</strong> antes de desayunar.</li>
                    <li>Lleva el <strong>expediente físico</strong> o muestra el código QR de VitalSenior.</li>
                    <li>Registra las indicaciones médicas en notas tras concluir la consulta.</li>
                </ul>
            </div>

            <!-- Directorio Rápido de Doctores Registrados -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-card">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-slate-800 text-sm sm:text-base">Médicos Registrados</h3>
                    <a href="<?= APP_URL ?>/expediente#doctores" class="text-xs font-bold text-blue-600 hover:text-blue-700">Ver todos →</a>
                </div>

                <?php if (empty($doctores)): ?>
                    <p class="text-xs text-slate-400 py-4 text-center">No hay médicos vinculados al expediente.</p>
                <?php else: ?>
                    <div class="space-y-3">
                        <?php foreach (array_slice($doctores, 0, 4) as $doc): ?>
                            <div class="p-3 bg-slate-50 rounded-2xl border border-slate-100 flex items-center justify-between">
                                <div class="min-w-0 pr-2">
                                    <p class="text-xs font-bold text-slate-800 truncate">Dr(a). <?= e($doc['nombre']) ?></p>
                                    <p class="text-[11px] text-slate-500 truncate"><?= e($doc['especialidad'] ?: 'Médico General') ?></p>
                                </div>
                                <?php if (!empty($doc['telefono_contacto'])): ?>
                                    <a href="tel:<?= e($doc['telefono_contacto']) ?>" class="tap-bounce touch-target w-9 h-9 rounded-xl bg-blue-100 hover:bg-blue-200 text-blue-700 flex items-center justify-center text-xs font-bold transition flex-shrink-0" title="Llamar al médico">
                                        📞
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

        </div>

    </div>

</div>

<!-- Modal / Drawer para Agendar Nueva Cita (Accesible y Responsivo) -->
<?php if ($puedeEditar): ?>
<div id="modal-crear-cita" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 sm:p-6 overflow-y-auto">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-100 my-8">
        
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
            <div class="flex items-center gap-2.5">
                <div class="w-10 h-10 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center text-xl font-bold">
                    📅
                </div>
                <div>
                    <h2 class="text-lg font-bold text-slate-800">Agendar Evento Médico</h2>
                    <p class="text-xs text-slate-500">Completa los datos de la cita o estudio</p>
                </div>
            </div>
            <button type="button" onclick="document.getElementById('modal-crear-cita').classList.add('hidden')" class="w-9 h-9 rounded-xl bg-slate-100 text-slate-500 hover:bg-slate-200 text-lg font-bold flex items-center justify-center transition cursor-pointer">
                &times;
            </button>
        </div>

        <form action="<?= APP_URL ?>/citas/crear" method="POST" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <input type="hidden" name="paciente_id" value="<?= $paciente['id'] ?>">

            <!-- Tipo de Evento -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Tipo de Evento *</label>
                <div class="grid grid-cols-2 gap-2">
                    <label class="tap-bounce flex items-center gap-2 p-3 bg-slate-50 border border-slate-200 rounded-2xl cursor-pointer hover:bg-blue-50/50 has-checked:border-blue-500 has-checked:bg-blue-50/70">
                        <input type="radio" name="tipo_evento" value="consulta" checked class="text-blue-600 focus:ring-blue-500">
                        <span class="text-xs font-bold text-slate-800">🩺 Consulta</span>
                    </label>
                    <label class="tap-bounce flex items-center gap-2 p-3 bg-slate-50 border border-slate-200 rounded-2xl cursor-pointer hover:bg-purple-50/50 has-checked:border-purple-500 has-checked:bg-purple-50/70">
                        <input type="radio" name="tipo_evento" value="laboratorio_estudio" class="text-purple-600 focus:ring-purple-500">
                        <span class="text-xs font-bold text-slate-800">🧪 Laboratorio</span>
                    </label>
                    <label class="tap-bounce flex items-center gap-2 p-3 bg-slate-50 border border-slate-200 rounded-2xl cursor-pointer hover:bg-emerald-50/50 has-checked:border-emerald-500 has-checked:bg-emerald-50/70">
                        <input type="radio" name="tipo_evento" value="terapia" class="text-emerald-600 focus:ring-emerald-500">
                        <span class="text-xs font-bold text-slate-800">🏃‍♂️ Terapia</span>
                    </label>
                    <label class="tap-bounce flex items-center gap-2 p-3 bg-slate-50 border border-slate-200 rounded-2xl cursor-pointer hover:bg-amber-50/50 has-checked:border-amber-500 has-checked:bg-amber-50/70">
                        <input type="radio" name="tipo_evento" value="resurtido_receta" class="text-amber-600 focus:ring-amber-500">
                        <span class="text-xs font-bold text-slate-800">💊 Resurtido</span>
                    </label>
                </div>
            </div>

            <!-- Título / Motivo -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Título o Motivo *</label>
                <input type="text" name="titulo" required placeholder="Ej. Control de Glucosa y Presión / Chequeo Anual" 
                       class="touch-target w-full rounded-2xl border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:ring-blue-500 bg-slate-50/50">
            </div>

            <!-- Fecha y Hora -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Fecha y Hora *</label>
                <input type="datetime-local" name="fecha_hora" required 
                       class="touch-target w-full rounded-2xl border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:ring-blue-500 bg-slate-50/50 font-mono">
            </div>

            <!-- Lugar / Clínica -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Lugar o Consultorio *</label>
                <input type="text" name="lugar" required placeholder="Ej. Hospital San José, Consultorio 204" 
                       class="touch-target w-full rounded-2xl border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:ring-blue-500 bg-slate-50/50">
            </div>

            <!-- Ubicación en Mapa (Opcional) -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Enlace a Google Maps (Opcional)</label>
                <input type="url" name="ubicacion_mapa" placeholder="https://maps.google.com/..." 
                       class="touch-target w-full rounded-2xl border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:ring-blue-500 bg-slate-50/50">
            </div>

            <!-- Médico y Acompañante Responsable en Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Médico Tratante</label>
                    <select name="doctor_id" class="touch-target w-full rounded-2xl border-slate-300 px-3 py-2.5 text-xs sm:text-sm focus:border-blue-500 focus:ring-blue-500 bg-slate-50/50">
                        <option value="">-- Sin asignar --</option>
                        <?php foreach ($doctores as $dr): ?>
                            <option value="<?= $dr['id'] ?>">Dr(a). <?= e($dr['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Familiar / Acompañante</label>
                    <select name="familiar_responsable_id" class="touch-target w-full rounded-2xl border-slate-300 px-3 py-2.5 text-xs sm:text-sm focus:border-blue-500 focus:ring-blue-500 bg-slate-50/50">
                        <option value="">-- Sin asignar --</option>
                        <?php foreach ($cuidadores as $cui): ?>
                            <option value="<?= $cui['id'] ?>"><?= e($cui['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Preparación Previa / Ayuno -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Preparación Previa (Ej. Ayuno)</label>
                <input type="text" id="input-preparacion" name="preparacion_previa" placeholder="Ej. Ayuno de 8 horas, tomar 1 litro de agua..." 
                       class="touch-target w-full rounded-2xl border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:ring-blue-500 bg-slate-50/50">
                <!-- Píldoras de atajo rápido -->
                <div class="flex flex-wrap gap-1.5 mt-2">
                    <button type="button" onclick="document.getElementById('input-preparacion').value='Ayuno estricto de 8 horas'" class="text-[11px] font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 px-2.5 py-1 rounded-lg transition">Ayuno 8h</button>
                    <button type="button" onclick="document.getElementById('input-preparacion').value='Ayuno de 12 horas (Química Sanguínea)'" class="text-[11px] font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 px-2.5 py-1 rounded-lg transition">Ayuno 12h</button>
                    <button type="button" onclick="document.getElementById('input-preparacion').value='Tomar 1 litro de agua 1h antes (Ultrasonido)'" class="text-[11px] font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 px-2.5 py-1 rounded-lg transition">Vejiga llena (1L agua)</button>
                    <button type="button" onclick="document.getElementById('input-preparacion').value='Llevar estudios previos y receta vigente'" class="text-[11px] font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 px-2.5 py-1 rounded-lg transition">Llevar estudios previos</button>
                </div>
            </div>

            <!-- Notas Adicionales -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Notas / Observaciones</label>
                <textarea name="notas" rows="2" placeholder="Instrucciones adicionales, transporte o recordatorios..." 
                          class="w-full rounded-2xl border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:ring-blue-500 bg-slate-50/50"></textarea>
            </div>

            <!-- Botones de Acción -->
            <div class="pt-4 flex items-center justify-end gap-3">
                <button type="button" onclick="document.getElementById('modal-crear-cita').classList.add('hidden')" class="tap-bounce touch-target px-5 py-3 rounded-2xl text-xs sm:text-sm font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 transition cursor-pointer">
                    Cancelar
                </button>
                <button type="submit" class="tap-bounce touch-target px-6 py-3 rounded-2xl text-xs sm:text-sm font-bold bg-blue-600 hover:bg-blue-700 text-white shadow-md shadow-blue-500/20 transition cursor-pointer">
                    Guardar en Agenda
                </button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>
