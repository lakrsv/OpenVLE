<?php

require_once 'header/auth_header.php';

if ($userRole->GetName() == "admin") {
    header("Location: manage_users.php");
}
