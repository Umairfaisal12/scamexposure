<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <style>
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

        .forgot-password-container {
            background: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        h1 {
            font-size: 24px;
            color: #333333;
            margin-bottom: 20px;
        }

        .status-message {
            font-weight: bold;
            margin-bottom: 10px;
        }

        .status-message.success {
            color: green;
        }

        .status-message.error {
            color: red;
        }

        label {
            font-weight: bold;
            color: #555555;
            display: block;
            margin-bottom: 8px;
            text-align: left;
        }

        input[type="email"] {
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

        .forgot-password-container p {
            color: #777777;
            font-size: 14px;
            margin-top: 10px;
        }

        .forgot-password-container p a {
            color: #007BFF;
            text-decoration: none;
            display: inline-block;
            margin-top: 5px;
        }

        .forgot-password-container p a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="forgot-password-container">
        <h1>Forgot Password</h1>

        <?php
        session_start();
        if (isset($_SESSION['status'])) {
            $status_class = strpos($_SESSION['status'], "email") !== false ? "success" : "error";
            echo '<p class="status-message ' . $status_class . '">' . htmlspecialchars($_SESSION['status']) . '</p>';
            unset($_SESSION['status']);
        }
        ?>

        <form action="password-reset-code.php" method="post">
            <input type="hidden" name="csrf_token" value="<?php echo bin2hex(random_bytes(32)); ?>">
            <label for="email">Email Address</label>
            <input type="email" name="email" id="email" placeholder="Enter your email" required pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" title="Enter a valid email address" autocomplete="off" aria-label="Email Address">
            <button type="submit" name="password_reset_link">Send Reset Link</button>
        </form>

        <p>Remember your password? <a href="login.php">Log in</a></p>
        <p>Don't have an account? <a href="register.php">Sign up</a></p>
        <p>Back to <a href="index.php">Home</a></p>
    </div>
</body>

</html>
