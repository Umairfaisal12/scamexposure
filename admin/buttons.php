<?php
// Start session with default cookie params
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
    <meta name="description" content="">
    <meta name="author" content="">

    <title>SB Admin 2 - Buttons</title>

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

                    <!-- Page Heading -->
                    <h1 class="h3 mb-4 text-gray-800">Buttons</h1>

                    <div class="row">
                        <div class="col-lg-6">

                            <!-- Circle Buttons -->
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Circle Buttons</h6>
                                </div>
                                <div class="card-body">
                                    <p>Use Font Awesome Icons (included with this theme package) along with the circle buttons as shown in the examples below!</p>

                                    <!-- Circle Buttons (Default) -->
                                    <div class="mb-2"><code>.btn-circle</code></div>
                                    <?php foreach (['primary' => 'facebook-f', 'success' => 'check', 'info' => 'info-circle', 'warning' => 'exclamation-triangle', 'danger' => 'trash'] as $btn => $icon): ?>
                                        <a href="#" class="btn btn-<?= $btn ?> btn-circle">
                                            <i class="fas fa-<?= $icon ?>"></i>
                                        </a>
                                    <?php endforeach; ?>

                                    <!-- Circle Buttons (Small) -->
                                    <div class="mt-4 mb-2"><code>.btn-circle .btn-sm</code></div>
                                    <?php foreach (['primary', 'success', 'info', 'warning', 'danger'] as $btn): ?>
                                        <a href="#" class="btn btn-<?= $btn ?> btn-circle btn-sm">
                                            <i class="fas fa-<?= $icon ?>"></i>
                                        </a>
                                    <?php endforeach; ?>

                                    <!-- Circle Buttons (Large) -->
                                    <div class="mt-4 mb-2"><code>.btn-circle .btn-lg</code></div>
                                    <?php foreach (['primary', 'success', 'info', 'warning', 'danger'] as $btn): ?>
                                        <a href="#" class="btn btn-<?= $btn ?> btn-circle btn-lg">
                                            <i class="fas fa-<?= $icon ?>"></i>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <!-- Brand Buttons -->
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Brand Buttons</h6>
                                </div>
                                <div class="card-body">
                                    <p>Google and Facebook buttons are available featuring each company's respective brand color. They are used on the user login and registration pages.</p>
                                    <p>You can create more custom buttons by adding a new color variable in the <code>_variables.scss</code> file and then using the Bootstrap button variant mixin to create a new style, as demonstrated in the <code>_buttons.scss</code> file.</p>
                                    <a href="#" class="btn btn-google btn-block">
                                        <i class="fab fa-google fa-fw"></i> .btn-google
                                    </a>
                                    <a href="#" class="btn btn-facebook btn-block">
                                        <i class="fab fa-facebook-f fa-fw"></i> .btn-facebook
                                    </a>
                                </div>
                            </div>

                        </div>

                        <div class="col-lg-6">

                            <!-- Split Buttons -->
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Split Buttons with Icon</h6>
                                </div>
                                <div class="card-body">
                                    <p>Works with any button colors, just use the <code>.btn-icon-split</code> class and the markup in the examples below.</p>
                                    <?php foreach (['primary' => 'flag', 'success' => 'check', 'info' => 'info-circle', 'warning' => 'exclamation-triangle', 'danger' => 'trash', 'secondary' => 'arrow-right', 'light' => 'arrow-right'] as $btn => $icon): ?>
                                        <a href="#" class="btn btn-<?= $btn ?> btn-icon-split">
                                            <span class="icon text-white-50"><i class="fas fa-<?= $icon ?>"></i></span>
                                            <span class="text">Split Button <?= ucfirst($btn) ?></span>
                                        </a>
                                        <div class="my-2"></div>
                                    <?php endforeach; ?>

                                    <p>Also works with small and large button classes!</p>
                                    <a href="#" class="btn btn-primary btn-icon-split btn-sm">
                                        <span class="icon text-white-50"><i class="fas fa-flag"></i></span>
                                        <span class="text">Split Button Small</span>
                                    </a>
                                    <div class="my-2"></div>
                                    <a href="#" class="btn btn-primary btn-icon-split btn-lg">
                                        <span class="icon text-white-50"><i class="fas fa-flag"></i></span>
                                        <span class="text">Split Button Large</span>
                                    </a>
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

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
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

    <!-- Core Scripts -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>

</body>

</html>

