<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

// Collect & sanitize inputs
$firstName = htmlspecialchars(trim($_POST['fname']    ?? ''));
$lastName  = htmlspecialchars(trim($_POST['lname']    ?? ''));
$email     = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
$phone     = htmlspecialchars(trim($_POST['phone']    ?? 'Not provided'));
$product   = htmlspecialchars(trim($_POST['project']  ?? 'Not specified'));
$comment   = htmlspecialchars(trim($_POST['message']  ?? ''));

// Validate required fields
if (!$firstName || !$lastName || !$email || !$comment) {
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
$subject = "New Contact Form: $firstName $lastName";

$message = "
New Contact Form Submission — USWaterWalls.com
================================================

Name:         $firstName $lastName
Email:        $email
Phone:        $phone
Project Type: $product

Message:
$comment

------------------------------------------------
Sent from uswaterwalls.com contact form
";

$headers  = "From: info@uswaterwalls.com\r\n";
$headers .= "Reply-To: $email\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();

if (mail($to, $subject, $message, $headers)) {
    echo json_encode(['success' => true, 'message' => 'Your message has been sent successfully!']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Message could not be sent. Please email us at info@uswaterwalls.com']);
}
?>
