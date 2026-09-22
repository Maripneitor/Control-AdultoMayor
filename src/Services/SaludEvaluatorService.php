<?php
declare(strict_types=1);

namespace App\Services;

class SaludEvaluatorService {
    
    public const STATUS_NORMAL = 'VERDE';
    public const STATUS_WARNING = 'AMARILLO';
    public const STATUS_CRITICAL = 'ROJO';

    /**
     * Evalúa signos vitales, glucosa, frecuencia cardíaca y calcula IMC
     *
     * @param array $datos
     * @param bool $confirmado
     * @return array
     */
    public function evaluar(array $datos, bool $confirmado = false): array {
        $resultado = [
            'estado' => self::STATUS_NORMAL,
            'requiere_confirmacion' => false,
            'mensaje_confirmacion' => '',
            'alertas' => [],
            'imc' => null,
            'categoria_imc' => null,
            'altura_m' => null,
            'peso' => null
        ];

        // 1. Evaluar Presión Arterial
        if (isset($datos['presion_sistolica']) && isset($datos['presion_diastolica'])) {
            $sis = (int)$datos['presion_sistolica'];
            $dia = (int)$datos['presion_diastolica'];

            if ($sis > 0 && $dia > 0) {
                // Plausibilidad extrema
                if (($sis > 230 || $sis < 55 || $dia > 135 || $dia < 35) && !$confirmado) {
                    $resultado['requiere_confirmacion'] = true;
                    $resultado['mensaje_confirmacion'] = "Escribiste una presión de {$sis}/{$dia} mmHg. Parece un valor inusual, ¿deseas confirmarlo?";
                    return $resultado;
                }

                // Semáforo clínico
                if ($sis >= 180 || $dia >= 120) {
                    $resultado['estado'] = self::STATUS_CRITICAL;
                    $resultado['alertas'][] = "🚨 Crisis Hipertensiva ({$sis}/{$dia} mmHg). Contacte a su médico de inmediato.";
                } elseif ($sis <= 80 || $dia <= 50) {
                    $resultado['estado'] = self::STATUS_CRITICAL;
                    $resultado['alertas'][] = "🚨 Hipotensión Severa ({$sis}/{$dia} mmHg). Mantenga al paciente en reposo y solicite asistencia.";
                } elseif ($sis > 130 || $dia > 85) {
                    if ($resultado['estado'] !== self::STATUS_CRITICAL) {
                        $resultado['estado'] = self::STATUS_WARNING;
                    }
                    $resultado['alertas'][] = "⚠️ Presión arterial elevada ({$sis}/{$dia} mmHg). Rango sugerido: 90-130 / 60-85 mmHg.";
                } elseif ($sis < 90 || $dia < 60) {
                    if ($resultado['estado'] !== self::STATUS_CRITICAL) {
                        $resultado['estado'] = self::STATUS_WARNING;
                    }
                    $resultado['alertas'][] = "⚠️ Presión arterial baja ({$sis}/{$dia} mmHg). Rango sugerido: 90-130 / 60-85 mmHg.";
                }
            }
        }

        // 2. Evaluar Glucosa
        if (isset($datos['glucosa']) && !empty($datos['glucosa'])) {
            $gluc = (float)$datos['glucosa'];
            $momento = $datos['momento_glucosa'] ?? 'ayunas';

            if ($gluc > 0) {
                // Plausibilidad extrema
                if (($gluc > 450 || $gluc < 35) && !$confirmado) {
                    $resultado['requiere_confirmacion'] = true;
                    $resultado['mensaje_confirmacion'] = "Escribiste un nivel de glucosa de {$gluc} mg/dL. Es un valor muy extremo, ¿estás seguro?";
                    return $resultado;
                }

                // Semáforo clínico
                if ($gluc < 70) {
                    $resultado['estado'] = self::STATUS_CRITICAL;
                    $resultado['alertas'][] = "🚨 Hipoglucemia ({$gluc} mg/dL). Nivel peligrosamente bajo de azúcar en sangre.";
                } elseif ($gluc > 250) {
                    $resultado['estado'] = self::STATUS_CRITICAL;
                    $resultado['alertas'][] = "🚨 Hiperglucemia Severa ({$gluc} mg/dL). Nivel de azúcar muy elevado.";
                } elseif ($momento === 'ayunas' && $gluc > 130) {
                    if ($resultado['estado'] !== self::STATUS_CRITICAL) {
                        $resultado['estado'] = self::STATUS_WARNING;
                    }
                    $resultado['alertas'][] = "⚠️ Glucosa en ayunas elevada ({$gluc} mg/dL). Rango sugerido: 70-130 mg/dL.";
                } elseif ($momento !== 'ayunas' && $gluc > 180) {
                    if ($resultado['estado'] !== self::STATUS_CRITICAL) {
                        $resultado['estado'] = self::STATUS_WARNING;
                    }
                    $resultado['alertas'][] = "⚠️ Glucosa postprandial elevada ({$gluc} mg/dL). Rango sugerido: menor a 180 mg/dL.";
                }
            }
        }

        // 3. Evaluar Ritmo / Frecuencia Cardíaca
        if (isset($datos['ritmo_cardiaco']) && !empty($datos['ritmo_cardiaco'])) {
            $ritmo = (int)$datos['ritmo_cardiaco'];
            if ($ritmo > 0) {
                // Plausibilidad extrema
                if (($ritmo > 210 || $ritmo < 35) && !$confirmado) {
                    $resultado['requiere_confirmacion'] = true;
                    $resultado['mensaje_confirmacion'] = "Escribiste {$ritmo} lpm de frecuencia cardíaca. ¿Deseas confirmar este valor?";
                    return $resultado;
                }

                if ($ritmo > 120) {
                    $resultado['estado'] = self::STATUS_CRITICAL;
                    $resultado['alertas'][] = "🚨 Taquicardia severa ({$ritmo} lpm).";
                } elseif ($ritmo < 50) {
                    $resultado['estado'] = self::STATUS_CRITICAL;
                    $resultado['alertas'][] = "🚨 Bradicardia severa ({$ritmo} lpm).";
                } elseif ($ritmo > 100) {
                    if ($resultado['estado'] !== self::STATUS_CRITICAL) {
                        $resultado['estado'] = self::STATUS_WARNING;
                    }
                    $resultado['alertas'][] = "⚠️ Pulso acelerado / Taquicardia leve ({$ritmo} lpm). Rango normal: 55-100 lpm.";
                } elseif ($ritmo < 55) {
                    if ($resultado['estado'] !== self::STATUS_CRITICAL) {
                        $resultado['estado'] = self::STATUS_WARNING;
                    }
                    $resultado['alertas'][] = "⚠️ Pulso bajo / Bradicardia leve ({$ritmo} lpm). Rango normal: 55-100 lpm.";
                }
            }
        }

        // 4. Calcular IMC (Peso y Altura)
        if (!empty($datos['peso']) && !empty($datos['altura'])) {
            $peso = (float)$datos['peso'];
            $alturaRaw = (float)$datos['altura'];

            // Normalizar altura a metros si viene en centímetros (ej: 165 -> 1.65, 90 -> 0.90)
            $alturaM = $alturaRaw >= 30.0 ? ($alturaRaw / 100.0) : $alturaRaw;

            if ($peso > 0 && $alturaM > 0.4) {
                $resultado['peso'] = round($peso, 2);
                $resultado['altura_m'] = round($alturaM, 2);

                // Plausibilidad extrema / Límites anatómicos (0.90m a 2.30m / 90cm a 230cm)
                if (($peso > 280 || $peso < 20 || $alturaM > 2.30 || $alturaM < 0.90) && !$confirmado) {
                    $resultado['requiere_confirmacion'] = true;
                    $resultado['mensaje_confirmacion'] = "Peso: {$peso} kg y Altura: {$alturaM} m están fuera del rango anatómico típico (0.90m - 2.30m). ¿Deseas confirmar?";
                    return $resultado;
                }

                $imc = round($peso / ($alturaM * $alturaM), 1);
                $resultado['imc'] = $imc;

                if ($imc < 18.5) {
                    $resultado['categoria_imc'] = 'Bajo peso';
                    if ($resultado['estado'] !== self::STATUS_CRITICAL) {
                        $resultado['estado'] = self::STATUS_WARNING;
                    }
                    $resultado['alertas'][] = "⚠️ IMC de {$imc} (Bajo peso / Riesgo de desnutrición en adulto mayor).";
                } elseif ($imc <= 24.9) {
                    $resultado['categoria_imc'] = 'Peso normal';
                } elseif ($imc <= 29.9) {
                    $resultado['categoria_imc'] = 'Sobrepeso';
                } elseif ($imc <= 34.9) {
                    $resultado['categoria_imc'] = 'Obesidad Grado I';
                    if ($resultado['estado'] !== self::STATUS_CRITICAL) {
                        $resultado['estado'] = self::STATUS_WARNING;
                    }
                    $resultado['alertas'][] = "⚠️ IMC de {$imc} (Obesidad Grado I).";
                } else {
                    $resultado['categoria_imc'] = 'Obesidad Severa';
                    if ($resultado['estado'] !== self::STATUS_CRITICAL) {
                        $resultado['estado'] = self::STATUS_WARNING;
                    }
                    $resultado['alertas'][] = "⚠️ IMC de {$imc} (Obesidad Severa / Grado II-III).";
                }
            }
        }

        return $resultado;
    }
}
