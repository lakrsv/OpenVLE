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

        <title>OpenVLE - Admin</title>
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
                        <li class="nav-item active">
                            <a class="nav-link" href="manage_courses.php">Manage Courses</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="manage_roles.php">Manage Roles</a>
                        </li>
                    </ul>
                    <a id="inbox" class="btn btn-default">
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
                            <a class="nav-link" href="user_home.php">Your Dashboard</a>
                        </li>
                        <li class="nav-item active">
                            <a class="nav-link" href="view_course.php">Your Courses</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="view_profile.php">Your Profile</a>
                        </li>
                    </ul>
                    <a id="inbox" class="btn btn-default">
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

        <?php
        echo '<div id="courseid" style="display: none;">' . $courseId . '</div>'
        ?>

        <!-- Singular course set -->
        <?php if ($courseId) { ?>
            <div class="jumbotron jumbotron-fluid">
                <div class="container">
                    <h1 class="display-4"><?php echo $course->GetName() ?></h1>
                    <p class="lead"><?php echo $course->GetDescription() ?></p>
                </div>
            </div>
            <div class="container-fluid mt-2">
                <div class="col-12">
                    <?php foreach ($courseSections as $section) { ?>
                        <div id="sections-<?php echo $section->GetId() ?>">
                            <div class="card">
                                <?php echo '<div class="card-header bg-dark row mx-0" id="section-header-' . $section->GetId() . '">' ?>
                                <div class="col-10">

                                    <h5 class="mb-0">
                                        <?php echo '<button class="btn btn-link text-white" data-toggle="collapse" data-target="#section-collapse-' . $section->GetId() . '" aria-expanded="true" aria-controls="section-collapse-' . $section->GetId() . '">' ?>
                                        <?php echo $section->GetName(); ?>
                                        </button>
                                    </h5>
                                </div>

                                <?php
                                if ($canAddSection) {
                                    echo '<div class="col-2 text-right">';
                                    echo '<a class="far fa-times-circle text-danger no-decoration deletesection" id=delete-section-' . $section->GetId() . '" data-toggle="tooltip" data-placement="bottom" title="Delete section"></a>';
                                    echo '</div>';
                                }
                                ?>
                            </div>

                            <?php
                            $sectionContents = $section->GetContents();
                            ?>

                            <?php echo '<div id="section-collapse-' . $section->GetId() . '" class="collapse show bg-light" aria-labelledby="section-header-' . $section->GetId() . '" data-parent="#sections-' . $section->GetId() . '">' ?>
                            <div class="card-body">
                                <?php foreach ($sectionContents as $content) { ?>
                                    <div id="contents-<?php echo $content->GetId() ?>">
                                        <div class="card">
                                            <?php echo '<div class="card-header bg-light row mx-0" id="content-header-' . $content->GetId() . '">' ?>
                                            <div class="col-10">
                                                <h5 class="mb-0">

                                                    <?php echo '<button class="btn btn-link text-dark" data-toggle="collapse" data-target="#content-collapse-' . $content->GetId() . '" aria-expanded="true" aria-controls="content-collapse-' . $content->GetId() . '">' ?>
                                                    <?php echo $content->GetName(); ?>
                                                    </button>
                                                </h5>
                                            </div>
                                            <?php
                                            if ($canAddContent) {
                                                echo '<div class="col-2 text-right">';
                                                echo '<a class="far fa-times-circle text-danger no-decoration deletecontent" id=delete-content-' . $content->GetId() . '" data-toggle="tooltip" data-placement="bottom" title="Delete content"></a>';
                                                echo '</div>';
                                            }
                                            ?>
                                        </div>

                                        <?php echo '<div id="content-collapse-' . $content->GetId() . '" class="collapse show" aria-labelledby="content-header-' . $content->GetId() . '" data-parent="#contents-' . $content->GetId() . '">' ?>
                                        <div class="card-body">
                                            <?php if ($content->GetType() == CourseSectionContent::Text) { ?>
                                                <?php echo $content->GetData() ?>
                                            <?php } else if ($content->GetType() == CourseSectionContent::PDF) { ?>
                                                <!-- Not Implemented -->
                                            <?php } else if ($content->GetType() == CourseSectionContent::Quiz) { ?>
                                                <!-- Not Implemented -->
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                        <?php if ($canAddContent) { ?>
                            <br>
                            <div>
                                <button id="add-content-<?php echo $section->GetId() ?>" class="btn btn-primary" type="button">Add Section Content</button>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        <?php } ?>      
        <br>
        <?php if ($canAddSection) { ?>
            <div>
                <button id="add-section-<?php echo $course->GetId() ?>" class="btn btn-primary" type="button">Add Section</button>
            </div>
        <?php } ?>
        <!-- Alert Box -->
        <div id ="courseAlert" class="alert alert-danger show invisible" role="alert">
            <div id="courseAlertBody"></div>
        </div>
        <!-- Show all courses -->
    <?php } else { ?>
    <?php } ?>

    <div class="row mt-2">
        <div class="col-12 text-left">
            <a class="btn btn-secondary" href="javascript:history.go(-1)">Back</a>
        </div>                         
    </div>

    <!-- Add Section Script -->
    <?php if ($canAddSection) { ?>
        <script>
        </script>
    <?php } ?>

    <!-- Add Content Script -->
    <?php if ($canAddContent) { ?>
        <script>
        </script>
    <?php } ?>

    <!-- Delete Section Script -->
    <?php if ($canAddSection) { ?>
        <script>
            var sectionId;
            var action;
            $(document).ready(function () {
                $('.deletesection').click(function (e) {
                    action = "delete-section";
                    e.preventDefault();
                    sectionId = $(this).attr('id').replace('delete-section-', '');
                    var $modal = $('#confirmationModal');
                    $modal.find('.modal-body').html(function () {
                        return "You are about delete this section."
                                + "<br><strong>Are you sure?</strong>";
                    });
                    $modal.modal({
                        show: true
                    });
                });
                $('#confirmation-modal-button').click(function (e) {
                    if (action !== "delete-section") {
                        return;
                    }
                    action = null;
                    e.preventDefault();
                    $.ajax({
                        type: "POST",
                        url: "manage/modify_course.php",
                        data: {
                            action: "delete-section",
                            section: sectionId
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
                                $alert.find("#roleAlertBody").html(function () {
                                    return "<strong>Success!</strong> " + $message;
                                });
                                $('#sections-' + sectionId).remove();

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

    <!-- Delete Content Script -->
    <?php if ($canAddContent) { ?>
        <script>
            var contentId;
            var action;
            $(document).ready(function () {
                $('.deletecontent').click(function (e) {
                    action = "delete-content";
                    e.preventDefault();
                    contentId = $(this).attr('id').replace('delete-content-', '');
                    var $modal = $('#confirmationModal');
                    $modal.find('.modal-body').html(function () {
                        return "You are about delete this section content."
                                + "<br><strong>Are you sure?</strong>";
                    });
                    $modal.modal({
                        show: true
                    });
                });
                $('#confirmation-modal-button').click(function (e) {
                    if (action !== "delete-content") {
                        return;
                    }
                    action = null;
                    e.preventDefault();
                    $.ajax({
                        type: "POST",
                        url: "manage/modify_course.php",
                        data: {
                            action: "delete-content",
                            content: contentId
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
                                $alert.find("#roleAlertBody").html(function () {
                                    return "<strong>Success!</strong> " + $message;
                                });
                                $('#content-' + contentId).remove();

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

    <!--Confirmation Modal -->
    <div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog" aria-labelledby="confirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmationModalLabel">Are you sure?</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    You are about to delete this role! Are you sure?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                    <button id="confirmation-modal-button" type="submit" class="btn btn-primary" data-dismiss="modal">Yes</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>
</body>
</html>