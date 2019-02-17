<?php

require_once __DIR__ . '/../auth/mysql_config.php';

class ContactDetails {

    private $userId;
    private $addressLine1;
    private $addressLine2;
    private $city;
    private $zip;
    private $number;

    public function __construct($userId, $addressLine1, $addressLine2, $city, $zip, $number) {
        $this->userId = $userId;
        $this->addressLine1 = $addressLine1;
        $this->addressLine2 = $addressLine2;
        $this->city = $city;
        $this->zip = $zip;
        $this->number = $number;
    }

    public function getUserId() {
        return $this->userId;
    }

    public function getAddressLine1() {
        return $this->addressLine1;
    }

    public function getAddressLine2() {
        return $this->addressLine2;
    }

    public function getCity() {
        return $this->city;
    }

    public function getZip() {
        return $this->zip;
    }

    public function getNumber() {
        return $this->number;
    }

    public static function GetContactDetailsForUser($userId) {
        $connection = MysqlConfig::Connect();
        $sql = "SELECT * FROM UserContacts WHERE userId = :userid LIMIT 1;";
        $statement = $connection->prepare($sql);
        $statement->bindValue("userid", $userId, PDO::PARAM_STR);
        $statement->execute();

        $row = $statement->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return NULL;
        }

        return new ContactDetails($row['userId'], $row['addressLine1'], $row['addressLine2'], $row['city'], $row['zip'], $row['number']);
    }

    public static function HasContactDetailsForUser($userId) {
        $connection = MysqlConfig::Connect();
        $sql = "SELECT userId FROM UserContacts WHERE userId = :userid LIMIT 1";
        $statement = $connection->prepare($sql);
        $statement->bindValue("userid", $userId);
        $statement->execute();

        return count($statement->fetchAll()) > 0 ? TRUE : FALSE;
    }

    public static function AddContactDetailsForUser($userId, $addressLine1, $addressLine2, $city, $zip, $number) {
        if (ContactDetails::HasContactDetailsForUser($userId)) {
            ContactDetails::UpdateContactDetails($userId, $addressLine1, $addressLine2, $city, $zip, $number);
        } else {
            ContactDetails::CreateContactDetails($userId, $addressLine1, $addressLine2, $city, $zip, $number);
        }
    }

    private static function UpdateContactDetails($userId, $addressLine1, $addressLine2, $city, $zip, $number) {
        $connection = MysqlConfig::Connect();
        $sql = "UPDATE UserContacts SET addressLine1=:addressline1, addressLine2=:addressline2, city=:city, zip=:zip, number=:number WHERE userId=:userid";
        $statement = $connection->prepare($sql);
        $statement->bindValue("userid", $userId, PDO::PARAM_STR);
        $statement->bindValue("addressline1", $addressLine1, PDO::PARAM_STR);
        $statement->bindValue("addressline2", $addressLine2, PDO::PARAM_STR);
        $statement->bindValue("city", $city, PDO::PARAM_STR);
        $statement->bindValue("zip", $zip, PDO::PARAM_STR);
        $statement->bindValue("number", $number, PDO::PARAM_STR);

        $statement->execute();
    }

    private static function CreateContactDetails($userId, $addressLine1, $addressLine2, $city, $zip, $number) {
        $connection = MysqlConfig::Connect();
        $sql = "INSERT INTO UserContacts (userId, addressLine1, addressLine2, city, zip, number) VALUES (:userid, :addressline1, :addressline2, :city, :zip, :number)";
        $statement = $connection->prepare($sql);
        $statement->bindValue("userid", $userId, PDO::PARAM_STR);
        $statement->bindValue("addressline1", $addressLine1, PDO::PARAM_STR);
        $statement->bindValue("addressline2", $addressLine2, PDO::PARAM_STR);
        $statement->bindValue("city", $city, PDO::PARAM_STR);
        $statement->bindValue("zip", $zip, PDO::PARAM_STR);
        $statement->bindValue("number", $number, PDO::PARAM_STR);

        $statement->execute();
    }

}
