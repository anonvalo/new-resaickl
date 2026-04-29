<?php
// 1. Start the Session FIRST (Crucial for the Login Header to work)
session_start();

// 2. Connect to the Hostinger MySQL Database
require 'db_connect.php';

// 3. Fetch the products from the database for the Featured Listings section
try {
    $stmt = $pdo->query("SELECT * FROM products ORDER BY id ASC LIMIT 5");
    $featured_products = $stmt->fetchAll();
} catch(PDOException $e) {
    echo "Error fetching products: " . $e->getMessage();
    $featured_products = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recycle Mart BD | Turn Waste into Worth</title>
    
    <link rel="icon" type="image/png" href="assets/images/ui/Recycle-Mart-logo-fav.png">
    
    <link rel="stylesheet" href="assets/css/global.css">
    <link rel="stylesheet" href="assets/css/home.css">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* Mobile Header Auth Actions Fix */
        .auth-actions {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-right: 16px;
            padding-right: 16px;
            border-right: 2px solid var(--border-color);
        }
        
        /* 100% BULLETPROOF FIX: The Stacking Context Override */
        .site-header {
            position: relative; 
            z-index: 999999 !important; /* Pulls the entire header ABOVE the overlay */
        }
        
        .mobile-overlay {
            z-index: 999900 !important; /* Forces the blurred overlay UNDERneath the header */
        }
        
        .main-nav.active {
            background-color: #ffffff !important; /* Forces solid white background */
            backdrop-filter: none !important;
            -webkit-backdrop-filter: none !important;
            filter: none !important;
            box-shadow: -5px 0 25px rgba(0,0,0,0.15) !important;
        }

        /* Mobile Adjustments for Clashing Header Icons */
        @media (max-width: 768px) {
            .auth-actions {
                gap: 8px;
                margin-right: 0px !important;
                padding-right: 8px !important;
                border-right: none !important; /* Removes the vertical line to save space */
            }
            .auth-text {
                display: none !important; /* Hides the text on mobile so the header doesn't break! */
            }
            .header-actions {
                gap: 12px !important; /* Tightens the grouping of the icons on the right */
            }
            .site-header .main-logo {
                max-height: 32px !important; /* Slightly shrinks logo to prevent clashing */
                width: auto;
            }
            .header-inner {
                padding-left: 12px !important; /* Moves logo slightly closer to the edge */
                padding-right: 12px !important;
            }
        }
    </style>
</head>
<body>

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
                    <li><a href="/" class="active">Home</a></li>
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

    <main>
        <section class="hero">
            <div class="container hero-inner">
                <div class="hero-content">
                    <h1>Turn Waste<br>into <span class="text-green">Worth</span></h1>
                    <p>Recycle mart is a marketplace for reusable materials. List your waste, find great deals, and give materials a second life.</p>
                    
                    <div class="hero-buttons">
                        <a href="sell-waste.php" class="btn btn-primary">Sell Your Waste <i class="fa-solid fa-arrow-right" style="font-size: 0.8rem; margin-left: 4px;"></i></a>
                        <a href="listings.php" class="btn btn-outline">
                            <i class="fa-solid fa-cart-plus"></i>
                            Shop Materials
                        </a>
                    </div>

                    <div class="trust-badges">
                        <div class="badge">
                            <i class="fa-solid fa-recycle badge-icon"></i>
                            <div>
                                <strong>Eco Friendly</strong>
                                <span>Sustainable Future</span>
                            </div>
                        </div>
                        <div class="badge">
                            <i class="fa-solid fa-shield-halved badge-icon"></i>
                            <div>
                                <strong>Trusted Community</strong>
                                <span>Verified Sellers</span>
                            </div>
                        </div>
                        <div class="badge">
                            <i class="fa-solid fa-seedling badge-icon"></i>
                            <div>
                                <strong>Better Tomorrow</strong>
                                <span>Less Waste, More Value</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="about-us" class="about-us">
            <div class="container">
                <div class="about-grid">
                    <div class="about-image-wrapper">
                        <div class="about-image-decoration"></div>
                        <img src="assets/images/hero/recycle-mart-about-us.png" alt="About Recycle Mart BD" class="about-img">
                        <div class="floating-badge">
                            <i class="fa-solid fa-leaf text-green"></i>
                            <div>
                                <strong>100%</strong>
                                <span>Sustainable</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="about-content">
                        <span class="badge-label">ABOUT US</span>
                        <h2>Pioneering a <span class="text-green">Sustainable</span> Marketplace</h2>
                        <p>Recycle Mart BD is more than just a platform; it's a movement towards a greener, circular economy. We connect individuals and businesses who have reusable materials with those who need them, preventing valuable resources from ending up in landfills.</p>
                        <p>Our mission is to make recycling profitable, accessible, and transparent for everyone across Bangladesh.</p>
                        
                        <ul class="about-features">
                            <li>
                                <i class="fa-solid fa-circle-check"></i> 
                                <span><strong>Verified Sellers:</strong> Ensuring trust and high-quality materials.</span>
                            </li>
                            <li>
                                <i class="fa-solid fa-circle-check"></i> 
                                <span><strong>Secure Transactions:</strong> Safe and seamless payment processing.</span>
                            </li>
                            <li>
                                <i class="fa-solid fa-circle-check"></i> 
                                <span><strong>Environmental Impact:</strong> Every transaction helps reduce carbon footprints.</span>
                            </li>
                        </ul>

                        <a href="/#how-it-works" class="btn btn-primary mt-4">Discover Our Journey <i class="fa-solid fa-arrow-right" style="margin-left: 8px;"></i></a>
                    </div>
                </div>
            </div>
        </section>

        <section id="how-it-works" class="how-it-works">
            <div class="container">
                <div class="section-header text-center">
                    <span class="badge-label">HOW IT WORKS</span>
                    <h2>Simple Steps, <span class="text-green">Big Impact</span></h2>
                    <p>Recycle. Reuse. Repeat in 3 easy steps.</p>
                </div>

                <div class="steps-wrapper">
                    <div class="step-card">
                        <div class="step-number">01</div>
                        <div class="step-icon"><i class="fa-solid fa-clipboard-list" style="font-size: 28px; color: var(--primary-green);"></i></div>
                        <h3>List Your Waste</h3>
                        <p>Upload details and photos of the materials you want to sell.</p>
                    </div>
                    
                    <div class="step-card">
                        <div class="step-number">02</div>
                        <div class="step-icon"><i class="fa-solid fa-magnifying-glass" style="font-size: 28px; color: var(--primary-green);"></i></div>
                        <h3>Buy or Connect</h3>
                        <p>Buyers find what they need or connect with you for bulk deals.</p>
                    </div>
                    
                    <div class="step-card">
                        <div class="step-number">03</div>
                        <div class="step-icon"><i class="fa-solid fa-handshake" style="font-size: 28px; color: var(--primary-green);"></i></div>
                        <h3>Recycle & Earn</h3>
                        <p>Materials get reused, you earn, and the planet benefits.</p>
                    </div>
                </div>
            </div>
        </section>

        <section id="categories" class="categories">
            <div class="container">
                <div class="section-header text-center">
                    <span class="badge-label"><i class="fa-solid fa-leaf" style="margin-right:4px;"></i> CATEGORIES</span>
                    <h2>Explore <span class="text-green">Material</span> Categories</h2>
                    <p>Find a wide range of reusable materials and give them a new life.</p>
                </div>

                <div class="category-grid">
                    <a href="listings.php" class="cat-card">
                        <div class="cat-img-wrapper">
                            <img src="assets/images/categories/rm-cate-paper-cardboards.png" alt="Paper & Cardboard">
                        </div>
                        <div class="cat-content">
                            <h3>Paper & Cardboard</h3>
                            <span>1,242 Listings</span>
                        </div>
                    </a>
                    <a href="listings.php" class="cat-card">
                        <div class="cat-img-wrapper">
                            <img src="assets/images/categories/rm-cate-plastics.png" alt="Plastics">
                        </div>
                        <div class="cat-content">
                            <h3>Plastics</h3>
                            <span>1,032 Listings</span>
                        </div>
                    </a>
                    <a href="listings.php" class="cat-card">
                        <div class="cat-img-wrapper">
                            <img src="assets/images/categories/rm-cate-metals.png" alt="Metals">
                        </div>
                        <div class="cat-content">
                            <h3>Metals</h3>
                            <span>542 Listings</span>
                        </div>
                    </a>
                    <a href="listings.php" class="cat-card">
                        <div class="cat-img-wrapper">
                            <img src="assets/images/categories/rm-cate-electronics.png" alt="Electronics">
                        </div>
                        <div class="cat-content">
                            <h3>Electronics</h3>
                            <span>527 Listings</span>
                        </div>
                    </a>
                    <a href="listings.php" class="cat-card">
                        <div class="cat-img-wrapper">
                            <img src="assets/images/categories/rm-cate-wood.png" alt="Wood">
                        </div>
                        <div class="cat-content">
                            <h3>Wood</h3>
                            <span>422 Listings</span>
                        </div>
                    </a>
                    <a href="listings.php" class="cat-card">
                        <div class="cat-img-wrapper">
                            <img src="assets/images/categories/rm-cate-glass.png" alt="Glass">
                        </div>
                        <div class="cat-content">
                            <h3>Glass</h3>
                            <span>321 Listings</span>
                        </div>
                    </a>
                </div>
                
                <div class="text-center mt-4">
                    <a href="listings.php" class="btn btn-primary">View All Categories <i class="fa-solid fa-arrow-right" style="margin-left: 4px;"></i></a>
                </div>
            </div>
        </section>

        <section id="featured-listings" class="featured-listings">
            <div class="container">
                <div class="section-header split-header">
                    <div>
                        <span class="badge-label">FEATURED LISTINGS</span>
                        <h2>Featured <span class="text-green">Listings</span></h2>
                        <p>Check out some of the top quality materials listed by our community.</p>
                    </div>
                </div>

                <div class="product-grid">
                    
                    <?php if (!empty($featured_products)): ?>
                        <?php foreach ($featured_products as $product): ?>
                            
                            <article class="product-card">
                                <a href="product-details.php?id=<?= htmlspecialchars($product['id']) ?>" class="product-card-link">
                                    <div class="card-image">
                                        
                                        <?php if (!empty($product['tag_text'])): ?>
                                            <span class="tag <?= htmlspecialchars($product['tag_class']) ?>"><?= htmlspecialchars($product['tag_text']) ?></span>
                                        <?php endif; ?>
                                        
                                        <button class="favorite-btn" onclick="event.preventDefault();"><i class="fa-regular fa-heart"></i></button>
                                        <img src="<?= htmlspecialchars($product['main_img']) ?>" alt="<?= htmlspecialchars($product['title']) ?>">
                                    </div>
                                    <div class="card-content">
                                        <h3><?= htmlspecialchars($product['title']) ?></h3>
                                        <span class="condition"><?= htmlspecialchars($product['condition_text']) ?></span>
                                        <div class="price-row">
                                            <span class="price">Tk.<?= number_format($product['price'], 0) ?> <small><?= htmlspecialchars($product['unit']) ?></small></span>
                                        </div>
                                        <div class="location">
                                            <i class="fa-solid fa-location-dot"></i>
                                            <?= htmlspecialchars($product['location']) ?>
                                        </div>
                                    </div>
                                </a>
                                <div class="card-action-box">
                                    <button class="btn btn-outline add-to-cart-btn" 
                                        data-id="<?= htmlspecialchars($product['id']) ?>" 
                                        data-title="<?= htmlspecialchars($product['title']) ?>" 
                                        data-price="<?= htmlspecialchars($product['price']) ?>" 
                                        data-img="<?= htmlspecialchars($product['main_img']) ?>">
                                        Add to Cart <i class="fa-solid fa-cart-plus"></i>
                                    </button>
                                </div>
                            </article>
                            
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No products found in the database yet.</p>
                    <?php endif; ?>
                    </div>
            </div>
        </section>

        <section id="contact-us" class="contact-us">
            <div class="container">
                <div class="contact-grid">
                    <div class="contact-content">
                        <span class="badge-label">CONTACT US</span>
                        <h2>Get In <span class="text-green">Touch</span></h2>
                        <p>Have questions about selling your waste or buying materials? Send us a message and our team will get back to you shortly.</p>
                        
                        <form id="homeContactForm" class="home-contact-form">
                            <div class="form-row">
                                <div class="input-group">
                                    <label>Full Name</label>
                                    <input type="text" id="contactName" placeholder="John Doe" required>
                                </div>
                                <div class="input-group">
                                    <label>Email Address</label>
                                    <input type="email" id="contactEmail" placeholder="john@example.com" required>
                                </div>
                            </div>
                            <div class="input-group">
                                <label>Subject</label>
                                <input type="text" id="contactSubject" placeholder="How can we help?" required>
                            </div>
                            <div class="input-group">
                                <label>Message</label>
                                <textarea id="contactMessage" rows="5" placeholder="Write your message here..." required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary" style="width: 100%;">Send Message <i class="fa-solid fa-paper-plane" style="margin-left: 8px;"></i></button>
                        </form>

                        <div id="contactSuccess" class="contact-success-msg hidden">
                            <i class="fa-solid fa-circle-check"></i>
                            <h3>Message Sent Successfully!</h3>
                            <p>Thank you for reaching out. We have sent a notification to our team and will reply to your email soon.</p>
                            <button type="button" class="btn btn-outline mt-4" id="resetContactForm">Send Another Message</button>
                        </div>
                    </div>

                    <div class="contact-image-wrapper">
                        <div class="contact-image-decoration"></div>
                        <img src="assets/images/hero/recycle-mart-contact-us.png" alt="Contact Recycle Mart BD" class="contact-img">
                        <div class="contact-info-card">
                            <div class="info-item">
                                <div class="info-icon"><i class="fa-solid fa-envelope"></i></div>
                                <div>
                                    <strong>Email Us Directly</strong>
                                    <span>info@recyclemartsbd.com</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <div id="floating-cart" class="floating-cart">
        <i class="fa-solid fa-cart-shopping"></i>
        <span id="cart-badge-count">0</span>
    </div>

    <div id="cart-drawer-overlay" class="cart-drawer-overlay"></div>

    <div id="cart-drawer" class="cart-drawer">
        <div class="drawer-header">
            <h3><i class="fa-solid fa-cart-shopping"></i> Your Cart</h3>
            <button id="close-drawer" class="close-drawer-btn"><i class="fa-solid fa-xmark"></i></button>
        </div>
        
        <div id="drawer-items" class="drawer-items"></div>
        
        <div class="drawer-footer">
            <div class="drawer-subtotal">
                <span>Subtotal:</span>
                <span>Tk. <span id="cart-subtotal-amount">0.00</span></span>
            </div>
            <p class="tax-note">Shipping & taxes calculated at checkout.</p>
            <a href="checkout.php" class="btn btn-primary" style="width: 100%; justify-content: center;">Proceed to Checkout</a>
            <button id="continue-shopping-btn" class="btn btn-outline" style="width: 100%; justify-content: center; margin-top: 12px;">Continue Shopping</button>
        </div>
    </div>

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
    
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const contactForm = document.getElementById('homeContactForm');
            const successMsg = document.getElementById('contactSuccess');
            const resetBtn = document.getElementById('resetContactForm');

            if (contactForm) {
                contactForm.addEventListener('submit', function(e) {
                    e.preventDefault(); 

                    const submitBtn = this.querySelector('button[type="submit"]');
                    const originalBtnText = submitBtn.innerHTML;
                    submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Sending...';
                    submitBtn.disabled = true;

                    const payload = {
                        name: document.getElementById('contactName').value,
                        email: document.getElementById('contactEmail').value,
                        subject: document.getElementById('contactSubject').value,
                        message: document.getElementById('contactMessage').value
                    };

                    fetch('process-contact.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(payload)
                    })
                    .then(response => response.json())
                    .then(result => {
                        if (result.status === 'success') {
                            contactForm.style.display = 'none';
                            successMsg.classList.remove('hidden');
                        } else {
                            alert(result.message);
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

            if (resetBtn) {
                resetBtn.addEventListener('click', () => {
                    contactForm.reset();
                    successMsg.classList.add('hidden');
                    contactForm.style.display = 'block';
                    
                    const submitBtn = contactForm.querySelector('button[type="submit"]');
                    submitBtn.innerHTML = 'Send Message <i class="fa-solid fa-paper-plane" style="margin-left: 8px;"></i>';
                    submitBtn.disabled = false;
                });
            }
        });
    </script>
</body>
</html>