<?php
require_once 'header/auth_header.php';
require_once 'auth/login.php';
require_once 'course/course.php';

// TODO - Change this to allow user to change their profile
$canManageProfile = $userRole->HasPermission("manage_profile");
$canManageUsers = $userRole->HasPermission("manage_users");
$canViewOtherProfiles = $userRole->HasPermission("view_profiles");

if (!$canManageProfile && !$canManageUsers) {
    header("Location: user-home.php");
}

$userId = filter_input(INPUT_GET, "id", FILTER_SANITIZE_NUMBER_INT);
if (!$userId) {
    exit("Get params not set");
}

if (!$canManageUsers) {
    if ($userId != $_SESSION["userid"]) {
        if ($canViewOtherProfiles) {
            $canManageProfile = FALSE;
        } else {
            $userId = $_SESSION["userid"];
        }
    }
} else {
    $canManageProfile = true;
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

        <script>
            $(function () {
                $('[data-toggle="tooltip"]').tooltip();
            });
        </script>

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
                <div class="col-lg-8">

                    <div class="col-12 mb-2 py-2 bg-dark text-white">
                        User Profile
                    </div>

                    <div class="row px-3">
                        <!-- General Profile Details -->
                        <div class="col-lg-6 pr-1 pl-0">

                            <div class="col-12 mb-2 py-2 bg-secondary text-white">
                                General Details
                            </div>

                            <div class="input-group mb-2">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="email-addon">E-mail</span>
                                </div>
                                <?php
                                echo '<input type="text" class="form-control" placeholder="Email" aria-label="Email" aria-describedby="email-addon" value="' . $userEmail . '" readonly>'
                                ?>                    
                            </div>
                            <div class="input-group mb-2">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="name-addon">Name</span>
                                </div>
                                <?php
                                echo '<input type="text" id="new-name" class="form-control" placeholder="Name" aria-label="Name" aria-describedby="name-addon" value="' . $userName . '">'
                                ?>
                            </div>

                            <?php if ($canManageProfile) { ?>
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="picture-upload">
                                        <label class="custom-file-label" for="picture-upload" aria-describedby="picture-upload-button" id="profile-label">Choose profile picture</label>
                                    </div>
                                </div>
                                <small class="form-text text-muted mb-3">(PNG, JPG) Max: 2 MB</small>
                            <?php } ?>

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
                                echo '<img src="' . $realPicturePath . '?' . filemtime($realPicturePath) . '" class="img-fluid" alt="Profile Picture">';
                            }
                            ?>


                            <div class="row mt-2">
                                <div class="col-12 text-left">
                                    <a class="btn btn-secondary" href="manage_users.php">Back</a>
                                    <?php if ($canManageProfile) { ?>
                                        <a class="btn btn-primary" href="#" id="save-changes-button">Save Changes</a>
                                    <?php } ?>
                                </div>
                            </div>

                            <!-- Alert Box -->
                            <div id ="editAlert" class="alert alert-danger show invisible mt-2" role="alert">
                                <div id="editAlertBody"></div>
                            </div>
                        </div>

                        <!-- Edit Contact Details-->
                        <div class="col-lg-6 pl-1 pr-0">
                            <div class="col-12 mb-2 py-2 bg-secondary text-white">
                                Contact Details
                            </div>

                            <?php if ($canManageProfile) { ?>
                            <?php } ?>
                        </div>
                    </div>
                </div>

                <!-- Edit Assigned Courses-->
                <div class="col-lg-4">
                    <div class="col-12 mb-2 py-2 bg-dark text-white">
                        Assigned Courses                      
                    </div>     

                    <div id="courseTable" class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <tbody>
                                <?php
                                $courses = Course::GetCoursesForUser($userId);
                                foreach ($courses as $course) {
                                    echo '<tr id=course-' . $course->GetId() . '>';
                                    echo '<th scope="row">';
                                    echo '<div class="container">';
                                    echo '<div class="row">';
                                    echo '<div class="col-10 coursename">';
                                    echo $course->GetName();
                                    echo '</div>';
                                    if ($canManageUsers) {
                                        echo '<div class="col-2 text-right">';
                                        echo '<a class="far fa-times-circle text-danger no-decoration unassigncourse" href="#" data-toggle="tooltip" data-placement="left" title="Unassign course"></a>';
                                        echo '</div>';
                                    }
                                    echo '</div>';
                                    echo '</div>';
                                    echo '</th>';
                                    echo '</tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if ($canManageUsers) { ?>
                        <script>
                            var courseId;
                            var userId;
                            $(document).ready(function () {
                                $('.unassigncourse').click(function (e) {
                                    e.preventDefault();

                                    userId = $('#userid').text();
                                    courseId = $(this).closest('tr').attr('id').replace('course-', '');

                                    var $modal = $('#profileModal');
                                    $modal.find('.modal-body').html(function () {
                                        return "You are about unassign this course."
                                                + "<br><strong>Are you sure?</strong>";
                                    });
                                    $modal.modal({
                                        show: true
                                    });
                                });
                                $('#modalConfirmButton').click(function (e) {
                                    e.preventDefault();
                                    $.ajax({
                                        type: "POST",
                                        url: "manage/modify_user.php",
                                        data: {
                                            action: "unassign-course",
                                            user: userId,
                                            course: courseId
                                        },
                                        success: function (data) {
                                            alert(data);
                                            data = $.parseJSON(data);
                                            var $success = data.success;
                                            var $message = data.message;

                                            var $alert = $('#courseAlert');
                                            $alert.removeClass("invisible");
                                            if ($success) {
                                                $alert.removeClass("alert-danger");
                                                $alert.addClass("alert-success");
                                                $alert.find("#courseAlertBody").html(function () {
                                                    return "<strong>Success!</strong> " + $message;
                                                });
                                                $('#course-' + courseId).remove();

                                            } else {
                                                $alert.removeClass("alert-success");
                                                $alert.addClass("alert-danger");
                                                $alert.find("#courseAlertBody").html(function () {
                                                    return "<strong>Error!</strong> " + $message;
                                                });
                                            }
                                        }
                                    });
                                });
                            });
                        </script>
                    <?php } ?>

                    <?php if ($canManageUsers) { ?>
                        <div class="input-group">
                            <select class="custom-select" id="select-course">
                                <option selected>Choose Course...</option>
                                <?php
                                $allCourses = Course::GetAll();
                                $userCourses = Course::GetCoursesForUser($userId);

                                foreach ($allCourses as $course) {
                                    if (in_array($course, $userCourses)) {
                                        continue;
                                    }
                                    echo '<option value="' . $course->GetId() . '">' . $course->GetName() . '</option>';
                                }
                                ?>
                            </select>
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button" id="assigncourse">Add</button>
                            </div>
                        </div>

                        <!-- Alert Box -->
                        <div id ="courseAlert" class="alert alert-danger show invisible mt-2" role="alert">
                            <div id="courseAlertBody"></div>
                        </div>

                        <script>
                            $(document).ready(function () {
                                $('#assigncourse').click(function (e) {
                                    e.preventDefault();
                                    var courseId = $('#select-course').children("option:selected").val();
                                    var userId = $('#userid').text();
                                    $.ajax({
                                        type: "POST",
                                        url: "manage/modify_user.php",
                                        data: {
                                            action: "assign-course",
                                            user: userId,
                                            course: courseId
                                        },
                                        success: function (data) {
                                            data = $.parseJSON(data);
                                            var $success = data.success;
                                            var $message = data.message;
                                            var $alert = $('#courseAlert');
                                            $alert.removeClass("invisible");
                                            if ($success) {
                                                $alert.removeClass("alert-danger");
                                                $alert.addClass("alert-success");
                                                $alert.find("#courseAlertBody").html(function () {
                                                    return "<strong>Success!</strong> " + $message + ". <a href='#' onclick='window.location.reload(true);' class='alert-link'>Please refresh to see changes</a>";
                                                });
                                            } else {
                                                $alert.removeClass("alert-success");
                                                $alert.addClass("alert-danger");
                                                $alert.find("#courseAlertBody").html(function () {
                                                    return "<strong>Error!</strong> " + $message;
                                                });
                                            }
                                        }
                                    });
                                });
                            });
                        </script>
                    <?php } ?>
                </div>

            </div>
        </div>

        <!-- Delete Role Confirmation Modal -->
        <div class="modal fade" id="profileModal" tabindex="-1" role="dialog" aria-labelledby="profileModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="profileModalLabel">Delete Role</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        You are about to delete this role! Are you sure?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                        <button id="modalConfirmButton" type="submit" class="btn btn-primary" data-dismiss="modal">Yes</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Change Profile Script -->
        <script>
<?php if ($canManageProfile): ?>
                var selectedFile;
                $(document).ready(function () {
                    $('#picture-upload').change(function (e) {
                        e.preventDefault();
                        selectedFile = e.target.files[0];
                        var fileName = selectedFile.name;
                        $('#profile-label').text(fileName);
                    });
                    $('#save-changes-button').click(function (e) {
                        e.preventDefault();
                        var formData = new FormData();
                        var userId = $('#userid').text();
                        var userName = $('#new-name').val();
                        formData.append('name', userName);
                        formData.append('userid', userId);
                        if (selectedFile) {
                            formData.append('file', selectedFile);
                        }

                        $.ajax({
                            type: "POST",
                            url: "manage/modify_profile.php",
                            cache: false,
                            contentType: false,
                            processData: false,
                            data: formData,
                            success: function (data) {
                                data = $.parseJSON(data);
                                var $success = data.success;
                                var $message = data.message;
                                var $alert = $('#editAlert');
                                $alert.removeClass("invisible");
                                if ($success) {
                                    $alert.removeClass("alert-danger");
                                    $alert.addClass("alert-success");
                                    $alert.find("#editAlertBody").html(function () {
                                        return "<strong>Success!</strong> " + $message + ". <a href='#' onclick='window.location.reload(true);' class='alert-link'>Please refresh to see changes</a>";
                                    });
                                } else {
                                    $alert.removeClass("alert-success");
                                    $alert.addClass("alert-danger");
                                    $alert.find("#editAlertBody").html(function () {
                                        return "<strong>Error!</strong> " + $message;
                                    });
                                }
                            }
                        });
                    });
                });
<?php endif; ?>
        </script>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>
    </body>
</html>