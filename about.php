<?php
// Start the session with a cookie lifetime of 0 (session expires on browser close)
session_set_cookie_params(0);
session_start();

// Include the database configuration file
include('includes/config.php');

// Suppress error reporting (useful in production, not recommended for development)
error_reporting(0);

// Save the current URL for redirection purposes
$_SESSION['redirectURL'] = $_SERVER['REQUEST_URI'];
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>About Us</title>
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    
    <!-- Google Fonts -->
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800">
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css?family=Lora:400,700,400italic,700italic">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="assets/fonts/font-awesome.min.css">
</head>

<body>
    <!-- Header -->
    <?php include 'includes/header.php'; ?>

    <!-- Masthead Section -->
    <header class="masthead" style="background-image:url('assets/img/home2.jpg');">
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

    <!-- About Us Content -->
    <?php
    $ptype = 'aboutus';
    $sql = "SELECT pages.pagename, pages.description FROM pages WHERE pages.pagetype=:ptype";
    $query = $dbh->prepare($sql);
    $query->bindParam(':ptype', $ptype, PDO::PARAM_STR);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_OBJ);

    if ($query->rowCount() > 0) {
        foreach ($results as $result) { ?>
            <div class="container">
                <div class="row">
                    <div class="col-md-10 col-lg-8 mx-auto">
                        <h2 class="post-title"><?php echo htmlentities($result->pagename); ?></h2>
                        <p><?php echo htmlentities($result->description); ?></p>
                    </div>
                </div>
            </div>
        <?php }
    }
    ?>
    <hr>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <!-- JavaScript Libraries -->
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/clean-blog.js"></script>
</body>

</html>
