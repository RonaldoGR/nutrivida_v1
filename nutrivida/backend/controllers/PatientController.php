<?php

require_once __DIR__ . '/../config/Connection.php';
require_once __DIR__ . '/../repositories/PatientRepository.php';
require_once __DIR__ . '/../services/PatientService.php';
require_once __DIR__ . '/../repositories/AddressRepository.php';
require_once __DIR__ . '/../services/AddressService.php';


class PatientController {
    private $connection;
    private $patientRepository;
    private $patientService;
    private $addressRepository;
    private $addressService;

    public function __construct()  {
        if (session_status() === PHP_SESSION_NONE) {
               session_start();
        }

        $this->connection = Connection::getConnection();

        $this->patientRepository = new PatientRepository($this->connection);
        $this->addressRepository = new AddressRepository($this->connection);

        $this->patientService = new PatientService($this->patientRepository, $this->addressRepository);
        $this->addressService = new AddressService($this->addressRepository);
    }

    public function logInController() {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);

        if (isset($_POST['login']) || isset($data['login']) && $data['login'] == true) {
            try {
                $email = $data['email'] ?? $_POST['email'] ?? '';
                $password = $data['password'] ?? $_POST['password'] ?? '';

                if (!empty($email) && !empty($password)) {
                    $loggedInUser = $this->patientService->logInService($email, $password);

                    if ($loggedInUser) {
                        $_SESSION['authenticate'] = true; 
                        $_SESSION['id'] = $loggedInUser['id_patient']; 
                        $_SESSION['name'] = $loggedInUser['name'];

                        echo json_encode([
                            'status' => 'success',
                            'message' => 'Login realizado com sucesso!',
                            'redirect' => '../../frontend/views/patient_dashboard.html' 
                        ]);
                        exit;
                    } else {
                        http_response_code(401);
                        echo json_encode([
                            'status' => 'error', 
                            'message' => 'Email ou senha incorretos.'
                        ]);
                        exit;
                    }
                } else {
                    http_response_code(400);
                     echo json_encode([
                        'status' => 'error', 
                        'message' => 'Preencha todos os campos.'
                    ]);
                     exit;
                }
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Erro interno do servidor.',
                    'errorMessage' => $e->getMessage()
                ]);
            }
            exit;
        }
    }

    public function logOutController() {
        header('Content-Type: application/json');
        $_SESSION = [];
        session_destroy();
        echo json_encode([
                            'status' => 'success',
                            'message' => 'Sessão finalizada',
                            'redirect' => '../../frontend/view/login.html' 
                        ]);
        exit;
    }

    public function registerController()  {
        header('Content-Type: application/json');
        if (isset($_POST['register'])) {
            try {
                $name = $_POST['name'];
                $cpf = $_POST['cpf'];
                $dateBirth = $_POST['dateBirth'];
                $phone = $_POST['phone'];
                $email = $_POST['email'];
                $password = $_POST['password'];
                $photoArray = $_FILES['photo'] ?? null;
           
                 $address = [
                    'country'      => $_POST['country'],
                    'neighborhood' => $_POST['neighborhood'],
                    'city'         => $_POST['city'],
                    'state'        => $_POST['state'],
                    'street'       => $_POST['street'],
                    'number'       => $_POST['number'],
                    'zip_code'     => $_POST['zip_code'],
                 ];
    

                $newPatient = $this->patientService->registerService($name, $cpf, $dateBirth, $photoArray, $phone, $email, $password, $address);

                echo json_encode([
                    'status' => 'success',
                    'data' => $newPatient,
                    'message' => 'Paciente registrado com sucesso!'
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
        if (isset($_POST['updateProfile'])) {

            try {
               if (isset($_POST['updateProfile'])) {
                    if (!isset($_SESSION['id'])) {
                        throw new Exception("Sessão expirada ou inválida.");
                    }
                    $id = $_SESSION['id'];
                }
                $currentPatient = $this->patientRepository->selectPatientById($id);
                if (!$currentPatient) {
                    throw new Exception("Paciente não encontrado.");
                }
                $currentAddress = $this->addressRepository->selectAddressByPatient($id);
    
                $name      = !empty($_POST['name'])      ? $_POST['name']      : $currentPatient['name'];
                $cpf       = !empty($_POST['cpf'])       ? $_POST['cpf']       : $currentPatient['cpf'];
                $dateBirth = !empty($_POST['dateBirth']) ? $_POST['dateBirth'] : $currentPatient['dateBirth'];
                $phone     = !empty($_POST['phone'])     ? $_POST['phone']     : $currentPatient['phone'];
                $email     = !empty($_POST['email'])     ? $_POST['email']     : $currentPatient['email'];
                $password  = !empty($_POST['password'])  ? $_POST['password']  : null;
                $photo = $_FILES['photo'] ?? null;

    
                $address = [
                'neighborhood' => !empty($_POST['neighborhood']) ? $_POST['neighborhood'] : $currentAddress['neighborhood'],
                'city'         => !empty($_POST['city'])         ? $_POST['city']         : $currentAddress['city'],
                'state'        => !empty($_POST['state'])        ? $_POST['state']        : $currentAddress['state'],
                'street'       => !empty($_POST['street'])       ? $_POST['street']       : $currentAddress['street'],
                'zip_code'     => !empty($_POST['zip_code'])     ? $_POST['zip_code']     : $currentAddress['zip_code'],
                'number'       => !empty($_POST['number'])       ? $_POST['number']       : $currentAddress['number'],
                'country'      => !empty($_POST['country'])      ? $_POST['country']      : $currentAddress['country']
            ];
            
            if(isset($_POST['cep']) && !empty($_POST['cep'])) {
                    $address['zip_code'] = $_POST['cep'];
                }

            $updatedPatient = $this->patientService->updateService($id, $name, $cpf, $dateBirth, $photo, $phone, $email, $password ,$address);

            echo json_encode([
                'status' => 'success',
                'data' => $updatedPatient,
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

    public function selectLoggedPatient($id) {
        header('Content-Type: application/json');
        try {
            $loggedPatient = $this->patientService->getPatientService($id);
            if (!$loggedPatient) {
                 throw new Exception("Usuário não encontrado.");
            }

            unset($loggedPatient['password']);

            $addressData = $this->addressService->getAddressByPatientService($id);
            $loggedPatient['address'] = $addressData ? $addressData : null;

            echo json_encode([
                'status' => 'success',
                'data' => $loggedPatient
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

    public function selectPatientByNameController($name) {
         header('Content-Type: application/json');
        try {
            
            $term = "%" . strtoupper($name) . "%";

            $patientList = $this->patientService->getPatientByNameService($term);
            

            foreach($patientList as $patient) {
                unset($patient['password']);
            }

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

    public function selectAllPatientController() {
        header('Content-Type: application/json');

        try {
            $allPatient = $this->patientService->getAllPatientService();
            if(!$allPatient) {
                throw new Exception("Não há pacientes registrados.");
            }

            unset($allPatient['password']);

            echo json_encode([
                'status' => 'success',
                'data' => $allPatient
            ]);

        } catch( Exception $e) {
             http_response_code(400);
                echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
       exit;
    }



    public function deleteController() {
        header('Content-Type: application/json');
        try {
            if (isset($_POST['delete'])) {
                $id = $_POST['delete'];
                $deletedPatient = $this->patientService->deleteAccountService($id);
                
                if(!$deletedPatient) {
                    throw new Exception("Usuário não encontrado");
                }
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Conta excluída com sucesso!'
                ]);
            }

        } catch (Exception $e) {
                http_response_code(400);
                echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }    
        exit;
    }
    public function updatePasswordController() {
        header('Content-Type: application/json');
        if (isset($_POST['updatePassword'])) {
            try {
                if (!isset($_SESSION['id'])) throw new Exception("Não logado.");
                
                $id = $_SESSION['id'];
                $currentPwd = $_POST['currentPassword'] ?? '';
                $newPwd = $_POST['newPassword'] ?? '';
    
                if(empty($currentPwd) || empty($newPwd)) {
                    throw new Exception("Preencha todos os campos.");
                }
    

                $this->patientService->updatePasswordService($id, $currentPwd, $newPwd);
    
                echo json_encode(['status' => 'success', 'message' => 'Senha atualizada!']);
    
            } catch (Exception $e) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            }
            exit;
        }
    }


    public function saveAddressController() {
        header('Content-Type: application/json');
        try {
            if (!isset($_SESSION['id'])) {
                throw new Exception("Usuário não autenticado.");
            }

            $data = [
                'id_address'   => $_POST['id_address'] ?? null, 
                'country'      => $_POST['country'],
                'neighborhood' => $_POST['neighborhood'],
                'city'         => $_POST['city'],
                'state'        => $_POST['state'],
                'street'       => $_POST['street'],
                'number'       => $_POST['number'],
                'zip_code'     => $_POST['cep'] 
            ];

          $result = $this->addressService->saveAddressService($data, $_SESSION['id']);

            if ($result) {
                echo json_encode([
                    'status' => 'success', 
                    'message' => 'Endereço salvo com sucesso!'
                ]);
            } else {
                throw new Exception("Erro ao gravar no banco de dados. Verifique os dados.");
            }

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit;
    }

    public function deleteAddressController() {
        header('Content-Type: application/json');
        try {
            if (!isset($_SESSION['id'])) throw new Exception("Usuário não autenticado.");
        
            $idAddress = $_POST['id_address']; 
            
            $this->addressService->deleteAddressService($idAddress);
            
            echo json_encode(['status' => 'success', 'message' => 'Endereço excluído!']);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit;
    }



}

