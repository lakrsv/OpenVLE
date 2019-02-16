<?php
require_once 'header/auth_header.php';
require_once 'course/course.php';

if (!$userRole->HasPermission("manage_courses")) {
    header("Location: user-home.php");
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
                <a id="logout" class="btn btn-outline-danger pull-right my-2 my-sm-0" href="auth/log_out.php">Log Out</a>
            </div>
        </nav>
        <div class="container-fluid mt-2">
            <!-- Display Courses -->
            <?php
            $allCourses = Course::GetAll();
            ?>

            <div id="courseTable" class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">Course</th>
                            <th scope="col">Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($allCourses as $course) {
                            echo '<tr id=course-' . $course->GetId() . '>';
                            echo '<th scope="row">';
                            echo '<div class="container">';
                            echo '<div class="row">';
                            echo '<div class="col-10 coursename">';
                            echo $course->GetName();
                            echo '</div>';
                            echo '<div class="col-2 text-right">';
                            echo '<a class="far fa-edit text-dark no-decoration editcourse" href="#"></a>';
                            echo '</div>';
                            echo '</div>';
                            echo '</div>';
                            echo '</th>';
                            echo '<td id="coursedescription">';
                            echo $course->GetDescription();
                            echo '</td>';
                            echo '</tr>';
                        }
                        ?>
                    </tbody>
                </table>
                <!-- Delete Course Script -->
                <script>
                    var $courseId;
                    $(document).ready(function () {
                        $('.deletecourse').click(function (e) {
                            e.preventDefault();
                            var $row = $(this).closest("tr");
                            $courseId = $row.attr('id').replace('course-', '');
                            var $courseName = $row.find('.coursename').text();
                            var $modal = $('#deleteCourseModal');
                            $modal.find('.modal-body').html(function () {
                                return "You are about delete the course <strong>" + $courseName + "</strong>."
                                        + "<br><strong>Are you sure?</strong>";
                            });
                            $modal.modal({
                                show: true
                            });
                        });
                        $('#deleteCourseButton').click(function (e) {
                            e.preventDefault();
                            $.ajax({
                                type: "POST",
                                url: "manage/modify_course.php",
                                data: {
                                    action: "delete",
                                    course: $courseId
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
                                            return "<strong>Success!</strong> " + $message;
                                        });
                                        $('#course-' + $courseId).remove();

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

                <form>
                    <div class="form-group">
                        <label for="coursename">Add a new course</label>
                        <input type="text" class="form-control" id="coursename" placeholder="Enter name" name="coursename" autocomplete="off">
                        <input type="text" class="form-control" id="description" placeholder="Enter description" name="description" autocomplete="off">
                    </div>
                    <button id="addCourseButton" type="submit" class="btn btn-primary">Add</button>
                </form>

                <!-- Add Course Script -->
                <script>
                    $(document).ready(function () {
                        $('#addCourseButton').click(function (e) {
                            e.preventDefault();
                            var $coursename = $('#coursename').val();
                            var $description = $('#description').val()
                            $.ajax({
                                type: "POST",
                                url: "manage/modify_course.php",
                                data: {
                                    action: "add",
                                    course: $coursename,
                                    description: $description
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
                                            return "<strong>Success!</strong> " + $message + ". <a href='#' onclick='window.location.reload(true);' class='alert-link'><strong>Please refresh to see changes</strong></a>";
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

                <!-- Alert Box -->
                <div id ="courseAlert" class="alert alert-danger show invisible" role="alert">
                    <div id="courseAlertBody"></div>
                </div>

                <!-- Delete Course Confirmation Modal -->
                <div class="modal fade" id="deleteCourseModal" tabindex="-1" role="dialog" aria-labelledby="deleteCourseModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="deleteCourseModalLabel">Delete Course</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                You are about to delete this course! Are you sure?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                                <button id="deleteCourseButton" type="button" class="btn btn-primary" data-dismiss="modal">Yes</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
            <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>
    </body>
</html>