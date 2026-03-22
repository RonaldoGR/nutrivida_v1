<?php

require_once __DIR__ . '/../config/Connection.php';
require_once __DIR__ . '/../repositories/MealRepository.php';
require_once __DIR__ . '/../repositories/MealAlimentRepository.php'; 
require_once __DIR__ . '/../repositories/DietRepository.php'; 
require_once __DIR__ . '/../services/MealService.php';

class MealController {
    
    private $connection;
    private $mealService;
    private $mealRepository;
    private $mealAlimentRepository;
    private $dietRepository;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['authenticate']) || $_SESSION['authenticate'] !== true) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['status' => 'error', 'message' => 'Acesso não autorizado.']);
            exit;
        }

        $this->connection = Connection::getConnection();
        
        $this->mealRepository = new MealRepository($this->connection);
        $this->mealAlimentRepository = new MealAlimentRepository($this->connection);
        $this->dietRepository = new DietRepository($this->connection);

        $this->mealService = new MealService(
            $this->mealRepository,       
            $this->mealAlimentRepository, 
            $this->dietRepository
        );
    }

    public function registerController() {
        header('Content-Type: application/json');

        if (isset($_POST['register'])) {
            try {
                $type        = $_POST['type'];
                $description = $_POST['description'];
                $fkDietId    = $_POST['fkDietId'];
                
                $photoArray  = $_FILES['photo'] ?? null;

                $alimentsString = $_POST['aliments'] ?? '[]';
                
                //  transforma de volta em array associativo (true)
                $aliments = json_decode($alimentsString, true);

                if (!is_array($aliments)) {
                    $aliments = []; 
                }

                $this->mealService->registerService(
                    $type, 
                    $description, 
                    $photoArray, 
                    $aliments, 
                    $fkDietId
                );

                echo json_encode([
                    'status' => 'success',
                    'message' => 'Refeição registrada com sucesso!'
                ]);

            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode([
                    'status' => 'error',
                    'message' => $e->getMessage()
                ]);
            }
            exit;
        }
    }

    public function updateController() {
        header('Content-Type: application/json');

        if (isset($_POST['update'])) {
            try {
                $id          = $_POST['update']; 
                $type        = $_POST['type'];
                $description = $_POST['description'];
                $fkDietId    = $_POST['fkDietId'];
                
                $photoArray  = $_FILES['photo'] ?? null;

                $alimentsString = $_POST['aliments'] ?? '[]';
                $aliments = json_decode($alimentsString, true);

                if (!is_array($aliments)) {
                    $aliments = []; 
                }

                $this->mealService->updateService(
                    $id,
                    $type,
                    $description,
                    $photoArray,
                    $aliments,
                    $fkDietId
                );

                echo json_encode([
                    'status' => 'success',
                    'message' => 'Refeição atualizada com sucesso!'
                ]);

            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode([
                    'status' => 'error',
                    'message' => $e->getMessage()
                ]);
            }
            exit;
        }
    }


    public function deleteController() {
        header('Content-Type: application/json');
        if (isset($_POST['delete'])) {
            try {
                $id = $_POST['delete'];
                $this->mealService->deleteMealService($id); 

                echo json_encode([
                    'status' => 'success', 
                    'message' => 'Refeição excluída com sucesso!'
                ]);
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode([
                    'status' => 'error', 
                    'message' => $e->getMessage()
                ]);
            }
            exit;
        }
    }

    public function selectMealByDietIdController($fkDietId) {
        header('Content-Type: application/json');
        try {
            $mealByDiet = $this->mealService->selectMealsByDietService($fkDietId); 
            echo json_encode([
                'status' => 'success',
                 'data' => $mealByDiet
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'error', 
                'message' => $e->getMessage()
            ]);
        }
        exit;
    }


    public function listAllController() {
        header('Content-Type: application/json');
        try {
            $allMeals = $this->mealRepository->selectAllMeals();
            echo json_encode([
                'status' => 'success',
                'data' => $allMeals
            ]);
        } catch (Exception $e){
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
        exit;
    }


    public function selectMealByIdController($id) {
        header('Content-Type: application/json');
        try {
            $meal = $this->mealService->getMealService($id);
            echo json_encode(['status' => 'success', 'data' => $meal]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
      exit;
    }
}