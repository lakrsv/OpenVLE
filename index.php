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

        <title>OpenVLE - Your Gateway to Learning</title>
    </head>
    <body>
        <!-- Log In Container -->
        <div class="container">
            <div class="row">
                <div class="col-xl-12 col-centered">
                    <!-- Logo Row -->
                    <div class="row justify-content-center">
                        <img src="img/openvle.png" class="rounded mx-auto d-block" alt="OpenVLE Logo">
                    </div>

                    <div class="row justify-content-center">
                        <form id="login" method="post">
                            <div class="form-group">
                                <label for="username">Email Address</label>
                                <input type="email" class="form-control" id="username" placeholder="Enter email">
                            </div>
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" id="password" placeholder="Your password">
                            </div>
                            <button type="submit" class="btn btn-primary">Log In</button>
                        </form>

                        <script>
                            $(document).ready(function () {
                                $('#login').submit(function (e) {
                                    e.preventDefault();
                                    $.ajax({
                                        type: "POST",
                                        url: "auth/login.php",
                                        data: {
                                            username: $('#username').val(),
                                            password: $('#password').val()
                                        },
                                        success: function (data) {
                                            if (data === "Success") {
                                                window.location = 'user-home.php';
                                            } else {
                                                alert("Invalid Username/Password");
                                            }
                                        }
                                    });
                                });
                            });
                        </script>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>
    </body>
</html>