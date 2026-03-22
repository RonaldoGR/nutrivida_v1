<?php

require_once __DIR__ . '/../controllers/PatientController.php';

class PatientRouter {

    private $patientController;

    public function __construct($patientController) {
        $this->patientController = $patientController;
    }

    public function handleRequest() {
        
        $method = $_SERVER['REQUEST_METHOD'];

        $action = $_GET['action'] ?? '';
        
        $jsonData = null;
        if ($method === 'POST') {
            $input = file_get_contents("php://input");
            $jsonData = json_decode($input, true);
            
            //  tenta achar no JSON ou POST
            if (empty($action)) {
                if (isset($jsonData['action'])) $action = $jsonData['action'];
                elseif (isset($_POST['action'])) $action = $_POST['action'];
            }
        }

        
        switch ($method) {
            case 'GET':

                if($action == 'logout') {
                    $this->patientController->logOutController();
                }
                elseif($action == 'getLogged') {
                    if (isset($_SESSION['id'])) {
                        $this->patientController->selectLoggedPatient($_SESSION['id']);
                     } else {
                        $this->sendError(401, 'Usuário não logado');
                    }
                }
                elseif ($action == 'select'){
                    $id = $_GET['id'] ?? null;
                    if($id) {
                        $this->patientController->selectLoggedPatient($id);
                    } else {
                        $this->sendError(400, 'ID obrigatório');
                    }
                    
                } elseif($action == 'selectAll'){
                    $this->patientController->selectAllPatientController();
                } 
                else {
                    $this->sendError(404, 'Rota GET não encontrada ou Action inválida');
                }

                break;

            case 'POST':
                // pode vir por action='login' ou ter campo 'login' no body)
                if ($action === 'login' || isset($jsonData['login']) || isset($_POST['login'])) {
                    $this->patientController->logInController();
                }

                elseif ($action === 'register' || isset($jsonData['register']) || isset($_POST['register'])) {
                    $this->patientController->registerController();
                }

                elseif ($action === 'update' || $action === 'updateProfile' || isset($_POST['update']) || isset($_POST['updateProfile'])){
                    $this->patientController->updateController();
                }
                elseif ($action === 'updatePassword' || isset($_POST['updatePassword'])) {
                      $this->patientController->updatePasswordController();
                }
                elseif ($action === 'saveAddress' || isset($_POST['saveAddress'])) {
                    $this->patientController->saveAddressController();
                }
                elseif ($action === 'deleteAddress' || isset($_POST['deleteAddress'])) {
                    $this->patientController->deleteAddressController();
                }
                elseif ($action === 'delete' || isset($jsonData['delete']) || isset($_POST['delete'])) {
                    $this->patientController->deleteController();
                }
                elseif($action === 'searchByName' || isset($jsonData['searchByName']) || isset($_POST['searchByName'])){
                    $name = $jsonData['name'] ?? $_POST['name'] ?? '';
                    $this->patientController->selectPatientByNameController($name);
                }
                else {
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