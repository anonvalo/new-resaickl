<?php
session_start();

// Set header to return JSON for our AJAX request
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Clean and sanitize incoming data from the form
    $title = htmlspecialchars(trim($_POST['title'] ?? 'N/A'));
    $category = htmlspecialchars(trim($_POST['category'] ?? 'N/A'));
    $price = htmlspecialchars(trim($_POST['price'] ?? '0'));
    $unit = htmlspecialchars(trim($_POST['unit'] ?? ''));
    $name = htmlspecialchars(trim($_POST['name'] ?? 'N/A'));
    $phone = htmlspecialchars(trim($_POST['phone'] ?? 'N/A'));
    $location = htmlspecialchars(trim($_POST['location'] ?? 'N/A'));

    // Basic Validation
    if (empty($_POST['title']) || empty($_POST['name']) || empty($_POST['phone'])) {
        echo json_encode(["status" => "error", "message" => "Please fill out all required fields."]);
        exit;
    }

    // --- EMAIL SETUP ---
    $to = "jhannatulhaqueanon@gmail.com";
    $subject = "New Material Listing Submission: " . $title;

    // --- BEAUTIFUL HTML EMAIL TEMPLATE ---
    $html_content = "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <style>
            body { font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f3f4f6; margin: 0; padding: 40px 20px; color: #1f2937; }
            .email-wrapper { max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
            .header { background-color: #16a34a; padding: 30px 40px; text-align: center; }
            .header h1 { margin: 0; color: #ffffff; font-size: 24px; font-weight: 700; letter-spacing: 0.5px; }
            .header p { color: #dcfce7; margin: 8px 0 0 0; font-size: 15px; }
            .body-content { padding: 40px; }
            .intro { font-size: 16px; line-height: 1.6; color: #4b5563; margin-bottom: 30px; }
            .data-box { background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 25px; margin-bottom: 25px; }
            .data-row { margin-bottom: 16px; display: flex; flex-direction: column; }
            .data-row:last-child { margin-bottom: 0; }
            .data-label { font-size: 12px; text-transform: uppercase; letter-spacing: 1px; color: #6b7280; font-weight: 700; margin-bottom: 6px; }
            .data-value { font-size: 16px; color: #111827; font-weight: 500; }
            .price-tag { display: inline-block; background-color: #dcfce7; color: #166534; padding: 4px 12px; border-radius: 20px; font-weight: 700; font-size: 14px; margin-top: 4px; }
            .footer { background-color: #f8fafc; text-align: center; padding: 20px; font-size: 13px; color: #94a3b8; border-top: 1px solid #e2e8f0; }
        </style>
    </head>
    <body>
        <div class='email-wrapper'>
            <div class='header'>
                <h1>New Listing Request</h1>
                <p>Recycle Mart BD Platform</p>
            </div>
            
            <div class='body-content'>
                <p class='intro'>Hello Admin,<br><br>A user has submitted a new material listing for review. Here are the details of the submission:</p>
                
                <div class='data-box'>
                    <div class='data-row'>
                        <span class='data-label'>Material Title</span>
                        <span class='data-value'>{$title}</span>
                    </div>
                    <div class='data-row'>
                        <span class='data-label'>Category</span>
                        <span class='data-value' style='text-transform: capitalize;'>{$category}</span>
                    </div>
                    <div class='data-row'>
                        <span class='data-label'>Expected Price</span>
                        <span class='price-tag'>Tk. {$price} / {$unit}</span>
                    </div>
                </div>

                <div class='data-box'>
                    <div class='data-row'>
                        <span class='data-label'>Seller Name</span>
                        <span class='data-value'>{$name}</span>
                    </div>
                    <div class='data-row'>
                        <span class='data-label'>Phone Number</span>
                        <span class='data-value'>{$phone}</span>
                    </div>
                    <div class='data-row'>
                        <span class='data-label'>Pickup Location</span>
                        <span class='data-value'>{$location}</span>
                    </div>
                </div>
                
                <p class='intro' style='margin-bottom: 0; font-size: 14px; text-align: center; font-weight: bold; color: #16a34a;'>Please see the attached files for images of the material.</p>
            </div>
            
            <div class='footer'>
                &copy; " . date('Y') . " Recycle Mart BD. All rights reserved.
            </div>
        </div>
    </body>
    </html>
    ";

    // --- MULTIPART EMAIL MAGIC FOR ATTACHMENTS ---
    $boundary = md5(time()); // Unique boundary separator

    // Headers
    $headers = "From: Recycle Mart Notifications <noreply@webexpert.live>\r\n";
    $headers .= "Reply-To: {$to}\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: multipart/mixed; boundary=\"{$boundary}\"\r\n";

    // 1. Add the HTML Body to the package
    $message = "--{$boundary}\r\n";
    $message .= "Content-Type: text/html; charset=UTF-8\r\n";
    $message .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
    $message .= $html_content . "\r\n\r\n";

    // 2. Loop through and attach uploaded photos
    if (!empty($_FILES['photos']['name'][0])) {
        foreach ($_FILES['photos']['name'] as $key => $filename) {
            if ($_FILES['photos']['error'][$key] == UPLOAD_ERR_OK) {
                $tmp_name = $_FILES['photos']['tmp_name'][$key];
                $type = $_FILES['photos']['type'][$key];
                
                // Read the file and convert it into a base64 string
                $content = chunk_split(base64_encode(file_get_contents($tmp_name)));

                $message .= "--{$boundary}\r\n";
                $message .= "Content-Type: {$type}; name=\"{$filename}\"\r\n";
                $message .= "Content-Disposition: attachment; filename=\"{$filename}\"\r\n";
                $message .= "Content-Transfer-Encoding: base64\r\n\r\n";
                $message .= $content . "\r\n\r\n";
            }
        }
    }
    
    // Close the email package
    $message .= "--{$boundary}--"; 

    // Send the email
    if (mail($to, $subject, $message, $headers)) {
        echo json_encode(["status" => "success", "message" => "Listing submitted successfully!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Server failed to send the notification email."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request."]);
}
?>