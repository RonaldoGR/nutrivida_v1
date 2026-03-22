<?php

require_once __DIR__ . '/../controllers/MealController.php';

class MealRouter {
    private $mealController;

    public function __construct($mealController) {
        $this->mealController = $mealController;
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
                switch ($action) {
                    case 'listAll':

                        if(method_exists($this->mealController, 'listAllController')) {
                            $this->mealController->listAllController();
                        } else {
                            $this->sendError(404, 'Método listAll não encontrado no Controller');
                        }
                        break;
                    
                    case 'selectById':
                        // Ex: api/meal.php?action=selectById&id=1
                        $id = $_GET['id'] ?? null;
                        if ($id) {
                           $this->mealController->selectMealByIdController($id);
                        }else {
                            $this->sendError(400, 'ID obrigatório');
                        }
                        break;
                     case 'selectByDiet':
                        $dietId = $_GET['dietId'] ?? null;
                        if($dietId) {
                            $this->mealController->selectMealByDietIdController($dietId);
                        } else {
                            $this->sendError(400, 'ID da diete é obrigatório');
                        }
                        break;   
                    default:
                        $this->sendError(404, 'Rota GET não encontrada');
                }
                break;

            case 'POST':
                if ($action === 'register' || isset($_POST['register']) || isset($jsonData['register'])) {
                    $this->mealController->registerController();
                } 
                elseif ($action === 'update' || isset($_POST['update']) || isset($jsonData['update'])) {
                    $this->mealController->updateController();
                }
                elseif ($action === 'delete' || isset($_POST['delete']) || isset($jsonData['delete'])) {
                    $this->mealController->deleteController();
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