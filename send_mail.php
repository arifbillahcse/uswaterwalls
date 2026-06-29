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

// Check if PHPMailer is installed
if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
    http_response_code(500);
    error_log("PHPMailer not installed. Run: composer install");
    echo json_encode(['success' => false, 'message' => 'Server configuration error. Please call us at (407) 792-8916']);
    exit;
}

require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

try {
    $mail = new PHPMailer(true);

    // SMTP Configuration for Hostinger
    $mail->isSMTP();
    $mail->Host       = 'smtp.hostinger.com';
    $mail->SMTPAuth   = true;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;
    $mail->Username   = 'info@uswaterwalls.com';
    $mail->Password   = 'PASTE_PASSWORD_HERE'; // Replace with actual Hostinger SMTP password

    // Set sender and recipient
    $mail->setFrom('info@uswaterwalls.com', 'US Water Walls');
    $mail->addAddress('info@uswaterwalls.com');
    $mail->addReplyTo($email, "$firstName $lastName");

    // Email content
    $mail->isHTML(false);
    $mail->Subject = "New Contact Form Submission: $firstName $lastName";

    $body = "New Contact Form Submission — US Water Walls\n";
    $body .= "=============================================\n\n";
    $body .= "Name:             $firstName $lastName\n";
    $body .= "Email:            $email\n";
    $body .= "Phone:            $phone\n";
    $body .= "Product Interest: $product\n";
    $body .= "\nMessage:\n";
    $body .= "$message\n";
    $body .= "\n---------------------------------------------\n";
    $body .= "Sent from uswaterwalls.com contact form\n";

    $mail->Body = $body;

    // Send the email
    $mail->send();

    echo json_encode(['success' => true, 'message' => 'Your message has been sent successfully! We\'ll get back to you within 48 hours.']);
    error_log("Email sent successfully to info@uswaterwalls.com from $email");

} catch (Exception $e) {
    http_response_code(500);
    error_log("PHPMailer Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
