<?php
require_once 'header/auth_header.php';
require_once 'auth/login.php';

// TODO - Change this to allow user to change their profile
if (!$userRole->HasPermission("manage_users")) {
    header("Location: user-home.php");
}

if(!isset($_GET['id'])){
    exit("Get params not set");
}

$userId = $_GET['id'];
echo "Welcome ". User::GetUserNameFromId($userId) ."!";
?>