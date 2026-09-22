<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-50 antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VitalSenior - Expediente Clínico de <?= e($paciente['nombre'] ?? 'Paciente') ?></title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS desde CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'system-ui', '-apple-system', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    },
                    boxShadow: {
                        'subtle': '0 1px 3px 0 rgba(0, 0, 0, 0.04), 0 1px 2px 0 rgba(0, 0, 0, 0.02)',
                        'card': '0 4px 20px -2px rgba(0, 0, 0, 0.05), 0 0 0 1px rgba(226, 232, 240, 0.8)',
                        'glow-rose': '0 4px 20px -2px rgba(225, 29, 72, 0.15)',
                        'glow-amber': '0 4px 20px -2px rgba(217, 119, 6, 0.15)',
                    }
                }
            }
        }
    </script>
    <style>
        .tap-bounce {
            transition: transform 0.15s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.15s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .tap-bounce:active {
            transform: scale(0.98);
        }
        .touch-min {
            min-height: 48px;
            min-width: 48px;
        }
        @media print {
            body { background: white !important; }
            header, .no-print, nav { display: none !important; }
            main { padding: 0 !important; grid-template-columns: 1fr !important; }
            .border-slate-200 { border: 1px solid #ccc !important; }
            .shadow-card, .shadow-sm { box-shadow: none !important; }
        }
    </style>
</head>
<body id="dashboard-body" class="bg-slate-50 text-slate-900 font-sans min-h-screen transition-opacity duration-500 opacity-0 flex flex-col justify-between selection:bg-blue-600/20 selection:text-blue-700">

    <!-- 1. Encabezado Reestructurado del Paciente Activo -->
    <header class="bg-white/95 backdrop-blur-md border-b border-slate-200/90 sticky top-0 z-30 shadow-xs px-4 sm:px-6 lg:px-8 py-3.5 no-print">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4">
            
            <!-- Bloque Izquierdo: Brand e Identidad del Paciente -->
            <div class="flex items-center justify-between sm:justify-start gap-3 sm:gap-4">
                <a href="<?= APP_URL ?>/dashboard" class="tap-bounce flex items-center gap-2.5 group shrink-0">
                    <div class="w-10 h-10 rounded-2xl bg-blue-600 flex items-center justify-center text-white shadow-md shadow-blue-500/20 text-lg font-black group-hover:scale-105 transition-transform">
                        ✚
                    </div>
                    <div class="hidden xs:block">
                        <span class="text-base sm:text-lg font-black tracking-tight text-slate-900">Vital<span class="text-blue-600">Senior</span></span>
                        <span class="block text-[10px] uppercase font-bold tracking-wider text-slate-400 -mt-1">Panel Clínico</span>
                    </div>
                </a>

                <!-- Selector de Paciente / Identificación -->
                <div class="flex items-center gap-2 pl-2 sm:pl-3 border-l border-slate-200">
                    <?php if (!empty($todosLosPacientes) && count($todosLosPacientes) > 1): ?>
                        <form action="<?= APP_URL ?>/dashboard" method="GET" class="relative inline-block m-0">
                            <label for="select-paciente" class="sr-only">Seleccionar Paciente</label>
                            <select id="select-paciente" 
                                    name="seleccionar_paciente" 
                                    onchange="this.form.submit()" 
                                    class="text-xs sm:text-sm font-extrabold text-blue-900 bg-blue-50/90 hover:bg-blue-100 border border-blue-200 rounded-xl py-2 pl-3 pr-8 focus:ring-2 focus:ring-blue-600 focus:border-blue-600 cursor-pointer shadow-xs transition-all appearance-none">
                                <?php foreach ($todosLosPacientes as $p): ?>
                                    <option value="<?= e((string)$p['id']) ?>" <?= $p['id'] == $pacienteActivoId ? 'selected' : '' ?>>
                                        👤 <?= e($p['nombre'] . ' ' . $p['apellidos']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2.5 text-blue-600 text-xs font-bold">
                                ▾
                            </div>
                        </form>
                    <?php else: ?>
                        <div class="flex items-center gap-1.5 bg-blue-50/80 border border-blue-200/80 px-3 py-1.5 rounded-xl">
                            <span class="text-xs">👤</span>
                            <span class="text-xs sm:text-sm font-extrabold text-blue-900">
                                <?= e($paciente['nombre'] . ' ' . $paciente['apellidos']) ?>
                            </span>
                        </div>
                    <?php endif; ?>

                    <a href="<?= APP_URL ?>/vincular" 
                       class="tap-bounce touch-min sm:touch-min-none px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl border border-slate-200 transition flex items-center justify-center shrink-0" 
                       title="Vincular otro Adulto Mayor">
                        <span>+ Vincular</span>
                    </a>
                </div>

                <!-- Botón de Menú Móvil Kebab -->
                <div class="sm:hidden relative">
                    <button id="btn-mobile-menu" 
                            type="button" 
                            onclick="document.getElementById('mobile-actions-menu').classList.toggle('hidden')"
                            class="tap-bounce touch-min w-10 h-10 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold flex items-center justify-center text-lg border border-slate-200">
                        ⋮
                    </button>
                    <!-- Menú Desplegable Móvil -->
                    <div id="mobile-actions-menu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-2xl shadow-xl border border-slate-200 py-1.5 z-50 animate-fadeIn">
                        <button onclick="window.print()" class="w-full text-left px-4 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 flex items-center gap-2">
                            <span>🖨️</span> Imprimir Expediente
                        </button>
                        <a href="<?= APP_URL ?>/emergencia?token=<?= e((string)($paciente['qr_token'] ?? '')) ?>" target="_blank" class="w-full text-left px-4 py-2.5 text-xs font-bold text-blue-700 hover:bg-blue-50 flex items-center gap-2">
                            <span>📱</span> Ficha QR Pública
                        </a>
                        <div class="border-t border-slate-100 my-1"></div>
                        <a href="<?= APP_URL ?>/logout" class="w-full text-left px-4 py-2.5 text-xs font-bold text-rose-600 hover:bg-rose-50 flex items-center gap-2">
                            <span>🚪</span> Cerrar Sesión
                        </a>
                    </div>
                </div>
            </div>

            <!-- Bloque Derecho: Acciones Compactas en Escritorio (sm+) -->
            <div class="hidden sm:flex items-center gap-2 shrink-0">
                <button onclick="window.print()" class="tap-bounce touch-min sm:touch-min-none px-3 py-2 bg-slate-100 hover:bg-slate-200 active:bg-slate-300 text-slate-700 text-xs font-bold rounded-xl border border-slate-200 transition flex items-center gap-1.5 shadow-xs">
                    <span>🖨️</span>
                    <span>Imprimir</span>
                </button>
                <a href="<?= APP_URL ?>/emergencia?token=<?= e((string)($paciente['qr_token'] ?? '')) ?>" target="_blank" class="tap-bounce touch-min sm:touch-min-none px-3.5 py-2 bg-blue-50 hover:bg-blue-100 active:bg-blue-200 text-blue-700 text-xs font-bold rounded-xl border border-blue-200 transition flex items-center gap-1.5 shadow-xs">
                    <span>📱</span>
                    <span>Ficha QR</span>
                </a>
                <a href="<?= APP_URL ?>/logout" class="tap-bounce touch-min sm:touch-min-none px-3 py-2 bg-rose-50 hover:bg-rose-100 active:bg-rose-200 text-rose-600 text-xs font-bold rounded-xl border border-rose-200 transition flex items-center gap-1.5 shadow-xs">
                    <span>Cerrar Sesión</span>
                </a>
            </div>

        </div>
    </header>

    <!-- 2. Contenido Principal del Dashboard -->
    <main id="accessible-content" class="max-w-7xl mx-auto p-4 sm:p-6 pb-24 md:pb-8 w-full grid grid-cols-1 lg:grid-cols-3 gap-5 sm:gap-6">
        <?php require_once dirname(__DIR__) . '/components/toast.php'; ?>

        <!-- BANNER DE ALERTAS DINÁMICAS (Anti-slop & Modern UI) -->
        <?php if (!empty($alertasDosis) || !empty($alertasInventario)): ?>
            <div class="lg:col-span-3 space-y-3 no-print">
                
                <!-- Alertas Críticas: Dosis Omitidas -->
                <?php foreach ($alertasDosis as $ad): ?>
                    <div class="bg-rose-50/90 hover:bg-rose-50 border border-rose-200/90 rounded-2xl sm:rounded-3xl p-4 sm:p-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3.5 shadow-glow-rose transition-all">
                        <div class="flex items-start sm:items-center gap-3.5">
                            <div class="w-11 h-11 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center text-xl shrink-0 border border-rose-200/80 shadow-xs">
                                🚨
                            </div>
                            <div>
                                <div class="flex items-center gap-2 mb-0.5">
                                    <span class="bg-rose-100 text-rose-700 text-[10px] font-black uppercase px-2.5 py-0.5 rounded-full border border-rose-200 tracking-wider">
                                        Crítica
                                    </span>
                                    <h3 class="text-rose-950 font-extrabold text-sm sm:text-base tracking-tight">
                                        Dosis Omitida no Registrada
                                    </h3>
                                </div>
                                <p class="text-slate-700 text-xs sm:text-sm font-medium leading-relaxed">
                                    Se omitió la toma de <strong class="text-slate-900 font-black"><?= e($ad['nombre_comercial']) ?></strong> programada para las <span class="font-mono font-bold text-rose-800 bg-rose-100/60 px-1.5 py-0.5 rounded"><?= date('H:i', strtotime($ad['hora_programada'])) ?></span>.
                                    <?= !empty($ad['nota_incidencia']) ? '<span class="text-slate-500 italic">(' . e($ad['nota_incidencia']) . ')</span>' : '' ?>
                                </p>
                            </div>
                        </div>
                        <a href="<?= APP_URL ?>/medicamentos/control" 
                           class="tap-bounce touch-min w-full sm:w-auto px-5 py-2.5 bg-rose-600 hover:bg-rose-700 active:bg-rose-800 text-white font-bold text-xs sm:text-sm rounded-xl shadow-md shadow-rose-600/20 transition-all flex items-center justify-center space-x-1.5 whitespace-nowrap self-stretch sm:self-center">
                            <span>Gestionar Toma</span>
                            <span>→</span>
                        </a>
                    </div>
                <?php endforeach; ?>

                <!-- Alertas de Inventario: Stock Bajo -->
                <?php foreach ($alertasInventario as $ai): ?>
                    <div class="bg-amber-50/90 hover:bg-amber-50 border border-amber-200/90 rounded-2xl sm:rounded-3xl p-4 sm:p-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3.5 shadow-glow-amber transition-all">
                        <div class="flex items-start sm:items-center gap-3.5">
                            <div class="w-11 h-11 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center text-xl shrink-0 border border-amber-200/80 shadow-xs">
                                ⚠️
                            </div>
                            <div>
                                <div class="flex items-center gap-2 mb-0.5">
                                    <span class="bg-amber-100 text-amber-800 text-[10px] font-black uppercase px-2.5 py-0.5 rounded-full border border-amber-200 tracking-wider">
                                        Inventario
                                    </span>
                                    <h3 class="text-amber-950 font-extrabold text-sm sm:text-base tracking-tight">
                                        Suministro Próximo a Agotarse
                                    </h3>
                                </div>
                                <p class="text-slate-700 text-xs sm:text-sm font-medium leading-relaxed">
                                    Quedan aprox. <strong class="text-amber-900 font-black font-mono"><?= e((string)$ai['dias_restantes']) ?> días</strong> de <strong class="text-slate-900"><?= e($ai['nombre_comercial']) ?></strong> (Stock restante: <span class="font-bold text-slate-800"><?= e((string)$ai['stock_actual']) ?> uds</span>).
                                </p>
                            </div>
                        </div>
                        <a href="<?= APP_URL ?>/medicamentos" 
                           class="tap-bounce touch-min w-full sm:w-auto px-5 py-2.5 bg-amber-600 hover:bg-amber-700 active:bg-amber-800 text-white font-bold text-xs sm:text-sm rounded-xl shadow-md shadow-amber-600/20 transition-all flex items-center justify-center space-x-1.5 whitespace-nowrap self-stretch sm:self-center">
                            <span>Resurtir Stock</span>
                            <span>→</span>
                        </a>
                    </div>
                <?php endforeach; ?>

            </div>
        <?php endif; ?>

        <!-- Columna 1: Registro de Salud Diario, Datos Vitales y Contactos -->
        <section class="space-y-5">
            <!-- Registro de Salud Diario -->
            <div class="bg-white p-5 sm:p-6 rounded-3xl border border-slate-200/80 shadow-card space-y-3">
                <div class="flex items-center justify-between">
                    <h2 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Signos Vitales</h2>
                    <span class="text-base">🩺</span>
                </div>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Monitorea la presión arterial, glucosa, horas de sueño y nivel de dolor hoy.
                </p>
                <a href="<?= APP_URL ?>/salud/stepper" class="tap-bounce touch-min w-full py-3.5 px-4 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white rounded-2xl text-center font-bold text-sm shadow-md shadow-blue-600/20 transition-all flex items-center justify-center gap-2">
                    <span>+ Registrar Signos de Hoy</span>
                </a>
            </div>

            <!-- Tarjeta Datos Vitales -->
            <div class="bg-white p-5 sm:p-6 rounded-3xl border border-slate-200/80 shadow-card space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Perfil Clínico Base</h2>
                    <a href="<?= APP_URL ?>/expediente" class="text-xs font-bold text-blue-600 hover:underline">Ver Todo →</a>
                </div>
                
                <div class="grid grid-cols-2 gap-2.5">
                    <div class="bg-blue-50/70 p-3 rounded-2xl border border-blue-100">
                        <span class="text-[11px] text-blue-600 font-bold block">Tipo de Sangre</span>
                        <span class="text-lg font-black text-blue-900"><?= e($paciente['tipo_sangre'] ?: 'N/D') ?></span>
                    </div>
                    <div class="bg-slate-50 p-3 rounded-2xl border border-slate-200/80">
                        <span class="text-[11px] text-slate-500 font-bold block">Nacimiento</span>
                        <span class="text-xs font-bold text-slate-800 tabular-nums"><?= e($paciente['fecha_nacimiento'] ?: 'No reg.') ?></span>
                    </div>
                </div>

                <div>
                    <span class="text-[11px] font-bold text-slate-500 block mb-1">Alergias Conocidas</span>
                    <p class="text-xs text-rose-700 font-semibold bg-rose-50/80 p-2.5 rounded-xl border border-rose-100">
                        <?= e($paciente['alergias'] ?: 'Sin alergias registradas') ?>
                    </p>
                </div>

                <div>
                    <span class="text-[11px] font-bold text-slate-500 block mb-1">Padecimientos Crónicos</span>
                    <p class="text-xs text-slate-700 font-medium bg-slate-50 p-2.5 rounded-xl border border-slate-200/80">
                        <?= e($paciente['enfermedades_cronicas'] ?: 'Ninguno registrado') ?>
                    </p>
                </div>
            </div>

            <!-- Contactos de Emergencia -->
            <div class="bg-white p-5 sm:p-6 rounded-3xl border border-slate-200/80 shadow-card space-y-3">
                <div class="flex items-center justify-between">
                    <h2 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Contactos Prioritarios</h2>
                    <span class="text-xs font-bold text-emerald-600">Red SOS</span>
                </div>
                <div class="space-y-2">
                    <?php if (empty($contactosEmergencia)): ?>
                        <p class="text-xs text-slate-400 italic py-2">No hay contactos registrados.</p>
                    <?php else: ?>
                        <?php foreach ($contactosEmergencia as $c): ?>
                            <div class="flex justify-between items-center p-3 bg-slate-50 rounded-2xl border border-slate-200/60">
                                <div>
                                    <p class="text-xs font-bold text-slate-900"><?= e($c['nombre']) ?></p>
                                    <span class="text-[11px] text-slate-500"><?= e($c['parentesco']) ?></span>
                                </div>
                                <a href="tel:<?= e($c['telefono']) ?>" class="tap-bounce touch-min sm:touch-min-none px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white rounded-xl text-xs font-bold transition flex items-center gap-1 shadow-xs">
                                    <span>📞</span>
                                    <span>Llamar</span>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- Columna 2: Medicación y Tomas del Día -->
        <section class="bg-white p-5 sm:p-6 rounded-3xl border border-slate-200/80 shadow-card flex flex-col justify-between space-y-4">
            <div>
                <div class="flex justify-between items-center mb-3">
                    <h2 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Tomas Programadas (Hoy)</h2>
                    <span class="text-xs font-extrabold text-blue-600 bg-blue-50 px-2.5 py-1 rounded-lg border border-blue-100 tabular-nums">
                        <?= date('d/m/Y') ?>
                    </span>
                </div>

                <div class="space-y-3">
                    <?php if (empty($tomasDelDia)): ?>
                        <div class="text-center py-10">
                            <span class="text-3xl block mb-2">🎉</span>
                            <p class="text-xs font-bold text-slate-600">No hay tomas pendientes para hoy</p>
                            <p class="text-[11px] text-slate-400 mt-0.5">El paciente tiene su esquema al día.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($tomasDelDia as $toma): ?>
                            <?php include __DIR__ . '/../medicamentos/partials/card_toma.php'; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex flex-col gap-2">
                <a href="<?= APP_URL ?>/medicamentos/control" class="tap-bounce touch-min sm:touch-min-none block text-center py-2.5 px-4 bg-slate-100 hover:bg-slate-200 text-xs font-bold text-slate-700 rounded-xl transition border border-slate-200/60">
                    Ver Control por Horarios
                </a>
                <a href="<?= APP_URL ?>/medicamentos" class="block text-center text-xs font-bold text-blue-600 hover:underline py-1">
                    Gestionar Catálogo de Fármacos →
                </a>
            </div>
        </section>

        <!-- Columna 3: Próximas Citas Médicas y Laboratorios -->
        <section class="bg-white p-5 sm:p-6 rounded-3xl border border-slate-200/80 shadow-card flex flex-col justify-between space-y-4">
            <div>
                <div class="flex justify-between items-center mb-3">
                    <h2 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Agenda y Citas Médicas</h2>
                    <a href="<?= APP_URL ?>/citas" class="text-xs font-bold text-blue-600 hover:underline">+ Agendar</a>
                </div>

                <div class="space-y-2.5">
                    <?php if (empty($proximasCitas)): ?>
                        <div class="text-center py-10">
                            <span class="text-3xl block mb-2">📅</span>
                            <p class="text-xs font-bold text-slate-600">Sin eventos próximos</p>
                            <p class="text-[11px] text-slate-400 mt-0.5">No hay citas médicas ni análisis agendados.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($proximasCitas as $cita): ?>
                            <div class="p-3.5 bg-slate-50 hover:bg-slate-100/80 rounded-2xl border border-slate-200/70 transition-all">
                                <div class="flex justify-between items-start mb-1">
                                    <span class="text-[10px] font-black uppercase px-2 py-0.5 rounded-md tracking-wider <?= $cita['tipo_evento'] === 'laboratorio' ? 'bg-purple-100 text-purple-700 border border-purple-200' : 'bg-blue-100 text-blue-700 border border-blue-200' ?>">
                                        <?= e($cita['tipo_evento']) ?>
                                    </span>
                                    <span class="text-[11px] font-mono font-bold text-slate-500 tabular-nums">
                                        <?= date('d M, H:i', strtotime($cita['fecha_hora'])) ?>
                                    </span>
                                </div>
                                <h3 class="text-xs font-extrabold text-slate-900"><?= e($cita['titulo']) ?></h3>
                                <p class="text-[11px] text-slate-600 mt-0.5">Lugar: <?= e($cita['lugar']) ?></p>
                                <?php if (!empty($cita['doctor_nombre'])): ?>
                                    <p class="text-[11px] text-slate-500 font-semibold mt-0.5">Dr(a): <?= e($cita['doctor_nombre']) ?></p>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100">
                <a href="<?= APP_URL ?>/citas" class="block text-center text-xs font-bold text-blue-600 hover:underline py-1">
                    Ver Calendario Completo →
                </a>
            </div>
        </section>
    </main>

    <!-- Barra de Navegación Móvil Fija -->
    <div class="no-print">
        <?php require_once dirname(__DIR__) . '/components/bottom_nav.php'; ?>
    </div>

    <!-- Script de Transición Suave y Cierre de Menú Kebab -->
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            requestAnimationFrame(() => {
                const body = document.getElementById('dashboard-body');
                if (body) {
                    body.classList.remove('opacity-0');
                    body.classList.add('opacity-100');
                }
            });

            // Cerrar menú móvil al hacer clic fuera
            document.addEventListener('click', (e) => {
                const menu = document.getElementById('mobile-actions-menu');
                const btn = document.getElementById('btn-mobile-menu');
                if (menu && btn && !btn.contains(e.target) && !menu.contains(e.target)) {
                    menu.classList.add('hidden');
                }
            });
        });
    </script>

</body>
</html>