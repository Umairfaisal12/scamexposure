<?php
// Database connection
require 'includes/config.php';

// Initialize variables
$message = '';
$existing_questions = [];

// Default questions
$default_questions = [
    "What is the company's registered trademark number?",
    "What was the year the company was founded?",
    "What is the company's official registration ID?",
    "Where was the company first started in?",
    "What is the name of the company's first major client?"
];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate input
        $questions = [];
        $answers = [];

        // Process each security question and answer
        for ($i = 1; $i <= 5; $i++) {
            $question = trim($_POST["question_$i"] ?? '');
            $answer = trim($_POST["answer_$i"] ?? '');

            if (empty($question) || empty($answer)) {
                throw new Exception("All questions and answers are required.");
            }

            $questions[] = htmlspecialchars($question, ENT_QUOTES, 'UTF-8');
            $answers[] = password_hash($answer, PASSWORD_BCRYPT);
        }

        // Begin database transaction
        $dbh->beginTransaction();

        // Update security_questions table
        $stmt = $dbh->prepare("
            INSERT INTO security_questions (question, answer_hash)
            VALUES (:question, :answer_hash)
            ON DUPLICATE KEY UPDATE answer_hash = :answer_hash
        ");

        foreach ($questions as $index => $question) {
            $result = $stmt->execute([
                ':question' => $question,
                ':answer_hash' => $answers[$index]
            ]);

            if (!$result) {
                throw new Exception("Failed to update security questions.");
            }
        }

        // Update admin table with security answers
        $admin_stmt = $dbh->prepare("
            UPDATE admin 
            SET 
                security_answer_hash_1 = :hash1,
                security_answer_hash_2 = :hash2,
                security_answer_hash_3 = :hash3,
                security_answer_hash_4 = :hash4,
                security_answer_hash_5 = :hash5,
                updationdate = NOW()
            WHERE username = :username
        ");

        $admin_result = $admin_stmt->execute([
            ':hash1' => $answers[0],
            ':hash2' => $answers[1],
            ':hash3' => $answers[2],
            ':hash4' => $answers[3],
            ':hash5' => $answers[4],
            ':username' => 'admin' // Adjust if using dynamic usernames
        ]);

        if (!$admin_result) {
            throw new Exception("Failed to update admin table.");
        }

        // Commit transaction
        $dbh->commit();
        $message = "Security questions updated successfully!";
    } catch (PDOException $e) {
        // Rollback transaction on error
        if ($dbh->inTransaction()) {
            $dbh->rollBack();
        }
        $message = "Database error: " . $e->getMessage();
    } catch (Exception $e) {
        $message = $e->getMessage();
    }
}

// Fetch existing questions
try {
    $stmt = $dbh->query("SELECT question FROM security_questions LIMIT 5");
    $existing_questions = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    $message = "Error fetching existing questions: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Security Questions - ScamExposure</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-primary">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-12 col-md-9">
                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <div class="p-5">
                            <h1 class="h4 text-gray-900 mb-4 text-center">Manage Security Questions</h1>
                            
                            <?php if ($message): ?>
                                <div class="alert <?= strpos($message, 'successfully') !== false ? 'alert-success' : 'alert-danger' ?> text-center">
                                    <?= htmlspecialchars($message) ?>
                                </div>
                            <?php endif; ?>

                            <form method="POST" action="">
                                <?php for ($i = 0; $i < 5; $i++): 
                                    $key = $i + 1;
                                    $question = $existing_questions[$i] ?? $default_questions[$i];
                                ?>
                                    <div class="form-group">
                                        <label for="question_<?= $key ?>">Question <?= $key ?></label>
                                        <input type="text" 
                                               id="question_<?= $key ?>" 
                                               class="form-control" 
                                               name="question_<?= $key ?>" 
                                               value="<?= htmlspecialchars($question, ENT_QUOTES) ?>" 
                                               readonly>
                                    </div>
                                    <div class="form-group">
                                        <input type="text" 
                                               class="form-control" 
                                               name="answer_<?= $key ?>" 
                                               placeholder="Enter Answer for Question <?= $key ?>" 
                                               required 
                                               maxlength="255">
                                    </div>
                                <?php endfor; ?>

                                <button type="submit" class="btn btn-primary btn-user btn-block">
                                    Save Security Questions
                                </button>
                            </form>

                            <div class="text-center mt-3">
                                <a href="index.php" class="btn btn-secondary btn-user">
                                    Back to Dashboard
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
</body>
</html>
