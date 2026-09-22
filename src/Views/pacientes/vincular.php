<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vincular Paciente - VitalSenior</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-900 font-sans min-h-screen">
    <!-- Header -->
    <header class="bg-white border-b border-slate-200 px-6 py-4 flex justify-between items-center shadow-sm">
        <h1 class="text-xl font-bold text-slate-800">Vinculación y Gestión de Pacientes</h1>
        <a href="<?= APP_URL ?>/dashboard" class="text-sm font-semibold text-blue-600 hover:underline">&larr; Volver al Dashboard</a>
    </header>

    <main class="max-w-4xl mx-auto p-6 mt-8">
        
        <?php if (!empty($flash)): ?>
            <?php 
                $colors = [
                    'success' => 'bg-emerald-100 border-emerald-500 text-emerald-800',
                    'warning' => 'bg-amber-100 border-amber-500 text-amber-800',
                    'danger'  => 'bg-rose-100 border-rose-500 text-rose-800',
                    'info'    => 'bg-blue-100 border-blue-500 text-blue-800'
                ];
                $alertStyle = $colors[$flash['type']] ?? $colors['info'];
            ?>
            <div class="mb-6">
                <div class="border-l-4 p-4 rounded-r-lg shadow-sm flex items-center justify-between <?= $alertStyle; ?>" role="alert">
                    <span class="text-base font-medium"><?= htmlspecialchars($flash['message']); ?></span>
                    <button onclick="this.parentElement.remove()" class="text-lg font-bold ml-4 p-1 hover:opacity-75 focus:outline-none">&times;</button>
                </div>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            
            <!-- Opcion A -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="w-12 h-12 bg-blue-100 text-blue-600 flex items-center justify-center rounded-xl mb-4 text-2xl font-bold">A</div>
                    <h2 class="text-xl font-bold text-slate-800 mb-2">Vincular Paciente Existente</h2>
                    <p class="text-slate-600 mb-6 text-sm">Si el adulto mayor ya creó su propia cuenta en VitalSenior, ingresa aquí su código de vinculación.</p>
                    
                    <form action="<?= APP_URL ?>/vincular/procesar" method="POST" class="space-y-4">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                        <input type="hidden" name="action" value="codigo">
                        
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Código de Vinculación</label>
                            <input type="text" name="codigo_vinculacion" placeholder="Ej: A4B9-C2X1" required class="w-full bg-slate-50 border border-slate-300 rounded-lg px-4 py-3 text-slate-800 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition font-mono font-bold tracking-widest uppercase">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Parentesco (Opcional)</label>
                            <select name="parentesco_codigo" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-4 py-3 text-slate-800 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition">
                                <option value="Hijo/a">Hijo/a</option>
                                <option value="Esposo/a">Esposo/a</option>
                                <option value="Nieto/a">Nieto/a</option>
                                <option value="Cuidador/Enfermero">Cuidador Profesional</option>
                                <option value="Otro">Otro Familiar</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="w-full bg-blue-600 text-white font-bold py-3 rounded-xl hover:bg-blue-700 shadow-md transition transform hover:-translate-y-1">
                            Vincular Paciente
                        </button>
                    </form>
                </div>
            </div>

            <!-- Opcion B -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="w-12 h-12 bg-green-100 text-green-600 flex items-center justify-center rounded-xl mb-4 text-2xl font-bold">B</div>
                    <h2 class="text-xl font-bold text-slate-800 mb-2">Crear Perfil Administrado</h2>
                    <p class="text-slate-600 mb-6 text-sm">Si el adulto mayor no usa tecnología y tú administrarás todo, crea su perfil directamente desde aquí.</p>
                    
                    <form action="<?= APP_URL ?>/vincular/procesar" method="POST" class="space-y-4">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                        <input type="hidden" name="action" value="nuevo">
                        
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Nombre Completo del Paciente</label>
                            <div class="flex gap-2">
                                <input type="text" name="nombre" placeholder="Nombres" required class="w-1/2 bg-slate-50 border border-slate-300 rounded-lg px-3 py-3 text-slate-800 focus:border-green-500 focus:ring-1 focus:ring-green-500 transition">
                                <input type="text" name="apellidos" placeholder="Apellidos" class="w-1/2 bg-slate-50 border border-slate-300 rounded-lg px-3 py-3 text-slate-800 focus:border-green-500 focus:ring-1 focus:ring-green-500 transition">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Fecha de Nacimiento</label>
                            <input type="date" name="fecha_nacimiento" required class="w-full bg-slate-50 border border-slate-300 rounded-lg px-4 py-3 text-slate-800 focus:border-green-500 focus:ring-1 focus:ring-green-500 transition">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Género</label>
                            <select name="genero" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-4 py-3 text-slate-800 focus:border-green-500 focus:ring-1 focus:ring-green-500 transition">
                                <option value="Masculino">Masculino</option>
                                <option value="Femenino">Femenino</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Tu Parentesco</label>
                            <select name="parentesco_nuevo" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-4 py-3 text-slate-800 focus:border-green-500 focus:ring-1 focus:ring-green-500 transition">
                                <option value="Hijo/a">Hijo/a</option>
                                <option value="Esposo/a">Esposo/a</option>
                                <option value="Nieto/a">Nieto/a</option>
                                <option value="Cuidador/Enfermero">Cuidador Profesional</option>
                                <option value="Otro">Otro Familiar</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="w-full bg-green-600 text-white font-bold py-3 rounded-xl hover:bg-green-700 shadow-md transition transform hover:-translate-y-1">
                            Crear Perfil Administrado
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </main>
</body>
</html>
