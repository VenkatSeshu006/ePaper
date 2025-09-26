<?php
require_once '../../../../../config.php';
require_once '../../../../controllers/EditionController.php';

header('Content-Type: application/json');

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid JSON input']);
    exit;
}

$edition_id = isset($input['edition_id']) ? (int)$input['edition_id'] : 0;
$page_id = isset($input['page_id']) ? (int)$input['page_id'] : 0;
$areas = isset($input['areas']) ? $input['areas'] : [];

if (!$edition_id || !$page_id || empty($areas)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit;
}

try {
    $editionController = new EditionController($pdo);
    
    // Verify edition exists
    $edition = $editionController->getEditionById($edition_id);
    if (!$edition) {
        throw new Exception('Edition not found');
    }
    
    // Verify page exists
    $page = $editionController->getImageById($page_id);
    if (!$page) {
        throw new Exception('Page not found');
    }
    
    // Get the full path to the source image
    $source_image_path = $_SERVER['DOCUMENT_ROOT'] . '/todom.fun/' . $page['image_path'];
    
    if (!file_exists($source_image_path)) {
        throw new Exception('Source image file not found: ' . $source_image_path);
    }
    
    // Create areamaps directory structure
    $areamaps_dir = $_SERVER['DOCUMENT_ROOT'] . '/todom.fun/uploads/areamaps';
    $edition_dir = $areamaps_dir . '/' . $edition['name'];
    $page_dir = $edition_dir . '/page_' . $page['order'];
    
    if (!is_dir($areamaps_dir)) {
        mkdir($areamaps_dir, 0755, true);
    }
    if (!is_dir($edition_dir)) {
        mkdir($edition_dir, 0755, true);
    }
    if (!is_dir($page_dir)) {
        mkdir($page_dir, 0755, true);
    }
    
    // Get image info
    $image_info = getimagesize($source_image_path);
    if (!$image_info) {
        throw new Exception('Could not get image information');
    }
    
    $image_width = $image_info[0];
    $image_height = $image_info[1];
    $image_type = $image_info[2];
    
    // Create source image resource
    switch ($image_type) {
        case IMAGETYPE_JPEG:
            $source_image = imagecreatefromjpeg($source_image_path);
            break;
        case IMAGETYPE_PNG:
            $source_image = imagecreatefrompng($source_image_path);
            break;
        case IMAGETYPE_GIF:
            $source_image = imagecreatefromgif($source_image_path);
            break;
        default:
            throw new Exception('Unsupported image type');
    }
    
    if (!$source_image) {
        throw new Exception('Could not create image resource');
    }
    
    $cropped_files = [];
    
    // Process each area
    foreach ($areas as $index => $area) {
        // Convert relative coordinates to absolute pixels
        $x = (float)$area['relativeX'] * $image_width;
        $y = (float)$area['relativeY'] * $image_height;
        $width = (float)$area['relativeWidth'] * $image_width;
        $height = (float)$area['relativeHeight'] * $image_height;
        
        // Ensure coordinates are within image bounds
        $x = max(0, min($x, $image_width - 1));
        $y = max(0, min($y, $image_height - 1));
        $width = max(1, min($width, $image_width - $x));
        $height = max(1, min($height, $image_height - $y));
        
        // Create cropped image
        $cropped_image = imagecreatetruecolor($width, $height);
        
        // Preserve transparency for PNG images
        if ($image_type == IMAGETYPE_PNG) {
            imagealphablending($cropped_image, false);
            imagesavealpha($cropped_image, true);
            $transparent = imagecolorallocatealpha($cropped_image, 255, 255, 255, 127);
            imagefill($cropped_image, 0, 0, $transparent);
        }
        
        // Copy the area from source to cropped image
        imagecopy($cropped_image, $source_image, 0, 0, $x, $y, $width, $height);
        
        // Generate filename
        $area_label = isset($area['label']) && !empty($area['label']) ? 
                     preg_replace('/[^a-zA-Z0-9_-]/', '_', $area['label']) : 
                     'area_' . ($index + 1);
        
        $filename = $area_label . '_' . date('YmdHis') . '_' . uniqid();
        
        // Save the cropped image
        $output_path = $page_dir . '/' . $filename;
        
        switch ($image_type) {
            case IMAGETYPE_JPEG:
                $output_path .= '.jpg';
                $success = imagejpeg($cropped_image, $output_path, 90);
                break;
            case IMAGETYPE_PNG:
                $output_path .= '.png';
                $success = imagepng($cropped_image, $output_path, 6);
                break;
            case IMAGETYPE_GIF:
                $output_path .= '.gif';
                $success = imagegif($cropped_image, $output_path);
                break;
        }
        
        if ($success) {
            $relative_path = 'uploads/areamaps/' . $edition['name'] . '/page_' . $page['order'] . '/' . basename($output_path);
            $cropped_files[] = [
                'area_index' => $index,
                'label' => $area_label,
                'filename' => basename($output_path),
                'path' => $relative_path,
                'coordinates' => [
                    'x' => round($x),
                    'y' => round($y),
                    'width' => round($width),
                    'height' => round($height)
                ]
            ];
        }
        
        // Clean up cropped image resource
        imagedestroy($cropped_image);
    }
    
    // Clean up source image resource
    imagedestroy($source_image);
    
    echo json_encode([
        'success' => true,
        'message' => 'Areas cropped and saved successfully',
        'cropped_files' => $cropped_files,
        'total_areas' => count($areas),
        'successful_crops' => count($cropped_files)
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error cropping areas: ' . $e->getMessage()
    ]);
}
?>