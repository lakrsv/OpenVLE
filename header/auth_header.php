<?php
require_once __DIR__.'/../auth/role.php';

if(session_status() == PHP_SESSION_NONE){
    session_start();
}

if (!isset($_SESSION['userid'])) {
    session_destroy();
    header("Location: index.php");
    die();
}

$userRole = Role::GetRoleFromUserId($_SESSION['userid']);