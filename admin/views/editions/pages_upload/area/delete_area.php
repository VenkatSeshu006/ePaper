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
    
    if (!isset($input['area_id'])) {
        throw new Exception('Missing area_id parameter');
    }
    
    $area_id = (int)$input['area_id'];
    
    // Get the area details including image_url before deletion
    $stmt = $pdo->prepare("SELECT image_url FROM area_mappings WHERE id = ?");
    $stmt->execute([$area_id]);
    $area = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$area) {
        throw new Exception('Area not found');
    }
    
    // Delete the area from database
    $stmt = $pdo->prepare("DELETE FROM area_mappings WHERE id = ?");
    $stmt->execute([$area_id]);
    
    // Delete the associated image file if it exists
    if (!empty($area['image_url'])) {
        $imagePath = '../../../../../' . $area['image_url'];
        if (file_exists($imagePath)) {
            if (!unlink($imagePath)) {
                // Log the error but don't fail the deletion
                error_log("Failed to delete image file: " . $imagePath);
            }
        }
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Area and associated image deleted successfully'
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>