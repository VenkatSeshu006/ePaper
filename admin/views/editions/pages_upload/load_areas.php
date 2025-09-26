<?php
require_once '../../../../config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

try {
    if (!isset($_GET['image_id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing required parameter: image_id']);
        exit;
    }
    
    $image_id = (int)$_GET['image_id'];
    
    // Fetch areas for this image
    $stmt = $pdo->prepare("
        SELECT id, name, x, y, width, height, created_at, updated_at 
        FROM area_mappings 
        WHERE image_id = ? 
        ORDER BY created_at ASC
    ");
    
    $stmt->execute([$image_id]);
    $areas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format areas for JavaScript consumption
    $formattedAreas = [];
    foreach ($areas as $area) {
        $formattedAreas[] = [
            'id' => 'area_' . $area['id'],
            'db_id' => $area['id'],
            'name' => $area['name'],
            'x' => (int)$area['x'],
            'y' => (int)$area['y'],
            'width' => (int)$area['width'],
            'height' => (int)$area['height'],
            'created_at' => $area['created_at'],
            'updated_at' => $area['updated_at']
        ];
    }
    
    echo json_encode([
        'success' => true,
        'areas' => $formattedAreas,
        'count' => count($formattedAreas)
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
?>