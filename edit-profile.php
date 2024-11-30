<?php
session_set_cookie_params(0);
session_start();
include('includes/config.php');

// Redirect to login page if not logged in
if (strlen($_SESSION['login']) == 0) {
    header('location: login.php');
} else {
    // Handle profile update submission
    if (isset($_POST['submit'])) {
        $fname = $_POST['fname'];
        $lname = $_POST['lname'];
        $phone = $_POST['phone'];
        $email = $_SESSION['login'];

        // Update user details in the database
        $sql1 = "UPDATE `users` SET `fname` = :fname, `lname` = :lname, `phone` = :phone WHERE `email` = :email";
        $query = $dbh->prepare($sql1);
        $query->bindParam(':fname', $fname, PDO::PARAM_STR);
        $query->bindParam(':lname', $lname, PDO::PARAM_STR);
        $query->bindParam(':phone', $phone, PDO::PARAM_STR);
        $query->bindParam(':email', $email, PDO::PARAM_STR);
        $query->execute();

        echo "<script>alert('Profile updated successfully');document.location = 'index.php';</script>";
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Profile</title>
    <!-- CSS -->
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lora:400,700,400italic,700italic">
    <link rel="stylesheet" href="assets/fonts/font-awesome.min.css">
</head>

<body>
    <!-- Header -->
    <?php include 'includes/header.php'; ?>

    <header class="masthead" style="background-image:url('assets/img/home1.jpg');">
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
            <div class="col-md-9 col-lg-6">
                <h4>Update Profile</h4>
                <br>
                <?php
                $email = $_SESSION['login'];

                // Fetch user details from the database
                $sql2 = "SELECT * FROM `users` WHERE `email` = :email";
                $query = $dbh->prepare($sql2);
                $query->bindParam(':email', $email, PDO::PARAM_STR);
                $query->execute();
                $results = $query->fetchAll(PDO::FETCH_OBJ);

                if ($query->rowCount() > 0) {
                    foreach ($results as $result) { ?>
                        <form method="post">
                            <!-- First Name -->
                            <div class="form-group">
                                <label for="fname">First Name</label>
                                <input type="text" class="form-control" id="fname" name="fname" 
                                       value="<?php echo htmlentities($result->fname); ?>">
                            </div>

                            <!-- Last Name -->
                            <div class="form-group">
                                <label for="lname">Last Name</label>
                                <input type="text" class="form-control" id="lname" name="lname" 
                                       value="<?php echo htmlentities($result->lname); ?>">
                            </div>

                            <!-- Email Address -->
                            <div class="form-group">
                                <label for="email1">Email Address</label>
                                <input type="email" class="form-control" id="email1" name="email" 
                                       value="<?php echo htmlentities($result->email); ?>" disabled>
                                <small id="emailHelp" class="form-text text-muted">
                                    To change your email address, please contact admin.
                                </small>
                            </div>

                            <!-- Phone -->
                            <div class="form-group">
                                <label for="phone">Phone</label>
                                <input type="tel" class="form-control" id="phone" name="phone" 
                                       autocomplete="off" value="<?php echo htmlentities($result->phone); ?>">
                            </div>
                    <?php }
                } ?>
                            <!-- Submit Button -->
                            <div class="form-group">
                                <button type="submit" class="btn btn-danger float-right" name="submit">Update</button>
                            </div>
                        </form>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <!-- JavaScript -->
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/clean-blog.js"></script>
</body>

</html>

<?php } ?>
