<?php
require_once 'header/auth_header.php';

if($role->GetRoleName() == "admin"){
    header("Location: manage_users.php");
}
