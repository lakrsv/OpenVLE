<?php
require_once 'header/auth_header.php';
require_once 'auth/login.php';
require_once 'classes/course.php';
require_once 'classes/courseSectionContent.php';
require_once 'classes/mailBox.php';

$canViewContent = $userRole->HasPermission("view_content");
$canManageCourses = $userRole->HasPermission("manage_courses");

if (!$canViewContent) {
    header("Location: user-home.php");
}

$to = filter_input(INPUT_GET, "to", FILTER_SANITIZE_NUMBER_INT);
$unread = filter_input(INPUT_GET, "unread", FILTER_SANITIZE_NUMBER_INT);
$read = filter_input(INPUT_GET, "read", FILTER_SANITIZE_NUMBER_INT);
$sent = filter_input(INPUT_GET, "sent", FILTER_SANITIZE_NUMBER_INT);

$showUnread = $unread || (!$to && !$read && !$sent);
$showRead = $read;
$showSent = $sent;
$showTo = $to;
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
                                        <?php echo MailBox::GetUnreadInboxCountForUser($_SESSION['userid']) ?>
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
                                        <?php echo MailBox::GetUnreadInboxCountForUser($_SESSION['userid']) ?>
                                    </strong>
                                </h5>
                            </i>
                        </span>
                    </a>
                    <a id="logout" class="btn btn-outline-danger pull-right my-2 my-sm-0" href="auth/log_out.php">Log Out</a>
                </div>
            </nav>
        <?php } ?>

        <div class="container-fluid row mt-2">
            <!-- Sidebar -->
            <div class="col-2">
                <!-- Categories / Folders -->
                <h5 class="card-title">Mail</h5>
                <row>
                    <div class="list-group">
                        <a href="?unread='1'" class="list-group-item d-flex justify-content-between align-items-center list-group-item-action <?php
                        if ($showUnread) {
                            echo "active";
                        }
                        ?>">
                            Unread
                            <span class="badge badge-light badge-pill">
                                <?php
                                echo MailBox::GetUnreadInboxCountForUser($_SESSION['userid']);
                                ?>
                            </span>
                        </a>
                        <a href="?read='1'" class="list-group-item d-flex justify-content-between align-items-center list-group-item-action <?php
                        if ($showRead) {
                            echo "active";
                        }
                        ?>">
                            Read
                        </a>
                        <a href="?sent='1'" class="list-group-item d-flex justify-content-between align-items-center list-group-item-action <?php
                        if ($showSent) {
                            echo "active";
                        }
                        ?>">
                            Sent
                        </a>
                    </div>
                </row>
                <hr>
                <!-- Contacts -->
                <h5 class="card-title">Contacts</h5>
                <row>
                    <div class="list-group contact-list">
                        <?php
                        $allUsers = User::GetAll();
                        foreach ($allUsers as $user) {
                            echo '<a href="view_mailbox.php?to=' . $user->GetId() . '" class="list-group-item d-flex justify-content-between align-items-center list-group-item-action">';
                            echo $user->GetName();
                            echo '</a>';
                        }
                        ?>
                    </div>
                </row>
            </div>
            <!-- Inbox (Filtered Or Send Mail) -->
            <div class="col-10">
                <?php if ($showUnread || $showRead || $showSent) { ?>
                    <h5 class="card-title">Inbox</h5>
                    <?php
                    $allMail;
                    if ($showUnread) {
                        $allMail = MailBox::GetUnreadMailForUser($_SESSION['userid']);
                    } else if ($showRead) {
                        $allMail = MailBox::GetReadMailForUser($_SESSION['userid']);
                    } else if ($showSent) {
                        $allMail = MailBox::GetSentMailForUser($_SESSION['userid']);
                    }
                    ?>

                    <div class="accordion" id="mail-accordion">
                        <?php
                        foreach ($allMail as $mail) {
                            echo '<div class="card">';

                            echo '<div class="card-header" id="mail-header-' . $mail->GetId() . '"';
                            echo '<h2 class="mb-0">';
                            echo '<button class="btn btn-link col-12" type="button" data-toggle="collapse" data-target="#collapse-mail-' . $mail->GetId() . '" aria-expanded="true" aria-controls="collapse-mail-' . $mail->GetId() . '">';
                            echo '<div class="row text-dark">';
                            echo '<div class="col-2 text-left">';
                            $mailUserId = $showSent ? $mail->GetToUserId() : $mail->GetFromUserId();
                            echo User::GetUsernameFromId($mailUserId);
                            echo '</div>';
                            echo '<div class="col-8 text-left">';
                            echo $mail->GetTitle();
                            echo '</div>';
                            echo '<div class="col-2 text-right">';
                            echo $mail->GetSendTime();
                            echo '</div>';
                            echo '</button>';
                            echo '</h2>';
                            echo '</div>';

                            echo '<div id="collapse-mail-' . $mail->GetId() . '" class="collapse" aria-labelledby="mail-header-' . $mail->GetId() . '" data-parent="#mail-accordion"';
                            echo '<div class="card-body">';
                            echo $mail->GetMessage();
                            echo '</div>';
                            echo '</div>';
                        }
                        ?>
                    </div>
                <?php } else if ($showTo) { ?>
                    <h5 class="card-title">New Mail</h5>
                    <form>
                        <div class="form-group">
                            <label for="toUser">Recipient</label>
                            <input type="text" class="form-control" id="toUser" aria-describedby="toUser" disabled value="<?php echo User::GetUsernameFromId($to) ?>">
                        </div>
                        <div class="form-group">
                            <label for="emailTitle">Title</label>
                            <input type="text" class="form-control" id="emailTitle" aria-describedby="emailTitle" placeholder="Enter title">
                        </div>
                        <div class="form-group">
                            <label for="emailMessage">Message</label>
                            <textarea class="form-control" id="emailMessage" aria-describedby="emailMessage" rows="6"></textarea>
                        </div>
                        <button id="send-mail" class="btn btn-primary" type="button">Send</button>
                    </form>
                <?php } ?>
            </div>
        </div>

        <!-- Send Mail Script -->
        <script>
            $(document).ready(function () {
                $('#send-mail').click(function (e) {
                    e.preventDefault();

                    var toUser = $('#toUser').val();
                    var title = $('#emailTitle').val();
                    var message = $('#emailMessage').val();

                    $.ajax({
                        type: "POST",
                        url: "classes/mailBox.php",
                        data: {
                            toUser: toUser,
                            title: title,
                            message: message
                        },
                        success: function (data) {
                            alert("Successfully sent mail to user!");
                        }
                    });
                });
            });
        </script>

        <div class="container-fluid mt-2">
            <hr>
            <div class="col-12 text-left">
                <a class="btn btn-secondary" href="javascript:history.go(-1)">Back</a>
            </div>                         
        </div>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>
    </body>

    <?php
    if ($showUnread) {
        // Clear unread email
        MailBox::SetAllReadForUser($_SESSION['userid']);
    }
    ?>
</html>