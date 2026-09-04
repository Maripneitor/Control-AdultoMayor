<?php
declare(strict_types=1);

namespace App\Services;

class SaludEvaluatorService {
    
    public const STATUS_NORMAL = 'VERDE';
    public const STATUS_WARNING = 'AMARILLO';
    public const STATUS_CRITICAL = 'ROJO';
    public const STATUS_NEEDS_CONFIRMATION = 'NEEDS_CONFIRMATION';

    public function evaluar(array $datos, bool $confirmado): array {
        $resultado = [
            'estado' => self::STATUS_NORMAL,
            'requiere_confirmacion' => false,
            'mensaje_confirmacion' => '',
            'alertas' => []
        ];

        // Evaluar Presión Arterial
        if (isset($datos['presion_sistolica']) && isset($datos['presion_diastolica'])) {
            $sis = (int)$datos['presion_sistolica'];
            $dia = (int)$datos['presion_diastolica'];

            // Valores de plausibilidad extrema
            if (($sis > 220 || $sis < 60 || $dia > 130 || $dia < 40) && !$confirmado) {
                $resultado['requiere_confirmacion'] = true;
                $resultado['mensaje_confirmacion'] = "Escribiste una presión de {$sis}/{$dia}. Parece muy fuera de lo normal, ¿estás seguro?";
                return $resultado;
            }

            // Semáforo clínico
            if ($sis >= 180 || $dia >= 120 || $sis <= 80 || $dia <= 50) {
                $resultado['estado'] = self::STATUS_CRITICAL;
                $resultado['alertas'][] = "Presión Arterial en nivel crítico ({$sis}/{$dia}). Contacte a su médico de inmediato.";
            } elseif ($sis > 130 || $dia > 85 || $sis < 90 || $dia < 60) {
                if ($resultado['estado'] !== self::STATUS_CRITICAL) {
                    $resultado['estado'] = self::STATUS_WARNING;
                }
                $resultado['alertas'][] = "Presión Arterial fuera del rango normal (90-130 / 60-85).";
            }
        }

        // Evaluar Glucosa
        if (isset($datos['glucosa'])) {
            $gluc = (float)$datos['glucosa'];
            $momento = $datos['momento_glucosa'] ?? 'ayunas'; // 'ayunas', 'antes_comer', 'despues_comer'

            // Valores de plausibilidad extrema
            if (($gluc > 400 || $gluc < 40) && !$confirmado) {
                $resultado['requiere_confirmacion'] = true;
                $resultado['mensaje_confirmacion'] = "Escribiste una glucosa de {$gluc} mg/dL. Parece muy extrema, ¿estás seguro?";
                return $resultado;
            }

            // Semáforo clínico
            if ($gluc < 70 || $gluc > 250) {
                $resultado['estado'] = self::STATUS_CRITICAL;
                $resultado['alertas'][] = "Nivel de Glucosa crítico ({$gluc} mg/dL).";
            } else {
                if ($momento === 'ayunas' && ($gluc < 70 || $gluc > 130)) {
                    if ($resultado['estado'] !== self::STATUS_CRITICAL) $resultado['estado'] = self::STATUS_WARNING;
                    $resultado['alertas'][] = "Glucosa en ayunas fuera de rango (70-130).";
                } elseif ($momento !== 'ayunas' && $gluc > 180) {
                    if ($resultado['estado'] !== self::STATUS_CRITICAL) $resultado['estado'] = self::STATUS_WARNING;
                    $resultado['alertas'][] = "Glucosa postprandial elevada (mayor a 180).";
                }
            }
        }

        // Evaluar Ritmo Cardíaco
        if (isset($datos['ritmo_cardiaco'])) {
            $ritmo = (int)$datos['ritmo_cardiaco'];
            if (($ritmo > 200 || $ritmo < 30) && !$confirmado) {
                $resultado['requiere_confirmacion'] = true;
                $resultado['mensaje_confirmacion'] = "Escribiste {$ritmo} lpm de ritmo cardíaco. Parece irreal, ¿estás seguro?";
                return $resultado;
            }

            if ($ritmo > 120 || $ritmo < 50) {
                $resultado['estado'] = self::STATUS_CRITICAL;
                $resultado['alertas'][] = "Ritmo cardíaco crítico ({$ritmo} lpm).";
            } elseif ($ritmo > 100 || $ritmo < 55) {
                if ($resultado['estado'] !== self::STATUS_CRITICAL) $resultado['estado'] = self::STATUS_WARNING;
                $resultado['alertas'][] = "Ritmo cardíaco fuera de rango (55-100 lpm).";
            }
        }

        return $resultado;
    }
}
