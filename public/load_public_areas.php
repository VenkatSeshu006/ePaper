<?php
// public/load_public_areas.php
require_once '../config.php';

// Set content type to JSON
header('Content-Type: application/json');

// Get the edition image ID from the request
$edition_image_id = isset($_GET['edition_image_id']) ? (int)$_GET['edition_image_id'] : null;

if (!$edition_image_id) {
    http_response_code(400);
    echo json_encode(['error' => 'Edition image ID is required']);
    exit;
}

try {
    // Verify that the edition image exists and is public
    $stmt = $pdo->prepare("
        SELECT ei.id, ei.image_path, e.id as edition_id
        FROM edition_images ei
        JOIN editions e ON ei.edition_id = e.id
        WHERE ei.id = ?
    ");
    $stmt->execute([$edition_image_id]);
    $image = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$image) {
        http_response_code(404);
        echo json_encode(['error' => 'Edition image not found']);
        exit;
    }
    
    // Fetch all areas for this edition image
    $stmt = $pdo->prepare("
        SELECT x, y, width, height, label, image_url
        FROM area_mappings
        WHERE edition_image_id = ?
        ORDER BY id ASC
    ");
    $stmt->execute([$edition_image_id]);
    $areas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Return the areas as JSON
    echo json_encode([
        'success' => true,
        'areas' => $areas,
        'edition_image_id' => $edition_image_id
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>