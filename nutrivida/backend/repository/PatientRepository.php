<?php 
require_once './config/Connection.php';
require_once '/../models/PatientModel.php';
require_once '/../models/AddressModel.php';

Class PatientRepository {
    private $pdo;

    public function __construct($connection){
        $this->pdo = $connection;
    }


    public function insertPatient (PatientModel $patient, AddressModel $address) {
         try {
            $this->pdo->beginTransaction();

            $query = $this->pdo->prepare("INSERT INTO patient (name, date_birth, cpf,photo, phone, email, password)
                                                 VALUES (:name, :dateBirth, :cpf, :photo, :phone, :email, :password)");
           $insertPatient = $query->execute([
                                       'name' => $patient->getName(),
                                       'date_birth' => $patient->getDateBirth(),
                                       'cpf' => $patient->getCpf(),
                                       'photo' => $patient->getPhoto(),
                                       'phone' => $patient->getPhone(),
                                       'email' => $patient->getEmail(),
                                       'password' => $patient->getPassword()
                                      ]);
            
          $newPatientId = $this->pdo->lastInsertId();
          
          $sqlAddress = "INSERT INTO address (country, neighborhood, city, state, street, number, zip_code, fk_patient_id) 
                           VALUES (:country, :neighborhood, :city, :state, :street, :number, :zip_code, :fk_patient_id)";
          
          $queryAddress = $this->pdo->prepare($sqlAddress);
          
          $insertAddress = $queryAddress->execute([
                'country'      => $address->getCountry(),  
                'neighborhood' => $address->getNeighborhood(),
                'city'         => $address->getCity(),
                'state'        => $address->getState(),
                'street'       => $address->getStreet(),
                'number'       => $address->getNumber(), 
                'zip_code'     => $address->getZipCode(),
                'fk_patient_id' =>$newPatientId // Aqui usamos o ID capturado
          ]);

          $this->pdo->commit();

          if($insertPatient && $insertAddress) {
              return true;
          }
        
        } catch(PDOException $e) {
            $this->pdo->rollBack();
             return false;
        }
    }


     public function updatePatient($patient) {
        try {
            $query = $this->pdo->prepare("UPDATE patient
                                          SET name = :name,
                                              date_birth = :dateBirth,
                                              cpf = :cpf,
                                              photo = :photo,
                                              phone = :phone,
                                              email = :email,
                                              password = :password
                                              WHERE id_patient = :idPatient");
            $update = $query->execute([
                'name' => $patient->getName(),
                'dateBirth' => $patient->getDateBirth(),
                'cpf' => $patient->getCpf(),
                'photo' => $patient->getPhoto(),
                'phone' => $patient->getPhone(),
                'email' => $patient->getEmail(),
                'password' => $patient->getPassword(),
                'idPatient' => $patient->getId()
            ]);
            return $update;
        } catch(PDOException $e) {
            throw $e;
        }
    }

     public function selectPatientByEmail($email) {
        try {
            $query = $this->pdo->prepare("SELECT * FROM patient WHERE email = :email");
            $executed = $query->execute(['email' => $email]);
            if($executed) return $query->fetch(PDO::FETCH_ASSOC);
            return false;
        } catch(PDOException $e) {
            throw $e;
        }
    }
     public function selectPatientById($id) {
        try {
            $query = $this->pdo->prepare("SELECT * FROM patient WHERE id_patient = :id");
            $executed = $query->execute(['id_patient' => $id]);
            if($executed) return $query->fetch(PDO::FETCH_ASSOC);
            return false;
        } catch(PDOException $e) {
            throw $e;
        }
    }

    public function selectAllPatients() {
        try {
            $query = $this->pdo->prepare("SELECT * FROM patient");
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            throw $e;
        }
    }

    public function selectPatientByName($name) {
        try {
            $query = $this->pdo->prepare("SELECT * FROM patient WHERE UPPER(name) LIKE :name");
            if($query->execute(['name' => $name])) {
                $patientList = $query->fetchAll(PDO::FETCH_ASSOC);
                if($patientList) return $patientList;
                return false;
            }
            return false;
        } catch(PDOException $e) {
            throw $e;
        }
    }

    public function deletePatient($id) {
        try {
            $query = $this->pdo->prepare("DELETE FROM patient WHERE id_patient = :id");
            return $query->execute(['id_patient' => $id]);
        } catch(PDOException $e) {
            throw $e;
        }
    }

}