<?php
session_set_cookie_params(0);
session_start();
include('includes/config.php');
if (strlen($_SESSION['alogin']) == 0) {
    header('location: login.php');
} else {
    // Decline comment
    if (isset($_REQUEST['did'])) {
        $did = intval($_GET['did']);
        $sts3 = 2;
        $sql3 = "UPDATE comments SET status=:sts3 WHERE id=:did";
        $query = $dbh->prepare($sql3);
        $query->bindParam(':did', $did, PDO::PARAM_STR);
        $query->bindParam(':sts3', $sts3, PDO::PARAM_STR);
        $query->execute();
        echo "<script>alert('Comment declined')</script>";
    }
    // Approve comment
    elseif (isset($_REQUEST['aid'])) {
        $aid = intval($_GET['aid']);
        $sts2 = 1;
        $sql2 = "UPDATE comments SET status=:sts2 WHERE id=:aid";
        $query = $dbh->prepare($sql2);
        $query->bindParam(':aid', $aid, PDO::PARAM_STR);
        $query->bindParam(':sts2', $sts2, PDO::PARAM_STR);
        $query->execute();
        echo "<script>alert('Comment approved')</script>";
    }
    // Delete comment
    elseif (isset($_REQUEST['delete_id'])) {
        $delete_id = intval($_GET['delete_id']);
        $sql_delete = "DELETE FROM comments WHERE id=:delete_id";
        $query_delete = $dbh->prepare($sql_delete);
        $query_delete->bindParam(':delete_id', $delete_id, PDO::PARAM_STR);
        $query_delete->execute();
        echo "<script>alert('Comment deleted')</script>";
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

        <title>Manage Comments</title>

        <!-- Custom fonts for this template-->
        <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
              rel="stylesheet">

        <!-- Custom styles for this template-->
        <link href="css/sb-admin-2.min.css" rel="stylesheet">

        <script src="vendor/jquery/jquery.min.js"></script>
    </head>

    <body id="page-top">

        <div id="wrapper">
            <?php include "includes/sidebar.php"; ?>
            <div id="content-wrapper" class="d-flex flex-column">
                <div id="content">
                    <?php include "includes/header.php"; ?>
                    <div class="container-fluid">
                        <h3 class="text-dark mb-4">Manage Comments</h3>
                        <div class="card shadow">
                            <div class="card-header py-3">
                                <p class="text-primary m-0 font-weight-bold">Comments</p>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive table mt-2">
                                    <table class="table dataTable my-0">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Username</th>
                                            <th>Email</th>
                                            <th>Comment</th>
                                            <th>Status</th>
                                            <th>Post</th>
                                            <th>Posting Date</th>
                                            <th>Approve</th>
                                            <th>Decline</th>
                                            <th>Delete</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        $sql = "SELECT comments.id, comments.name, comments.email, comments.postingdate, 
                                                comments.comment, comments.status, posts.title 
                                                FROM comments 
                                                JOIN posts ON posts.id = comments.postid 
                                                ORDER BY comments.id DESC";
                                        $query = $dbh->prepare($sql);
                                        $query->execute();
                                        $results = $query->fetchAll(PDO::FETCH_OBJ);
                                        $cnt = 1;
                                        if ($query->rowCount() > 0) {
                                            foreach ($results as $result) { ?>
                                                <tr id="comment-<?php echo $result->id; ?>">
                                                    <td><?php echo htmlentities($cnt); ?></td>
                                                    <td><?php echo htmlentities($result->name); ?></td>
                                                    <td><?php echo htmlentities($result->email); ?></td>
                                                    <td><?php echo htmlentities($result->comment); ?></td>
                                                    <td id="status-<?php echo $result->id; ?>">
                                                        <?php echo ($result->status == 1) ? "Approved" : (($result->status == 2) ? "Unapproved" : "Pending"); ?>
                                                    </td>
                                                    <td><?php echo htmlentities($result->title); ?></td>
                                                    <td><?php echo htmlentities($result->postingdate); ?></td>
                                                    <td>
                                                        <a href="manage-comments.php?aid=<?php echo $result->id; ?>"
                                                           onclick="return confirm('Do you want to approve this comment?');">Approve</a>
                                                    </td>
                                                    <td>
                                                        <a href="manage-comments.php?did=<?php echo $result->id; ?>"
                                                           onclick="return confirm('Do you want to decline this comment?');">Decline</a>
                                                    </td>
                                                    <td>
                                                        <a href="manage-comments.php?delete_id=<?php echo $result->id; ?>"
                                                           onclick="return confirm('Are you sure you want to delete this comment?');"
                                                           >Delete</a>
                                                    </td>
                                                </tr>
                                                <?php $cnt++;
                                            }
                                        } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php include "includes/footer.php"; ?>
            </div>
        </div>

        <!-- Bootstrap core JavaScript-->
        <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

        <!-- Core plugin JavaScript-->
        <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

        <!-- Custom scripts for all pages-->
        <script src="js/sb-admin-2.min.js"></script>

    </body>

    </html>
<?php } ?>
