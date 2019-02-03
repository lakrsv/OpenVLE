<?php
require_once 'header/auth_header.php';

echo "Welcome ".$_SESSION['username']."!";
echo "\nYour role is ".$role->GetRoleName();

if($role->HasPermission("admin")){
    echo "You have admin permissions!";
}
