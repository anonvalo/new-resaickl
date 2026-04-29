<?php
require 'db_connect.php';
session_start();

// If the user is already logged in, redirect them to their dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: my-account.php");
    exit;
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Find the user by email
    $stmt = $pdo->prepare("SELECT id, first_name, password FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    // Verify the password securely
    if ($user && password_verify($password, $user['password'])) {
        // Password is correct! Start the session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['first_name'] = $user['first_name'];
        
        // Send them straight to the new Dashboard!
        header("Location: my-account.php");
        exit;
    } else {
        $error = "Invalid email or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login | Recycle Mart BD</title>
    <link rel="icon" type="image/png" href="assets/images/ui/Recycle-Mart-logo-fav.png">
    
    <link rel="stylesheet" href="assets/css/global.css">
    <link rel="stylesheet" href="assets/css/forms.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        .auth-container { max-width: 450px; margin: 80px auto; padding: 40px; background: white; border-radius: 12px; box-shadow: var(--shadow-md); border: 1px solid var(--border-color); }
        .alert { padding: 15px; border-radius: 8px; margin-bottom: 20px; font-size: 0.9rem; font-weight: 600; }
        .alert-danger { background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; }
        
        /* FIX: Forcing all inputs to have the exact same height and padding */
        .auth-container .input-group input {
            width: 100%;
            height: 50px;
            padding: 12px 16px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 1rem;
            font-family: inherit;
            box-sizing: border-box;
            outline: none;
            transition: border-color 0.3s ease;
        }
        .auth-container .input-group input:focus {
            border-color: var(--primary-green);
        }
        .auth-container .input-group {
            margin-bottom: 20px;
            text-align: left;
        }
        .auth-container .input-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #374151;
        }
    </style>
</head>
<body class="bg-light">

    <div class="container">
        <div class="auth-container">
            <div class="text-center" style="margin-bottom: 30px;">
                <a href="index.php"><img src="assets/images/ui/Recycle-Mart-Logo.png" alt="Logo" style="height: 40px;"></a>
                <h2 style="margin-top: 20px;">Welcome Back</h2>
                <p class="text-muted">Login to manage your listings and orders.</p>
            </div>

            <?php if($error): ?> <div class="alert alert-danger"><?= $error ?></div> <?php endif; ?>

            <form action="login.php" method="POST">
                <div class="input-group">
                    <label>Email Address</label>
                    <input type="email" name="email" required placeholder="john@example.com">
                </div>
                <div class="input-group">
                    <label>Password</label>
                    <input type="password" name="password" required placeholder="••••••••">
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%; height: 50px; font-size: 1rem;">Login securely</button>
            </form>
            
            <p class="text-center" style="margin-top: 25px; font-size: 0.95rem; color: var(--text-muted);">
                Don't have an account? <a href="register.php" class="text-green" style="font-weight: 700;">Create one here</a>
            </p>
        </div>
    </div>

</body>
</html>