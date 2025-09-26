<?php
require_once '../../../../config.php';
require_once '../../../controllers/EditionController.php';

header('Content-Type: application/json');

$editionController = new EditionController($pdo);

if (!isset($_POST['id']) || !isset($_FILES['file'])) {
    echo json_encode(['success' => false, 'error' => 'Missing ID or file']);
    exit;
}

$image_id = (int)$_POST['id'];
$file = $_FILES['file'];

// Fetch existing image details
$stmt = $pdo->prepare("SELECT edition_id, image_path, `order` FROM edition_images WHERE id = ?");
$stmt->execute([$image_id]);
$image = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$image) {
    echo json_encode(['success' => false, 'error' => 'Image not found']);
    exit;
}

$edition_id = $image['edition_id'];
$order = $image['order'];
$edition = $editionController->getEditionById($edition_id);

$upload_dir = "/uploads/editions/{$edition['alias']}-{$edition['edition_date']}/";
$full_upload_path = $_SERVER['DOCUMENT_ROOT'] . $upload_dir;

$old_file = $_SERVER['DOCUMENT_ROOT'] . $image['image_path'];
if (file_exists($old_file)) {
    unlink($old_file);
}

$file_name = "page_$order.jpg";
$file_path = $full_upload_path . $file_name;

if (move_uploaded_file($file['tmp_name'], $file_path)) {
    $db_path = $upload_dir . $file_name;
    $stmt = $pdo->prepare("UPDATE edition_images SET image_path = ? WHERE id = ?");
    $stmt->execute([$db_path, $image_id]);
    echo json_encode(['success' => true, 'new_path' => $db_path . '?v=' . time()]); // Add cache-busting parameter
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to upload new file']);
}
?>