<?php
require_once 'header/auth_header.php';
require_once 'auth/role.php';
require_once 'auth/permission.php';

if (!$userRole->HasPermission("manage_roles")) {
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
                    <li class="nav-item">
                        <a class="nav-link" href="manage_users.php">Manage Users</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="manage_courses.php">Manage Courses</a>
                    </li>
                    <li class="nav-item active">
                        <a class="nav-link" href="manage_roles.php">Manage Roles</a>
                    </li>
                </ul>
                <a id="logout" class="btn btn-outline-danger pull-right my-2 my-sm-0" href="auth/log_out.php">Log Out</a>
            </div>
        </nav>
        <div class="container-fluid mt-2">
            <!-- Display Roles -->
            <?php
            $allRoles = Role::GetAll();
            if (!$allRoles) {
                exit("No Roles Configured!");
            }

            $allPermissions = Permission::GetAll();
            if (!$allPermissions) {
                exit("No Permissions Configured!");
            }
            ?>

            <div id="roleTable" class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">Role</th>
                            <?php
                            foreach ($allPermissions as $permission) {
                                echo '<th scope="col" data-toggle="tooltip" data-placement="bottom" title="' . $permission->GetDescription() . '">' . $permission->GetName() . '</th>';
                            }
                            ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($allRoles as $role) {
                            echo '<tr id=role-' . $role->GetId() . '>';
                            echo '<th scope="row">';
                            echo '<div class="container">';
                            echo '<div class="row">';
                            echo '<div class="col-10 rolename">';
                            echo $role->GetName();
                            echo '</div>';
                            echo '<div class="col-2 text-right">';
                            if ($role->GetName() != "admin") {
                                echo '<a class="far fa-times-circle text-danger no-decoration deleterole" href="#"></a>';
                            }
                            echo '</div>';
                            echo '</div>';
                            echo '</div>';
                            echo '</th>';
                            foreach ($allPermissions as $permission) {
                                $hasPermission = $role->HasPermission($permission->GetName());
                                $checked = $hasPermission ? "checked" : "";
                                $disabled = $role->GetName() == "admin" || $permission->GetName() == "admin" ? "disabled" : "";

                                echo '<td id=permission-' . $permission->GetId() . '>';
                                echo '<div>';
                                echo '<input type="checkbox" ' . $checked . ' ' . $disabled . ' class="permission-checkbox">';
                                echo '</div>';
                                echo '</td>';
                            }
                            echo '</tr>';
                        }
                        ?>
                    </tbody>
                </table>
                <!-- Delete Role Script -->
                <script>
                    var $roleId;
                    $(document).ready(function () {
                        $('.deleterole').click(function (e) {
                            e.preventDefault();
                            var $row = $(this).closest("tr");
                            $roleId = $row.attr('id').replace('role-', '');
                            var $roleName = $row.find('.rolename').text();
                            var $modal = $('#deleteRoleModal');
                            $modal.find('.modal-body').html(function () {
                                return "You are about delete the role <strong>" + $roleName + "</strong>."
                                        + "<br><strong>Are you sure?</strong>";
                            });
                            $modal.modal({
                                show: true
                            });
                        });
                        $('#deleteRoleButton').click(function (e) {
                            e.preventDefault();
                            $.ajax({
                                type: "POST",
                                url: "manage/modify_role.php",
                                data: {
                                    action: "delete",
                                    role: $roleId
                                },
                                success: function (data) {
                                    data = $.parseJSON(data);
                                    var $success = data.success;
                                    var $message = data.message;

                                    var $alert = $('#roleAlert');
                                    $alert.removeClass("invisible");
                                    if ($success) {
                                        $alert.removeClass("alert-danger");
                                        $alert.addClass("alert-success");
                                        $alert.find("#roleAlertBody").html(function () {
                                            return "<strong>Success!</strong> " + $message;
                                        });
                                        $('#role-' + $roleId).remove();

                                    } else {
                                        $alert.removeClass("alert-success");
                                        $alert.addClass("alert-danger");
                                        $alert.find("#roleAlertBody").html(function () {
                                            return "<strong>Error!</strong> " + $message;
                                        });
                                    }
                                }
                            });
                        });
                    });
                </script>
                <!-- Change Permissions Script -->
                <script>
                    $(document).ready(function () {
                        $('.permission-checkbox').change(function () {
                            var $checked = $(this).is(":checked");

                            var $roleRow = $(this).closest("tr");
                            var $roleId = $roleRow.attr('id').replace('role-', '');

                            var $permissionRow = $(this).closest("td");
                            var $permissionId = $permissionRow.attr('id').replace('permission-', '');

                            var $action = $checked ? "add" : "remove";

                            $.ajax({
                                type: "POST",
                                url: "manage/modify_permission.php",
                                data: {
                                    action: $action,
                                    roleId: $roleId,
                                    permissionId: $permissionId
                                },
                                success: function (data) {
                                    data = $.parseJSON(data);
                                    var $success = data.success;
                                    var $message = data.message;

                                    var $alert = $('#roleAlert');
                                    $alert.removeClass("invisible");
                                    if ($success) {
                                        $alert.removeClass("alert-danger");
                                        $alert.addClass("alert-success");
                                        $alert.find("#roleAlertBody").html(function () {
                                            return "<strong>Success!</strong> " + $message;
                                        });

                                    } else {
                                        $alert.removeClass("alert-success");
                                        $alert.addClass("alert-danger");
                                        $alert.find("#roleAlertBody").html(function () {
                                            return "<strong>Error!</strong> " + $message;
                                        });
                                    }
                                }
                            });
                        });
                    });
                </script>
            </div>

            <form>
                <div class="form-group">
                    <label for="newRole">Add a new role</label>
                    <input type="text" class="form-control" id="newRole" placeholder="Enter role name">
                </div>
                <button id="newRoleButton" type="button" class="btn btn-primary">Add</button>
            </form>

            <!-- Add Role Script -->
            <script>
                $(document).ready(function () {
                    $('#newRoleButton').click(function (e) {
                        e.preventDefault();
                        var $roleName = $('#newRole').val();
                        $.ajax({
                            type: "POST",
                            url: "manage/modify_role.php",
                            data: {
                                action: "add",
                                role: $roleName
                            },
                            success: function (data) {
                                data = $.parseJSON(data);
                                var $success = data.success;
                                var $message = data.message;

                                var $alert = $('#roleAlert');
                                $alert.removeClass("invisible");
                                if ($success) {
                                    $alert.removeClass("alert-danger");
                                    $alert.addClass("alert-success");
                                    $alert.find("#roleAlertBody").html(function () {
                                        return "<strong>Success!</strong> " + $message + ". <a href='#' onclick='window.location.reload(true);' class='alert-link'><strong>Please refresh to see changes</strong></a>";
                                    });

                                } else {
                                    $alert.removeClass("alert-success");
                                    $alert.addClass("alert-danger");
                                    $alert.find("#roleAlertBody").html(function () {
                                        return "<strong>Error!</strong> " + $message;
                                    });
                                }
                            }
                        });
                    });
                });
            </script>

            <br>

            <!-- Alert Box -->
            <div id ="roleAlert" class="alert alert-danger show invisible" role="alert">
                <div id="roleAlertBody"></div>
            </div>

            <!-- Delete Role Confirmation Modal -->
            <div class="modal fade" id="deleteRoleModal" tabindex="-1" role="dialog" aria-labelledby="deleteRoleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteRoleModalLabel">Delete Role</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            You are about to delete this role! Are you sure?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                            <button id="deleteRoleButton" type="button" class="btn btn-primary" data-dismiss="modal">Yes</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>
    </body>
</html>