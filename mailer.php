<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . "/vendor/autoload.php";

$mail = new PHPMailer(true);

try {
    // Enable SMTP debugging
    // $mail->SMTPDebug = SMTP::DEBUG_SERVER;  // Uncomment for detailed debug output

    $mail->isSMTP(); // Use SMTP protocol
    $mail->SMTPAuth = true; // Enable SMTP authentication

    $mail->Host = "smtp.sendgrid.net"; // SMTP server to send through
    $mail->Username = "apikey"; // Always 'apikey' for SendGrid
    $mail->Password = "SG.fYBYCkU6SYeNWr~jT3Z_qA.Uw0n7o8dbmd6o1k8yLjcu7D0UK1-UU0Pnbhs42PR_ks"; // SendGrid API key

    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Use TLS encryption
    $mail->Port = 587; // TLS port (you can also try 465 for SSL if needed)

    $mail->isHTML(true); // Set email format to HTML

    // Return the mail object
    return $mail;

} catch (Exception $e) {
    echo "Mailer Error: " . $mail->ErrorInfo;
}

