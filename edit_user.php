<?php
require_once 'header/auth_header.php';
require_once 'auth/login.php';

// TODO - Change this to allow user to change their profile
$canManageProfile = $userRole->HasPermission("manage_profile");
$canManageUsers = $userRole->HasPermission("manage_users");
if (!$canManageProfile && !$canManageUsers) {
    header("Location: user-home.php");
}

if (!isset($_GET['id'])) {
    exit("Get params not set");
}

$userId = $_GET['id'];

if (!$canManageUsers) {
    $userId = $_SESSION["userid"];
}

$userName = User::GetUsernameFromId($userId);
$userEmail = User::GetEmailFromId($userId);
?>

<!doctype html>
<html lang="en">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">

        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">

        <!-- Custom CSS -->
        <link rel="stylesheet" href="css/custom.css">

        <!-- JQuery -->
        <script
            src="https://code.jquery.com/jquery-3.3.1.min.js"
            integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
        crossorigin="anonymous"></script>

        <title>OpenVLE - Admin</title>
    </head>
    <body>
        <nav class="navbar navbar-expand-md navbar-dark bg-dark static-top">
            <a class="navbar-brand" href="#">OpenVLE Admin</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapse" aria-controls="collapse" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="collapse">
                <ul class="navbar-nav mr-auto px-2">
                    <li class="nav-item active">
                        <a class="nav-link" href="manage_users.php">Manage Users</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="manage_courses.php">Manage Courses</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="manage_roles.php">Manage Roles</a>
                    </li>
                </ul>
                <a id="logout" class="btn btn-outline-danger pull-right my-2 my-sm-0" href="auth/log_out.php">Log Out</a>
            </div>
        </nav>

        <?php
        echo '<div id="userid" style="display: none;">' . $userId . '</div>'
        ?>

        <div class="container-fluid mt-2">
            <div class="row">
                <div class="col-lg-4">
                    <div class="col-12 mb-3 py-2 bg-dark text-white">
                        Edit User
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="email-addon">E-mail</span>
                        </div>
                        <?php
                        echo '<input type="text" class="form-control" placeholder="Email" aria-label="Email" aria-describedby="email-addon" value="' . $userEmail . '" readonly>'
                        ?>                    
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="name-addon">Name</span>
                        </div>
                        <?php
                        echo '<input type="text" class="form-control" placeholder="Name" aria-label="Name" aria-describedby="name-addon" value="' . $userName . '">'
                        ?>
                    </div>
                    <div class="input-group">
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="picture-upload">
                            <label class="custom-file-label" for="picture-upload" aria-describedby="picture-upload-button" id="profile-label">Choose profile picture</label>
                        </div>
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="submit" id="picture-upload-button">Upload</button>
                        </div>
                    </div>
                    <small class="form-text text-muted mb-3">(PNG, JPG) Max: 2 MB</small>

                    <?php
                    $profilePictureFilepath1 = 'uploads/profile_pictures/' . $userId . '.jpeg';
                    $profilePictureFilepath2 = 'uploads/profile_pictures/' . $userId . '.png';
                    $realPicturePath;
                    if (file_exists($profilePictureFilepath1)) {
                        $realPicturePath = $profilePictureFilepath1;
                    } else if (file_exists($profilePictureFilepath2)) {
                        $realPicturePath = $profilePictureFilepath2;
                    }

                    if (isset($realPicturePath)) {
                        echo '<img src="' . $realPicturePath . '" class="img-fluid" alt="Profile Picture">';
                    }
                    ?>
                </div>


            </div>
            <div class="row mt-3">
                <div class="col-12 text-left">
                    <a class="btn btn-primary" href="#">Save Changes</a>
                    <a class="btn btn-secondary" href="manage_users.php">Back</a>
                </div>
            </div>
        </div>

        <!-- Upload Picture Script -->
        <script>
            var selectedFile;
            $(document).ready(function () {
                $('#picture-upload').change(function (e) {
                    e.preventDefault();
                    selectedFile = e.target.files[0];
                    var fileName = selectedFile.name;
                    $('#profile-label').text(fileName);
                });
                $('#picture-upload-button').click(function (e) {
                    e.preventDefault();
                    var formData = new FormData();
                    var userId = $('#userid').text();
                    formData.append('action', 'upload_image');
                    formData.append('userid', userId);
                    formData.append('file', selectedFile);
                    $.ajax({
                        type: "POST",
                        url: "manage/modify_profile.php",
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: formData,
                        success: function (data) {
                            alert(data);
                            data = $.parseJSON(data);
                            var $success = data.success;
                            var $message = data.message;
                        }
                    });
                });
            });
        </script>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>
    </body>
</html>