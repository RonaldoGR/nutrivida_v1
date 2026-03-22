<?php

Class AddressModel {
    
   private $id;
   private $country;
   private $neighborhood;
   private $city;
   private $state;
   private $street;
   private $number;
   private $zip_code;
   private $fkPatientId;


    public function __construct() {}

    public function getId() {
        return $this->id;
    }

    public function getCountry() {
        return $this->country;
    }
    public function getNumber() {
        return $this->number;
    }

    public function getNeighborhood() {
        return $this->neighborhood;
    }

    public function getCity() {
        return $this->city;
    }

    public function getState() {
        return $this->state;
    }

    public function getStreet() {
        return $this->street;
    }

    public function getZipCode() {
        return $this->zip_code;
    }

    public function getFkPatientId() {
        return $this->fkPatientId;
    }


    public function setId($id) {
        $this->id = $id;
    }

    public function setCountry($country) {
        $this->country = $country;
    }

    public function setNeighborhood($neighborhood) {
        $this->neighborhood = $neighborhood;
    }

    public function setCity($city) {
        $this->city = $city;
    }

    public function setNumber($number) {
        $this->number = $number;
    }

    public function setState($state) {
        $this->state = $state;
    }

    public function setStreet($street) {
        $this->street = $street;
    }

    public function setZipCode($zip_code) {
        $this->zip_code = $zip_code;
    }

    public function setFkPatientId($fkPatientId) {
        $this->fkPatientId = $fkPatientId;
    }
}