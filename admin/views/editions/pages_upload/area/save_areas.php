<?php
require_once '../../../../../config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['edition_image_id']) || !isset($input['areas'])) {
        throw new Exception('Missing required parameters');
    }
    
    $edition_image_id = (int)$input['edition_image_id'];
    $areas = $input['areas'];
    
    // Validate edition_image_id exists
    $stmt = $pdo->prepare("SELECT id FROM edition_images WHERE id = ?");
    $stmt->execute([$edition_image_id]);
    if (!$stmt->fetch()) {
        throw new Exception('Invalid edition image ID');
    }
    
    // Begin transaction
    $pdo->beginTransaction();
    
    // Get existing areas with image URLs before deletion
    $stmt = $pdo->prepare("SELECT image_url FROM area_mappings WHERE edition_image_id = ? AND image_url IS NOT NULL");
    $stmt->execute([$edition_image_id]);
    $existingImages = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Delete existing areas for this image
    $stmt = $pdo->prepare("DELETE FROM area_mappings WHERE edition_image_id = ?");
    $stmt->execute([$edition_image_id]);
    
    // Delete associated image files
    foreach ($existingImages as $imageUrl) {
        if (!empty($imageUrl)) {
            $imagePath = '../../../../../' . $imageUrl;
            if (file_exists($imagePath)) {
                if (!unlink($imagePath)) {
                    error_log("Failed to delete image file: " . $imagePath);
                }
            }
        }
    }
    
    // Insert new areas
    $stmt = $pdo->prepare("
        INSERT INTO area_mappings (edition_image_id, x, y, width, height, label, image_url) 
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    
    foreach ($areas as $area) {
        if (!isset($area['x'], $area['y'], $area['width'], $area['height'])) {
            throw new Exception('Invalid area data');
        }
        
        $stmt->execute([
            $edition_image_id,
            (float)$area['x'],
            (float)$area['y'],
            (float)$area['width'],
            (float)$area['height'],
            $area['label'] ?? null,
            $area['image_path'] ?? null
        ]);
    }
    
    // Commit transaction
    $pdo->commit();
    
    echo json_encode([
        'success' => true, 
        'message' => 'Areas saved successfully',
        'count' => count($areas)
    ]);
    
} catch (Exception $e) {
    // Rollback transaction on error
    if ($pdo->inTransaction()) {
        $pdo->rollback();
    }
    
    http_response_code(400);
    echo json_encode([
        'success' => false, 
        'message' => $e->getMessage()
    ]);
}
?>