<?php
session_set_cookie_params(0);
session_start();
include('includes/config.php');

// Redirect to login if not logged in
if (strlen($_SESSION['alogin']) == 0) {
    header('location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Admin Dashboard">
    <meta name="author" content="">

    <title>Admin - Dashboard</title>

    <!-- Fonts and Styles -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body id="page-top">
    <!-- Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <?php include "includes/sidebar.php"; ?>

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <?php include "includes/header.php"; ?>

                <!-- Begin Page Content -->
                <div class="container-fluid">

             

                    <!-- Dashboard Cards -->
                    <div class="row">

                        <!-- Dashboard Card Component -->
                        <?php
                        function createDashboardCard($title, $count, $color, $icon, $link) {
                            return <<<HTML
                            <div class="col-xl-3 col-md-6 mb-4">
                                <div class="card border-left-{$color} shadow h-100 py-2">
                                    <a href="{$link}">
                                        <div class="card-body">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="text-xs font-weight-bold text-{$color} text-uppercase mb-1">{$title}</div>
                                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{$count}</div>
                                                </div>
                                                <div class="col-auto">
                                                    <i class="fas {$icon} fa-2x text-gray-300"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            HTML;
                        }

                        // Query Counts
                        $queries = [
                            ['title' => 'Active Users', 'sql' => "SELECT id FROM users WHERE status=1", 'color' => 'success', 'icon' => 'fa-users',  'link' => 'manage-users.php'],
                            ['title' => 'Pending Users', 'sql' => "SELECT id FROM users WHERE status=0", 'color' => 'warning', 'icon' => 'fa-user-clock', 'link' => 'manage-users.php'],
                            ['title' => 'Active Posts', 'sql' => "SELECT id FROM posts WHERE status=1", 'color' => 'success', 'icon' => 'fa-file-alt',  'link' => 'manage-posts.php'],
                            ['title' => 'Pending Posts', 'sql' => "SELECT id FROM posts WHERE status=0", 'color' => 'primary', 'icon' => 'fa-file-import', 'link' => 'manage-posts.php'],
                            ['title' => 'Posted News', 'sql' => "SELECT id FROM scam_news WHERE published=1", 'color' => 'info', 'icon' => 'fa-newspaper', 'link' => 'manage-news.php'],
                            ['title' => 'Active Comments', 'sql' => "SELECT id FROM comments  WHERE status=1", 'color' => 'success','icon' => 'fa-comments', 'link' => 'manage-comments.php'],
                            ['title' => 'Pending Comments', 'sql' => "SELECT id FROM comments  WHERE status=0", 'color' => 'success', 'icon' => 'fa-comment-dots', 'link' => 'manage-comments.php']
                        ];

                        foreach ($queries as $query) {
                            $stmt = $dbh->prepare($query['sql']);
                            $stmt->execute();
                            $count = $stmt->rowCount();
                            echo createDashboardCard($query['title'], $count, $query['color'], $query['icon'], $query['link']);
                        }
                        ?>

                    </div>
                    <!-- End Dashboard Cards -->

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <?php include "includes/footer.php"; ?>

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Wrapper -->

    <!-- Scroll to Top Button -->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal -->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="logoutModalLabel">Ready to Leave?</h5>
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

    <!-- Scripts -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
</body>

</html>
