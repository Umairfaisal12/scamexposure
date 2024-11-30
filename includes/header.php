<?php
// Start the session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Get the current page name
$current_page = basename($_SERVER['PHP_SELF']);
?>
<nav class="navbar fixed-top navbar-light navbar-expand-lg" id="mainNav">
    <div class="container">
        <a class="navbar-brand" href="index.php">SCAM EXPOSURE</a>
        <button data-toggle="collapse" data-target="#navbarResponsive" class="navbar-toggler" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
            <i class="fa fa-bars"></i>
        </button>
        <div class="collapse navbar-collapse" id="navbarResponsive">
            <ul class="nav navbar-nav ml-auto">
                <li class="nav-item" role="presentation">
                    <a class="nav-link" href="index.php" 
                       style="<?php echo $current_page == 'index.php' ? 'color: red;' : ''; ?>">
                       Home
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" href="about.php" 
                       style="<?php echo $current_page == 'about.php' ? 'color: red;' : ''; ?>">
                       About us
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" href="contact.php" 
                       style="<?php echo $current_page == 'contact.php' ? 'color: red;' : ''; ?>">
                       Contact us
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" href="view-posts.php" 
                       style="<?php echo $current_page == 'view-posts.php' ? 'color: red;' : ''; ?>">
                       View Posts
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" href="scamnews.php" 
                       style="<?php echo $current_page == 'scamnews.php' ? 'color: red;' : ''; ?>">
                       Scam News
                    </a>
                </li>
                
                <?php if (isset($_SESSION['login']) && strlen($_SESSION['login']) != 0) { ?>
                    <li class="nav-item dropdown" role="presentation">
                        <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                           style="<?php echo in_array($current_page, ['add-post.php', 'manage-posts.php']) ? 'color: red;' : ''; ?>">
                           Manage Posts
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink">
                            <a class="dropdown-item" href="add-post.php">Add Post</a>
                            <a class="dropdown-item" href="manage-posts.php">Edit Post</a>
                        </div>
                    </li>
                <?php } ?>

                <?php if (!isset($_SESSION['login']) || strlen($_SESSION['login']) == 0) { ?>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" href="login.php" 
                           style="<?php echo $current_page == 'login.php' ? 'color: red;' : ''; ?>">
                           Log in
                        </a>
                    </li>
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
                            <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                               style="<?php echo in_array($current_page, ['edit-profile.php', 'update-password.php']) ? 'color: red;' : ''; ?>">
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
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" href="register.php" 
                           style="<?php echo $current_page == 'register.php' ? 'color: red;' : ''; ?>">
                           Register
                        </a>
                    </li>
                <?php } ?>
            </ul>
            

        </div>
    </div>
</nav>
