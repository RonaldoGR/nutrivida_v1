<?php 
require_once __DIR__ . '/../repositories/NutritionistRepository.php';
require_once __DIR__ . '/../models/NutritionistModel.php';


Class NutritionistService {
    private $nutritionistRepository;

    public function __construct($nutritionistRepository){
      $this->nutritionistRepository = $nutritionistRepository;
    }


    public function logInService($email, $password) {
        $currentNutritionist = $this->nutritionistRepository->selectNutritionistByEmail($email);

        if(!$currentNutritionist) {
            return false;
        }

        if (password_verify($password, $currentNutritionist['password'])) {
             return $currentNutritionist;
        } else {
            return false; 
        }
    }


    public function registerService($name, $cpf, $crn, $email, $photoArray, $password) {

        $this->validate($name, $cpf, $crn, $email, $photoArray, $password);

        $photoName = null;
        if (isset($photoArray) && !empty($photoArray['name'])) {
           $photoName = $this->uploadImage($photoArray);
        }

        $nutritionist = new NutritionistModel();
        $nutritionist->setName($name);
        $nutritionist->setCpf($cpf);
        $nutritionist->setCrn($crn);
        $nutritionist->setEmail($email);
        $nutritionist->setPhoto($photoName);
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $nutritionist->setPassword($hash);

        return $this->nutritionistRepository->insertNutritionist($nutritionist);
    }


    public function updateService($id, $name, $cpf, $crn, $email, $photoArray, $password) {
        $currentNutritionist = $this->nutritionistRepository->selectNutritionist($id);
        $photoName = $currentNutritionist['photo'];

        if(isset($photoArray) && !empty($photoArray['name'])) {
            $photoName = $this->uploadImage($photoArray);
        }

        $nutritionist = new NutritionistModel();
        $nutritionist->setId($id);
        $nutritionist->setName($name);
        $nutritionist->setCpf($cpf);
        $nutritionist->setCrn($crn);
        $nutritionist->setEmail($email);
        $nutritionist->setPhoto($photoName);


        if(!empty($password)) {
            $nutritionist->setPassword(password_hash($password, PASSWORD_DEFAULT));
        } else {
            $nutritionist->setPassword($currentNutritionist['password']);
        }

  
        return $this->nutritionistRepository->updateNutritionist($nutritionist);
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
       return $this->nutritionistRepository->deleteNutricionist($id);
    }

    public function getNutritionistService($id) {
        return $this->nutritionistRepository->selectNutritionist($id);
    }

    public function getAllNutritionistService() {
        return $this->nutritionistRepository->selectAllNutritionist();
    }

    public function getNutritionistByNameService($name) {
        return $this->nutritionistRepository->selectNutritionistByName($name);
    }



 public function updatePasswordService($id, $currentPwd, $newPwd) {
        
        $currentData = $this->nutritionistRepository->selectNutritionist($id);
        
        if(!$currentData) {
             throw new Exception("Nutricionista não encontrado.");
        }

        if (!password_verify($currentPwd, $currentData['password'])) {
            throw new Exception("A senha atual está incorreta.");
        }

        $newHash = password_hash($newPwd, PASSWORD_DEFAULT);
        $nutriModel = new NutritionistModel();
        
        $nutriModel->setId($currentData['id_nutritionist']);
        $nutriModel->setName($currentData['name']);
        $nutriModel->setCpf($currentData['cpf']);
        $nutriModel->setCrn($currentData['crn']);
        $nutriModel->setEmail($currentData['email']);
        $nutriModel->setPhoto($currentData['photo']); // Mantém a foto atual
        

        $nutriModel->setPassword($newHash); 

        return $this->nutritionistRepository->updateNutritionist($nutriModel);
    }

    private function validate($name, $cpf, $crn, $email, $photo, $password) {
        if (empty($name) || empty($cpf) || empty($crn) || empty($email) || empty($photo) || empty($password)) {
            throw new Exception('Todos os campos devem estar preenchidos');
        }
    }
}