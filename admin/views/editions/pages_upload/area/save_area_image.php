<?php
require_once '../../../../../config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

/**
 * Function to create composite image with logo above cropped image
 */
function mergeLogoWithImage($croppedImagePath, $logoPath, $placement = 'top') {
    // Check if logo file exists
    if (!file_exists($logoPath)) {
        return $croppedImagePath; // Return original if no logo
    }
    
    // Get image info
    $croppedInfo = getimagesize($croppedImagePath);
    $logoInfo = getimagesize($logoPath);
    
    if (!$croppedInfo || !$logoInfo) {
        return $croppedImagePath; // Return original if can't read images
    }
    
    // Create image resources
    $croppedImage = null;
    $logoImage = null;
    
    // Load cropped image based on type
    switch ($croppedInfo[2]) {
        case IMAGETYPE_JPEG:
            $croppedImage = imagecreatefromjpeg($croppedImagePath);
            break;
        case IMAGETYPE_PNG:
            $croppedImage = imagecreatefrompng($croppedImagePath);
            break;
        default:
            return $croppedImagePath;
    }
    
    // Load logo image based on type
    switch ($logoInfo[2]) {
        case IMAGETYPE_JPEG:
            $logoImage = imagecreatefromjpeg($logoPath);
            break;
        case IMAGETYPE_PNG:
            $logoImage = imagecreatefrompng($logoPath);
            break;
        default:
            imagedestroy($croppedImage);
            return $croppedImagePath;
    }
    
    if (!$croppedImage || !$logoImage) {
        if ($croppedImage) imagedestroy($croppedImage);
        if ($logoImage) imagedestroy($logoImage);
        return $croppedImagePath;
    }
    
    // Get dimensions
    $croppedWidth = imagesx($croppedImage);
    $croppedHeight = imagesy($croppedImage);
    $logoWidth = imagesx($logoImage);
    $logoHeight = imagesy($logoImage);
    
    // Calculate logo size (max 80% of cropped image width, but reasonable height)
    $maxLogoWidth = (int)($croppedWidth * 0.8);
    $maxLogoHeight = 100; // Maximum logo height in pixels
    
    // Scale logo if needed
    if ($logoWidth > $maxLogoWidth || $logoHeight > $maxLogoHeight) {
        $scale = min($maxLogoWidth / $logoWidth, $maxLogoHeight / $logoHeight);
        $newLogoWidth = (int)($logoWidth * $scale);
        $newLogoHeight = (int)($logoHeight * $scale);
        
        // Create resized logo
        $resizedLogo = imagecreatetruecolor($newLogoWidth, $newLogoHeight);
        
        // Preserve transparency for PNG
        if ($logoInfo[2] == IMAGETYPE_PNG) {
            imagealphablending($resizedLogo, false);
            imagesavealpha($resizedLogo, true);
            $transparent = imagecolorallocatealpha($resizedLogo, 255, 255, 255, 127);
            imagefill($resizedLogo, 0, 0, $transparent);
        }
        
        imagecopyresampled($resizedLogo, $logoImage, 0, 0, 0, 0, $newLogoWidth, $newLogoHeight, $logoWidth, $logoHeight);
        imagedestroy($logoImage);
        $logoImage = $resizedLogo;
        $logoWidth = $newLogoWidth;
        $logoHeight = $newLogoHeight;
    }
    
    // Create composite image based on placement
    $padding = 10; // Padding between logo and cropped image
    $compositeWidth = max($croppedWidth, $logoWidth);
    $compositeHeight = $logoHeight + $padding + $croppedHeight;
    
    // Create composite canvas
    $compositeImage = imagecreatetruecolor($compositeWidth, $compositeHeight);
    
    // Set white background
    $white = imagecolorallocate($compositeImage, 255, 255, 255);
    imagefill($compositeImage, 0, 0, $white);
    
    // Enable alpha blending for proper transparency
    imagealphablending($compositeImage, true);
    
    // Position elements based on placement setting
    if ($placement === 'bottom') {
        // Logo at bottom: cropped image first, then logo
        $croppedX = (int)(($compositeWidth - $croppedWidth) / 2);
        $croppedY = 0;
        
        $logoX = (int)(($compositeWidth - $logoWidth) / 2);
        $logoY = $croppedHeight + $padding;
    } else {
        // Logo at top (default): logo first, then cropped image
        $logoX = (int)(($compositeWidth - $logoWidth) / 2);
        $logoY = 0;
        
        $croppedX = (int)(($compositeWidth - $croppedWidth) / 2);
        $croppedY = $logoHeight + $padding;
    }
    
    // Copy logo to composite image
    imagecopy($compositeImage, $logoImage, $logoX, $logoY, 0, 0, $logoWidth, $logoHeight);
    
    // Copy cropped image to composite image
    imagecopy($compositeImage, $croppedImage, $croppedX, $croppedY, 0, 0, $croppedWidth, $croppedHeight);
    
    // Save composite image
    $success = false;
    switch ($croppedInfo[2]) {
        case IMAGETYPE_JPEG:
            $success = imagejpeg($compositeImage, $croppedImagePath, 90);
            break;
        case IMAGETYPE_PNG:
            $success = imagepng($compositeImage, $croppedImagePath);
            break;
    }
    
    // Clean up
    imagedestroy($croppedImage);
    imagedestroy($logoImage);
    imagedestroy($compositeImage);
    
    return $success ? $croppedImagePath : $croppedImagePath;
}

try {
    // Validate required parameters
    if (!isset($_POST['edition_image_id']) || !isset($_POST['area_number'])) {
        throw new Exception('Missing required parameters');
    }
    
    $edition_image_id = (int)$_POST['edition_image_id'];
    $area_number = (int)$_POST['area_number'];
    
    // Validate edition_image_id exists
    $stmt = $pdo->prepare("SELECT id FROM edition_images WHERE id = ?");
    $stmt->execute([$edition_image_id]);
    if (!$stmt->fetch()) {
        throw new Exception('Invalid edition image ID');
    }
    
    // Check if file was uploaded
    if (!isset($_FILES['cropped_image']) || $_FILES['cropped_image']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('No file uploaded or upload error');
    }
    
    $uploadedFile = $_FILES['cropped_image'];
    
    // Validate file type
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
    if (!in_array($uploadedFile['type'], $allowedTypes)) {
        throw new Exception('Invalid file type. Only JPEG and PNG are allowed.');
    }
    
    // Create upload directory if it doesn't exist
    $uploadDir = '../../../../../uploads/areamaps/';
    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            throw new Exception('Failed to create upload directory');
        }
    }
    
    // Generate unique filename
    $fileExtension = pathinfo($uploadedFile['name'], PATHINFO_EXTENSION);
    $fileName = 'area_' . $edition_image_id . '_' . $area_number . '_' . time() . '.' . $fileExtension;
    $filePath = $uploadDir . $fileName;
    
    // Move uploaded file
    if (!move_uploaded_file($uploadedFile['tmp_name'], $filePath)) {
        throw new Exception('Failed to save uploaded file');
    }
    
    // Get area mapping logo and placement from settings
    $stmt = $pdo->prepare("SELECT key_name, value FROM settings WHERE key_name IN ('area_mapping_logo', 'area_mapping_logo_position')");
    $stmt->execute();
    $settingsResults = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    
    $logoPath = null;
    $placement = 'top'; // Default placement
    
    if (!empty($settingsResults['area_mapping_logo'])) {
        $logoPath = '../../../../../' . $settingsResults['area_mapping_logo'];
    }
    
    if (!empty($settingsResults['area_mapping_logo_position'])) {
        $placement = $settingsResults['area_mapping_logo_position'];
    }
    
    if ($logoPath) {
        // Merge logo with cropped image using the specified placement
        mergeLogoWithImage($filePath, $logoPath, $placement);
    }
    
    // Return the public path for database storage
    $publicPath = 'uploads/areamaps/' . $fileName;
    
    echo json_encode([
        'success' => true,
        'message' => 'Area image saved successfully with logo',
        'image_path' => $publicPath,
        'filename' => $fileName
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>