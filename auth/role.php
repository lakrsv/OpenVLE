<?php

require_once 'mysql_config.php';

class Role {

    private $id;
    private $name;
    private $permissions;

    public function __construct($roleId) {
        $this->id = $roleId;
        $this->name = $this->FetchRoleName($roleId);
        $this->permissions = $this->FetchPermissions($roleId);
    }

    public static function AddRoleWithname($roleName) {
        $connection = MysqlConfig::Connect();
        $sql = "INSERT INTO Roles (name) VALUES (:name)";
        $statement = $connection->prepare($sql);
        $statement->bindValue("name", $roleName);
        $statement->execute();
    }

    public static function AnyRoleHasName($roleName) {
        $connection = MysqlConfig::Connect();
        $sql = "SELECT id FROM Roles WHERE name = :name LIMIT 1";
        $statement = $connection->prepare($sql);
        $statement->bindValue("name", $roleName);
        $statement->execute();

        return count($statement->fetchAll()) > 0 ? TRUE : FALSE;
    }

    public static function AnyUserHasRoleWithId($roleId) {
        $connection = MysqlConfig::Connect();
        $sql = "SELECT id FROM UserRoles WHERE roleId = :roleid LIMIT 1";
        $statement = $connection->prepare($sql);
        $statement->bindValue("roleid", $roleId);
        $statement->execute();

        return count($statement->fetchAll()) > 0 ? TRUE : FALSE;
    }

    public static function DeleteRoleWithId($roleId) {
        $connection = MysqlConfig::Connect();
        $sql = "DELETE FROM Roles WHERE id = :roleid";
        $statement = $connection->prepare($sql);
        $statement->bindValue("roleid", $roleId, PDO::PARAM_STR);
        $statement->execute();
    }

    public static function GetRoleFromUserId($userId) {
        $connection = MysqlConfig::Connect();
        $sql = "SELECT roleId FROM UserRoles WHERE userId = :userid LIMIT 1";
        $statement = $connection->prepare($sql);
        $statement->bindValue("userid", $userId, PDO::PARAM_STR);
        $statement->execute();

        $roleId = $statement->fetchColumn();
        return new Role($roleId);
    }

    public static function GetRoleIdFromRoleName($roleName) {
        $connection = MysqlConfig::Connect();
        $sql = "SELECT id FROM Roles WHERE name = :name LIMIT 1";
        $statement = $connection->prepare($sql);
        $statement->bindValue("name", $roleName, PDO::PARAM_STR);
        $statement->execute();

        return $statement->fetchColumn();
    }

    public static function GetAll() {
        $connection = MysqlConfig::Connect();
        $sql = "SELECT id FROM Roles";
        $statement = $connection->prepare($sql);
        $statement->execute();

        $roles = array();
        while ($row = $statement->fetchColumn()) {
            array_push($roles, new Role($row));
        }

        return $roles;
    }

    public function GetId() {
        return $this->id;
    }

    public function GetName() {
        return $this->name;
    }

    public function HasPermission($permissionName) {
        return isset($this->permissions[$permissionName]) || isset($this->permissions["admin"]);
    }

    private function FetchRoleName() {
        $connection = MysqlConfig::Connect();
        $sql = "SELECT name FROM Roles WHERE id = :roleid LIMIT 1";
        $statement = $connection->prepare($sql);
        $statement->bindValue("roleid", $this->id, PDO::PARAM_STR);
        $statement->execute();

        return $statement->fetchColumn();
    }

    private function FetchPermissions($roleId) {
        $connection = MysqlConfig::Connect();
        $sql = "SELECT p.name FROM RolePermissions rp INNER JOIN Permissions p ON rp.permissionId = p.id WHERE rp.roleId = :roleid";
        $statement = $connection->prepare($sql);
        $statement->bindValue("roleid", $this->id, PDO::PARAM_STR);
        $statement->execute();

        $permissions = array();
        while ($row = $statement->fetchColumn()) {
            $permissions[$row] = true;
        }

        return $permissions;
    }

}
