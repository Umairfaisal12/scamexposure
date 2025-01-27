<?php
session_set_cookie_params(0);
session_start();
include('includes/config.php');
error_reporting(0);

// Store the current URL for redirecting after login
$_SESSION['redirectURL'] = $_SERVER['REQUEST_URI'];

// Handle comment submission
if (isset($_POST['submit'])) {
    $name2 = $_POST['name'];
    $email = $_POST['email'];
    $comment = $_POST['comment'];
    $postid = intval($_GET['id']);
    $st1 = '0';
    
    $sql = "INSERT INTO comments(`postid`, `name`, `email`, `comment`, `status`) 
            VALUES(:postid, :name2, :email, :comment, :st1)";
    $query = $dbh->prepare($sql);
    $query->bindParam(':postid', $postid, PDO::PARAM_STR);
    $query->bindParam(':name2', $name2, PDO::PARAM_STR);
    $query->bindParam(':email', $email, PDO::PARAM_STR);
    $query->bindParam(':comment', $comment, PDO::PARAM_STR);
    $query->bindParam(':st1', $st1, PDO::PARAM_STR);
    $query->execute();

    if ($dbh->lastInsertId()) {
        echo "<script>alert('Comment submitted, wait for approval');</script>";
    } else {
        echo "<script>alert('Something went wrong');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Post Details</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lora:400,700,400italic,700italic">
    <link rel="stylesheet" href="assets/fonts/font-awesome.min.css">
    <style>
        .post-image-container {
            width: 690px;
            height: 400px;
            margin: 20px auto;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            background-color: #fff;
            border: 1px solid #ddd;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .post-image {
            width: 100%;
            height: 100%;
            object-fit: contain;
            display: block;
        }

        .post-content {
            margin-top: 30px;
            line-height: 1.6;
        }

        .post-image-container:hover .post-image {
            transform: scale(1.02);
            transition: transform 0.3s ease;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <?php include 'includes/header.php'; ?>

    <?php
    $id = intval($_GET['id']);
    $s = 1;

    $sql1 = "SELECT posts.*, categories.catname, categories.id AS cid 
             FROM posts 
             JOIN categories ON categories.id = posts.category 
             WHERE posts.id = :id AND posts.status = :s";
    $query = $dbh->prepare($sql1);
    $query->bindParam(':id', $id, PDO::PARAM_STR);
    $query->bindParam(':s', $s, PDO::PARAM_STR);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_OBJ);

    if ($query->rowCount() > 0) {
        foreach ($results as $result) {
    ?>
    <header class="masthead" style="background-image:url('assets/img/home3.jpg');">
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

    <article>
        <div class="container">
            <div class="row">
                <div class="col-md-10 col-lg-8 mx-auto">
                    <div class="post-preview">
                        <h2 class="post-title"><?php echo htmlentities($result->title); ?></h2>
                        <p class="post-meta">
                            Category: <?php echo htmlentities($result->catname); ?><br>
                            Posted by <b><?php echo htmlentities($result->username); ?></b>
                            on <?php echo htmlentities($result->creationdate); ?>
                        </p>
                        <p style="font-weight: bold;"><?php echo htmlentities($result->grabber); ?></p>
                        
                        <?php if (!empty($result->image1)) { ?>
                            <div class="post-image-container">
                                <img class="post-image" src="assets/img/postimages/<?php echo htmlentities($result->image1); ?>" alt="Post Image">
                            </div>
                        <?php } ?>

                        <div class="post-content">
                            <?php echo html_entity_decode($result->description); ?>
                        </div>
                    </div>
                </div>
                <?php }
                } ?>

                <!-- Display Comments -->
                <div class="col-md-10 col-lg-8 mx-auto">
                    <?php
                    $pid = intval($_GET['id']);
                    $sts = 1;

                    $sql3 = "SELECT `name`, `comment`, `postingdate` 
                             FROM comments 
                             WHERE postid = :pid AND status = :sts";
                    $query = $dbh->prepare($sql3);
                    $query->bindParam(':pid', $pid, PDO::PARAM_STR);
                    $query->bindParam(':sts', $sts, PDO::PARAM_STR);
                    $query->execute();
                    $results3 = $query->fetchAll(PDO::FETCH_OBJ);

                    if ($query->rowCount() > 0) {
                        foreach ($results3 as $result3) {
                    ?>
                    <div class="card my-4">
                        <h5 class="card-header">Comments</h5>
                        <div class="card-body">
                            <div class="media-body">
                                <h6>
                                    <?php echo htmlentities($result3->name); ?><br />
                                    <span style="font-size:11px;"><b>commented at</b> <?php echo htmlentities($result3->postingdate); ?></span>
                                </h6>
                                <div style="font-size: 18px;">
                                    Comment: <?php echo htmlentities($result3->comment); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php }
                    } ?>
                </div>

                <!-- Post a Comment -->
                <div class="col-md-10 col-lg-8 mx-auto">
                    <div class="card my-4">
                        <h5 class="card-header">Leave a Comment:</h5>
                        <div class="card-body">
                            <form name="Comment" method="post">
                                <input type="hidden" name="csrftoken" value="<?php echo htmlentities($_SESSION['token']); ?>" />
                                <div class="form-group">
                                    <?php if ($_SESSION['login']) {
                                        $email = $_SESSION['login'];
                                        $sql2 = "SELECT fname, lname FROM users WHERE email = :email";
                                        $query = $dbh->prepare($sql2);
                                        $query->bindParam(':email', $email, PDO::PARAM_STR);
                                        $query->execute();
                                        $results = $query->fetchAll(PDO::FETCH_OBJ);
                                        $name = ($query->rowCount() > 0) ? $results[0]->fname . " " . $results[0]->lname : '';
                                    ?>
                                        <input type="text" name="name" value="<?php echo htmlentities($name); ?>" class="form-control" required>
                                    <?php } else { ?>
                                        <input type="text" name="name" class="form-control" placeholder="Enter your fullname" autocomplete="off" required>
                                    <?php } ?>
                                </div>

                                <div class="form-group">
                                    <?php if ($_SESSION['login']) { ?>
                                        <input type="email" name="email" value="<?php echo $_SESSION['login']; ?>" class="form-control" placeholder="Enter your valid email" autocomplete="off" required>
                                    <?php } else { ?>
                                        <input type="email" name="email" class="form-control" placeholder="Enter your valid email" autocomplete="off" required>
                                    <?php } ?>
                                </div>

                                <div class="form-group">
                                    <textarea class="form-control" name="comment" rows="3" placeholder="Comment" required></textarea>
                                </div>

                                <?php if ($_SESSION['login']) { ?>
                                    <button type="submit" class="btn btn-primary float-right" name="submit">Submit</button>
                                <?php } else { ?>
                                    <a href="login.php" class="btn btn-primary float-right">Log in & Comment</a>
                                <?php } ?>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </article>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/clean-blog.js"></script>
</body>

</html>
