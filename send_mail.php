<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

// Collect & sanitize inputs
$firstName = htmlspecialchars(trim($_POST['firstName'] ?? ''));
$lastName  = htmlspecialchars(trim($_POST['lastName']  ?? ''));
$email     = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
$phone     = htmlspecialchars(trim($_POST['phone']   ?? 'Not provided'));
$product   = htmlspecialchars(trim($_POST['product'] ?? 'Not specified'));
$message   = htmlspecialchars(trim($_POST['message'] ?? ''));

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

$to      = 'info@uswaterwalls.com';
$subject = "New Contact Form Submission: $firstName $lastName";

$mailBody = "New Contact Form Submission — US Water Walls\n";
$mailBody .= "=============================================\n\n";
$mailBody .= "Name:             $firstName $lastName\n";
$mailBody .= "Email:            $email\n";
$mailBody .= "Phone:            $phone\n";
$mailBody .= "Product Interest: $product\n";
$mailBody .= "\nMessage:\n";
$mailBody .= "$message\n";
$mailBody .= "\n---------------------------------------------\n";
$mailBody .= "Sent from uswaterwalls.com contact form\n";

// Proper email headers for Hostinger
$headers  = "From: info@uswaterwalls.com\r\n";
$headers .= "Reply-To: $email\r\n";
$headers .= "Return-Path: info@uswaterwalls.com\r\n";
$headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
$headers .= "X-Priority: 3\r\n";

// Attempt to send email
$sent = @mail($to, $subject, $mailBody, $headers);

if ($sent) {
    echo json_encode(['success' => true, 'message' => 'Your message has been sent successfully! We\'ll get back to you within 48 hours.']);
} else {
    // Log error for debugging
    error_log("Mail failed for: $email, To: $to, Subject: $subject");
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Message could not be sent. Please call us at (407) 792-8916']);
}
?>
