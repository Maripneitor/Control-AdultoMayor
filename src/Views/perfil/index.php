<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VitalSenior - Mi Perfil</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-slate-50 text-slate-900 font-sans min-h-screen">
    <header class="bg-white border-b border-slate-200 sticky top-0 z-30 px-6 py-4 flex justify-between items-center shadow-sm">
        <div class="flex items-center space-x-3">
            <a href="<?= APP_URL ?>/dashboard" class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center text-white font-bold text-xl hover:bg-blue-700 transition">←</a>
            <div>
                <h1 class="text-lg font-bold text-slate-800 leading-tight">Mi Perfil y Ajustes</h1>
                <p class="text-xs text-slate-500">Administra tu cuenta y seguridad</p>
            </div>
        </div>
        <div class="flex items-center space-x-4">
            <a href="<?= APP_URL ?>/logout" class="px-3 py-1.5 bg-red-50 text-red-600 text-xs font-semibold rounded-lg border border-red-200 hover:bg-red-100 transition">Cerrar Sesión</a>
        </div>
    </header>

    <main class="max-w-4xl mx-auto p-6 mt-4 grid grid-cols-1 md:grid-cols-3 gap-6">
        <?php require_once dirname(__DIR__) . '/components/toast.php'; ?>


        <!-- Columna 1: Info Básica y Botones Rápidos -->
        <div class="space-y-6">
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm text-center">
                <div class="w-24 h-24 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4 text-3xl font-bold text-blue-600">
                    <?= strtoupper(substr($usuario['nombre'], 0, 1)) ?>
                </div>
                <h2 class="text-lg font-bold text-slate-800"><?= e($usuario['nombre']) ?></h2>
                <span class="text-xs font-bold uppercase text-slate-500 bg-slate-100 px-2 py-1 rounded-full inline-block mt-2">
                    Rol: <?= e(str_replace('_', ' ', $usuario['rol'])) ?>
                </span>
            </div>

            <?php if ($usuario['rol'] === 'adulto_mayor' && isset($paciente)): ?>
            <div class="bg-blue-50 p-6 rounded-2xl border border-blue-200 shadow-sm text-center">
                <h3 class="text-sm font-bold text-blue-900 mb-2">Código de Vinculación</h3>
                <p class="text-xs text-blue-700 mb-4">Comparte este código con tu familiar para que gestione tu cuenta.</p>
                <div class="text-2xl font-black tracking-widest text-blue-800 bg-white py-3 px-4 rounded-xl border border-blue-200 shadow-inner">
                    <?= e($paciente['codigo_vinculacion']) ?>
                </div>
            </div>
            <?php endif; ?>

            <div class="bg-rose-50 p-6 rounded-2xl border border-rose-200 shadow-sm">
                <h3 class="text-sm font-bold text-rose-900 mb-2">Zona de Peligro</h3>
                <p class="text-xs text-rose-700 mb-4">Una vez eliminada la cuenta, no hay vuelta atrás.</p>
                <form action="<?= APP_URL ?>/perfil/eliminar" method="POST" onsubmit="return confirm('¿ESTÁS ABSOLUTAMENTE SEGURO de eliminar tu cuenta? Todos tus datos se perderán para siempre.');">
                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                    <button type="submit" class="w-full py-2 bg-rose-600 text-white font-bold text-sm rounded-lg hover:bg-rose-700 transition">Eliminar Cuenta</button>
                </form>
            </div>
        </div>

        <!-- Columna 2: Formularios (Datos y Contraseña) -->
        <div class="md:col-span-2 space-y-6">
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                <h2 class="text-lg font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2">Datos Personales</h2>
                <form action="<?= APP_URL ?>/perfil/actualizar" method="POST" class="space-y-4">
                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Nombre Completo</label>
                            <input type="text" name="nombre" value="<?= e($usuario['nombre']) ?>" required class="w-full rounded-lg border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Correo Electrónico</label>
                            <input type="email" name="email" value="<?= e($usuario['email']) ?>" required class="w-full rounded-lg border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Teléfono (WhatsApp)</label>
                            <input type="tel" name="telefono" value="<?= e($usuario['telefono'] ?? '') ?>" class="w-full rounded-lg border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Ej. 5512345678">
                        </div>
                    </div>

                    <div class="flex items-center mt-4">
                        <input type="checkbox" id="alertas" name="alertas_whatsapp" value="1" <?= (!empty($usuario['alertas_whatsapp'])) ? 'checked' : '' ?> class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="alertas" class="ml-2 block text-sm text-slate-700 font-semibold">
                            Recibir alertas y recordatorios por WhatsApp
                        </label>
                    </div>

                    <div class="mt-6 pt-4 border-t border-slate-100 flex justify-end">
                        <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 transition">Guardar Cambios</button>
                    </div>
                </form>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                <h2 class="text-lg font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2">Seguridad</h2>
                <form action="<?= APP_URL ?>/perfil/password" method="POST" class="space-y-4">
                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                    
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Contraseña Actual</label>
                        <input type="password" name="current_password" required class="w-full rounded-lg border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Nueva Contraseña</label>
                            <input type="password" name="new_password" required class="w-full rounded-lg border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Confirmar Nueva Contraseña</label>
                            <input type="password" name="confirm_password" required class="w-full rounded-lg border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>

                    <div class="mt-6 pt-4 border-t border-slate-100 flex justify-end">
                        <button type="submit" class="px-6 py-2.5 bg-slate-800 text-white font-bold rounded-lg hover:bg-slate-900 transition">Actualizar Contraseña</button>
                    </div>
                </form>
            </div>
        </div>

    </main>
</body>
</html>
