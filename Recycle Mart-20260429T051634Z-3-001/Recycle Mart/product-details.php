<?php
// 1. Start the Session FIRST
session_start();

// 2. Connect to the database
require 'db_connect.php';

// 3. Safely get the Product ID from the URL
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// 4. Fetch the main product details
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

// 5. If the product isn't found in the database, redirect to homepage or show an error
if (!$product) {
    echo "<h1>Product Not Found</h1><a href='/'>Return to Homepage</a>";
    exit;
}

// 6. Fetch the gallery images for this specific product
$gallery_stmt = $pdo->prepare("SELECT img_url FROM product_gallery WHERE product_id = ?");
$gallery_stmt->execute([$product_id]);
$gallery_images = $gallery_stmt->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['title']) ?> | Recycle Mart BD</title>
    
    <link rel="icon" type="image/png" href="assets/images/ui/Recycle-Mart-logo-fav.png">
    
    <link rel="stylesheet" href="assets/css/global.css">
    <link rel="stylesheet" href="assets/css/product.css">
    
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
        
        .site-header {
            position: relative; 
            z-index: 999999 !important; 
        }
        
        .mobile-overlay {
            z-index: 999900 !important; 
        }
        
        .main-nav.active {
            background-color: #ffffff !important; 
            backdrop-filter: none !important;
            -webkit-backdrop-filter: none !important;
            filter: none !important;
            box-shadow: -5px 0 25px rgba(0,0,0,0.15) !important;
        }

        @media (max-width: 768px) {
            .auth-actions {
                gap: 8px;
                margin-right: 0px !important;
                padding-right: 8px !important;
                border-right: none !important; 
            }
            .auth-text {
                display: none !important; 
            }
            .header-actions {
                gap: 12px !important; 
            }
            .site-header .main-logo {
                max-height: 32px !important; 
                width: auto;
            }
            .header-inner {
                padding-left: 12px !important; 
                padding-right: 12px !important;
            }
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

    <div class="breadcrumbs container">
        <a href="/">Home</a> <i class="fa-solid fa-angle-right" style="font-size: 0.8rem; margin: 0 8px; color: var(--text-muted);"></i> 
        <a href="listings.php">Listings</a> <i class="fa-solid fa-angle-right" style="font-size: 0.8rem; margin: 0 8px; color: var(--text-muted);"></i> 
        <span id="detail-breadcrumb-title"><?= htmlspecialchars($product['title']) ?></span>
    </div>

    <main class="container product-container">
        
        <div class="product-gallery">
            <div class="main-image" id="img-zoom-container">
                <?php if (!empty($product['tag_text'])): ?>
                    <span class="tag <?= htmlspecialchars($product['tag_class']) ?>" id="detail-tag"><?= htmlspecialchars($product['tag_text']) ?></span>
                <?php endif; ?>
                <img src="<?= htmlspecialchars($product['main_img']) ?>" alt="<?= htmlspecialchars($product['title']) ?>" id="detail-main-img">
            </div>
            
            <div class="thumbnail-row" id="detail-thumbnails">
                <?php foreach($gallery_images as $index => $img): ?>
                    <img src="<?= htmlspecialchars($img) ?>" class="thumb <?= $index === 0 ? 'active' : '' ?>" alt="Gallery Image">
                <?php endforeach; ?>
            </div>
        </div>

        <div class="product-info">
            <h1 id="detail-title"><?= htmlspecialchars($product['title']) ?></h1>
            <div class="product-meta">
                <span class="condition badge-label" id="detail-condition"><?= htmlspecialchars($product['condition_text']) ?></span>
                <span class="location">
                    <i class="fa-solid fa-location-dot"></i>
                    <span id="detail-location-text"><?= htmlspecialchars($product['location']) ?></span>
                </span>
            </div>

            <div class="product-price">
                Tk. <span id="detail-price"><?= number_format($product['price'], 2) ?></span> <span class="unit" id="detail-unit"><?= htmlspecialchars($product['unit']) ?></span>
            </div>
            
            <p style="margin-top: 10px; font-weight: 600; color: <?= $product['stock_qty'] > 0 ? 'var(--primary-green)' : '#ef4444' ?>;">
                <i class="fa-solid <?= $product['stock_qty'] > 0 ? 'fa-box-open' : 'fa-ban' ?>"></i>
                <?= $product['stock_qty'] > 0 ? $product['stock_qty'] . ' Pcs Available' : 'Out of Stock' ?>
            </p>

            <div class="product-description" style="margin-top: 24px;">
                <h3>Description</h3>
                <p id="detail-desc"><?= nl2br(htmlspecialchars($product['description'])) ?></p>
            </div>

            <div class="purchase-actions" style="display: flex; align-items: center; margin-top: 24px;">
                <div class="quantity-selector">
                    <button type="button" class="qty-btn minus" id="detail-qty-minus"><i class="fa-solid fa-minus"></i></button>
                    <input type="number" value="1" min="1" max="<?= $product['stock_qty'] ?>" class="qty-input" id="detail-qty-input">
                    <button type="button" class="qty-btn plus" id="detail-qty-plus"><i class="fa-solid fa-plus"></i></button>
                </div>
                
                <button class="btn btn-primary add-to-cart-btn" id="detail-add-to-cart" 
                    data-id="<?= htmlspecialchars($product['id']) ?>" 
                    data-title="<?= htmlspecialchars($product['title']) ?>" 
                    data-price="<?= htmlspecialchars($product['price']) ?>" 
                    data-img="<?= htmlspecialchars($product['main_img']) ?>"
                    <?= $product['stock_qty'] <= 0 ? 'disabled style="background: gray; cursor: not-allowed;"' : '' ?>>
                    <?= $product['stock_qty'] > 0 ? 'Add to Cart' : 'Sold Out' ?>
                </button>
            </div>

            <div class="seller-card" style="margin-top: 40px;">
                <div class="seller-header">
                    <div class="seller-avatar">E</div>
                    <div>
                        <h4>Verified Eco-Seller</h4>
                        <p class="verified-badge"><i class="fa-solid fa-circle-check"></i> Trusted Source</p>
                    </div>
                </div>
                <button class="btn btn-outline" style="width: 100%; margin-top: 16px;">Contact Seller</button>
            </div>
        </div>
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
</body>
</html>