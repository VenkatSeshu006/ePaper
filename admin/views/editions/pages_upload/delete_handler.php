<?php
require_once '../../../../config.php';
require_once '../../../controllers/EditionController.php';

header('Content-Type: application/json');

$editionController = new EditionController($pdo);

// Expect JSON data with an array of IDs
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['ids']) || !is_array($input['ids']) || empty($input['ids'])) {
    echo json_encode(['success' => false, 'error' => 'No IDs provided']);
    exit;
}

try {
    foreach ($input['ids'] as $id) {
        $editionController->deleteEditionImage((int)$id);
    }
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Failed to delete: ' . $e->getMessage()]);
}
?>