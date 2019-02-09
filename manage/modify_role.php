<?php
require_once __DIR__.'/../auth/mysql_config.php';
require_once __DIR__.'/../auth/role.php';
require_once __DIR__.'/../header/auth_header.php';

if(!isset($_POST['action'], $_POST['role'])){
    exit("Post params not set");
}

if (!$userRole->HasPermission("manage_roles")) {
    header("Location: user-home.php");
}

$role = $_POST['role'];
$action = $_POST['action'];

switch ($action){
    case 'delete':
        return TryDeleteRole($role);
    case 'add':
        return TryAddRole($role);
}

function TryDeleteRole($roleId){
    $response = array();
    if(Role::AnyUserHasRoleWithId($roleId)){
        $response['success'] = FALSE;
        $response['message'] = "Can't delete role as users are currently assigned to it";
        echo json_encode($response);
        return FALSE;
    }
    else{
        Role::DeleteRoleWithId($roleId);      
        $response['success'] = TRUE;
        $response['message'] = "Successfully deleted role";
        echo json_encode($response);
        return TRUE;
    }
}

function TryAddRole($roleName){
    $response = array();
    if(Role::AnyRoleHasName($roleName)){
        $response['success'] = FALSE;
        $response['message'] = "Can't add role as a role with this name already exists";
        echo json_encode($response);
        return FALSE;
    }
    else{
        Role::AddRoleWithName($roleName);
        $response['success'] = TRUE;
        $response['message'] = "Successfully added role";
        echo json_encode($response);
        return TRUE;
    }  
}