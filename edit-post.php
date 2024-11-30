<?php
session_set_cookie_params(0);
session_start();
error_reporting(0);

// Include configuration
include('includes/config.php');

// Redirect to login if not logged in
if (strlen($_SESSION['login']) == 0) {
    header('location: login.php');
} else {
    // Handle form submission to update post
    if (isset($_POST['submit'])) {
        $title = $_POST['title'];
        $cat = $_POST['selectcat'];
        $grabber = $_POST['grabber'];
        $description = $_POST['description'];
        $id = intval($_GET['id']);

        // Update post in the database
        $sql = "UPDATE `posts` 
                SET title=:title, category=:cat, grabber=:grabber, description=:description 
                WHERE id=:id";
        $query = $dbh->prepare($sql);
        $query->bindParam(':title', $title, PDO::PARAM_STR);
        $query->bindParam(':cat', $cat, PDO::PARAM_STR);
        $query->bindParam(':grabber', $grabber, PDO::PARAM_STR);
        $query->bindParam(':description', $description, PDO::PARAM_STR);
        $query->bindParam(':id', $id, PDO::PARAM_STR);
        $query->execute();

        echo "<script>alert('Post has been updated successfully');document.location = 'view-posts.php';</script>";
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Edit Post</title>
    <!-- CSS -->
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lora:400,700,400italic,700italic">
    <link rel="stylesheet" href="assets/fonts/font-awesome.min.css">
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
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
            <div class="col-md-10 col-lg-8 mx-auto">
                <h2 class="post-title">Edit Post</h2>
                <br>
                <?php
                $id = intval($_GET['id']);

                // Fetch post details
                $sql = "SELECT posts.*, categories.catname, categories.id AS cid 
                        FROM posts 
                        JOIN categories ON categories.id = posts.category 
                        WHERE posts.id = :id";
                $query = $dbh->prepare($sql);
                $query->bindParam(':id', $id, PDO::PARAM_STR);
                $query->execute();
                $results = $query->fetchAll(PDO::FETCH_OBJ);

                if ($query->rowCount() > 0) {
                    foreach ($results as $result) { ?>
                        <form id="contactForm" method="post" enctype="multipart/form-data">
                            <!-- Title -->
                            <div class="control-group">
                                <label for="title"><strong>Title</strong></label>
                                <div class="form-group floating-label-form-group controls">
                                    <input class="form-control" type="text" id="title" name="title" 
                                           value="<?php echo htmlentities($result->title); ?>" 
                                           required placeholder="Title">
                                </div>
                            </div>

                            <!-- Category -->
                            <div class="control-group">
                                <label for="select1"><strong>Select Category</strong></label>
                                <div class="form-group floating-label-form-group controls">
                                    <select class="form-control" id="select1" name="selectcat" required>
                                        <option value="<?php echo htmlentities($result->cid); ?>">
                                            <?php echo htmlentities($result->catname); ?>
                                        </option>
                                        <?php
                                        $ret = "SELECT `id`, `catname` FROM `categories`";
                                        $query = $dbh->prepare($ret);
                                        $query->execute();
                                        $categories = $query->fetchAll(PDO::FETCH_OBJ);

                                        if ($query->rowCount() > 0) {
                                            foreach ($categories as $category) {
                                                if ($category->catname != $result->catname) { ?>
                                                    <option value="<?php echo htmlentities($category->id); ?>">
                                                        <?php echo htmlentities($category->catname); ?>
                                                    </option>
                                        <?php }
                                            }
                                        } ?>
                                    </select>
                                </div>
                            </div>

                            <!-- Grabber -->
                            <div class="control-group">
                                <label for="grabber"><strong>Grabber</strong></label>
                                <div class="form-group floating-label-form-group controls">
                                    <input class="form-control" type="text" id="grabber" name="grabber" 
                                           value="<?php echo htmlentities($result->grabber); ?>" 
                                           required placeholder="Grabber">
                                </div>
                            </div>

                            <!-- Header Image -->
                            <div class="control-group">
                                <label for="image1"><strong>Header Image</strong></label>
                                <div class="form-group floating-label-form-group controls">
                                    <img src="assets/img/postimages/<?php echo htmlentities($result->image1); ?>" 
                                         width="300" height="200" style="border:solid 1px #000">
                                    <br><br>
                                    <a href="changeimage1.php?imgid=<?php echo htmlentities($result->id); ?>">Change Header Image</a>
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="control-group">
                                <label for="desc"><strong>Description</strong></label>
                                <div class="form-group floating-label-form-group controls mb-3">
                                    <textarea class="form-control Mysummernote" id="desc" name="description" rows="5" 
                                              required placeholder="Description"><?php echo htmlentities($result->description); ?></textarea>
                                    <script>
                                        $(document).ready(function () {
                                            $(".Mysummernote").summernote({
                                                height: 300,
                                                width: 750,
                                            });
                                        });
                                    </script>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="form-group">
                                <button class="btn btn-primary" id="sendMessageButton" type="submit" name="submit">Update</button>
                            </div>
                        </form>
                <?php }
                } ?>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <!-- JavaScript -->
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/clean-blog.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
</body>

</html>

<?php } ?>
