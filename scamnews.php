<?php
session_set_cookie_params(0);
session_start();
include('includes/config.php');
error_reporting(0);

// Set redirect URL
$_SESSION['redirectURL'] = $_SERVER['REQUEST_URI'];

// Pagination setup
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 6; // Posts per page
$offset = ($page - 1) * $limit;

// Fetch published news with pagination
try {
    $sql = "SELECT * FROM scam_news WHERE published = 1 ORDER BY posted_at DESC LIMIT :limit OFFSET :offset";
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $news = $stmt->fetchAll(PDO::FETCH_OBJ);
} catch (PDOException $e) {
    echo "Error fetching news: " . $e->getMessage();
}

// Get total published posts
try {
    $total_posts_stmt = $dbh->query("SELECT COUNT(*) FROM scam_news WHERE published = 1");
    $total_posts = $total_posts_stmt->fetchColumn();
    $total_pages = ceil($total_posts / $limit);
} catch (PDOException $e) {
    echo "Error counting total posts: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scam News</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/fonts/font-awesome.min.css">
    <link rel="stylesheet" href="admin/css/style.css">
</head>

<body>

<?php include 'includes/header.php'; ?>

<header class="masthead" style="background-image: url('assets/img/newspage.jpg');">
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

<section>
    <h2 class="text-center my-4">NEWS ABOUT LATEST SCAMS</h2>
    <div class="container">
        <div class="row">
            <?php if (!empty($news)): ?>
                <?php foreach ($news as $index => $item): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <div class="image-slider">
                                <img src="admin/img/news_images/<?php echo htmlspecialchars($item->image1); ?>" alt="Image 1" class="active">
                                <img src="admin/img/news_images/<?php echo htmlspecialchars($item->image2); ?>" alt="Image 2">
                                <img src="admin/img/news_images/<?php echo htmlspecialchars($item->image3); ?>" alt="Image 3">
                                <img src="admin/img/news_images/<?php echo htmlspecialchars($item->image4); ?>" alt="Image 4">
                            </div>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars(strip_tags($item->title)); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars(strip_tags(substr($item->description, 0, 100))) . '...'; ?></p>
                                <p class="text-muted">Published by: <?php echo htmlspecialchars($item->publisher); ?></p>
                                <a href="viewnews.php?id=<?php echo htmlentities($item->id); ?>" class="btn btn-primary">Read Full News</a>
                            </div>
                        </div>
                    </div>
                    <?php if (($index + 1) % 3 == 0): ?>
                        </div><div class="row">
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <p class="text-center">No news available at the moment.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<div class="container">
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            <li class="page-item <?php if ($page <= 1) echo 'disabled'; ?>">
                <a class="page-link" href="?page=<?php echo $page - 1; ?>" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>

            <li class="page-item <?php if ($page >= $total_pages) echo 'disabled'; ?>">
                <a class="page-link" href="?page=<?php echo $page + 1; ?>" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        </ul>
    </nav>
</div>

<?php include 'includes/footer.php'; ?>

<script src="assets/js/jquery.min.js"></script>
<script src="assets/bootstrap/js/bootstrap.min.js"></script>
<script>
    $(document).ready(function () {
        // Slider functionality
        $('.image-slider').each(function () {
            var $slider = $(this);
            var $images = $slider.find('img');
            var currentIndex = 0;

            function showNextImage() {
                $images.eq(currentIndex).removeClass('active');
                currentIndex = (currentIndex + 1) % $images.length;
                $images.eq(currentIndex).addClass('active');
            }

            setInterval(showNextImage, 3000); // Change image every 3 seconds
        });
    });
</script>
</body>

</html>
