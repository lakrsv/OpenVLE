<?php

require_once __DIR__ . '/../auth/mysql_config.php';
require_once __DIR__ . '/../auth/role.php';
require_once __DIR__ . '/../header/auth_header.php';
require_once __DIR__ . '/../auth/permission.php';

if (!isset($_POST['roleId'], $_POST['permissionId'], $_POST['action'])) {
    exit("Post params not set");
}

if (!$userRole->HasPermission("manage_roles")) {
    header("Location: user-home.php");
}

$roleId = $_POST['roleId'];
$permissionId = $_POST['permissionId'];
$action = $_POST['action'];

switch ($action) {
    case 'remove':
        return TryRemovePermission($roleId, $permissionId);
    case 'add':
        return TryAddPermission($roleId, $permissionId);
}

function TryRemovePermission($roleId, $permissionId) {
    $response = array();
    $role = new Role($roleId);
    $hasPermission = $role->HasPermission(Permission::GetPermissionNameFromId($permissionId));
    if (!$hasPermission) {
        $response['success'] = FALSE;
        $response['message'] = "Can't remove permission as role doesn't have it";
        echo json_encode($response);
        return FALSE;
    } else {
        Permission::RemovePermissionFromRoleWithId($roleId, $permissionId);
        $response['success'] = TRUE;
        $response['message'] = "Successfully changed permission for role " . $role->GetName();
        echo json_encode($response);
        return TRUE;
    }
}

function TryAddPermission($roleId, $permissionId) {
    $response = array();
    $role = new Role($roleId);
    $hasPermission = $role->HasPermission(Permission::GetPermissionNameFromId($permissionId));
    if ($hasPermission) {
        $response['success'] = FALSE;
        $response['message'] = "Can't add permission as role already has it";
        echo json_encode($response);
        return FALSE;
    } else {
        Permission::AddPermissionToRoleWithId($roleId, $permissionId);
        $response['success'] = TRUE;
        $response['message'] = "Successfully changed permission for role " . $role->GetName();
        echo json_encode($response);
        return TRUE;
    }
}
