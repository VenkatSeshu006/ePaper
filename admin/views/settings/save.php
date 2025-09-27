<?php
require_once '../../../config.php'; // Database connection

// Check if the logged-in user is an admin
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

$error = '';
$success = '';

try {
    // Handle text fields
    foreach ($_POST as $key => $value) {
        $stmt = $pdo->prepare("UPDATE settings SET value = ?, updated_at = NOW() WHERE key_name = ?");
        $stmt->execute([$value, $key]);
    }

    // Handle file uploads
    $uploadDir = "../../../uploads/settings/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    if (!empty($_FILES['site_favicon']['tmp_name'])) {
        $faviconPath = $uploadDir . basename($_FILES['site_favicon']['name']);
        move_uploaded_file($_FILES['site_favicon']['tmp_name'], $faviconPath);
        $stmt = $pdo->prepare("UPDATE settings SET value = ? WHERE key_name = 'site_favicon'");
        $stmt->execute(["/uploads/settings/" . basename($_FILES['site_favicon']['name'])]);
    }

    if (!empty($_FILES['site_logo']['tmp_name'])) {
        $logoPath = $uploadDir . basename($_FILES['site_logo']['name']);
        move_uploaded_file($_FILES['site_logo']['tmp_name'], $logoPath);
        $stmt = $pdo->prepare("UPDATE settings SET value = ? WHERE key_name = 'site_logo'");
        $stmt->execute(["/ePaper/uploads/settings/" . basename($_FILES['site_logo']['name'])]);
    }

    $success = 'Settings updated successfully.';
} catch (PDOException $e) {
    $error = 'Error: ' . $e->getMessage();
}

header("Location: index.php");
exit();