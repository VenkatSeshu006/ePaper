<?php
// public/edition.php
require_once '../config.php';
require_once 'includes/header.php';
require_once 'includes/analytics.php';

// Fetch the edition ID from the URL
$edition_id = isset($_GET['id']) ? (int)$_GET['id'] : null;

// Validate the edition ID
if (!$edition_id) {
    header("Location: index.php");
    exit;
}

// Fetch the edition details
$stmt = $pdo->prepare("
    SELECT e.*, c.name AS category_name
    FROM editions e
    LEFT JOIN categories c ON e.category_id = c.id
    WHERE e.id = ?
");
$stmt->execute([$edition_id]);
$edition = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$edition) {
    header("Location: index.php");
    exit;
}

// Track edition view
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
track_analytics('edition_view', $edition_id, $edition['category_id'], $user_id);

// Fetch color schema for secondary header styling
$colors = [];
$stmt = $pdo->query("SELECT key_name, value FROM color_schema");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $colors[$row['key_name']] = $row['value'];
}

// Fetch all editions in the category for datepicker
$stmt = $pdo->prepare("
    SELECT id, title, edition_date
    FROM editions
    WHERE category_id = ?
    ORDER BY edition_date DESC
");
$stmt->execute([$edition['category_id']]);
$all_category_editions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Prepare dates and IDs for JavaScript
$edition_dates = [];
$edition_map = [];
foreach ($all_category_editions as $cat_edition) {
    $date = $cat_edition['edition_date'];
    $edition_dates[] = $date;
    $edition_map[$date] = $cat_edition['id'];
}

// Fetch edition images
$stmt = $pdo->prepare("
    SELECT ei.*
    FROM edition_images ei
    WHERE ei.edition_id = ?
    ORDER BY ei.order ASC
");
$stmt->execute([$edition['id']]);
$images = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($images)) {
    $default_image = '/uploads/placeholder.jpg'; // Fallback if no images
} else {
    $default_image = $images[0]['image_path'];
}

// Fetch the area mapping logo from settings
$stmt = $pdo->prepare("SELECT value FROM settings WHERE key_name = 'area_mapping_logo'");
$stmt->execute();
$logo_result = $stmt->fetch(PDO::FETCH_ASSOC);
$area_mapping_logo = $logo_result ? $logo_result['value'] : '/uploads/settings/default-logo.png';

// Get the base URL
$base_url = "https://" . $_SERVER['HTTP_HOST'];
$current_url = "$base_url/public/edition.php?id=$edition_id";
?>

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<!-- Cropper.js CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css">
<!-- jQuery UI CSS -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css">
<!-- Custom CSS -->
<link rel="stylesheet" href="assets/css/edition.css?v=<?php echo time(); ?>">

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- jQuery UI -->
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<!-- jsPDF Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<!-- Cropper.js JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>

<!-- Pass PHP data to JavaScript -->
<script>
    window.editionData = {
        imagePaths: <?php echo json_encode(array_column($images, 'image_path')); ?>,
        editionId: <?php echo $edition['id']; ?>,
        imageIds: <?php echo json_encode(array_column($images, 'id')); ?>,
        editionTitle: <?php echo json_encode($edition['title']); ?>,
        baseUrl: '<?php echo $base_url; ?>',
        availableDates: <?php echo json_encode($edition_dates); ?>,
        editionMap: <?php echo json_encode($edition_map); ?>,
        totalPages: <?php echo count($images); ?>,
        currentUrl: '<?php echo $current_url; ?>'
    };
</script>
<!-- Load external JS -->
<script src="assets/js/edition.js?v=<?php echo time(); ?>"></script>

<!-- Secondary Header attached to main header -->
<div class="secondary-header">
    <div class="secondary-header-container">
        <div class="edition-info">
            <select class="page-selector" id="page-selector">
                <!-- Options will be populated by JavaScript -->
            </select>
        </div>
        <div class="edition-controls">
            <div class="pagination-controls">
                <!-- Pagination populated by JS -->
            </div>
            <div class="mobile-nav-controls">
                <button class="mobile-nav-btn" id="mobile-prev-btn" title="Previous Page">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button class="mobile-nav-btn" id="mobile-next-btn" title="Next Page">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
            <div class="action-buttons">
                <button class="action-btn pdf-download" title="Download PDF">
                    <i class="fas fa-download"></i>
                    <span>PDF</span>
                </button>
                <button class="action-btn clip-button" title="Clip Page">
                    <i class="fa-solid fa-scissors"></i>
                    <span>Clip</span>
                </button>
                <button class="action-btn" id="archive-button" title="View Archive">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Archive</span>
                </button>
            </div>
        </div>
    </div>
    <div id="datepicker-container"></div>
</div>

<style>
/* Secondary Header Styles */
.secondary-header {
    background: <?= htmlspecialchars($colors['secondary_header_background'] ?? '#2c3e50') ?>;
    border-bottom: 3px solid var(--accent-color, #3498db);
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    position: sticky;
    top: 0;
    z-index: 999;
    margin-top: 0;
}

/* Mobile: Sticky to bottom */
@media (max-width: 768px) {
    .secondary-header {
        position: fixed;
        top: auto;
        bottom: 0;
        left: 0;
        right: 0;
        border-bottom: none;
        border-top: 3px solid var(--accent-color, #3498db);
        box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
    }
    
    /* Add padding to body to prevent content from being hidden behind fixed header */
    body {
        padding-bottom: 80px;
    }
}

.secondary-header-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 15px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
}

.edition-info {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.page-selector {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.3);
    color: <?= htmlspecialchars($colors['secondary_header_text_color'] ?? '#ffffff') ?>;
    padding: 8px 12px;
    border-radius: 6px;
    font-size: 1rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
    min-width: 200px;
}

.page-selector:hover {
    background: rgba(255, 255, 255, 0.2);
    border-color: rgba(255, 255, 255, 0.5);
}

.page-selector:focus {
    outline: none;
    background: rgba(255, 255, 255, 0.2);
    border-color: <?= htmlspecialchars($colors['secondary_header_button_color'] ?? '#3498db') ?>;
    box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.3);
}

.page-selector option {
    background: <?= htmlspecialchars($colors['secondary_header_background'] ?? '#2c3e50') ?>;
    color: <?= htmlspecialchars($colors['secondary_header_text_color'] ?? '#ffffff') ?>;
    padding: 8px;
}

.edition-controls {
    display: flex;
    align-items: center;
    gap: 20px;
    flex-wrap: wrap;
}

.pagination-controls {
    display: flex;
    align-items: center;
    gap: 10px;
}

.secondary-nav-btn {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: <?= htmlspecialchars($colors['secondary_header_text_color'] ?? '#ffffff') ?>;
    padding: 6px 10px;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 0.9rem;
}

.secondary-nav-btn:hover:not(:disabled) {
    background: rgba(255, 255, 255, 0.2);
    border-color: rgba(255, 255, 255, 0.4);
}

.secondary-nav-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.page-numbers {
    display: flex;
    gap: 4px;
}

.page-num-btn {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: <?= htmlspecialchars($colors['secondary_header_text_color'] ?? '#ffffff') ?>;
    padding: 6px 10px;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 0.9rem;
    min-width: 35px;
}

.page-num-btn:hover {
    background: rgba(255, 255, 255, 0.2);
    border-color: rgba(255, 255, 255, 0.4);
}

.page-num-btn.active {
    background: <?= htmlspecialchars($colors['secondary_header_button_color'] ?? '#3498db') ?>;
    border-color: <?= htmlspecialchars($colors['secondary_header_button_color'] ?? '#3498db') ?>;
    font-weight: 600;
}

.page-indicator {
    color: rgba(255, 255, 255, 0.9);
    font-size: 0.9rem;
    margin-left: 10px;
    white-space: nowrap;
}

.action-buttons {
    display: flex;
    gap: 10px;
}

.action-btn {
    background: <?= htmlspecialchars($colors['secondary_header_button_color'] ?? '#3498db') ?>;
    border: 1px solid <?= htmlspecialchars($colors['secondary_header_button_color'] ?? '#3498db') ?>;
    color: <?= htmlspecialchars($colors['secondary_header_text_color'] ?? '#ffffff') ?>;
    padding: 8px 16px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 0.9rem;
    cursor: pointer;
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
}

.action-btn:hover {
    background: rgba(255, 255, 255, 0.2);
    border-color: rgba(255, 255, 255, 0.4);
    transform: translateY(-1px);
}

.action-btn i {
    font-size: 1rem;
}

/* Datepicker Container Positioning */
#datepicker-container {
    position: absolute;
    top: 100%;
    left: 0;
    z-index: 1050;
    background: white;
    border: 1px solid #ddd;
    border-radius: 4px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    display: none;
    margin-top: 5px;
    min-width: 250px;
}

#archive-button {
    position: relative;
}

.action-buttons {
    position: relative;
}

/* Mobile Navigation Buttons */
.mobile-nav-controls {
    display: none;
    align-items: center;
    gap: 8px;
}

.mobile-nav-btn {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: <?= htmlspecialchars($colors['secondary_header_text_color'] ?? '#ffffff') ?>;
    padding: 8px 12px;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 1rem;
    backdrop-filter: blur(10px);
}

.mobile-nav-btn:hover:not(:disabled) {
    background: rgba(255, 255, 255, 0.2);
    border-color: rgba(255, 255, 255, 0.4);
}

.mobile-nav-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

/* Responsive Design */
@media (max-width: 768px) {
    .secondary-header-container {
        flex-direction: row;
        align-items: center;
        gap: 8px;
        padding: 10px 15px;
        flex-wrap: nowrap;
    }
    
    .edition-info {
        flex-shrink: 0;
    }
    
    .page-selector {
        min-width: 100px;
        font-size: 0.85rem;
        padding: 6px 8px;
    }
    
    .edition-controls {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 6px;
        flex-wrap: nowrap;
    }
    
    .pagination-controls {
        display: none;
    }
    
    .mobile-nav-controls {
        display: flex;
        flex-shrink: 0;
    }
    
    .mobile-nav-btn {
        padding: 6px 8px;
        font-size: 0.9rem;
    }
    
    .action-buttons {
        display: flex;
        flex-wrap: nowrap;
        gap: 6px;
        flex-shrink: 0;
    }
    
    .action-btn span {
        display: none;
    }
    
    .action-btn {
        padding: 6px 8px;
        min-width: auto;
        font-size: 0.9rem;
    }
    
    .page-indicator {
        display: none;
    }
}

@media (max-width: 480px) {
    .secondary-header-container {
        padding: 10px 15px;
    }
    
    .edition-title {
        font-size: 1.1rem;
    }
    
    .action-btn {
        padding: 6px 10px;
    }
    
    .secondary-nav-btn {
        padding: 4px 8px;
        font-size: 0.8rem;
    }
}

/* Simple Title Styling */
.simple-title {
    font-size: 1.2rem;
    font-weight: 600;
    color: #333;
    margin: 0 0 15px 0;
    padding: 0;
    border: none;
    background: none;
}

/* Mobile responsive title sizing */
@media (max-width: 768px) {
    .simple-title {
        font-size: 1.1rem !important;
        font-weight: 700 !important;
        line-height: 1.3;
        margin: 0 0 10px 0;
    }
}

@media (max-width: 576px) {
    .simple-title {
        font-size: 1rem !important;
        font-weight: 700 !important;
        line-height: 1.2;
        margin: 0 0 8px 0;
    }
}

/* Remove old header styles */

/* Popup Button Styles */
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

<div class="main-content">
    <div class="boxed-layout">
        <!-- Simple Title -->
        <h1 class="simple-title" id="simple-title" style="font-size: 1.8rem; font-weight: 700;"><?php echo htmlspecialchars($edition['title'] . ' - ' . $edition['category_name']); ?> <?php echo date('j M Y', strtotime($edition['edition_date'])); ?> <span id="page-number">Page 1</span>
        </h1>

        <div class="row">
            <!-- Left Column (Thumbnails) -->
            <div class="d-md-block col-md-2 edition-navigation">
                <div class="navigation-previews">
                    <?php foreach ($images as $index => $image): ?>
                        <a href="#" class="preview-link" data-index="<?php echo $index; ?>">
                            <img src="<?php echo htmlspecialchars($image['image_path']); ?>" alt="Page <?php echo $index + 1; ?>" class="preview-image">
                            <span class="page-number">Page <?php echo $index + 1; ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Right Column (Main Image) -->
            <div class="d-md-block col-md-10 edition-viewer">
            
           <div class="social-share">
                <a href="#" class="social-share-btn" data-platform="facebook"><i class="fab fa-facebook"></i></a>
                <a href="#" class="social-share-btn" data-platform="twitter"><i class="fab fa-twitter"></i></a>
                <a href="#" class="social-share-btn" data-platform="whatsapp"><i class="fab fa-whatsapp"></i></a>
                <a href="#" class="social-share-btn" data-platform="linkedin"><i class="fab fa-linkedin"></i></a>
                <a href="#" class="social-share-btn" data-platform="telegram"><i class="fab fa-telegram"></i></a>
                <a href="#" class="social-share-btn" data-platform="print"><i class="fas fa-print"></i></a>
                <a href="#" class="social-share-btn" data-platform="email"><i class="fas fa-envelope"></i></a>
            </div>
             
            <div class="image-container">
                
                <?php foreach ($images as $index => $image): ?>
                    <img src="<?php echo htmlspecialchars($image['image_path']); ?>" alt="Page <?php echo $index + 1; ?>" class="full-image" <?php if ($index !== 0) echo 'style="display:none;"'; ?>>
                <?php endforeach; ?>
                <button class="image-prev-button"><i class="fas fa-chevron-left"></i></button>
                <button class="image-next-button"><i class="fas fa-chevron-right"></i></button>
            </div>
            <div class="controls">
                <span class="page-counter">Page 1 of <?php echo count($images); ?></span>
                <button class="full-screen"><i class="fas fa-expand"></i></button>
                <button class="prev-button"><i class="fas fa-chevron-left"></i></button>
                <button class="next-button"><i class="fas fa-chevron-right"></i></button>
            </div>
        </div>
        </div>
    </div>
</div>

<!-- Image Popup Modal -->
<div class="modal fade" id="imagePopup" tabindex="-1" aria-labelledby="imagePopupLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <img src="<?php echo htmlspecialchars($area_mapping_logo); ?>" alt="Site Logo" class="popup-logo">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-footer social-share">
                <a href="#" class="social-share-btn" data-platform="facebook" data-url=""><i class="fab fa-facebook"></i></a>
                <a href="#" class="social-share-btn" data-platform="twitter" data-url=""><i class="fab fa-twitter"></i></a>
                <a href="#" class="social-share-btn" data-platform="whatsapp" data-url=""><i class="fab fa-whatsapp"></i></a>
                <a href="#" class="social-share-btn" data-platform="linkedin" data-url=""><i class="fab fa-linkedin"></i></a>
                <a href="#" class="social-share-btn" data-platform="telegram" data-url=""><i class="fab fa-telegram"></i></a>
                <a href="#" class="social-share-btn" data-platform="print" data-url=""><i class="fas fa-print"></i></a>
                <a href="#" class="social-share-btn" data-platform="email" data-url=""><i class="fas fa-envelope"></i></a>
            </div>
            <div class="modal-body">
                <img id="popupImage" src="" alt="Page Image" class="popup-image">
            </div>
            <div class="modal-footer social-share">
                <a href="#" class="social-share-btn" data-platform="facebook" data-url=""><i class="fab fa-facebook"></i></a>
                <a href="#" class="social-share-btn" data-platform="twitter" data-url=""><i class="fab fa-twitter"></i></a>
                <a href="#" class="social-share-btn" data-platform="whatsapp" data-url=""><i class="fab fa-whatsapp"></i></a>
                <a href="#" class="social-share-btn" data-platform="linkedin" data-url=""><i class="fab fa-linkedin"></i></a>
                <a href="#" class="social-share-btn" data-platform="telegram" data-url=""><i class="fab fa-telegram"></i></a>
                <a href="#" class="social-share-btn" data-platform="print" data-url=""><i class="fas fa-print"></i></a>
                <a href="#" class="social-share-btn" data-platform="email" data-url=""><i class="fas fa-envelope"></i></a>
            </div>
        </div>
    </div>
</div>

<!-- Area Image Modal -->
<div class="modal fade" id="areaImageModal" tabindex="-1" aria-labelledby="areaImageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <img src="<?php echo htmlspecialchars($area_mapping_logo); ?>" alt="Site Logo" class="popup-logo">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="areaImage" src="" alt="Area Image" class="popup-image">
                <div class="mt-3">
                    <a id="areaImageDownload" href="" class="btn btn-primary me-2" download><i class="fas fa-download"></i> Download</a>
                    <a id="areaImageOpen" href="" class="btn btn-secondary" target="_blank"><i class="fas fa-external-link-alt"></i> Open</a>
                </div>
            </div>
            <div class="modal-footer social-share">
                <a href="#" class="social-share-btn" data-platform="facebook" data-url=""><i class="fab fa-facebook"></i></a>
                <a href="#" class="social-share-btn" data-platform="twitter" data-url=""><i class="fab fa-twitter"></i></a>
                <a href="#" class="social-share-btn" data-platform="whatsapp" data-url=""><i class="fab fa-whatsapp"></i></a>
                <a href="#" class="social-share-btn" data-platform="email" data-url=""><i class="fas fa-envelope"></i></a>
            </div>
        </div>
    </div>
</div>

<!-- Clip Preview Modal -->
<div class="modal fade" id="clipPreviewModal" tabindex="-1" aria-labelledby="clipPreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="clipPreviewModalLabel">Clipped Image Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <img id="clipPreviewImage" src="" alt="Clipped Image" class="img-fluid mb-3">
                
                <!-- Open and Download Buttons -->
                <div class="popup-actions mb-3 text-center">
                    <a id="clipOpenBtn" href="" class="popup-button me-2" target="_blank"><i class="fas fa-external-link-alt"></i> Open</a>
                    <a id="clipDownloadBtn" href="" class="popup-button" download><i class="fas fa-download"></i> Download</a>
                </div>
                
                <div class="input-group">
                    <input type="text" id="clipPreviewLink" class="form-control" readonly>
                    <button class="btn btn-primary" id="copyClipLinkBtn">Copy Link</button>
                </div>
            </div>
            <div class="modal-footer social-share">
                <a href="#" class="social-share-btn" data-platform="facebook" data-url=""><i class="fab fa-facebook"></i></a>
                <a href="#" class="social-share-btn" data-platform="twitter" data-url=""><i class="fab fa-twitter"></i></a>
                <a href="#" class="social-share-btn" data-platform="whatsapp" data-url=""><i class="fab fa-whatsapp"></i></a>
                <a href="#" class="social-share-btn" data-platform="linkedin" data-url=""><i class="fab fa-linkedin"></i></a>
                <a href="#" class="social-share-btn" data-platform="telegram" data-url=""><i class="fab fa-telegram"></i></a>
                <a href="#" class="social-share-btn" data-platform="print" data-url=""><i class="fas fa-print"></i></a>
                <a href="#" class="social-share-btn" data-platform="email" data-url=""><i class="fas fa-envelope"></i></a>
            </div>
        </div>
    </div>
</div>

<?php
require_once 'includes/footer.php';
?>