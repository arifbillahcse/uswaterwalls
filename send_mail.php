<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

// Form fields from contact.html
$fname   = trim($_POST['fname']   ?? '');
$lname   = trim($_POST['lname']   ?? '');
$email   = trim($_POST['email']   ?? '');
$phone   = trim($_POST['phone']   ?? '');
$project = trim($_POST['project'] ?? '');
$message = trim($_POST['message'] ?? '');

// Required fields
if (!$fname || !$lname || !$email || !$message) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Please fill in all required fields.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
    exit;
}

$to      = 'info@usvideowalls.com';
$subject = 'New Contact Form Submission - US Video Walls';

$body = "You have a new contact form submission from usvideowalls.com\r\n";
$body .= "=============================================================\r\n\r\n";
$body .= "First Name   : " . htmlspecialchars($fname)   . "\r\n";
$body .= "Last Name    : " . htmlspecialchars($lname)   . "\r\n";
$body .= "Email        : " . htmlspecialchars($email)   . "\r\n";
$body .= "Phone        : " . ($phone   ? htmlspecialchars($phone)   : 'Not provided')   . "\r\n";
$body .= "Project Type : " . ($project ? htmlspecialchars($project) : 'Not specified') . "\r\n\r\n";
$body .= "Message:\r\n";
$body .= htmlspecialchars($message) . "\r\n\r\n";
$body .= "=============================================================\r\n";
$body .= "Sent from usvideowalls.com\r\n";

$headers = "From: info@usvideowalls.com\r\n";
$headers .= "Reply-To: " . htmlspecialchars($email) . "\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();

if (mail($to, $subject, $body, $headers)) {
    echo json_encode(['success' => true, 'message' => 'Your message has been sent! We will get back to you within 24 hours.']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to send message. Please email us directly at info@usvideowalls.com']);
}
?>
