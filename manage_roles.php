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
        <div class="container mt-2">
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

            <table class="table table-striped table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">Role</th>
                        <?php
                        foreach ($allPermissions as $permission) {
                            echo '<th scope="col">' . $permission->GetName() . '</th>';
                        }
                        ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($allRoles as $role) {
                        echo '<tr id='.$role->GetId().'>';
                        echo '<th scope="row">' . $role->GetName() . '</th>';
                        foreach ($allPermissions as $permission) {
                            $hasPermission = $role->HasPermission($permission->GetName());
                            $checked = $hasPermission ? "checked" : "";
                            
                            echo '<td id='.$permission->GetId().'>';
                            echo '<div>';
                            echo '<input type="checkbox" '.$checked.'>';
                            echo '</div>';
                            echo '</td>';
                        }
                        echo '</tr>';
                    }
                    ?>
                </tbody>
            </table>

        </div>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>
    </body>
</html>