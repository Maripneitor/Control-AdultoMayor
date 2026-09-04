<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="max-w-2xl mx-auto mt-10">
    <div class="bg-white p-8 rounded-xl shadow-lg text-center">
        <h2 class="text-3xl font-bold mb-6 text-gray-800">Confirmar Registro</h2>
        <div class="bg-yellow-100 border-l-4 border-yellow-500 p-6 mb-8 text-left rounded-r-lg">
            <p class="text-xl text-yellow-800 font-semibold mb-2">Aviso Importante</p>
            <p class="text-lg text-yellow-700"><?php echo htmlspecialchars($_SESSION['flash_mensaje'] ?? 'El valor ingresado parece ser inusual.'); ?></p>
        </div>

        <form action="<?php echo APP_URL; ?>/salud/registrar" method="POST" class="space-y-6">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
            
            <?php foreach ($datos as $key => $value): ?>
                <?php if ($key !== 'csrf_token' && $key !== 'confirmado_por_usuario'): ?>
                    <input type="hidden" name="<?php echo htmlspecialchars($key); ?>" value="<?php echo htmlspecialchars($value); ?>">
                <?php endif; ?>
            <?php endforeach; ?>
            <input type="hidden" name="confirmado_por_usuario" value="1">

            <div class="flex flex-col sm:flex-row gap-4 justify-center mt-8">
                <a href="<?php echo APP_URL; ?>/dashboard" class="w-full sm:w-auto px-8 py-4 bg-gray-500 hover:bg-gray-600 text-white font-bold rounded-xl text-xl shadow-md transition-all">
                    Cancelar
                </a>
                <button type="submit" class="w-full sm:w-auto px-8 py-4 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl text-xl shadow-md transition-all">
                    Sí, registrar valor
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
