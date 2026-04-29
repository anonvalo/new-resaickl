<?php
session_start();
require 'db_connect.php';

// Set header to return JSON for our AJAX request
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Catch the JSON payload from the frontend cart
    $data = json_decode(file_get_contents("php://input"), true);
    
    // Extract customer info
    $first_name = htmlspecialchars(trim($data['first_name'] ?? ''));
    $last_name = htmlspecialchars(trim($data['last_name'] ?? ''));
    $email = filter_var(trim($data['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $address = htmlspecialchars(trim($data['address'] ?? ''));
    $city = htmlspecialchars(trim($data['city'] ?? ''));
    $zip = htmlspecialchars(trim($data['zip'] ?? ''));
    
    // Extract the cart array
    $cart = $data['cart'] ?? [];

    if (empty($first_name) || empty($email) || empty($cart)) {
        echo json_encode(["status" => "error", "message" => "Missing required order information."]);
        exit;
    }

    try {
        // Begin Database Transaction (Lock it down to prevent errors)
        $pdo->beginTransaction();

        // 1. Calculate the real total from the Database (Never trust frontend prices!)
        $total_amount = 200; // Starting with Shipping (150) + Fee (50)
        
        foreach ($cart as $item) {
            // Check stock and get the real price securely
            $stmt = $pdo->prepare("SELECT price, stock_qty, title FROM products WHERE id = ? FOR UPDATE");
            $stmt->execute([$item['id']]);
            $product = $stmt->fetch();

            if (!$product) {
                throw new Exception("Product ID {$item['id']} not found.");
            }

            if ($product['stock_qty'] < $item['quantity']) {
                throw new Exception("Sorry, '{$product['title']}' only has {$product['stock_qty']} left in stock.");
            }

            $total_amount += ($product['price'] * $item['quantity']);
        }

        // 2. Insert into the `orders` table
        $user_id = $_SESSION['user_id'] ?? NULL; // Attach to user if logged in
        
        $order_stmt = $pdo->prepare("INSERT INTO orders (user_id, first_name, last_name, email, address, city, zip, total_amount, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Processing')");
        $order_stmt->execute([$user_id, $first_name, $last_name, $email, $address, $city, $zip, $total_amount]);
        
        // Get the new Order ID
        $order_id = $pdo->lastInsertId();

        // 3. Loop through cart again to deduct stock and save order_items
        foreach ($cart as $item) {
            
            // Get real price again for the order_items receipt
            $stmt = $pdo->prepare("SELECT price FROM products WHERE id = ?");
            $stmt->execute([$item['id']]);
            $real_price = $stmt->fetchColumn();

            // Insert the item record
            $item_stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            $item_stmt->execute([$order_id, $item['id'], $item['quantity'], $real_price]);

            // THE INVENTORY TRIGGER: Deduct the stock quantity!
            $stock_stmt = $pdo->prepare("UPDATE products SET stock_qty = stock_qty - ? WHERE id = ?");
            $stock_stmt->execute([$item['quantity'], $item['id']]);
        }

        // Commit the transaction - Everything succeeded!
        $pdo->commit();
        echo json_encode(["status" => "success", "message" => "Order placed successfully!", "order_id" => $order_id]);

    } catch (Exception $e) {
        // If anything fails (like stock running out), cancel the database changes
        $pdo->rollBack();
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request."]);
}
?>