<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LegMed Footer</title>
    <!-- Bootstrap 5.3 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom Footer CSS -->
    <link rel="stylesheet" href="{{ asset('css/common/footer.css') }}">
</head>
<body>

<!-- Footer -->
<footer class="legmed-footer">
    <!-- Top Border -->
    <div class="legmed-footer-border"></div>
    
    <!-- Main Footer Content -->
    <div class="container py-5">
        <div class="row">
            <!-- About & Logo Block (40% on desktop) -->
            <div class="col-lg-5 col-md-12 mb-4">
                <div class="legmed-footer-about">
                    <!-- Logo -->
                    <div class="legmed-footer-logo mb-3">
                        <img src="{{ asset('assets/images/web_assets/logo.jpg') }}" alt="LegMed Logo" class="legmed-logo-img">
                        <h5 class="legmed-brand-text">LegMed</h5>
                    </div>
                    
                    <!-- Company Blurb -->
                    <p class="legmed-footer-text">
                        LegMed is a leading healthcare technology company dedicated to providing innovative medical solutions and services. We are committed to advancing healthcare through cutting-edge technology and exceptional patient care.
                    </p>
                    
                    <!-- Address -->
                    <div class="legmed-footer-address">
                        <p class="legmed-footer-text mb-1">
                            <i class="fas fa-map-marker-alt me-2"></i>
                            123 Medical Center Drive, Suite 456<br>
                            Healthcare City, HC 12345
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Quick Links Block (20% on desktop) -->
            <div class="col-lg-2 col-md-6 mb-4">
                <div class="legmed-footer-links">
                    <h6 class="legmed-footer-title mb-3">Quick Links</h6>
                    <ul class="legmed-footer-nav">
                        <li><a href="#" class="legmed-footer-link">Home</a></li>
                        <li><a href="#" class="legmed-footer-link">About Us</a></li>
                        <li><a href="#" class="legmed-footer-link">Our Services</a></li>
                        <li><a href="#" class="legmed-footer-link">Association</a></li>
                        <li><a href="#" class="legmed-footer-link">Gallery</a></li>
                        <li><a href="#" class="legmed-footer-link">Contact Us</a></li>
                        <li><a href="#" class="legmed-footer-link">Careers</a></li>
                    </ul>
                </div>
            </div>
            
            <!-- Contact & Social Block (40% on desktop) -->
            <div class="col-lg-5 col-md-6 mb-4">
                <div class="legmed-footer-contact">
                    <h6 class="legmed-footer-title mb-3">Contact & Social</h6>
                    
                    <!-- Contact Info -->
                    <div class="legmed-contact-info mb-3">
                        <p class="legmed-footer-text mb-2">
                            <i class="fas fa-phone me-2"></i>
                            <a href="tel:+1234567890" class="legmed-footer-link">+1 (234) 567-8900</a>
                        </p>
                        <p class="legmed-footer-text mb-2">
                            <i class="fas fa-envelope me-2"></i>
                            <a href="mailto:info@legmed.com" class="legmed-footer-link">info@legmed.com</a>
                        </p>
                    </div>
                    
                    <!-- Social Icons -->
                    <div class="legmed-social-icons">
                        <a href="#" class="legmed-social-link" aria-label="LinkedIn">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a href="#" class="legmed-social-link" aria-label="Facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="legmed-social-link" aria-label="Twitter">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="legmed-social-link" aria-label="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Gallery Section -->
    <!-- <div class="legmed-gallery-section">
        <div class="container">
            <h6 class="legmed-footer-title text-center mb-4">Gallery</h6>
            <div class="row g-3">
                <div class="col-6 col-md-3">
                    <div class="legmed-gallery-item" data-bs-toggle="modal" data-bs-target="#legmed-gallery-modal" data-image-index="0">
                        <img src="{{ asset('assets/images/web_assets/gallery1.jpg') }}" alt="Gallery Image 1" class="legmed-gallery-thumb" loading="lazy">
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="legmed-gallery-item" data-bs-toggle="modal" data-bs-target="#legmed-gallery-modal" data-image-index="1">
                        <img src="{{ asset('assets/images/web_assets/gallery2.jpg') }}" alt="Gallery Image 2" class="legmed-gallery-thumb" loading="lazy">
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="legmed-gallery-item" data-bs-toggle="modal" data-bs-target="#legmed-gallery-modal" data-image-index="2">
                        <img src="{{ asset('assets/images/web_assets/gallery3.jpg') }}" alt="Gallery Image 3" class="legmed-gallery-thumb" loading="lazy">
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="legmed-gallery-item" data-bs-toggle="modal" data-bs-target="#legmed-gallery-modal" data-image-index="3">
                        <img src="{{ asset('assets/images/web_assets/gallery4.jpg') }}" alt="Gallery Image 4" class="legmed-gallery-thumb" loading="lazy">
                    </div>
                </div>
            </div>
        </div>
    </div>
     -->
    <!-- Copyright Bar -->
    <div class="legmed-copyright-bar">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center">
                    <p class="legmed-copyright-text mb-0">
                        © {{ date('Y') }} LegMed. All rights reserved.
                    </p>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Gallery Modal -->
<div class="modal fade" id="legmed-gallery-modal" tabindex="-1" aria-labelledby="legmed-gallery-modal-label" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content legmed-modal-content">
            <div class="modal-header legmed-modal-header">
                <button type="button" class="btn-close legmed-modal-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body legmed-modal-body p-0">
                <div class="legmed-modal-image-container position-relative">
                    <img id="legmed-modal-image" src="" alt="Gallery Image" class="legmed-modal-image">
                    
                    <!-- Navigation Arrows -->
                    <button class="legmed-modal-nav legmed-modal-prev" aria-label="Previous Image">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button class="legmed-modal-nav legmed-modal-next" aria-label="Next Image">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Font Awesome for Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<!-- Bootstrap 5.3 JS CDN -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom Gallery JavaScript -->
<script>
(function() {
    'use strict';
    
    // Gallery images array
    const galleryImages = [
        '{{ asset("assets/images/web_assets/gallery1.jpg") }}',
        '{{ asset("assets/images/web_assets/gallery2.jpg") }}',
        '{{ asset("assets/images/web_assets/gallery3.jpg") }}',
        '{{ asset("assets/images/web_assets/gallery4.jpg") }}'
    ];
    
    const galleryImageAlts = [
        'Gallery Image 1',
        'Gallery Image 2', 
        'Gallery Image 3',
        'Gallery Image 4'
    ];
    
    let currentImageIndex = 0;
    const modal = document.getElementById('legmed-gallery-modal');
    const modalImage = document.getElementById('legmed-modal-image');
    const prevBtn = document.querySelector('.legmed-modal-prev');
    const nextBtn = document.querySelector('.legmed-modal-next');
    
    // Function to update modal image
    function updateModalImage(index) {
        currentImageIndex = index;
        modalImage.src = galleryImages[index];
        modalImage.alt = galleryImageAlts[index];
    }
    
    // Function to show next image
    function showNext() {
        currentImageIndex = (currentImageIndex + 1) % galleryImages.length;
        updateModalImage(currentImageIndex);
    }
    
    // Function to show previous image
    function showPrev() {
        currentImageIndex = (currentImageIndex - 1 + galleryImages.length) % galleryImages.length;
        updateModalImage(currentImageIndex);
    }
    
    // Event listeners for gallery thumbnails
    document.querySelectorAll('.legmed-gallery-item').forEach(item => {
        item.addEventListener('click', function() {
            const index = parseInt(this.dataset.imageIndex);
            updateModalImage(index);
        });
    });
    
    // Event listeners for navigation buttons
    prevBtn.addEventListener('click', showPrev);
    nextBtn.addEventListener('click', showNext);
    
    // Keyboard navigation
    document.addEventListener('keydown', function(e) {
        if (modal.classList.contains('show')) {
            switch(e.key) {
                case 'ArrowLeft':
                    e.preventDefault();
                    showPrev();
                    break;
                case 'ArrowRight':
                    e.preventDefault();
                    showNext();
                    break;
                case 'Escape':
                    e.preventDefault();
                    bootstrap.Modal.getInstance(modal).hide();
                    break;
            }
        }
    });
    
    // Focus management for accessibility
    modal.addEventListener('shown.bs.modal', function() {
        modalImage.focus();
    });
})();
</script>

</body>
</html>