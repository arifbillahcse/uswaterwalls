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

// Log submission
error_log("FORM SUBMITTED - From: $email, Name: $firstName $lastName, Product: $product");

// Try PHPMailer first (if installed)
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    try {
        require 'vendor/autoload.php';

        $mail = new PHPMailer\PHPMailer\PHPMailer(true);

        // SMTP Configuration for Hostinger
        $mail->isSMTP();
        $mail->Host       = 'smtp.hostinger.com';
        $mail->SMTPAuth   = true;
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->Username   = 'info@uswaterwalls.com';
        $mail->Password   = 'PASTE_PASSWORD_HERE'; // Replace with actual password

        // Email content
        $mail->setFrom('info@uswaterwalls.com', 'US Water Walls');
        $mail->addAddress('info@uswaterwalls.com');
        $mail->addReplyTo($email, "$firstName $lastName");

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
        $mail->send();

        echo json_encode(['success' => true, 'message' => 'Your message has been sent successfully! We\'ll get back to you within 48 hours.']);
        error_log("Email sent successfully via PHPMailer");
    } catch (Exception $e) {
        error_log("PHPMailer Error: " . $mail->ErrorInfo);
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Message could not be sent. Please call us at (407) 792-8916']);
    }
} else {
    // Fallback to native PHP mail() function
    error_log("PHPMailer not found, falling back to mail()");

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

    $headers  = "From: info@uswaterwalls.com\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    if (mail($to, $subject, $mailBody, $headers)) {
        echo json_encode(['success' => true, 'message' => 'Your message has been sent successfully! We\'ll get back to you within 48 hours.']);
        error_log("Email sent successfully via mail()");
    } else {
        error_log("mail() delivery failed");
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Message could not be sent. Please call us at (407) 792-8916']);
    }
}
?>
