<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="max-w-4xl mx-auto mt-8 mb-16 px-4">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-slate-800">Control de Tomas Diarias</h1>
        <a href="<?= APP_URL ?>/dashboard" class="px-4 py-2 bg-slate-200 text-slate-700 font-bold rounded-lg hover:bg-slate-300 transition-colors">Volver al Dashboard</a>
    </div>

    <?php 
        $horaActual = (int)date('H');
        $turnoActivo = 'manana';
        if ($horaActual >= 12 && $horaActual < 19) {
            $turnoActivo = 'tarde';
        } elseif ($horaActual >= 19 || $horaActual < 5) {
            $turnoActivo = 'noche';
        }

        $grupos = [
            'manana' => ['titulo' => 'Mañana 🌅', 'tomas' => []],
            'tarde'  => ['titulo' => 'Tarde ☀️', 'tomas' => []],
            'noche'  => ['titulo' => 'Noche 🌙', 'tomas' => []]
        ];

        foreach ($tomasDelDia as $t) {
            $momento = strtolower($t['momento_dia'] ?? 'manana');
            if (isset($grupos[$momento])) {
                $grupos[$momento]['tomas'][] = $t;
            } else {
                $grupos['manana']['tomas'][] = $t;
            }
        }
        
        // Mantener solo el turno activo
        $grupoActivo = [$turnoActivo => $grupos[$turnoActivo]];
    ?>

    <div class="space-y-8">
        <?php foreach ($grupoActivo as $clave => $grupo): ?>
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="bg-slate-50 px-6 py-4 border-b border-slate-200">
                    <h2 class="text-2xl font-bold text-slate-700"><?= $grupo['titulo'] ?></h2>
                </div>
                <div class="p-6">
                    <?php if (empty($grupo['tomas'])): ?>
                        <p class="text-slate-500 italic text-center text-lg">No hay medicamentos programados para esta hora.</p>
                    <?php else: ?>
                        <div class="grid grid-cols-1 gap-4">
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

<!-- Contenedor para el check gigante -->
<div id="success-overlay" class="fixed inset-0 bg-white/90 z-50 hidden flex-col items-center justify-center transition-opacity duration-300 opacity-0">
    <div class="text-9xl mb-4 animate-bounce">✅</div>
    <h2 class="text-4xl font-bold text-emerald-600">¡Toma Registrada!</h2>
</div>

<script>
    function handleTomaSubmit(form, id) {
        const card = form.closest('.flex-col');
        const overlay = document.getElementById('success-overlay');
        
        // Fade out card
        card.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
        card.style.opacity = '0';
        card.style.transform = 'scale(0.95)';
        
        // Show overlay
        overlay.classList.remove('hidden');
        overlay.style.display = 'flex';
        setTimeout(() => overlay.classList.remove('opacity-0'), 10);
        
        setTimeout(() => {
            form.submit();
        }, 1500);
        
        return false; // Prevent default submit
    }

    function handleSnoozeSubmit(form, id) {
        const card = form.closest('.flex-col');
        
        // Change card to amber
        card.className = "flex flex-col p-4 mb-4 rounded-xl border bg-amber-50/50 border-amber-200 transition-colors duration-500";
        
        // Replace buttons with countdown text
        const buttonsContainer = form.parentElement;
        buttonsContainer.innerHTML = `
            <div class="w-full py-3 rounded-xl bg-amber-100 text-amber-800 font-bold text-center flex justify-center items-center">
                ⏳ Te recordaremos a las ${getSnoozeTime()}
            </div>
        `;
        
        setTimeout(() => {
            form.submit();
        }, 2000); // Allow user to see the change for 2s before submitting
        
        return false;
    }

    function getSnoozeTime() {
        const now = new Date();
        now.setMinutes(now.getMinutes() + 15);
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
