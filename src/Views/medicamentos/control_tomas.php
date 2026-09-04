<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="max-w-4xl mx-auto mt-8 mb-16 px-4">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-slate-800">Control de Tomas Diarias</h1>
        <a href="<?= APP_URL ?>/dashboard" class="px-4 py-2 bg-slate-200 text-slate-700 font-bold rounded-lg hover:bg-slate-300 transition-colors">Volver al Dashboard</a>
    </div>

    <?php 
        // Agrupar tomas por momento del día
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
    ?>

    <div class="space-y-8">
        <?php foreach ($grupos as $clave => $grupo): ?>
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

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
