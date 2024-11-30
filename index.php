<?php
session_set_cookie_params(0);
session_start();
include('includes/config.php');
error_reporting(0);

// Save the current URL for potential redirection
$_SESSION['redirectURL'] = $_SERVER['REQUEST_URI'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Home</title>
    <!-- CSS -->
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lora:400,700,400italic,700italic">
    <link rel="stylesheet" href="assets/fonts/font-awesome.min.css">
</head>

<body>
    <!-- Header -->
    <?php include 'includes/header.php'; ?>

    <!-- Masthead -->
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
            <?php
            $s = 1;
            $sql = "SELECT posts.*, categories.catname 
                    FROM posts 
                    JOIN categories ON categories.id = posts.category 
                    WHERE posts.status = :s 
                    ORDER BY posts.id DESC 
                    LIMIT 3";
            $query = $dbh->prepare($sql);
            $query->bindParam(':s', $s, PDO::PARAM_STR);
            $query->execute();
            $results = $query->fetchAll(PDO::FETCH_OBJ);

            if ($query->rowCount() > 0) {
                foreach ($results as $result) { ?>
                    <div class="col-md-10 col-lg-12">
                        <div class="post-preview">
                            <a href="post-details.php?id=<?php echo htmlentities($result->id); ?>">
                                <h2 class="post-title"><?php echo htmlentities($result->title); ?>, <i><?php echo htmlentities($result->catname); ?></i></h2>
                                <h3 class="post-subtitle"><?php echo htmlentities($result->grabber); ?></h3>
                            </a>
                            <p class="post-meta">
                                Posted by&nbsp;<?php echo htmlentities($result->username); ?>
                                on <?php echo htmlentities($result->creationdate); ?>
                            </p>
                        </div>
                        <hr>
                    </div>
            <?php }
            } ?>
        </div>

        <div class="row">
            <div class="col-md-10 col-lg-12">
                <div class="clearfix">
                    <a href="view-posts.php">
                        <button class="btn btn-primary float-right" type="button">Older Posts&nbsp;⇒</button>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <!-- Chatbot -->
    <script>
        window.embeddedChatbotConfig = {
            chatbotId: "M2mNvb-ttaPu1pLW0GUnI",
            domain: "www.chatbase.co"
        }
    </script>
    <script src="https://www.chatbase.co/embed.min.js" chatbotId="M2mNvb-ttaPu1pLW0GUnI" domain="www.chatbase.co" defer></script>

    <!-- Chatbot Interaction -->
    <div onclick="openChatbot()">Click to Chat</div>
    <script>
        function openChatbot() {
            const chatbotButton = document.querySelector('[data-chatbase-button]');
            if (chatbotButton) {
                chatbotButton.click();
            }
        }
    </script>

    <!-- JavaScript -->
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/clean-blog.js"></script>
</body>

</html>
