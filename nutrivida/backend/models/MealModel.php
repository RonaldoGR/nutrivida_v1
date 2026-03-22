<?php

class MealModel {
    private $id;
    private $type;        
    private $description;
    private $photo; 
    private $fkDietId;    
    
    private $aliments = []; 

    public function __construct() {}

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getType() {
        return $this->type;
    }

    public function setType($type) {
        $this->type = $type;
    }

    public function getDescription() {
        return $this->description;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

    public function getPhoto() {
        return $this->photo;
    }

    public function setPhoto($photo) {
        $this->photo = $photo;
    }


    public function getFkDietId() {
        return $this->fkDietId;
    }

    public function setFkDietId($fkDietId) {
        $this->fkDietId = $fkDietId;
    }


    public function getAliments() {
        return $this->aliments;
    }

    public function setAliments(array $aliments) {
        $this->aliments = $aliments;
    }
    
    public function addAliment($aliment) {
        $this->aliments[] = $aliment;
    }
}