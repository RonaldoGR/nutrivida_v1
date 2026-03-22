<?php

require_once __DIR__ . '/../config/Connection.php';
require_once __DIR__ . '/../repositories/AddressRepository.php';
require_once __DIR__ . '/../services/AddressService.php';

class AddressController {
    private $connection;
    private $addressRepository;
    private $addressService;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['authenticate']) || $_SESSION['authenticate'] !== true) {
            header('Content-Type: application/json');
            http_response_code(401); 
            echo json_encode(['status' => 'error', 'message' => 'Acesso negado.']);
            exit;
        }


        $this->connection = Connection::getConnection();
        $this->addressRepository = new AddressRepository($this->connection);
        $this->addressService = new AddressService($this->addressRepository);
    }

    public function registerController() {
        header('Content-Type: application/json');
        if (isset($_POST['registerAddress'])) {
            try {
                $country      = $_POST['country'];  
                $neighborhood = $_POST['neighborhood'];
                $city         = $_POST['city'];
                $state        = $_POST['state'];
                $street       = $_POST['street'];
                $number       = $_POST['number'];
                $zip_code     = $_POST['zip_code'];
                $fkPatientId  = $_POST['fkPatientId'];
    
                $newAddress = $this->addressService->registerService(
                    $country,
                    $neighborhood,
                    $city,
                    $state,
                    $street,
                    $number,
                    $zip_code,
                    $fkPatientId
                );

                echo json_encode([
                    'status' => 'success',
                    'data' => $newAddress,
                    'message' => 'Endereço registrado com sucesso!'
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

        if (isset($_POST['updateAddress'])) {
            try {
                $id = $_POST['updateAddress'];
    
                $current = $this->addressRepository->selectAddress($id);
                $country      = !empty($_POST['country'])      ? $_POST['country']      : $current['country']; 
                $neighborhood = !empty($_POST['neighborhood']) ? $_POST['neighborhood'] : $current['neighborhood'];
                $city         = !empty($_POST['city'])         ? $_POST['city']         : $current['city'];
                $state        = !empty($_POST['state'])        ? $_POST['state']        : $current['state'];
                $street       = !empty($_POST['street'])       ? $_POST['street']       : $current['street'];
                $number       = !empty($_POST['number'])       ? $_POST['number']       : $current['number'];   
                $zip_code     = !empty($_POST['zip_code'])     ? $_POST['zip_code']     : $current['zip_code'];
                $fkPatientId  = !empty($_POST['fkPatientId'])  ? $_POST['fkPatientId']  : $current['fkPatientId'];
    
               $addressUpdated = $this->addressService->updateService(
                    $id,
                    $country,
                    $neighborhood,
                    $city,
                    $state,
                    $street,
                    $number,
                    $zip_code,
                    $fkPatientId
                );

                echo json_encode([
                    'status' => 'success',
                    'data' => $addressUpdated,
                    'message' => 'Endereço atualizado com sucesso!'
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

        if (isset($_POST['deleteAddress'])) {
            try {
                $id = $_POST['deleteAddress'];
                $this->addressService->deleteAddressService($id);
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Endereço excluído com sucesso!'
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

    public function listAllController() {
        header('Content-Type: application/json');
        try {
            $addressList = $this->addressService->getAllAddressesService();
            echo json_encode([
                'status' => 'success',
                'data' => $addressList
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

    public function listByPatientController() {
         header('Content-Type: application/json');

         if (isset($_POST['searchByPatient'])) {
            try {
                    $fkPatientId = $_SESSION['id'];
                    $dataPatient = $this->addressService->getAddressByPatientService($fkPatientId);
                    echo json_encode([
                        'status' => 'success',
                        'data' => $dataPatient
                    ]);
            } catch (Exception $e) {
                    http_response_code(500);
                    echo json_encode([
                    'status' => 'error',
                    'message' => $e->getMessage()
                ]);
            }
        }
        exit;
    }
}
