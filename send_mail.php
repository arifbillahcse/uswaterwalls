<?php

// Collect & sanitize inputs
$firstName = htmlspecialchars(trim($_POST['fname']    ?? ''));
$lastName  = htmlspecialchars(trim($_POST['lname']    ?? ''));
$email     = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
$phone     = htmlspecialchars(trim($_POST['phone']    ?? 'Not provided'));
$project   = htmlspecialchars(trim($_POST['project']  ?? 'Not specified'));
$comment   = htmlspecialchars(trim($_POST['message']  ?? ''));

// Validate required fields
if (!$firstName || !$lastName || !$email || !$comment) {
    header('Location: contact.html?error=missing');
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: contact.html?error=email');
    exit;
}

$to      = 'info@usvideowalls.com';
$subject = "New Contact Form: $firstName $lastName";

$message = "
New Contact Form Submission — US Video Walls
================================================

Name:         $firstName $lastName
Email:        $email
Phone:        $phone
Project Type: $project

Message:
$comment

------------------------------------------------
Sent from usvideowalls.com contact form
";

$headers  = "From: info@usvideowalls.com\r\n";
$headers .= "Reply-To: $email\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();

if (mail($to, $subject, $message, $headers)) {
    header('Location: contact.html?sent=1');
} else {
    header('Location: contact.html?error=send');
}
exit;
?>
