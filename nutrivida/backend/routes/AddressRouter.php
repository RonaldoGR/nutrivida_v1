<?php

require_once __DIR__ . '/../controllers/AddressController.php';

class AddressRouter {
    private $addressController;

    public function __construct($addressController) {
        $this->addressController = $addressController;
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
                if ($action === 'listAll') {
                    $this->addressController->listAllController();
                }
                else {
                    $this->sendError(404, 'Rota GET não encontrada');
                }
                break;

            case 'POST':
                
                if ($action === 'register' || isset($_POST['registerAddress']) || isset($jsonData['registerAddress'])) {
                    $this->addressController->registerController();
                } 
                elseif ($action === 'update' || isset($_POST['updateAddress']) || isset($jsonData['updateAddress'])) {
                    $this->addressController->updateController();
                }
                elseif ($action === 'delete' || isset($_POST['deleteAddress']) || isset($jsonData['deleteAddress'])) {
                    $this->addressController->deleteController();
                }
                elseif ($action === 'listByPatient' || isset($_POST['searchByPatient']) || isset($jsonData['searchByPatient'])) {
                    $this->addressController->listByPatientController();
                }
                else {
                    $this->sendError(404, 'Rota POST não encontrada');
                }
                break;

            default:
                $this->sendError(405, 'Método não permitido');
        }
    }

    private function sendError($code, $message) {
        http_response_code($code);
        echo json_encode(['status' => 'error', 'message' => $message]);
        exit;
    }
}