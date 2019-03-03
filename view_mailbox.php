<?php
require_once 'header/auth_header.php';
require_once 'auth/login.php';
require_once 'classes/course.php';
require_once 'classes/courseSectionContent.php';

// TODO - Change this to allow user to change their profile
$canManageCourses = $userRole->HasPermission("manage_courses");
$canAddAssignment = $userRole->HasPermission("add_assignment");
$canAddQuiz = $userRole->HasPermission("add_quiz");
$canAddResource = $userRole->HasPermission("add_resource");
$canViewContent = $userRole->HasPermission("view_content");
$userCourses = Course::GetCoursesForUser($_SESSION['userid']);

$canAddSection = $canManageCourses || $canAddAssignment || $canAddQuiz || $canAddResource;
$canAddContent = $canManageCourses || $canAddAssignment || $canAddQuiz || $canAddResource;

if (!$canManageCourses && !$canViewContent) {
    header("Location: user-home.php");
}

$courseId = filter_input(INPUT_GET, "id", FILTER_SANITIZE_NUMBER_INT);
$course;
if (!$courseId) {
    // View all courses?
} else {
    $course = Course::GetCourseWithId($courseId);
    $courseSections = $course->GetSections();
    if (!$canManageCourses && !in_array($course, $userCourses)) {
        header("Location: view_course.php");
    }
}
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

        <title>OpenVLE - Mail</title>
    </head>
    <body>

        <script>
            $(function () {
                $('[data-toggle="tooltip"]').tooltip();
            });
        </script>

        <?php if ($canManageCourses) { ?>
            <nav class="navbar navbar-expand-md navbar-dark bg-dark static-top">
                <a class="navbar-brand" href="#">OpenVLE Admin</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapse" aria-controls="collapse" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="collapse">
                    <ul class="navbar-nav mr-auto px-2">
                        <li class="nav-item">
                            <a class="nav-link" href="manage_users.php">Manage Users</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="manage_courses.php">Manage Courses</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="manage_roles.php">Manage Roles</a>
                        </li>
                    </ul>
                    <a id="inbox" class="btn btn-default" href="view_mailbox.php">
                        <span class="fa-stack">
                            <i class="fas fa-envelope fa-stack-2x text-white"></i>
                            <i class="fa-stack-1x text-info text-right pr-1 pt-3">
                                <h5>
                                    <strong>
                                        <!-- Amount in inbox -->
                                        <!--+1-->
                                    </strong>
                                </h5>
                            </i>
                        </span>
                    </a>
                    <a id="logout" class="btn btn-outline-danger pull-right my-2 my-sm-0" href="auth/log_out.php">Log Out</a>
                </div>
            </nav>
        <?php } else { ?>
            <nav class="navbar navbar-expand-md navbar-dark bg-dark static-top">
                <a class="navbar-brand" href="user_home.php">OpenVLE</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapse" aria-controls="collapse" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="collapse">
                    <ul class="navbar-nav mr-auto px-2">
                        <li class="nav-item">
                            <a class="nav-link" href="view_course.php">Your Courses</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="view_profile.php">Your Profile</a>
                        </li>
                    </ul>
                    <a id="inbox" class="btn btn-default" href="view_mailbox.php">
                        <span class="fa-stack">
                            <i class="fas fa-envelope fa-stack-2x text-white"></i>
                            <i class="fa-stack-1x text-info text-right pr-1 pt-3">
                                <h5>
                                    <strong>
                                        <!-- Amount in inbox -->
                                        <!--+1-->
                                    </strong>
                                </h5>
                            </i>
                        </span>
                    </a>
                    <a id="logout" class="btn btn-outline-danger pull-right my-2 my-sm-0" href="auth/log_out.php">Log Out</a>
                </div>
            </nav>
        <?php } ?>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>
    </body>
</html>