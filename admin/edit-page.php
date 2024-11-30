<?php
session_set_cookie_params(0);
session_start();
include('includes/config.php');

// Redirect to login if the user is not logged in
if (strlen($_SESSION['alogin']) == 0) {
    header('location: login.php');
    exit();
}

// Handle form submission to update the page details
if (isset($_POST['submit'])) {
    $pagename = $_POST['pagename'];
    $pagetype = $_POST['pagetype'];
    $description = $_POST['desc'];
    $id = intval($_GET['id']);

    $sql = "UPDATE `pages` SET pagename=:pagename, pagetype=:pagetype, description=:description WHERE id=:id";
    $query = $dbh->prepare($sql);
    $query->bindParam(':pagename', $pagename, PDO::PARAM_STR);
    $query->bindParam(':pagetype', $pagetype, PDO::PARAM_STR);
    $query->bindParam(':description', $description, PDO::PARAM_STR);
    $query->bindParam(':id', $id, PDO::PARAM_INT);
    $query->execute();

    echo "<script>alert('Page has been updated successfully');</script>";
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
    <title>Edit Page</title>

    <!-- Custom fonts and styles -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
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
                    <h3 class="text-dark mb-4">Edit Page</h3>
                    <div class="row mb-3">
                        <div class="col-lg-8">
                            <div class="card shadow mb-3">
                                <div class="card-header py-3">
                                    <p class="text-primary m-0 font-weight-bold">Edit Page</p>
                                </div>
                                <div class="card-body">

                                    <?php
                                    // Fetch current page details
                                    $id = intval($_GET['id']);
                                    $sql = "SELECT * FROM pages WHERE id=:id";
                                    $query = $dbh->prepare($sql);
                                    $query->bindParam(':id', $id, PDO::PARAM_INT);
                                    $query->execute();
                                    $results = $query->fetchAll(PDO::FETCH_OBJ);

                                    if ($query->rowCount() > 0) {
                                        foreach ($results as $result) {
                                    ?>
                                            <form method="post">
                                                <div class="form-group">
                                                    <label for="pagename"><strong>Page Name</strong></label>
                                                    <input type="text" id="pagename" name="pagename" class="form-control" 
                                                           value="<?php echo htmlentities($result->pagename); ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="pagetype"><strong>Page Type</strong></label>
                                                    <input type="text" id="pagetype" name="pagetype" class="form-control" 
                                                           value="<?php echo htmlentities($result->pagetype); ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="textarea1"><strong>Page Description</strong></label>
                                                    <textarea id="textarea1" name="desc" rows="4" class="form-control" 
                                                              style="height: 200px;" required><?php echo htmlentities($result->description); ?></textarea>
                                                </div>
                                                <button type="submit" name="submit" class="btn btn-primary">Update</button>
                                            </form>
                                    <?php
                                        }
                                    } else {
                                        echo "<p class='text-danger'>Page not found!</p>";
                                    }
                                    ?>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <?php include "includes/footer.php"; ?>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button -->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal -->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="logout.php">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap and Core Scripts -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>

</body>

</html>

