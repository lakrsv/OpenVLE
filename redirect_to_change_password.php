<?php

require_once 'header/auth_header.php';
require_once 'auth/change_password.php';

$userId = filter_input(INPUT_GET, "userId", FILTER_SANITIZE_NUMBER_INT);
if (!$userId) {
    if (!isset($_SESSION["userId"])) {
        session_destroy();
        echo("uId not set");
        die();
    }
    $userId = $_SESSION["userId"];
}

$canChangePassword = false;
if (!$userRole->HasPermission("manage_users") && $userId != $_SESSION["userid"]) {
    session_destroy();
    echo("Not permitted");
    die();
}

$url = ChangePassword::GetResetPasswordLink($userId);
echo ($url);
header("Location: " . $url);

