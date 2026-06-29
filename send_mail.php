<?php
header('Content-Type: application/json');

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die(json_encode(['success' => false, 'message' => 'Method not allowed']));
}

// Sanitize inputs
$firstName = htmlspecialchars(trim($_POST['firstName'] ?? ''));
$lastName  = htmlspecialchars(trim($_POST['lastName']  ?? ''));
$email     = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
$phone     = htmlspecialchars(trim($_POST['phone']   ?? 'Not provided'));
$product   = htmlspecialchars(trim($_POST['product'] ?? 'Not specified'));
$message   = htmlspecialchars(trim($_POST['message'] ?? ''));

// Validate
if (!$firstName || !$lastName || !$email || !$message) {
    http_response_code(400);
    die(json_encode(['success' => false, 'message' => 'Please fill in all required fields.']));
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    die(json_encode(['success' => false, 'message' => 'Please enter a valid email address.']));
}

// Load PHPMailer directly (no composer needed)
require __DIR__ . '/phpmailer/Exception.php';
require __DIR__ . '/phpmailer/PHPMailer.php';
require __DIR__ . '/phpmailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

try {
    $mail = new PHPMailer(true);

    // SMTP Settings — Hostinger
    $mail->isSMTP();
    $mail->Host       = 'smtp.hostinger.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'info@uswaterwalls.com';
    $mail->Password   = 'PASTE_PASSWORD_HERE'; // ← Replace with your Hostinger email password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;
    $mail->CharSet    = 'UTF-8';

    // From / To
    $mail->setFrom('info@uswaterwalls.com', 'US Water Walls Website');
    $mail->addAddress('info@uswaterwalls.com', 'US Water Walls');
    $mail->addReplyTo($email, "$firstName $lastName");

    // Email content
    $mail->isHTML(false);
    $mail->Subject = "New Contact Form: $firstName $lastName";
    $mail->Body =
        "New Contact Form Submission — US Water Walls\n" .
        "============================================\n\n" .
        "Name:     $firstName $lastName\n" .
        "Email:    $email\n" .
        "Phone:    $phone\n" .
        "Product:  $product\n\n" .
        "Message:\n$message\n\n" .
        "--------------------------------------------\n" .
        "Sent from uswaterwalls.com contact form";

    $mail->send();

    echo json_encode(['success' => true, 'message' => "Thank you $firstName! We'll get back to you within 48 hours."]);

} catch (Exception $e) {
    error_log('PHPMailer Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Could not send message: ' . $e->getMessage()]);
}
?>
