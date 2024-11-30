<?php
session_start();
include('includes/config.php');

// Redirect to dashboard if already logged in
if (!empty($_SESSION['alogin'])) {
    header("location: index.php");
    exit();
}

// Handle login
if (isset($_POST['login'])) {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    try {
        // Fetch user record
        $sql = "SELECT email, password FROM admin WHERE email = :email LIMIT 1";
        $query = $dbh->prepare($sql);
        $query->bindParam(':email', $email, PDO::PARAM_STR);
        $query->execute();
        $user = $query->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Password verified
            $_SESSION['alogin'] = $email;
            echo "<script>document.location = 'index.php';</script>";
        } else {
            // Invalid credentials
            echo "<script>alert('Invalid login credentials');</script>";
        }
    } catch (PDOException $e) {
        // Log the error message (not shown to the user)
        error_log("Database error: " . $e->getMessage());
        echo "<script>alert('An error occurred. Please try again later.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Admin Login">
    <meta name="author" content="">

    <title>Admin Login</title>

    <!-- Fonts and Styles -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

    <!-- Validation Script -->
    <script>
        function validate() {
            const email = document.userlogin.email.value;
            const password = document.userlogin.password.value;
            let isValid = true;

            // Email validation
            if (!email) {
                document.getElementById('emailcheck').innerHTML = 'Enter your email address';
                isValid = false;
            } else {
                const mailformat = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!mailformat.test(email)) {
                    document.getElementById('emailcheck').innerHTML = 'Enter a valid email address';
                    isValid = false;
                } else {
                    document.getElementById('emailcheck').innerHTML = '';
                }
            }

            // Password validation
            if (!password) {
                document.getElementById('passwordcheck').innerHTML = 'Enter your password';
                isValid = false;
            } else {
                document.getElementById('passwordcheck').innerHTML = '';
            }

            return isValid;
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
                            <div class="col-lg-6 d-none d-lg-block" 
                                 style="background-image: url(img/admin.jpg); background-size: cover; background-position: center;">
                            </div>
                            
                            <div class="col-lg-6">
                            <br/>
                            <br/><br/>
                            
                            <br/>
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">Admin Login</h1>
                                    </div>
                                    
                                    <form class="user" method="post" name="userlogin" onsubmit="return validate();" novalidate>
                                        <div class="form-group">
                                            <input type="email" class="form-control form-control-user" id="email" name="email" placeholder="Enter Email Address...">
                                            <span id="emailcheck" style="font-size: 12px; color: red;"></span>
                                        </div>
                                        <div class="form-group">
                                            <input type="password" class="form-control form-control-user" id="password" name="password" placeholder="Password">
                                            <span id="passwordcheck" style="font-size: 12px; color: red;"></span>
                                        </div>
                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox small">
                                                <input type="checkbox" class="custom-control-input" id="customCheck">
                                                <label class="custom-control-label" for="customCheck">Remember Me</label>
                                            </div>
                                        </div>
                                        <button type="submit" name="login" class="btn btn-primary btn-user btn-block">
                                            Login
                                        </button>
                                    </form>
                                    <hr>
                                    <div class="text-center">
                                        <a class="small" href="forgot-password.php">Forgot Password?</a>
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
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>

</body>

</html>
