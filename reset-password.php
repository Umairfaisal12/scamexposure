<?php
session_start();
include('includes/config.php'); // Include the PDO connection

if (isset($_GET['token']) && isset($_GET['email'])) {
    $email = filter_var($_GET['email'], FILTER_SANITIZE_EMAIL);
    $token = htmlspecialchars($_GET['token'], ENT_QUOTES, 'UTF-8');

    // Validate email and token
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || empty($token)) {
        $_SESSION['status'] = "Invalid link.";
        header("Location: forgot-password.php");
        exit();
    }

    // Check if the token and email are valid and not expired
    $check_token_query = "
        SELECT reset_token_expires_at 
        FROM users 
        WHERE email = :email AND reset_token_hash = :token 
        LIMIT 1
    ";
    $stmt = $dbh->prepare($check_token_query);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':token', $token, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $expiry = $row['reset_token_expires_at'];

        // Check if the token has expired
        if (strtotime($expiry) < time()) {
            $_SESSION['status'] = "Reset link expired. Please request a new one.";
            header("Location: forgot-password.php");
            exit();
        }
    } else {
        $_SESSION['status'] = "Invalid or expired reset link.";
        header("Location: forgot-password.php");
        exit();
    }
} else {
    $_SESSION['status'] = "Unauthorized access.";
    header("Location: forgot-password.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Password</title>
    <style>
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        /* Container Styles */
        .reset-password-container {
            background: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        /* Text Styles */
        h1 {
            font-size: 24px;
            color: #333333;
            margin-bottom: 20px;
        }

        label {
            font-weight: bold;
            color: #555555;
            display: block;
            margin-bottom: 8px;
            text-align: left;
        }

        p {
            color: #777777;
            font-size: 14px;
        }

        .error-message {
            color: #ff0000;
            font-size: 14px;
            margin-bottom: 20px;
        }

        /* Input and Button Styles */
        input[type="password"], input[type="email"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #dddddd;
            border-radius: 4px;
            font-size: 16px;
        }

        button {
            background-color: #007BFF;
            color: #ffffff;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <div class="reset-password-container">
        <h1>Create New Password</h1>

        <?php if (isset($_SESSION['status'])): ?>
            <p class="error-message"><?= htmlspecialchars($_SESSION['status']) ?></p>
            <?php unset($_SESSION['status']); ?>
        <?php endif; ?>

        <form action="update-pswrd.php" method="post" onsubmit="return validatePasswords();">
            <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

            <label for="email">Email Address</label>
            <input type="email" name="email" id="email" value="<?= htmlspecialchars($email) ?>" readonly>

            <label for="password">New Password</label>
            <input type="password" name="password" id="password" placeholder="Enter new password" required>

            <label for="confirm_password">Confirm Password</label>
            <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm new password" required>

            <button type="submit" name="update_password_btn">Reset Password</button>
        </form>
    </div>

    <script>
        // Validate passwords on client side
        function validatePasswords() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;

            if (password.length < 8) {
                alert('Password must be at least 8 characters long.');
                return false;
            }

            if (password !== confirmPassword) {
                alert('Passwords do not match.');
                return false;
            }

            return true;
        }
    </script>
</body>

</html>
