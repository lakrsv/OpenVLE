<?php
require_once 'header/auth_header.php';
require_once 'auth/login.php';

if (!$userRole->HasPermission("manage_users")) {
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

        <script>
            $(function () {
                $('[data-toggle="tooltip"]').tooltip()
            })
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
        <div class="container-fluid mt-2">
            <!-- Display Roles -->
            <?php
            $allUsers = User::GetAll();
            $allRoles = Role::GetAll();
            ?>

            <div id="userTable" class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">User</th>
                            <th scope="col">Role</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($allUsers as $user) {
                            $userRole = Role::GetRoleFromUserId($user->GetId());

                            echo '<tr id=user-' . $user->GetId() . '>';
                            echo '<th scope="row">';
                            echo '<div class="container">';
                            echo '<div class="row">';
                            echo '<div class="col-10 email">';
                            echo $user->GetEmail();
                            echo '</div>';
                            echo '<div class="col-2 text-right">';
                            echo '<a class="far fa-edit text-dark no-decoration edituser" href="view_profile.php?id=' . $user->GetId() . '" data-toggle="tooltip" data-placement="bottom" title="Edit user profile"></a>';
                            echo '</div>';
                            echo '</div>';
                            echo '</div>';
                            echo '</th>';
                            echo '<td id="role">';
                            echo '<div class="input-group">';
                            echo '<div class="input-group-prepend">';
                            echo '<label class="input-group-text" for="roleSelect' . $user->GetId() . '">Role</label>';
                            echo '</div>';
                            $disabled = $userRole->GetName() == 'admin' ? "disabled" : "";
                            echo '<select class="custom-select role-select" id="roleSelect' . $user->GetId() . '" ' . $disabled . '>';

                            foreach ($allRoles as $role) {
                                $selectedRole = $role->GetId() == $userRole->GetId() ? "selected" : "";
                                echo '<option ' . $selectedRole . '>';
                                echo $role->GetName();
                                echo '</option>';
                            }
                            echo '</select>';
                            echo '</div>';
                            echo '</td>';
                            echo '</tr>';
                        }
                        ?>
                    </tbody>
                </table>
                <!-- Delete User Script -->
                <script>
                    var $userId;
                    $(document).ready(function () {
                        $('.deleteuser').click(function (e) {
                            e.preventDefault();
                            var $row = $(this).closest("tr");
                            $userId = $row.attr('id').replace('user-', '');
                            var $email = $row.find('.email').text();
                            var $modal = $('#deleteUserModal');
                            $modal.find('.modal-body').html(function () {
                                return "You are about delete the user <strong>" + $email + "</strong>."
                                        + "<br><strong>Are you sure?</strong>";
                            });
                            $modal.modal({
                                show: true
                            });
                        });
                        $('#deleteUserButton').click(function (e) {
                            e.preventDefault();
                            $.ajax({
                                type: "POST",
                                url: "manage/modify_user.php",
                                data: {
                                    action: "delete",
                                    user: $userId
                                },
                                success: function (data) {
                                    data = $.parseJSON(data);
                                    var $success = data.success;
                                    var $message = data.message;

                                    var $alert = $('#userAlert');
                                    $alert.removeClass("invisible");
                                    if ($success) {
                                        $alert.removeClass("alert-danger");
                                        $alert.addClass("alert-success");
                                        $alert.find("#userAlertBody").html(function () {
                                            return "<strong>Success!</strong> " + $message;
                                        });
                                        $('#user-' + $userId).remove();

                                    } else {
                                        $alert.removeClass("alert-success");
                                        $alert.addClass("alert-danger");
                                        $alert.find("#userAlertBody").html(function () {
                                            return "<strong>Error!</strong> " + $message;
                                        });
                                    }
                                }
                            });
                        });
                    });
                </script>

                <!-- Change Role Script -->
                <script>
                    $(document).ready(function () {
                        $('.role-select').change(function () {
                            var $selected = $(this).find("option:selected");
                            var $roleName = $selected.val();
                            var $userId = $(this).closest("tr").attr("id").replace("user-", "");

                            $.ajax({
                                type: "POST",
                                url: "manage/modify_user.php",
                                data: {
                                    action: "change-role",
                                    roleName: $roleName,
                                    user: $userId
                                },
                                success: function (data) {
                                    data = $.parseJSON(data);
                                    var $success = data.success;
                                    var $message = data.message;

                                    var $alert = $('#userAlert');
                                    $alert.removeClass("invisible");
                                    if ($success) {
                                        $alert.removeClass("alert-danger");
                                        $alert.addClass("alert-success");
                                        $alert.find("#userAlertBody").html(function () {
                                            return "<strong>Success!</strong> " + $message;
                                        });

                                    } else {
                                        $alert.removeClass("alert-success");
                                        $alert.addClass("alert-danger");
                                        $alert.find("#userAlertBody").html(function () {
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
                        <label for="email">Add a new user</label>
                        <input type="email" class="form-control" id="email" placeholder="Enter email" name="email" autocomplete="email">
                        <input type="text" class="form-control" id="username" placeholder="Enter username" name="username" autocomplete="off">
                        <input type="password" class="form-control" id="password" placeholder="Enter password" name="password" autocomplete="new-password">
                    </div>
                    <button id="addUserButton" type="submit" class="btn btn-primary">Add</button>
                </form>

                <!-- Add User Script -->
                <script>
                    $(document).ready(function () {
                        $('#addUserButton').click(function (e) {
                            e.preventDefault();
                            var $email = $('#email').val();
                            var $username = $('#username').val();
                            var $password = $('#password').val()

                            $.ajax({
                                type: "POST",
                                url: "manage/modify_user.php",
                                data: {
                                    action: "add",
                                    user: $email,
                                    name: $username,
                                    password: $password
                                },
                                success: function (data) {
                                    data = $.parseJSON(data);
                                    var $success = data.success;
                                    var $message = data.message;

                                    var $alert = $('#userAlert');
                                    $alert.removeClass("invisible");
                                    if ($success) {
                                        $alert.removeClass("alert-danger");
                                        $alert.addClass("alert-success");
                                        $alert.find("#userAlertBody").html(function () {
                                            return "<strong>Success!</strong> " + $message + ". <a href='#' onclick='window.location.reload(true);' class='alert-link'><strong>Please refresh to see changes</strong></a>";
                                        });

                                    } else {
                                        $alert.removeClass("alert-success");
                                        $alert.addClass("alert-danger");
                                        $alert.find("#userAlertBody").html(function () {
                                            return "<strong>Error!</strong> " + $message;
                                        });
                                    }
                                }
                            });
                        });
                    });
                </script>

                <!-- Alert Box -->
                <div id ="userAlert" class="alert alert-danger show invisible" role="alert">
                    <div id="userAlertBody"></div>
                </div>

                <!-- Delete User Confirmation Modal -->
                <div class="modal fade" id="deleteUserModal" tabindex="-1" role="dialog" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="deleteUserModalLabel">Delete User</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                You are about to delete this user! Are you sure?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                                <button id="deleteUserButton" type="button" class="btn btn-primary" data-dismiss="modal">Yes</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
            <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>
    </body>
</html>