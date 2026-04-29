<?php
// 1. Start the Session FIRST
session_start();

// 2. Connect to the database
require 'db_connect.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sell Your Waste | Recycle Mart BD</title>
    
    <link rel="icon" type="image/png" href="assets/images/ui/Recycle-Mart-logo-fav.png">
    
    <link rel="stylesheet" href="assets/css/global.css">
    <link rel="stylesheet" href="assets/css/forms.css">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        .auth-actions {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-right: 16px;
            padding-right: 16px;
            border-right: 2px solid var(--border-color);
        }
        .site-header { position: relative; z-index: 999999 !important; }
        .mobile-overlay { z-index: 999900 !important; }
        .main-nav.active {
            background-color: #ffffff !important; 
            backdrop-filter: none !important;
            -webkit-backdrop-filter: none !important;
            filter: none !important;
            box-shadow: -5px 0 25px rgba(0,0,0,0.15) !important;
        }
        @media (max-width: 768px) {
            .auth-actions {
                gap: 8px; margin-right: 0px !important; padding-right: 8px !important; border-right: none !important; 
            }
            .auth-text { display: none !important; }
            .header-actions { gap: 12px !important; }
            .site-header .main-logo { max-height: 32px !important; width: auto; }
            .header-inner { padding-left: 12px !important; padding-right: 12px !important; }
        }
    </style>
</head>
<body class="bg-light">

    <div class="mobile-overlay"></div>
    <header class="site-header">
        <div class="container header-inner">
            <a href="/" class="logo">
                <img src="assets/images/ui/Recycle-Mart-Logo.png" alt="Recycle Mart Logo" class="main-logo">
            </a>

            <nav class="main-nav">
                <div class="mobile-menu-header">
                    <a href="/" class="logo">
                        <img src="assets/images/ui/Recycle-Mart-Logo.png" alt="Recycle Mart Logo" class="main-logo">
                    </a>
                    <button class="mobile-close" aria-label="Close Menu"><i class="fa-solid fa-xmark"></i></button>
                </div>
                
                <ul class="nav-links">
                    <li><a href="/">Home</a></li>
                    <li><a href="/#about-us">About Us</a></li>
                    <li><a href="/#how-it-works">How It Works</a></li>
                    <li><a href="/#categories">Categories</a></li>
                    <li><a href="listings.php">Listings</a></li>
                    <li><a href="/#contact-us">Contact</a></li>
                </ul>

                <div class="mobile-only-action">
                    <a href="sell-waste.php" class="btn btn-primary" style="width: 100%; margin-top: 24px;">Sell Your Waste <i class="fa-solid fa-arrow-right" style="margin-left: 8px;"></i></a>
                </div>
            </nav>

            <div class="header-actions">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <div class="auth-actions">
                        <a href="logout.php" style="color: #ef4444; font-size: 1.1rem; margin-right: 8px;" title="Logout">
                            <i class="fa-solid fa-arrow-right-from-bracket"></i>
                        </a>
                        <a href="my-account.php" style="font-weight: 700; font-size: 0.95rem; color: var(--text-main); display: flex; align-items: center; text-decoration: none;">
                            <i class="fa-solid fa-circle-user text-green" style="margin-right: 6px; font-size: 1.2rem;"></i> 
                            <span class="auth-text" style="color: var(--text-main);">Hi, <?= htmlspecialchars($_SESSION['first_name']) ?></span>
                        </a>
                    </div>
                <?php else: ?>
                    <div class="auth-actions">
                        <a href="login.php" style="font-size: 0.95rem; color: var(--text-main); font-weight: 700; text-decoration: none; display: flex; align-items: center;">
                            <i class="fa-regular fa-circle-user" style="margin-right: 6px; font-size: 1.2rem;"></i> 
                            <span class="auth-text">Login</span>
                        </a>
                    </div>
                <?php endif; ?>
                <button class="cart-btn" aria-label="Cart" id="header-cart-btn">
                    <i class="fa-solid fa-cart-shopping"></i>
                </button>
                <a href="sell-waste.php" class="btn btn-primary desktop-sell-btn">Sell Your Waste <i class="fa-solid fa-arrow-right" style="font-size: 0.8rem; margin-left: 4px;"></i></a>
                <button class="mobile-toggle" aria-label="Open Menu"><i class="fa-solid fa-bars-staggered"></i></button>
            </div>
        </div>
    </header>

    <main class="form-page-container">
        <div class="container form-container">
            <div class="form-header text-center">
                <h2>List Your <span class="text-green">Materials</span></h2>
                <p>Fill out the details below. Admins will review your listing before it goes live.</p>
            </div>

            <div class="progress-wrapper">
                <ul class="progress-bar">
                    <li class="active">Details</li>
                    <li>Photos</li>
                    <li>Contact</li>
                </ul>
            </div>

            <div class="form-wrapper">
                <form id="sellWasteForm">
                    
                    <div class="form-step active" id="step1">
                        <h3>1. Material Details</h3>
                        <div class="input-group">
                            <label>Listing Title</label>
                            <input type="text" id="listingTitle" placeholder="e.g., 50kg Clean Copper Wire" required>
                        </div>
                        
                        <div class="input-group">
                            <label>Category</label>
                            <select id="listingCategory" required>
                                <option value="" disabled selected>Select Category</option>
                                <option value="paper">Paper & Cardboard</option>
                                <option value="plastics">Plastics</option>
                                <option value="metals">Metals</option>
                                <option value="electronics">Electronics</option>
                                <option value="wood">Wood</option>
                                <option value="glass">Glass</option>
                            </select>
                        </div>

                        <div class="form-row">
                            <div class="input-group">
                                <label>Expected Price (Tk.)</label>
                                <input type="number" id="listingPrice" placeholder="0.00" required>
                            </div>
                            <div class="input-group">
                                <label>Unit</label>
                                <select id="listingUnit" required>
                                    <option value="kg">Per Kg</option>
                                    <option value="piece">Per Piece</option>
                                    <option value="bundle">Per Bundle</option>
                                    <option value="ton">Per Ton</option>
                                </select>
                            </div>
                        </div>

                        <div class="btn-row justify-end">
                            <button type="button" class="btn btn-primary btn-next">Next Step <i class="fa-solid fa-arrow-right" style="margin-left: 8px;"></i></button>
                        </div>
                    </div>

                    <div class="form-step" id="step2">
                        <h3>2. Upload Photos</h3>
                        <p class="text-muted" style="margin-bottom: 20px; font-size: 0.9rem;">Clear photos help buyers understand the condition of your materials.</p>
                        
                        <div class="drag-drop-zone" id="dropZone">
                            <i class="fa-solid fa-cloud-arrow-up" style="font-size: 32px; color: var(--primary-green); margin-bottom: 12px;"></i>
                            <p>Drag & drop images here or <strong>browse files</strong></p>
                            <input type="file" id="fileInput" multiple accept="image/*" hidden>
                        </div>
                        
                        <div class="image-preview-container" id="imagePreview"></div>

                        <div class="btn-row flex-between">
                            <button type="button" class="btn btn-outline btn-prev"><i class="fa-solid fa-arrow-left" style="margin-right: 8px;"></i> Back</button>
                            <button type="button" class="btn btn-primary btn-next">Next Step <i class="fa-solid fa-arrow-right" style="margin-left: 8px;"></i></button>
                        </div>
                    </div>

                    <div class="form-step" id="step3">
                        <h3>3. Your Contact Info</h3>
                        
                        <div class="input-group">
                            <label>Full Name</label>
                            <input type="text" id="sellerName" placeholder="John Doe" required>
                        </div>

                        <div class="input-group">
                            <label>Phone Number</label>
                            <input type="tel" id="sellerPhone" placeholder="+880 xxxx xxxx" required>
                        </div>

                        <div class="input-group">
                            <label>Pickup Location (City, Area)</label>
                            <input type="text" id="sellerLocation" placeholder="e.g., Dhaka, Bangladesh" required>
                        </div>

                        <div class="btn-row flex-between">
                            <button type="button" class="btn btn-outline btn-prev"><i class="fa-solid fa-arrow-left" style="margin-right: 8px;"></i> Back</button>
                            <button type="submit" id="submitListingBtn" class="btn btn-primary">Submit Listing</button>
                        </div>
                    </div>

                    <div class="form-success hidden text-center" id="listingSuccessMessage">
                        <div class="success-icon">
                            <i class="fa-solid fa-check" style="color: white; font-size: 32px;"></i>
                        </div>
                        <h3>Listing Submitted!</h3>
                        <p>Thank you. An admin will review your materials and publish the listing shortly.</p>
                        <a href="/" class="btn btn-outline mt-4">Return Home</a>
                    </div>

                </form>
            </div>
        </div>
    </main>

    <footer class="site-footer">
        <div class="container footer-grid">
            <div class="footer-brand">
                <a href="/" class="logo footer-logo">
                    <img src="assets/images/ui/Recycle-Mart-Logo.png" alt="Recycle Mart Logo" class="main-logo" style="filter: brightness(0) invert(1);">
                </a>
                <p>A community marketplace for reusable materials in Bangladesh. Let's build a cleaner, greener and more sustainable future together.</p>
                <div class="social-links">
                    <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#"><i class="fa-brands fa-twitter"></i></a>
                    <a href="#"><i class="fa-brands fa-linkedin-in"></i></a>
                </div>
            </div>

            <div class="footer-links">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="/">Home</a></li>
                    <li><a href="/#about-us">About Us</a></li>
                    <li><a href="/#how-it-works">How It Works</a></li>
                    <li><a href="/#categories">Categories</a></li>
                    <li><a href="listings.php">Listings</a></li>
                    <li><a href="/#contact-us">Contact Us</a></li>
                </ul>
            </div>

            <div class="footer-links">
                <h4>Support</h4>
                <ul>
                    <li><a href="sell-waste.php">Sell Your Waste</a></li>
                    <li><a href="#">Help Center</a></li>
                    <li><a href="#">Safety Tips</a></li>
                    <li><a href="#">Terms & Conditions</a></li>
                    <li><a href="#">Privacy Policy</a></li>
                </ul>
            </div>

            <div class="footer-newsletter">
                <h4>Stay Updated</h4>
                <p>Subscribe to our newsletter for tips, updates, and new listings.</p>
                <form class="newsletter-form">
                    <input type="email" placeholder="Enter your email" required>
                    <button type="submit" aria-label="Subscribe">
                        <i class="fa-solid fa-paper-plane"></i>
                    </button>
                </form>
            </div>
        </div>
        
        <div class="footer-bottom">
            <div class="container flex-between">
                <p>&copy; 2026 Recycle Mart BD. All rights reserved.</p>
                <p>Making waste valuable, naturally. <i class="fa-solid fa-leaf text-green"></i></p>
            </div>
        </div>
    </footer>

    <script src="assets/js/main.js"></script>
    <script src="assets/js/multi-step.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const sellForm = document.getElementById('sellWasteForm');
            
            if (sellForm) {
                sellForm.addEventListener('submit', function(e) {
                    e.preventDefault(); 

                    const submitBtn = document.getElementById('submitListingBtn');
                    const originalBtnText = submitBtn.innerHTML;
                    submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Submitting...';
                    submitBtn.disabled = true;

                    const formData = new FormData();
                    formData.append('title', document.getElementById('listingTitle').value);
                    formData.append('category', document.getElementById('listingCategory').value);
                    formData.append('price', document.getElementById('listingPrice').value);
                    formData.append('unit', document.getElementById('listingUnit').value);
                    formData.append('name', document.getElementById('sellerName').value);
                    formData.append('phone', document.getElementById('sellerPhone').value);
                    formData.append('location', document.getElementById('sellerLocation').value);

                    const fileInput = document.getElementById('fileInput');
                    if (fileInput && fileInput.files.length > 0) {
                        for (let i = 0; i < fileInput.files.length; i++) {
                            formData.append('photos[]', fileInput.files[i]);
                        }
                    }

                    fetch('process-sell-waste.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            document.getElementById('step1').classList.remove('active');
                            document.getElementById('step2').classList.remove('active');
                            document.getElementById('step3').classList.remove('active');
                            document.querySelector('.progress-wrapper').style.display = 'none';
                            
                            document.getElementById('listingSuccessMessage').classList.remove('hidden');
                        } else {
                            alert("Error: " + data.message);
                            submitBtn.innerHTML = originalBtnText;
                            submitBtn.disabled = false;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Something went wrong. Please try again later.');
                        submitBtn.innerHTML = originalBtnText;
                        submitBtn.disabled = false;
                    });
                });
            }
        });
    </script>
</body>
</html>