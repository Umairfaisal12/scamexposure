<?php
session_set_cookie_params(0);
session_start();
error_reporting(0);

// Include configuration
include('includes/config.php');

// Save the current URL for potential redirection
$_SESSION['redirectURL'] = $_SERVER['REQUEST_URI'];

// Handle contact form submission
if (isset($_POST['send'])) {
    $uname = $_POST['uname'];
    $email = $_POST['email'];
    $contact = $_POST['contact'];
    $message = $_POST['message'];

    // Insert form data into the database
    $sql = "INSERT INTO contactusquery(`uname`, `email`, `contact`, `message`) VALUES(:uname, :email, :contact, :message)";
    $query = $dbh->prepare($sql);
    $query->bindParam(':uname', $uname, PDO::PARAM_STR);
    $query->bindParam(':email', $email, PDO::PARAM_STR);
    $query->bindParam(':contact', $contact, PDO::PARAM_STR);
    $query->bindParam(':message', $message, PDO::PARAM_STR);
    $query->execute();

    $lastInsertId = $dbh->lastInsertId();
    if ($lastInsertId) {
        echo "<script>alert('Form successfully submitted. We\'ll contact you soon');</script>";
    } else {
        echo "<script>alert('An error occurred. Try again');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Contact Us</title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lora:400,700,400italic,700italic">
    <link rel="stylesheet" href="assets/fonts/font-awesome.min.css">
</head>

<body>
    <!-- Header -->
    <?php include 'includes/header.php'; ?>

    <header class="masthead" style="background-image:url('assets/img/home4.jpg');">
        <div class="overlay"></div>
        <div class="container">
            <div class="row">
                <div class="col-md-10 col-lg-8 mx-auto">
                    <div class="site-heading">
                        <h1>Scam Exposure Site</h1>
                        <span class="subheading">Let's say no to scams</span>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-lg-8 mx-auto">
                <h2 class="post-title">Contact Us</h2>
                <form id="contactForm" name="sentMessage" action="https://api.web3forms.com/submit" method="POST">
                    <input type="hidden" name="access_key" value="6b90204f-b878-44fc-a6f2-b4221e6c5b36">
                    
                    <!-- Name Field -->
                    <div class="control-group">
                        <div class="form-group floating-label-form-group controls">
                            <label for="name">Name</label>
                            <input class="form-control" type="text" id="name" name="uname" placeholder="Name" required>
                            <small class="form-text text-danger help-block"></small>
                        </div>
                    </div>
                    
                    <!-- Email Field -->
                    <div class="control-group">
                        <div class="form-group floating-label-form-group controls">
                            <label for="email">Email Address</label>
                            <input class="form-control" type="email" id="email" name="email" placeholder="Email Address" required>
                            <small class="form-text text-danger help-block"></small>
                        </div>
                    </div>
                    
                    <!-- Phone Field -->
                    <div class="control-group">
                        <div class="form-group floating-label-form-group controls">
                            <label for="phone">Phone Number</label>
                            <input class="form-control" type="tel" id="phone" name="contact" placeholder="Phone Number" required>
                            <small class="form-text text-danger help-block"></small>
                        </div>
                    </div>
                    
                    <!-- Message Field -->
                    <div class="control-group">
                        <div class="form-group floating-label-form-group controls mb-3">
                            <label for="message">Message</label>
                            <textarea class="form-control" id="message" name="message" rows="5" placeholder="Message" required></textarea>
                            <small class="form-text text-danger help-block"></small>
                        </div>
                    </div>
                    
                    <!-- Submit Button -->
                    <div id="success"></div>
                    <div class="form-group">
                        <button class="btn btn-primary float-right" id="sendMessageButton" type="submit" name="send">Send</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <hr>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <!-- JavaScript -->
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/clean-blog.js"></script>
</body>

</html>
