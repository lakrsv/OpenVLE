<?php

class Permission {

    private $id;
    private $name;
    private $description;

    public function __construct($id, $name, $description) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
    }

    public function GetId() {
        return $this->id;
    }

    public function GetName() {
        return $this->name;
    }

    public function GetDescription() {
        return $this->description;
    }

    public static function GetAll() {
        $connection = MysqlConfig::Connect();
        $sql = "SELECT * FROM Permissions";
        $statement = $connection->prepare($sql);
        $statement->execute();

        $permissions = array();
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            array_push($permissions, new Permission($row['id'], $row['name'], $row['description']));
        }

        return $permissions;
    }

}
