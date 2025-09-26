<?php
$page_title = "Area Mapping";
require_once '../../../../includes/header.php';
require_once '../../../../../config.php';
require_once '../../../../controllers/EditionController.php';

$editionController = new EditionController($pdo);

$edition_id = isset($_GET['edition_id']) ? (int)$_GET['edition_id'] : 0;
$page_id = isset($_GET['page_id']) ? (int)$_GET['page_id'] : 0;

$edition = $editionController->getEditionById($edition_id);

if (!$edition) {
    echo "<div class='alert alert-danger'>Edition not found.</div>";
    require_once '../../../../includes/footer.php';
    exit;
}

// Get page information if page_id is provided
$page_info = null;
if ($page_id) {
    $page_info = $editionController->getImageById($page_id);
    if (!$page_info) {
        echo "<div class='alert alert-danger'>Page not found.</div>";
        require_once '../../../../includes/footer.php';
        exit;
    }
}
?>



<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <?php if ($page_info): ?>
                    <div style="display: flex; gap: 20px; align-items: flex-start;">
                        <div id="image-container" style="position: relative; display: inline-block; max-width: 70%; overflow: auto;">
                            <img id="page-image" src="<?= htmlspecialchars($page_info['image_path']) ?>" alt="Page <?= $page_info['order'] ?>" style="max-width: 100%; height: auto;">
                        </div>
                        <div style="display: flex; flex-direction: column; gap: 10px; min-width: 150px; position: sticky; top: 20px;">
                            <button type="button" class="btn btn-primary" id="add-area-btn">Add Area Map</button>
                            <button type="button" class="btn btn-success" id="save-all-btn">Save All</button>
                            <button type="button" class="btn btn-warning" id="clear-all-btn">Clear All</button>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        No page selected. Please go back and select a page to map areas.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>



<!-- Load Interact.js from CDN -->
<script src="https://cdn.jsdelivr.net/npm/interactjs/dist/interact.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css">

<style>
    .image-container {
        position: relative;
        display: inline-block;
        max-width: 100%;
        overflow: visible;
        user-select: none;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
    }
    .image-container img {
        user-select: none;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        pointer-events: auto;
        cursor: crosshair;
    }
    .area-box {
        box-sizing: border-box;
    }
    .area-box.editing {
        border: 2px solid #007bff;
        background: rgba(0, 123, 255, 0.1);
    }
    .area-box.saved {
        border: 2px solid #28a745;
        background: rgba(40, 167, 69, 0.1);
    }
    .area-box:hover .resize-handle-nw,
    .area-box:hover .resize-handle-n,
    .area-box:hover .resize-handle-e,
    .area-box:hover .resize-handle-se,
    .area-box:hover .resize-handle-s,
    .area-box:hover .resize-handle-sw,
    .area-box:hover .resize-handle-w {
        opacity: 1;
    }
    .resize-handle-nw,
    .resize-handle-n,
    .resize-handle-e,
    .resize-handle-se,
    .resize-handle-s,
    .resize-handle-sw,
    .resize-handle-w {
        opacity: 0.7;
        box-shadow: 0 1px 3px rgba(0,0,0,0.3);
    }
    .resize-handle-nw:hover,
    .resize-handle-n:hover,
    .resize-handle-e:hover,
    .resize-handle-se:hover,
    .resize-handle-s:hover,
    .resize-handle-sw:hover,
    .resize-handle-w:hover {
        background: #666;
        opacity: 1;
    }
</style>

<script>
let areaBoxes = [];
let boxCounter = 0;

// Crop and Save Area
function cropAndSaveArea(areaBox, areaNumber, pageId, callback) {
    const image = document.getElementById('page-image');
    const imageRect = image.getBoundingClientRect();
    const boxRect = areaBox.element.getBoundingClientRect();
    
    // Calculate crop coordinates relative to the actual image
    const scaleX = image.naturalWidth / image.offsetWidth;
    const scaleY = image.naturalHeight / image.offsetHeight;
    
    const cropX = (boxRect.left - imageRect.left) * scaleX;
    const cropY = (boxRect.top - imageRect.top) * scaleY;
    const cropWidth = boxRect.width * scaleX;
    const cropHeight = boxRect.height * scaleY;
    
    // Create canvas for cropping
    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d');
    
    canvas.width = cropWidth;
    canvas.height = cropHeight;
    
    // Draw the cropped portion
    ctx.drawImage(
        image,
        cropX, cropY, cropWidth, cropHeight,
        0, 0, cropWidth, cropHeight
    );
    
    // Convert to blob and upload
    canvas.toBlob(function(blob) {
        const formData = new FormData();
        const filename = `area_${areaNumber}_${pageId}_${Date.now()}.jpg`;
        formData.append('cropped_image', blob, filename);
        formData.append('edition_image_id', pageId);
        formData.append('area_number', areaNumber);
        
        fetch('save_area_image.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                callback(data.image_path);
            } else {
                console.error('Error saving area image:', data.message);
                callback(null);
            }
        })
        .catch(error => {
            console.error('Error uploading area image:', error);
            callback(null);
        });
    }, 'image/jpeg', 0.9);
}

// Save areas to database
function saveAreasToDatabase(pageId, areas, saveBtn, originalText) {
    fetch('save_areas.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            edition_image_id: pageId,
            areas: areas
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Change all boxes to saved state (green)
            areaBoxes.forEach(box => {
                if (box.element) {
                    box.element.classList.remove('editing');
                    box.element.classList.add('saved');
                }
            });
            alert(`Successfully saved ${data.count} areas with images`);
        } else {
            alert('Error saving areas: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error saving areas');
    })
    .finally(() => {
        saveBtn.textContent = originalText;
        saveBtn.disabled = false;
    });
}

// Function to load saved areas
function loadAreas() {
    const urlParams = new URLSearchParams(window.location.search);
    const pageId = urlParams.get('page_id');
    
    if (!pageId) {
        return; // No page selected
    }
    
    fetch(`load_areas.php?edition_image_id=${pageId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.areas.length > 0) {
                const container = document.getElementById('image-container');
                const image = document.getElementById('page-image');
                
                if (!image) {
                    return;
                }
                
                // Clear existing areas
                document.querySelectorAll('.area-box').forEach(box => box.remove());
                areaBoxes = [];
                boxCounter = 0;
                
                // Load saved areas
                data.areas.forEach((area, index) => {
                    const areaBox = addAreaBox(
                        (area.x / 100) * image.offsetWidth,
                        (area.y / 100) * image.offsetHeight,
                        (area.width / 100) * image.offsetWidth,
                        (area.height / 100) * image.offsetHeight,
                        area.label || `Area ${index + 1}`,
                        true // Mark as saved (green)
                    );
                    
                    // Store the database ID for deletion purposes
                    if (areaBox) {
                        const areaData = areaBoxes.find(b => b.element === areaBox);
                        if (areaData) {
                            areaData.area_id = area.id;
                            areaData.image_url = area.image_url;
                        }
                    }
                });
            }
        })
        .catch(error => {
            console.error('Error loading areas:', error);
        });
}

// Add Area Map button functionality - adds a rectangle box in the visible viewport
document.getElementById('add-area-btn').addEventListener('click', function() {
    const image = document.getElementById('page-image');
    const container = document.getElementById('image-container');
    
    if (!image || !container) return;
    
    // Get the visible viewport area
    const imageRect = image.getBoundingClientRect();
    const containerRect = container.getBoundingClientRect();
    const viewportHeight = window.innerHeight;
    
    // Calculate the visible portion of the image
    const imageTop = imageRect.top;
    const imageBottom = imageRect.bottom;
    const imageHeight = image.offsetHeight;
    
    // Find the center of the visible portion
    let visibleTop = Math.max(0, -imageTop);
    let visibleBottom = Math.min(imageHeight, viewportHeight - imageTop);
    
    // If the entire image is visible, use the center
    if (imageTop >= 0 && imageBottom <= viewportHeight) {
        visibleTop = 0;
        visibleBottom = imageHeight;
    }
    
    // Calculate the center Y position of the visible area
    const visibleCenterY = (visibleTop + visibleBottom) / 2;
    
    // Add a box at the center of the visible area
    addAreaBox(image.offsetWidth / 2 - 100, visibleCenterY - 50);
});

// Function to create area boxes
function addAreaBox(x, y, width, height, label, isSaved = false) {
    const container = document.getElementById('image-container');
    const image = document.getElementById('page-image');
    
    if (!image) {
        alert('Please select a page first');
        return null;
    }
    
    boxCounter++;
    
    // Get image dimensions and position
    const imageRect = image.getBoundingClientRect();
    const containerRect = container.getBoundingClientRect();
    const imageOffsetX = imageRect.left - containerRect.left;
    const imageOffsetY = imageRect.top - containerRect.top;
    
    // Set initial dimensions
    const initialWidth = width || 200;
    const initialHeight = height || 100;
    
    // Constrain position within image bounds
    const maxX = imageOffsetX + image.offsetWidth - initialWidth;
    const maxY = imageOffsetY + image.offsetHeight - initialHeight;
    const constrainedX = Math.max(imageOffsetX, Math.min(imageOffsetX + (x || 0), maxX));
    const constrainedY = Math.max(imageOffsetY, Math.min(imageOffsetY + (y || 0), maxY));
    
    const box = document.createElement('div');
    box.className = 'area-box';
    box.id = 'area-' + boxCounter;
    box.style.cssText = `
        position: absolute;
        left: ${constrainedX}px;
        top: ${constrainedY}px;
        width: ${initialWidth}px;
        height: ${initialHeight}px;
        cursor: move;
        z-index: 10;
        touch-action: none;
    `;
    
    // Set initial state based on isSaved parameter
    box.classList.add(isSaved ? 'saved' : 'editing');
    
    // Add 7 resize handles (3 corners + 4 midpoints, excluding top-right for cancel button)
    const handles = [
        { class: 'resize-handle-nw', style: 'top: -4px; left: -4px; cursor: nw-resize;' },
        { class: 'resize-handle-n', style: 'top: -4px; left: 50%; transform: translateX(-50%); cursor: n-resize;' },
        { class: 'resize-handle-e', style: 'top: 50%; right: -4px; transform: translateY(-50%); cursor: e-resize;' },
        { class: 'resize-handle-se', style: 'bottom: -4px; right: -4px; cursor: se-resize;' },
        { class: 'resize-handle-s', style: 'bottom: -4px; left: 50%; transform: translateX(-50%); cursor: s-resize;' },
        { class: 'resize-handle-sw', style: 'bottom: -4px; left: -4px; cursor: sw-resize;' },
        { class: 'resize-handle-w', style: 'top: 50%; left: -4px; transform: translateY(-50%); cursor: w-resize;' }
    ];
    
    handles.forEach(handle => {
        const handleElement = document.createElement('div');
        handleElement.className = handle.class;
        handleElement.style.cssText = `
            position: absolute;
            width: 8px;
            height: 8px;
            background: white;
            border: 1px solid #333;
            border-radius: 0;
            z-index: 12;
            ${handle.style}
        `;
        box.appendChild(handleElement);
    });
    
    // Store image bounds for constraint checking
    box.dataset.imageOffsetX = imageOffsetX;
    box.dataset.imageOffsetY = imageOffsetY;
    box.dataset.imageWidth = image.offsetWidth;
    box.dataset.imageHeight = image.offsetHeight;
    
    // Add label
    const labelElement = document.createElement('div');
    labelElement.textContent = label || 'Area ' + boxCounter;
    labelElement.style.cssText = `
        position: absolute;
        top: -25px;
        left: 0;
        background: #007bff;
        color: white;
        padding: 2px 6px;
        font-size: 12px;
        border-radius: 3px;
        white-space: nowrap;
        pointer-events: none;
    `;
    box.appendChild(labelElement);
    
    // Add delete button
    const deleteBtn = document.createElement('button');
    deleteBtn.innerHTML = '×';
    deleteBtn.style.cssText = `
        position: absolute;
        top: -12px;
        right: -12px;
        width: 24px;
        height: 24px;
        border: none;
        background: #dc3545;
        color: white;
        border-radius: 50%;
        cursor: pointer;
        font-size: 16px;
        line-height: 1;
        z-index: 11;
    `;
    deleteBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        
        // Check if this is a saved area (has area_id from database)
        const areaData = areaBoxes.find(b => b.id === box.id);
        
        if (areaData && areaData.area_id) {
            // This is a saved area, delete from server
            if (confirm('Are you sure you want to delete this area? This will also delete the associated image file.')) {
                fetch('delete_area.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        area_id: areaData.area_id
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Remove from UI
                        container.removeChild(box);
                        areaBoxes = areaBoxes.filter(b => b.id !== box.id);
                    } else {
                        alert('Error deleting area: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error deleting area');
                });
            }
        } else {
             // This is an unsaved area, just remove from UI
             container.removeChild(box);
             areaBoxes = areaBoxes.filter(b => b.id !== box.id);
         }
     });
    box.appendChild(deleteBtn);
    
    container.appendChild(box);
    
    // Calculate and store relative positions
    const relativeX = ((constrainedX - imageOffsetX) / image.offsetWidth) * 100;
    const relativeY = ((constrainedY - imageOffsetY) / image.offsetHeight) * 100;
    const relativeWidth = (initialWidth / image.offsetWidth) * 100;
    const relativeHeight = (initialHeight / image.offsetHeight) * 100;
    
    areaBoxes.push({
        id: box.id,
        element: box,
        x: x || 0,
        y: y || 0,
        width: width || 100,
        height: height || 50,
        relativeX: relativeX,
        relativeY: relativeY,
        relativeWidth: relativeWidth,
        relativeHeight: relativeHeight
    });
    
    // Make box draggable and resizable with Interact.js and boundary constraints
    interact(box)
        .draggable({
            listeners: {
                start(event) {
                    // Change to editing state when dragging starts
                    event.target.classList.remove('saved');
                    event.target.classList.add('editing');
                },
                move(event) {
                    const target = event.target;
                    const imageOffsetX = parseFloat(target.dataset.imageOffsetX) || 0;
                    const imageOffsetY = parseFloat(target.dataset.imageOffsetY) || 0;
                    const imageWidth = parseFloat(target.dataset.imageWidth) || 0;
                    const imageHeight = parseFloat(target.dataset.imageHeight) || 0;
                    
                    let x = (parseFloat(target.getAttribute('data-x')) || 0) + event.dx;
                    let y = (parseFloat(target.getAttribute('data-y')) || 0) + event.dy;
                    
                    // Get current position and size
                    const currentLeft = parseFloat(target.style.left) || 0;
                    const currentTop = parseFloat(target.style.top) || 0;
                    const boxWidth = target.offsetWidth;
                    const boxHeight = target.offsetHeight;
                    
                    // Calculate new position
                    const newLeft = currentLeft + event.dx;
                    const newTop = currentTop + event.dy;
                    
                    // Constrain within image bounds
                    const constrainedLeft = Math.max(imageOffsetX, Math.min(newLeft, imageOffsetX + imageWidth - boxWidth));
                    const constrainedTop = Math.max(imageOffsetY, Math.min(newTop, imageOffsetY + imageHeight - boxHeight));
                    
                    // Update position
                     target.style.left = constrainedLeft + 'px';
                     target.style.top = constrainedTop + 'px';
                     
                     // Reset transform and data attributes
                     target.style.transform = 'translate(0px, 0px)';
                     target.setAttribute('data-x', 0);
                     target.setAttribute('data-y', 0);
                     
                     // Update relative position in areaBoxes array
                     const areaData = areaBoxes.find(area => area.element === target);
                     if (areaData) {
                         const image = document.getElementById('page-image');
                         const imageRect = image.getBoundingClientRect();
                         const targetRect = target.getBoundingClientRect();
                         areaData.relativeX = ((targetRect.left - imageRect.left) / image.offsetWidth) * 100;
                         areaData.relativeY = ((targetRect.top - imageRect.top) / image.offsetHeight) * 100;
                     }
                }
            }
        })
        .resizable({
            edges: { left: '.resize-handle-w, .resize-handle-nw, .resize-handle-sw', 
                    right: '.resize-handle-e, .resize-handle-se', 
                    bottom: '.resize-handle-s, .resize-handle-se, .resize-handle-sw', 
                     top: '.resize-handle-n, .resize-handle-nw' },
            listeners: {
                start(event) {
                    // Change to editing state when resizing starts
                    event.target.classList.remove('saved');
                    event.target.classList.add('editing');
                },
                move(event) {
                    const target = event.target;
                    const imageOffsetX = parseFloat(target.dataset.imageOffsetX) || 0;
                    const imageOffsetY = parseFloat(target.dataset.imageOffsetY) || 0;
                    const imageWidth = parseFloat(target.dataset.imageWidth) || 0;
                    const imageHeight = parseFloat(target.dataset.imageHeight) || 0;
                    
                    let x = (parseFloat(target.getAttribute('data-x')) || 0);
                    let y = (parseFloat(target.getAttribute('data-y')) || 0);
                    
                    // Get current position
                    const currentLeft = parseFloat(target.style.left) || 0;
                    const currentTop = parseFloat(target.style.top) || 0;
                    
                    // Calculate new dimensions and position
                    let newWidth = event.rect.width;
                    let newHeight = event.rect.height;
                    let newLeft = currentLeft + event.deltaRect.left;
                    let newTop = currentTop + event.deltaRect.top;
                    
                    // Constrain within image bounds
                    if (newLeft < imageOffsetX) {
                        newWidth -= (imageOffsetX - newLeft);
                        newLeft = imageOffsetX;
                    }
                    if (newTop < imageOffsetY) {
                        newHeight -= (imageOffsetY - newTop);
                        newTop = imageOffsetY;
                    }
                    if (newLeft + newWidth > imageOffsetX + imageWidth) {
                        newWidth = imageOffsetX + imageWidth - newLeft;
                    }
                    if (newTop + newHeight > imageOffsetY + imageHeight) {
                        newHeight = imageOffsetY + imageHeight - newTop;
                    }
                    
                    // Apply constraints
                     target.style.width = Math.max(50, newWidth) + 'px';
                     target.style.height = Math.max(25, newHeight) + 'px';
                     target.style.left = newLeft + 'px';
                     target.style.top = newTop + 'px';
                     
                     // Reset transform
                     target.style.transform = 'translate(0px, 0px)';
                     target.setAttribute('data-x', 0);
                     target.setAttribute('data-y', 0);
                     
                     // Update relative position and size in areaBoxes array
                     const areaData = areaBoxes.find(area => area.element === target);
                     if (areaData) {
                         const image = document.getElementById('page-image');
                         const imageRect = image.getBoundingClientRect();
                         const targetRect = target.getBoundingClientRect();
                         areaData.relativeX = ((targetRect.left - imageRect.left) / image.offsetWidth) * 100;
                         areaData.relativeY = ((targetRect.top - imageRect.top) / image.offsetHeight) * 100;
                         areaData.relativeWidth = (targetRect.width / image.offsetWidth) * 100;
                         areaData.relativeHeight = (targetRect.height / image.offsetHeight) * 100;
                     }
                }
            },
            modifiers: [
                interact.modifiers.restrictSize({
                    min: { width: 50, height: 25 }
                })
            ]
        });
    
    return box;
}

// Clear All functionality
document.getElementById('clear-all-btn').addEventListener('click', function() {
    const container = document.getElementById('image-container');
    areaBoxes.forEach(box => {
        if (box.element.parentNode) {
            container.removeChild(box.element);
        }
    });
    areaBoxes = [];
    boxCounter = 0;
});

// Save All functionality
document.getElementById('save-all-btn').addEventListener('click', function() {
    // Allow saving even with empty areas - this will clear all saved areas
    
    const imageContainer = document.getElementById('image-container');
    const image = imageContainer.querySelector('img');
    
    if (!image) {
        alert('No image found to save areas for');
        return;
    }
    
    // Get current page ID from URL
    const urlParams = new URLSearchParams(window.location.search);
    const pageId = urlParams.get('page_id');
    const editionId = urlParams.get('edition_id');
    
    if (!pageId) {
        alert('No page selected. Please select a page first.');
        return;
    }
    
    // Show loading state
    const saveBtn = this;
    const originalText = saveBtn.textContent;
    saveBtn.textContent = 'Saving...';
    saveBtn.disabled = true;
    
    if (areaBoxes.length === 0) {
        // No areas to save, just clear existing areas
        fetch('save_areas.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                edition_image_id: pageId,
                areas: []
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Areas cleared successfully!');
            } else {
                throw new Error('Failed to clear areas: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error: ' + error.message);
        })
        .finally(() => {
            saveBtn.textContent = originalText;
            saveBtn.disabled = false;
        });
        return;
    }
    
    let processedAreas = 0;
    const totalAreas = areaBoxes.length;
    const areasData = [];
    
    // Process each area box
    areaBoxes.forEach((box, index) => {
        const imageRect = image.getBoundingClientRect();
        const containerRect = imageContainer.getBoundingClientRect();
        const boxRect = box.element.getBoundingClientRect();
        
        // Calculate relative coordinates within the image
        const relativeX = (boxRect.left - imageRect.left) / image.offsetWidth * 100;
        const relativeY = (boxRect.top - imageRect.top) / image.offsetHeight * 100;
        const relativeWidth = boxRect.width / image.offsetWidth * 100;
        const relativeHeight = boxRect.height / image.offsetHeight * 100;
        
        // Crop and save image for this area
        cropAndSaveArea(box, index + 1, pageId, (imagePath) => {
            areasData.push({
                x: relativeX,
                y: relativeY,
                width: relativeWidth,
                height: relativeHeight,
                label: `Area ${index + 1}`,
                image_path: imagePath,
                relativeX: box.relativeX,
                relativeY: box.relativeY,
                relativeWidth: box.relativeWidth,
                relativeHeight: box.relativeHeight
            });
            
            processedAreas++;
            
            // When all areas are processed, save to database
            if (processedAreas === totalAreas) {
                saveAreasToDatabase(pageId, areasData, saveBtn, originalText);
            }
        });
    });
});

// Function to update area positions on zoom
function updateAreaPositions() {
    const container = document.getElementById('image-container');
    const image = document.getElementById('page-image');
    
    if (!image) return;
    
    const imageRect = image.getBoundingClientRect();
    const containerRect = container.getBoundingClientRect();
    const imageOffsetX = imageRect.left - containerRect.left;
    const imageOffsetY = imageRect.top - containerRect.top;
    
    areaBoxes.forEach(areaData => {
        const box = areaData.element;
        
        // Calculate new position and size based on current image dimensions
        const newLeft = imageOffsetX + (areaData.relativeX / 100) * image.offsetWidth;
        const newTop = imageOffsetY + (areaData.relativeY / 100) * image.offsetHeight;
        const newWidth = (areaData.relativeWidth / 100) * image.offsetWidth;
        const newHeight = (areaData.relativeHeight / 100) * image.offsetHeight;
        
        // Update box position and size
        box.style.left = newLeft + 'px';
        box.style.top = newTop + 'px';
        box.style.width = newWidth + 'px';
        box.style.height = newHeight + 'px';
        
        // Update stored image bounds for constraint checking
        box.dataset.imageOffsetX = imageOffsetX;
        box.dataset.imageOffsetY = imageOffsetY;
        box.dataset.imageWidth = image.offsetWidth;
        box.dataset.imageHeight = image.offsetHeight;
    });
}

// Function to store relative positions
function storeRelativePositions() {
    const image = document.getElementById('page-image');
    if (!image) return;
    
    areaBoxes.forEach(areaData => {
        const box = areaData.element;
        const imageRect = image.getBoundingClientRect();
        const boxRect = box.getBoundingClientRect();
        
        // Store relative positions as percentages
        areaData.relativeX = ((boxRect.left - imageRect.left) / image.offsetWidth) * 100;
        areaData.relativeY = ((boxRect.top - imageRect.top) / image.offsetHeight) * 100;
        areaData.relativeWidth = (boxRect.width / image.offsetWidth) * 100;
        areaData.relativeHeight = (boxRect.height / image.offsetHeight) * 100;
    });
}

// Observe image size changes for zoom responsiveness
function setupZoomObserver() {
    const image = document.getElementById('page-image');
    if (!image) return;
    
    // Use ResizeObserver to detect image size changes
    if (window.ResizeObserver) {
        const resizeObserver = new ResizeObserver(entries => {
            updateAreaPositions();
        });
        resizeObserver.observe(image);
    }
    
    // Fallback: Listen for window resize events
    window.addEventListener('resize', updateAreaPositions);
    
    // Listen for zoom events (wheel with ctrl/cmd)
    document.addEventListener('wheel', function(e) {
        if (e.ctrlKey || e.metaKey) {
            setTimeout(updateAreaPositions, 100); // Delay to allow zoom to complete
        }
    });
}

// Add double-click functionality to create area box at clicked position
function setupImageDoubleClick() {
    const image = document.getElementById('page-image');
    const container = document.getElementById('image-container');
    
    if (!image || !container) return;
    
    image.addEventListener('dblclick', function(e) {
        e.preventDefault();
        
        // Get click position relative to the image
        const imageRect = image.getBoundingClientRect();
        const containerRect = container.getBoundingClientRect();
        
        // Calculate click position relative to container
        const clickX = e.clientX - containerRect.left;
        const clickY = e.clientY - containerRect.top;
        
        // Calculate position relative to image within container
        const imageOffsetX = imageRect.left - containerRect.left;
        const imageOffsetY = imageRect.top - containerRect.top;
        
        // Check if click is within image bounds
        if (clickX >= imageOffsetX && clickX <= imageOffsetX + image.offsetWidth &&
            clickY >= imageOffsetY && clickY <= imageOffsetY + image.offsetHeight) {
            
            // Calculate position for new area box (center the box on click point)
            const boxWidth = 200;
            const boxHeight = 100;
            const newX = clickX - imageOffsetX - (boxWidth / 2);
            const newY = clickY - imageOffsetY - (boxHeight / 2);
            
            // Create new area box at clicked position
            addAreaBox(newX, newY, boxWidth, boxHeight);
        }
    });
}

// Load areas when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Wait for image to load before loading areas
    const image = document.getElementById('page-image');
    if (image) {
        if (image.complete) {
            loadAreas();
            setupZoomObserver();
            setupImageDoubleClick();
        } else {
            image.addEventListener('load', function() {
                loadAreas();
                setupZoomObserver();
                setupImageDoubleClick();
            });
        }
    }
});
</script>

<?php require_once '../../../../includes/footer.php'; ?>