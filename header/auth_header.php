<?php
require_once __DIR__.'/../auth/role.php';

if(session_status() == PHP_SESSION_NONE){
    session_start();
}

if (!isset($_SESSION['userid'])) {
    header("Location: index.php");
}

$userRole = Role::GetRoleFromUserId($_SESSION['userid']);