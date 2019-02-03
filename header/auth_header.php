<?php
require 'auth/role.php';

if (!isset($_SESSION)) {
    session_start();
}
if (!isset($_SESSION['userid'])) {
    header("Location: index.php");
}

$role = Role::GetRoleFromUserId($_SESSION['userid']);