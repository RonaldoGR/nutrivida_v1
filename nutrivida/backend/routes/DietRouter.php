<?php

require_once __DIR__ . '/../controllers/DietController.php';

class DietRouter {
    private $dietController;

    public function __construct($dietController) {
        $this->dietController = $dietController;
    }

    public function handleRequest() {
        $method = $_SERVER['REQUEST_METHOD'];

        $action = $_GET['action'] ?? '';
        
        $jsonData = null;
        if ($method === 'POST') {
            $input = file_get_contents("php://input");
            $jsonData = json_decode($input, true);
            
            if (empty($action)) {
                if (isset($jsonData['action'])) $action = $jsonData['action'];
                elseif (isset($_POST['action'])) $action = $_POST['action'];
            }
        }


         switch ($method) {
            case 'GET':
                $id = $_GET['id'] ?? null;
                if($action == 'select') {
                    if($id) {
                      $this->dietController->selectDietByIdController($id);
                    } else {
                        $this->sendError(400, 'ID obrigatório');
                    }
                }
                elseif($action == 'selectFullDetails') {
                    $this->dietController->selectFullDetailsController();
                }
                elseif($action == 'selectAll' || isset($jsonData['selectAll']) || isset($_POST['selectAll'])){
                    $this->dietController->selectAllDietController();
                }
                elseif($action == 'selectByPatient' || isset($jsonData['selectByPatient']) || isset($_POST['selectByPatient'])) {
                    $this->dietController->selectDietsByPatientIdController(null);
                } 
                elseif($action == 'selectByPatientId') {
                    $id = $_GET['id'] ?? null;
                    $this->dietController->selectDietsByPatientIdParamController($id);
                }
                else {
                    $this->sendError(400, 'Rota GET não encontrada');
                }
                break;
            case 'POST':
                       
                if($action === 'register' || isset($jsonData['register']) || isset($_POST['register'])){
                    $this->dietController->registerController();
                 } 
                elseif($action === 'update' || isset($jsonData['update']) || isset($_POST['update'])){
                    $this->dietController->updateController();
                }
                elseif($action === 'delete' || isset($jsonData['delete']) || isset($_POST['delete'])){
                    $this->dietController->deleteController();
                }
                elseif($action == 'searchByStatus' || isset($jsonData['searchByStatus']) || isset($_POST['searchByStatus'])){
                    $this->dietController->searchDietByStatusController();
                } else {
                    $this->sendError(404, 'Rota POST não encontrada');
                }
                break;
            default:
                $this->sendError(405, 'Método não permitido');
                break;
        }
    }

    private function sendError($code, $message) {
        http_response_code($code);
        echo json_encode(['status' => 'error', 'message' => $message]);
        exit;
    }
}
