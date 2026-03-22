<?php
require_once __DIR__ . '/../config/Connection.php';
require_once __DIR__ . '/../models/MealModel.php';

class MealRepository {
    private $pdo;

    public function __construct($connection) {
        $this->pdo = $connection;
    }

    public function insertMeal($meal) {
        try {
            $query = $this->pdo->prepare(
                "INSERT INTO meal (type, description, photo, fk_diet_id) 
                 VALUES (:type, :description, :photo, :fkDietId)"
            );
            $result = $query->execute([
                'type'        => $meal->getType(),
                'description' => $meal->getDescription(),
                'photo'       => $meal->getPhoto(),  
                'fkDietId'    => $meal->getFkDietId()
            ]);
            if($result) {
                return $this->pdo->lastInsertId();
            }
            return false;

        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function updateMeal($meal) {
        try {
            $query = $this->pdo->prepare(
                "UPDATE meal 
                 SET type = :type, 
                     description = :description,
                     photo = :photo 
                 WHERE id_meal = :id"
            );
            return $query->execute([
                'type'        => $meal->getType(),
                'description' => $meal->getDescription(),
                'photo'       => $meal->getPhoto(),
                'id'          => $meal->getId()
            ]);
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function selectMeal($id) {
        try {
            $query = $this->pdo->prepare("SELECT * FROM meal WHERE id_meal = :id");
            $query->execute(['id' => $id]);
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function selectDietsByPatientId($fkPatientId) {
        try {
            $query = $this->pdo->prepare("SELECT * FROM diet WHERE fk_patient_id = :patientId");
            $query->execute(['patientId' => $fkPatientId]);
            
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw $e;
        }
    }


    public function selectAllMeals () {
        try {
            $query = $this->pdo->prepare("SELECT m.*, p.name AS patient_name, d.status AS diet_status
                                          FROM meal m
                                          INNER JOIN diet d
                                            ON m.fk_diet_id = d.id_diet
                                          INNER JOIN patient p
                                            ON d.fk_patient_id = p.id_patient 
                                          ORDER BY m.id_meal DESC 
                                        ");
            if($query->execute()){
                return $query->fetchAll(PDO::FETCH_ASSOC);
            }
            return [];
        } catch (PDOException $e) {
            throw $e;
        } 
    }

    public function selectMealsByDietId($dietId) {
        try {
            $query = $this->pdo->prepare("SELECT * FROM meal WHERE fk_diet_id = :dietId");
            $query->execute(['dietId' => $dietId]);
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw $e;
        }
    }
    
    public function deleteMeal($id) {
         try {
            $query = $this->pdo->prepare("DELETE FROM meal WHERE id_meal = :id");
            return $query->execute(['id' => $id]);
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function getConnection() {
         return $this->pdo;
    }
}