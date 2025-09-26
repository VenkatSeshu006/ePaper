<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if this is an AJAX request (form submission)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    // Handle AJAX form submission
    require_once '../../../config.php';
    require_once '../../controllers/EditionController.php';

    header('Content-Type: application/json');

    try {
        $editionController = new EditionController($pdo);
        
        // Validate required fields
        $required_fields = ['title', 'alias', 'category_id', 'edition_date'];
        foreach ($required_fields as $field) {
            if (empty($_POST[$field])) {
                throw new Exception("Missing required field: $field");
            }
        }
        
        // Extract edition data
        $title = trim($_POST['title']);
        $alias = trim($_POST['alias']);
        $description = trim($_POST['description'] ?? '');
        $category_id = (int)$_POST['category_id'];
        $edition_date = $_POST['edition_date'];
        $is_featured = isset($_POST['is_featured']) && $_POST['is_featured'] == '1' ? 1 : 0;
        
        // Start transaction
        $pdo->beginTransaction();
        
        // Create the edition
        if (!$editionController->addEdition($title, $alias, $description, $category_id, $edition_date, $is_featured)) {
            throw new Exception("Failed to create edition");
        }
        
        // Get the newly created edition ID
        $edition_id = $pdo->lastInsertId();
        
        // Create upload directory
        $upload_dir = "/uploads/editions/{$alias}-{$edition_date}/";
        $full_upload_path = $_SERVER['DOCUMENT_ROOT'] . $upload_dir;
        if (!file_exists($full_upload_path)) {
            if (!mkdir($full_upload_path, 0777, true)) {
                throw new Exception("Failed to create upload directory");
            }
        }
        
        // Process uploaded files
        $uploaded_files = [];
        $order = 1;
        
        if (isset($_FILES['files']) && is_array($_FILES['files']['tmp_name'])) {
            for ($i = 0; $i < count($_FILES['files']['tmp_name']); $i++) {
                if ($_FILES['files']['error'][$i] !== UPLOAD_ERR_OK) {
                    continue; // Skip files with errors
                }
                
                $tmp_name = $_FILES['files']['tmp_name'][$i];
                $original_name = $_FILES['files']['name'][$i];
                $file_size = $_FILES['files']['size'][$i];
                $mime_type = $_FILES['files']['type'][$i];
                
                // Validate file type
                $allowed_types = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
                if (!in_array($mime_type, $allowed_types)) {
                    continue; // Skip unsupported file types
                }
                
                // Generate unique filename
                $file_extension = pathinfo($original_name, PATHINFO_EXTENSION);
                $new_filename = "page_" . str_pad($order, 2, '0', STR_PAD_LEFT) . "." . $file_extension;
                $file_path = $full_upload_path . $new_filename;
                $db_path = $upload_dir . $new_filename;
                
                // Move uploaded file
                if (move_uploaded_file($tmp_name, $file_path)) {
                    // Optimize image if needed
                    optimizeImage($file_path, $mime_type);
                    
                    // Add to database
                    if ($editionController->addEditionImage($edition_id, $db_path, $order)) {
                        $uploaded_files[] = [
                            'path' => $db_path,
                            'order' => $order,
                            'size' => $file_size,
                            'original_name' => $original_name
                        ];
                        $order++;
                    } else {
                        // If database insert fails, remove the file
                        if (file_exists($file_path)) {
                            unlink($file_path);
                        }
                    }
                }
            }
        }
        
        // Commit transaction
        $pdo->commit();
        
        // If this is a featured edition, unfeatured others
        if ($is_featured) {
            try {
                $stmt = $pdo->prepare("UPDATE editions SET is_featured = 0 WHERE id != ? AND is_featured = 1");
                $stmt->execute([$edition_id]);
            } catch (Exception $e) {
                // Log but don't fail - this is not critical
                error_log("Failed to unfeatured other editions: " . $e->getMessage());
            }
        }
        
        // Prepare response
        $response = [
            'success' => true,
            'edition_id' => $edition_id,
            'uploaded_files' => $uploaded_files,
            'message' => "Edition '{$title}' created successfully with " . count($uploaded_files) . " pages"
        ];
        
        echo json_encode($response);
        exit;

    } catch (Exception $e) {
        // Rollback transaction on error
        if ($pdo->inTransaction()) {
            $pdo->rollback();
        }
        
        // Clean up any uploaded files on error
        if (isset($edition_id) && isset($full_upload_path) && file_exists($full_upload_path)) {
            $files = glob($full_upload_path . '*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
            rmdir($full_upload_path);
        }
        
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
        exit;
    }
}

/**
 * Optimize uploaded images for web display
 */
function optimizeImage($file_path, $mime_type) {
    $max_width = 2000;
    $max_height = 2000;
    $quality = 85;
    
    try {
        switch ($mime_type) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($file_path);
                break;
            case 'image/png':
                $image = imagecreatefrompng($file_path);
                break;
            case 'image/webp':
                $image = imagecreatefromwebp($file_path);
                break;
            default:
                return; // Unsupported type
        }
        
        if (!$image) {
            return;
        }
        
        $width = imagesx($image);
        $height = imagesy($image);
        
        // Calculate new dimensions if resizing is needed
        if ($width > $max_width || $height > $max_height) {
            $ratio = min($max_width / $width, $max_height / $height);
            $new_width = intval($width * $ratio);
            $new_height = intval($height * $ratio);
            
            // Create new image with new dimensions
            $new_image = imagecreatetruecolor($new_width, $new_height);
            
            // Preserve transparency for PNG
            if ($mime_type === 'image/png') {
                imagealphablending($new_image, false);
                imagesavealpha($new_image, true);
                $transparent = imagecolorallocatealpha($new_image, 255, 255, 255, 127);
                imagefill($new_image, 0, 0, $transparent);
            }
            
            // Resize image
            imagecopyresampled($new_image, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
            
            // Save optimized image
            switch ($mime_type) {
                case 'image/jpeg':
                    imagejpeg($new_image, $file_path, $quality);
                    break;
                case 'image/png':
                    imagepng($new_image, $file_path, intval($quality / 10));
                    break;
                case 'image/webp':
                    imagewebp($new_image, $file_path, $quality);
                    break;
            }
            
            imagedestroy($new_image);
        }
        
        imagedestroy($image);
        
    } catch (Exception $e) {
        // Log error but don't fail the upload
        error_log("Image optimization failed for {$file_path}: " . $e->getMessage());
    }
}

$page_title = "Create New Edition"; // Set the page title
require_once '../../includes/header.php'; // Include the shared header
require_once '../../../config.php'; // Database connection
require_once '../../controllers/EditionController.php';
require_once '../../controllers/CategoryController.php';

$editionController = new EditionController($pdo);
$categoryController = new CategoryController($pdo);
$categories = $categoryController->getAllCategories();

$error = null; // Initialize error variable
$success = null;
?>

<!-- Add PDF.js for PDF processing -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<style>
.upload-zone {
    border: 2px dashed #007bff;
    border-radius: 8px;
    padding: 40px;
    text-align: center;
    background: #f8f9fa;
    margin: 20px 0;
    transition: all 0.3s ease;
    cursor: pointer;
}

.upload-zone:hover {
    border-color: #0056b3;
    background: #e3f2fd;
}

.upload-zone.dragover {
    border-color: #28a745;
    background: #d4edda;
}

.file-preview {
    margin-top: 20px;
}

.preview-item {
    display: flex;
    align-items: center;
    padding: 10px;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    margin-bottom: 10px;
    background: white;
}

.preview-item img {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 4px;
    margin-right: 15px;
}

.preview-info {
    flex: 1;
}

.preview-info h6 {
    margin: 0;
    font-size: 14px;
}

.preview-info p {
    margin: 5px 0 0 0;
    font-size: 12px;
    color: #6c757d;
}

.remove-file {
    background: #dc3545;
    color: white;
    border: none;
    padding: 5px 10px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 12px;
}

.remove-file:hover {
    background: #c82333;
}

.progress-bar-container {
    width: 100%;
    background-color: #f0f0f0;
    border-radius: 4px;
    margin: 10px 0;
    overflow: hidden;
}

.progress-bar {
    height: 8px;
    background-color: #007bff;
    width: 0%;
    transition: width 0.3s ease;
}

.upload-status {
    margin-top: 10px;
    padding: 10px;
    border-radius: 4px;
    display: none;
}

.upload-status.success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.upload-status.error {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.form-section {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}

.section-title {
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 15px;
    color: #495057;
    border-bottom: 2px solid #007bff;
    padding-bottom: 10px;
}

.featured-toggle {
    display: flex;
    align-items: center;
    background: #f8f9fa;
    padding: 15px;
    border-radius: 6px;
    border: 1px solid #dee2e6;
}

.featured-toggle input {
    margin-right: 10px;
    transform: scale(1.2);
}

.featured-toggle label {
    margin: 0;
    font-weight: 500;
    cursor: pointer;
}

.btn-create-edition {
    background: linear-gradient(45deg, #007bff, #0056b3);
    border: none;
    padding: 12px 30px;
    font-size: 16px;
    font-weight: 600;
    border-radius: 6px;
    color: white;
    transition: all 0.3s ease;
}

.btn-create-edition:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,123,255,0.3);
    color: white;
}

.btn-create-edition:disabled {
    background: #6c757d;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
}

.file-counter {
    position: absolute;
    top: 10px;
    right: 15px;
    background: #007bff;
    color: white;
    padding: 5px 10px;
    border-radius: 15px;
    font-size: 12px;
    font-weight: 600;
}
</style>

<div class="content-header">
    <div class="container-fluid">
        <h1><i class="fas fa-plus-circle"></i> Create New Edition</h1>
        <p class="text-muted">Fill in the edition details and upload PDF or images in one step</p>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <?php if (isset($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        <?php endif; ?>

        <?php if (isset($success)): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        <?php endif; ?>

        <form id="editionForm" method="POST" enctype="multipart/form-data">
            <!-- Edition Details Section -->
            <div class="form-section">
                <h3 class="section-title"><i class="fas fa-info-circle"></i> Edition Details</h3>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="title"><i class="fas fa-heading"></i> Title *</label>
                            <input type="text" name="title" id="title" class="form-control" required 
                                   placeholder="Enter edition title" oninput="generateAlias()">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="alias"><i class="fas fa-link"></i> URL Alias</label>
                            <input type="text" name="alias" id="alias" class="form-control" readonly
                                   placeholder="Auto-generated from title">
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="category_id"><i class="fas fa-tags"></i> Category *</label>
                            <select name="category_id" id="category_id" class="form-control" required>
                                <option value="">Select a category</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="edition_date"><i class="fas fa-calendar"></i> Edition Date *</label>
                            <input type="date" name="edition_date" id="edition_date" class="form-control" required
                                   value="<?= date('Y-m-d') ?>">
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="description"><i class="fas fa-align-left"></i> Description</label>
                    <textarea name="description" id="description" class="form-control" rows="3"
                              placeholder="Brief description of this edition"></textarea>
                </div>
                
                <div class="featured-toggle">
                    <input type="checkbox" name="is_featured" id="is_featured">
                    <label for="is_featured">
                        <i class="fas fa-star"></i> Mark as Featured Edition
                        <small class="d-block text-muted">Featured editions appear on the homepage</small>
                    </label>
                </div>
            </div>

            <!-- File Upload Section -->
            <div class="form-section" style="position: relative;">
                <h3 class="section-title"><i class="fas fa-cloud-upload-alt"></i> Upload Content</h3>
                <div class="file-counter" id="fileCounter" style="display: none;">0 files</div>
                
                <div class="upload-zone" id="uploadZone" onclick="document.getElementById('fileInput').click()">
                    <i class="fas fa-cloud-upload-alt fa-3x mb-3 text-primary"></i>
                    <h4>Drop PDF or images here</h4>
                    <p class="text-muted mb-3">or click to browse files</p>
                    <p class="text-muted">
                        <small>Supports: PDF, JPG, PNG, WebP (Max: 50MB per file)</small><br>
                        <small><i class="fas fa-lightbulb text-warning"></i> <strong>Tip:</strong> PDFs will be automatically converted to individual page images</small>
                    </p>
                </div>
                
                <input type="file" id="fileInput" style="display: none;" multiple 
                       accept=".pdf,image/*" onchange="handleFileSelect(event)">
                
                <div id="filePreview" class="file-preview"></div>
                
                <div class="progress-bar-container" id="progressContainer" style="display: none;">
                    <div class="progress-bar" id="progressBar"></div>
                </div>
                
                <div class="upload-status" id="uploadStatus"></div>
            </div>

            <!-- Action Buttons -->
            <div class="form-section">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <a href="index.php" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Editions
                        </a>
                    </div>
                    <div>
                        <button type="button" class="btn btn-outline-primary mr-2" id="previewBtn" disabled>
                            <i class="fas fa-eye"></i> Preview
                        </button>
                        <button type="submit" class="btn btn-create-edition" id="submitBtn" disabled>
                            <i class="fas fa-rocket"></i> Create Edition & Upload
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

<script>
let selectedFiles = [];
let processedFiles = [];

// Drag and drop functionality
const uploadZone = document.getElementById('uploadZone');
const fileInput = document.getElementById('fileInput');
const filePreview = document.getElementById('filePreview');
const fileCounter = document.getElementById('fileCounter');
const submitBtn = document.getElementById('submitBtn');
const previewBtn = document.getElementById('previewBtn');

uploadZone.addEventListener('dragover', (e) => {
    e.preventDefault();
    uploadZone.classList.add('dragover');
});

uploadZone.addEventListener('dragleave', (e) => {
    e.preventDefault();
    uploadZone.classList.remove('dragover');
});

uploadZone.addEventListener('drop', (e) => {
    e.preventDefault();
    uploadZone.classList.remove('dragover');
    const files = Array.from(e.dataTransfer.files);
    handleFiles(files);
});

function handleFileSelect(event) {
    const files = Array.from(event.target.files);
    handleFiles(files);
}

function handleFiles(files) {
    files.forEach(file => {
        if (validateFile(file)) {
            selectedFiles.push(file);
            if (file.type === 'application/pdf') {
                processPDF(file);
            } else {
                processImage(file);
            }
        }
    });
    updateUI();
}

function validateFile(file) {
    const validTypes = ['application/pdf', 'image/jpeg', 'image/png', 'image/webp'];
    const maxSize = 50 * 1024 * 1024; // 50MB
    
    if (!validTypes.includes(file.type)) {
        showStatus('error', `Invalid file type: ${file.name}`);
        return false;
    }
    
    if (file.size > maxSize) {
        showStatus('error', `File too large: ${file.name} (Max: 50MB)`);
        return false;
    }
    
    return true;
}

function processPDF(file) {
    const reader = new FileReader();
    reader.onload = function(e) {
        const typedarray = new Uint8Array(e.target.result);
        
        pdfjsLib.getDocument(typedarray).promise.then(pdf => {
            const numPages = pdf.numPages;
            let pagesProcessed = 0;
            
            for (let i = 1; i <= numPages; i++) {
                pdf.getPage(i).then(page => {
                    const scale = 2.0;
                    const viewport = page.getViewport({ scale });
                    
                    const canvas = document.createElement('canvas');
                    const ctx = canvas.getContext('2d');
                    canvas.width = viewport.width;
                    canvas.height = viewport.height;
                    
                    page.render({
                        canvasContext: ctx,
                        viewport: viewport
                    }).promise.then(() => {
                        canvas.toBlob(blob => {
                            const pageFile = new File([blob], `${file.name}_page_${i}.jpg`, { type: 'image/jpeg' });
                            processedFiles.push({
                                file: pageFile,
                                preview: canvas.toDataURL(),
                                name: `${file.name} - Page ${i}`,
                                size: blob.size,
                                type: 'pdf-page'
                            });
                            
                            pagesProcessed++;
                            if (pagesProcessed === numPages) {
                                updatePreview();
                                updateUI();
                            }
                        }, 'image/jpeg', 0.8);
                    });
                });
            }
        });
    };
    reader.readAsArrayBuffer(file);
}

function processImage(file) {
    const reader = new FileReader();
    reader.onload = function(e) {
        processedFiles.push({
            file: file,
            preview: e.target.result,
            name: file.name,
            size: file.size,
            type: 'image'
        });
        updatePreview();
        updateUI();
    };
    reader.readAsDataURL(file);
}

function updatePreview() {
    filePreview.innerHTML = '';
    
    processedFiles.forEach((item, index) => {
        const previewItem = document.createElement('div');
        previewItem.className = 'preview-item';
        previewItem.innerHTML = `
            <img src="${item.preview}" alt="Preview">
            <div class="preview-info">
                <h6>${item.name}</h6>
                <p>Size: ${formatFileSize(item.size)} | Type: ${item.type === 'pdf-page' ? 'PDF Page' : 'Image'}</p>
            </div>
            <button type="button" class="remove-file" onclick="removeFile(${index})">
                <i class="fas fa-trash"></i>
            </button>
        `;
        filePreview.appendChild(previewItem);
    });
}

function removeFile(index) {
    processedFiles.splice(index, 1);
    updatePreview();
    updateUI();
}

function updateUI() {
    const totalFiles = processedFiles.length;
    
    if (totalFiles > 0) {
        fileCounter.style.display = 'block';
        fileCounter.textContent = `${totalFiles} files`;
        previewBtn.disabled = false;
    } else {
        fileCounter.style.display = 'none';
        previewBtn.disabled = true;
    }
    
    // Enable submit button if form is valid and files are selected
    const formValid = document.getElementById('title').value && 
                     document.getElementById('category_id').value && 
                     document.getElementById('edition_date').value;
    
    submitBtn.disabled = !(formValid && totalFiles > 0);
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

function showStatus(type, message) {
    const status = document.getElementById('uploadStatus');
    status.className = `upload-status ${type}`;
    status.textContent = message;
    status.style.display = 'block';
    
    if (type === 'success') {
        setTimeout(() => {
            status.style.display = 'none';
        }, 3000);
    }
}

function updateProgress(percent) {
    const progressContainer = document.getElementById('progressContainer');
    const progressBar = document.getElementById('progressBar');
    
    if (percent > 0) {
        progressContainer.style.display = 'block';
        progressBar.style.width = percent + '%';
    } else {
        progressContainer.style.display = 'none';
    }
}

// Form validation
document.getElementById('title').addEventListener('input', updateUI);
document.getElementById('category_id').addEventListener('change', updateUI);
document.getElementById('edition_date').addEventListener('change', updateUI);

function generateAlias() {
    const titleInput = document.getElementById('title');
    const aliasInput = document.getElementById('alias');
    let alias = titleInput.value
        .toLowerCase()
        .replace(/[^a-z0-9\s-]/g, '')
        .replace(/\s+/g, '-')
        .trim();
    aliasInput.value = alias;
    updateUI();
}

// Form submission
document.getElementById('editionForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    if (processedFiles.length === 0) {
        showStatus('error', 'Please select at least one file to upload');
        return;
    }
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating Edition...';
    
    try {
        // Create FormData with edition details and files
        const formData = new FormData();
        
        // Edition details
        formData.append('title', document.getElementById('title').value);
        formData.append('alias', document.getElementById('alias').value);
        formData.append('description', document.getElementById('description').value);
        formData.append('category_id', document.getElementById('category_id').value);
        formData.append('edition_date', document.getElementById('edition_date').value);
        formData.append('is_featured', document.getElementById('is_featured').checked ? 1 : 0);
        
        // Files
        processedFiles.forEach((item, index) => {
            formData.append(`files[]`, item.file);
        });
        
        // Submit to handler
        const response = await fetch('add.php', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showStatus('success', `Edition created successfully! ${result.uploaded_files.length} files uploaded.`);
            
            // Show options to user
            const message = `
                <div class="alert alert-success">
                    <h5><i class="fas fa-check-circle"></i> Edition Created Successfully!</h5>
                    <p>Edition "${document.getElementById('title').value}" has been created with ${result.uploaded_files.length} pages.</p>
                    <div class="mt-3">
                        <a href="index.php" class="btn btn-primary">
                            <i class="fas fa-list"></i> Back to Editions List
                        </a>
                        <a href="pages_upload/upload.php?id=${result.edition_id}" class="btn btn-outline-primary">
                            <i class="fas fa-plus"></i> Add More Pages
                        </a>
                        <a href="../../../public/edition.php?id=${result.edition_id}" class="btn btn-outline-success" target="_blank">
                            <i class="fas fa-eye"></i> Preview Edition
                        </a>
                    </div>
                </div>
            `;
            
            document.querySelector('section.content .container-fluid').innerHTML = message;
        } else {
            throw new Error(result.error || 'Failed to create edition');
        }
    } catch (error) {
        showStatus('error', error.message);
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-rocket"></i> Create Edition & Upload';
    }
});

// Preview functionality
previewBtn.addEventListener('click', function() {
    if (processedFiles.length === 0) return;
    
    // Simple preview - show first image in a modal or new window
    const firstFile = processedFiles[0];
    const previewWindow = window.open('', 'preview', 'width=800,height=600');
    previewWindow.document.write(`
        <html>
        <head><title>Preview</title></head>
        <body style="margin: 0; display: flex; justify-content: center; align-items: center; min-height: 100vh; background: #f0f0f0;">
            <img src="${firstFile.preview}" style="max-width: 100%; max-height: 100%; object-fit: contain;">
        </body>
        </html>
    `);
});
</script>

<?php require_once '../../includes/footer.php'; ?>