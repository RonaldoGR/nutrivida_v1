<?php
require_once __DIR__ . '/../repositories/AddressRepository.php';
require_once __DIR__ . '/../models/AddressModel.php';

class AddressService {
    private $addressRepository;

    public function __construct($addressRepository){
        $this->addressRepository = $addressRepository;
    }

    public function registerService($country, $neighborhood, $city, $state, $street, $number, $zip_code, $fkPatientId) {

        $this->validate($country, $neighborhood, $city, $state, $street, $number, $zip_code, $fkPatientId);

        $address = new AddressModel();
        $address->setCountry($country);
        $address->setNeighborhood($neighborhood);
        $address->setCity($city);
        $address->setState($state);
        $address->setStreet($street);
        $address->setNumber($number);
        $address->setZipCode($zip_code);
        $address->setFkPatientId($fkPatientId);

        return $this->addressRepository->insertAddress($address);
    }

    public function updateService($id,$country, $neighborhood, $city, $state, $street, $number, $zip_code, $fkPatientId) {

        $address = new AddressModel();
        $address->setId($id);
        $address->setCountry($country);
        $address->setNeighborhood($neighborhood);
        $address->setCity($city);
        $address->setState($state);
        $address->setStreet($street);
        $address->setNumber($number);
        $address->setZipCode($zip_code);
        $address->setFkPatientId($fkPatientId);

        return $this->addressRepository->updateAddress($address);
    }


    public function saveAddressService($data, $patientId) {
        $address = new AddressModel();
        $address->setCountry($data['country']);
        $address->setNeighborhood($data['neighborhood']);
        $address->setCity($data['city']);
        $address->setState($data['state']);
        $address->setStreet($data['street']);
        $address->setNumber($data['number']);
        $address->setZipCode($data['zip_code']);
        $address->setFkPatientId($patientId);

        if (!empty($data['id_address'])) {
            $address->setId($data['id_address']);
            return $this->addressRepository->updateAddress($address);
        } else {
            return $this->addressRepository->insertAddress($address); 
        }
    }

    public function deleteAddressService($id) {
        return $this->addressRepository->deleteAddress($id);
    }

    public function getAddressService($id) {
        return $this->addressRepository->selectAddress($id);
    }

    public function getAllAddressesService() {
        return $this->addressRepository->selectAllAddresses();
    }

    public function getAddressByPatientService($fkPatientId) {
        return $this->addressRepository->selectAddressByPatient($fkPatientId);
    }

    private function validate($country, $neighborhood, $city, $state, $street, $number, $zip_code, $fkPatientId) {
        if (
            empty($neighborhood) || empty($city) || empty($state) ||
            empty($street) || empty($zip_code) || empty($fkPatientId) ||
            empty($number) || empty($country)
        ) {
            throw new Exception('Todos os campos devem estar preenchidos');
        }
    }
}