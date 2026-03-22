<?php

require_once __DIR__ . '/../config/Connection.php';
require_once __DIR__ . '/../services/NutritionistService.php';
require_once __DIR__ . '/../repositories/NutritionistRepository.php';
require_once __DIR__ . '/../repositories/PatientRepository.php';
require_once __DIR__ . '/../services/PatientService.php';
require_once __DIR__ . '/../repositories/AddressRepository.php';
require_once __DIR__ . '/../services/AddressService.php';



class NutritionistController {
    private $connection;
    private $nutritionistRepository;
    private $nutritionistService;
    private $patientRepository;
    private $patientService;
    private $addressRepository;
    private $addressService;

    public function __construct() {
         if (session_status() === PHP_SESSION_NONE) {
               session_start();
        }
        $this->connection = Connection::getConnection();
        
        $this->nutritionistRepository = new NutritionistRepository($this->connection);
        $this->addressRepository = new AddressRepository($this->connection);
        $this->patientRepository = new PatientRepository($this->connection);

        $this->nutritionistService = new NutritionistService($this->nutritionistRepository);
        $this->patientService = new PatientService($this->patientRepository, $this->addressRepository);
        $this->addressService = new AddressService($this->addressRepository);
    }


    public function logInController() {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);

        if (isset($_POST['login']) || (isset($data['login']) && $data['login'] == true)) {
            try {
                $email = $data['email'] ?? $_POST['email'] ?? '';
                $password = $data['password'] ?? $_POST['password'] ?? '';

                if(!empty($email) && !empty($password)) {
                    $loggedNutritionist = $this->nutritionistService->logInService($email, $password);

                    if($loggedNutritionist) {
                        $_SESSION['authenticate'] = true; 
                        $_SESSION['id_nutritionist'] = $loggedNutritionist['id_nutritionist']; 
                        $_SESSION['name'] = $loggedNutritionist['name'];

                         echo json_encode([
                            'status' => 'success',
                            'message' => 'Login realizado com sucesso!',
                            'redirect' => '/frontend/views/nutritionist_dashboard.html' 
                        ]);
                        exit;
                    }
                }
            } catch (Exception $e) {
                http_response_code(401);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'E-mail ou senha incorretos.',
                    'errorMessage' => $e->getMessage()
                ]);
            }
            exit;
        }
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Nenhum dado recebido.']);
        exit;
    }

    public function logOutController() {
        header('Content-Type: application/json');
        $_SESSION = [];
        session_destroy();
        echo json_encode([
                            'status' => 'success',
                            'message' => 'Sessão finalizada',
                            'redirect' => '../../view/home.html' 
                        ]);
        exit;
    }




    public function registerController() {
         header('Content-Type: application/json');
        if (isset($_POST['register'])) {
            try {
                $name = $_POST['name'];
                $cpf = $_POST['cpf'];
                $crn = $_POST['crn'];
                $email = $_POST['email'];
                $password = $_POST['password'];
                $photoArray = $_FILES['photo'] ?? null;
                
                $newNutritionist = $this->nutritionistService->registerService($name, $cpf, $crn, $email, $photoArray, $password);

                echo json_encode([
                    'status' => 'success',
                    'data' => $newNutritionist,
                    'message' => 'Nutricionista registrado com sucesso!'
                ]);
            } catch(Exception $e) {
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

            try  {
                $id = $_POST['update'];
                $currentNutritionist = $this->nutritionistRepository->selectNutritionist($id);
                
                $name     = !empty($_POST['name'])     ? $_POST['name']     : $currentNutritionist['name'];
                $cpf      = !empty($_POST['cpf'])      ? $_POST['cpf']      : $currentNutritionist['cpf'];
                $crn      = !empty($_POST['crn'])      ? $_POST['crn']      : $currentNutritionist['crn'];
                $email    = !empty($_POST['email'])    ? $_POST['email']    : $currentNutritionist['email'];
                $password = !empty($_POST['password']) ? $_POST['password'] : null;
                $photo = $_FILES['photo'] ?? null;

                $updatedNutritionist = $this->nutritionistService->updateService($id, $name, $cpf, $crn, $email, $photo, $password);
                echo json_encode([
                    'status' => 'success',
                    'data' => $updatedNutritionist,
                    'message' => 'Perfil atualizado com sucesso!'
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


    
    public function selectLoggedNutritionist($id) {
        header('Content-Type: application/json');
        try {
            $loggedNutritionist = $this->nutritionistService->getNutritionistService($id);
            if (!$loggedNutritionist) {
                 throw new Exception("Usuário não encontrado.");
            }

            unset($loggedNutritionist['password']);

            echo json_encode([
                'status' => 'success',
                'data' => $loggedNutritionist
            ]);
        } catch (Exception $e) {
                http_response_code(400);
                echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        } 
        exit;
    }

    public function selectAllNutritionistController() {
        try {
            $nutritionistList = $this->nutritionistService->getAllNutritionistService();

            foreach($nutritionistList as $nutritionist) {
                unset($nutritionist['password']);
            }

            unset($nutritionist);

             echo json_encode([
                'status' => 'success',
                'data' => $nutritionistList
             ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
        exit;
    }

    public function searchNutritionistByNameController() {
        header('Content-Type: application/json');
        if (isset($_POST['searchByName'])) {
            try {
                $name = "%" . strtoupper($_POST['name']) . "%";
                $nutritionistSearched = $this->nutritionistService->getNutritionistByNameService($name);

                unset($nutritionistSearched['password']);
                
                echo json_encode([
                    'status' => 'success',
                    'data' => $nutritionistSearched
                ]);
            } catch (Exception $e) {
                http_response_code(400);
                echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
                ]);
            }
            exit;
        }
    }

    public function deleteController()  {
        header('Content-Type: application/json');
        if (isset($_POST['delete'])) {
            try {
                $id = $_POST['delete'];
                $this->nutritionistService->deleteAccountService($id);

                echo json_encode([
                    'status' => 'success',
                    'message' => 'Conta excluída com sucesso!'
                ]);
            } catch (Exception $e) {
                http_response_code(400);
                echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
             ]);
            }
        }
        exit;
    }

    
    public function selectAllPatientController() {
         header('Content-Type: application/json');
         try {
              $patientList = $this->patientService->getAllPatientService();

              foreach ($patientList as &$patient) {
                        unset($patient['password']);
                }
         
                unset($patient);

              echo json_encode([
                'status' => 'success',
                'data' => $patientList
              ]);
         } catch (Exception $e) {
                http_response_code(400);
                echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
        exit;
    }

    public function searchPatientByNameController() {
        header('Content-Type: application/json');
        if (isset($_POST['searchByName'])) {
            try {
                $name = "%" . strtoupper($_POST['name']) . "%";
                $patientSearched = $this->patientService->getPatientByNameService($name);

                unset($patientSearched['password']);
                
                echo json_encode([
                    'status' => 'success',
                    'data' => $patientSearched
                ]);
            } catch (Exception $e) {
                http_response_code(400);
                echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
              ]);
            }
        }
      exit;
    }


    public function updatePasswordController() {
    header('Content-Type: application/json');
    if (isset($_POST['updatePassword'])) {
            try {
                if (!isset($_SESSION['id_nutritionist'])) throw new Exception("Não logado como Nutricionista.");
                
                $id = $_SESSION['id_nutritionist'];
                $currentPwd = $_POST['currentPassword'] ?? '';
                $newPwd = $_POST['newPassword'] ?? '';

                if(empty($currentPwd) || empty($newPwd)) {
                    throw new Exception("Preencha todos os campos.");
                }

                $this->nutritionistService->updatePasswordService($id, $currentPwd, $newPwd);

                echo json_encode(['status' => 'success', 'message' => 'Senha atualizada com sucesso!']);

            } catch (Exception $e) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            }
        exit;
        }
    }



}