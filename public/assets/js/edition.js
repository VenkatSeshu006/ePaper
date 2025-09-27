document.addEventListener('DOMContentLoaded', function() {
    // Access data from window.editionData
    const { imagePaths, editionId, imageIds, editionTitle, baseUrl, availableDates, editionMap, totalPages, currentUrl } = window.editionData;

    const thumbnails = document.querySelectorAll('.preview-link');
    const images = document.querySelectorAll('.full-image');
    // Zoom buttons removed
    const fullScreenBtn = document.querySelector('.full-screen');
    const prevBtn = document.querySelector('.prev-button');
    const nextBtn = document.querySelector('.next-button');
    const imagePrevBtn = document.querySelector('.image-prev-button');
    const imageNextBtn = document.querySelector('.image-next-button');
    const pageCounter = document.querySelector('.page-counter');
    const pagination = document.querySelector('.pagination-controls'); // Use secondary header pagination as main
    const secondaryPagination = document.querySelector('.pagination-controls'); // Keep for compatibility
    const pageSelector = document.getElementById('page-selector'); // New: Page selector dropdown
    const popupModal = new bootstrap.Modal(document.getElementById('imagePopup'));
    const clipPreviewModal = new bootstrap.Modal(document.getElementById('clipPreviewModal'));
    const popupImage = document.getElementById('popupImage');
    const pdfDownloadBtn = document.querySelector('.pdf-download');
    const imageContainer = document.querySelector('.image-container');
    let currentIndex = 0;
    let zoomLevel = 1;
    let translateX = 0; // New: For panning
    let translateY = 0; // New: For panning
    let isDragging = false; // New: Track dragging state
    let startX, startY; // New: Track drag start position
    const pagesPerGroup = 5;

    // Cropper.js variable
    let cropper = null;

    // Initialize based on URL page parameter
    const urlParams = new URLSearchParams(window.location.search);
    const page = parseInt(urlParams.get('page')) || 1;
    currentIndex = Math.max(0, Math.min(totalPages - 1, page - 1));
    updateImage(currentIndex);
    renderPagination(currentIndex);
    populatePageSelector(); // New: Populate dropdown options

    // Thumbnail clicks
    thumbnails.forEach((thumbnail, index) => {
        thumbnail.addEventListener('click', function(e) {
            e.preventDefault();
            updateImage(index);
            renderPagination(index);
            updateURL(index + 1);
        });
    });

    // Zoom functionality removed

    // New: Zoom panning (left-click drag only when zoomed)
    imageContainer.addEventListener('mousedown', (e) => {
        if (zoomLevel <= 1 || cropper || e.button !== 0) return; // Only left-click, when zoomed, and not cropping
        isDragging = true;
        startX = e.clientX - translateX;
        startY = e.clientY - translateY;
        imageContainer.style.cursor = 'grab';
    });

    imageContainer.addEventListener('mousemove', (e) => {
        if (!isDragging) return;
        e.preventDefault(); // Prevent text selection
        translateX = e.clientX - startX;
        translateY = e.clientY - startY;
        updateZoom();
    });

    imageContainer.addEventListener('mouseup', () => {
        isDragging = false;
        imageContainer.style.cursor = 'pointer';
    });

    imageContainer.addEventListener('mouseleave', () => {
        isDragging = false;
        imageContainer.style.cursor = 'pointer';
    });

    // Touch swipe navigation (unchanged)
    let touchStartX = 0;
    let touchStartY = 0;
    let touchMoveX = 0;
    let touchMoveY = 0;
    
    imageContainer.addEventListener('touchstart', e => {
        touchStartX = e.touches[0].clientX;
        touchStartY = e.touches[0].clientY;
    });
    
    imageContainer.addEventListener('touchmove', e => {
        if (!e.touches || !e.touches[0]) return;
        touchMoveX = e.touches[0].clientX;
        touchMoveY = e.touches[0].clientY;
        const deltaX = touchStartX - touchMoveX;
        const deltaY = touchStartY - touchMoveY;
        
        // Only prevent default (block scrolling) if swipe is predominantly horizontal
        if (Math.abs(deltaX) > Math.abs(deltaY) && Math.abs(deltaX) > 10) {
            e.preventDefault(); // Prevent scrolling only for horizontal swipes
        }
    }, { passive: false });

    imageContainer.addEventListener('touchend', e => {
        if (!e.changedTouches || !e.changedTouches[0]) return;
        const touchEndX = e.changedTouches[0].clientX;
        const touchEndY = e.changedTouches[0].clientY;
        const swipeDistanceX = touchStartX - touchEndX;
        const swipeDistanceY = touchStartY - touchEndY;

        // Trigger navigation only if swipe is predominantly horizontal and exceeds threshold
        if (Math.abs(swipeDistanceX) > Math.abs(swipeDistanceY) && Math.abs(swipeDistanceX) > 50) {
            if (swipeDistanceX > 0) navigateImage(1); // Swipe left
            else navigateImage(-1); // Swipe right
        }
    });

    // Full-screen functionality
    fullScreenBtn.addEventListener('click', () => {
        const viewer = document.querySelector('.edition-viewer');
        if (document.fullscreenElement) {
            document.exitFullscreen();
        } else {
            viewer.requestFullscreen().catch(err => console.error('Full screen failed:', err));
        }
    });

    // Navigation buttons
    prevBtn.addEventListener('click', () => navigateImage(-1));
    nextBtn.addEventListener('click', () => navigateImage(1));
    imagePrevBtn.addEventListener('click', () => navigateImage(-1));
    imageNextBtn.addEventListener('click', () => navigateImage(1));

    // Mobile navigation buttons
    const mobilePrevBtn = document.getElementById('mobile-prev-btn');
    const mobileNextBtn = document.getElementById('mobile-next-btn');
    
    if (mobilePrevBtn) {
        mobilePrevBtn.addEventListener('click', () => navigateImage(-1));
    }
    
    if (mobileNextBtn) {
        mobileNextBtn.addEventListener('click', () => navigateImage(1));
    }

    // Pagination click handler
    pagination.addEventListener('click', function(e) {
        const link = e.target.closest('.page-link');
        if (!link) return;
        e.preventDefault();
        const page = parseInt(link.getAttribute('data-page'));
        if (!isNaN(page)) {
            // Play page turn sound effect
            playPageTurnSound();
            
            const newIndex = page - 1;
            updateImage(newIndex);
            renderPagination(newIndex);
            updateURL(page);
        }
    });

    // Social share buttons (main viewer)
    document.querySelectorAll('.edition-viewer .social-share-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const platform = this.getAttribute('data-platform');
            shareOnPlatform(platform, currentUrl);
        });
    });

    // Popup trigger for image click
    images.forEach(image => {
        image.addEventListener('click', function() {
            const imagePath = this.getAttribute('src');
            const popupUrl = `${baseUrl}/public/popup_template.php?image=${encodeURIComponent(imagePath)}`;
            popupImage.src = imagePath;
            document.querySelectorAll('#imagePopup .social-share-btn').forEach(btn => {
                btn.setAttribute('data-url', popupUrl);
            });
            popupModal.show();
        });
    });

    // Social share buttons in image popup
    document.querySelectorAll('#imagePopup .social-share-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const platform = this.getAttribute('data-platform');
            const url = this.getAttribute('data-url');
            shareOnPlatform(platform, url);
        });
    });

    // PDF Download functionality
    pdfDownloadBtn.addEventListener('click', function(e) {
        e.preventDefault();
        generatePDF();
    });

    // Clip button functionality
    const clipButton = document.querySelector('.clip-button');
    if (!clipButton) {
        console.error('Clip button not found');
        return;
    }
    
    clipButton.addEventListener('click', () => {
        console.log('Clip button clicked');
        const currentImage = document.querySelector('.full-image[style*="display: block"]');
        if (!currentImage) {
            console.error('No current image found for clipping');
            alert('No image is currently displayed for clipping');
            return;
        }

        if (cropper) {
            console.log('Destroying existing cropper');
            cropper.destroy();
            cropper = null;
            // Zoom buttons removed
            clipButton.innerHTML = '<i class="fa-solid fa-scissors"></i> Clip';
            // Reload areas when cropper is destroyed
            loadAreasForCurrentImage();
            return;
        }

        // Check if Cropper is available
        if (typeof Cropper === 'undefined') {
            console.error('Cropper.js library not loaded');
            alert('Cropper library is not available. Please refresh the page and try again.');
            return;
        }

        console.log('Starting cropper initialization');
        // Clear areas when starting cropper
        clearPublicAreas();
        
        cropper = new Cropper(currentImage, {
            zoomable: false,
            movable: false,
            rotatable: false,
            scalable: false,
            viewMode: 1,
            autoCrop: true,
            dragMode: 'move',
            cropBoxMovable: true,
            cropBoxResizable: true,
            ready() {
                const imgWidth = currentImage.naturalWidth;
                const imgHeight = currentImage.naturalHeight;
                const cropWidth = imgWidth * 0.3;
                const cropHeight = imgHeight * 0.3;
                const x = (imgWidth - cropWidth) / 2;
                const y = (imgHeight - cropHeight) / 4.5;

                cropper.setData({
                    x: x,
                    y: y,
                    width: cropWidth,
                    height: cropHeight
                });

                const cropBox = document.querySelector('.cropper-crop-box');
                const shareBtn = document.createElement('button');
                shareBtn.className = 'cropper-share-btn';
                shareBtn.innerHTML = '<i class="fas fa-share-alt"></i> Share It';
                const cancelBtn = document.createElement('button');
                cancelBtn.className = 'cropper-cancel-btn';
                cancelBtn.innerHTML = '<i class="fas fa-times"></i> Cancel';

                cropBox.appendChild(shareBtn);
                cropBox.appendChild(cancelBtn);

                updateButtonPositions(shareBtn, cancelBtn);

                shareBtn.addEventListener('click', () => {
                    console.log('Share button clicked, getting cropped canvas');
                    const canvas = cropper.getCroppedCanvas();
                    if (canvas) {
                        console.log('Canvas created successfully, converting to blob');
                        canvas.toBlob(blob => {
                            if (!blob) {
                                console.error('Failed to create blob from canvas');
                                alert('Failed to process the cropped image. Please try again.');
                                return;
                            }
                            
                            console.log('Blob created, preparing form data');
                            const formData = new FormData();
                            formData.append('image', blob, 'cropped.jpg');
                            formData.append('edition_id', editionId);
                            formData.append('image_id', imageIds[currentIndex]);

                            console.log('Sending AJAX request to save_clip.php');
                            $.ajax({
                                url: 'save_clip.php',
                                type: 'POST',
                                data: formData,
                                processData: false,
                                contentType: false,
                                success: function(response) {
                                    console.log('AJAX response received:', response);
                                    try {
                                        const data = JSON.parse(response);
                                        if (data.success) {
                                            console.log('Clip saved successfully:', data);
                                            const clipUrl = `${baseUrl}/ePaper/public/clips.php?id=${data.clip_id}`;
                                            
                                            // Ensure clip path starts with forward slash for web access
                                            let clipImagePath = data.clip_path;
                                            if (!clipImagePath.startsWith('/')) {
                                                clipImagePath = '/' + clipImagePath;
                                            }
                                            // The save_clip.php now returns paths with /ePaper/ prefix, so use directly
                                            
                                            console.log('Setting clip image src to:', clipImagePath);
                                            document.getElementById('clipPreviewImage').src = clipImagePath;
                                            document.getElementById('clipPreviewImage').style.display = 'block';
                                            document.getElementById('clipImageError').style.display = 'none';
                                            document.getElementById('clipImagePath').textContent = 'Path: ' + clipImagePath;
                                            document.getElementById('clipPreviewLink').value = clipUrl;
                                            
                                            // Set Open and Download button URLs
                                            document.getElementById('clipOpenBtn').href = clipUrl;
                                            document.getElementById('clipDownloadBtn').href = clipImagePath;
                                            
                                            document.querySelectorAll('#clipPreviewModal .social-share-btn').forEach(btn => {
                                                btn.setAttribute('data-url', clipUrl);
                                            });
                                            clipPreviewModal.show();
                                            cropper.destroy();
                                            cropper = null;
                                            // Zoom buttons removed
                                            clipButton.innerHTML = '<i class="fa-solid fa-scissors"></i> Clip';
                                            // Reload areas after successful clip
                                            loadAreasForCurrentImage();
                                        } else {
                                            console.error('Server error:', data.message);
                                            alert('Failed to save clip: ' + data.message);
                                        }
                                    } catch (e) {
                                        console.error('Failed to parse JSON response:', e);
                                        console.error('Raw response:', response);
                                        alert('Server returned invalid response: ' + response.substring(0, 100) + '...');
                                    }
                                },
                                error: function(xhr, status, error) {
                                    console.error('AJAX error:', {xhr, status, error});
                                    console.error('Response text:', xhr.responseText);
                                    alert('Error saving clip. Please check the console for details and try again.');
                                }
                            });
                        }, 'image/jpeg');
                    } else {
                        console.error('Failed to get cropped canvas');
                        alert('Failed to crop the image. Please try again.');
                    }
                });

                cancelBtn.addEventListener('click', () => {
                    cropper.destroy();
                    cropper = null;
                    // Zoom buttons removed
                    clipButton.innerHTML = '<i class="fa-solid fa-scissors"></i> Clip';
                    // Reload areas when cropper is cancelled
                    loadAreasForCurrentImage();
                });

                cropper.on('cropmove', () => updateButtonPositions(shareBtn, cancelBtn));
                cropper.on('cropend', () => updateButtonPositions(shareBtn, cancelBtn));
            }
        });

        // Zoom buttons removed
        clipButton.innerHTML = '<i class="fa-solid fa-scissors"></i> Stop';
    });

    // Datepicker for Archive - Fixed positioning
    const datepickerContainer = $("#datepicker-container");
    $("#archive-button").click(function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        datepickerContainer.toggle();
        const isMobile = window.matchMedia("(max-width: 768px)").matches;
        
        if (datepickerContainer.is(':visible')) {
            if (isMobile) {
                // Mobile/tablet: Modal overlay
                datepickerContainer.css({
                    position: 'fixed',
                    top: '50%',
                    left: '50%',
                    transform: 'translate(-50%, -50%)',
                    zIndex: 1050,
                    backgroundColor: '#fff',
                    padding: '10px',
                    boxShadow: '0 4px 8px rgba(0,0,0,0.2)',
                    borderRadius: '4px',
                    right: 'auto',
                    marginTop: '0'
                }).datepicker({
                    beforeShowDay: function(date) {
                        const formattedDate = $.datepicker.formatDate('yy-mm-dd', date);
                        return [availableDates.includes(formattedDate), ''];
                    },
                    onSelect: function(dateText) {
                        const editionId = editionMap[dateText];
                        if (editionId) {
                            window.location.href = 'edition.php?id=' + editionId;
                        }
                        datepickerContainer.hide();
                        removeOverlay();
                    },
                    dateFormat: 'yy-mm-dd',
                    changeMonth: true,
                    changeYear: true
                });

                // Add overlay for mobile
                const overlay = document.createElement('div');
                overlay.className = 'datepicker-overlay';
                overlay.style.cssText = 'position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1040;';
                document.body.appendChild(overlay);
                overlay.addEventListener('click', () => {
                    datepickerContainer.hide();
                    removeOverlay();
                });
            } else {
                // Desktop: Position exactly below the Archive button using fixed positioning
                const archiveButton = document.getElementById('archive-button');
                const buttonRect = archiveButton.getBoundingClientRect();
                
                // Use fixed positioning to ensure accurate placement
                datepickerContainer.css({
                    position: 'fixed',
                    top: (buttonRect.bottom + 5) + 'px',
                    left: buttonRect.left + 'px',
                    right: 'auto',
                    transform: 'none',
                    zIndex: '1050',
                    backgroundColor: 'white',
                    border: '1px solid #ddd',
                    borderRadius: '4px',
                    boxShadow: '0 4px 12px rgba(0,0,0,0.15)',
                    padding: '0',
                    marginTop: '0',
                    minWidth: '250px'
                }).datepicker({
                    beforeShowDay: function(date) {
                        const formattedDate = $.datepicker.formatDate('yy-mm-dd', date);
                        return [availableDates.includes(formattedDate), ''];
                    },
                    onSelect: function(dateText) {
                        const editionId = editionMap[dateText];
                        if (editionId) {
                            window.location.href = 'edition.php?id=' + editionId;
                        }
                        datepickerContainer.hide();
                    },
                    dateFormat: 'yy-mm-dd',
                    changeMonth: true,
                    changeYear: true
                });
            }
        } else {
            removeOverlay();
        }
    });

    // Close datepicker when clicking outside
    $(document).click(function(e) {
        if (!$(e.target).closest('#archive-button, #datepicker-container').length) {
            datepickerContainer.hide();
            removeOverlay();
        }
    });

    // Helper function to remove overlay
    function removeOverlay() {
        const overlay = document.querySelector('.datepicker-overlay');
        if (overlay) overlay.remove();
    }

    // Other helper functions
    function updateButtonPositions(shareBtn, cancelBtn) {
        shareBtn.style.position = 'absolute';
        shareBtn.style.top = '-25px';
        shareBtn.style.left = '0px';
        shareBtn.style.zIndex = '1002';

        cancelBtn.style.position = 'absolute';
        cancelBtn.style.top = '-25px';
        cancelBtn.style.left = '85px';
        cancelBtn.style.zIndex = '1002';
    }

    function shareOnPlatform(platform, url) {
        switch (platform) {
            case 'facebook': window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`, '_blank'); break;
            case 'twitter': window.open(`https://x.com/intent/tweet?url=${encodeURIComponent(url)}`, '_blank'); break;
            case 'whatsapp': window.open(`https://api.whatsapp.com/send?text=${encodeURIComponent(url)}`, '_blank'); break;
            case 'linkedin': window.open(`https://www.linkedin.com/shareArticle?mini=true&url=${encodeURIComponent(url)}`, '_blank'); break;
            case 'telegram': window.open(`https://t.me/share/url?url=${encodeURIComponent(url)}`, '_blank'); break;
            case 'print': window.print(); break;
            case 'email': window.location.href = `mailto:?subject=Check this clip&body=${encodeURIComponent(url)}`; break;
        }
    }

    document.getElementById('copyClipLinkBtn')?.addEventListener('click', function() {
        const linkInput = document.getElementById('clipPreviewLink');
        linkInput.select();
        navigator.clipboard.writeText(linkInput.value).then(() => {
            alert('Link copied to clipboard!');
        }).catch(err => {
            console.error('Failed to copy:', err);
            alert('Failed to copy link. Please copy manually.');
        });
    });

    document.querySelectorAll('#clipPreviewModal .social-share-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const platform = this.getAttribute('data-platform');
            const url = this.getAttribute('data-url');
            shareOnPlatform(platform, url);
        });
    });

    function updateImageTitleOverlay(index) {
        const pageNumber = index + 1;
        const pageNumberElement = document.getElementById('page-number');
        if (pageNumberElement) {
            pageNumberElement.textContent = `Page ${pageNumber}`;
        }
    }

    function updateImage(index) {
        if (index < 0 || index >= totalPages) return;
        images.forEach(img => img.style.display = 'none');
        images[index].style.display = 'block';
        thumbnails.forEach(thumb => thumb.classList.remove('active'));
        thumbnails[index].classList.add('active');
        currentIndex = index;
        pageCounter.textContent = `Page ${currentIndex + 1} of ${totalPages}`;
        resetZoom();
        
        // Update the image title overlay
        updateImageTitleOverlay(index);
    }

    // Page turn sound effect
    const pageTurnSound = new Audio('/public/assets/sounds/page-turn.mp3');
    pageTurnSound.volume = 0.3; // Set volume to 30%
    
    function playPageTurnSound() {
        try {
            pageTurnSound.currentTime = 0; // Reset to beginning
            pageTurnSound.play().catch(e => {
                // Silently handle autoplay restrictions
                console.log('Audio autoplay prevented');
            });
        } catch (e) {
            // Silently handle any audio errors
            console.log('Audio playback error');
        }
    }

    function navigateImage(direction) {
        const newIndex = currentIndex + direction;
        if (newIndex < 0 || newIndex >= totalPages) return;
        
        // Play page turn sound effect
        playPageTurnSound();
        
        updateImage(newIndex);
        renderPagination(newIndex);
        updateURL(newIndex + 1);
    }

    function populatePageSelector() {
        if (!pageSelector) return;
        
        // Clear existing options
        pageSelector.innerHTML = '';
        
        // Add options for each page
        for (let i = 1; i <= totalPages; i++) {
            const option = document.createElement('option');
            option.value = i;
            option.textContent = `Page ${i}`;
            pageSelector.appendChild(option);
        }
        
        // Set current page as selected
        pageSelector.value = currentIndex + 1;
        
        // Add event listener for dropdown change
        pageSelector.addEventListener('change', function() {
            const selectedPage = parseInt(this.value);
            if (selectedPage >= 1 && selectedPage <= totalPages) {
                // Play page turn sound effect
                playPageTurnSound();
                
                updateImage(selectedPage - 1);
                renderPagination(selectedPage - 1);
                updateURL(selectedPage);
            }
        });
    }

    function renderPagination(index) {
        const currentPage = index + 1;
        const isMobile = window.matchMedia("(max-width: 576px)").matches;
        let html = '';
        let secondaryHtml = ''; // New: For secondary header
        
        // Update page selector dropdown
        if (pageSelector) {
            pageSelector.value = currentPage;
        }
        if (isMobile) {
            html = `
                <a href="#" class="page-link prev-page" data-page="${currentPage - 1}"><i class="fas fa-chevron-left"></i></a>
                <span class="page-link active">${currentPage}</span>
                <a href="#" class="page-link next-page" data-page="${currentPage + 1}"><i class="fas fa-chevron-right"></i></a>
            `;
            // Secondary header pagination for mobile
            secondaryHtml = `
                <button class="secondary-nav-btn prev-page" data-page="${currentPage - 1}" ${currentPage <= 1 ? 'disabled' : ''}>
                    <i class="fas fa-chevron-left"></i>
                </button>
                <span class="page-indicator">Page ${currentPage} of ${totalPages}</span>
                <button class="secondary-nav-btn next-page" data-page="${currentPage + 1}" ${currentPage >= totalPages ? 'disabled' : ''}>
                    <i class="fas fa-chevron-right"></i>
                </button>
            `;
        } else {
            const startPage = Math.floor((currentPage - 1) / pagesPerGroup) * pagesPerGroup + 1;
            const endPage = Math.min(startPage + pagesPerGroup - 1, totalPages);
            
            if (currentPage > 1) {
                html += `<a href="#" class="page-link prev-page" data-page="${currentPage - 1}"><i class="fas fa-chevron-left"></i></a>`;
            }
            for (let i = startPage; i <= endPage; i++) {
                const activeClass = (i === currentPage) ? 'active' : '';
                html += `<a href="#" class="page-link ${activeClass}" data-page="${i}">${i}</a>`;
            }
            if (currentPage < totalPages) {
                html += `<a href="#" class="page-link next-page" data-page="${currentPage + 1}"><i class="fas fa-chevron-right"></i></a>`;
            }
            
            // Secondary header pagination for desktop
            secondaryHtml = `
                <button class="secondary-nav-btn prev-page" data-page="${currentPage - 1}" ${currentPage <= 1 ? 'disabled' : ''}>
                    <i class="fas fa-chevron-left"></i>
                </button>
                <div class="page-numbers">
                    ${Array.from({length: Math.min(5, totalPages)}, (_, i) => {
                        const pageNum = startPage + i;
                        if (pageNum > totalPages) return '';
                        const activeClass = pageNum === currentPage ? 'active' : '';
                        return `<button class="page-num-btn ${activeClass}" data-page="${pageNum}">${pageNum}</button>`;
                    }).join('')}
                </div>
                <button class="secondary-nav-btn next-page" data-page="${currentPage + 1}" ${currentPage >= totalPages ? 'disabled' : ''}>
                    <i class="fas fa-chevron-right"></i>
                </button>
                <span class="page-indicator">Page ${currentPage} of ${totalPages}</span>
            `;
        }
        
        // Now pagination and secondaryPagination both point to the same element
        if (pagination) {
            pagination.innerHTML = secondaryHtml;
            
            // Add event listeners for pagination
            pagination.querySelectorAll('[data-page]').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const page = parseInt(this.getAttribute('data-page'));
                    if (page >= 1 && page <= totalPages) {
                        updateImage(page - 1);
                        renderPagination(page - 1);
                        updateURL(page);
                    }
                });
            });
        }
    }

    function updateZoom() {
        if (cropper) return;
        const image = images[currentIndex];
        const containerWidth = imageContainer.clientWidth;
        const containerHeight = imageContainer.clientHeight;
        const imageWidth = image.naturalWidth * zoomLevel;
        const imageHeight = image.naturalHeight * zoomLevel;

        // Limit translation to keep image within bounds
        translateX = Math.max(-(imageWidth - containerWidth), Math.min(0, translateX));
        translateY = Math.max(-(imageHeight - containerHeight), Math.min(0, translateY));

        image.style.transform = `scale(${zoomLevel}) translate(${translateX}px, ${translateY}px)`;
        image.style.transformOrigin = 'center center';
    }

    function resetZoom() {
        zoomLevel = 1;
        translateX = 0;
        translateY = 0;
        updateZoom();
    }

    function updateURL(page) {
        const url = new URL(window.location);
        url.searchParams.set('id', editionId);
        url.searchParams.set('page', page);
        window.history.pushState({}, '', url);
    }

    function showPDFLoadingAnimation(current = 0, total = totalPages) {
        pdfDownloadBtn.disabled = true;
        pdfDownloadBtn.innerHTML = `<i class="fas fa-spinner fa-spin"></i> Generating ${current}/${total}`;
    }

    function hidePDFLoadingAnimation() {
        pdfDownloadBtn.disabled = false;
        pdfDownloadBtn.innerHTML = '<i class="fas fa-download"></i> PDF';
    }

    async function generatePDF() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF('p', 'mm', 'a4');
        const pageWidth = 210;
        const pageHeight = 297;

        showPDFLoadingAnimation(0);
        for (let i = 0; i < imagePaths.length; i++) {
            const img = new Image();
            img.crossOrigin = 'Anonymous';
            img.src = imagePaths[i];
            await new Promise(resolve => img.onload = resolve);
            const imgWidth = img.width;
            const imgHeight = img.height;
            const scale = Math.min(pageWidth / imgWidth, pageHeight / imgHeight);
            const scaledWidth = imgWidth * scale;
            const scaledHeight = imgHeight * scale;
            if (i > 0) doc.addPage();
            doc.addImage(img, 'JPEG', (pageWidth - scaledWidth) / 2, (pageHeight - scaledHeight) / 2, scaledWidth, scaledHeight);
            showPDFLoadingAnimation(i + 1); // Update progress
        }
        doc.save(`${editionTitle.replace(/\s+/g, '-')}-edition.pdf`);
        hidePDFLoadingAnimation();
    }

    // Floating social share button functionality
    const floatingShareBtn = document.createElement('button');
    floatingShareBtn.className = 'floating-share-btn';
    floatingShareBtn.innerHTML = '<i class="fas fa-share"></i>';
    document.body.appendChild(floatingShareBtn);

    const sharePopup = document.createElement('div');
    sharePopup.id = 'sharePopup';
    sharePopup.innerHTML = `
        <button class="close-share-btn"><i class="fas fa-times"></i></button>
        <a href="#" class="social-share-btn" data-platform="facebook"><i class="fab fa-facebook"></i></a>
        <a href="#" class="social-share-btn" data-platform="twitter"><i class="fab fa-twitter"></i></a>
        <a href="#" class="social-share-btn" data-platform="whatsapp"><i class="fab fa-whatsapp"></i></a>
        <a href="#" class="social-share-btn" data-platform="linkedin"><i class="fab fa-linkedin"></i></a>
        <a href="#" class="social-share-btn" data-platform="telegram"><i class="fab fa-telegram"></i></a>
        <a href="#" class="social-share-btn" data-platform="print"><i class="fas fa-print"></i></a>
        <a href="#" class="social-share-btn" data-platform="email"><i class="fas fa-envelope"></i></a>
    `;
    document.body.appendChild(sharePopup);

    floatingShareBtn.addEventListener('click', function(e) {
        e.preventDefault();
        sharePopup.style.display = sharePopup.style.display === 'block' ? 'none' : 'block';
    });

    // Close button for share popup
    const closeShareBtn = sharePopup.querySelector('.close-share-btn');
    if (closeShareBtn) {
        closeShareBtn.addEventListener('click', function(e) {
            e.preventDefault();
            sharePopup.style.display = 'none';
        });
    }

    // Social share functionality for floating button
    document.querySelectorAll('#sharePopup .social-share-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const platform = this.getAttribute('data-platform');
            shareOnPlatform(platform, currentUrl);
            sharePopup.style.display = 'none'; // Close after sharing
        });
    });

    // Close popup if clicking outside
    document.addEventListener('click', function(e) {
        if (!floatingShareBtn.contains(e.target) && !sharePopup.contains(e.target)) {
            sharePopup.style.display = 'none';
        }
    });

    // Public Area Mapping Functionality
    let currentAreas = [];

    async function loadPublicAreas(imageId) {
        try {
            const response = await fetch(`load_public_areas.php?edition_image_id=${imageId}`);
            const data = await response.json();
            
            if (data.success && data.areas) {
                currentAreas = data.areas;
                displayPublicAreas();
            } else {
                currentAreas = [];
                clearPublicAreas();
            }
        } catch (error) {
            console.error('Error loading public areas:', error);
            currentAreas = [];
            clearPublicAreas();
        }
    }

    function displayPublicAreas() {
        clearPublicAreas();
        
        // Don't display areas when cropper is active
        if (cropper) return;
        
        const currentImage = document.querySelector('.full-image[style*="display: block"], .full-image:not([style*="display: none"])');
        if (!currentImage || currentAreas.length === 0) return;

        currentAreas.forEach((area, index) => {
            const areaBox = document.createElement('div');
            areaBox.className = 'public-area-box';
            areaBox.style.left = area.x + '%';
            areaBox.style.top = area.y + '%';
            areaBox.style.width = area.width + '%';
            areaBox.style.height = area.height + '%';
            
            // Labels removed - only show boxes without text
            
            // Add click event to open area image modal
            areaBox.addEventListener('click', function(e) {
                e.stopPropagation();
                console.log('Area clicked:', area);
                
                // Check if area has an associated image
                if (area.image_url && area.image_url.trim() !== '') {
                    // Show area image in modal
                    const areaImageModal = new bootstrap.Modal(document.getElementById('areaImageModal'));
                    const areaImage = document.getElementById('areaImage');
                    const areaImageDownload = document.getElementById('areaImageDownload');
                    const areaImageOpen = document.getElementById('areaImageOpen');
                    
                    // Set image source and download/open links (adjust path for public directory)
                    const imageUrl = area.image_url.startsWith('uploads/') ? '../' + area.image_url : area.image_url;
                    areaImage.src = imageUrl;
                    areaImageDownload.href = imageUrl;
                    areaImageOpen.href = imageUrl;
                    
                    // Set social share URLs
                    const popupUrl = `/public/area_popup_template.php?image_url=${encodeURIComponent(area.image_url)}`;
                    document.querySelectorAll('#areaImageModal .social-share-btn').forEach(btn => {
                        btn.setAttribute('data-url', popupUrl);
                    });
                    
                    // Show the modal
                    areaImageModal.show();
                } else {
                    console.log('No image associated with this area');
                }
            });
            
            imageContainer.appendChild(areaBox);
        });
    }

    function clearPublicAreas() {
        const existingAreas = document.querySelectorAll('.public-area-box');
        existingAreas.forEach(area => area.remove());
    }

    function updatePublicAreasOnZoom() {
        // Areas use percentage positioning, so they scale automatically with zoom
        // No additional scaling needed as CSS handles this
    }

    // Load areas when image changes
    function loadAreasForCurrentImage() {
        // Don't load areas when cropper is active
        if (cropper) return;
        
        if (imageIds && imageIds[currentIndex]) {
            loadPublicAreas(imageIds[currentIndex]);
        }
    }

    // Override the existing updateImage function to include area loading
    const originalUpdateImage = updateImage;
    updateImage = function(index) {
        originalUpdateImage(index);
        // Load areas for the new image after a short delay to ensure image is displayed
        setTimeout(() => {
            loadAreasForCurrentImage();
        }, 100);
    };

    // Load areas for the initial image
    setTimeout(() => {
        loadAreasForCurrentImage();
    }, 500);

    // Update areas when zoom changes
    const originalUpdateZoom = updateZoom;
    updateZoom = function() {
        originalUpdateZoom();
        updatePublicAreasOnZoom();
    };

    // Archive button datepicker functionality - removed duplicate implementation
    
    function initializeDatepicker() {
        if (typeof $ !== 'undefined' && $.fn.datepicker && availableDates && availableDates.length > 0) {
            // Clear existing content
            datepickerContainer.innerHTML = '';
            
            // Create a temporary input for the datepicker
            const tempInput = document.createElement('input');
            tempInput.type = 'text';
            tempInput.style.display = 'none';
            datepickerContainer.appendChild(tempInput);
            
            // Initialize jQuery UI datepicker
            $(tempInput).datepicker({
                inline: true,
                dateFormat: 'yy-mm-dd',
                beforeShowDay: function(date) {
                    const dateStr = $.datepicker.formatDate('yy-mm-dd', date);
                    const isAvailable = availableDates.includes(dateStr);
                    return [isAvailable, isAvailable ? 'available-date' : 'unavailable-date'];
                },
                onSelect: function(dateText) {
                    if (editionMap && editionMap[dateText]) {
                        const newEditionId = editionMap[dateText];
                        window.location.href = `edition.php?id=${newEditionId}`;
                    }
                    datepickerContainer.style.display = 'none';
                }
            });
            
            // Move the datepicker widget to our container
            const datepickerWidget = $(tempInput).datepicker('widget');
            if (datepickerWidget.length > 0) {
                datepickerContainer.appendChild(datepickerWidget[0]);
                tempInput.remove();
            }
        } else {
            // Fallback: create a simple date list
            createSimpleDateList();
        }
    }
    
    function createSimpleDateList() {
        datepickerContainer.innerHTML = '';
        
        if (availableDates && availableDates.length > 0) {
            const dateList = document.createElement('div');
            dateList.className = 'simple-date-list';
            dateList.style.cssText = 'max-height: 300px; overflow-y: auto; padding: 10px;';
            
            availableDates.forEach(date => {
                const dateItem = document.createElement('div');
                dateItem.className = 'date-item';
                dateItem.style.cssText = 'padding: 8px 12px; cursor: pointer; border-bottom: 1px solid #eee; transition: background-color 0.2s;';
                dateItem.textContent = new Date(date).toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
                
                dateItem.addEventListener('mouseenter', function() {
                    this.style.backgroundColor = '#f5f5f5';
                });
                
                dateItem.addEventListener('mouseleave', function() {
                    this.style.backgroundColor = '';
                });
                
                dateItem.addEventListener('click', function() {
                    if (editionMap && editionMap[date]) {
                        const newEditionId = editionMap[date];
                        window.location.href = `edition.php?id=${newEditionId}`;
                    }
                    datepickerContainer.style.display = 'none';
                });
                
                dateList.appendChild(dateItem);
            });
            
            datepickerContainer.appendChild(dateList);
        }
    }
});