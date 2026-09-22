<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VitalSenior - Expediente Médico</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        function switchTab(tabId) {
            document.querySelectorAll('.tab-content').forEach(t => t.classList.add('hidden'));
            document.querySelectorAll('.tab-btn').forEach(t => {
                t.classList.remove('border-blue-600', 'text-blue-600');
                t.classList.add('border-transparent', 'text-slate-500');
            });
            
            document.getElementById(tabId).classList.remove('hidden');
            document.getElementById('btn-' + tabId).classList.remove('border-transparent', 'text-slate-500');
            document.getElementById('btn-' + tabId).classList.add('border-blue-600', 'text-blue-600');
        }
    </script>
</head>
<body class="bg-slate-50 text-slate-900 font-sans min-h-screen">
    <header class="bg-white border-b border-slate-200 sticky top-0 z-30 px-6 py-4 flex justify-between items-center shadow-sm">
        <div class="flex items-center space-x-3">
            <a href="<?= APP_URL ?>/dashboard" class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center text-white font-bold text-xl hover:bg-blue-700 transition">←</a>
            <div>
                <h1 class="text-lg font-bold text-slate-800 leading-tight">Expediente Médico</h1>
                <?php 
                    $nombrePaciente = trim(($paciente['nombre'] ?? '') . ' ' . ($paciente['apellidos'] ?? '')) ?: ($paciente['nombre_completo'] ?? 'Paciente VitalSenior');
                    $uuidPublico = $paciente['uuid_publico'] ?? ($paciente['id'] ?? '');
                ?>
                <p class="text-xs text-slate-500">Paciente: <span class="font-semibold text-blue-700"><?= e($nombrePaciente) ?></span></p>
            </div>
        </div>
        <div class="flex items-center space-x-4">
            <button type="button" 
                    id="btn-abrir-qr"
                    onclick="abrirModalQR('<?= e($uuidPublico) ?>', '<?= e($nombrePaciente) ?>')" 
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-sm transition flex items-center space-x-2 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                </svg>
                <span>Generar / Imprimir Código QR de Emergencia</span>
            </button>
        </div>
    </header>

    <main class="max-w-4xl mx-auto p-6 mt-4">
        <?php if (!empty($flash)): ?>
            <div class="mb-4 p-4 rounded-lg <?= $flash['type'] === 'success' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' ?>">
                <?= htmlspecialchars($flash['message']); ?>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <!-- Tabs Navigation -->
            <div class="flex border-b border-slate-200 px-6">
                <button id="btn-tab-datos" onclick="switchTab('tab-datos')" class="tab-btn py-4 px-4 font-bold text-sm border-b-2 border-blue-600 text-blue-600 focus:outline-none">Datos Básicos</button>
                <button id="btn-tab-padecimientos" onclick="switchTab('tab-padecimientos')" class="tab-btn py-4 px-4 font-bold text-sm border-b-2 border-transparent text-slate-500 hover:text-slate-700 focus:outline-none">Alergias y Padecimientos</button>
                <button id="btn-tab-doctores" onclick="switchTab('tab-doctores')" class="tab-btn py-4 px-4 font-bold text-sm border-b-2 border-transparent text-slate-500 hover:text-slate-700 focus:outline-none">Doctores</button>
            </div>

            <div class="p-6">
                <!-- Tab: Datos Básicos -->
                <div id="tab-datos" class="tab-content space-y-4">
                    <form action="<?= APP_URL ?>/expediente/actualizar" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                        <input type="hidden" name="seccion" value="datos">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">Tipo de Sangre</label>
                                <select name="tipo_sangre" class="w-full rounded-lg border-slate-300 px-3 py-2 text-sm focus:border-blue-500">
                                    <option value="N/D" <?= ($paciente['tipo_sangre'] ?? 'N/D') === 'N/D' ? 'selected' : '' ?>>No Especificado</option>
                                    <?php foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $ts): ?>
                                        <option value="<?= $ts ?>" <?= ($paciente['tipo_sangre'] ?? '') === $ts ? 'selected' : '' ?>><?= $ts ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">Peso (kg)</label>
                                <input type="number" step="0.1" name="peso" value="<?= e((string)($paciente['peso'] ?? '')) ?>" class="w-full rounded-lg border-slate-300 px-3 py-2 text-sm focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">Altura (cm)</label>
                                <input type="number" step="1" name="altura" value="<?= e((string)($paciente['altura'] ?? '')) ?>" class="w-full rounded-lg border-slate-300 px-3 py-2 text-sm focus:border-blue-500">
                            </div>
                        </div>
                        <div class="mt-6">
                            <button type="submit" class="px-6 py-2 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700">Guardar Cambios</button>
                        </div>
                    </form>
                </div>

                <!-- Tab: Alergias y Padecimientos -->
                <div id="tab-padecimientos" class="tab-content hidden space-y-6">
                    <form action="<?= APP_URL ?>/expediente/actualizar" method="POST" class="space-y-4">
                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                        <input type="hidden" name="seccion" value="padecimientos">
                        
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Alergias Conocidas</label>
                            <textarea name="alergias" rows="2" class="w-full rounded-lg border-slate-300 px-3 py-2 text-sm focus:border-blue-500" placeholder="Ej. Penicilina, Nueces"><?= e($paciente['alergias'] ?? '') ?></textarea>
                            <p class="text-xs text-slate-500 mt-1">Separa con comas.</p>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Padecimientos Crónicos / Mayores</label>
                            <textarea name="enfermedades_cronicas" rows="3" class="w-full rounded-lg border-slate-300 px-3 py-2 text-sm focus:border-blue-500" placeholder="Ej. Hipertensión, Diabetes Tipo 2, Marcapasos"><?= e($paciente['enfermedades_cronicas'] ?? '') ?></textarea>
                            <p class="text-xs text-slate-500 mt-1">Esta información aparecerá en la Ficha de Emergencia pública.</p>
                        </div>
                        
                        <div class="mt-6">
                            <button type="submit" class="px-6 py-2 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700">Guardar Cambios</button>
                        </div>
                    </form>
                </div>

                <!-- Tab: Doctores -->
                <div id="tab-doctores" class="tab-content hidden space-y-6">
                    <?php if (!empty($doctores)): ?>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <?php foreach ($doctores as $doc): ?>
                                <div class="p-4 bg-slate-50 rounded-xl border border-slate-200">
                                    <h3 class="font-bold text-slate-800">Dr. <?= e($doc['nombre']) ?></h3>
                                    <p class="text-sm text-slate-600"><?= e($doc['especialidad']) ?></p>
                                    <p class="text-sm text-blue-600 font-semibold mt-1">📞 <?= e($doc['telefono']) ?></p>
                                    <form action="<?= APP_URL ?>/expediente/eliminar_doctor" method="POST" class="mt-3" onsubmit="return confirm('¿Seguro que deseas eliminar este doctor?');">
                                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                        <input type="hidden" name="doctor_id" value="<?= $doc['id'] ?>">
                                        <button type="submit" class="text-xs text-red-600 font-semibold hover:underline">Eliminar</button>
                                    </form>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-sm text-slate-500">No hay médicos registrados.</p>
                    <?php endif; ?>

                    <hr class="border-slate-200">
                    <h3 class="font-bold text-slate-800">Añadir Médico Tratante</h3>
                    <form action="<?= APP_URL ?>/expediente/anadir_doctor" method="POST" class="bg-blue-50 p-4 rounded-xl border border-blue-100">
                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Nombre</label>
                                <input type="text" name="nombre" required class="w-full rounded-lg border-slate-300 px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Especialidad</label>
                                <input type="text" name="especialidad" class="w-full rounded-lg border-slate-300 px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Teléfono</label>
                                <input type="tel" name="telefono" required class="w-full rounded-lg border-slate-300 px-3 py-2 text-sm">
                            </div>
                        </div>
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white text-sm font-bold rounded-lg hover:bg-blue-700">Añadir Médico</button>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal de Código QR de Emergencia (Vista 6) -->
    <div id="modal-qr-emergencia" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-3xl shadow-2xl max-w-sm w-full p-6 border border-slate-100 transform transition-all text-center relative print-area">
            <!-- Botón Cerrar (no imprimible) -->
            <button type="button" onclick="cerrarModalQR()" class="no-print absolute top-4 right-4 text-slate-400 hover:text-slate-600 p-2 rounded-full hover:bg-slate-100 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            <!-- Encabezado del Carnet de Emergencia -->
            <div class="flex items-center justify-center space-x-2 mb-2">
                <span class="w-7 h-7 bg-red-600 rounded-lg flex items-center justify-center text-white font-black text-sm">✚</span>
                <span class="font-extrabold text-slate-900 tracking-tight text-lg">VitalSenior SOS</span>
            </div>
            <p class="text-xs text-slate-500 font-medium">Carnet Médico de Emergencia</p>
            
            <h3 id="qr-paciente-nombre" class="mt-3 text-base font-bold text-slate-800 leading-tight">
                <?= e($nombrePaciente) ?>
            </h3>

            <!-- Canvas contenedor del QR renderizado client-side -->
            <div class="my-4 flex justify-center bg-slate-50 p-4 rounded-2xl border border-slate-200">
                <canvas id="qr-canvas" class="mx-auto rounded-lg shadow-sm"></canvas>
            </div>

            <p class="text-[11px] text-slate-500 leading-relaxed">
                Escanee este código con cualquier cámara de smartphone para acceder inmediatamente a la información de emergencia, tipo de sangre y contactos de auxilio.
            </p>

            <!-- Acciones Modal (no imprimibles) -->
            <div class="mt-6 flex flex-col sm:flex-row gap-2 no-print">
                <button type="button" onclick="imprimirCarnetQR()" class="flex-1 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-md shadow-blue-500/20 transition flex items-center justify-center space-x-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H9v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    <span>Imprimir Carnet</span>
                </button>
                <button type="button" onclick="cerrarModalQR()" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition cursor-pointer">
                    Cerrar
                </button>
            </div>
        </div>
    </div>

    <!-- Estilos de Impresión Exclusiva para el Carnet QR -->
    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            #modal-qr-emergencia, #modal-qr-emergencia .print-area, #modal-qr-emergencia .print-area * {
                visibility: visible;
            }
            #modal-qr-emergencia {
                position: fixed;
                left: 0;
                top: 0;
                width: 100vw;
                height: 100vh;
                display: flex !important;
                align-items: center;
                justify-content: center;
                background: white !important;
                box-shadow: none !important;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>

    <!-- Script de Generación Client-Side y Manejador del Modal -->
    <script src="<?= APP_URL ?>/assets/js/qr-modal.js"></script>
</body>
</html>
