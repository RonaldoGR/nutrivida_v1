<?php
require_once __DIR__ . '/../repositories/DietRepository.php';
require_once __DIR__ . '/../models/DietModel.php';

class DietService {
    private $dietRepository;

    public function __construct($dietRepository){
        $this->dietRepository = $dietRepository;
    }

    public function registerService($status, $startDate, $endDate, $fkNutritionistId, $fkPatient) {

        $this->validate($status, $endDate, $startDate, $fkNutritionistId, $fkPatient);

            $diet = new DietModel();
        $diet->setStatus($status);
        $diet->setEndDate($endDate);
        $diet->setStartDate($startDate);
        $diet->setFkNutritionistId($fkNutritionistId);
        $diet->setFkPatientId($fkPatient);

        return $this->dietRepository->insertDiet($diet);
    }


    public function updateService($id, $status, $startDate, $endDate, $fkNutritionistId, $fkPatientId) {

        $currentDiet = $this->dietRepository->selectDiet($id);
        if (!$currentDiet) {
            throw new Exception("Dieta não encontrada.");
        }

        $diet = new DietModel();
        $diet->setId($id);
        $diet->setStatus($status);
        $diet->setEndDate($endDate);
        $diet->setStartDate($startDate);

        $diet->setFkNutritionistId($fkNutritionistId);
        $diet->setFkPatientId($fkPatientId);

        return $this->dietRepository->updateDiet($diet);
    }

    public function deleteDietService($id) {
        return $this->dietRepository->deleteDiet($id);
    }

    public function getDietService($id) {
        return $this->dietRepository->selectDiet($id);
    }

    public function getDietsByPatientIdService($patientId) {
        return $this->dietRepository->selectDietsByPatientId($patientId);   
    }

    public function getAllDietService($fkNutritionistId) {
        return $this->dietRepository->selectAllDiets($fkNutritionistId);
    }

    public function getDietByStatusService($status) {
        return $this->dietRepository->selectDietByStatus($status);
    }

    private function validate($status, $endDate, $startDate, $fkNutritionistId, $fkPatient) {
        if (empty($status) || empty($endDate) || empty($startDate) || empty($fkNutritionistId) || empty($fkPatient)) {
            throw new Exception('Todos os campos devem estar preenchidos');
        }
    }
} 