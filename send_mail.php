<?php
header('Content-Type: application/json');

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die(json_encode(['success' => false, 'message' => 'Method not allowed']));
}

// Sanitize inputs
$firstName = trim($_POST['firstName'] ?? '');
$lastName = trim($_POST['lastName'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$product = trim($_POST['product'] ?? '');
$message = trim($_POST['message'] ?? '');

// Validate required fields
if (empty($firstName) || empty($lastName) || empty($email) || empty($message)) {
    http_response_code(400);
    die(json_encode(['success' => false, 'message' => 'Please fill in all required fields']));
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    die(json_encode(['success' => false, 'message' => 'Invalid email address']));
}

// Check if PHPMailer is installed
if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
    http_response_code(500);
    die(json_encode(['success' => false, 'message' => 'PHPMailer not installed. Run: composer install']));
}

// Load PHPMailer
require __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

try {
    $mail = new PHPMailer(true);

    // SMTP Settings
    $mail->isSMTP();
    $mail->Host = 'smtp.hostinger.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'info@uswaterwalls.com';
    $mail->Password = 'PASTE_PASSWORD_HERE';  // Replace with actual password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // Email details
    $mail->setFrom('info@uswaterwalls.com', 'US Water Walls');
    $mail->addAddress('info@uswaterwalls.com');
    $mail->addReplyTo($email, "$firstName $lastName");

    $mail->isHTML(false);
    $mail->CharSet = 'UTF-8';

    // Build email
    $mail->Subject = "New Contact: $firstName $lastName";

    $body = "New Contact Form Submission\n";
    $body .= "============================\n\n";
    $body .= "Name: $firstName $lastName\n";
    $body .= "Email: $email\n";
    $body .= "Phone: $phone\n";
    $body .= "Product: $product\n\n";
    $body .= "Message:\n";
    $body .= $message;

    $mail->Body = $body;

    // Send
    if ($mail->send()) {
        echo json_encode(['success' => true, 'message' => 'Message sent! We\'ll contact you within 48 hours.']);
    } else {
        throw new Exception('Send failed: ' . $mail->ErrorInfo);
    }

} catch (Exception $e) {
    error_log('PHPMailer Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
