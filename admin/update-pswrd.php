<?php
session_start();
include('includes/config.php');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to log errors
function logError($message) {
    error_log(date('[Y-m-d H:i:s] ') . $message . "\n", 3, 'password_reset_errors.log');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Retrieve inputs
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $token = htmlspecialchars($_POST['token'], ENT_QUOTES, 'UTF-8');
        $new_password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        // Validate inputs
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format.");
        }

        if (empty($token)) {
            throw new Exception("Invalid or missing reset token.");
        }

        if ($new_password !== $confirm_password) {
            throw new Exception("Passwords do not match.");
        }

        if (strlen($new_password) < 8) {
            throw new Exception("Password must be at least 8 characters long.");
        }

        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/', $new_password)) {
            throw new Exception("Password must contain at least one uppercase letter, one lowercase letter, and one number.");
        }

        // Check if the token and email are valid and not expired
        $query = "SELECT id, reset_token_expires_at FROM admin WHERE email = :email AND reset_token_hash = :token LIMIT 1";
        $stmt = $dbh->prepare($query);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':token', $token, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            throw new Exception("Invalid or expired reset link.");
        }

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check token expiration
        if (strtotime($user['reset_token_expires_at']) < time()) {
            throw new Exception("Reset link has expired. Please request a new one.");
        }

        // Hash the new password
        $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);

        // Update the user's password and invalidate the reset token
        $update_query = "
            UPDATE admin SET 
                password = :password, 
                reset_token_hash = NULL, 
                reset_token_expires_at = NULL, 
                updationdate = NOW() 
            WHERE id = :id
        ";
        $update_stmt = $dbh->prepare($update_query);
        $update_stmt->bindParam(':password', $new_password_hash, PDO::PARAM_STR);
        $update_stmt->bindParam(':id', $user['id'], PDO::PARAM_INT);

        if ($update_stmt->execute()) {
            session_destroy();
            echo "<script>alert('Password successfully reset. You can now log in with your new password.');window.location.href='login.php';</script>";
            exit();
        } else {
            logError("Failed to update password. Error: " . implode(', ', $update_stmt->errorInfo()));
            throw new Exception("Password reset failed. Please try again.");
        }
    } catch (Exception $e) {
        logError($e->getMessage());
        $_SESSION['status'] = $e->getMessage();
        header("Location: reset-password.php?email=" . urlencode($_POST['email']) . "&token=" . urlencode($_POST['token']));
        exit();
    }
} else {
    $_SESSION['status'] = "Unauthorized access.";
    header("Location: forgot-password.php");
    exit();
}
?>
