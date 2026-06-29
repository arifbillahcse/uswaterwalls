<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

$firstName = trim($_POST['firstName'] ?? '');
$lastName  = trim($_POST['lastName']  ?? '');
$email     = trim($_POST['email']     ?? '');
$phone     = trim($_POST['phone']     ?? 'Not provided');
$product   = trim($_POST['product']   ?? 'Not specified');
$message   = trim($_POST['message']   ?? '');

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
$subject = "New Quote Request: $firstName $lastName";
$headers = "From: info@uswaterwalls.com\r\nReply-To: $email\r\n";

$body = "New Quote Request — US Water Walls\n";
$body .= "===================================\n\n";
$body .= "Name:     $firstName $lastName\n";
$body .= "Email:    $email\n";
$body .= "Phone:    $phone\n";
$body .= "Product:  $product\n\n";
$body .= "Message:\n$message\n\n";
$body .= "-----------------------------------\n";
$body .= "Sent from uswaterwalls.com";

if (mail($to, $subject, $body, $headers)) {
    echo json_encode(['success' => true, 'message' => 'Thank you! We will get back to you within 48 hours.']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to send. Please call us at (407) 792-8916']);
}
?>
