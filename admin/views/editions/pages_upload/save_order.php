<?php
require_once '../../../../config.php';
require_once '../../../controllers/EditionController.php';

header('Content-Type: application/json');

$editionController = new EditionController($pdo);

// Expect JSON data from SortableJS
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['order']) || !is_array($input['order'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid order data']);
    exit;
}

try {
    foreach ($input['order'] as $item) {
        $image_id = (int)$item['id'];
        $new_order = (int)$item['order'];
        $editionController->updateImageOrder($image_id, $new_order);
    }
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Failed to update order: ' . $e->getMessage()]);
}
?>