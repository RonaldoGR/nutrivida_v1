<?php

require_once __DIR__ . '/../config/Connection.php';
require_once __DIR__ . '/../repositories/DietRepository.php';
require_once __DIR__ . '/../services/DietService.php';
require_once __DIR__ . '/../models/DietModel.php';
require_once __DIR__ . '/../repositories/MealRepository.php';
require_once __DIR__ . '/../repositories/MealAlimentRepository.php';

class DietController {
    private $connection;
    private $dietRepository;
    private $dietService;
    private $mealRepository;
    private $mealAlimentRepository;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->connection = Connection::getConnection();
        $this->dietRepository = new DietRepository($this->connection);
        $this->dietService = new DietService($this->dietRepository);
        $this->mealRepository = new MealRepository($this->connection);
        $this->mealAlimentRepository = new MealAlimentRepository($this->connection);
    }

    public function registerController() {
         header('Content-Type: application/json');
        if (isset($_POST['register'])) {
        
            try {

                 if (!isset($_SESSION['id_nutritionist'])) {
                     throw new Exception("Usuário não autenticado.");
                 }
              
                $status  = $_POST['status'];          
                $start   = $_POST['startDate'];
                $end     = $_POST['endDate'];
                $fkNutri = $_SESSION['id_nutritionist'];
                $fkPatient = $_POST['fkPatientId'];
                
            
                 $dietData = $this->dietService->registerService(
                            $status,
                            $start,
                            $end,
                            $fkNutri,
                            $fkPatient
                          );

                echo json_encode([
                    'status' => 'success',
                    'data' => $dietData
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
                $id = $_POST['id'];
                $currentDiet = $this->dietRepository->selectDiet($id);

                if ($currentDiet['fk_nutritionist_id'] != $_SESSION['id_nutritionist']) {
                     throw new Exception("Você não tem permissão para editar esta dieta.");
                }

                $status = !empty($_POST['status']) ? $_POST['status'] : $currentDiet['status'];
                $start  = !empty($_POST['startDate']) ? $_POST['startDate'] : $currentDiet['start_date'];
                $end    = !empty($_POST['endDate']) ? $_POST['endDate'] : $currentDiet['end_date'];
                $fkNutri = $currentDiet['fk_nutritionist_id'];
                $fkPatient = !empty($_POST['fkPatientId']) ? $_POST['fkPatientId'] : $currentDiet['fk_patient_id'];

                $dietData = $this->dietService->updateService(
                                $id,
                                $status,
                                $start,
                                $end,
                                $fkNutri,
                                $fkPatient
                            );
                echo json_encode([
                    'status' => 'success',
                    'data' => $dietData
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


    public function selectDietByIdController($id) {
        header('Content-Type: application/json');

        try {
            $dietById = $this->dietService->getDietService($id);
            echo json_encode([
                'status' => 'success',
                'data' => $dietById
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

public function selectDietsByPatientIdController($fkPatientId) {
  header('Content-Type: application/json');
        
        try {
            if (!isset($_SESSION['id'])) {
                http_response_code(401);
                throw new Exception("Usuário não autenticado.");
            }

            $patientId = $_SESSION['id'];
            
            $diets = $this->dietService->getDietsByPatientIdService($patientId);
            $fullDiets = [];

            if($diets) {
                foreach($diets as $diet) {
                    $dietId = $diet['id_diet'];
                    
                    // 2. Para cada dieta, busca as refeições associadas
                    $meals = $this->mealRepository->selectMealsByDietId($dietId);
                    $mealsFull = [];

                    if($meals) {
                        foreach($meals as $m) {
                            // 3. Para cada refeição, busca os alimentos (ingredientes)
                            $aliments = $this->mealAlimentRepository->selectAlimentsByMealId($m['id_meal']);
                            $m['aliments'] = $aliments;
                            $mealsFull[] = $m;
                        }
                    }
                    
                    // 4. Anexa as refeições completas à dieta
                    $diet['meals'] = $mealsFull;
                    $fullDiets[] = $diet;
                }
            }
            
            echo json_encode([
                'status' => 'success',
                'data' => $fullDiets ?: []
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



    public function selectAllDietController() {
        header('Content-Type: application/json');

        try {
            $diets = $this->dietService->getAllDietService($_SESSION['id_nutritionist']);
            echo json_encode([
                'status' => 'success',
                'data' => $diets
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

    public function searchDietByStatusController() {
         header('Content-Type: application/json');
        if (isset($_POST['searchByStatus'])) {
            try {
                $status = "%" . strtoupper($_POST['status']) . "%";
                $dietList = $this->dietService->getDietByStatusService($status);
                echo json_encode([
                    'status' => 'success',
                    'data' => $dietList
                ]);

            } catch (\Exception $e) {
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
                $this->dietService->deleteDietService($id);
                echo json_encode([
                    'status' => 'success'
                ]);

            } catch (\Exception $e) {
                http_response_code(500);
                echo json_encode([
                    'status' => 'error',
                    'message' => $e->getMessage()
                ]);
            }
            exit;
        }
    }


    public function selectFullDetailsController() {
        header('Content-Type: application/json');

        $idDiet = $_GET['id'] ?? null;
        if(!$idDiet) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'ID é obrigatório'
            ]); 
        }

        try {
            $diet = $this->dietService->getDietService($idDiet);
            if(!$diet){
                throw new Exception("Dieta não encontrada.");
            }

            $meals = $this->mealRepository->selectMealsByDietId($idDiet);
            $mealsFull = [];

            if($meals) {
                foreach($meals as $m) {
                    $aliments = $this->mealAlimentRepository->selectAlimentsByMealId($m['id_meal']);
                    $m['aliments'] = $aliments;
                    $mealsFull[] = $m;
                }
            }

            $response = [
                'diet_info' => $diet,
                'meals' => $mealsFull
            ];

            echo json_encode([
                'status' => 'success',
                'data' => $response
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


    public function selectDietsByPatientIdParamController($patientId) {
        header('Content-Type: application/json');
        
        try {
            if (!isset($_SESSION['id_nutritionist'])) {
                http_response_code(403); 
                throw new Exception("Acesso negado. Apenas nutricionistas podem realizar esta ação.");
            }

            if (!$patientId) {
                throw new Exception("ID do paciente é obrigatório.");
            }

            $diets = $this->dietService->getDietsByPatientIdService($patientId);
            $fullDiets = [];

            if($diets) {
                foreach($diets as $diet) {
                    $dietId = $diet['id_diet'];
                    
                    $meals = $this->mealRepository->selectMealsByDietId($dietId);
                    $mealsFull = [];

                    if($meals) {
                        foreach($meals as $m) {
                            $aliments = $this->mealAlimentRepository->selectAlimentsByMealId($m['id_meal']);
                            $m['aliments'] = $aliments;
                            $mealsFull[] = $m;
                        }
                    }
                    
                    $diet['meals'] = $mealsFull;
                    $fullDiets[] = $diet;
                }
            }
            
            echo json_encode([
                'status' => 'success',
                'data' => $fullDiets ?: []
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