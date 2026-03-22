<?php
// routes/AlimentRouter.php

require_once __DIR__ . '/../controllers/AlimentController.php';

class AlimentRouter {
    private $alimentController;

    public function __construct($alimentController) {
        $this->alimentController = $alimentController;
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
                //  (ex: ?action=search&name=Arroz)
                if ($action === 'listAll') {
                    $this->alimentController->listAllController();
                } elseif ($action === 'select') {
                    $this->alimentController->selectAlimentController();
                }
                else {
                    $this->sendError(404, 'Rota GET não encontrada');
                }
                break;

            case 'POST':
                if ($action === 'register' || isset($_POST['registerAliment']) || isset($jsonData['registerAliment'])) {
                    $this->alimentController->registerController();
                } 
                elseif ($action === 'update' || isset($_POST['updateAliment']) || isset($jsonData['updateAliment'])) {
                    $this->alimentController->updateController();
                }
                elseif ($action === 'delete' || isset($_POST['deleteAliment']) || isset($jsonData['deleteAliment'])) {
                    $this->alimentController->deleteController();
                }
                elseif ($action === 'search' || isset($_POST['search']) || isset($jsonData['search'])) {
                    $this->alimentController->searchController();
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