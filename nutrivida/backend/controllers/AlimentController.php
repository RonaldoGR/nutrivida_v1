<?php

require_once __DIR__ . '/../config/Connection.php';
require_once __DIR__ . '/../repositories/AlimentRepository.php';
require_once __DIR__ . '/../services/AlimentService.php';

class AlimentController {
    private $connection;
    private $alimentRepository;
    private $alimentService;


    public function __construct() {
        if(session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if(!isset($_SESSION['authenticate']) || $_SESSION['authenticate'] !== true) {
            header('Content-type: application/json');
            http_response_code(401);
            echo json_encode([
                'status' => 'error',
                'message' => 'Acesso não autorizado.'
            ]);
            exit;
        }

        $this->connection = Connection::getConnection();
        $this->alimentRepository = new AlimentRepository($this->connection);
        $this->alimentService = new AlimentService($this->alimentRepository);
    }

    public function registerController() {
        header('Content-Type: application/json');

        if (isset($_POST['registerAliment'])) {
            try {
                //  falta implementar verificação se $_SESSION['role'] == 'nutricionista'
                
                $description = $_POST['description'];
                $quantity    = $_POST['quantity']; 
                $calories    = $_POST['calories'];

                $this->alimentService->registerService($description, $quantity, $calories);

                echo json_encode([
                    'status' => 'success',
                    'message' => 'Alimento cadastrado com sucesso!'
                ]);

            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            }
            exit;
        }
    }

    public function updateController() {
        header('Content-Type: application/json');

        if (isset($_POST['updateAliment'])) {
            try {
                $id          = $_POST['id'];
                $description = $_POST['description'];
                $quantity    = $_POST['quantity'];
                $calories    = $_POST['calories'];

                $this->alimentService->updateService($id, $description, $quantity, $calories);

                echo json_encode([
                    'status' => 'success',
                    'message' => 'Alimento atualizado com sucesso!'
                ]);

            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            }
            exit;
        }
    }

    public function listAllController() {
        header('Content-Type: application/json');
        try {
            $list = $this->alimentService->getAllAlimentsService();
            echo json_encode([
                'status' => 'success',
                'data' => $list
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit;
    }
    
    public function selectAlimentController() {
        header('Content-Type: application/json');

        $id = $_GET['id'] ?? "";

        try {
            $alimentSelected = $this->alimentService->selectAlimentService($id);
            echo json_encode([
                'status' => 'success',
                'data' => $alimentSelected
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }




    public function searchController() {
        header('Content-Type: application/json');
        if (isset($_POST['search'])) {
            try {
                $name = $_POST['name']; 
                $list = $this->alimentService->searchAlimentByNameService($name);
                
                echo json_encode([
                    'status' => 'success',
                    'data' => $list
                ]);
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            }
            exit;
        }
    }

    public function deleteController() {
        header('Content-Type: application/json');
        if (isset($_POST['deleteAliment'])) {
            try {
                $id = $_POST['id'];
                $this->alimentService->deleteService($id);

                echo json_encode([
                    'status' => 'success',
                    'message' => 'Alimento excluído com sucesso!'
                ]);
            } catch (Exception $e) {
                //  1451 é o padrão do MySQL para Foreign Key Constraint
                if (strpos($e->getMessage(), '1451') !== false) {
                    http_response_code(409); 
                    echo json_encode(['status' => 'error', 'message' => 'Não é possível excluir este alimento pois ele faz parte de uma ou mais refeições.']);
                } else {
                    http_response_code(500);
                    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
                }
            }
            exit;
        }
    }
}