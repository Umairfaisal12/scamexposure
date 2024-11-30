<?php
// Database connection
require 'includes/config.php';

$message = '';
$show_email_option = false;
$security_questions = [];

try {
    // Fetch all security questions from the database
    $stmt = $dbh->prepare("SELECT id, question FROM security_questions ORDER BY id ASC");
    $stmt->execute();
    $security_questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $message = "Error fetching security questions: " . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $security_answers = [
        $_POST['security_answer_1'],
        $_POST['security_answer_2'],
        $_POST['security_answer_3'],
        $_POST['security_answer_4'],
        $_POST['security_answer_5']
    ];

    try {
        // Fetch user data using PDO
        $stmt = $dbh->prepare("
            SELECT 
                   security_answer_hash_1, 
                   security_answer_hash_2, 
                   security_answer_hash_3, 
                   security_answer_hash_4, 
                   security_answer_hash_5 
            FROM admin 
            WHERE email = :email
        ");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Array of stored hashes (for each question)
            $stored_answers = [
                $user['security_answer_hash_1'],
                $user['security_answer_hash_2'],
                $user['security_answer_hash_3'],
                $user['security_answer_hash_4'],
                $user['security_answer_hash_5']
            ];

            // Count correct answers
            $correct_answers = 0;
            foreach ($stored_answers as $index => $stored_hash) {
                if (!empty($stored_hash) &&
                    isset($security_answers[$index]) && 
                    password_verify($security_answers[$index], $stored_hash)) {
                    $correct_answers++;
                }
            }

            // Check if at least 4 answers are correct
            if ($correct_answers >= 4) {
                $show_email_option = true;
                $message = "Verification successful! You can now send a password reset email.";
            } else {
                $message = "At least 4 correct answers are required to proceed. Try again.";
            }
        } else {
            $message = "No account found with this email.";
        }
    } catch (PDOException $e) {
        $message = "Database error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recover Password</title>
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card mt-5 shadow-lg">
                    <div class="card-header text-center">
                        <h4>Recover Your Password</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($message): ?>
                            <div class="alert <?= $show_email_option ? 'alert-success' : 'alert-danger' ?>">
                                <?= htmlspecialchars($message); ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($show_email_option): ?>
                            <form action="send-reset-email.php" method="POST">
                                <input type="hidden" name="email" value="<?= htmlspecialchars($email); ?>">
                                <button type="submit" class="btn btn-primary btn-block">Send Password Reset Email</button>
                            </form>
                        <?php else: ?>
                            <form method="POST" action="">
                                <div class="form-group">
                                    <input type="email" name="email" class="form-control" placeholder="Enter Email Address" required>
                                </div>
                                <h5>Answer Security Questions</h5>
                                <?php foreach ($security_questions as $index => $question): ?>
                                    <div class="form-group">
                                        <label><?= htmlspecialchars($question['question']) ?></label>
                                        <input type="text" name="security_answer_<?= $index + 1 ?>" class="form-control" required>
                                    </div>
                                <?php endforeach; ?>
                                <button type="submit" class="btn btn-primary btn-block">Verify</button>
                            </form>
                        <?php endif; ?>

                        <div class="text-center mt-3">
                            <a href="login.php" class="small">Back to Login</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
