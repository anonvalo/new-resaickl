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
    <title>Secure Checkout | Recycle Mart BD</title>
    
    <link rel="icon" type="image/png" href="assets/images/ui/Recycle-Mart-logo-fav.png">
    
    <link rel="stylesheet" href="assets/css/global.css">
    <link rel="stylesheet" href="assets/css/forms.css"> 
    <link rel="stylesheet" href="assets/css/checkout.css">
    
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

    <main class="container checkout-container" id="checkout-main-view">
        
        <div class="checkout-forms">
            <h2>Secure Checkout</h2>
            
            <form id="paymentForm">
                <div class="checkout-card">
                    <h3>1. Billing & Shipping Details</h3>
                    <div class="form-row">
                        <div class="input-group">
                            <label>First Name</label>
                            <input type="text" placeholder="John" required>
                        </div>
                        <div class="input-group">
                            <label>Last Name</label>
                            <input type="text" placeholder="Doe" required>
                        </div>
                    </div>
                    
                    <div class="input-group">
                        <label>Email Address</label>
                        <input type="email" placeholder="john@example.com" required>
                    </div>

                    <div class="input-group">
                        <label>Street Address</label>
                        <input type="text" placeholder="123 Recycle Lane" required>
                    </div>

                    <div class="form-row">
                        <div class="input-group">
                            <label>City / District</label>
                            <input type="text" placeholder="Dhaka" required>
                        </div>
                        <div class="input-group">
                            <label>Postal Code</label>
                            <input type="text" placeholder="1000" required>
                        </div>
                    </div>
                </div>

                <div class="checkout-card">
                    <h3>2. Payment Method</h3>
                    <p class="text-muted" style="margin-bottom: 20px; font-size: 0.9rem;">
                        <i class="fa-solid fa-shield-halved" style="color:var(--primary-green);"></i> All transactions are secure and encrypted.<br>
                        <strong>Use Test Card:</strong> <span style="color:#3b82f6;">4242 4242 4242 4242</span>
                    </p>
                    
                    <div class="input-group">
                        <label>Name on Card</label>
                        <input type="text" placeholder="John Doe" required>
                    </div>

                    <div class="input-group">
                        <label>Card Number</label>
                        <div class="card-input-wrapper">
                            <input type="text" id="checkout-cc-num" placeholder="4242 4242 4242 4242" maxlength="19" required>
                            <i class="fa-regular fa-credit-card" style="position: absolute; right: 16px; color: var(--text-muted); font-size: 1.2rem;"></i>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="input-group">
                            <label>Expiry Date</label>
                            <input type="text" placeholder="MM/YY" maxlength="5" required>
                        </div>
                        <div class="input-group">
                            <label>CVV</label>
                            <input type="text" placeholder="123" maxlength="4" required>
                        </div>
                    </div>
                </div>

                <button type="submit" id="process-order-btn" class="btn btn-primary place-order-btn">Place Order Securely</button>
            </form>
        </div>

        <div class="order-summary-wrapper">
            <div class="checkout-card order-summary sticky-summary">
                <h3>Order Summary</h3>
                
                <div class="cart-items" id="checkout-order-items">
                    </div>

                <div class="summary-totals">
                    <div class="total-row">
                        <span>Subtotal</span>
                        <span>Tk. <span id="checkout-subtotal">0.00</span></span>
                    </div>
                    <div class="total-row">
                        <span>Shipping</span>
                        <span>Tk. <span id="checkout-shipping">150.00</span></span>
                    </div>
                    <div class="total-row">
                        <span>Platform Fee</span>
                        <span>Tk. <span id="checkout-fee">50.00</span></span>
                    </div>
                    <div class="total-row grand-total">
                        <span>Total</span>
                        <span class="text-green">Tk. <span id="checkout-total">0.00</span></span>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <main class="container hidden" id="checkout-success-view" style="padding: 100px 24px; max-width: 800px; margin: 0 auto;">
        <div class="checkout-success-msg">
            <i class="fa-solid fa-circle-check"></i>
            <h2>Payment Successful!</h2>
            <p>Thank you for your purchase. Your order has been securely processed and is being prepared.</p>
            <a href="/" class="btn btn-primary mt-4">Return to Homepage</a>
            <a href="/#featured-listings" class="btn btn-outline mt-4" style="margin-left: 12px;">Continue Shopping</a>
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
    <script>
        const ccInput = document.getElementById('checkout-cc-num');
        if (ccInput) {
            ccInput.addEventListener('input', function (e) {
                e.target.value = e.target.value.replace(/[^\d]/g, '').replace(/(.{4})/g, '$1 ').trim();
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            const paymentForm = document.getElementById('paymentForm');
            
            if(paymentForm) {
                paymentForm.addEventListener('submit', function(e) {
                    e.preventDefault(); 
                    
                    const submitBtn = document.getElementById('process-order-btn');
                    const originalBtnText = submitBtn.innerHTML;
                    submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Processing Order...';
                    submitBtn.disabled = true;

                    const cartData = JSON.parse(localStorage.getItem('recycleMartCart')) || [];
                    
                    if(cartData.length === 0) {
                        alert("Your cart is empty!");
                        submitBtn.innerHTML = originalBtnText;
                        submitBtn.disabled = false;
                        return;
                    }

                    const payload = {
                        first_name: this.querySelector('input[placeholder="John"]').value,
                        last_name: this.querySelector('input[placeholder="Doe"]').value,
                        email: this.querySelector('input[type="email"]').value,
                        address: this.querySelector('input[placeholder="123 Recycle Lane"]').value,
                        city: this.querySelector('input[placeholder="Dhaka"]').value,
                        zip: this.querySelector('input[placeholder="1000"]').value,
                        cart: cartData
                    };

                    fetch('process-checkout.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(payload)
                    })
                    .then(res => res.json())
                    .then(data => {
                        if(data.status === 'success') {
                            localStorage.removeItem('recycleMartCart');
                            const cartBadge = document.getElementById('cart-badge-count');
                            if(cartBadge) cartBadge.innerText = '0';
                            
                            document.getElementById('checkout-main-view').classList.add('hidden');
                            document.getElementById('checkout-success-view').classList.remove('hidden');
                            window.scrollTo(0, 0);
                        } else {
                            alert("Order Error: " + data.message); 
                            submitBtn.innerHTML = originalBtnText;
                            submitBtn.disabled = false;
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        alert("An error occurred connecting to the server. Please check your internet connection.");
                        submitBtn.innerHTML = originalBtnText;
                        submitBtn.disabled = false;
                    });
                });
            }
        });
    </script>
</body>
</html>