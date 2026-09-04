<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Expediente - <?= htmlspecialchars($paciente['nombre']) ?></title>
    <style>
        body { font-family: sans-serif; color: #333; line-height: 1.5; }
        h1, h2 { color: #006A60; border-bottom: 2px solid #006A60; padding-bottom: 5px; }
        .section { margin-bottom: 20px; }
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .info-table th, .info-table td { padding: 8px; border: 1px solid #ddd; text-align: left; }
        .info-table th { background-color: #f4f4f4; width: 30%; }
        .alert { background-color: #ffebee; color: #c62828; padding: 10px; border-left: 5px solid #c62828; }
    </style>
</head>
<body>
    <div style="text-align: center; margin-bottom: 30px;">
        <h1>Expediente Clínico: <?= htmlspecialchars($paciente['nombre'] . ' ' . $paciente['apellidos']) ?></h1>
        <p>Generado el: <?= date('d/m/Y H:i') ?></p>
    </div>

    <div class="section">
        <h2>Datos Vitales</h2>
        <table class="info-table">
            <tr>
                <th>Tipo de Sangre</th>
                <td><?= htmlspecialchars($paciente['tipo_sangre'] ?: 'N/D') ?></td>
            </tr>
            <tr>
                <th>Fecha de Nacimiento</th>
                <td><?= htmlspecialchars($paciente['fecha_nacimiento']) ?></td>
            </tr>
            <tr>
                <th>Alergias</th>
                <td style="color: #c62828; font-weight: bold;"><?= htmlspecialchars($paciente['alergias'] ?: 'Ninguna registrada') ?></td>
            </tr>
            <tr>
                <th>Enfermedades Crónicas</th>
                <td><?= htmlspecialchars($paciente['enfermedades_cronicas'] ?: 'Ninguna registrada') ?></td>
            </tr>
        </table>
    </div>

    <!-- Novedad Fase 6: Promedios y Gráfica -->
    <div class="section">
        <h2>Resumen Clínico (Últimos Registros)</h2>
        <table class="info-table" style="margin-bottom: 20px;">
            <tr>
                <th style="text-align: center;">Presión Promedio</th>
                <th style="text-align: center;">Glucosa Promedio</th>
            </tr>
            <tr>
                <td style="text-align: center; font-size: 1.2em;"><strong><?= round($avgSis) ?> / <?= round($avgDia) ?> mmHg</strong></td>
                <td style="text-align: center; font-size: 1.2em;"><strong><?= round($avgGluc, 1) ?> mg/dL</strong></td>
            </tr>
        </table>
        
        <div style="text-align: center;">
            <img src="<?= htmlspecialchars($chartUrl) ?>" alt="Gráfica de signos vitales" style="max-width: 100%; height: auto; border: 1px solid #ccc;">
        </div>
    </div>

    <div class="section">
        <h2>Medicamentos Registrados</h2>
        <?php if(empty($medicamentos)): ?>
            <p>No hay medicamentos registrados.</p>
        <?php else: ?>
            <table class="info-table">
                <thead>
                    <tr>
                        <th>Medicamento</th>
                        <th>Dosis</th>
                        <th>Frecuencia</th>
                        <th>Indicaciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($medicamentos as $med): ?>
                        <tr>
                            <td><?= htmlspecialchars($med['nombre_comercial'] . ' (' . $med['nombre_generico'] . ')') ?></td>
                            <td><?= htmlspecialchars($med['dosis']) ?></td>
                            <td>Cada <?= htmlspecialchars((string)($med['frecuencia_horas'] ?? '24')) ?> hrs</td>
                            <td><?= htmlspecialchars((string)($med['instrucciones'] ?? '')) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <div class="section">
        <h2>Contactos de Emergencia</h2>
        <?php if(empty($contactos)): ?>
            <p>No hay contactos registrados.</p>
        <?php else: ?>
            <table class="info-table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Parentesco</th>
                        <th>Teléfono</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($contactos as $contacto): ?>
                        <tr>
                            <td><?= htmlspecialchars($contacto['nombre']) ?></td>
                            <td><?= htmlspecialchars($contacto['parentesco']) ?></td>
                            <td><?= htmlspecialchars($contacto['telefono']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
