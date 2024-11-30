<?php
session_set_cookie_params(0);
session_start();
include('includes/config.php');

// Redirect to login if not logged in
if (strlen($_SESSION['alogin']) == 0) {
    header('location: login.php');
    exit();
}

// Check if a valid news ID is provided
if (isset($_GET['id'])) {
    $news_id = intval($_GET['id']);

    // Handle form submission
    if (isset($_POST['submit'])) {
        $title = $_POST['title'];
        $description = $_POST['description'];
        $publisher = $_POST['publisher'];
        $link = ""; // Modify this if links are to be included
        $posted_at = date("Y-m-d H:i:s"); // Current timestamp
        $admin_id = $_SESSION['alogin'];

        // Image upload handling
        $target_dir = "img/news_images/";
        $image1 = $_FILES["image1"]["name"];
        $image2 = $_FILES["image2"]["name"];
        $image3 = $_FILES["image3"]["name"];
        $image4 = $_FILES["image4"]["name"];

        if (!empty($image1)) move_uploaded_file($_FILES["image1"]["tmp_name"], $target_dir . basename($image1));
        if (!empty($image2)) move_uploaded_file($_FILES["image2"]["tmp_name"], $target_dir . basename($image2));
        if (!empty($image3)) move_uploaded_file($_FILES["image3"]["tmp_name"], $target_dir . basename($image3));
        if (!empty($image4)) move_uploaded_file($_FILES["image4"]["tmp_name"], $target_dir . basename($image4));

        // Update query using COALESCE to retain existing images if no new ones are uploaded
        $sql = "UPDATE scam_news 
                SET title = :title, description = :description, publisher = :publisher, 
                    image1 = COALESCE(NULLIF(:image1, ''), image1), 
                    image2 = COALESCE(NULLIF(:image2, ''), image2), 
                    image3 = COALESCE(NULLIF(:image3, ''), image3), 
                    image4 = COALESCE(NULLIF(:image4, ''), image4), 
                    link = :link, posted_at = :posted_at, admin_id = :admin_id 
                WHERE id = :news_id";

        $query = $dbh->prepare($sql);
        $query->bindParam(':title', $title, PDO::PARAM_STR);
        $query->bindParam(':description', $description, PDO::PARAM_STR);
        $query->bindParam(':publisher', $publisher, PDO::PARAM_STR);
        $query->bindParam(':image1', $image1, PDO::PARAM_STR);
        $query->bindParam(':image2', $image2, PDO::PARAM_STR);
        $query->bindParam(':image3', $image3, PDO::PARAM_STR);
        $query->bindParam(':image4', $image4, PDO::PARAM_STR);
        $query->bindParam(':link', $link, PDO::PARAM_STR);
        $query->bindParam(':posted_at', $posted_at, PDO::PARAM_STR);
        $query->bindParam(':admin_id', $admin_id, PDO::PARAM_STR);
        $query->bindParam(':news_id', $news_id, PDO::PARAM_INT);
        $query->execute();

        echo "<script>alert('News updated successfully'); document.location = 'adminnews.php';</script>";
    }

    // Fetch existing news details
    $sql = "SELECT * FROM scam_news WHERE id = :news_id";
    $query = $dbh->prepare($sql);
    $query->bindParam(':news_id', $news_id, PDO::PARAM_INT);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_OBJ);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Edit News</title>

    <!-- Fonts and Styles -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

    <!-- jQuery and Summernote -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
</head>

<body id="page-top">

    <div id="wrapper">
        <!-- Sidebar -->
        <?php include "includes/sidebar.php"; ?>

        <div id="content-wrapper" class="d-flex flex-column">
            <!-- Main Content -->
            <div id="content">
                <!-- Header -->
                <?php include "includes/header.php"; ?>

                <!-- Page Content -->
                <div class="container-fluid">
                    <h3 class="text-dark mb-4">Edit News</h3>
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <p class="text-primary m-0 font-weight-bold">Update News</p>
                        </div>
                        <div class="card-body">
                            <?php if ($result) { ?>
                                <form method="post" enctype="multipart/form-data">
                                    <div class="form-group">
                                        <label for="title">Title</label>
                                        <input type="text" class="form-control" name="title" value="<?php echo htmlentities($result->title ?? ''); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="description">Description</label>
                                        <textarea class="form-control Mysummernote" name="description" required><?php echo htmlentities($result->description ?? ''); ?></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="publisher">Publisher Name</label>
                                        <input type="text" class="form-control" name="publisher" value="<?php echo htmlentities($result->publisher ?? ''); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="images">Upload Images (Leave blank to keep existing images)</label>
                                        <input type="file" class="form-control" name="image1">
                                        <input type="file" class="form-control" name="image2">
                                        <input type="file" class="form-control" name="image3">
                                        <input type="file" class="form-control" name="image4">
                                    </div>
                                    <button type="submit" name="submit" class="btn btn-primary">Update News</button>
                                </form>
                            <?php } else { ?>
                                <div class="alert alert-danger">Error: News not found.</div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Footer -->
            <?php include "includes/footer.php"; ?>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $(".Mysummernote").summernote({
                height: 300,
                width: 750
            });
        });
    </script>
</body>

</html>
<?php ?>
