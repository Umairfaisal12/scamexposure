<?php
// Start the session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scam Exposure</title>
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="
</head>
<body>

<!-- Navbar -->
<nav class="navbar fixed-top navbar-light navbar-expand-lg" id="mainNav">
    <div class="container">
        <a class="navbar-brand" href="index.php">SCAM EXPOSURE</a>
        <button data-toggle="collapse" data-target="#navbarResponsive" class="navbar-toggler" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
            <i class="fa fa-bars"></i>
        </button>
        <div class="collapse navbar-collapse" id="navbarResponsive">
            <ul class="nav navbar-nav ml-auto">
                <li class="nav-item" role="presentation"><a class="nav-link" href="index.php">Home</a></li>
                <li class="nav-item" role="presentation"><a class="nav-link" href="about.php">About us</a></li>
                <li class="nav-item" role="presentation"><a class="nav-link" href="contact.php">Contact us</a></li>
                <li class="nav-item" role="presentation"><a class="nav-link" href="view-posts.php">View Posts</a></li>
                <li class="nav-item" role="presentation"><a class="nav-link" href="scamnews.php">Daily Scam News</a></li>
                
                <?php if (isset($_SESSION['login']) && strlen($_SESSION['login']) != 0) { ?>
                    <li class="nav-item dropdown" role="presentation">
                        <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Manage Posts
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink">
                            <a class="dropdown-item" href="add-post.php">Add Post</a>
                            <a class="dropdown-item" href="manage-posts.php">Edit Post</a>
                        </div>
                    </li>
                <?php } ?>

                <?php if (!isset($_SESSION['login']) || strlen($_SESSION['login']) == 0) { ?>
                    <li class="nav-item" role="presentation"><a class="nav-link" href="login.php">Log in</a></li>
                <?php } else {
                    // Get user name from the database based on session login
                    $email = $_SESSION['login'];
                    $sql = "SELECT fname, lname FROM users WHERE email = :email";
                    $query = $dbh->prepare($sql);
                    $query->bindParam(':email', $email, PDO::PARAM_STR);
                    $query->execute();
                    $result = $query->fetch(PDO::FETCH_OBJ);

                    if ($result) { ?>
                        <li class="nav-item dropdown" role="presentation">
                            <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <?php echo htmlentities($result->fname . " " . htmlentities($result->lname)); ?>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink">
                                <a class="dropdown-item" href="edit-profile.php">Edit Profile</a>
                                <a class="dropdown-item" href="update-password.php">Update Password</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="logout.php">Log out</a>
                            </div>
                        </li>
                    <?php }
                } ?>

                <?php if (!isset($_SESSION['login']) || strlen($_SESSION['login']) == 0) { ?>
                    <li class="nav-item" role="presentation"><a class="nav-link" href="register.php">Register</a></li>
                <?php } ?>
            </ul>
        </div>
    </div>
</nav>

<!-- Page Content -->
<div class="container">
    <!-- Your content goes here -->
</div>

<!-- Bootstrap and jQuery scripts -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
