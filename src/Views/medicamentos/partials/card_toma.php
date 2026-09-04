<div class="flex items-center justify-between p-3.5 rounded-xl border <?= $toma['estado'] === 'tomada' ? 'bg-emerald-50/50 border-emerald-200' : 'bg-white border-slate-200' ?>">
    <div class="flex items-center space-x-3">
        <?php 
            $color = !empty($toma['color_distintivo']) ? $toma['color_distintivo'] : 'Blanco';
            $formaStr = !empty($toma['forma']) ? $toma['forma'] : 'tableta';
            $hexColor = strtolower($color) === 'blanco' ? '#ffffff' : strtolower($color);
            $textColor = strtolower($color) === 'blanco' ? '#000000' : '#ffffff';
        ?>
        <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-lg shadow-sm border border-slate-200" 
             style="background-color: <?= e($hexColor) ?>; color: <?= e($textColor) ?>">
            <?= $formaStr === 'capsula' ? '💊' : ($formaStr === 'gotas' ? '💧' : '💊') ?>
        </div>
        <div>
            <p class="text-sm font-bold text-slate-800"><?= e($toma['nombre_comercial']) ?></p>
            <p class="text-xs text-slate-500">
                Dosis: <?= e((string)$toma['dosis']) ?> • <?= e(ucfirst($formaStr)) ?> 
                • <strong>Momento: <?= e(ucfirst($toma['momento_dia'] ?? 'N/A')) ?></strong>
            </p>
        </div>
    </div>
    <?php if ($toma['estado'] !== 'tomada'): ?>
        <form action="<?= APP_URL ?>/medicamentos/toma" method="POST">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <input type="hidden" name="registro_id" value="<?= e((string)$toma['registro_id']) ?>">
            <!-- Botón gigante requerido -->
            <button type="submit" class="w-16 h-16 rounded-full bg-green-500 hover:bg-green-600 text-white flex items-center justify-center shadow-lg transition-transform hover:scale-105">
                <svg class="w-8 h-8 fill-current" viewBox="0 0 24 24"><path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/></svg>
            </button>
        </form>
    <?php else: ?>
        <div class="w-16 h-16 rounded-full bg-slate-200 border-2 border-slate-300 text-slate-400 flex items-center justify-center cursor-not-allowed">
            <svg class="w-8 h-8 fill-current" viewBox="0 0 24 24"><path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/></svg>
        </div>
    <?php endif; ?>
</div>
