<?php
// public/popup_template.php
require_once '../config.php';

$clip_id = isset($_GET['clip_id']) ? (int)$_GET['clip_id'] : null;
if (!$clip_id) {
    die('No clip specified.');
}

// Fetch clip details from database - using correct table name
$stmt = $pdo->prepare("SELECT clip_path FROM clipped_images WHERE id = ?");
$stmt->execute([$clip_id]);
$clip = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$clip) {
    die('Clip not found in database.');
}

// Check if file exists (handle both relative and absolute paths)
$file_path = $clip['clip_path'];
if (substr($file_path, 0, 1) !== '/') {
    $file_path = '/' . $file_path;
}

// Handle ePaper subdirectory - check if path includes /ePaper/ or not
if (strpos($file_path, '/ePaper/') === false) {
    // Old format path, construct correct file system path
    $full_file_path = __DIR__ . '/../' . ltrim(str_replace('/ePaper/', '', $file_path), '/');
} else {
    // New format path, use document root
    $full_file_path = $_SERVER['DOCUMENT_ROOT'] . $file_path;
}

if (!file_exists($full_file_path)) {
    // Try alternative path construction
    $alt_file_path = __DIR__ . '/../uploads/clips/' . basename($file_path);
    if (!file_exists($alt_file_path)) {
        die('Clip file not found on server. Tried: ' . htmlspecialchars($file_path) . ' and ' . htmlspecialchars($alt_file_path));
    }
}

$clip_path = $clip['clip_path'];

// Ensure clip_path starts with forward slash for web access
if (substr($clip_path, 0, 1) !== '/') {
    $clip_path = '/' . $clip_path;
}

// Construct the full URL for sharing
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$base_url = $protocol . $_SERVER['HTTP_HOST'];
$popup_url = "$base_url/ePaper/public/popup_template.php?clip_id=" . urlencode($clip_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clip Popup</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="assets/css/edition.css?v=<?php echo time(); ?>">
    <style>
        .mini-popup {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 400px;
            height: 500px;
            background: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
            border-radius: 4px;
            overflow: hidden;
        }
        .clip-image {
            width: 100%;
            height: 300px;
            object-fit: contain;
            display: block;
            margin: 0 auto;
        }
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
        }
        .social-share-btn {
            flex-grow: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 40px;
            border: none;
            border-radius: 0;
            font-size: 16px;
            text-decoration: none;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s;
        }
        .social-share-btn i {
            color: white;
        }
        .social-share-btn[data-platform="facebook"] { background-color: #1877F2; }
        .social-share-btn[data-platform="twitter"] { background-color: #000000; }
        .social-share-btn[data-platform="whatsapp"] { background-color: #25D366; }
        .social-share-btn[data-platform="email"] { background-color: #0072C6; }
        .social-share-btn:hover {
            transform: scale(1.1);
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <div class="mini-popup">
        <img src="<?php echo htmlspecialchars($clip_path); ?>" alt="Cropped Clip" class="clip-image"
             onerror="console.error('Failed to load clip image:', this.src); this.style.display='none'; this.nextElementSibling.style.display='block';">
        <div style="display: none; padding: 20px; text-align: center; background: #f8f9fa;">
            <p><strong>Image not found:</strong><br><?php echo htmlspecialchars($clip_path); ?></p>
        </div>
        <div class="popup-actions">
            <a href="<?php echo htmlspecialchars($popup_url); ?>" class="popup-button" target="_blank"><i class="fas fa-external-link-alt"></i> Open</a>
            <a href="<?php echo htmlspecialchars($clip_path); ?>" class="popup-button" download><i class="fas fa-download"></i> Download</a>
            <div class="social-share">
                <a href="#" class="social-share-btn" data-platform="facebook" data-url="<?php echo $popup_url; ?>"><i class="fab fa-facebook"></i></a>
                <a href="#" class="social-share-btn" data-platform="twitter" data-url="<?php echo $popup_url; ?>"><i class="fab fa-twitter"></i></a>
                <a href="#" class="social-share-btn" data-platform="whatsapp" data-url="<?php echo $popup_url; ?>"><i class="fab fa-whatsapp"></i></a>
                <a href="#" class="social-share-btn" data-platform="email" data-url="<?php echo $popup_url; ?>"><i class="fas fa-envelope"></i></a>
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
                    window.location.href = `mailto:?subject=Check this clip&body=${encodeURIComponent(url)}`;
                    break;
            }
        }
    });
    </script>
</body>
</html>