<?php
require_once __DIR__ . '/../models/MealModel.php';
require_once __DIR__ . '/../repositories/MealRepository.php'; 
require_once __DIR__ . '/../models/MealAlimentModel.php';

class MealService {
    private $mealRepository;
    private $mealAlimentRepository;
    private $dietRepository;

    public function __construct($mealRepository, $mealAlimentRepository, $dietRepository) {
        $this->mealRepository = $mealRepository;
        $this->mealAlimentRepository = $mealAlimentRepository;
        $this->dietRepository = $dietRepository;
    }


    public function registerService($type, $description, $photoArray, $aliments, $fkDietId) {
        $currentDietMeal = $this->dietRepository->selectDiet($fkDietId);
        if(!$currentDietMeal) {
            throw new Exception("Dieta não encontrada.");
        }

        $photoName = null;
        if(isset($photoArray) && !empty($photoArray)){
            $photoName = $this->uploadImage($photoArray);
        }

        $meal = new MealModel();
        $meal->setType($type);
        $meal->setDescription($description);
        $meal->setPhoto($photoName);
        $meal->setFkDietId($fkDietId);

        try {

            $this->mealRepository->getConnection()->beginTransaction();
            $this->mealRepository->insertMeal($meal);
            $mealId = $this->mealRepository->getConnection()->lastInsertId();

            if(!empty($aliments) && is_array($aliments)) {
                foreach ($aliments as $item) {
                    $mealAliment = new MealAlimentModel();
                    $mealAliment->setFkMealId($mealId);
                    $mealAliment->setFkAlimentId($item['id_aliment']);
                    $mealAliment->setQuantity($item['quantity']);

                    $this->mealAlimentRepository->insertMealAliment($mealAliment);
                }
            }

            $this->mealRepository->getConnection()->commit();
            return true;

        } catch (Exception $e) {
            $this->mealRepository->getConnection()->rollBack();
            throw new Exception("Error: " .$e->getMessage());
        }
    }


    public function updateService($id, $type, $description, $photoArray, $aliments, $fkDietId){

        $currentMeal = $this->mealRepository->selectMeal($id);
        if(!$currentMeal) {
            throw new Exception("Refeição não encontrada.");
        }
        
        $photoName = $currentMeal['photo'];
        if(isset($photoArray) && !empty($photoArray['name'])) {
            $photoName = $this->uploadImage($photoArray);
        }

        $meal = new MealModel();
        $meal->setType($type);
        $meal->setDescription($description);
        $meal->setPhoto($photoName);
        $meal->setId($id);
        $meal->setFkDietId($fkDietId);


        try {
            $this->mealRepository->getConnection()->beginTransaction();
            $this->mealRepository->updateMeal($meal);
            $this->mealAlimentRepository->deleteAllAlimentsByMealId($id);

            if(!empty($aliments) && is_array($aliments)) {
                foreach ($aliments as $item) {
                    $mealAliment = new MealAlimentModel();
                    $mealAliment->setFkMealId($id);
                    $mealAliment->setFkAlimentId($item['id_aliment']);
                    $mealAliment->setQuantity($item['quantity']);

                    $this->mealAlimentRepository->insertMealAliment($mealAliment);
                }
            }

            $this->mealRepository->getConnection()->commit();
            return true;
        }  catch (Exception $e) {
            $this->mealRepository->getConnection()->rollBack();
            throw new Exception("Error: " .$e->getMessage());
        }

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



    public function deleteMealService($id) {
        $this->mealRepository->getConnection();
        $this->mealRepository->getConnection()->beginTransaction();
        try {
            $this->mealAlimentRepository->deleteAllAlimentsByMealId($id);
            $this->mealRepository->deleteMeal($id);
            $this->mealRepository->getConnection()->commit();
            return true;
        } catch (Exception $e) {
            $this->mealRepository->getConnection()->rollback();
            throw $e;
        }
    }

    public function getMealService($id) {
        $mealData = $this->mealRepository->selectMeal($id);
        if(!$mealData || empty($mealData)) {
            throw new Exception("Refeição não encontrada");
        }

        $alimentsData = $this->mealAlimentRepository->selectAlimentsByMealId($id);
        $mealData[0]['aliments'] = $alimentsData;

        return $mealData;
    }

    public function selectMealsByDietService($fkDietId) {
        return $this->mealRepository->selectMealsByDietId($fkDietId);
    }

    public function selectAllMealsService(){
        return $this->mealRepository->selectAllMealsService();
    }
}