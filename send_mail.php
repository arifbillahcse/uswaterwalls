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

$to      = 'ron@medicalmurals.com';
$subject = "New Contact Form Submission: $firstName $lastName — US Water Walls";

$mailBody = "
New Contact Form Submission — US Water Walls
=============================================

Name:             $firstName $lastName
Email:            $email
Phone:            $phone
Product Interest: $product

Message:
$message

---------------------------------------------
Sent from uswaterwalls.com contact form
";

$headers  = "From: info@uswaterwalls.com\r\n";
$headers .= "Reply-To: $email\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();

if (mail($to, $subject, $mailBody, $headers)) {
    echo json_encode(['success' => true, 'message' => 'Your message has been sent successfully! We\'ll get back to you within 48 hours.']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Message could not be sent. Please call us at (407) 792-8916']);
}
?>
