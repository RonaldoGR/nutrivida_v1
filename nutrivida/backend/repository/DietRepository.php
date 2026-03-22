<?php
require_once './config/Connection.php';


Class DietRepository {
    private $pdo;

    public function __construct($connection){
        $this->pdo = $connection;
    }


       public function insertDiet($diet) {
        try {
            $query = $this->pdo->prepare(
                "INSERT INTO diet (status, end_date, start_date, fk_nutritionist_id, fk_patient_id)
                 VALUES (:status, :endDate, :startDate, :fkNutritionistId, :fkPatientId)"
            );

            return $query->execute([
                'status' => $diet->getStatus(),
                'endDate' => $diet->getEndDate(),
                'startDate' => $diet->getStartDate(),
                'fkNutritionistId' => $diet->getFkNutritionistId(),
                'fkPatientId' => $diet->getFkPatientId()
            ]);
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function updateDiet($diet) {
        try {
            $query = $this->pdo->prepare(
                "UPDATE diet
                 SET status = :status,
                     end_date = :endDate,
                     start_date = :startDate,
                     fk_nutritionist_id = :fkNutritionistId,
                     fk_patient_id = :fkPatientId
                 WHERE id_diet = :idDiet"
            );

            return $query->execute([
                'status' => $diet->getStatus(),
                'end_date' => $diet->getEndDate(),
                'start_date' => $diet->getStartDate(),
                'fkNutritionistId' => $diet->getFkNutritionistId(),
                'fkPatientId' => $diet->getFkPatientId(),
                'idDiet' => $diet->getId()
            ]);
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function selectDiet($id) {
        try {
            $query = $this->pdo->prepare("SELECT * FROM diet WHERE id = :id");
            $executed = $query->execute(['id_diet' => $id]);
            if ($executed) return $query->fetch(PDO::FETCH_ASSOC);
            return false;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function selectAllDiets() {
        try {
            $query = $this->pdo->prepare("SELECT * FROM diet");
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function selectDietByStatus($status) {
        try {
            $query = $this->pdo->prepare("SELECT * FROM diet WHERE UPPER(status) LIKE :status");
            if ($query->execute(['status' => $status])) {
                $list = $query->fetchAll(PDO::FETCH_ASSOC);
                if ($list) return $list;
                return false;
            }
            return false;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function deleteDiet($id) {
        try {
            $query = $this->pdo->prepare("DELETE FROM diet WHERE id_diet = :id");
            return $query->execute(['id_diet' => $id]);
        } catch (PDOException $e) {
            throw $e;
        }
    }

}