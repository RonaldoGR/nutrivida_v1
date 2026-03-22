<?php 
require_once __DIR__ . '/../config/Connection.php';


Class NutritionistRepository {
    private $pdo;

    public function __construct($connection){
        $this->pdo = $connection;
    }


    public function insertNutritionist($nutritionist) {
        try {
            $query = $this->pdo->prepare("INSERT INTO nutritionist (name, cpf, crn, email, photo, password)
                                                 VALUES (:name, :cpf, :crn, :email, :photo, :password)");
            $insert = $query->execute([
                                       'name' => $nutritionist->getName(),
                                       'cpf' => $nutritionist->getCpf(),
                                       'crn' => $nutritionist->getCrn(),
                                       'email' => $nutritionist->getEmail(),
                                       'photo' => $nutritionist->getPhoto(),
                                       'password' => $nutritionist->getPassword()
                                      ]);

            return $insert;                            
        } catch(PDOException $e) {
           throw $e;
        }
    }

      public function updateNutritionist($nutritionist){

        try {
            $query = $this->pdo->prepare("UPDATE nutritionist
                                          SET name = :name,
                                          cpf = :cpf,
                                          crn = :crn,
                                          email = :email,
                                          photo = :photo,
                                          password = :password
                                          WHERE id_nutritionist = :id");

            $update = $query->execute([
                'name' => $nutritionist->getName(),
                'cpf' => $nutritionist->getCpf(),
                'crn' => $nutritionist->getCrn(),
                'email' => $nutritionist->getEmail(),
                'photo' => $nutritionist->getPhoto(),
                'password' => $nutritionist->getPassword(),
                'id' => $nutritionist->getId()
            ]);
            
            return $update;

        } catch (PDOException $e) {
            throw $e;
            
        }

    }

      public function selectNutritionist($id) {
        try {
           $query = $this->pdo->prepare("SELECT * FROM nutritionist WHERE id_nutritionist = :id");
           $executed = $query->execute(['id' => $id]);
           if($executed) return $query->fetch(PDO::FETCH_ASSOC);
           return false; 
        } catch (PDOException $e) {
            throw $e;
        }
    }

      public function selectAllNutritionist() {
        try {
            $query = $this->pdo->prepare("SELECT * FROM nutritionist");
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC);  

        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function selectNutritionistByEmail($email) {
        try {
            $query = $this->pdo->prepare("SELECT * FROM nutritionist WHERE email = :email");
            $query->execute(['email' => $email]);
            return $query->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw $e;
        }
    }
    
    public function selectNutritionistByName($name) {
           try {
            $query = $this->pdo->prepare("SELECT * FROM nutritionist
                                         WHERE UPPER(name) LIKE :name");

            if($query->execute(['name' => $name])) {
                $nutritionistList = $query->fetchAll(PDO::FETCH_ASSOC);
                if($nutritionistList) return $nutritionistList;
                return false;
            }
            return false; 
        } catch (PDOException $e) {
            throw $e;
        }
    }


    public function deleteNutritionist($id) {
         try {
            $query = $this->pdo->prepare("DELETE FROM nutritionist WHERE id_nutritionist = :id");
            return $query->execute(['id' => $id]);
        } catch (PDOException $e) {
            throw $e;
        }
    }
}