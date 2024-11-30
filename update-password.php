<?php
session_set_cookie_params(0);
session_start();
include('includes/config.php');
error_reporting(0);
if (strlen($_SESSION['login']) == 0) {
    header('location: login.php');
} else {
    if (isset($_POST['submit'])) {
        $currentPassword = $_POST['password'];
        $newPassword = $_POST['newpassword'];
        $confirmPassword = $_POST['confirmpassword'];

        if ($newPassword !== $confirmPassword) {
            echo "<script>alert('New password and Confirm password do not match.');document.location = 'update-password.php';</script>";
            exit();
        }

        $email = $_SESSION['login'];

        // Fetch the stored hashed password
        $sql = "SELECT `password` FROM `users` WHERE email=:email";
        $query = $dbh->prepare($sql);
        $query->bindParam(':email', $email, PDO::PARAM_STR);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_OBJ);

        if ($result && password_verify($currentPassword, $result->password)) {
            // Update the password
            $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);
            $updateSql = "UPDATE `users` SET password=:newpassword WHERE email=:email";
            $updateQuery = $dbh->prepare($updateSql);
            $updateQuery->bindParam(':email', $email, PDO::PARAM_STR);
            $updateQuery->bindParam(':newpassword', $newPasswordHash, PDO::PARAM_STR);
            $updateQuery->execute();

            echo "<script>alert('Your password has been successfully updated.');document.location = 'index.php';</script>";
        } else {
            echo "<script>alert('Your current password is incorrect.');document.location = 'update-password.php';</script>";
        }
    }
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Update Password</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lora:400,700,400italic,700italic">
    <link rel="stylesheet" href="assets/fonts/font-awesome.min.css">
    <script>
        var checkPass = function () {
            var password = document.getElementById('newpass').value;
            var repassword = document.getElementById('confirmpass').value;
            var regexpass = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z]{8,12}$/;
            if (password !== "" || password !== null) {
                if (password.match(regexpass)) {
                    document.getElementById('newpassmsg').innerHTML = '';
                    document.getElementById('submit').disabled = false;
                    if (password === repassword) {
                        document.getElementById('confirmpassmsg').style.color = 'green';
                        document.getElementById('confirmpassmsg').innerHTML = 'Password matched';
                        document.getElementById('submit').disabled = false;
                    } else {
                        document.getElementById('confirmpassmsg').style.color = 'red';
                        document.getElementById('confirmpassmsg').innerHTML = 'Password not matching';
                        document.getElementById('submit').disabled = true;
                    }
                } else {
                    document.getElementById('newpassmsg').innerHTML = 'Minimum len 8 & max len 12 where 1 uppercase & 1 digit mandatory';
                    document.getElementById('submit').disabled = true;
                }
            } else {
                document.getElementById('newpassmsg').innerHTML = 'Empty password';
                document.getElementById('submit').disabled = true;
            }
        };
    </script>
</head>

<body>
    <!-- Header -->
    <?php include 'includes/header.php'; ?>

    <header class="masthead" style="background-image:url('assets/img/home1.jpg');">
        <div class="overlay"></div>
        <div class="container">
            <div class="row">
                <div class="col-md-10 col-lg-8 mx-auto">
                    <div class="site-heading">
                        <h1>Scam Exposure Site</h1>
                        <span class="subheading">Let's say no to scams</span>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- Header -->

    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="card-box">
                    <h4 class="m-t-0 header-title"><b>Update Password</b></h4>
                    <hr/>

                    <div class="row">
                        <div class="col-md-10">
                            <form class="form-horizontal" name="chngpwd" method="post" novalidate>

                                <div class="form-group">
                                    <label for="pass" class="col-md-4 control-label">Current Password</label>
                                    <div class="col-md-4">
                                        <input id="pass" type="password" class="form-control" name="password" autocomplete="off" required>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="newpass" class="col-md-4 control-label">New Password</label>
                                    <div class="col-md-4">
                                        <input id="newpass" type="password" class="form-control" name="newpassword" autocomplete="off" onkeyup="checkPass();" required>
                                        <span id="newpassmsg" style="font-size: 12px;"></span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="confirmpass" class="col-md-4 control-label">Confirm Password</label>
                                    <div class="col-md-4">
                                        <input id="confirmpass" type="password" class="form-control" autocomplete="off" name="confirmpassword" onkeyup="checkPass();" required>
                                        <span id="confirmpassmsg" style="font-size: 12px;"></span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-md-4">
                                        <button type="submit" id="submit" class="btn btn-danger float-right" name="submit">Submit</button>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/clean-blog.js"></script>
</body>

</html>
<?php } ?>
