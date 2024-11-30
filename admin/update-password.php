<?php
session_set_cookie_params(0);
session_start();
include('includes/config.php');
error_reporting(0);

if (strlen($_SESSION['alogin']) == 0) {
    header('location: login.php');
    exit();
}

if (isset($_POST['submit'])) {
    $currentPassword = $_POST['password'];
    $newPassword = $_POST['newpassword'];
    $email = $_SESSION['alogin'];

    try {
        // Fetch the current hashed password from the database
        $sql = "SELECT `password` FROM `admin` WHERE email = :email";
        $query = $dbh->prepare($sql);
        $query->bindParam(':email', $email, PDO::PARAM_STR);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);

        if ($result && password_verify($currentPassword, $result['password'])) {
            // Hash the new password using bcrypt
            $hashedNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            // Update the password in the database
            $updateSql = "UPDATE `admin` SET password = :newpassword WHERE email = :email";
            $updateQuery = $dbh->prepare($updateSql);
            $updateQuery->bindParam(':email', $email, PDO::PARAM_STR);
            $updateQuery->bindParam(':newpassword', $hashedNewPassword, PDO::PARAM_STR);
            $updateQuery->execute();

            echo "<script>alert('Your password has been successfully updated');</script>";
        } else {
            echo "<script>alert('Your current password is incorrect');</script>";
        }
    } catch (PDOException $e) {
        echo "<script>alert('An error occurred: " . $e->getMessage() . "');</script>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Update Password</title>

    <!-- Custom fonts and styles -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

    <!-- Inline JavaScript -->
    <script>
        function validatePasswords() {
            const password = document.getElementById('newpass').value;
            const confirmPassword = document.getElementById('confirmpass').value;

            if (password.length < 8 || password.length > 12) {
                document.getElementById('newpassmsg').textContent = 'Password must be 8-12 characters long.';
                return false;
            }

            if (password !== confirmPassword) {
                document.getElementById('confirmpassmsg').textContent = 'Passwords do not match.';
                return false;
            }

            document.getElementById('newpassmsg').textContent = '';
            document.getElementById('confirmpassmsg').textContent = '';
            return true;
        }
    </script>
</head>

<body id="page-top">
    <div id="wrapper">
        <!-- Sidebar -->
        <?php include "includes/sidebar.php"; ?>
        <!-- End of Sidebar -->

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <!-- Topbar -->
                <?php include "includes/header.php"; ?>
                <!-- End of Topbar -->

                <!-- Page Content -->
                <div class="container-fluid">
                    <h3 class="text-dark mb-4">Update Password</h3>

                    <div class="row mb-3">
                        <div class="col-lg-8">
                            <div class="card shadow mb-3">
                                <div class="card-header py-3">
                                    <p class="text-primary m-0 font-weight-bold">Update Password</p>
                                </div>
                                <div class="card-body">
                                    <form method="post" name="chngpwd" onsubmit="return validatePasswords();">
                                        <div class="form-group">
                                            <label for="pass"><strong>Current Password</strong></label>
                                            <input type="password" class="form-control" id="pass" name="password" required>
                                            <span id="passmsg" class="text-danger"></span>
                                        </div>
                                        <div class="form-group">
                                            <label for="newpass"><strong>New Password</strong></label>
                                            <input type="password" class="form-control" id="newpass" name="newpassword" required>
                                            <span id="newpassmsg" class="text-danger"></span>
                                        </div>
                                        <div class="form-group">
                                            <label for="confirmpass"><strong>Confirm Password</strong></label>
                                            <input type="password" class="form-control" id="confirmpass" name="confirmpassword" required>
                                            <span id="confirmpassmsg" class="text-danger"></span>
                                        </div>
                                        <button type="submit" name="submit" class="btn btn-primary">Update</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.container-fluid -->
            </div>
            <!-- Footer -->
            <?php include "includes/footer.php"; ?>
        </div>
    </div>

    <!-- Scroll to Top Button -->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal -->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="logout.php">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Core JavaScript -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
</body>

</html>
