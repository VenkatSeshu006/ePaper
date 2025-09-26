<?php
session_start(); // Start session

// Redirect to login page if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

require_once '../../../config.php'; // Database connection
require_once '../../controllers/EditionController.php';

$editionController = new EditionController($pdo);

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$editionController->deleteEdition($_GET['id']);
header("Location: index.php");
exit();
?>