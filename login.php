<?php
session_start();
error_reporting(0);
include('includes/config.php');

// Redirect logged-in users to the index page
if (!empty($_SESSION['login'])) {
    header("location: index.php");
    exit();
}

// Handle login form submission
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password']; // User-entered password
    $status = 1;

    // Query user details from the database
    $sql = "SELECT email, password FROM users WHERE email = :email AND status = :status";
    $query = $dbh->prepare($sql);
    $query->bindParam(':email', $email, PDO::PARAM_STR);
    $query->bindParam(':status', $status, PDO::PARAM_INT);
    $query->execute();

    if ($query->rowCount() > 0) {
        $user = $query->fetch(PDO::FETCH_ASSOC);
        $stored_password = $user['password'];

        // Verify password with bcrypt
        if (password_verify($password, $stored_password)) {
            // Set session and redirect user
            $_SESSION['login'] = $email;
            $currentpage = !empty($_SESSION['redirectURL']) ? $_SESSION['redirectURL'] : 'index.php';
            echo "<script type='text/javascript'> document.location = '$currentpage'; </script>";
            exit();
        } else {
            echo "<script>alert('Invalid login credentials.');</script>";
        }
    } else {
        echo "<script>alert('Invalid login credentials.');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Login - User</title>

    <!-- Custom fonts and styles -->
    <link href="admin/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">
    <link href="admin/css/sb-admin-2.min.css" rel="stylesheet">

    <script type="text/javascript">
        function validate() {
            let email = document.userlogin.email.value;
            let pass = document.userlogin.password.value;

            if (!email && !pass) {
                document.getElementById('emailcheck').innerHTML = 'Enter your email address';
                document.getElementById('passwordcheck').innerHTML = 'Enter your password';
                return false;
            }
            if (!email) {
                document.getElementById('emailcheck').innerHTML = 'Enter your email address';
                document.userlogin.email.focus();
                return false;
            }
            const mailformat = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!email.match(mailformat)) {
                document.getElementById('emailcheck').innerHTML = 'Enter a correct email address';
                document.userlogin.email.focus();
                return false;
            }
            if (!pass) {
                document.getElementById('passwordcheck').innerHTML = 'Enter your password';
                document.userlogin.password.focus();
                return false;
            }
            return true;
        }
    </script>
</head>

<body class="bg-gradient-primary">
    <div class="container">
        <!-- Outer Row -->
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-12 col-md-9">
                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <!-- Nested Row -->
                        <div class="row">
                            <div class="col-lg-6 d-none d-lg-block" style="background-image: url(admin/img/user1.jpg); background-size: contain; background-repeat: no-repeat; width: 400px; height: 400px; margin: 150px auto 0 auto;"></div>
                            <div class="col-lg-6">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">User Login</h1>
                                    </div>
                                    <form class="user" method="post" name="userlogin" onsubmit="return validate();" novalidate>
                                        <div class="form-group">
                                            <input type="email" class="form-control form-control-user" name="email" autocomplete="off" placeholder="Enter Email Address...">
                                            <span id="emailcheck" style="font-size: 12px; color: red;"></span>
                                        </div>
                                        <div class="form-group" style="position: relative;">
                                            <input type="password" class="form-control form-control-user" id="password" name="password" autocomplete="off" placeholder="Password" style="padding-right: 40px;">
                                            <i id="togglePassword" class="fas fa-eye" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #aaa;"></i>
                                            <span id="passwordcheck" style="font-size: 12px; color: red;"></span>
                                        </div>
                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox small">
                                                <input type="checkbox" class="custom-control-input" id="customCheck">
                                                <label class="custom-control-label" for="customCheck">Remember Me</label>
                                            </div>
                                        </div>
                                        <button class="btn btn-success btn-block text-white btn-user" type="submit" name="login">Login</button>
                                        <hr>
                                    </form>
                                    <div class="text-center">
                                        <a href="index.php" class="btn btn-primary btn-block text-white btn-user">Home</a><br/>
                                    </div>
                                    <div class="text-center">
                                        <a href="view-posts.php" class="btn btn-info btn-block text-white btn-user">View Posts</a><br/>
                                    </div>
                                    <div class="text-center">
                                        <a href="forgot-password.php" class="btn btn-warning btn-block text-black-50 btn-user">Forgot Password?</a><br/>
                                    </div>
                                    <div class="text-center">
                                        <a href="register.php" class="btn btn-danger btn-block text-white btn-user">Don't have an account? Register</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="admin/vendor/jquery/jquery.min.js"></script>
    <script src="admin/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="admin/vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="admin/js/sb-admin-2.min.js"></script>
    <script>
        // Toggle password visibility
        const passwordInput = document.getElementById('password');
        const togglePassword = document.getElementById('togglePassword');

        togglePassword.addEventListener('click', function () {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    </script>
</body>

</html>
