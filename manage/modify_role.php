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

$roleId = $_POST['role'];
$action = $_POST['action'];

switch ($action){
    case 'delete':
        echo "Deleting role ".$roleId;
        break;
}