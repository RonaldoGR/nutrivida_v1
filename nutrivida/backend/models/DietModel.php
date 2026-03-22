<?php

Class DietModel {
    private $id;
    private $status;
    private $endDate;
    private $startDate;
    private $fkNutritionistId;
    private $fkPatientId;

    public function __construct(){}

    public function getId() {
        return $this->id;
    }

    public function getStatus() {
        return $this->status;
    }

    public function getEndDate() {
        return $this->endDate;
    }

    public function getStartDate() {
        return $this->startDate;
    }

    public function getFkNutritionistId() {
        return $this->fkNutritionistId;
    }

    public function getFkPatientId() {
         return $this->fkPatientId;
    }


    public function setId($id) {
        $this->id = $id;
    }

    public function setStatus($status) {
        $this->status = $status;
    }

    public function setEndDate($endDate) {
        $this->endDate = $endDate;
    }

    public function setStartDate($startDate) {
        $this->startDate = $startDate;
    }

    public function setFkNutritionistId($fkNutritionistId) {
        $this->fkNutritionistId = $fkNutritionistId;
    }

    public function setFkPatientId($id) {
         $this->fkPatientId = $id; 
    }

}