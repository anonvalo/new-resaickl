<?php
session_start();
require 'db_connect.php';

// If not logged in, kick them to the login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$success_msg = "";
$error_msg = "";

// --- 1. Handle Profile Picture Upload ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['profile_pic'])) {
    $file = $_FILES['profile_pic'];
    
    if ($file['error'] === UPLOAD_ERR_OK) {
        $max_size = 500 * 1024; // 500KB in bytes
        if ($file['size'] <= $max_size) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
            if (in_array($file['type'], $allowed_types)) {
                
                // Create a unique filename and save to assets
                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $filename = 'user_' . $user_id . '_' . time() . '.' . $ext;
                $upload_dir = 'assets/images/'; // Saving to assets/images
                $filepath = $upload_dir . $filename;
                
                if (move_uploaded_file($file['tmp_name'], $filepath)) {
                    // Update Database
                    $stmt = $pdo->prepare("UPDATE users SET profile_pic = ? WHERE id = ?");
                    $stmt->execute([$filepath, $user_id]);
                    $success_msg = "Profile picture updated successfully!";
                } else {
                    $error_msg = "Failed to save the uploaded file to the server.";
                }
            } else {
                $error_msg = "Invalid file type. Please upload a JPG, PNG, or WEBP.";
            }
        } else {
            $error_msg = "Image is too large. Please upload an image smaller than 500KB.";
        }
    } else {
        $error_msg = "There was an error processing your upload.";
    }
}

// --- 2. Handle Profile Text Updates ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);

    try {
        $stmt = $pdo->prepare("UPDATE users SET first_name = ?, last_name = ?, phone = ?, address = ? WHERE id = ?");
        $stmt->execute([$first_name, $last_name, $phone, $address, $user_id]);
        $_SESSION['first_name'] = $first_name; // Update session name so header changes
        $success_msg = "Profile details updated successfully!";
    } catch (PDOException $e) {
        $error_msg = "Error updating profile. Please try again.";
    }
}

// --- 3. Fetch Fresh Data ---
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Fetch Their Orders
try {
    $order_stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC");
    $order_stmt->execute([$user_id]);
    $orders = $order_stmt->fetchAll();
} catch (PDOException $e) {
    $orders = []; // If table doesn't exist yet, just return empty array
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account | Recycle Mart BD</title>
    <link rel="icon" type="image/png" href="assets/images/ui/Recycle-Mart-logo-fav.png">
    
    <link rel="stylesheet" href="assets/css/global.css">
    <link rel="stylesheet" href="assets/css/forms.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* FULL HEADER CSS FIXES */
        .auth-actions { display: flex; align-items: center; gap: 16px; margin-right: 16px; padding-right: 16px; border-right: 2px solid var(--border-color); }
        .site-header { position: relative; z-index: 999999 !important; background: white; border-bottom: 1px solid var(--border-color); }
        .mobile-overlay { z-index: 999900 !important; }
        .main-nav.active { background-color: #ffffff !important; backdrop-filter: none !important; -webkit-backdrop-filter: none !important; filter: none !important; box-shadow: -5px 0 25px rgba(0,0,0,0.15) !important; }

        @media (max-width: 768px) {
            .auth-actions { gap: 8px; margin-right: 0px !important; padding-right: 8px !important; border-right: none !important; }
            .auth-text { display: none !important; }
            .header-actions { gap: 12px !important; }
            .site-header .main-logo { max-height: 32px !important; width: auto; }
            .header-inner { padding-left: 12px !important; padding-right: 12px !important; }
        }

        /* DASHBOARD LAYOUT */
        body { background-color: #f8fafc; }
        .dashboard-wrapper { display: flex; gap: 30px; max-width: 1200px; margin: 60px auto; padding: 0 20px; }
        
        /* 30% Left Column */
        .dash-sidebar { 
            width: 30%; 
            background: linear-gradient(135deg, #064e3b, #16a34a);
            border-radius: 16px; 
            color: white; 
            padding: 40px 30px; 
            box-shadow: 0 10px 25px rgba(22, 163, 74, 0.2); 
            height: fit-content;
        }
        .user-intro { text-align: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid rgba(255,255,255,0.2); }
        
        /* Perfect Circle 1:1 Image Upload UI */
        .profile-pic-wrapper {
            position: relative;
            width: 110px;
            height: 110px;
            margin: 0 auto 15px auto; /* This strictly centers the image! */
            border-radius: 50%;
            border: 4px solid white;
            overflow: hidden;
            cursor: pointer;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }
        .profile-pic-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover; /* Forces perfect 1:1 cropping */
            display: block;
        }
        .profile-pic-overlay {
            position: absolute; top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.6);
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            color: white;
            opacity: 0; transition: opacity 0.3s ease;
        }
        .profile-pic-wrapper:hover .profile-pic-overlay { opacity: 1; }

        .user-intro h3 { margin: 0 0 5px 0; font-size: 1.4rem; color: white; }
        .user-intro p { margin: 0; color: #dcfce7; font-size: 0.9rem; }
        
        .dash-nav { list-style: none; padding: 0; margin: 0; }
        .dash-nav li { margin-bottom: 10px; }
        .dash-nav a { 
            display: flex; align-items: center; padding: 12px 20px; color: white; text-decoration: none; 
            border-radius: 8px; font-weight: 600; transition: all 0.3s ease; cursor: pointer;
        }
        .dash-nav a i { margin-right: 12px; font-size: 1.1rem; width: 20px; text-align: center; }
        .dash-nav a:hover, .dash-nav a.active { background: rgba(255,255,255,0.15); transform: translateX(5px); }
        
        /* 70% Right Column */
        .dash-content { 
            width: 70%; 
            background: white; 
            border-radius: 16px; 
            padding: 40px; 
            box-shadow: 0 4px 20px rgba(0,0,0,0.03); 
            border: 1px solid var(--border-color); 
        }
        .dash-content h2 { margin-top: 0; margin-bottom: 30px; color: #1f2937; border-bottom: 2px solid #f1f5f9; padding-bottom: 15px; }
        
        .tab-content { display: none; }
        .tab-content.active { display: block; animation: fadeIn 0.4s ease; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        .form-row { display: flex; gap: 20px; margin-bottom: 20px; }
        .input-group { width: 100%; text-align: left; }
        .input-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #374151; font-size: 0.9rem; }
        .input-group input, .input-group textarea { width: 100%; padding: 14px 16px; border: 1px solid #d1d5db; border-radius: 8px; font-family: inherit; font-size: 1rem; }
        
        .alert { padding: 15px; border-radius: 8px; margin-bottom: 25px; font-weight: 600; }
        .alert-success { background: #dcfce7; color: #16a34a; border: 1px solid #bbf7d0; }
        .alert-danger { background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; }

        /* Order Cards */
        .order-card { border: 1px solid #e2e8f0; border-radius: 10px; padding: 20px; margin-bottom: 20px; background: #fafafa; }
        .order-header { display: flex; justify-content: space-between; border-bottom: 1px solid #e2e8f0; padding-bottom: 12px; margin-bottom: 15px; }
        .order-status { font-weight: 700; color: #16a34a; padding: 4px 12px; background: #dcfce7; border-radius: 20px; font-size: 0.85rem; }

        @media (max-width: 768px) {
            .dashboard-wrapper { flex-direction: column; margin: 30px auto; }
            .dash-sidebar, .dash-content { width: 100%; }
            .form-row { flex-direction: column; gap: 0; }
        }
    </style>
</head>
<body>

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
                <div class="auth-actions">
                    <a href="logout.php" style="color: #ef4444; font-size: 1.1rem; margin-right: 8px;" title="Logout">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i>
                    </a>
                    <a href="my-account.php" style="font-weight: 700; font-size: 0.95rem; color: var(--text-main); display: flex; align-items: center; text-decoration: none;">
                        <i class="fa-solid fa-circle-user text-green" style="margin-right: 6px; font-size: 1.2rem;"></i> 
                        <span class="auth-text" style="color: var(--text-main);">Hi, <?= htmlspecialchars($_SESSION['first_name']) ?></span>
                    </a>
                </div>

                <button class="cart-btn" aria-label="Cart" id="header-cart-btn">
                    <i class="fa-solid fa-cart-shopping"></i>
                </button>
                <a href="sell-waste.php" class="btn btn-primary desktop-sell-btn">Sell Your Waste <i class="fa-solid fa-arrow-right" style="font-size: 0.8rem; margin-left: 4px;"></i></a>
                <button class="mobile-toggle" aria-label="Open Menu"><i class="fa-solid fa-bars-staggered"></i></button>
            </div>
        </div>
    </header>

    <div class="dashboard-wrapper">
        
        <aside class="dash-sidebar">
            <div class="user-intro">
                <form id="avatarForm" action="my-account.php" method="POST" enctype="multipart/form-data">
                    <div class="profile-pic-wrapper" onclick="document.getElementById('profilePicInput').click();" title="Upload up to 500KB">
                        <img src="<?= htmlspecialchars($user['profile_pic'] ?? 'assets/images/ui/default-avatar.png') ?>" alt="Profile">
                        <div class="profile-pic-overlay">
                            <i class="fa-solid fa-pen"></i>
                            <span style="font-size: 0.8rem; margin-top: 4px; font-weight: bold;">Edit Photo</span>
                        </div>
                    </div>
                    <input type="file" id="profilePicInput" name="profile_pic" accept="image/jpeg, image/png, image/webp" style="display: none;" onchange="document.getElementById('avatarForm').submit();">
                </form>
                
                <h3><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></h3>
                <p>Member since 2026</p>
            </div>
            
            <ul class="dash-nav">
                <li><a class="tab-link active" data-target="profile-tab"><i class="fa-regular fa-id-badge"></i> My Profile</a></li>
                <li><a class="tab-link" data-target="orders-tab"><i class="fa-solid fa-box-open"></i> My Orders</a></li>
                <li><a href="/"><i class="fa-solid fa-globe"></i> Visit Site</a></li>
                <li style="margin-top: 30px;"><a href="logout.php" style="color: #fca5a5;"><i class="fa-solid fa-arrow-right-from-bracket"></i> Log Out</a></li>
            </ul>
        </aside>

        <div class="dash-content">
            
            <?php if($success_msg): ?> <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> <?= $success_msg ?></div> <?php endif; ?>
            <?php if($error_msg): ?> <div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation"></i> <?= $error_msg ?></div> <?php endif; ?>

            <div id="profile-tab" class="tab-content active">
                <h2>Account Details</h2>
                <form action="my-account.php" method="POST">
                    <input type="hidden" name="update_profile" value="1">
                    
                    <div class="form-row">
                        <div class="input-group">
                            <label>First Name</label>
                            <input type="text" name="first_name" value="<?= htmlspecialchars($user['first_name']) ?>" required>
                        </div>
                        <div class="input-group">
                            <label>Last Name</label>
                            <input type="text" name="last_name" value="<?= htmlspecialchars($user['last_name']) ?>" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="input-group">
                            <label>Email Address <span style="color: red; font-size: 0.8rem;">(Cannot be changed)</span></label>
                            <input type="email" value="<?= htmlspecialchars($user['email']) ?>" readonly style="background: #f1f5f9; color: #64748b; cursor: not-allowed;">
                        </div>
                        <div class="input-group">
                            <label>Phone Number</label>
                            <input type="text" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" placeholder="+880 1xxxxxxxxx">
                        </div>
                    </div>

                    <div class="input-group" style="margin-bottom: 25px;">
                        <label>Full Address</label>
                        <textarea name="address" rows="3" placeholder="Enter your full street address and city..."><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary" style="padding: 14px 30px; font-size: 1rem;"><i class="fa-solid fa-floppy-disk" style="margin-right: 8px;"></i> Save Changes</button>
                </form>
            </div>

            <div id="orders-tab" class="tab-content">
                <h2>My Orders</h2>
                
                <?php if (empty($orders)): ?>
                    <div class="empty-state text-center" style="padding: 60px 20px; background: #f8fafc; border-radius: 12px; border: 2px dashed #e2e8f0;">
                        <i class="fa-solid fa-box-open" style="font-size: 4rem; color: #cbd5e1; margin-bottom: 20px;"></i>
                        <h3 style="color: #475569; margin-bottom: 10px;">You haven't placed any orders yet.</h3>
                        <p style="color: #94a3b8; margin-bottom: 25px;">Ready to start turning waste into worth? Find great deals on reusable materials.</p>
                        <a href="/#featured-listings" class="btn btn-primary">Shop Now <i class="fa-solid fa-arrow-right" style="margin-left: 8px;"></i></a>
                    </div>
                <?php else: ?>
                    <?php foreach($orders as $order): ?>
                        <div class="order-card">
                            <div class="order-header">
                                <div>
                                    <strong style="font-size: 1.1rem;">Order #<?= str_pad($order['id'], 5, '0', STR_PAD_LEFT) ?></strong>
                                    <div style="font-size: 0.85rem; color: #64748b; margin-top: 4px;"><i class="fa-regular fa-clock"></i> Placed on <?= date('F j, Y', strtotime($order['created_at'] ?? 'now')) ?></div>
                                </div>
                                <div>
                                    <span class="order-status"><?= htmlspecialchars($order['status']) ?></span>
                                </div>
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <div>
                                    <span style="color: #64748b; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px;">Total Amount</span>
                                    <div style="font-weight: 800; font-size: 1.2rem; color: #1f2937;">Tk. <?= number_format($order['total_amount'], 2) ?></div>
                                </div>
                                <button class="btn btn-outline" style="padding: 8px 16px; font-size: 0.9rem;">View Invoice</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

        </div>
    </div>

    <script>
        document.querySelectorAll('.tab-link').forEach(link => {
            link.addEventListener('click', function() {
                // Remove active from all nav links
                document.querySelectorAll('.tab-link').forEach(l => l.classList.remove('active'));
                // Add active to clicked link
                this.classList.add('active');
                
                // Hide all tab contents
                document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
                
                // Show the target tab content
                const targetId = this.getAttribute('data-target');
                document.getElementById(targetId).classList.add('active');
            });
        });
    </script>
    <script src="assets/js/main.js"></script>
</body>
</html>