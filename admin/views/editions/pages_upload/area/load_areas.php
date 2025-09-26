<?php
require_once '../../../../../config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    if (!isset($_GET['edition_image_id'])) {
        throw new Exception('Missing edition_image_id parameter');
    }
    
    $edition_image_id = (int)$_GET['edition_image_id'];
    
    // Validate edition_image_id exists
    $stmt = $pdo->prepare("SELECT id FROM edition_images WHERE id = ?");
    $stmt->execute([$edition_image_id]);
    if (!$stmt->fetch()) {
        throw new Exception('Invalid edition image ID');
    }
    
    // Load areas for this image
    $stmt = $pdo->prepare("
        SELECT id, x, y, width, height, label, image_url, created_at 
        FROM area_mappings 
        WHERE edition_image_id = ? 
        ORDER BY created_at ASC
    ");
    $stmt->execute([$edition_image_id]);
    
    $areas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'areas' => $areas,
        'count' => count($areas)
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false, 
        'message' => $e->getMessage()
    ]);
}
?>