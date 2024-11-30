<?php
session_set_cookie_params(0);
session_start();
include('includes/config.php');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Save the redirect URL
$_SESSION['redirectURL'] = $_SERVER['REQUEST_URI'];

// Handle comment submission
if (isset($_POST['submit'])) {
    $name2 = $_POST['name'];
    $email = $_POST['email'];
    $comment = $_POST['comment'];
    $newsId = intval($_GET['id']);
    $st1 = '0';

    $sql = "INSERT INTO comments(`news_id`, `name`, `email`, `comment`, `status`) 
            VALUES(:newsId, :name2, :email, :comment, :st1)";
    $query = $dbh->prepare($sql);
    $query->bindParam(':newsId', $newsId, PDO::PARAM_INT);
    $query->bindParam(':name2', $name2, PDO::PARAM_STR);
    $query->bindParam(':email', $email, PDO::PARAM_STR);
    $query->bindParam(':comment', $comment, PDO::PARAM_STR);
    $query->bindParam(':st1', $st1, PDO::PARAM_INT);

    $query->execute();
    $lastInsertId = $dbh->lastInsertId();

    if ($lastInsertId) {
        echo "<script>alert('Comment submitted, wait for approval');</script>";
    } else {
        echo "<script>alert('Something went wrong');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Scam News Details</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lora:400,700,400italic,700italic">
    <link rel="stylesheet" href="assets/fonts/font-awesome.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <style>
        .image-slider {
            position: relative;
            width: 100%;
            max-width: 800px;
            height: 450px;
            margin: auto;
            overflow: hidden;
            background-color: #f0f0f0;
        }

        .image-slider img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            display: none;
        }

        .image-slider img.active {
            display: block;
        }

        .slider-nav {
            position: absolute;
            top: 50%;
            width: 100%;
            display: flex;
            justify-content: space-between;
            transform: translateY(-50%);
        }

        .slider-nav button {
            background-color: rgba(255, 255, 255, 0.8);
            border: none;
            padding: 10px;
            cursor: pointer;
            font-size: 18px;
        }

        .slider-nav button:hover {
            background-color: rgba(255, 255, 255, 1);
        }

        .description-container {
            max-width: 800px;
            margin: 20px auto;
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
        }

        .description-text {
            text-align: justify;
            font-size: 16px;
        }
    </style>
</head>

<body>
    <?php include 'includes/header.php'; ?>

    <?php
    $id = intval($_GET['id']);
    $s = 1;
    $sql1 = "SELECT * FROM scam_news WHERE id=:id AND published=:s";
    $query = $dbh->prepare($sql1);
    $query->bindParam(':id', $id, PDO::PARAM_INT);
    $query->bindParam(':s', $s, PDO::PARAM_INT);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_OBJ);

    if ($query->rowCount() > 0):
        foreach ($results as $result):
    ?>
            <header class="masthead" style="background-image:url('assets/img/viewnews.jpg');">
                <div class="site-heading">
                    <h1>Scam Exposure Site</h1>
                    <span class="subheading">Let's say no to scams</span>
                </div>
            </header>

            <article>
                <div class="container">
                    <div class="row">
                        <div class="col-md-10 col-lg-8 mx-auto">
                            <div class="post-preview">
                                <h2 class="post-title"><?= htmlentities($result->title) ?></h2>
                                <p class="post-meta">
                                    Posted by <b><?= htmlentities($result->publisher) ?></b>
                                    on <?= htmlentities($result->posted_at) ?>
                                </p>

                                <!-- Image Slider -->
                                <div class="image-slider">
                                    <?php
                                    $images = [
                                        htmlentities($result->image1),
                                        htmlentities($result->image2),
                                        htmlentities($result->image3),
                                        htmlentities($result->image4)
                                    ];
                                    foreach ($images as $index => $image):
                                        if (!empty($image)):
                                            $activeClass = $index === 0 ? 'active' : '';
                                            echo "<img src='admin/img/news_images/$image' alt='Image' class='$activeClass'>";
                                        endif;
                                    endforeach;
                                    ?>
                                    <div class="slider-nav">
                                        <button id="prevBtn">&#10094;</button>
                                        <button id="nextBtn">&#10095;</button>
                                    </div>
                                </div>

                                <!-- Description -->
                                <div class="description-container">
                                    <p class="description-text">
                                        <?= strip_tags(html_entity_decode($result->description), '<p><br>') ?>
                                    </p>
                                </div>
                            </div>

                            <?php include 'includes/footer.php'; ?>
                        </div>
                    </div>
                </div>
            </article>
    <?php
        endforeach;
    endif;
    ?>

    <script>
        $(document).ready(function () {
            var currentIndex = 0;
            var $images = $('.image-slider img');
            var totalImages = $images.length;
            var autoSlideInterval;
            var manualControlTimeout;

            // Show the first image
            $images.eq(currentIndex).addClass('active');

            // Function to show the selected image
            function showImage(index) {
                $images.removeClass('active');
                $images.eq(index).addClass('active');
            }

            // Next button
            $('#nextBtn').click(function () {
                currentIndex = (currentIndex + 1) % totalImages;
                showImage(currentIndex);
                stopAutoSlide();
            });

            // Previous button
            $('#prevBtn').click(function () {
                currentIndex = (currentIndex - 1 + totalImages) % totalImages;
                showImage(currentIndex);
                stopAutoSlide();
            });

            // Automatic sliding
            function startAutoSlide() {
                autoSlideInterval = setInterval(function () {
                    currentIndex = (currentIndex + 1) % totalImages;
                    showImage(currentIndex);
                }, 3000);
            }

            // Stop auto slide temporarily
            function stopAutoSlide() {
                clearInterval(autoSlideInterval);
                clearTimeout(manualControlTimeout);
                manualControlTimeout = setTimeout(startAutoSlide, 10000);
            }

            // Start auto slide on load
            startAutoSlide();
        });
    </script>
</body>

</html>
