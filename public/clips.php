<?php
// public/clips.php
require_once '../config.php';

// Get the clip ID from the URL
$clip_id = isset($_GET['id']) ? (int)$_GET['id'] : null;

if (!$clip_id) {
    // Redirect to a 404 page or homepage if no ID is provided
    header("Location: index.php");
    exit;
}

// Fetch the clip details from the database
$stmt = $pdo->prepare("
    SELECT ci.clip_path
    FROM clipped_images ci
    WHERE ci.id = :clip_id
");
$stmt->execute(['clip_id' => $clip_id]);
$clip = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$clip) {
    // Redirect if clip not found
    header("Location: index.php");
    exit;
}

// Fetch the area mapping logo from settings
$stmt = $pdo->prepare("SELECT value FROM settings WHERE key_name = 'area_mapping_logo'");
$stmt->execute();
$logo_result = $stmt->fetch(PDO::FETCH_ASSOC);
$area_mapping_logo = $logo_result ? $logo_result['value'] : '/uploads/settings/default-logo.png';

// Construct the full URL for sharing
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$base_url = $protocol . $_SERVER['HTTP_HOST'];
$clip_url = "$base_url/ePaper/public/clips.php?id=$clip_id";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clipped Image</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="assets/css/edition.css?v=<?php echo time(); ?>">
    <style>
        .popup-actions {
            padding: 20px;
            text-align: center;
        }
        .popup-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100px;
            height: 40px;
            margin: 0 10px;
            background-color: #2F5779;
            color: white;
            border: none;
            border-radius: 2px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
        }
        .popup-button:hover {
            background-color: #1E3A5F;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="card">
            <div class="card-header text-center">
                <img src="<?php echo htmlspecialchars($area_mapping_logo); ?>" alt="Site Logo" class="popup-logo">
            </div>
            <div class="card-footer social-share">
                <a href="#" class="social-share-btn" data-platform="facebook" data-url="<?php echo $clip_url; ?>"><i class="fab fa-facebook"></i></a>
                <a href="#" class="social-share-btn" data-platform="twitter" data-url="<?php echo $clip_url; ?>"><i class="fab fa-twitter"></i></a>
                <a href="#" class="social-share-btn" data-platform="whatsapp" data-url="<?php echo $clip_url; ?>"><i class="fab fa-whatsapp"></i></a>
                <a href="#" class="social-share-btn" data-platform="linkedin" data-url="<?php echo $clip_url; ?>"><i class="fab fa-linkedin"></i></a>
                <a href="#" class="social-share-btn" data-platform="telegram" data-url="<?php echo $clip_url; ?>"><i class="fab fa-telegram"></i></a>
                <a href="#" class="social-share-btn" data-platform="print" data-url="<?php echo $clip_url; ?>"><i class="fas fa-print"></i></a>
                <a href="#" class="social-share-btn" data-platform="email" data-url="<?php echo $clip_url; ?>"><i class="fas fa-envelope"></i></a>
            </div>
            <div class="card-body text-center">
                <?php 
                $clip_image_path = $clip['clip_path'];
                // Ensure path starts with forward slash for web access
                if (substr($clip_image_path, 0, 1) !== '/') {
                    $clip_image_path = '/' . $clip_image_path;
                }
                // Handle ePaper subdirectory - if path doesn't include /ePaper/, add it
                if (strpos($clip_image_path, '/ePaper/') === false && strpos($clip_image_path, '/uploads/') === 0) {
                    $clip_image_path = '/ePaper' . $clip_image_path;
                }
                ?>
                <img src="<?php echo htmlspecialchars($clip_image_path); ?>" alt="Clipped Image" class="popup-image" style="max-width: 100%; height: auto;"
                     onerror="console.error('Failed to load image:', this.src); this.style.display='none'; this.nextElementSibling.style.display='block';">
                <div style="display: none; padding: 20px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px;">
                    <p><strong>Image not found:</strong> <?php echo htmlspecialchars($clip_image_path); ?></p>
                    <p>Please check if the file exists at this location.</p>
                </div>
                
                <!-- Open and Download Buttons -->
                <div class="popup-actions mt-3">
                    <a href="<?php echo htmlspecialchars($clip_url); ?>" class="popup-button" target="_blank"><i class="fas fa-external-link-alt"></i> Open</a>
                    <a href="<?php echo htmlspecialchars($clip_image_path); ?>" class="popup-button" download><i class="fas fa-download"></i> Download</a>
                </div>
            </div>
            <div class="card-footer social-share">
                <a href="#" class="social-share-btn" data-platform="facebook" data-url="<?php echo $clip_url; ?>"><i class="fab fa-facebook"></i></a>
                <a href="#" class="social-share-btn" data-platform="twitter" data-url="<?php echo $clip_url; ?>"><i class="fab fa-twitter"></i></a>
                <a href="#" class="social-share-btn" data-platform="whatsapp" data-url="<?php echo $clip_url; ?>"><i class="fab fa-whatsapp"></i></a>
                <a href="#" class="social-share-btn" data-platform="linkedin" data-url="<?php echo $clip_url; ?>"><i class="fab fa-linkedin"></i></a>
                <a href="#" class="social-share-btn" data-platform="telegram" data-url="<?php echo $clip_url; ?>"><i class="fab fa-telegram"></i></a>
                <a href="#" class="social-share-btn" data-platform="print" data-url="<?php echo $clip_url; ?>"><i class="fas fa-print"></i></a>
                <a href="#" class="social-share-btn" data-platform="email" data-url="<?php echo $clip_url; ?>"><i class="fas fa-envelope"></i></a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.social-share-btn').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const platform = this.getAttribute('data-platform');
                const url = this.getAttribute('data-url');
                shareOnPlatform(platform, url);
            });
        });

        function shareOnPlatform(platform, url) {
            switch (platform) {
                case 'facebook':
                    window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`, '_blank');
                    break;
                case 'twitter':
                    window.open(`https://x.com/intent/tweet?url=${encodeURIComponent(url)}`, '_blank');
                    break;
                case 'whatsapp':
                    window.open(`https://api.whatsapp.com/send?text=${encodeURIComponent(url)}`, '_blank');
                    break;
                case 'linkedin':
                    window.open(`https://www.linkedin.com/shareArticle?mini=true&url=${encodeURIComponent(url)}`, '_blank');
                    break;
                case 'telegram':
                    window.open(`https://t.me/share/url?url=${encodeURIComponent(url)}`, '_blank');
                    break;
                case 'print':
                    window.print();
                    break;
                case 'email':
                    window.location.href = `mailto:?subject=Check this clipped image&body=${encodeURIComponent(url)}`;
                    break;
            }
        }
    });
    </script>
</body>
</html>