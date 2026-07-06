<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

$firstName = htmlspecialchars(trim($_POST['fname']    ?? ''));
$lastName  = htmlspecialchars(trim($_POST['lname']    ?? ''));
$email     = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
$phone     = htmlspecialchars(trim($_POST['phone']    ?? 'Not provided'));
$product   = htmlspecialchars(trim($_POST['project']  ?? 'Not specified'));
$comment   = htmlspecialchars(trim($_POST['message']  ?? ''));

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

$body = "New Contact Form Submission — USWaterWalls.com\n";
$body .= "================================================\n\n";
$body .= "Name:             $firstName $lastName\n";
$body .= "Email:            $email\n";
$body .= "Phone:            $phone\n";
$body .= "Product Interest: $product\n\n";
$body .= "Message:\n$comment\n\n";
$body .= "------------------------------------------------\n";
$body .= "Sent from uswaterwalls.com contact form\n";

$headers  = "From: no-reply@uswaterwalls.com\r\n";
$headers .= "Reply-To: $email\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();

$sent = mail($to, $subject, $body, $headers);

if ($sent) {
    echo json_encode(['success' => true, 'message' => 'Your message has been sent successfully!']);
} else {
    $err = error_get_last();
    echo json_encode(['success' => false, 'message' => 'Mail failed. Error: ' . ($err['message'] ?? 'unknown')]);
}
?>
