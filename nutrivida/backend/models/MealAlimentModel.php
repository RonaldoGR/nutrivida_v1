<?php

class MealAlimentModel {
    private $fkMealId;      
    private $fkAlimentId;   
    private $quantity;       

    // Opcional: guardar o objeto Aliment aqui dentro para facilitar o acesso ao nome/calorias
    // private $alimentData; 

    public function __construct() {}

    public function getFkMealId() {
        return $this->fkMealId;
    }

    public function setFkMealId($fkMealId) {
        $this->fkMealId = $fkMealId;
    }

    public function getFkAlimentId() {
        return $this->fkAlimentId;
    }

    public function setFkAlimentId($fkAlimentId) {
        $this->fkAlimentId = $fkAlimentId;
    }

    public function getQuantity() {
        return $this->quantity;
    }

    public function setQuantity($quantity) {
        $this->quantity = $quantity;
    }
}