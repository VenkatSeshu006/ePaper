<?php
// public/area_popup_template.php
require_once '../config.php';

$image_url = isset($_GET['image_url']) ? $_GET['image_url'] : null;
if (!$image_url) {
    die('No image specified.');
}

// Ensure the image_url starts with a forward slash
if (substr($image_url, 0, 1) !== '/') {
    $image_url = '/' . $image_url;
}

// Validate that the image file exists
$image_path = $_SERVER['DOCUMENT_ROOT'] . $image_url;
if (!file_exists($image_path)) {
    die('Image not found.');
}

// Construct the relative URL for sharing (domain-independent)
$popup_url = "/public/area_popup_template.php?image_url=" . urlencode($image_url);
$full_url = $image_url;

// Fetch the area mapping logo from settings
$stmt = $pdo->prepare("SELECT value FROM settings WHERE key_name = 'area_mapping_logo'");
$stmt->execute();
$logo_result = $stmt->fetch(PDO::FETCH_ASSOC);
$area_mapping_logo = $logo_result ? $logo_result['value'] : '/uploads/settings/default-logo.png';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Area Image Popup</title>
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
        .social-share {
            display: flex;
            gap: 0;
            margin-top: 15px;
            justify-content: center;
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
        <img src="<?php echo htmlspecialchars($full_url); ?>" alt="Area Image" class="clip-image">
        <div class="popup-actions">
            <a href="<?php echo htmlspecialchars($popup_url); ?>" class="popup-button" target="_blank"><i class="fas fa-external-link-alt"></i> Open</a>
            <a href="<?php echo htmlspecialchars($full_url); ?>" class="popup-button" download><i class="fas fa-download"></i> Download</a>
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
                    window.location.href = `mailto:?subject=Check this area image&body=${encodeURIComponent(url)}`;
                    break;
            }
        }

        // Close popup with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                window.close();
            }
        });
    });
    </script>
</body>
</html>