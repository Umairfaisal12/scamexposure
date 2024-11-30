<?php
session_set_cookie_params(0);
session_start();
include('includes/config.php');

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if the user is logged in
if (strlen($_SESSION['alogin']) == 0) {
    header('location: login.php');
    exit;
}

// Handle Delete Action
if (isset($_GET['del'])) {
    $id = intval($_GET['del']);
    $sql = "DELETE FROM scam_news WHERE id = :id";
    $query = $dbh->prepare($sql);
    $query->bindParam(':id', $id, PDO::PARAM_INT);
    if ($query->execute()) {
        // Redirect immediately after delete
        header('Location: adminnews.php');
        exit;
    } else {
        echo "<script>alert('Failed to delete news');</script>";
    }
}

// Handle Publish Action
if (isset($_GET['action']) && $_GET['action'] === 'publish' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "UPDATE scam_news SET published = 1 WHERE id = :id";
    $query = $dbh->prepare($sql);
    $query->bindParam(':id', $id, PDO::PARAM_INT);
    
    if ($query->execute()) {
        // Redirect immediately after publish
        header('Location: adminnews.php');
        exit;
    } else {
        $errorInfo = $query->errorInfo(); // Get error information
        echo "<script>alert('Failed to publish news: " . htmlspecialchars($errorInfo[2]) . "');</script>";
    }
}

// Handle Unpublish Action
if (isset($_GET['action']) && $_GET['action'] === 'unpublish' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "UPDATE scam_news SET published = 0 WHERE id = :id";
    $query = $dbh->prepare($sql);
    $query->bindParam(':id', $id, PDO::PARAM_INT);
    
    if ($query->execute()) {
        // Redirect immediately after unpublish
        header('Location: adminnews.php');
        exit;
    } else {
        $errorInfo = $query->errorInfo(); // Get error information
        echo "<script>alert('Failed to unpublish news: " . htmlspecialchars($errorInfo[2]) . "');</script>";
    }
}

// Redirect to adminnews.php if there's no action or no errors
header('Location: adminnews.php');
exit;
?>
