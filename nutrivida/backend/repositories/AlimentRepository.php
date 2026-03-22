<?php
require_once __DIR__ . '/../config/Connection.php';
require_once __DIR__ . '/../models/AlimentModel.php';

class AlimentRepository {
    private $pdo;

    public function __construct($connection) {
        $this->pdo = $connection;
    }

    public function insertAliment($aliment) {
        try {
            $query = $this->pdo->prepare(
                "INSERT INTO aliment (description, quantity, calories) 
                 VALUES (:description, :quantity, :calories)"
            );
            return $query->execute([
                'description' => $aliment->getDescription(),
                'quantity'    => $aliment->getQuantity(), 
                'calories'    => $aliment->getCalories()
            ]);
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function updateAliment($aliment) {
        try {
            $query = $this->pdo->prepare(
                "UPDATE aliment 
                 SET description = :description, 
                     quantity = :quantity, 
                     calories = :calories 
                 WHERE id_aliment = :id"
            );
            return $query->execute([
                'description' => $aliment->getDescription(),
                'quantity'    => $aliment->getQuantity(),
                'calories'    => $aliment->getCalories(),
                'id'          => $aliment->getId()
            ]);
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function selectAliment($id) {
        try {
            $query = $this->pdo->prepare("SELECT * FROM aliment WHERE id_aliment = :id");
            $executed = $query->execute(['id' => $id]);
            if($executed) return $query->fetch(PDO::FETCH_ASSOC);
            return false;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function selectAllAliments() {
        try {
            $query = $this->pdo->prepare("SELECT * FROM aliment ORDER BY description ASC");
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw $e;
        }
    }
    

    public function searchAlimentByName($name) {
        try {
            $name = "%" . $name . "%";
            $query = $this->pdo->prepare("SELECT * FROM aliment WHERE description LIKE :name");
            $query->execute(['name' => $name]);
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function deleteAliment($id) {
         try {
            $query = $this->pdo->prepare("DELETE FROM aliment WHERE id_aliment = :id");
            return $query->execute(['id' => $id]);
        } catch (PDOException $e) {
            throw $e;
        }
    }
}