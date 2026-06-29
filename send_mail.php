<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

// Load PHPMailer (bundled — no composer needed)
require_once __DIR__ . '/phpmailer/Exception.php';
require_once __DIR__ . '/phpmailer/PHPMailer.php';
require_once __DIR__ . '/phpmailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Collect & sanitize inputs
$firstName = htmlspecialchars(trim($_POST['fname']    ?? ''));
$lastName  = htmlspecialchars(trim($_POST['lname']    ?? ''));
$email     = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
$phone     = htmlspecialchars(trim($_POST['phone']    ?? 'Not provided'));
$project   = htmlspecialchars(trim($_POST['project']  ?? 'Not specified'));
$message   = htmlspecialchars(trim($_POST['message']  ?? ''));

// Validate required fields
if (!$firstName || !$lastName || !$email || !$message) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Please fill in all required fields.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
    exit;
}

try {
    $mail = new PHPMailer(true);

    // Hostinger SMTP settings
    $mail->isSMTP();
    $mail->Host       = 'smtp.hostinger.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'info@usvideowalls.com';
    $mail->Password   = 'YOUR_PASSWORD_HERE'; // Replace with your Hostinger email password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port       = 465;

    // Sender & recipient
    $mail->setFrom('info@usvideowalls.com', 'US Video Walls');
    $mail->addAddress('info@usvideowalls.com', 'US Video Walls');
    $mail->addReplyTo($email, "$firstName $lastName");

    // Email content
    $mail->isHTML(false);
    $mail->Subject = "New Contact Form: $firstName $lastName";
    $mail->Body    = "
New Contact Form Submission — US Video Walls
================================================

Name:             $firstName $lastName
Email:            $email
Phone:            $phone
Project Type:     $project

Message:
$message

------------------------------------------------
Sent from usvideowalls.com contact form
";

    $mail->send();

    echo json_encode([
        'success' => true,
        'message' => 'Your message has been sent successfully! We will respond within 24 hours.'
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Message could not be sent. Please email us directly at info@usvideowalls.com'
    ]);
}
?>
