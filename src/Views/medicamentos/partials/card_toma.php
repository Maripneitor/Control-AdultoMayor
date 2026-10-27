<?php
$tomaId = (int)($toma['registro_id'] ?? ($toma['id'] ?? 0));
$estado = strtolower((string)($toma['estado'] ?? 'pendiente'));
$esTomada = in_array($estado, ['tomada', 'tomado'], true);
$esOmitida = $estado === 'omitido';
$esPospuesta = in_array($estado, ['pospuesto', 'pospuesta'], true);

$color = !empty($toma['color_distintivo']) ? $toma['color_distintivo'] : 'Blanco';
$formaStr = !empty($toma['forma']) ? strtolower($toma['forma']) : 'tableta';
$hexColor = strtolower($color) === 'blanco' ? '#f8fafc' : strtolower($color);
$textColor = strtolower($color) === 'blanco' ? '#334155' : '#ffffff';

// Check if within 15 min window for undo
$dentroDeVentanaDeshacer = true;
if ($esTomada && !empty($toma['tomado_at'])) {
    $tomadoTime = strtotime($toma['tomado_at']);
    if ($tomadoTime && (time() - $tomadoTime) > 900) { // 15 min = 900s
        $dentroDeVentanaDeshacer = false;
    }
}
?>

<div class="flex flex-col p-4 sm:p-5 mb-4 rounded-2xl border transition-all duration-200 <?= $esTomada ? 'bg-emerald-50/60 border-emerald-200' : ($esOmitida ? 'bg-rose-50/60 border-rose-200' : ($esPospuesta ? 'bg-amber-50/60 border-amber-200' : 'bg-white border-slate-200/90 shadow-card')) ?>">
    
    <div class="flex items-start sm:items-center space-x-3.5 mb-4">
        <!-- Icono de Medicamento / Forma Farmacéutica -->
        <div class="w-12 h-12 rounded-2xl flex items-center justify-center font-bold text-lg shadow-xs border border-slate-200/80 shrink-0" 
             style="background-color: <?= e($hexColor) ?>; color: <?= e($textColor) ?>">
            <?php if ($formaStr === 'gotas'): ?>
                <svg class="w-6 h-6" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M12 2.25c-4.5 5.25-6.75 9-6.75 12a6.75 6.75 0 0013.5 0c0-3-2.25-6.75-6.75-12z"/></svg>
            <?php elseif ($formaStr === 'jarabe' || $formaStr === 'solucion'): ?>
                <svg class="w-6 h-6" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.942A4.5 4.5 0 0115.918 17H8.082a4.5 4.5 0 01-2.312-.642L4.2 15.3m15.6 0A2.25 2.25 0 0021 13.35V11.25a2.25 2.25 0 00-2.25-2.25h-.15m-14.4 6.3A2.25 2.25 0 013 13.35V11.25A2.25 2.25 0 015.25 9h.15"/></svg>
            <?php else: ?>
                <svg class="w-6 h-6" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a4.5 4.5 0 119 9l-9 9a4.5 4.5 0 01-9-9l9-9z"/><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 13.5l9-9"/></svg>
            <?php endif; ?>
        </div>

        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 flex-wrap mb-0.5">
                <p class="text-base sm:text-lg font-bold text-slate-800 leading-tight truncate">
                    <?= e($toma['nombre_comercial'] ?? $toma['medicamento_nombre'] ?? '') ?>
                </p>
                <?php if ($esTomada): ?>
                    <span class="text-[11px] font-bold text-emerald-700 bg-emerald-100 border border-emerald-200/80 px-2 py-0.5 rounded-full inline-flex items-center gap-1">
                        <svg class="w-3 h-3" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        Tomada
                    </span>
                <?php elseif ($esOmitida): ?>
                    <span class="text-[11px] font-bold text-rose-700 bg-rose-100 border border-rose-200/80 px-2 py-0.5 rounded-full inline-flex items-center gap-1">
                        <svg class="w-3 h-3" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                        Dosis Omitida
                    </span>
                <?php elseif ($esPospuesta): ?>
                    <span class="text-[11px] font-bold text-amber-700 bg-amber-100 border border-amber-200/80 px-2 py-0.5 rounded-full inline-flex items-center gap-1">
                        <svg class="w-3 h-3" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Pospuesta
                    </span>
                <?php endif; ?>
            </div>

            <p class="text-xs sm:text-sm text-slate-600">
                Dosis: <span class="font-bold text-slate-800"><?= e((string)$toma['dosis']) ?></span> • <?= e(ucfirst($formaStr)) ?> 
            </p>
            <p class="text-xs font-semibold text-blue-600 mt-0.5 flex items-center gap-1">
                <svg class="w-3.5 h-3.5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Programada: <?= !empty($toma['hora_programada']) ? substr($toma['hora_programada'], 0, 5) : 'N/A' ?></span>
            </p>
        </div>
    </div>
    
    <!-- Barra de Acciones Dinámica según Estado Clínico y Permisos -->
    <div class="flex items-center gap-2.5 w-full pt-2 border-t border-slate-100">
        <?php if (!empty($puedeSuministrar)): ?>
            <?php if ($esTomada): ?>
                <!-- Estado TOMADA: Badge positivo + Botón Deshacer si está dentro de ventana -->
                <div class="flex items-center justify-between w-full">
                    <div class="text-xs text-emerald-800 font-medium flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-emerald-600" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Dosis registrada exitosamente</span>
                    </div>
                    <?php if ($dentroDeVentanaDeshacer && !empty($puedeEditar)): ?>
                        <form action="<?= APP_URL ?>/medicamentos/deshacer" method="POST" class="inline m-0">
                            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                            <input type="hidden" name="toma_id" value="<?= $tomaId ?>">
                            <button type="submit" class="tap-bounce px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold rounded-xl border border-slate-200 transition cursor-pointer" title="Deshacer toma si fue marcada por error">
                                Deshacer
                            </button>
                        </form>
                    <?php endif; ?>
                </div>

            <?php elseif ($esOmitida): ?>
                <!-- Estado OMITIDA: Opción de Registrar toma tardía o Mantener omitida -->
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-2.5 w-full">
                    <span class="text-xs text-rose-800 font-medium">
                        Horario sobrepasado. Puedes registrarla ahora con nota de retraso:
                    </span>
                    <div class="flex items-center gap-2 shrink-0">
                        <form action="<?= APP_URL ?>/medicamentos/toma" method="POST" class="inline m-0 flex-1 sm:flex-initial" onsubmit="return handleTomaSubmit(this, '<?= $tomaId ?>')">
                            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                            <input type="hidden" name="toma_id" value="<?= $tomaId ?>">
                            <input type="hidden" name="accion" value="justificar_retraso">
                            <button type="submit" class="tap-bounce touch-min w-full sm:w-auto px-4 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-sm transition flex items-center justify-center gap-1.5 cursor-pointer">
                                <svg class="w-4 h-4" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>Registrar toma tardía</span>
                            </button>
                        </form>
                    </div>
                </div>

            <?php else: ?>
                <!-- Estado PENDIENTE / POSPUESTO -->
                <form action="<?= APP_URL ?>/medicamentos/toma" method="POST" class="flex-1" onsubmit="return handleTomaSubmit(this, '<?= $tomaId ?>')">
                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                    <input type="hidden" name="toma_id" value="<?= $tomaId ?>">
                    <input type="hidden" name="accion" value="marcar_tomada">
                    <button type="submit" class="tap-bounce touch-min w-full py-3 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm flex items-center justify-center gap-2 shadow-xs transition cursor-pointer">
                        <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        <span>Marcar Tomada</span>
                    </button>
                </form>
                
                <?php if (!empty($puedeEditar)): ?>
                    <form action="<?= APP_URL ?>/medicamentos/posponer" method="POST" class="flex-1" onsubmit="return handleSnoozeSubmit(this, '<?= $tomaId ?>')">
                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                        <input type="hidden" name="toma_id" value="<?= $tomaId ?>">
                        <button type="submit" class="tap-bounce touch-min w-full py-3 px-4 rounded-xl bg-amber-50 hover:bg-amber-100 active:bg-amber-200 text-amber-800 font-bold text-sm border border-amber-200 flex items-center justify-center gap-2 transition cursor-pointer">
                            <svg class="w-4 h-4 text-amber-600" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Posponer (+30m)</span>
                        </button>
                    </form>
                <?php endif; ?>
            <?php endif; ?>
        <?php else: ?>
            <!-- Vista de Solo Lectura / Doctor (Insignias Estáticas de Estado) -->
            <div class="flex items-center justify-between w-full py-1">
                <?php if ($esTomada): ?>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-bold">
                        <svg class="w-4 h-4 text-emerald-600" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        <span>Dosis Tomada <?= !empty($toma['tomado_at']) ? '• ' . date('H:i A', strtotime($toma['tomado_at'])) : '' ?></span>
                    </span>
                <?php elseif ($esOmitida): ?>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-rose-50 text-rose-700 border border-rose-200 text-xs font-bold">
                        <svg class="w-4 h-4 text-rose-600" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                        <span>Dosis Omitida</span>
                    </span>
                <?php elseif ($esPospuesta): ?>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-50 text-amber-700 border border-amber-200 text-xs font-bold">
                        <svg class="w-4 h-4 text-amber-600" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Pospuesta (Pendiente de suministro)</span>
                    </span>
                <?php else: ?>
                    <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-slate-100 text-slate-600 border border-slate-200 text-xs font-bold">
                        <svg class="w-4 h-4 text-slate-400" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Pendiente de suministro</span>
                    </span>
                <?php endif; ?>
                <span class="text-[11px] text-slate-400 font-medium"><?= !empty($esDoctor) ? 'Solo Consulta Médica' : 'Modo Solo Lectura' ?></span>
            </div>
        <?php endif; ?>
    </div>
</div>
