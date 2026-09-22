<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VitalSenior - Mis Medicamentos</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-slate-50 text-slate-900 font-sans min-h-screen">
    <!-- Header reutilizado del Dashboard -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-30 px-6 py-4 flex justify-between items-center shadow-sm">
        <div class="flex items-center space-x-3">
            <a href="<?= APP_URL ?>/dashboard" class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center text-white font-bold text-xl hover:bg-blue-700 transition">←</a>
            <div>
                <h1 class="text-lg font-bold text-slate-800 leading-tight">Inventario de Medicamentos</h1>
                <p class="text-xs text-slate-500">Paciente: <span class="font-semibold text-blue-700"><?= e($paciente['nombre'] . ' ' . $paciente['apellidos']) ?></span></p>
            </div>
        </div>
        <div class="flex items-center space-x-4">
            <a href="<?= APP_URL ?>/logout" class="px-3 py-1.5 bg-red-50 text-red-600 text-xs font-semibold rounded-lg border border-red-200 hover:bg-red-100 transition">Cerrar Sesión</a>
        </div>
    </header>

    <main class="max-w-7xl mx-auto p-6 grid grid-cols-1 lg:grid-cols-3 gap-6">
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
            <div class="lg:col-span-3 mb-4">
                <div class="border-l-4 p-4 rounded-r-lg shadow-sm flex items-center justify-between <?= $alertStyle; ?>" role="alert">
                    <span class="text-base font-medium"><?= htmlspecialchars($flash['message']); ?></span>
                    <button onclick="this.parentElement.remove()" class="text-lg font-bold ml-4 p-1 hover:opacity-75 focus:outline-none">&times;</button>
                </div>
            </div>
        <?php endif; ?>

        <!-- Columna 1 y 2: Lista de Medicamentos -->
        <section class="lg:col-span-2 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-bold text-slate-800">Medicamentos Activos</h2>
                <div class="flex items-center bg-blue-50 px-3 py-1.5 rounded-lg border border-blue-100">
                    <span class="text-xs font-semibold text-blue-800 mr-2">Adherencia General:</span>
                    <span class="text-sm font-bold <?= ($adherencia ?? 100) >= 80 ? 'text-emerald-600' : 'text-rose-600' ?>">
                        <?= e((string)($adherencia ?? 100)) ?>%
                    </span>
                </div>
            </div>
            
            <div class="space-y-4">
                <?php if (empty($medicamentos)): ?>
                    <p class="text-sm text-slate-500 text-center py-8">No tienes medicamentos registrados.</p>
                <?php else: ?>
                    <?php foreach ($medicamentos as $med): ?>
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between p-4 bg-slate-50 rounded-xl border border-slate-200 hover:border-blue-300 transition-colors">
                            <div class="flex items-start gap-4 w-full sm:w-auto">
                                <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 text-xl font-bold shrink-0">
                                    💊
                                </div>
                                <div class="flex-1">
                                    <h3 class="font-bold text-slate-800"><?= e($med['nombre_comercial']) ?></h3>
                                    <p class="text-sm text-slate-600">Dosis: <?= e($med['dosis']) ?> (Cada <?= e((string)$med['frecuencia_horas']) ?> hrs)</p>
                                    <?php if (!empty($med['instrucciones'])): ?>
                                        <p class="text-xs text-slate-500 mt-1 italic">"<?= e($med['instrucciones']) ?>"</p>
                                    <?php endif; ?>
                                    
                                    <?php 
                                        $dosisAlDia = floor(24 / max(1, $med['frecuencia_horas'])) * max(1, $med['dosis_por_toma']);
                                        $diasRestantes = $dosisAlDia > 0 ? floor($med['stock_actual'] / $dosisAlDia) : 0;
                                        $stockPct = min(100, max(0, ($diasRestantes / 30) * 100)); // asumiendo base de 30 dias
                                        $barColor = $diasRestantes > 7 ? 'bg-emerald-500' : ($diasRestantes > 3 ? 'bg-amber-400' : 'bg-red-500');
                                    ?>
                                    <div class="mt-3">
                                        <div class="flex justify-between text-xs mb-1">
                                            <span class="font-semibold text-slate-600">Inventario (Stock: <?= e((string)$med['stock_actual']) ?>)</span>
                                            <span class="font-bold <?= $diasRestantes < 4 ? 'text-red-600' : 'text-slate-600' ?>"><?= $diasRestantes ?> días restantes</span>
                                        </div>
                                        <div class="w-full bg-slate-200 rounded-full h-2">
                                            <div class="<?= $barColor ?> h-2 rounded-full" style="width: <?= $stockPct ?>%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php if (!empty($puedeEditar)): ?>
                            <div class="mt-4 sm:mt-0 flex gap-2">
                                <button onclick="confirmarAccion('pausar', <?= e((string)$med['id']) ?>)" class="px-3 py-1.5 bg-amber-50 text-amber-600 text-xs font-semibold rounded-lg border border-amber-200 hover:bg-amber-100 transition">Pausar</button>
                                <button onclick="confirmarAccion('eliminar', <?= e((string)$med['id']) ?>)" class="px-3 py-1.5 bg-red-50 text-red-600 text-xs font-semibold rounded-lg border border-red-200 hover:bg-red-100 transition">Eliminar</button>
                            </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>

        <?php if (!empty($puedeEditar)): ?>
        <!-- Columna 3: Formulario para añadir medicamento (Solo para perfiles con permiso de edición) -->
        <section class="bg-blue-50 p-6 rounded-2xl border border-blue-100 shadow-sm h-fit sticky top-24">
            <h2 class="text-md font-bold text-blue-900 mb-4">Añadir Nuevo Fármaco</h2>
            
            <form action="<?= APP_URL ?>/medicamentos/crear" method="POST" class="space-y-4">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                <input type="hidden" name="paciente_id" value="<?= e((string)($paciente['id'] ?? '')) ?>">
                
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nombre Comercial</label>
                    <input type="text" name="nombre_comercial" required class="w-full rounded-lg border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Ej. Paracetamol">
                </div>
                
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Dosis (Presentación)</label>
                    <input type="text" name="dosis" required class="w-full rounded-lg border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Ej. 1 Tableta (500mg)">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Frecuencia (Horas)</label>
                        <input type="number" name="frecuencia_horas" min="1" max="72" value="8" required class="w-full rounded-lg border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Dosis por Toma</label>
                        <input type="number" name="dosis_por_toma" min="1" max="20" value="1" required class="w-full rounded-lg border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Stock Inicial</label>
                        <input type="number" name="stock_actual" min="0" value="30" required class="w-full rounded-lg border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Alerta Stock Mínimo</label>
                        <input type="number" name="stock_minimo_alerta" min="1" value="5" required class="w-full rounded-lg border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Instrucciones Especiales</label>
                    <textarea name="instrucciones" rows="2" class="w-full rounded-lg border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Ej. Tomar en ayunas con agua"></textarea>
                </div>

                <button type="submit" class="w-full py-2.5 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 transition">
                    Guardar Medicamento
                </button>
            </form>
        </section>
        <?php endif; ?>
    </main>

    <script>
        function confirmarAccion(accion, id) {
            const mensajes = {
                pausar: { title: '¿Pausar tratamiento?', text: 'El medicamento dejará de generar recordatorios diarios.' },
                eliminar: { title: '¿Eliminar medicamento?', text: 'Esta acción no se puede deshacer y borrará el historial.' }
            };

            Swal.fire({
                title: mensajes[accion].title,
                text: mensajes[accion].text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: accion === 'eliminar' ? '#ef4444' : '#f59e0b',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Sí, proceder',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Aquí iría el submit de un form oculto o fetch request
                    Swal.fire('Completado', 'La acción se realizó con éxito.', 'success');
                }
            });
        }
    </script>
</body>
</html>
