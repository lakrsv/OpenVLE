<?php

require_once __DIR__ . '/../auth/mysql_config.php';
require_once __DIR__ . '/../auth/role.php';
require_once __DIR__ . '/../header/auth_header.php';

if (!isset($_POST['action'], $_POST['userid'])) {
    exit("Post params not set");
}

if (!$userRole->HasPermission("manage_profile")) {
    header("Location: user-home.php");
}

$action = $_POST["action"];
$userId = $_POST["userid"];

// Security check.
if (!$userRole->HasPermission("manage_users")) {
    $userId = $_SESSION["userid"];
}

switch ($action) {
    case "upload_image":
        return TryUploadImage($userId);
}

function TryUploadImage($userId) {
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
        echo json_encode($response);
        return FALSE;
    }

    if ($file["size"] > $maxImageSize) {
        $response['success'] = FALSE;
        $response['message'] = "This image is too large. Max 2 MB";
        echo json_encode($response);
        return FALSE;
    }

    if (file_exists($uploadPath)) {
        chmod($uploadPath, 0755);
        unlink($uploadPath);
    }

    if (move_uploaded_file($file["tmp_name"], $uploadPath)) {
        $response['success'] = TRUE;
        $response['message'] = "Image successfully uploaded!";

        echo json_encode($response);
        return TRUE;
    } else {
        $response['success'] = FALSE;
        $response['message'] = "An error occured while trying to upload the image";
        echo json_encode($response);
        return TRUE;
    }
}
