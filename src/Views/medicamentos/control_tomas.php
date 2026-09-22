<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="max-w-4xl mx-auto mt-6 mb-16 px-4 sm:px-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="<?= APP_URL ?>/dashboard" class="text-xs font-bold text-slate-400 hover:text-blue-600 transition flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                    <span>Dashboard</span>
                </a>
                <span class="text-slate-300">/</span>
                <span class="text-xs font-bold text-blue-600">Control de Tomas</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">Control de Tomas Diarias</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Seguimiento en tiempo real de la administración de medicamentos.</p>
        </div>
        <a href="<?= APP_URL ?>/dashboard" 
           class="tap-bounce touch-min self-start sm:self-auto px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl border border-slate-200 shadow-xs transition flex items-center gap-2">
            <svg class="w-4 h-4 text-slate-500" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3"/></svg>
            <span>Volver al Dashboard</span>
        </a>
    </div>

    <?php require_once dirname(__DIR__) . '/components/toast.php'; ?>

    <?php 
        $horaActual = (int)date('H');
        $turnoActivo = 'manana';
        if ($horaActual >= 12 && $horaActual < 19) {
            $turnoActivo = 'tarde';
        } elseif ($horaActual >= 19 || $horaActual < 5) {
            $turnoActivo = 'noche';
        }

        $grupos = [
            'manana' => [
                'titulo' => 'Turno Mañana',
                'subtitulo' => '06:00 - 12:00 hrs',
                'color' => 'amber',
                'icon' => '<svg class="w-5 h-5 text-amber-500" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75"><circle cx="12" cy="12" r="4"/><path stroke-linecap="round" d="M12 2v2m0 16v2M4.93 4.93l1.41 1.41m11.32 11.32l1.41 1.41M2 12h2m16 0h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/></svg>',
                'tomas' => []
            ],
            'tarde'  => [
                'titulo' => 'Turno Tarde',
                'subtitulo' => '12:00 - 19:00 hrs',
                'color' => 'orange',
                'icon' => '<svg class="w-5 h-5 text-orange-500" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z"/></svg>',
                'tomas' => []
            ],
            'noche'  => [
                'titulo' => 'Turno Noche',
                'subtitulo' => '19:00 - 05:00 hrs',
                'color' => 'indigo',
                'icon' => '<svg class="w-5 h-5 text-indigo-500" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z"/></svg>',
                'tomas' => []
            ]
        ];

        foreach ($tomasDelDia as $t) {
            $momento = strtolower($t['momento_dia'] ?? 'manana');
            if (isset($grupos[$momento])) {
                $grupos[$momento]['tomas'][] = $t;
            } else {
                $grupos['manana']['tomas'][] = $t;
            }
        }
    ?>

    <!-- Selector de Turnos / Pestañas de Vista Rápida -->
    <div class="grid grid-cols-3 gap-2 sm:gap-3 mb-6">
        <?php foreach ($grupos as $clave => $info): 
            $esActivo = ($clave === $turnoActivo);
            $totalEnTurno = count($info['tomas']);
            $tomadasEnTurno = count(array_filter($info['tomas'], fn($item) => in_array($item['estado'] ?? '', ['tomado', 'tomada'], true)));
        ?>
            <div class="p-3 sm:p-4 rounded-2xl border transition-all <?= $esActivo ? 'bg-blue-50/80 border-blue-200 ring-2 ring-blue-500/20' : 'bg-white border-slate-200/80' ?>">
                <div class="flex items-center justify-between mb-1">
                    <div class="w-8 h-8 rounded-xl bg-slate-100 flex items-center justify-center">
                        <?= $info['icon'] ?>
                    </div>
                    <span class="text-[10px] sm:text-xs font-bold font-mono px-2 py-0.5 rounded-full <?= $tomadasEnTurno === $totalEnTurno && $totalEnTurno > 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' ?>">
                        <?= $tomadasEnTurno ?>/<?= $totalEnTurno ?>
                    </span>
                </div>
                <p class="text-xs sm:text-sm font-bold text-slate-800"><?= $info['titulo'] ?></p>
                <p class="text-[10px] text-slate-400 hidden sm:block"><?= $info['subtitulo'] ?></p>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Lista de Tomas por Turnos -->
    <div class="space-y-6">
        <?php foreach ($grupos as $clave => $grupo): ?>
            <div class="bg-white rounded-3xl shadow-card border border-slate-200/80 overflow-hidden">
                <div class="bg-slate-50/80 px-5 sm:px-6 py-3.5 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <?= $grupo['icon'] ?>
                        <div>
                            <h2 class="text-base sm:text-lg font-bold text-slate-800"><?= $grupo['titulo'] ?></h2>
                            <p class="text-[11px] text-slate-400 font-medium"><?= $grupo['subtitulo'] ?></p>
                        </div>
                    </div>
                    <span class="text-xs font-bold text-slate-500 bg-white px-2.5 py-1 rounded-xl border border-slate-200">
                        <?= count($grupo['tomas']) ?> <?= count($grupo['tomas']) === 1 ? 'dosis' : 'dosis' ?>
                    </span>
                </div>
                <div class="p-4 sm:p-6">
                    <?php if (empty($grupo['tomas'])): ?>
                        <div class="text-center py-8">
                            <svg class="w-8 h-8 text-slate-300 mx-auto mb-2" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a4.5 4.5 0 119 9l-9 9a4.5 4.5 0 01-9-9l9-9z"/><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 13.5l9-9"/></svg>
                            <p class="text-xs text-slate-400 font-medium">No hay medicamentos programados para este turno.</p>
                        </div>
                    <?php else: ?>
                        <div class="grid grid-cols-1 gap-3 sm:gap-4">
                            <?php foreach ($grupo['tomas'] as $toma): ?>
                                <?php include __DIR__ . '/partials/card_toma.php'; ?>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Modal Overlay Animado de Confirmación de Toma -->
<div id="success-overlay" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex-col items-center justify-center transition-opacity duration-300 opacity-0 px-4">
    <div class="bg-white p-8 rounded-3xl shadow-2xl border border-slate-100 flex flex-col items-center max-w-sm w-full text-center transform scale-95 transition-transform duration-300">
        <div class="w-16 h-16 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center mb-4 border border-emerald-200 shadow-sm animate-bounce">
            <svg class="w-8 h-8" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
        </div>
        <h2 class="text-xl font-bold text-slate-900">¡Toma Registrada!</h2>
        <p class="text-xs text-slate-500 mt-1">El estado y stock han sido actualizados correctamente.</p>
    </div>
</div>

<script>
    function handleTomaSubmit(form, id) {
        const card = form.closest('.rounded-2xl');
        const overlay = document.getElementById('success-overlay');
        
        if (card) {
            card.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
            card.style.opacity = '0.4';
            card.style.transform = 'scale(0.98)';
        }
        
        if (overlay) {
            overlay.classList.remove('hidden');
            overlay.style.display = 'flex';
            setTimeout(() => {
                overlay.classList.remove('opacity-0');
                const box = overlay.querySelector('div');
                if (box) box.classList.remove('scale-95');
            }, 10);
        }
        
        setTimeout(() => {
            form.submit();
        }, 800);
        
        return false;
    }

    function handleSnoozeSubmit(form, id) {
        const card = form.closest('.rounded-2xl');
        
        if (card) {
            card.className = "flex flex-col p-4 sm:p-5 mb-4 rounded-2xl border bg-amber-50/70 border-amber-200 transition-colors duration-500";
        }
        
        const buttonsContainer = form.parentElement;
        if (buttonsContainer) {
            buttonsContainer.innerHTML = `
                <div class="w-full py-3 rounded-xl bg-amber-100 text-amber-800 text-xs font-bold text-center flex justify-center items-center gap-2 border border-amber-200">
                    <svg class="w-4 h-4 text-amber-600" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Pospuesto: Te recordaremos a las ${getSnoozeTime()}</span>
                </div>
            `;
        }
        
        setTimeout(() => {
            form.submit();
        }, 1200);
        
        return false;
    }

    function getSnoozeTime() {
        const now = new Date();
        now.setMinutes(now.getMinutes() + 30);
        let h = now.getHours();
        let m = now.getMinutes();
        let ampm = h >= 12 ? 'PM' : 'AM';
        h = h % 12;
        h = h ? h : 12;
        m = m < 10 ? '0' + m : m;
        return `${h}:${m} ${ampm}`;
    }
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
