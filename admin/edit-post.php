<?php
session_set_cookie_params(0);
session_start();
include('includes/config.php');

// Redirect to login if not logged in
if (strlen($_SESSION['alogin']) == 0) {
    header('location: login.php');
    exit();
}

// Handle form submission
if (isset($_POST['submit'])) {
    $title = $_POST['title'];
    $cat = $_POST['selectcat'];
    $grabber = $_POST['grabber'];
    $description = $_POST['description'];
    $id = intval($_GET['id']);

    // Handle image upload
    $image1 = $_FILES['img1']['name'];
    $image_tmp1 = $_FILES['img1']['tmp_name'];
    $imagePath = "assets/img/postimages/" . $image1;

    if ($image1) {
        // Replace old image if a new one is uploaded
        move_uploaded_file($image_tmp1, $imagePath);
        $sql = "UPDATE `posts` SET title=:title, category=:cat, grabber=:grabber, description=:description, image1=:image1 WHERE id=:id";
    } else {
        // Keep the old image if no new one is uploaded
        $sql = "UPDATE `posts` SET title=:title, category=:cat, grabber=:grabber, description=:description WHERE id=:id";
    }

    $query = $dbh->prepare($sql);
    $query->bindParam(':title', $title, PDO::PARAM_STR);
    $query->bindParam(':cat', $cat, PDO::PARAM_STR);
    $query->bindParam(':grabber', $grabber, PDO::PARAM_STR);
    $query->bindParam(':description', $description, PDO::PARAM_STR);
    $query->bindParam(':id', $id, PDO::PARAM_INT);

    if ($image1) {
        $query->bindParam(':image1', $image1, PDO::PARAM_STR);
    }

    $query->execute();

    echo "<script>alert('Post has been updated successfully'); document.location = 'manage-posts.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Edit Post</title>

    <!-- Fonts and Styles -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
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
                    <h3 class="text-dark mb-4">Edit Post</h3>
                    <div class="row mb-3">
                        <div class="col-lg-8">
                            <div class="card shadow mb-3">
                                <div class="card-header py-3">
                                    <p class="text-primary m-0 font-weight-bold">Edit Post</p>
                                </div>
                                <div class="card-body">
                                    <?php
                                    // Fetch post details
                                    $id = intval($_GET['id']);
                                    $sql = "SELECT posts.*, categories.catname, categories.id AS cid 
                                            FROM posts 
                                            JOIN categories ON categories.id = posts.category 
                                            WHERE posts.id = :id";
                                    $query = $dbh->prepare($sql);
                                    $query->bindParam(':id', $id, PDO::PARAM_INT);
                                    $query->execute();
                                    $results = $query->fetchAll(PDO::FETCH_OBJ);

                                    if ($query->rowCount() > 0) {
                                        foreach ($results as $result) {
                                    ?>
                                            <form method="post" enctype="multipart/form-data">
                                                <div class="form-group">
                                                    <label for="title1"><strong>Title</strong></label>
                                                    <input type="text" id="title1" name="title" class="form-control" value="<?php echo htmlentities($result->title); ?>" required>
                                                </div>

                                                <div class="form-group">
                                                    <label for="select1"><strong>Select Category</strong></label>
                                                    <select id="select1" name="selectcat" class="form-control" required>
                                                        <option value="<?php echo htmlentities($result->cid); ?>"><?php echo htmlentities($result->catname); ?></option>
                                                        <?php
                                                        $ret = "SELECT id, catname FROM categories";
                                                        $query = $dbh->prepare($ret);
                                                        $query->execute();
                                                        $categories = $query->fetchAll(PDO::FETCH_OBJ);
                                                        foreach ($categories as $category) {
                                                            if ($category->catname != $result->catname) {
                                                                echo "<option value='" . htmlentities($category->id) . "'>" . htmlentities($category->catname) . "</option>";
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>

                                                <div class="form-group">
                                                    <label for="image1"><strong>Header Image</strong></label>
                                                    <img src="assets/img/postimages/<?php echo htmlentities($result->image1); ?>" width="300" height="200" style="border:solid 1px #000">
                                                    <br><br>
                                                    <a href="changeimage1.php?imgid=<?php echo htmlentities($result->id); ?>">Change Header Image</a>
                                                </div>

                                                <div class="form-group">
                                                    <label for="textarea2"><strong>Grabber</strong></label>
                                                    <textarea id="textarea2" name="grabber" rows="4" class="form-control" required><?php echo htmlentities($result->grabber); ?></textarea>
                                                </div>

                                                <div class="form-group">
                                                    <label for="description">Description</label>
                                                    <textarea class="form-control Mysummernote" id="description" name="description" required><?php echo htmlentities($result->description ?? ''); ?></textarea>
                                                </div>

                                                <button type="submit" name="submit" class="btn btn-primary float-right">Update</button>
                                            </form>
                                    <?php
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End of Page Content -->
            </div>
            <!-- Footer -->
            <?php include "includes/footer.php"; ?>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>

    <script>
        $(document).ready(function() {
            $(".Mysummernote").summernote({
                height: 300, // Set the editor height
                focus: true // Set focus to the editable area after initializing Summernote
            });
        });
    </script>

</body>

</html>
