<?php
// public/save_clip.php
require_once '../config.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

/**
 * Function to create composite image with logo above or below cropped image
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

// Log the request for debugging
error_log("Clip save request received: " . print_r($_POST, true));
error_log("Files received: " . print_r($_FILES, true));

// Check if the request is POST and has required data
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    error_log("Invalid request method: " . $_SERVER['REQUEST_METHOD']);
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

if (!isset($_FILES['image'])) {
    error_log("No image file uploaded");
    echo json_encode(['success' => false, 'message' => 'No image file uploaded']);
    exit;
}

if (!isset($_POST['edition_id']) || !isset($_POST['image_id'])) {
    error_log("Missing edition_id or image_id");
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit;
}

$edition_id = (int)$_POST['edition_id'];
$image_id = (int)$_POST['image_id'];

error_log("Processing clip for edition_id: $edition_id, image_id: $image_id");

// Validate edition and image exist
try {
    $stmt = $pdo->prepare("SELECT id FROM editions WHERE id = :id");
    $stmt->execute(['id' => $edition_id]);
    if (!$stmt->fetch()) {
        error_log("Invalid edition ID: $edition_id");
        echo json_encode(['success' => false, 'message' => 'Invalid edition ID']);
        exit;
    }

    $stmt = $pdo->prepare("SELECT id FROM edition_images WHERE id = :id AND edition_id = :edition_id");
    $stmt->execute(['id' => $image_id, 'edition_id' => $edition_id]);
    if (!$stmt->fetch()) {
        error_log("Invalid image ID: $image_id for edition: $edition_id");
        echo json_encode(['success' => false, 'message' => 'Invalid image ID']);
        exit;
    }
} catch (Exception $e) {
    error_log("Database validation error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error during validation']);
    exit;
}

// Handle file upload
$upload_dir = '../uploads/clips/';
if (!is_dir($upload_dir)) {
    error_log("Creating clips directory: $upload_dir");
    if (!mkdir($upload_dir, 0777, true)) {
        error_log("Failed to create clips directory");
        echo json_encode(['success' => false, 'message' => 'Failed to create upload directory']);
        exit;
    }
}

// Check file upload errors
if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    error_log("File upload error: " . $_FILES['image']['error']);
    echo json_encode(['success' => false, 'message' => 'File upload error: ' . $_FILES['image']['error']]);
    exit;
}

$timestamp = date('YmdHis');
$filename = "clip_{$edition_id}_{$image_id}_{$timestamp}.jpg";
$filepath = $upload_dir . $filename;
$public_path = "/uploads/clips/{$filename}";

error_log("Attempting to save file to: $filepath");

if (move_uploaded_file($_FILES['image']['tmp_name'], $filepath)) {
    error_log("File saved successfully to: $filepath");
    
    // Get area mapping logo and placement from settings
    try {
        $stmt = $pdo->prepare("SELECT key_name, value FROM settings WHERE key_name IN ('area_mapping_logo', 'area_mapping_logo_position')");
        $stmt->execute();
        $settingsResults = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        
        $logoPath = null;
        $placement = 'top'; // Default placement
        
        if (!empty($settingsResults['area_mapping_logo'])) {
            $logoPath = '../' . $settingsResults['area_mapping_logo'];
        }
        
        if (!empty($settingsResults['area_mapping_logo_position'])) {
            $placement = $settingsResults['area_mapping_logo_position'];
        }
        
        // Merge logo with clipped image if logo exists
        if ($logoPath && file_exists($logoPath)) {
            error_log("Merging logo with clip image. Logo: $logoPath, Placement: $placement");
            mergeLogoWithImage($filepath, $logoPath, $placement);
        } else {
            error_log("No logo to merge or logo file not found: $logoPath");
        }
    } catch (Exception $e) {
        error_log("Error retrieving logo settings: " . $e->getMessage());
        // Continue without logo if settings retrieval fails
    }
    
    // Save to database
    try {
        $stmt = $pdo->prepare("
            INSERT INTO clipped_images (edition_id, image_id, clip_path, created_at)
            VALUES (:edition_id, :image_id, :clip_path, NOW())
        ");
        $stmt->execute([
            'edition_id' => $edition_id,
            'image_id' => $image_id,
            'clip_path' => $public_path
        ]);
        $clip_id = $pdo->lastInsertId();
        
        error_log("Clip saved to database with ID: $clip_id");

        echo json_encode([
            'success' => true,
            'clip_id' => $clip_id,
            'clip_path' => $public_path
        ]);
    } catch (Exception $e) {
        error_log("Database insert error: " . $e->getMessage());
        // Clean up the uploaded file if database insert fails
        if (file_exists($filepath)) {
            unlink($filepath);
        }
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    error_log("Failed to move uploaded file from " . $_FILES['image']['tmp_name'] . " to $filepath");
    echo json_encode(['success' => false, 'message' => 'Failed to save image file']);
}
?>