<?php
require_once __DIR__ . '/../config/Connection.php';

class AddressRepository {
    private $pdo;

    public function __construct($connection){
        $this->pdo = $connection;
    }

    public function insertAddress($address) {
        try {
            $query = $this->pdo->prepare(
                "INSERT INTO address (country, neighborhood, city, state, street, `number`, zip_code, fk_patient_id)
                 VALUES (:country, :neighborhood, :city, :state, :street, :number, :zip_code, :fkPatientId)"
            );

            return $query->execute([
                'country'      => $address->getCountry(), 
                'neighborhood' => $address->getNeighborhood(),
                'city'         => $address->getCity(),
                'state'        => $address->getState(),
                'street'       => $address->getStreet(),
                'number'       => $address->getNumber(),
                'zip_code'     => $address->getZipCode(),
                'fkPatientId'  => $address->getFkPatientId()
            ]);
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function updateAddress($address) {
        try {
            $query = $this->pdo->prepare(
                "UPDATE address
                 SET country = :country,
                     neighborhood = :neighborhood,
                     city = :city,
                     state = :state,
                     street = :street,
                     `number` = :number,
                     zip_code = :zip_code,
                     fk_patient_id = :fkPatientId
                 WHERE id_address = :id"
            );

            return $query->execute([
                'country'      => $address->getCountry(), 
                'neighborhood' => $address->getNeighborhood(),
                'city'         => $address->getCity(),
                'state'        => $address->getState(),
                'street'       => $address->getStreet(),
                'number'       => $address->getNumber(),
                'zip_code'     => $address->getZipCode(),
                'fkPatientId'  => $address->getFkPatientId(),
                'id'           => $address->getId()
            ]);
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function selectAddress($id) {
        try {
            $query = $this->pdo->prepare("SELECT * FROM address WHERE id_address = :id");
            $query->execute(['id' => $id]);
            return $query->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw $e;
        }
    }

    // não sei se vou usar
    public function selectAllAddresses() {
        try {
            $query = $this->pdo->prepare("SELECT * FROM address");
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function selectAddressByPatient($fkPatientId) {
        try {
            $query = $this->pdo->prepare("SELECT * FROM address WHERE fk_patient_id = :fkPatientId");
            $query->execute(['fkPatientId' => $fkPatientId]);
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function deleteAddress($id) {
        try {
            $query = $this->pdo->prepare("DELETE FROM address WHERE id_address = :id");
            return $query->execute(['id' => $id]);
        } catch (PDOException $e) {
            throw $e;
        }
    }
}
