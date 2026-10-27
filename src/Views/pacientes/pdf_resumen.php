<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Expediente Clínico - <?= htmlspecialchars(($paciente['nombre'] ?? '') . ' ' . ($paciente['apellidos'] ?? '')) ?></title>
    <style>
        @page {
            margin: 25px 30px 30px 30px;
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #1e293b;
            line-height: 1.4;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
        }
        .header {
            width: 100%;
            border-bottom: 2px solid #0d9488;
            padding-bottom: 12px;
            margin-bottom: 15px;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
        }
        .brand-title {
            font-size: 20px;
            font-weight: bold;
            color: #0d9488;
            margin: 0;
        }
        .brand-subtitle {
            font-size: 10px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 2px 0 0 0;
        }
        .meta-text {
            text-align: right;
            font-size: 9px;
            color: #64748b;
        }
        .meta-text strong {
            color: #0f172a;
        }
        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #0f766e;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1.5px solid #ccfbf1;
            padding-bottom: 4px;
            margin-top: 14px;
            margin-bottom: 8px;
        }
        .table-data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .table-data th, .table-data td {
            padding: 6px 8px;
            font-size: 10px;
            border: 1px solid #e2e8f0;
            text-align: left;
        }
        .table-data th {
            background-color: #f8fafc;
            color: #334155;
            font-weight: bold;
        }
        .table-data tr:nth-child(even) td {
            background-color: #fafafa;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: bold;
        }
        .badge-danger {
            background-color: #fee2e2;
            color: #991b1b;
        }
        .badge-success {
            background-color: #dcfce7;
            color: #166534;
        }
        .badge-info {
            background-color: #e0f2fe;
            color: #0369a1;
        }
        .kpi-container {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .kpi-box {
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 8px;
            text-align: center;
            background-color: #f8fafc;
        }
        .kpi-label {
            font-size: 9px;
            color: #64748b;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 4px;
        }
        .kpi-val {
            font-size: 14px;
            font-weight: bold;
            color: #0f172a;
        }
        .kpi-unit {
            font-size: 8px;
            color: #64748b;
        }
        .no-data {
            font-style: italic;
            color: #94a3b8;
            padding: 6px 0;
            font-size: 10px;
        }
        .footer {
            margin-top: 20px;
            border-top: 1px solid #e2e8f0;
            padding-top: 8px;
            text-align: center;
            font-size: 8px;
            color: #94a3b8;
        }
        .page-avoid {
            page-break-inside: avoid;
        }
    </style>
</head>
<body>

    <!-- Encabezado Clínico -->
    <div class="header">
        <table class="header-table">
            <tr>
                <td style="width: 55%; vertical-align: middle;">
                    <div class="brand-title">VitalSenior</div>
                    <div class="brand-subtitle">Expediente y Resumen Clínico Integral</div>
                </td>
                <td style="width: 45%; vertical-align: middle;" class="meta-text">
                    <div><strong>Folio Paciente:</strong> #<?= str_pad((string)($paciente['id'] ?? '0'), 5, '0', STR_PAD_LEFT) ?></div>
                    <div><strong>Emisión:</strong> <?= date('d/m/Y H:i') ?> hrs</div>
                    <div><strong>Estado:</strong> Expediente Activo</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- 1. Ficha Demográfica y Antecedentes -->
    <div class="page-avoid">
        <div class="section-title">1. Datos Demográficos y Perfil Médico</div>
        <table class="table-data">
            <tr>
                <th style="width: 20%;">Nombre Completo:</th>
                <td style="width: 30%;"><strong><?= htmlspecialchars(($paciente['nombre'] ?? '') . ' ' . ($paciente['apellidos'] ?? '')) ?></strong></td>
                <th style="width: 20%;">Tipo de Sangre:</th>
                <td style="width: 30%;"><span class="badge badge-info"><?= htmlspecialchars($paciente['tipo_sangre'] ?? 'N/D') ?></span></td>
            </tr>
            <tr>
                <th>Fecha de Nacimiento:</th>
                <td><?= !empty($paciente['fecha_nacimiento']) ? date('d/m/Y', strtotime($paciente['fecha_nacimiento'])) : 'No registrada' ?></td>
                <th>Edad Calculada:</th>
                <td>
                    <?php
                    if (!empty($paciente['fecha_nacimiento'])) {
                        $nac = new DateTime($paciente['fecha_nacimiento']);
                        $hoy = new DateTime();
                        echo $hoy->diff($nac)->y . ' años';
                    } else {
                        echo 'N/D';
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <th>Alergias Conocidas:</th>
                <td colspan="3">
                    <?php if (!empty($paciente['alergias'])): ?>
                        <span class="badge badge-danger"><?= htmlspecialchars($paciente['alergias']) ?></span>
                    <?php else: ?>
                        <span style="color: #64748b;">Sin alergias registradas</span>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <th>Enfermedades Crónicas:</th>
                <td colspan="3">
                    <?= !empty($paciente['enfermedades_cronicas']) ? htmlspecialchars($paciente['enfermedades_cronicas']) : '<span style="color: #64748b;">Ninguna registrada</span>' ?>
                </td>
            </tr>
            <?php if (!empty($paciente['notas_cognitivas'])): ?>
            <tr>
                <th>Estado Funcional / Cognitivo:</th>
                <td colspan="3"><?= htmlspecialchars($paciente['notas_cognitivas']) ?></td>
            </tr>
            <?php endif; ?>
        </table>
    </div>

    <!-- 2. Signos Vitales y Promedios Recientes -->
    <div class="page-avoid">
        <div class="section-title">2. Métricas y Monitoreo de Signos Vitales (Últimos 7 días)</div>
        <table class="kpi-container">
            <tr>
                <td style="width: 33.3%; padding-right: 4px;">
                    <div class="kpi-box">
                        <div class="kpi-label">Presión Arterial Media</div>
                        <div class="kpi-val"><?= ($avgSis > 0 && $avgDia > 0) ? round($avgSis) . '/' . round($avgDia) : 'N/A' ?></div>
                        <div class="kpi-unit">mmHg</div>
                    </div>
                </td>
                <td style="width: 33.3%; padding-left: 2px; padding-right: 2px;">
                    <div class="kpi-box">
                        <div class="kpi-label">Glucosa Promedio</div>
                        <div class="kpi-val"><?= ($avgGluc > 0) ? round($avgGluc, 1) : 'N/A' ?></div>
                        <div class="kpi-unit">mg/dL</div>
                    </div>
                </td>
                <td style="width: 33.3%; padding-left: 4px;">
                    <div class="kpi-box">
                        <div class="kpi-label">Registros Evaluados</div>
                        <div class="kpi-val"><?= count($registrosSalud ?? []) ?></div>
                        <div class="kpi-unit">Tiempos/Muestras</div>
                    </div>
                </td>
            </tr>
        </table>

        <?php if (!empty($registrosSalud)): ?>
            <table class="table-data">
                <thead>
                    <tr>
                        <th style="width: 22%;">Fecha/Hora</th>
                        <th style="width: 20%;">Presión (mmHg)</th>
                        <th style="width: 18%;">Glucosa (mg/dL)</th>
                        <th style="width: 18%;">Pulso (lpm)</th>
                        <th style="width: 22%;">Estado / Ánimo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($registrosSalud as $reg): ?>
                        <tr>
                            <td><?= date('d/m/Y H:i', strtotime($reg['created_at'] ?? 'now')) ?></td>
                            <td>
                                <?= (!empty($reg['presion_sistolica']) && !empty($reg['presion_diastolica'])) 
                                    ? htmlspecialchars($reg['presion_sistolica'] . '/' . $reg['presion_diastolica']) 
                                    : '-' ?>
                            </td>
                            <td><?= !empty($reg['glucosa']) ? htmlspecialchars((string)$reg['glucosa']) : '-' ?></td>
                            <td><?= !empty($reg['pulso']) ? htmlspecialchars((string)$reg['pulso']) : '-' ?></td>
                            <td><?= htmlspecialchars($reg['estado_animo'] ?? 'Normal') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="no-data">No se han registrado signos vitales en los últimos 7 días.</div>
        <?php endif; ?>
    </div>

    <!-- 3. Farmacoterapia y Medicamentos Activos -->
    <div class="page-avoid">
        <div class="section-title">3. Tratamiento Farmacológico y Adherencia Semanal</div>
        <table class="kpi-container" style="margin-bottom: 8px;">
            <tr>
                <td style="width: 50%; padding-right: 4px;">
                    <div class="kpi-box">
                        <div class="kpi-label">Adherencia Semanal a Tomas (7 días)</div>
                        <div class="kpi-val" style="color: <?= ((float)($adherencia ?? 100) >= 80) ? '#166534' : (((float)($adherencia ?? 100) >= 50) ? '#b45309' : '#991b1b') ?>;">
                            <?= number_format((float)($adherencia ?? 100), 1) ?>%
                        </div>
                        <div class="kpi-unit">Dosis administradas a tiempo</div>
                    </div>
                </td>
                <td style="width: 50%; padding-left: 4px;">
                    <div class="kpi-box">
                        <div class="kpi-label">Fármacos Activos Prescritos</div>
                        <div class="kpi-val"><?= count($medicamentos ?? []) ?></div>
                        <div class="kpi-unit">Medicamentos en régimen</div>
                    </div>
                </td>
            </tr>
        </table>

        <?php if (!empty($medicamentos)): ?>
            <table class="table-data">
                <thead>
                    <tr>
                        <th style="width: 30%;">Medicamento</th>
                        <th style="width: 20%;">Dosis</th>
                        <th style="width: 20%;">Frecuencia</th>
                        <th style="width: 30%;">Indicaciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($medicamentos as $med): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($med['nombre_comercial'] ?? '') ?></strong>
                                <?php if (!empty($med['nombre_generico'])): ?>
                                    <div style="font-size: 8px; color: #64748b;"><?= htmlspecialchars($med['nombre_generico']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($med['dosis'] ?? '-') ?></td>
                            <td>Cada <?= htmlspecialchars((string)($med['frecuencia_horas'] ?? '24')) ?> hrs</td>
                            <td><?= htmlspecialchars((string)($med['instrucciones'] ?? 'Sin notas')) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="no-data">El paciente no tiene medicamentos activos configurados.</div>
        <?php endif; ?>
    </div>

    <!-- 4. Directorio de Contactos de Emergencia y Médicos -->
    <div class="page-avoid">
        <div class="section-title">4. Red de Soporte y Contactos de Emergencia</div>
        <?php if (!empty($contactos)): ?>
            <table class="table-data">
                <thead>
                    <tr>
                        <th style="width: 35%;">Nombre</th>
                        <th style="width: 25%;">Parentesco / Relación</th>
                        <th style="width: 25%;">Teléfono</th>
                        <th style="width: 15%;">Prioridad</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($contactos as $con): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($con['nombre'] ?? '') ?></strong></td>
                            <td><?= htmlspecialchars($con['parentesco'] ?? 'Familiar') ?></td>
                            <td><?= htmlspecialchars($con['telefono'] ?? '-') ?></td>
                            <td>
                                <?php if (!empty($con['es_prioritario'])): ?>
                                    <span class="badge badge-success">Prioritario</span>
                                <?php else: ?>
                                    <span style="color: #64748b;">Secundario</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="no-data">No hay contactos de auxilio o emergencia registrados.</div>
        <?php endif; ?>
    </div>

    <!-- 5. Recetas y Prescripciones Médicas -->
    <?php if (!empty($recetas)): ?>
        <div class="page-avoid">
            <div class="section-title">5. Recetas y Prescripciones Clínicas Emitidas</div>
            <table class="table-data">
                <thead>
                    <tr>
                        <th style="width: 15%;">Fecha</th>
                        <th style="width: 30%;">Médico Emisor</th>
                        <th style="width: 25%;">Diagnóstico</th>
                        <th style="width: 30%;">Indicaciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recetas as $rec): ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($rec['fecha_emision'])) ?></td>
                            <td>
                                <strong><?= htmlspecialchars($rec['doctor_nombre'] ?? ($medico_emisor ?? 'Médico Tratante')) ?></strong>
                                <?php if (!empty($rec['cedula_profesional'])): ?>
                                    <div style="font-size: 8px; color: #64748b;">Céd. <?= htmlspecialchars($rec['cedula_profesional']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($rec['diagnostico'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($rec['indicaciones_generales'] ?? '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <!-- 6. Citas y Visitas Médicas -->
    <?php if (!empty($visitas)): ?>
        <div class="page-avoid">
            <div class="section-title">6. Historial de Consultas y Citas Médicas</div>
            <table class="table-data">
                <thead>
                    <tr>
                        <th style="width: 18%;">Fecha</th>
                        <th style="width: 25%;">Médico / Especialista</th>
                        <th style="width: 27%;">Motivo de Consulta</th>
                        <th style="width: 30%;">Diagnóstico / Seguimiento</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($visitas as $vis): ?>
                        <tr>
                            <td><?= !empty($vis['fecha_visita']) ? date('d/m/Y H:i', strtotime($vis['fecha_visita'])) : '-' ?></td>
                            <td><strong><?= htmlspecialchars($vis['doctor_nombre'] ?? 'Médico Tratante') ?></strong></td>
                            <td><?= htmlspecialchars($vis['motivo_consulta'] ?? '-') ?></td>
                            <td>
                                <?= htmlspecialchars($vis['diagnostico_notas'] ?? '-') ?>
                                <?php if (!empty($vis['fecha_seguimiento'])): ?>
                                    <div style="font-size: 8px; color: #0d9488; margin-top: 2px;">
                                        Próxima cita: <?= date('d/m/Y', strtotime($vis['fecha_seguimiento'])) ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <!-- Bloque de Firma y Validación Médica -->
    <?php if (!empty($doctor) || !empty($medico_emisor)): ?>
        <div class="page-avoid" style="margin-top: 30px; text-align: center;">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="width: 30%;"></td>
                    <td style="width: 40%; border-top: 1.5px solid #334155; padding-top: 6px; text-align: center;">
                        <strong style="font-size: 11px; color: #0f172a;"><?= htmlspecialchars($medico_emisor ?? ($doctor['nombre'] ?? 'Dr(a). Médico Tratante')) ?></strong><br>
                        <span style="font-size: 9px; color: #475569;"><?= htmlspecialchars($doctor['especialidad'] ?? 'Geriatría y Medicina Integral') ?></span><br>
                        <span style="font-size: 9px; color: #64748b;">Cédula Profesional: <?= htmlspecialchars($cedula_profesional ?? ($doctor['cedula_profesional'] ?? 'Sin Cédula')) ?></span>
                    </td>
                    <td style="width: 30%;"></td>
                </tr>
            </table>
        </div>
    <?php endif; ?>

    <!-- Pie del documento -->
    <div class="footer">
        Documento generado automáticamente por VitalSenior • Sistema de Asistencia y Cuidado del Adulto Mayor.<br>
        La información contenida en este expediente es confidencial y para uso exclusivo de atención médica y cuidado geriátrico.
    </div>

</body>
</html>
