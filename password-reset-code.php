<?php
ob_start(); // Start output buffering
session_start();
include('includes/config.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require 'vendor/autoload.php';

if (isset($_POST['password_reset_link'])) {
    try {
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        
        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format.");
        }
        
        // Generate secure token and expiry time
        $token = bin2hex(random_bytes(16));
        $expiry = date("Y-m-d H:i:s", strtotime("+1 hour"));
        
        // Check if the email exists and is active
        $check_email_query = "SELECT fname, email FROM users WHERE email = :email AND status = 1 LIMIT 1";
        $stmt = $dbh->prepare($check_email_query);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $fname = $row['fname'];
            
            // Update the reset token and expiry time
            $update_token_query = "UPDATE users SET reset_token_hash = :token, reset_token_expires_at = :expiry WHERE email = :email";
            $update_stmt = $dbh->prepare($update_token_query);
            $update_stmt->bindParam(':token', $token, PDO::PARAM_STR);
            $update_stmt->bindParam(':expiry', $expiry, PDO::PARAM_STR);
            $update_stmt->bindParam(':email', $email, PDO::PARAM_STR);
            
            if ($update_stmt->execute()) {
                if (send_password_reset_link($fname, $email, $token)) {
                    $_SESSION['status'] = "Password reset link has been sent to your email!";
                } else {
                    throw new Exception("Failed to send password reset email.");
                }
            } else {
                throw new Exception("Failed to update reset token.");
            }
        } else {
            throw new Exception("Email not found or account is inactive.");
        }
    } catch (Exception $e) {
        error_log("Password Reset Error: " . $e->getMessage());
        $_SESSION['status'] = $e->getMessage();
    }
    
    // Clear output buffer before redirect
    ob_end_clean();
    header("Location: forgot-password.php");
    exit();
}

function send_password_reset_link($fname, $email, $token) {
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'umairfaisal785@gmail.com'; // Your Gmail address
        $mail->Password = 'zjvu thki ccre liir'; // Your Gmail App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;
        
        // Recipients
        $mail->setFrom('umairfaisal785@gmail.com', 'SCMEX Support'); // Update with your website name
        $mail->addAddress($email, $fname);
        
        // Email subject and body content
        $mail->isHTML(true);
        $mail->Subject = 'Password Reset Request - SCMEX'; // Update with your website name
        
        // Create reset link
        $resetLink = "http://localhost/scmex/reset-password.php?token=" . urlencode($token) . "&email=" . urlencode($email);
        
        // HTML email content
        $mail->Body = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #eee; border-radius: 5px;'>
                <div style='text-align: center; margin-bottom: 20px;'>
                    <h2 style='color: #333;'>Password Reset Request</h2>
                </div>
                <div style='margin-bottom: 30px;'>
                    <h3 style='color: #444;'>Hello {$fname},</h3>
                    <p style='color: #666; line-height: 1.6;'>We received a request to reset your password. If you didn't make this request, you can safely ignore this email.</p>
                </div>
                <div style='text-align: center; margin: 30px 0;'>
                    <a href='{$resetLink}' 
                       style='background-color: #007bff; 
                              color: white; 
                              padding: 12px 30px; 
                              text-decoration: none; 
                              border-radius: 5px; 
                              display: inline-block;
                              font-weight: bold;
                              border: none;
                              box-shadow: 0 2px 4px rgba(0,0,0,0.1);'>
                        Reset Password
                    </a>
                </div>
                <div style='margin: 30px 0; padding: 15px; background-color: #f8f9fa; border-radius: 5px;'>
                    <p style='margin: 0; color: #666;'><strong>Note:</strong> This link will expire in 1 hour for security reasons.</p>
                </div>
                <div style='margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee;'>
                    <p style='color: #666; font-size: 14px;'>If the button above doesn't work, copy and paste this link into your browser:</p>
                    <p style='word-break: break-all; color: #007bff; font-size: 14px;'>{$resetLink}</p>
                </div>
                <div style='margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee;'>
                    <p style='color: #999; font-size: 12px; text-align: center;'>
                        If you didn't request a password reset, please ignore this email or contact support if you have concerns.
                    </p>
                </div>
            </div>
        ";
        
        // Plain text alternative
        $mail->AltBody = "Hello {$fname},\n\n" .
                        "We received a request to reset your password.\n\n" .
                        "Click the following link to reset your password: {$resetLink}\n\n" .
                        "This link will expire in 1 hour.\n\n" .
                        "If you didn't request this, please ignore this email or contact support.";
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mailer Error: " . $mail->ErrorInfo);
        return false;
    }
}

// Flush any remaining output buffer at the end
ob_end_flush();
?>
