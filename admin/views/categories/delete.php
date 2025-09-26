<?php
// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect to login page if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

require_once '../../../config.php'; // Database connection
require_once '../../controllers/CategoryController.php';

$categoryController = new CategoryController($pdo);

// Check if the category ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php?error=1"); // Redirect with an error query parameter
    exit();
}

$category_id = $_GET['id'];

// Attempt to delete the category
try {
    $success = $categoryController->deleteCategory($category_id);

    if ($success) {
        header("Location: index.php?success=1"); // Redirect with a success query parameter
        exit();
    } else {
        header("Location: index.php?error=2"); // Redirect with an error query parameter
        exit();
    }
} catch (Exception $e) {
    error_log("Error deleting category: " . $e->getMessage());
    header("Location: index.php?error=3"); // Redirect with a generic error query parameter
    exit();
}
?>