<?php

require_once __DIR__ . '/../auth/mysql_config.php';
require_once __DIR__ . '/../auth/role.php';
require_once __DIR__ . '/../header/auth_header.php';
require_once __DIR__ . '/../auth/login.php';

if (!$userRole->HasPermission("manage_profile")) {
    header("Location: user-home.php");
}

$userId = filter_input(INPUT_POST, "userid", FILTER_SANITIZE_NUMBER_INT);
if (!$userId) {
    exit("Post params not set");
}

// Security check.
if (!$userRole->HasPermission("manage_users")) {
    $userId = $_SESSION["userid"];
}

TryChangeProfile($userId);

function TryChangeProfile($userId) {

    $response = array();

    if (HasFile()) {
        $response = TryChangePicture($userId);
        if (!$response["success"]) {
            echo json_encode($response);
            return FALSE;
        }
    }


    $name = filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING);
    if ($name) {
        $response = TryChangeName($userId, $name);
        if (!$response["success"]) {
            echo json_encode($response);
            return FALSE;
        }
    }

    $response["success"] = TRUE;
    $response["message"] = "Successfully updated profile";
    echo json_encode($response);
    return TRUE;
}

function TryChangePicture($userId) {
    $uploadDirectory = __DIR__ . "/../uploads/profile_pictures/";
    $allowedExtensions = [IMAGETYPE_JPEG, IMAGETYPE_PNG];
    $maxImageSize = 2000000;

    $file = $_FILES["file"];
    $imageType = exif_imagetype($file["tmp_name"]);

    $uploadPath = $uploadDirectory . basename($userId) . image_type_to_extension($imageType);

    $response = array();

    $isImage = getimagesize($file["tmp_name"]);
    if (!$isImage || !in_array($imageType, $allowedExtensions)) {
        $response['success'] = FALSE;
        $response['message'] = "Can't upload file, not a valid image (Valid Extensions are JPG and PNG)";
        return $response;
    }

    if ($file["size"] > $maxImageSize) {
        $response['success'] = FALSE;
        $response['message'] = "This image is too large. Max 2 MB";
        return $response;
    }

    if (file_exists($uploadPath)) {
        chmod($uploadPath, 0755);
    }

    if (move_uploaded_file($file["tmp_name"], $uploadPath)) {
        $response['success'] = TRUE;
        $response['message'] = "Image successfully uploaded!";

        return $response;
    } else {
        $response['success'] = FALSE;
        $response['message'] = "An error occured while trying to upload the image";
        echo json_encode($response);

        return $response;
    }
}

function TryChangeName($userId, $newName) {
    $response = array();
    if ($newName && strlen($newName) > 1 && IsValidName($newName)) {
        if (User::UserWithIdExists($userId)) {
            User::ChangeUserName($userId, $newName);
            $response['success'] = TRUE;
            $response['message'] = "Successfully changed username!";
            return $response;
        }
    }

    $response['success'] = FALSE;
    $response['message'] = "Failed updating username";
    return $response;
}

function HasFile() {
    if (isset($_FILES["file"]) && $_FILES["file"]["tmp_name"] != '') {
        if (is_uploaded_file($_FILES["file"]["tmp_name"])) {
            return TRUE;
        }
    }
    return FALSE;
}

function IsValidName($name) {
    return preg_match("/^[a-zA-Z'-]+$/", $name);
}
