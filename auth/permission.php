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
    
    public static function AddPermissionToRoleWithId($roleId, $permissionId){
        $connection = MysqlConfig::Connect();
        $sql = "INSERT INTO RolePermissions (roleId, permissionId) VALUES (:roleid, :permissionid)";
        $statement = $connection->prepare($sql);
        $statement->bindValue("roleid", $roleId);
        $statement->bindValue("permissionid", $permissionId);
        $statement->execute();
    }
    
    public static function RemovePermissionFromRoleWithId($roleId, $permissionId){
        $connection = MysqlConfig::Connect();
        $sql = "DELETE FROM RolePermissions WHERE roleId = :roleid AND permissionId = :permissionid;";
        $statement = $connection->prepare($sql);
        $statement->bindValue("roleid", $roleId, PDO::PARAM_STR);
        $statement->bindValue("permissionid", $permissionId, PDO::PARAM_STR);
        $statement->execute();
    }
    
    public static function GetPermissionNameFromId($permissionId){
        $connection = MysqlConfig::Connect();
        $sql = "SELECT name FROM Permissions WHERE id = :permissionid LIMIT 1";
        $statement = $connection->prepare($sql);
        $statement->bindValue("permissionid", $permissionId, PDO::PARAM_STR);
        $statement->execute();

        return $statement->fetchColumn();
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
