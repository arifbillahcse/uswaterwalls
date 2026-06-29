<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

// Collect & sanitize
$firstName = htmlspecialchars(trim($_POST['firstName'] ?? ''));
$lastName  = htmlspecialchars(trim($_POST['lastName']  ?? ''));
$email     = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
$phone     = htmlspecialchars(trim($_POST['phone']   ?? 'Not provided'));
$product   = htmlspecialchars(trim($_POST['product'] ?? 'Not specified'));
$message   = htmlspecialchars(trim($_POST['message'] ?? ''));

// Validate
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
$subject = "New Contact Form: $firstName $lastName";

$body = "New Contact Form Submission — US Water Walls\n";
$body .= "=============================================\n\n";
$body .= "Name:     $firstName $lastName\n";
$body .= "Email:    $email\n";
$body .= "Phone:    $phone\n";
$body .= "Product:  $product\n\n";
$body .= "Message:\n$message\n\n";
$body .= "---------------------------------------------\n";
$body .= "Sent from uswaterwalls.com contact form";

$headers  = "From: info@uswaterwalls.com\r\n";
$headers .= "Reply-To: $email\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

if (mail($to, $subject, $body, $headers)) {
    echo json_encode(['success' => true, 'message' => "Thank you $firstName! We'll get back to you within 48 hours."]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Could not send message. Please call us at (407) 792-8916']);
}
?>
