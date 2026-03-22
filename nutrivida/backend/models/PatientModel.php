<?php

Class PatientModel {
    private $id;
    private $name;
    private $dateBirth;
    private $cpf;
    private $photo;
    private $phone;
    private $email;
    private $password;

    public function __construct() {}

      public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getDateBirth() {
        return $this->dateBirth;
    }

    public function getCpf() {
        return $this->cpf;
    }

    public function getPhoto() {
        return $this->photo;
    }

    public function getPhone() {
        return $this->phone;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getPassword() {
        return $this->password;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function setDateBirth($dateBirth) {
        $this->dateBirth = $dateBirth;
    }

    public function setCpf($cpf) {
        $this->cpf = $cpf;
    }

    public function setPhoto($photo) {
        $this->photo = $photo;
    }

    public function setPhone($phone) {
        $this->phone = $phone;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function setPassword($password) {
        $this->password = $password;
    }

}