<?php

require_once 'mysql_config.php';

class Role {

    private $roleId;
    private $roleName;
    private $permissions;

    public function __construct($roleId) {
        $this->roleId = $roleId;
        $this->roleName = ucfirst($this->FetchRoleName($roleId));
        $this->permissions = $this->FetchPermissions($roleId);
    }

    public static function GetRoleFromUserId($userId) {
        $connection = MysqlConfig::Connect();
        $statement = Role::CreateRoleStatement($connection, $userId);
        $statement->execute();

        $roleId = $statement->fetchColumn();
        return new Role($roleId);
    }
    
    public function GetRoleName(){
        return $this->roleName;
    }
    
    public function HasPermission($permissionName){
        return isset($this->permissions[$permissionName]) || isset($this->permissions["admin"]);
    }

    private function FetchRoleName() {
        $connection = MysqlConfig::Connect();
        $statement = $this->CreateRoleNameStatement($connection);
        $statement->execute();
        
        return $statement->fetchColumn();
    }

    private function FetchPermissions($roleId) {
        $connection = MysqlConfig::Connect();
        $statement = $this->CreatePermissionsStatement($connection);
        $statement->execute();
        
        $permissions = array();
        while($row = $statement->fetchColumn()){
            $permissions[$row] = true;
        }
        
        return $permissions;
    }

    private function CreateRoleNameStatement($connection) {
        $sql = "SELECT name FROM Roles WHERE id = :roleid LIMIT 1";
        $statement = $connection->prepare($sql);
        $statement->bindValue("roleid", $this->roleId, PDO::PARAM_STR);
        return $statement;
    }

    private static function CreateRoleStatement($connection, $userId) {
        $sql = "SELECT roleId FROM UserRoles WHERE userId = :userid LIMIT 1";
        $statement = $connection->prepare($sql);
        $statement->bindValue("userid", $userId, PDO::PARAM_STR);
        return $statement;
    }

    private function CreatePermissionsStatement($connection) {
        $sql = "SELECT p.name FROM RolePermissions rp INNER JOIN Permissions p ON rp.permissionId = p.id WHERE rp.roleId = :roleid";
        $statement = $connection->prepare($sql);
        $statement->bindValue("roleid", $this->roleId, PDO::PARAM_STR);
        return $statement;
    }

}
