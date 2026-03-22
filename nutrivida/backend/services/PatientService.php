<?php
require_once __DIR__ . '/../repositories/PatientRepository.php';
require_once __DIR__ . '/../models/PatientModel.php';
require_once __DIR__ . '/../models/AddressModel.php';
require_once __DIR__ . '/../repositories/AddressRepository.php';
require_once __DIR__ . '/../services/AddressService.php';

class PatientService {
    private $patientRepository;
    private $addressRepository;

    public function __construct($patientRepository, $addressRepository){
        $this->patientRepository = $patientRepository;
        $this->addressRepository = $addressRepository;
    }

    public function logInService($email, $password) {        
        $currentPatient = $this->patientRepository->selectPatientByEmail($email);

        if (!$currentPatient) {
             return false;
         }

        if (password_verify($password, $currentPatient['password'])) {
             return $currentPatient;
        } else {
            return false; 
        }
    }

    public function registerService($name, $cpf, $dateBirth, $photoArray, $phone, $email, $password, $addressData) {

        $this->validate($name, $cpf, $dateBirth, $phone, $email, $password);

        $photoName = "Sem foto";
      
        $patient = new PatientModel();
        $patient->setName($name);
        $patient->setCpf($cpf);
        $patient->setDateBirth($dateBirth);
        $patient->setPhoto($photoName);
        $patient->setPhone($phone);
        $patient->setEmail($email);
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $patient->setPassword($hash);

        $address = new AddressModel();
        $address->setCountry($addressData['country']);
        $address->setNeighborhood($addressData['neighborhood']);
        $address->setCity($addressData['city']);
        $address->setState($addressData['state']);
        $address->setStreet($addressData['street']);
        $address->setNumber($addressData['number']);
        $address->setZipCode($addressData['zip_code']);

        if ($this->patientRepository->insertPatient($patient, $address)) {
            return true;
        } else {
            return false;
        }
    }

    public function updateService($id, $name, $cpf, $dateBirth, $photoArray, $phone, $email, $password, $addressData) {
        
        $currentPatient = $this->patientRepository->selectPatientById($id);
        $photoName = $currentPatient['photo'];
       if (isset($photoArray) && $photoArray['error'] === UPLOAD_ERR_OK) {
            $photoName = $this->uploadImage($photoArray);
        }

        $finalPassword = $currentPatient['password'];
        if(!empty($password)) {
            $finalPassword = password_hash($password, PASSWORD_DEFAULT);
        }

        $patient = new PatientModel();
        $patient->setId($id);
        $patient->setName($name);
        $patient->setCpf($cpf);
        $patient->setDateBirth($dateBirth);
        $patient->setPhoto($photoName);
        $patient->setPhone($phone);
        $patient->setEmail($email);
        $patient->setPassword($finalPassword);

        $this->patientRepository->updatePatient($patient);

        $address = new AddressModel();
        $address->setFkPatientId($id);
        $address->setCountry($addressData['country']);
        $address->setNeighborhood($addressData['neighborhood']);
        $address->setCity($addressData['city']);
        $address->setState($addressData['state']);
        $address->setStreet($addressData['street']);
        $address->setNumber($addressData['number']);

        $this->addressRepository->updateAddress($address);

    }

    
    private function uploadImage($file) {
    require __DIR__ . '/../config/Upload.php';

        $fileName = $file['name'];
        $fileSize = $file['size'];
        $fileTmp = $file['tmp_name'];
        $fileError = $file['error'];

        if( $fileError !== UPLOAD_ERR_OK) {
            throw new Exception("Erro no upload. Código: " . $fileError);
        }

        if($overwrite == "no" && file_exists("$path/$fileName")) {
            throw new Exception("O arquivo '$fileName' já existe.");
        }

        if($limit_size == "yes" && ($fileSize > $size_bytes)) {
            throw new Exception("Arquivo muito grande. Máximo: " . ($size_bytes/1000) . "KB");
        }

        $ext = strtolower(strrchr($fileName, '.'));

        if(($limit_ext == "yes") && !in_array($ext, $valid_extensions)) {
            throw new Exception("Extensão inválida");
        }

        if(move_uploaded_file($fileTmp, "$path/$fileName")) {
            return $fileName;
        } else {
            throw new Exception("Falha ao mover o arquivo.");
        }
    }

    public function deleteAccountService($id) {
        return $this->patientRepository->deletePatient($id);
    }

    public function getPatientService($id) {
        return $this->patientRepository->selectPatientById($id);
    }

    public function getAllPatientService() {
        return $this->patientRepository->selectAllPatients();
    }

    public function getPatientByNameService($name) {
        return $this->patientRepository->selectPatientByName($name);
    }


   public function updatePasswordService($id, $currentPwd, $newPwd) {
        
        $patient = $this->patientRepository->selectPatientById($id);
        if(!$patient) throw new Exception("Paciente não encontrado.");

        $email = $patient['email'];

        $loginResult = $this->logInService($email, $currentPwd);

        if (!$loginResult) {
            throw new Exception("A senha atual está incorreta. Verifique se digitou corretamente.");
        }

        $newHash = password_hash($newPwd, PASSWORD_DEFAULT);

        $patientModel = new PatientModel();
        $patientModel->setId($id);
        $patientModel->setName($patient['name']);
        $patientModel->setCpf($patient['cpf']);
        $patientModel->setDateBirth($patient['date_birth']);
        $patientModel->setPhoto($patient['photo']);
        $patientModel->setPhone($patient['phone']);
        $patientModel->setEmail($patient['email']);

        $patientModel->setPassword($newHash); 

        return $this->patientRepository->updatePatient($patientModel);
    }

    private function validate($name, $cpf, $dateBirth, $phone, $email, $password) {
        if (empty($name) || empty($cpf) || empty($dateBirth) || empty($phone) || empty($email)  || empty($password)) {
            throw new Exception('Todos os campos devem estar preenchidos');
        }
    }
}