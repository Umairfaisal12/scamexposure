<?php
session_set_cookie_params(0);
session_start();
include('includes/config.php');
error_reporting(0);

// Set redirect URL
$_SESSION['redirectURL'] = $_SERVER['REQUEST_URI'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>View Posts</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lora:400,700,400italic,700italic">
    <link rel="stylesheet" href="assets/fonts/font-awesome.min.css">
</head>

<body>
    <!-- Header -->
    <?php include 'includes/header.php'; ?>

    <header class="masthead" style="background-image:url('assets/img/home3.jpg');">
        <div class="overlay"></div>
        <div class="container">
            <div class="row">
                <div class="col-md-10 col-lg-8 mx-auto">
                    <div class="site-heading">
                        <h1>Scam Exposure Site</h1>
                        <span class="subheading">Let's say no to scams</span>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <article>
        <div class="container">
            <div class="row">
                <?php
                // Pagination logic
                $page_no = isset($_GET['page_no']) && $_GET['page_no'] != "" ? (int)$_GET['page_no'] : 1;
                $total_records_per_page = 3;
                $offset = ($page_no - 1) * $total_records_per_page;

                $previous_page = $page_no - 1;
                $next_page = $page_no + 1;
                $adjacents = 2;

                // Fetch total records for pagination
                $sql1 = "SELECT * FROM `posts` WHERE posts.status=1";
                $stmt1 = $dbh->prepare($sql1);
                $stmt1->execute();
                $total_records = $stmt1->rowCount();

                $total_no_of_pages = ceil($total_records / $total_records_per_page);
                $second_last = $total_no_of_pages - 1;

                // Fetch paginated posts
                $sql = "
                    SELECT posts.*, categories.catname 
                    FROM posts 
                    JOIN categories ON categories.id = posts.category 
                    WHERE posts.status = :status 
                    ORDER BY posts.id DESC 
                    LIMIT :offset, :limit
                ";
                $query = $dbh->prepare($sql);
                $status = 1;
                $query->bindParam(':status', $status, PDO::PARAM_INT);
                $query->bindParam(':offset', $offset, PDO::PARAM_INT);
                $query->bindParam(':limit', $total_records_per_page, PDO::PARAM_INT);
                $query->execute();
                $results = $query->fetchAll(PDO::FETCH_OBJ);

                if ($query->rowCount() > 0):
                    foreach ($results as $result): ?>
                        <div class="col-md-10 col-lg-12">
                            <div class="post-preview">
                                <a href="post-details.php?id=<?= htmlentities($result->id) ?>">
                                    <h2 class="post-title">
                                        <?= htmlentities($result->title) ?>, 
                                        <i><?= htmlentities($result->catname) ?></i>
                                    </h2>
                                    <h3 class="post-subtitle"><?= htmlentities($result->grabber) ?></h3>
                                </a>
                                <p class="post-meta">
                                    Posted by <?= htmlentities($result->username) ?>
                                    on <?= htmlentities($result->creationdate) ?>
                                </p>
                            </div>
                            <hr>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No posts available.</p>
                <?php endif; ?>
            </div>

            <!-- Pagination -->
            <div class="pagination-container text-center mt-3">
                <strong>Page <?= $page_no ?> of <?= $total_no_of_pages ?></strong>
            </div>
            <nav aria-label="Page navigation example">
                <ul class="pagination justify-content-center">
                    <!-- Previous Button -->
                    <li class="page-item <?= $page_no <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= $page_no > 1 ? '?page_no=' . $previous_page : '#' ?>">Previous</a>
                    </li>

                    <!-- Page Numbers -->
                    <?php
                    if ($total_no_of_pages <= 10):
                        for ($counter = 1; $counter <= $total_no_of_pages; $counter++):
                            $active = $counter == $page_no ? 'active' : '';
                            echo "<li class='page-item $active'><a class='page-link' href='?page_no=$counter'>$counter</a></li>";
                        endfor;
                    elseif ($total_no_of_pages > 10):
                        if ($page_no <= 4):
                            for ($counter = 1; $counter < 8; $counter++):
                                $active = $counter == $page_no ? 'active' : '';
                                echo "<li class='page-item $active'><a class='page-link' href='?page_no=$counter'>$counter</a></li>";
                            endfor;
                            echo "<li class='page-item'><a class='page-link'>...</a></li>";
                            echo "<li class='page-item'><a class='page-link' href='?page_no=$second_last'>$second_last</a></li>";
                            echo "<li class='page-item'><a class='page-link' href='?page_no=$total_no_of_pages'>$total_no_of_pages</a></li>";
                        elseif ($page_no > 4 && $page_no < $total_no_of_pages - 4):
                            echo "<li class='page-item'><a class='page-link' href='?page_no=1'>1</a></li>";
                            echo "<li class='page-item'><a class='page-link' href='?page_no=2'>2</a></li>";
                            echo "<li class='page-item'><a class='page-link'>...</a></li>";
                            for ($counter = $page_no - $adjacents; $counter <= $page_no + $adjacents; $counter++):
                                $active = $counter == $page_no ? 'active' : '';
                                echo "<li class='page-item $active'><a class='page-link' href='?page_no=$counter'>$counter</a></li>";
                            endfor;
                            echo "<li class='page-item'><a class='page-link'>...</a></li>";
                            echo "<li class='page-item'><a class='page-link' href='?page_no=$second_last'>$second_last</a></li>";
                            echo "<li class='page-item'><a class='page-link' href='?page_no=$total_no_of_pages'>$total_no_of_pages</a></li>";
                        else:
                            echo "<li class='page-item'><a class='page-link' href='?page_no=1'>1</a></li>";
                            echo "<li class='page-item'><a class='page-link' href='?page_no=2'>2</a></li>";
                            echo "<li class='page-item'><a class='page-link'>...</a></li>";
                            for ($counter = $total_no_of_pages - 6; $counter <= $total_no_of_pages; $counter++):
                                $active = $counter == $page_no ? 'active' : '';
                                echo "<li class='page-item $active'><a class='page-link' href='?page_no=$counter'>$counter</a></li>";
                            endfor;
                        endif;
                    endif;
                    ?>

                    <!-- Next Button -->
                    <li class="page-item <?= $page_no >= $total_no_of_pages ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= $page_no < $total_no_of_pages ? '?page_no=' . $next_page : '#' ?>">Next</a>
                    </li>

                    <!-- Last Page Button -->
                    <?php if ($page_no < $total_no_of_pages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page_no=<?= $total_no_of_pages ?>">Last &rsaquo;&rsaquo;</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </article>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/clean-blog.js"></script>
</body>

</html>
