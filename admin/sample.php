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
    $password = md5($_POST['password']);
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
