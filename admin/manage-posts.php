<?php
session_set_cookie_params(0);
session_start();
include('includes/config.php');

if (strlen($_SESSION['alogin']) == 0) {
    header('location: login.php');
    exit();
}

// DELETE Post
if (isset($_REQUEST['del'])) {
    $delid = intval($_GET['del']);
    $sql = "DELETE FROM posts WHERE id=:delid";
    $query = $dbh->prepare($sql);
    $query->bindParam(':delid', $delid, PDO::PARAM_STR);
    $query->execute();
    echo "<script>alert('Post deleted successfully');document.location = 'manage-posts.php';</script>";
}

// Unpublish Post
if (isset($_REQUEST['uid'])) {
    $uid = intval($_GET['uid']);
    $sts3 = 2;
    $sql3 = "UPDATE posts SET status=:sts3 WHERE id=:uid";
    $query = $dbh->prepare($sql3);
    $query->bindParam(':uid', $uid, PDO::PARAM_STR);
    $query->bindParam(':sts3', $sts3, PDO::PARAM_STR);
    $query->execute();
    echo "<script>alert('Post Unpublished');document.location = 'manage-posts.php';</script>";
}

// Publish Post
if (isset($_REQUEST['aid'])) {
    $aid = intval($_GET['aid']);
    $sts2 = 1;
    $sql2 = "UPDATE posts SET status=:sts2 WHERE id=:aid";
    $query = $dbh->prepare($sql2);
    $query->bindParam(':aid', $aid, PDO::PARAM_STR);
    $query->bindParam(':sts2', $sts2, PDO::PARAM_STR);
    $query->execute();
    echo "<script>alert('Post approved');document.location = 'manage-posts.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Manage Posts</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body id="page-top">

    <div id="wrapper">
        <!-- Sidebar -->
        <?php include "includes/sidebar.php"; ?>

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <?php include "includes/header.php"; ?>

                <!-- Page Content -->
                <div class="container-fluid">
                    <h3 class="text-dark mb-4">Manage Posts</h3>
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <p class="text-primary m-0 font-weight-bold">Posts</p>
                        </div>
                        <div class="card-body">

                            <!-- Table Controls -->
                            <div class="row">
                                <div class="col-md-6 text-nowrap">
                                    <label>
                                        Show
                                        <select class="form-control form-control-sm custom-select custom-select-sm">
                                            <option value="5" selected>5</option>
                                            <option value="10">10</option>
                                        </select>
                                    </label>
                                </div>
                                <div class="col-md-6 text-md-right">
                                    <input type="search" class="form-control form-control-sm" placeholder="Search">
                                </div>
                            </div>

                            <!-- Posts Table -->
                            <div class="table-responsive mt-2">
                                <table class="table my-0">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Title</th>
                                            <th>Category</th>
                                            <th>Edit</th>
                                            <th>Status</th>
                                            <th>Publish</th>
                                            <th>Unpublish</th>
                                            <th>Delete</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $sql = "SELECT posts.*, categories.catname FROM posts JOIN categories ON categories.id = posts.category";
                                        $query = $dbh->prepare($sql);
                                        $query->execute();
                                        $results = $query->fetchAll(PDO::FETCH_OBJ);
                                        $cnt = 1;

                                        if ($query->rowCount() > 0) {
                                            foreach ($results as $result) {
                                                $statusText = $result->status == 0 ? "Pending" : ($result->status == 1 ? "Published" : "Unpublished");
                                                ?>
                                                <tr>
                                                    <td><?= htmlentities($cnt++) ?></td>
                                                    <td><?= htmlentities($result->title) ?></td>
                                                    <td><?= htmlentities($result->catname) ?></td>
                                                    <td><a href="edit-post.php?id=<?= $result->id ?>">Edit</a></td>
                                                    <td><?= $statusText ?></td>
                                                    <td>
                                                        <a href="manage-posts.php?aid=<?= $result->id ?>" onclick="return confirm('Do you want to approve this post?');">Publish</a>
                                                    </td>
                                                    <td>
                                                        <a href="manage-posts.php?uid=<?= $result->id ?>" onclick="return confirm('Do you want to unpublish this post?');">Unpublish</a>
                                                    </td>
                                                    <td>
                                                        <a href="manage-posts.php?del=<?= $result->id ?>" onclick="return confirm('Do you want to delete?');">Delete</a>
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="row">
                                <div class="col-md-6 align-self-center">
                                    <p class="dataTables_info">Showing 1 to 5 of 100</p>
                                </div>
                                <div class="col-md-6">
                                    <nav class="d-lg-flex justify-content-lg-end">
                                        <ul class="pagination">
                                            <li class="page-item disabled"><a class="page-link" href="#" aria-label="Previous">«</a></li>
                                            <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                                            <li class="page-item"><a class="page-link" href="#">3</a></li>
                                            <li class="page-item"><a class="page-link" href="#" aria-label="Next">»</a></li>
                                        </ul>
                                    </nav>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>

            <!-- Footer -->
            <?php include "includes/footer.php"; ?>

        </div>
    </div>

    <!-- Scroll to Top Button -->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal -->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ready to Leave?</h5>
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

    <!-- JavaScript -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>

</body>

</html>
