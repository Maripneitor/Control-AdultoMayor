<div class="flex flex-col p-4 mb-4 rounded-xl border <?= in_array($toma['estado'], ['tomada', 'tomado'], true) ? 'bg-emerald-50/50 border-emerald-200' : 'bg-white border-slate-200 shadow-sm' ?>">
    <div class="flex items-center space-x-4 mb-4">
        <?php 
            $color = !empty($toma['color_distintivo']) ? $toma['color_distintivo'] : 'Blanco';
            $formaStr = !empty($toma['forma']) ? $toma['forma'] : 'tableta';
            $hexColor = strtolower($color) === 'blanco' ? '#ffffff' : strtolower($color);
            $textColor = strtolower($color) === 'blanco' ? '#000000' : '#ffffff';
        ?>
        <div class="w-12 h-12 rounded-full flex items-center justify-center font-bold text-2xl shadow-sm border border-slate-200 shrink-0" 
             style="background-color: <?= e($hexColor) ?>; color: <?= e($textColor) ?>">
            <?= $formaStr === 'capsula' ? '💊' : ($formaStr === 'gotas' ? '💧' : '💊') ?>
        </div>
        <div class="flex-1">
            <p class="text-lg font-bold text-slate-800 leading-tight"><?= e($toma['nombre_comercial']) ?></p>
            <p class="text-sm text-slate-600 mt-1">
                Dosis: <span class="font-bold"><?= e((string)$toma['dosis']) ?></span> • <?= e(ucfirst($formaStr)) ?> 
            </p>
            <p class="text-xs font-semibold text-blue-600 mt-1">
                Hora programada: <?= !empty($toma['hora_programada']) ? substr($toma['hora_programada'], 0, 5) : 'N/A' ?>
            </p>
        </div>
    </div>
    
    <div class="flex items-center gap-3 w-full">
        <?php if ($toma['estado'] === 'pendiente' || $toma['estado'] === 'pospuesto'): ?>
            <form action="<?= APP_URL ?>/medicamentos/toma" method="POST" class="flex-1" onsubmit="return handleTomaSubmit(this, '<?= e((string)$toma['registro_id']) ?>')">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                <input type="hidden" name="registro_id" value="<?= e((string)$toma['registro_id']) ?>">
                <button type="submit" class="w-full py-4 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-lg flex items-center justify-center shadow-md transition-transform hover:scale-[1.02]">
                    <svg class="w-6 h-6 mr-2 fill-current" viewBox="0 0 24 24"><path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/></svg>
                    Tomada
                </button>
            </form>
            
            <?php if (!empty($puedeEditar)): ?>
            <form action="<?= APP_URL ?>/medicamentos/posponer" method="POST" class="flex-1" onsubmit="return handleSnoozeSubmit(this, '<?= e((string)$toma['registro_id']) ?>')">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                <input type="hidden" name="registro_id" value="<?= e((string)$toma['registro_id']) ?>">
                <button type="submit" class="w-full py-4 rounded-xl bg-amber-100 hover:bg-amber-200 text-amber-700 font-bold text-base border border-amber-300 flex items-center justify-center transition-colors">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Recordar más tarde
                </button>
            </form>
            <?php endif; ?>
        <?php else: ?>
            <div class="w-full py-3 rounded-xl bg-slate-100 border-2 border-slate-200 text-slate-500 font-bold text-center flex justify-center items-center">
                <?= ucfirst(e($toma['estado'])) ?>
            </div>
        <?php endif; ?>
    </div>
</div>
