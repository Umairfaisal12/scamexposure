<?php
// Start session to handle user interactions if needed
session_start();

$email = $_POST["email"];

// Generate a random token
$token = bin2hex(random_bytes(16));

// Hash the token for secure storage
$token_hash = hash("sha256", $token);

// Set expiration time for the reset token (30 minutes from now)
$expiry = date("Y-m-d H:i:s", time() + 60 * 30);

// Include the database connection
$mysqli = require __DIR__ . "/database.php";

// Prepare the SQL query to store the token and expiry in the database
$sql = "UPDATE users
        SET reset_token_hash = ?,
            reset_token_expires_at = ?
        WHERE email = ?";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("sss", $token_hash, $expiry, $email);
$stmt->execute();

// Check if the query affected any rows (meaning the email exists)
if ($mysqli->affected_rows) {
    // Send the email with the reset link
    $mail = require __DIR__ . "/mailer.php";

    $mail->setFrom("noreply@example.com");
    $mail->addAddress($email); // Send email to the user's address
    $mail->Subject = "Password Reset";
    $mail->Body = <<<END
    Click <a href="http://example.com/reset-password.php?token=$token">here</a> 
    to reset your password.
    END;

    // Try to send the email, and catch any potential errors
    try {
        $mail->send();
        echo "Message sent, please check your inbox.";
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer error: {$mail->ErrorInfo}";
    }
} else {
    // No user found with that email
    echo "No account found with that email.";
}
