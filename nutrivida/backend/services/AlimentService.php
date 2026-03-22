<?php
require_once __DIR__ . '/../models/AlimentModel.php';
require_once __DIR__ . '/../repositories/AlimentRepository.php';


class AlimentService {
    private $alimentRepository;

    public function __construct($alimentRepository){
        $this->alimentRepository = $alimentRepository;
    }


    public function registerService($description, $quantity, $calories) {
        $this->validate($description, $quantity, $calories);

        $aliment = new AlimentModel();
        $aliment->setDescription($description);
        $aliment->setQuantity($quantity);
        $aliment->setCalories($calories);

        return $this->alimentRepository->insertAliment($aliment);
    }


    public function updateService($id, $description, $quantity, $calories) {
        $this->validate($description, $quantity, $calories);

        $aliment = new AlimentModel();
        $aliment->setId($id);
        $aliment->setDescription($description);
        $aliment->setQuantity($quantity);
        $aliment->setCalories($calories);

        return $this->alimentRepository->updateAliment($aliment);
    }

    public function selectAlimentService ($id) {
        return $this->alimentRepository->selectAliment($id);
    }

    public function deleteService ($id) {
        return $this->alimentRepository->deleteAliment($id);
    }

    public function getAllAlimentsService() {
        return $this->alimentRepository->selectAllAliments();
    }

    public function searchAlimentByNameService ($name) {
        return $this->alimentRepository->searchAlimentByName($name);
    }

    private function validate($description, $quantity, $calories) {
        if (empty($description) || empty($quantity) || empty($calories)) {
            throw new Exception("Todos os campos (descrição, porção e calorias) são obrigatórios.");
        }
    }
}