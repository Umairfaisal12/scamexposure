<?php
session_set_cookie_params(0);
session_start();
error_reporting(0);
include('includes/config.php');

// Redirect to login if the user is not logged in
if (strlen($_SESSION['login']) == 0) {
    header('location: login.php');
    exit();
}

// Handle form submission to update the image
if (isset($_POST['update'])) {
    $image1 = $_FILES["img1"]["name"];
    $id = intval($_GET['imgid']); // Retrieve the post ID from the URL

    // Upload the image to the server
    move_uploaded_file($_FILES["img1"]["tmp_name"], "assets/img/postimages/" . $image1);

    // Update the database with the new image
    $sql = "UPDATE posts SET image1=:image1 WHERE id=:id";
    $query = $dbh->prepare($sql);
    $query->bindParam(':image1', $image1, PDO::PARAM_STR);
    $query->bindParam(':id', $id, PDO::PARAM_INT);
    $query->execute();

    echo "<script>alert('Image updated successfully');</script>";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Update Header Image</title>

    <!-- Bootstrap and Font Awesome -->
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lora:400,700,400italic,700italic">
    <link rel="stylesheet" href="assets/fonts/font-awesome.min.css">
</head>

<body>
    <!-- Header -->
    <header class="masthead" style="background-image:url('assets/img/home1.jpg');">
        <div class="overlay"></div>
        
    </header>
    <!-- End of Header -->

    <!-- Update Header Image Form -->
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-lg-8 mx-auto">
                <h2 class="post-title">Update Header Image</h2>
                <br>

                <?php
                // Fetch the current image from the database
                $id = intval($_GET['imgid']);
                $sql = "SELECT image1 FROM posts WHERE id=:id";
                $query = $dbh->prepare($sql);
                $query->bindParam(':id', $id, PDO::PARAM_INT);
                $query->execute();
                $results = $query->fetchAll(PDO::FETCH_OBJ);

                if ($query->rowCount() > 0) {
                    foreach ($results as $result) {
                        echo '<div class="col-sm-8 mb-4">
                            <img src="assets/img/postimages/' . htmlentities($result->image1) . '" width="300" height="200" style="border:solid 1px #000">
                        </div>';
                    }
                }
                ?>

                <form id="contactForm" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>Upload New Header Image<span style="color:red">*</span></label>
                        <input type="file" name="img1" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-primary" type="submit" name="update">Update</button>
                        <button class="btn btn-secondary" onclick="goBackWithId(); return false;">Back to Previous Page</button>
                    </div>
                </form>

                <!-- JavaScript to handle redirection -->
                <script>
                    function goBackWithId() {
                        const postId = "<?php echo intval($_GET['imgid']); ?>";
                        window.location.href = `http://localhost/scmex/admin/edit-post.php?id=${postId}`;
                    }
                </script>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <!-- JS and Bootstrap Scripts -->
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/clean-blog.js"></script>
</body>

</html>
