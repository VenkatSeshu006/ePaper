<?php
// admin/views/user-clips/delete.php
require_once '../../../config.php';
require_once '../../controllers/ClipController.php';

$clipController = new ClipController($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    // Single delete
    $clip_id = (int)$_GET['id'];
    if ($clipController->deleteClip($clip_id)) {
        header("Location: index.php?success=1");
    } else {
        header("Location: index.php?error=2");
    }
    exit;
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'delete_multiple' && !empty($_POST['clip_ids'])) {
        // Multiple delete
        $clip_ids = array_map('intval', $_POST['clip_ids']);
        if ($clipController->deleteMultipleClips($clip_ids)) {
            header("Location: index.php?success=1");
        } else {
            header("Location: index.php?error=2");
        }
    } elseif ($_POST['action'] === 'delete_older_than' && isset($_POST['time_frame'])) {
        // Time-based delete
        $time_frame = $_POST['time_frame'];
        if ($clipController->deleteClipsOlderThan($time_frame)) {
            header("Location: index.php?success=1");
        } else {
            header("Location: index.php?error=2");
        }
    } else {
        header("Location: index.php?error=1");
    }
    exit;
} else {
    header("Location: index.php?error=1");
    exit;
}
?>