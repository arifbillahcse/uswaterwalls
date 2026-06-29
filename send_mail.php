<?php

// Collect & sanitize inputs
$firstName = htmlspecialchars(trim($_POST['firstName'] ?? ''));
$lastName  = htmlspecialchars(trim($_POST['lastName']  ?? ''));
$email     = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
$phone     = htmlspecialchars(trim($_POST['phone']   ?? 'Not provided'));
$product   = htmlspecialchars(trim($_POST['product'] ?? 'Not specified'));
$comment   = htmlspecialchars(trim($_POST['message'] ?? ''));

// Validate required fields
if (!$firstName || !$lastName || !$email || !$comment) {
    header('Location: contact.html?error=1');
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: contact.html?error=1');
    exit;
}

$to      = 'info@uswaterwalls.com';
$subject = "New Contact Form: $firstName $lastName";

$message = "
New Contact Form Submission — US Water Walls
================================================

Name:             $firstName $lastName
Email:            $email
Phone:            $phone
Product Interest: $product

Message:
$comment

------------------------------------------------
Sent from uswaterwalls.com contact form
";

$headers  = "From: info@uswaterwalls.com\r\n";
$headers .= "Reply-To: $email\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();

if (mail($to, $subject, $message, $headers)) {
    header('Location: contact.html?success=1');
} else {
    header('Location: contact.html?error=1');
}
exit;
?>
