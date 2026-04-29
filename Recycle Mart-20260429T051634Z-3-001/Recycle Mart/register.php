<?php
require 'db_connect.php';
session_start();

// If the user is already logged in, redirect them to the dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: my-account.php");
    exit;
}

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Basic Validation
    if ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {
        // Check if email already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = "This email is already registered.";
        } else {
            // Securely hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert into Database
            try {
                $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, password) VALUES (?, ?, ?, ?)");
                $stmt->execute([$first_name, $last_name, $email, $hashed_password]);
                $success = "Registration successful! You can now login.";
            } catch (PDOException $e) {
                $error = "Registration failed. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Join Recycle Mart | User Registration</title>
    <link rel="icon" type="image/png" href="assets/images/ui/Recycle-Mart-logo-fav.png">
    
    <link rel="stylesheet" href="assets/css/global.css">
    <link rel="stylesheet" href="assets/css/forms.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        .auth-container { max-width: 500px; margin: 80px auto; padding: 40px; background: white; border-radius: 12px; box-shadow: var(--shadow-md); border: 1px solid var(--border-color); }
        .alert { padding: 15px; border-radius: 8px; margin-bottom: 20px; font-size: 0.9rem; font-weight: 600; }
        .alert-danger { background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; }
        .alert-success { background: #dcfce7; color: #16a34a; border: 1px solid #bbf7d0; }
        
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
                <a href="/"><img src="assets/images/ui/Recycle-Mart-Logo.png" alt="Logo" style="height: 40px;"></a>
                <h2 style="margin-top: 20px;">Create an Account</h2>
                <p class="text-muted">Join the sustainable marketplace today.</p>
            </div>

            <?php if($error): ?> <div class="alert alert-danger"><?= $error ?></div> <?php endif; ?>
            <?php if($success): ?> <div class="alert alert-success"><?= $success ?></div> <?php endif; ?>

            <form action="register.php" method="POST">
                <div class="form-row">
                    <div class="input-group">
                        <label>First Name</label>
                        <input type="text" name="first_name" required placeholder="John">
                    </div>
                    <div class="input-group">
                        <label>Last Name</label>
                        <input type="text" name="last_name" required placeholder="Doe">
                    </div>
                </div>
                <div class="input-group">
                    <label>Email Address</label>
                    <input type="email" name="email" required placeholder="john@example.com">
                </div>
                <div class="input-group">
                    <label>Password</label>
                    <input type="password" name="password" required placeholder="••••••••">
                </div>
                <div class="input-group">
                    <label>Confirm Password</label>
                    <input type="password" name="confirm_password" required placeholder="••••••••">
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%; height: 50px; font-size: 1rem;">Create Account</button>
            </form>
            
            <p class="text-center" style="margin-top: 25px; font-size: 0.95rem; color: var(--text-muted);">
                Already have an account? <a href="login.php" class="text-green" style="font-weight: 700;">Login Here</a>
            </p>
        </div>
    </div>

</body>
</html>