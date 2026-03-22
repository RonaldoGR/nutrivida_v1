<?php

require_once __DIR__ . '/../controllers/NutritionistController.php';

class NutritionistRouter {

    private $nutritionistController;

    public function __construct($nutritionistController) {
        $this->nutritionistController = $nutritionistController;
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

                if($action == 'logout') {
                    $this->nutritionistController->logOutController();
                }
                elseif($action == 'getLogged') {
                    if (isset($_SESSION['id_nutritionist'])) {
                        $this->nutritionistController->selectLoggedNutritionist($_SESSION['id_nutritionist']);
                     } else {
                        $this->sendError(401, 'Usuário não logado');
                    }
                } else {
                    $this->sendError(404, 'Rota GET não encontrada ou Action inválida');
                }

                break;

            case 'POST':
                
                if ($action === 'login' || isset($jsonData['login']) || isset($_POST['login'])) {
                    $this->nutritionistController->logInController();
                }

                elseif ($action === 'register' || isset($jsonData['register']) || isset($_POST['register'])) {
                    $this->nutritionistController->registerController();
                }

                elseif ($action === 'update' || isset($jsonData['update']) || isset($_POST['update'])) {
                    $this->nutritionistController->updateController();
                }
                elseif ($action === 'updatePassword' || isset($_POST['updatePassword'])) {
                     $this->nutritionistController->updatePasswordController();
                }
                elseif ($action === 'delete' || isset($jsonData['delete']) || isset($_POST['delete'])) {
                    $this->nutritionistController->deleteController();
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