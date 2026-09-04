<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Medicamento;
use App\Models\Paciente;
use App\Core\Controller;

class MedicamentoController extends Controller {
    private Medicamento $medicamentoModel;
    private Paciente $pacienteModel;

    public function __construct() {
        $this->medicamentoModel = new Medicamento();
        $this->pacienteModel = new Paciente();
    }

    public function index(): void {
        $userId = (int)$_SESSION['user_id'];
        $paciente = $this->pacienteModel->findByUserId($userId);
        
        $medicamentos = $this->medicamentoModel->getAllByPacienteId((int)$paciente['id']);
        $this->render('medicamentos/index', [
            'paciente' => $paciente,
            'medicamentos' => $medicamentos
        ], false);
    }

    public function controlTomas(): void {
        $userId = (int)$_SESSION['user_id'];
        $paciente = $this->pacienteModel->find((int)($_SESSION['paciente_activo_id'] ?? 0));
        if (!$paciente) {
            $paciente = $this->pacienteModel->findByUserId($userId);
        }

        $this->medicamentoModel->generarTomasDiarias((int)$paciente['id']);
        $tomasDelDia = $this->medicamentoModel->getTodayDoses((int)$paciente['id']);

        require_once __DIR__ . '/../Views/medicamentos/control_tomas.php';
    }

    public function store(): void {
        if (!validateCsrfToken($_POST['csrf_token'] ?? null)) {
            die('Error CSRF.');
        }

        $userId = (int)$_SESSION['user_id'];
        $paciente = $this->pacienteModel->findByUserId($userId);

        $nombre = trim($_POST['nombre_comercial'] ?? '');
        $dosis = trim($_POST['dosis'] ?? '');
        $frecuenciaHoras = (int)($_POST['frecuencia_horas'] ?? 8);
        $instrucciones = trim($_POST['instrucciones'] ?? '');

        if (!empty($nombre) && !empty($dosis) && $frecuenciaHoras > 0) {
            $this->medicamentoModel->agregar([
                'paciente_id' => (int)$paciente['id'],
                'nombre_comercial' => $nombre,
                'dosis' => $dosis,
                'frecuencia_horas' => $frecuenciaHoras,
                'instrucciones' => $instrucciones
            ]);
            $this->setFlash('success', 'Medicamento registrado exitosamente.');
        } else {
            $this->setFlash('danger', 'Por favor completa todos los campos correctamente.');
        }

        header('Location: ' . APP_URL . '/medicamentos');
        exit;
    }

    public function registrarToma(): void {
        if (!validateCsrfToken($_POST['csrf_token'] ?? null)) {
            die('Error CSRF.');
        }

        $userId = (int)$_SESSION['user_id'];
        $paciente = $this->pacienteModel->findByUserId($userId);
        
        $registroId = (int)($_POST['registro_id'] ?? 0);
        
        if ($registroId > 0 && $paciente) {
            try {
                $db = \Config\Database::getInstance()->getConnection();
                $db->beginTransaction();

                // 1. Verificar si ya está tomado
                // En la DB de la fase 6 se creó tomas_historial pero el index.php de la fase 5 
                // generaba "registro_tomas". Asumiremos que el botón de dashboard manda el ID de registro_tomas (o tomas_historial)
                // Usaremos registro_tomas ya que es lo que DashboardController inyecta con getTodayDoses.
                // Adaptaremos la consulta:
                $stmt = $db->prepare("SELECT estado, medicamento_id FROM registro_tomas WHERE id = :id FOR UPDATE");
                $stmt->execute(['id' => $registroId]);
                $toma = $stmt->fetch(\PDO::FETCH_ASSOC);

                if ($toma && $toma['estado'] !== 'tomada') {
                    // 2. Marcar como tomado
                    $stmtUpdate = $db->prepare("UPDATE registro_tomas SET estado = 'tomada', fecha_hora_tomada = NOW() WHERE id = :id");
                    $stmtUpdate->execute(['id' => $registroId]);

                    // 3. Descontar inventario de medicamentos
                    $medId = (int)$toma['medicamento_id'];
                    $stmtMed = $db->prepare("SELECT stock_actual, dosis_por_toma, stock_minimo_alerta FROM medicamentos WHERE id = :mid FOR UPDATE");
                    $stmtMed->execute(['mid' => $medId]);
                    $medicamento = $stmtMed->fetch(\PDO::FETCH_ASSOC);

                    if ($medicamento) {
                        $nuevoStock = max(0, $medicamento['stock_actual'] - $medicamento['dosis_por_toma']);
                        $stmtUpdateMed = $db->prepare("UPDATE medicamentos SET stock_actual = :stock WHERE id = :mid");
                        $stmtUpdateMed->execute(['stock' => $nuevoStock, 'mid' => $medId]);

                        if ($nuevoStock <= $medicamento['stock_minimo_alerta']) {
                            // Alerta de inventario
                            $_SESSION['flash_inventory_alert'] = "Te quedan dosis limitadas para este medicamento. Recuerda surtir tu receta.";
                        }
                    }

                    $db->commit();
                    $this->setFlash('success', 'Toma registrada correctamente.');
                } else {
                    $db->rollBack();
                    $this->setFlash('warning', 'La toma ya estaba registrada.');
                }

            } catch (\Exception $e) {
                if (isset($db) && $db->inTransaction()) {
                    $db->rollBack();
                }
                $this->setFlash('danger', 'Error al registrar la toma: ' . $e->getMessage());
            }
        }

        header('Location: ' . APP_URL . '/dashboard');
        exit;
    }
}
