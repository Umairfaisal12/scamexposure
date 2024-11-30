<?php
session_set_cookie_params(0);
session_start();
include('includes/config.php');

// Check if user is logged in
if (strlen($_SESSION['alogin']) == 0) {
    header('location: login.php');
    exit;
} else {
    // Handle form submission for publishing news
    if (isset($_POST['submit'])) {
        $title = $_POST['title'];
        $description = $_POST['description'];
        $publisher = $_POST['publisher'];
        $link = ""; // Placeholder for a link if needed
        $posted_at = date("Y-m-d H:i:s"); // Capture the current timestamp
        $admin_id = $_SESSION['alogin']; // Assuming admin_id is stored in session as alogin

        // Image Upload
        $image1 = $_FILES["image1"]["name"];
        $image2 = $_FILES["image2"]["name"];
        $image3 = $_FILES["image3"]["name"];
        $image4 = $_FILES["image4"]["name"];

        // Upload path
        $target_dir = "img/news_images/";
        move_uploaded_file($_FILES["image1"]["tmp_name"], $target_dir . basename($image1));
        move_uploaded_file($_FILES["image2"]["tmp_name"], $target_dir . basename($image2));
        move_uploaded_file($_FILES["image3"]["tmp_name"], $target_dir . basename($image3));
        move_uploaded_file($_FILES["image4"]["tmp_name"], $target_dir . basename($image4));

        // Insert into database
        $sql = "INSERT INTO scam_news (title, description, publisher, image1, image2, image3, image4, link, posted_at, admin_id, published) 
                VALUES (:title, :description, :publisher, :image1, :image2, :image3, :image4, :link, :posted_at, :admin_id, 0)";
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
        $query->execute();

        echo "<script>alert('News published successfully');document.location = 'manage-news.php';</script>";
    }

    // Handle Publish/Unpublish Action
    if (isset($_GET['action']) && isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $sql = "UPDATE scam_news SET published = CASE WHEN published = 1 THEN 0 ELSE 1 END WHERE id = :id";
        $query = $dbh->prepare($sql);
        $query->bindParam(':id', $id, PDO::PARAM_INT);
        $query->execute();
        echo "<script>alert('News action updated successfully');document.location = 'manage-news.php';</script>";
    }

    // Handle Delete Action
    if (isset($_GET['del'])) {
        $id = intval($_GET['del']);
        $sql = "DELETE FROM scam_news WHERE id = :id";
        $query = $dbh->prepare($sql);
        $query->bindParam(':id', $id, PDO::PARAM_INT);
        $query->execute();
        echo "<script>alert('News deleted successfully');document.location = 'manage-news.php';</script>";
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Manage News</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>

<body id="page-top">
    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <?php include "includes/sidebar.php"; ?>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <?php include "includes/header.php"; ?>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">
                    <h3 class="text-dark mb-4">Publish News / Manage News</h3>
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <p class="text-primary m-0 font-weight-bold">Publish News</p>
                        </div>
                        <div class="card-body">
                            <form method="post" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label for="title">Title</label>
                                    <input type="text" class="form-control" name="title" required>
                                </div>
                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea class="form-control Mysummernote" name="description" rows="5" required></textarea>
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
                                <div class="form-group">
                                    <label for="publisher">Publisher Name</label>
                                    <input type="text" class="form-control" name="publisher" required>
                                </div>
                                <div class="form-group">
                                    <label for="images">Upload Images (4 images required)</label>
                                    <input type="file" class="form-control" name="image1" required>
                                    <input type="file" class="form-control" name="image2" required>
                                    <input type="file" class="form-control" name="image3" required>
                                    <input type="file" class="form-control" name="image4" required>
                                </div>
                                <button type="submit" name="submit" class="btn btn-primary">Publish News</button>
                            </form>
                        </div>
                    </div>

                    <!-- Display Table -->
                    <div class="card shadow mt-4">
                        <div class="card-header py-3">
                            <p class="text-primary m-0 font-weight-bold">News List</p>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Title</th>
                                            <th>Description</th>
                                            <th>Publisher</th>
                                            <th>Actions</th>
                                            <th>Delete</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $sql = "SELECT * FROM scam_news";
                                    $query = $dbh->prepare($sql);
                                    $query->execute();
                                    $results = $query->fetchAll(PDO::FETCH_OBJ);
                                    $cnt = 1;
                                    foreach ($results as $result) {
                                        echo "<tr>";
                                        echo "<td>" . htmlentities($cnt) . "</td>";
                                        echo "<td>" . htmlentities($result->title) . "</td>";
                                        echo "<td>" . htmlentities($result->description) . "</td>";
                                        echo "<td>" . htmlentities($result->publisher) . "</td>";
                                        echo "<td>";
                                        // Toggle publish/unpublish action
                                        if ($result->published) {
                                            echo "<a href='manage-news.php?action=unpublish&id={$result->id}' onclick='return confirm(\"Do you want to unpublish this post?\");'>Unpublish</a> | ";
                                        } else {
                                            echo "<a href='manage-news.php?action=publish&id={$result->id}' onclick='return confirm(\"Do you want to publish this post?\");'>Publish</a> | ";
                                        }
                                        // Edit action
                                        echo "<a href='editnews.php?id={$result->id}'>Edit</a>";
                                        echo "</td>";
                                        echo "<td><a href='manage-news.php?del={$result->id}' onclick='return confirm(\"Do you want to delete this post?\");'>Delete</a></td>";
                                        echo "</tr>";
                                        $cnt++;
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End of Page Content -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <?php include "includes/footer.php"; ?>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>
     <!-- Summernote CDN -->
     <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
</body>
</html>
<?php } ?>