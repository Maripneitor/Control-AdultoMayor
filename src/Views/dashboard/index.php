<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VitalSenior - Mi Perfil</title>
    <link rel="manifest" href="<?= APP_URL ?>/manifest.json">
    <meta name="theme-color" content="#2563eb">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: sans-serif; font-size: 110%; } /* Tipografía más grande por defecto */
    </style>
</head>
<body id="dashboard-body" class="bg-gray-100 text-gray-900 font-sans min-h-screen transition-opacity duration-700 opacity-0">
    <!-- Barra Superior -->
    <header class="bg-white border-b-4 border-blue-800 sticky top-0 z-30 px-6 py-6 flex flex-col md:flex-row justify-between items-center shadow-md gap-4">
        <div class="text-center md:text-left">
            <h1 class="text-3xl font-extrabold text-blue-900">Hola, <?= e($paciente['nombre']) ?></h1>
            <p class="text-lg font-bold text-gray-700 mt-2">Bienvenido a tu perfil médico.</p>
        </div>
        <div class="flex flex-wrap justify-center gap-4">
            <a href="<?= APP_URL ?>/logout" class="px-6 py-3 bg-red-100 text-red-800 text-lg font-bold rounded-xl border-2 border-red-300 hover:bg-red-200 transition">Cerrar Sesión</a>
        </div>
    </header>

    <main class="max-w-4xl mx-auto p-6 grid grid-cols-1 gap-8 mt-4">
        <?php require_once dirname(__DIR__) . '/components/toast.php'; ?>


        <?php
            // Calcular la próxima medicina
            $proximaMedicina = null;
            if (!empty($tomasDelDia)) {
                $ahora = date('H:i');
                foreach ($tomasDelDia as $toma) {
                    if ($toma['estado'] === 'pendiente' && $toma['hora_programada'] >= $ahora) {
                        $proximaMedicina = $toma;
                        break;
                    }
                }
                // Si no hay pendientes futuras, tomamos la primera pendiente del día aunque haya pasado (retrasada)
                if (!$proximaMedicina) {
                    foreach ($tomasDelDia as $toma) {
                        if ($toma['estado'] === 'pendiente') {
                            $proximaMedicina = $toma;
                            break;
                        }
                    }
                }
            }
        ?>

        <!-- Panel Gigante: Próxima Medicina -->
        <section class="bg-white p-8 rounded-3xl border-4 border-blue-200 shadow-xl text-center">
            <h2 class="text-2xl font-extrabold text-gray-800 mb-4">Tu próxima medicina es:</h2>
            <?php if ($proximaMedicina): ?>
                <div class="bg-blue-50 py-8 px-4 rounded-2xl border-4 border-blue-400 my-6">
                    <h3 class="text-5xl font-black text-blue-900 mb-4"><?= e($proximaMedicina['medicamento_nombre']) ?></h3>
                    <p class="text-4xl font-bold text-blue-700">a las <?= date('H:i', strtotime($proximaMedicina['hora_programada'])) ?></p>
                </div>
                <a href="<?= APP_URL ?>/medicamentos/control" class="inline-block w-full md:w-auto px-10 py-5 bg-green-600 text-white text-2xl font-black rounded-2xl hover:bg-green-700 shadow-lg transform transition active:scale-95">
                    Marcar como Tomada
                </a>
            <?php else: ?>
                <div class="bg-gray-100 py-10 px-4 rounded-2xl border-4 border-gray-300 my-6">
                    <p class="text-3xl font-bold text-gray-600">No tienes medicinas pendientes para hoy.</p>
                </div>
            <?php endif; ?>
        </section>

        <!-- Botón SOS Asíncrono de Emergencia -->
        <section>
            <button id="btn-sos" 
                    data-paciente-id="<?= e((string)$paciente['id']) ?>"
                    class="flex flex-col items-center justify-center w-full py-8 bg-red-600 text-white rounded-3xl shadow-[0_10px_0_rgb(153,27,27)] hover:bg-red-500 hover:-translate-y-1 active:translate-y-2 active:shadow-none transition-all cursor-pointer focus:outline-none focus:ring-4 focus:ring-red-300">
                <span class="text-6xl mb-2">🆘</span>
                <span class="text-3xl font-black tracking-wider uppercase">Botón SOS de Emergencia</span>
                <span class="text-sm font-semibold text-red-100 mt-1">Avisar a mis familiares inmediatamente</span>
            </button>
        </section>

        <!-- Botones Comunes de Acción -->
        <section class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-4">
            <a href="<?= APP_URL ?>/salud/stepper" class="flex flex-col items-center p-6 bg-white rounded-2xl border-4 border-indigo-200 hover:border-indigo-500 shadow-md transition-colors text-center">
                <span class="text-5xl mb-4">❤️</span>
                <span class="text-xl font-bold text-gray-800">Ver Expediente Completo</span>
            </a>
            <a href="<?= APP_URL ?>/medicamentos" class="flex flex-col items-center p-6 bg-white rounded-2xl border-4 border-teal-200 hover:border-teal-500 shadow-md transition-colors text-center">
                <span class="text-5xl mb-4">💊</span>
                <span class="text-xl font-bold text-gray-800">Gestionar Medicamentos</span>
            </a>
            <a href="<?= APP_URL ?>/emergencia?id=<?= e((string)($paciente['uuid_publico'] ?? '')) ?>" target="_blank" class="flex flex-col items-center p-6 bg-white rounded-2xl border-4 border-gray-300 hover:border-gray-500 shadow-md transition-colors text-center">
                <span class="text-5xl mb-4">📱</span>
                <span class="text-xl font-bold text-gray-800">Ver Ficha QR de Emergencia</span>
            </a>
        </section>
        
        <!-- Código de vinculación para el familiar -->
        <section class="mt-8 bg-gray-50 p-6 rounded-2xl border-2 border-gray-300 text-center">
            <h3 class="text-xl font-bold text-gray-600 mb-2">Tu código de vinculación familiar:</h3>
            <div class="inline-block bg-white px-8 py-3 border-4 border-dashed border-gray-400 rounded-xl">
                <span class="text-3xl font-mono font-black tracking-widest text-gray-800"><?= e($paciente['codigo_vinculacion'] ?? 'NO-GEN') ?></span>
            </div>
            <p class="text-gray-500 text-lg mt-3">Dale este código a tu familiar para que pueda ayudarte.</p>
        </section>

    </main>

    <!-- Script del Botón SOS y Registro de PWA -->
    <script src="<?= APP_URL ?>/assets/js/sos-handler.js"></script>
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            requestAnimationFrame(() => {
                const body = document.getElementById('dashboard-body');
                if (body) {
                    body.classList.remove('opacity-0');
                    body.classList.add('opacity-100');
                }
            });

            // Registro del Service Worker para soporte offline
            if ('serviceWorker' in navigator) {
                navigator.serviceWorker.register('<?= APP_URL ?>/sw.js')
                    .then((reg) => console.log('[PWA] Service Worker registrado con éxito:', reg.scope))
                    .catch((err) => console.warn('[PWA] Fallo al registrar Service Worker:', err));
            }
        });
    </script>
</body>
</html>