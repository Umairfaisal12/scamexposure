<?php
session_set_cookie_params(0);
session_start();
include('includes/config.php');

// Check if admin is logged in
if (strlen($_SESSION['alogin']) == 0) {
    header('location: login.php');
    exit;
}

if (isset($_POST['submit'])) {
    // Collecting form data
    $title = $_POST['title'];
    $cat = $_POST['selectcat'];
    $grabber = $_POST['grabber'];
    $description = $_POST['description'];
    $image1 = $_FILES["img1"]["name"];
    $status = 0;

    // Upload directory for images
    $target_dir = "assets/img/postimages/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // Move uploaded file
    move_uploaded_file($_FILES["img1"]["tmp_name"], $target_dir . $image1);

    // Default values for admin user
    $username = "admin";
    $uid = $_SESSION['userid'];

    // Insert post into database
    $sql = "INSERT INTO posts(title, category, grabber, description, username, image1, userid, status) 
            VALUES(:title, :cat, :grabber, :description, :username, :image1, :uid, :status)";
    $query = $dbh->prepare($sql);
    $query->bindParam(':title', $title, PDO::PARAM_STR);
    $query->bindParam(':cat', $cat, PDO::PARAM_STR);
    $query->bindParam(':grabber', $grabber, PDO::PARAM_STR);
    $query->bindParam(':description', $description, PDO::PARAM_STR);
    $query->bindParam(':username', $username, PDO::PARAM_STR);
    $query->bindParam(':image1', $image1, PDO::PARAM_STR);
    $query->bindParam(':uid', $uid, PDO::PARAM_STR);
    $query->bindParam(':status', $status, PDO::PARAM_STR);

    if ($query->execute()) {
        echo "<script>alert('Post submitted successfully, wait for approval');document.location = 'index.php';</script>";
    } else {
        echo "<script>alert('Something went wrong')</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Add Post</title>

    <!-- Custom fonts and styles -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="css/custom.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>

<body id="page-top">
    <div id="wrapper">
        <!-- Sidebar -->
        <?php include "includes/sidebar.php"; ?>

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <!-- Topbar -->
                <?php include "includes/header.php"; ?>

                <!-- Begin Page Content -->
                <div class="container">
                    <div class="row">
                        <div class="col-md-10 col-lg-8 mx-auto">
                            <h2 class="post-title">Add a post</h2>
                            <form id="contactForm" method="post" enctype="multipart/form-data">
                                <!-- Title -->
                                <div class="form-group">
                                    <label for="title">Title</label>
                                    <input class="form-control" type="text" id="title" name="title" required placeholder="Title">
                                    <small class="form-text text-danger">Title</small>
                                </div>

                                <!-- Category -->
                                <div class="form-group">
                                    <label for="select1"><strong>Select Category</strong></label>
                                    <select class="form-control" id="select1" name="selectcat" required>
                                        <option value="">-- Select --</option>
                                        <?php
                                        $ret = "SELECT `id`, `catname` FROM `categories`";
                                        $query = $dbh->prepare($ret);
                                        $query->execute();
                                        $results = $query->fetchAll(PDO::FETCH_OBJ);
                                        if ($query->rowCount() > 0) {
                                            foreach ($results as $result) { ?>
                                                <option value="<?php echo htmlentities($result->id); ?>">
                                                    <?php echo htmlentities($result->catname); ?>
                                                </option>
                                            <?php }
                                        } ?>
                                    </select>
                                    <small class="form-text text-danger">Select Category</small>
                                </div>

                                <!-- Grabber -->
                                <div class="form-group">
                                    <label for="grabber">Grabber</label>
                                    <input class="form-control" type="text" id="grabber" name="grabber" required placeholder="Grabber">
                                    <small class="form-text text-danger">Grabber</small>
                                </div>

                                <!-- Image -->
                                <div class="form-group">
                                    <label for="file1">Add an image</label>
                                    <input type="file" class="form-control-file" id="file1" name="img1">
                                    <small class="form-text text-danger">Header Image</small>
                                </div>

                                <!-- Description -->
                                <div class="form-group">
                                    <label for="desc">Description</label>
                                    <textarea class="form-control Mysummernote" id="desc" name="description" rows="5" required></textarea>
                                    <small class="form-text text-danger">Description</small>
                                    <script>
    $(document).ready(function() {
        $(".Mysummernote").summernote({
            height: 300,
            width: 750,
        });
        $('.dropdown-toggle').dropdown();
    });
</script>
                                </div>

                                <!-- Submit -->
                                <button class="btn btn-primary float-right" id="sendMessageButton" type="submit" name="submit">Post</button>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- End of Page Content -->
            </div>

            <!-- Footer -->
            <?php include "includes/footer.php"; ?>
        </div>
    </div>

    <!-- Scroll to Top Button -->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Bootstrap core JavaScript -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>

     <!-- Summernote CDN -->
     <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
</body>

</html>
