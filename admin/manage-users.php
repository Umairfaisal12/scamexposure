<?php
session_set_cookie_params(0);
session_start();
include('includes/config.php');

// Redirect to login if session is not set
if (strlen($_SESSION['alogin']) == 0) {
    header('location: login.php');
    exit();
}

// Delete User
if (isset($_REQUEST['del'])) {
    $delid = intval($_GET['del']);
    $sql = "DELETE FROM users WHERE id=:delid";
    $query = $dbh->prepare($sql);
    $query->bindParam(':delid', $delid, PDO::PARAM_STR);
    $query->execute();
    echo "<script>alert('User deleted');document.location = 'manage-users.php';</script>";
}

// Approve User
if (isset($_REQUEST['uaid'])) {
    $uaid = intval($_GET['uaid']);
    $sql = "UPDATE users SET status=1 WHERE id=:uaid";
    $query = $dbh->prepare($sql);
    $query->bindParam(':uaid', $uaid, PDO::PARAM_STR);
    $query->execute();
    echo "<script>alert('User approved successfully');document.location = 'manage-users.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Manage Users</title>
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
            <div id="content">
                <!-- Topbar -->
                <?php include "includes/header.php"; ?>

                <!-- Page Content -->
                <div class="container-fluid">
                    <h3 class="text-dark mb-4">Users</h3>

                    <!-- Active Users Card -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <p class="text-primary m-0 font-weight-bold">Active Users</p>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Username</th>
                                            <th>Email</th>
                                            <th>Delete</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $sql = "SELECT * FROM users WHERE status=1";
                                        $query = $dbh->prepare($sql);
                                        $query->execute();
                                        $results = $query->fetchAll(PDO::FETCH_OBJ);
                                        $cnt = 1;

                                        foreach ($results as $result) {
                                            $name = htmlentities($result->fname . " " . $result->lname);
                                            echo "<tr>";
                                            echo "<td>" . htmlentities($cnt++) . "</td>";
                                            echo "<td>" . htmlentities($name) . "</td>";
                                            echo "<td>" . htmlentities($result->email) . "</td>";
                                            echo "<td><a href='manage-users.php?del={$result->id}' onclick='return confirm(\"Do you want to delete?\");'>Delete</a></td>";
                                            echo "</tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Pending Users Card -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <p class="text-primary m-0 font-weight-bold">Pending Users</p>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Username</th>
                                            <th>Email</th>
                                            <th>Approve</th>
                                            <th>Delete</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $sql = "SELECT * FROM users WHERE status=0";
                                        $query = $dbh->prepare($sql);
                                        $query->execute();
                                        $results = $query->fetchAll(PDO::FETCH_OBJ);
                                        $cnt = 1;

                                        foreach ($results as $result) {
                                            $name = htmlentities($result->fname . " " . $result->lname);
                                            echo "<tr>";
                                            echo "<td>" . htmlentities($cnt++) . "</td>";
                                            echo "<td>" . htmlentities($name) . "</td>";
                                            echo "<td>" . htmlentities($result->email) . "</td>";
                                            echo "<td><a href='manage-users.php?uaid={$result->id}' onclick='return confirm(\"Do you want to approve this user?\");'>Approve</a></td>";
                                            echo "<td><a href='manage-users.php?del={$result->id}' onclick='return confirm(\"Do you want to delete?\");'>Delete</a></td>";
                                            echo "</tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <?php include "includes/footer.php"; ?>
            </div>
        </div>
    </div>

    <!-- Scroll to Top Button -->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal -->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
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
