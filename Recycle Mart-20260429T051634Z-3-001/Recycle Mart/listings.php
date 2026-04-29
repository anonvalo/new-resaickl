<?php
// 1. Start the Session FIRST
session_start();

// 2. Connect to the database
require 'db_connect.php';

// 3. Fetch ALL products from the database
try {
    $stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
    $all_products = $stmt->fetchAll();
} catch(PDOException $e) {
    echo "Error fetching products: " . $e->getMessage();
    $all_products = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Listings | Recycle Mart BD</title>
    
    <link rel="icon" type="image/png" href="assets/images/ui/Recycle-Mart-logo-fav.png">
    
    <link rel="stylesheet" href="assets/css/global.css">
    <link rel="stylesheet" href="assets/css/home.css"> 
    <link rel="stylesheet" href="assets/css/listings.css">
    
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
                    <li><a href="listings.php" class="active">Listings</a></li>
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

                <button class="cart-btn" aria-label="Cart">
                    <i class="fa-solid fa-cart-shopping"></i>
                </button>
                <a href="sell-waste.php" class="btn btn-primary desktop-sell-btn">Sell Your Waste <i class="fa-solid fa-arrow-right" style="font-size: 0.8rem; margin-left: 4px;"></i></a>
                <button class="mobile-toggle" aria-label="Open Menu"><i class="fa-solid fa-bars-staggered"></i></button>
            </div>
        </div>
    </header>

    <div class="page-header text-center">
        <div class="container">
            <h1>Browse <span class="text-green">Materials</span></h1>
            <p>Find high-quality reusable materials for your next project.</p>
        </div>
    </div>

    <main class="container shop-layout">
        <aside class="shop-sidebar">
            <div class="filter-widget">
                <h3>Categories</h3>
                <ul class="filter-list">
                    <li><label><input type="checkbox" checked> All Categories</label> <span>(3,064)</span></li>
                    <li><label><input type="checkbox"> Paper & Cardboard</label> <span>(1,242)</span></li>
                    <li><label><input type="checkbox"> Plastics</label> <span>(1,032)</span></li>
                    <li><label><input type="checkbox"> Metals</label> <span>(542)</span></li>
                    <li><label><input type="checkbox"> Electronics</label> <span>(527)</span></li>
                </ul>
            </div>

            <div class="filter-widget">
                <h3>Condition</h3>
                <ul class="filter-list">
                    <li><label><input type="checkbox"> Brand New</label></li>
                    <li><label><input type="checkbox"> Like New / Reusable</label></li>
                    <li><label><input type="checkbox"> Needs Repair</label></li>
                    <li><label><input type="checkbox"> Scrap / Bulk</label></li>
                </ul>
            </div>

            <div class="filter-widget">
                <h3>Price Range</h3>
                <div class="price-inputs">
                    <input type="number" placeholder="Min Tk.">
                    <span>-</span>
                    <input type="number" placeholder="Max Tk.">
                </div>
                <button class="btn btn-outline apply-filter-btn" style="width: 100%; margin-top: 12px; padding: 8px;">Apply Filter</button>
            </div>
        </aside>

        <div class="shop-content">
            <div class="shop-toolbar flex-between">
                <p>Showing <strong>1-<?= count($all_products) ?></strong> results</p>
                <select class="sort-dropdown">
                    <option>Sort by: Latest</option>
                    <option>Sort by: Price (Low to High)</option>
                    <option>Sort by: Price (High to Low)</option>
                </select>
            </div>

            <div class="product-grid shop-grid">
                <?php if (!empty($all_products)): ?>
                    <?php foreach ($all_products as $product): ?>
                        <article class="product-card">
                            <div class="card-image">
                                <?php if (!empty($product['tag_text'])): ?>
                                    <span class="tag <?= htmlspecialchars($product['tag_class']) ?>"><?= htmlspecialchars($product['tag_text']) ?></span>
                                <?php endif; ?>
                                <button class="favorite-btn"><i class="fa-regular fa-heart"></i></button>
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
                                <a href="product-details.php?id=<?= htmlspecialchars($product['id']) ?>" class="btn btn-primary" style="width:100%; margin-top: 16px;">View Details</a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No materials available right now.</p>
                <?php endif; ?>
            </div>

            <div class="pagination">
                <a href="#" class="page-link active">1</a>
                <a href="#" class="page-link">2</a>
                <a href="#" class="page-link">3</a>
                <span>...</span>
                <a href="#" class="page-link">12</a>
                <a href="#" class="page-link next"><i class="fa-solid fa-arrow-right"></i></a>
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
</body>
</html>