<?php
$page_title = "Upload Pages";
require_once '../../../includes/header.php';
require_once '../../../../config.php';
require_once '../../../controllers/EditionController.php';

$editionController = new EditionController($pdo);

$edition_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$edition = $editionController->getEditionById($edition_id);

if (!$edition) {
    echo "<div class='alert alert-danger'>Edition not found.</div>";
    require_once '../../../includes/footer.php';
    exit;
}

$upload_dir = "/uploads/editions/{$edition['alias']}-{$edition['edition_date']}/";
$full_upload_path = $_SERVER['DOCUMENT_ROOT'] . $upload_dir;
if (!file_exists($full_upload_path)) {
    mkdir($full_upload_path, 0777, true);
}

$images = $editionController->getEditionImages($edition_id);
$max_order = !empty($images) ? max(array_column($images, 'order')) : 0;
?>

<!-- Add Lightbox2 CSS and JS in the head, ensuring jQuery loads first -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/css/lightbox.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/js/lightbox.min.js"></script>

<div class="content-header">
    <div class="container-fluid">
        <h1>Upload Pages for <?= htmlspecialchars($edition['title']) ?></h1>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Upload New Pages</h3>
            </div>
            <div class="card-body">
                <form id="uploadForm" enctype="multipart/form-data">
                    <div class="form-group">
                        <input type="file" class="form-control" id="fileInput" multiple accept="image/*,.pdf">
                    </div>
                    <button type="submit" class="btn btn-primary" id="uploadBtn">Upload</button>
                    <div id="preview" class="mt-3"></div>
                </form>
                <div id="uploadStatus" class="mt-3"></div>
                <!-- Progress Bar for PDF Conversion -->
                <div id="conversionProgress" class="mt-3" style="display: none;">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Converting PDF to Images</h5>
                            <div class="progress mb-2">
                                <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
                            </div>
                            <div id="progressText" class="text-muted">Preparing conversion...</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-default">
            <div class="card-header">
                <h3 class="card-title">Existing Pages</h3>
                <div class="card-tools">
                    <button class="btn btn-danger" id="deleteSelectedBtn" disabled><i class="fas fa-trash"></i> Delete Selected</button>
                </div>
            </div>
            <div class="card-body">
                <ul id="pageList" class="list-group" style="min-height: 100px;">
                    <?php foreach ($images as $image): ?>
                        <li class="list-group-item d-flex align-items-center" data-id="<?= $image['id'] ?>">
                            <input type="checkbox" class="mr-2 page-checkbox">
                            <a href="<?= htmlspecialchars($image['image_path']) ?>" data-lightbox="edition-<?= $edition_id ?>" data-title="Page <?= $image['order'] ?>">
                                <img src="<?= htmlspecialchars($image['image_path'] . '?v=' . time()) ?>" alt="Page <?= $image['order'] ?>" style="width: 100px; height: auto; margin-right: 10px;">
                            </a>
                            <div class="ml-auto d-flex align-items-center">

                                <label class="btn btn-sm btn-warning mr-2">
                                    <i class="fas fa-exchange-alt"></i> Replace
                                    <input type="file" class="replace-input" data-id="<?= $image['id'] ?>" accept="image/*" hidden>
                                </label>
                                <button class="btn btn-sm btn-info mr-2 area-mapping-btn" data-id="<?= $image['id'] ?>"><i class="fas fa-map"></i> Area Mapping</button>
                                <button class="btn btn-sm btn-danger delete-btn" data-id="<?= $image['id'] ?>"><i class="fas fa-trash"></i> Delete</button>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</section>

<style>
    #pageList .list-group-item:hover {
        cursor: move;
    }

    /* Ensure consistent button heights and alignment */
    #pageList .list-group-item .d-flex {
        align-items: center;
        justify-content: flex-end;
    }

    #pageList .list-group-item button,
    #pageList .list-group-item label.btn {
        height: 38px;
        padding: 0 12px;
        line-height: 1.5;
        display: inline-flex;
        align-items: center;
    }

    #pageList .list-group-item .replace-input {
        display: none;
    }

    /* Adjust icon and text spacing for uniformity */
    #pageList .list-group-item .btn i {
        margin-right: 5px;
    }

    /* Ensure label behaves like a button for alignment */
    #pageList .list-group-item label.btn {
        cursor: pointer;
        margin: 0;
    }
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
<script>
    // Ensure Lightbox2 is initialized after jQuery and other scripts
    document.addEventListener('DOMContentLoaded', function() {
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.worker.min.js';

        // Upload handling
        // Upload handling (updated processFile function)
document.getElementById('uploadForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const files = document.getElementById('fileInput').files;
    if (!files.length) return;

    const status = document.getElementById('uploadStatus');
    const progressContainer = document.getElementById('conversionProgress');
    const progressBar = document.getElementById('progressBar');
    const progressText = document.getElementById('progressText');
    
    status.innerHTML = '<div class="alert alert-info">Processing...</div>';
    const formData = new FormData();
    let order = <?= $max_order ?>;
    let totalPages = 0;
    let processedPages = 0;
    
    // First pass: count total pages in all PDFs
    for (const file of files) {
        if (file.type === 'application/pdf') {
            const pdf = await pdfjsLib.getDocument(URL.createObjectURL(file)).promise;
            totalPages += pdf.numPages;
        } else {
            totalPages += 1; // Regular images count as 1 page
        }
    }
    
    // Show progress bar if there are PDFs to convert
    const hasPDFs = Array.from(files).some(file => file.type === 'application/pdf');
    if (hasPDFs) {
        progressContainer.style.display = 'block';
        updateProgress(0, totalPages, 'Starting conversion...');
    }
    
    function updateProgress(current, total, message) {
        const percentage = Math.round((current / total) * 100);
        progressBar.style.width = percentage + '%';
        progressBar.setAttribute('aria-valuenow', percentage);
        progressBar.textContent = percentage + '%';
        progressText.textContent = message;
    }

    const processFile = async (file) => {
        return new Promise((resolve) => {
            const img = new Image();
            const reader = new FileReader();

            reader.onload = (e) => {
                img.src = e.target.result;

                img.onload = () => {
                    const canvas = document.createElement('canvas');
                    const ctx = canvas.getContext('2d');

                    // Set canvas size to match image dimensions
                    canvas.width = img.width;
                    canvas.height = img.height;

                    // Draw image on canvas (converts to JPEG)
                    ctx.drawImage(img, 0, 0);
                    const dataURL = canvas.toDataURL('image/jpeg'); // Force JPEG output
                    formData.append('files[]', dataURL);
                    order++;
                    
                    // Update progress for regular images
                    if (hasPDFs) {
                        processedPages++;
                        updateProgress(processedPages, totalPages, `Processing image ${processedPages} of ${totalPages}`);
                    }
                    
                    resolve();
                };
            };

            if (file.type === 'application/pdf') {
                pdfjsLib.getDocument(URL.createObjectURL(file)).promise.then(async (pdf) => {
                    updateProgress(processedPages, totalPages, `Converting PDF: ${file.name} (${pdf.numPages} pages)`);
                    
                    for (let i = 1; i <= pdf.numPages; i++) {
                        const page = await pdf.getPage(i);
                        const viewport = page.getViewport({ scale: 1.5 });
                        const canvas = document.createElement('canvas');
                        canvas.width = viewport.width;
                        canvas.height = viewport.height;
                        await page.render({ canvasContext: canvas.getContext('2d'), viewport }).promise;
                        formData.append('files[]', canvas.toDataURL('image/jpeg'));
                        order++;
                        
                        // Update progress for each PDF page
                        processedPages++;
                        updateProgress(processedPages, totalPages, `Converting page ${i} of ${pdf.numPages} from ${file.name}`);
                    }
                    resolve();
                });
            } else {
                reader.readAsDataURL(file); // Handle images
            }
        });
    };

    const promises = Array.from(files).map(file => processFile(file));
    await Promise.all(promises);

    // Update progress to show upload phase
    if (hasPDFs) {
        updateProgress(totalPages, totalPages, 'Conversion complete! Uploading files...');
    }

    fetch('upload_handler.php?id=<?= $edition_id ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        // Hide progress bar
        progressContainer.style.display = 'none';
        
        status.innerHTML = data.success ? 
            '<div class="alert alert-success">Pages uploaded successfully!</div>' : 
            '<div class="alert alert-danger">' + data.error + '</div>';
        if (data.success) location.reload();
    })
    .catch(() => {
        // Hide progress bar on error
        progressContainer.style.display = 'none';
        status.innerHTML = '<div class="alert alert-danger">Upload failed.</div>';
    });
});
        // SortableJS for reordering with instant page number update
        Sortable.create(document.getElementById('pageList'), {
            animation: 150,
            onEnd: (evt) => {
                const items = Array.from(evt.target.children);
                // No need to update page numbers since they're removed
            }
        });

        // Multi-select delete
        const deleteSelectedBtn = document.getElementById('deleteSelectedBtn');
        document.querySelectorAll('.page-checkbox').forEach(cb => {
            cb.addEventListener('change', () => {
                const checked = document.querySelectorAll('.page-checkbox:checked').length;
                deleteSelectedBtn.disabled = !checked;
            });
        });
        deleteSelectedBtn.addEventListener('click', () => {
            const checked = document.querySelectorAll('.page-checkbox:checked');
            if (!checked.length) return;
            if (confirm('Are you sure you want to delete the selected pages?')) {
                const ids = Array.from(checked).map(cb => cb.parentElement.dataset.id);
                fetch('delete_handler.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ ids })
                }).then(() => location.reload());
            }
        });

        // Single delete
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                if (confirm('Are you sure you want to delete this page?')) {
                    fetch('delete_handler.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ ids: [btn.dataset.id] })
                    }).then(() => location.reload());
                }
            });
        });

        // Replace image
        document.querySelectorAll('.replace-input').forEach(input => {
            input.addEventListener('change', () => {
                const file = input.files[0];
                if (!file) return;
                const formData = new FormData();
                formData.append('file', file);
                formData.append('id', input.dataset.id);
                fetch('replace_handler.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update the image src with the new path and cache-busting parameter
                        const listItem = input.closest('li.list-group-item');
                        const img = listItem.querySelector('img');
                        const link = listItem.querySelector('a');
                        img.src = data.new_path; // Use the new path with ?v=timestamp
                        link.href = data.new_path; // Update the Lightbox link as well
                        alert('Image replaced successfully!');
                    } else {
                        alert('Replace failed: ' + data.error);
                    }
                })
                .catch(error => alert('Error replacing image: ' + error.message));
            });
        });

        // Area mapping button
        document.querySelectorAll('.area-mapping-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const editionId = <?= $edition_id ?>;
                const pageId = btn.dataset.id;
                window.location.href = `area/area_mapping.php?edition_id=${editionId}&page_id=${pageId}`;
            });
        });

    });
</script>



<style>
.area-mapping-controls {
    padding: 20px 0;
}

.button-group {
    display: flex;
    gap: 15px;
    margin-bottom: 20px;
}

.button-group .btn {
    flex: 1;
    padding: 12px 20px;
    font-weight: 500;
    border-radius: 6px;
    transition: all 0.3s ease;
}

.button-group .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.button-group .btn i {
    margin-right: 8px;
}
</style>

<?php require_once '../../../includes/footer.php'; ?>