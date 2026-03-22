<?php
require_once __DIR__ . '/../config/Connection.php';
require_once __DIR__ . '/../models/MealAlimentModel.php';

class MealAlimentRepository {
    private $pdo;

    public function __construct($connection) {
        $this->pdo = $connection;
    }

    public function insertMealAliment($mealAliment) {
        try {
            $query = $this->pdo->prepare(
                "INSERT INTO meal_aliment (fk_meal_id, fk_aliment_id, aliment_quantity) 
                 VALUES (:fkMealId, :fkAlimentId, :quantity)"
            );
            return $query->execute([
                'fkMealId'    => $mealAliment->getFkMealId(),
                'fkAlimentId' => $mealAliment->getFkAlimentId(),
                'quantity'    => $mealAliment->getQuantity() 
            ]);
        } catch (PDOException $e) {
            throw $e;
        }
    }


    public function updateMealAlimentQuantity($mealAliment) {
        try {
            $query = $this->pdo->prepare(
                "UPDATE meal_aliment 
                 SET quantity = :quantity 
                 WHERE fk_meal_id = :fkMealId AND fk_aliment_id = :fkAlimentId"
            );
            return $query->execute([
                'quantity'    => $mealAliment->getQuantity(),
                'fkMealId'    => $mealAliment->getFkMealId(),
                'fkAlimentId' => $mealAliment->getFkAlimentId()
            ]);
        } catch (PDOException $e) {
            throw $e;
        }
    }


    public function deleteMealAliment($fkMealId, $fkAlimentId) {
        try {
            $query = $this->pdo->prepare(
                "DELETE FROM meal_aliment 
                 WHERE fk_meal_id = :fkMealId AND fk_aliment_id = :fkAlimentId"
            );
            return $query->execute([
                'fkMealId'    => $fkMealId,
                'fkAlimentId' => $fkAlimentId
            ]);
        } catch (PDOException $e) {
            throw $e;
        }
    }

    // todos os alimentos de uma refeição 
   public function selectAlimentsByMealId($mealId) {
        try {
            $sql = "SELECT ma.fk_meal_id, 
                           ma.aliment_quantity, 
                           a.id_aliment, 
                           a.description, 
                           a.calories, 
                           a.quantity
                    FROM meal_aliment ma
                    INNER JOIN aliment a ON ma.fk_aliment_id = a.id_aliment
                    WHERE ma.fk_meal_id = :mealId";
            
            $query = $this->pdo->prepare($sql);
            $query->execute(['mealId' => $mealId]);
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw $e;
        }
    }


    public function deleteAllAlimentsByMealId($mealId) {
        try {
            $query = $this->pdo->prepare("DELETE FROM meal_aliment
                                         WHERE fk_meal_id = :mealId");
            return $query->execute(['mealId' => $mealId]);
        } catch (PDOException $e) {
            throw $e;
        }
    }
}