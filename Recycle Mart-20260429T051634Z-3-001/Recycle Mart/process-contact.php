<?php
// Ensure this only runs on a POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Catch the JSON data sent from Javascript
    $data = json_decode(file_get_contents("php://input"), true);

    // Clean and sanitize inputs to prevent malicious code
    $name = htmlspecialchars(trim($data['name'] ?? ''));
    $email = filter_var(trim($data['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $subject = htmlspecialchars(trim($data['subject'] ?? ''));
    $message = htmlspecialchars(trim($data['message'] ?? ''));

    // Validate that required fields aren't empty
    if (empty($name) || empty($email) || empty($message)) {
        echo json_encode(["status" => "error", "message" => "Please fill all required fields."]);
        exit;
    }

    // --- EMAIL SETUP ---
    $to = "jhannatulhaqueanon@gmail.com";
    $email_subject = "Recycle Mart Inquiry: " . $subject;

    // --- BEAUTIFUL HTML EMAIL TEMPLATE ---
    $html_content = "
    <html>
    <head>
      <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4fdf8; color: #333; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 1px solid #e5e7eb; }
        .header { background-color: #16a34a; color: white; padding: 25px 20px; text-align: center; }
        .header h2 { margin: 0; font-size: 24px; letter-spacing: 0.5px; }
        .content { padding: 30px; }
        .content p { line-height: 1.6; margin-top: 0; margin-bottom: 25px; color: #4b5563; }
        .label { font-weight: 700; color: #16a34a; font-size: 0.85em; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 5px; }
        .value { background: #f9fafb; padding: 15px; border-radius: 6px; border: 1px solid #f3f4f6; margin-top: 0; margin-bottom: 20px; font-size: 15px; color: #1f2937; }
        .footer { background: #f9fafb; padding: 20px; text-align: center; font-size: 12px; color: #6b7280; border-top: 1px solid #e5e7eb; }
      </style>
    </head>
    <body>
      <div class='container'>
        <div class='header'>
          <h2>New Form Submission</h2>
        </div>
        <div class='content'>
          <p>You have received a new message from the Recycle Mart BD contact form.</p>

          <div class='label'>Name</div>
          <div class='value'>{$name}</div>

          <div class='label'>Email Address</div>
          <div class='value'><a href='mailto:{$email}' style='color: #16a34a; text-decoration: none;'>{$email}</a></div>

          <div class='label'>Subject</div>
          <div class='value'>{$subject}</div>

          <div class='label'>Message</div>
          <div class='value' style='white-space: pre-wrap;'>{$message}</div>
        </div>
        <div class='footer'>
          &copy; " . date("Y") . " Recycle Mart BD. All rights reserved.
        </div>
      </div>
    </body>
    </html>
    ";

    // Required headers for sending HTML email
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    // Using a generic 'noreply' to avoid spam filters blocking forged 'From' addresses
    $headers .= "From: Recycle Mart <noreply@webexpert.live>" . "\r\n";
    // Setting Reply-To so hitting 'reply' goes directly to the customer
    $headers .= "Reply-To: " . $email . "\r\n";

    // Send the email and return success/failure to the Javascript
    if (mail($to, $email_subject, $html_content, $headers)) {
        echo json_encode(["status" => "success", "message" => "Message sent!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Server failed to send email."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}
?>