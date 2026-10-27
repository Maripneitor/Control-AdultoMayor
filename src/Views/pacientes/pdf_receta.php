<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Receta Médica - VitalSenior</title>
    <style>
        @page {
            margin: 20mm 15mm 20mm 15mm;
            size: A4 portrait;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            color: #1e293b;
            font-size: 11px;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }
        .header {
            border-bottom: 2px solid #2563eb;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .logo-title {
            font-size: 20px;
            font-weight: bold;
            color: #1e3a8a;
            margin: 0;
        }
        .subtitle {
            font-size: 10px;
            color: #64748b;
            margin-top: 2px;
        }
        .doc-info {
            text-align: right;
            font-size: 10px;
            color: #334155;
        }
        .doc-name {
            font-size: 14px;
            font-weight: bold;
            color: #0f172a;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: bold;
            background-color: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
        }
        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #1e3a8a;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 4px;
            margin-top: 15px;
            margin-bottom: 10px;
            text-transform: uppercase;
        }
        .patient-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 10px 14px;
            margin-bottom: 15px;
        }
        .table-info {
            width: 100%;
            border-collapse: collapse;
        }
        .table-info td {
            padding: 4px 0;
            font-size: 10px;
        }
        .label {
            font-weight: bold;
            color: #475569;
            width: 15%;
        }
        .value {
            color: #0f172a;
            width: 35%;
        }
        .rx-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 15px;
        }
        .rx-table th {
            background-color: #f1f5f9;
            color: #334155;
            font-weight: bold;
            font-size: 10px;
            padding: 8px;
            border: 1px solid #cbd5e1;
            text-align: left;
        }
        .rx-table td {
            padding: 8px;
            border: 1px solid #e2e8f0;
            font-size: 10px;
            vertical-align: top;
        }
        .rx-symbol {
            font-size: 24px;
            font-weight: bold;
            color: #2563eb;
            float: left;
            margin-right: 8px;
            line-height: 1;
        }
        .instructions-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 10px;
            color: #334155;
            line-height: 1.6;
        }
        .signature-section {
            margin-top: 40px;
            text-align: center;
        }
        .signature-line {
            width: 240px;
            border-top: 1px solid #334155;
            margin: 0 auto 5px auto;
        }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 6px;
        }
    </style>
</head>
<body>

    <!-- Header Membretado -->
    <table style="width: 100%;" class="header">
        <tr>
            <td style="width: 50%; vertical-align: top;">
                <div class="logo-title">VitalSenior</div>
                <div class="subtitle">Sistema Integral de Atención y Cuidado Geriátrico</div>
                <div style="margin-top: 6px;">
                    <span class="badge">PRESCRIPCIÓN MÉDICA OFICIAL</span>
                </div>
            </td>
            <td style="width: 50%; vertical-align: top;" class="doc-info">
                <div class="doc-name"><?= htmlspecialchars($receta['doctor_nombre'] ?? 'Dr(a). Médico Tratante') ?></div>
                <div><?= htmlspecialchars($receta['especialidad'] ?? 'Medicina General / Geriatría') ?></div>
                <?php if (!empty($receta['cedula_profesional'])): ?>
                    <div>Cédula Profesional: <strong><?= htmlspecialchars($receta['cedula_profesional']) ?></strong></div>
                <?php endif; ?>
                <?php if (!empty($receta['hospital_clinica'])): ?>
                    <div><?= htmlspecialchars($receta['hospital_clinica']) ?></div>
                <?php endif; ?>
                <?php if (!empty($receta['telefono_consultorio'])): ?>
                    <div>Tel: <?= htmlspecialchars($receta['telefono_consultorio']) ?></div>
                <?php endif; ?>
            </td>
        </tr>
    </table>

    <!-- Ficha del Paciente -->
    <div class="patient-box">
        <table class="table-info">
            <tr>
                <td class="label">Paciente:</td>
                <td class="value"><strong><?= htmlspecialchars(($receta['paciente_nombre'] ?? '') . ' ' . ($receta['paciente_apellidos'] ?? '')) ?></strong></td>
                <td class="label">Fecha Emisión:</td>
                <td class="value"><strong><?= date('d/m/Y', strtotime($receta['fecha_emision'] ?? 'now')) ?></strong></td>
            </tr>
            <tr>
                <td class="label">Tipo Sangre:</td>
                <td class="value"><?= htmlspecialchars($receta['tipo_sangre'] ?? 'N/D') ?></td>
                <td class="label">Folio Receta:</td>
                <td class="value font-mono">#REC-<?= str_pad((string)($receta['id'] ?? 1), 5, '0', STR_PAD_LEFT) ?></td>
            </tr>
            <tr>
                <td class="label">Alergias:</td>
                <td class="value" colspan="3" style="color: #b91c1c; font-weight: bold;"><?= htmlspecialchars($receta['alergias'] ?? 'Ninguna conocida') ?></td>
            </tr>
        </table>
    </div>

    <!-- Diagnóstico Clínico -->
    <div class="section-title">Diagnóstico Clínico</div>
    <div class="instructions-box" style="margin-bottom: 15px;">
        <strong>Diagnóstico:</strong> <?= nl2br(htmlspecialchars($receta['diagnostico'] ?? 'Sin diagnóstico especificado')) ?>
    </div>

    <!-- Prescripción Farmacológica (Rx) -->
    <div class="section-title">
        <span class="rx-symbol">℞</span> Prescripción Farmacológica y Dosificación
    </div>

    <?php if (!empty($medicamentosRecetados)): ?>
        <table class="rx-table">
            <thead>
                <tr>
                    <th style="width: 35%;">Medicamento / Fármaco</th>
                    <th style="width: 20%;">Dosis</th>
                    <th style="width: 20%;">Frecuencia</th>
                    <th style="width: 25%;">Instrucciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($medicamentosRecetados as $med): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($med['nombre_comercial']) ?></strong></td>
                        <td><?= htmlspecialchars($med['dosis']) ?></td>
                        <td>Cada <?= htmlspecialchars((string)$med['frecuencia_horas']) ?> horas</td>
                        <td><?= htmlspecialchars($med['instrucciones'] ?? 'Según horario') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <!-- Indicaciones Generales -->
    <div class="section-title">Indicaciones Generales y Cuidados</div>
    <div class="instructions-box">
        <?= nl2br(htmlspecialchars($receta['indicaciones_generales'] ?? 'Seguir las indicaciones dadas en consulta médica.')) ?>
    </div>

    <!-- Firma y Sello Médico -->
    <div class="signature-section">
        <div class="signature-line"></div>
        <div style="font-weight: bold; font-size: 11px;"><?= htmlspecialchars($receta['doctor_nombre'] ?? 'Dr(a). Médico Tratante') ?></div>
        <div style="font-size: 9px; color: #64748b;">
            <?= htmlspecialchars($receta['especialidad'] ?? 'Médico Especialista') ?>
            <?php if (!empty($receta['cedula_profesional'])): ?>
                • Céd. Prof. <?= htmlspecialchars($receta['cedula_profesional']) ?>
            <?php endif; ?>
        </div>
        <div style="font-size: 8px; color: #94a3b8; margin-top: 4px;">Firma Electrónica Médica Certificada por VitalSenior</div>
    </div>

    <!-- Footer -->
    <div class="footer">
        VitalSenior • Documento médico oficial emitido electrónicamente el <?= date('d/m/Y H:i') ?> • Conservar para control de enfermería y familiar
    </div>

</body>
</html>
