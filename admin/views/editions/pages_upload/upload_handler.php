<?php
require_once '../../../../config.php';
require_once '../../../controllers/EditionController.php';

header('Content-Type: application/json');

$editionController = new EditionController($pdo);

$edition_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$edition = $editionController->getEditionById($edition_id);

if (!$edition) {
    echo json_encode(['success' => false, 'error' => 'Invalid edition ID']);
    exit;
}

$upload_dir = "/uploads/editions/{$edition['alias']}-{$edition['edition_date']}/";
$full_upload_path = $_SERVER['DOCUMENT_ROOT'] . $upload_dir;
if (!file_exists($full_upload_path)) {
    mkdir($full_upload_path, 0777, true);
}

$images = $editionController->getEditionImages($edition_id);
$max_order = !empty($images) ? max(array_column($images, 'order')) : 0;

if (isset($_POST['files']) && is_array($_POST['files'])) {
    $uploaded_files = [];
    foreach ($_POST['files'] as $index => $file_data) {
        if (preg_match('/^data:image\/jpeg;base64,/', $file_data)) {
            $file_data = str_replace('data:image/jpeg;base64,', '', $file_data);
            $file_data = base64_decode($file_data);
            if ($file_data === false) {
                error_log("Invalid file data at index $index for edition $edition_id");
                echo json_encode(['success' => false, 'error' => 'Invalid file data at index ' . $index]);
                exit;
            }

            $order = $max_order + $index + 1;
            $file_name = "page_$order.jpg";
            $file_path = $full_upload_path . $file_name;

            if (file_put_contents($file_path, $file_data)) {
                $db_path = $upload_dir . $file_name;
                $editionController->addEditionImage($edition_id, $db_path, $order);
                $uploaded_files[] = $db_path . '?v=' . time(); // Add cache-busting parameter
            } else {
                error_log("Failed to save file at index $index for edition $edition_id");
                echo json_encode(['success' => false, 'error' => 'Failed to save file at index ' . $index]);
                exit;
            }
        } else {
            error_log("Unexpected file data format at index $index for edition $edition_id: " . substr($file_data, 0, 50));
            echo json_encode(['success' => false, 'error' => 'Unexpected file format at index ' . $index]);
            exit;
        }
    }

    echo json_encode(['success' => true, 'files' => $uploaded_files]);
} else {
    echo json_encode(['success' => false, 'error' => 'No files received']);
}
?>