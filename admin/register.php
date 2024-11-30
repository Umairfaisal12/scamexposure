<?php
session_start();
error_reporting(0);
include('includes/config.php');

// Redirect to home if logged in
if (!empty($_SESSION['login'])) {
    header("location: index.php");
    exit();
}

if (isset($_POST['signup'])) {
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $email = $_POST['email'];
    $password = md5($_POST['password']);
    $status = 0;

    $sql = "INSERT INTO users(`fname`,`lname`,`email`,`password`,`status`) VALUES(:fname,:lname,:email,:password,:status)";
    $query = $dbh->prepare($sql);
    $query->bindParam(':fname', $fname, PDO::PARAM_STR);
    $query->bindParam(':lname', $lname, PDO::PARAM_STR);
    $query->bindParam(':email', $email, PDO::PARAM_STR);
    $query->bindParam(':password', $password, PDO::PARAM_STR);
    $query->bindParam(':status', $status, PDO::PARAM_STR);
    $query->execute();

    $lastInsertId = $dbh->lastInsertId();
    if ($lastInsertId) {
        echo "<script>alert('Registration successful, wait for approval');document.location = 'login.php'</script>";
    } else {
        echo "<script>alert('Something went wrong');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Register User - Admin</title>

    <!-- Custom fonts and styles -->
    <link href="admin/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

    <!-- JavaScript for validations -->
    <script>
        function validate() {
            const fname = document.signup.fname.value.trim();
            const lname = document.signup.lname.value.trim();
            const email = document.signup.email.value.trim();
            const password = document.signup.password.value.trim();
            const passwordRepeat = document.signup.passwordrepeat.value.trim();

            if (!fname.match(/^[a-zA-Z ]{2,30}$/)) {
                document.getElementById('checkfname').innerHTML = 'Invalid First Name';
                return false;
            }

            if (!lname.match(/^[a-zA-Z]{2,15}$/)) {
                document.getElementById('checklname').innerHTML = 'Invalid Last Name';
                return false;
            }

            if (!email) {
                alert('Email is required');
                return false;
            }

            if (!password.match(/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z]{8,12}$/)) {
                document.getElementById('checkpass').innerHTML = 'Password must be 8-12 chars with uppercase, lowercase & digit';
                return false;
            }

            if (password !== passwordRepeat) {
                document.getElementById('message').innerHTML = 'Passwords do not match';
                return false;
            }

            return true;
        }

        function checkAvailability() {
            $("#loaderIcon").show();
            jQuery.ajax({
                url: "check-availability.php",
                data: { email: $("#email").val() },
                type: "POST",
                success: function (data) {
                    $("#user-availability-status").html(data);
                    $("#loaderIcon").hide();
                },
                error: function () { $("#loaderIcon").hide(); }
            });
        }
    </script>
</head>

<body class="bg-gradient-primary">

    <div class="container">
        <div class="card o-hidden border-0 shadow-lg my-5">
            <div class="card-body p-0">
                <div class="row">
                    <div class="col-lg-5 d-none d-lg-block" style="background-image: url(img/reg4.jpg); background-size: cover; height: 80vh;"></div>
                    <div class="col-lg-7">
                        <div class="p-5">
                            <div class="text-center">
                                <h1 class="h4 text-gray-900 mb-4">Create an Account</h1>
                            </div>
                            <form class="user" method="post" name="signup" onsubmit="return validate();" novalidate>
                                <div class="form-group row">
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <input type="text" id="fname" name="fname" class="form-control form-control-user" placeholder="First Name" onkeyup="checkAvailability();">
                                        <span id="checkfname" style="color: red; font-size: 12px;"></span>
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="text" id="lname" name="lname" class="form-control form-control-user" placeholder="Last Name" onkeyup="checkAvailability();">
                                        <span id="checklname" style="color: red; font-size: 12px;"></span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <input type="email" id="email" name="email" class="form-control form-control-user" placeholder="Email Address" onblur="checkAvailability();">
                                    <span id="user-availability-status" style="font-size: 12px;"></span>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <input type="password" id="password" name="password" class="form-control form-control-user" placeholder="Password">
                                        <span id="checkpass" style="color: red; font-size: 12px;"></span>
                                    </div>
                                    <div class="col-sm-6">
                                        <input type="password" id="confirm_password" name="passwordrepeat" class="form-control form-control-user" placeholder="Repeat Password">
                                        <span id="message" style="font-size: 12px;"></span>
                                    </div>
                                </div>
                                <button type="submit" name="signup" class="btn btn-danger btn-block text-white btn-user">Register Account</button>
                                <hr>
                            </form>
                            <div class="text-center">
                                <a href="index.php" class="btn btn-primary btn-block">Home</a>
                                <a href="login.php" class="btn btn-success btn-block">Already have an account? Login!</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Core JS -->
    <script src="admin/vendor/jquery/jquery.min.js"></script>
    <script src="admin/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="admin/vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="admin/js/sb-admin-2.min.js"></script>

</body>

</html>
