<?php
/**
 * Contact Form Handler - US Video Walls
 * Uses PHPMailer to send emails via Hostinger SMTP
 */

header('Content-Type: application/json');

// Check if form was submitted via POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Include PHPMailer
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

try {
    // Validate and sanitize form inputs
    $fname = isset($_POST['fname']) ? trim($_POST['fname']) : '';
    $lname = isset($_POST['lname']) ? trim($_POST['lname']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $project = isset($_POST['project']) ? trim($_POST['project']) : '';
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';

    // Validate required fields
    if (empty($fname) || empty($lname) || empty($email) || empty($message)) {
        throw new Exception('Missing required fields: first name, last name, email, and message are required.');
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email address.');
    }

    // Instantiate PHPMailer
    $mail = new PHPMailer(true);

    // SMTP Configuration for Hostinger
    $mail->isSMTP();
    $mail->Host = 'smtp.hostinger.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'info@medicalmurals.com';
    $mail->Password = 'YOUR_HOSTINGER_SMTP_PASSWORD_HERE'; // Replace with actual password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // SSL
    $mail->Port = 465;

    // Recipient email
    $mail->setFrom('info@medicalmurals.com', 'US Video Walls Contact Form');
    $mail->addAddress('info@medicalmurals.com', 'US Video Walls Team');

    // Reply-to sender's email
    $mail->addReplyTo($email, $fname . ' ' . $lname);

    // Email content
    $mail->isHTML(true);
    $mail->Subject = 'New Contact Form Submission - US Video Walls';

    // Build HTML email body
    $emailBody = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; color: #333; line-height: 1.6; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; }
            .header { background-color: #1b68ff; color: white; padding: 20px; border-radius: 8px 8px 0 0; text-align: center; }
            .content { padding: 20px; }
            .field { margin-bottom: 15px; }
            .label { font-weight: bold; color: #1b68ff; }
            .value { margin-top: 5px; padding: 10px; background-color: #f5f5f5; border-radius: 4px; }
            .footer { text-align: center; font-size: 12px; color: #999; margin-top: 20px; border-top: 1px solid #ddd; padding-top: 10px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>New Contact Form Submission</h2>
            </div>
            <div class='content'>
                <div class='field'>
                    <span class='label'>Name:</span>
                    <div class='value'>" . htmlspecialchars($fname) . " " . htmlspecialchars($lname) . "</div>
                </div>

                <div class='field'>
                    <span class='label'>Email:</span>
                    <div class='value'><a href='mailto:" . htmlspecialchars($email) . "'>" . htmlspecialchars($email) . "</a></div>
                </div>

                <div class='field'>
                    <span class='label'>Phone:</span>
                    <div class='value'>" . (empty($phone) ? '<em>Not provided</em>' : htmlspecialchars($phone)) . "</div>
                </div>

                <div class='field'>
                    <span class='label'>Project Type:</span>
                    <div class='value'>" . (empty($project) ? '<em>Not specified</em>' : htmlspecialchars($project)) . "</div>
                </div>

                <div class='field'>
                    <span class='label'>Message:</span>
                    <div class='value'>" . nl2br(htmlspecialchars($message)) . "</div>
                </div>
            </div>
            <div class='footer'>
                <p>This is an automated email from the US Video Walls contact form.</p>
                <p>Sent at: " . date('Y-m-d H:i:s') . " UTC</p>
            </div>
        </div>
    </body>
    </html>
    ";

    $mail->Body = $emailBody;

    // Send email
    if ($mail->send()) {
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Your message has been sent successfully. We will respond within 24 hours.'
        ]);
    } else {
        throw new Exception('Failed to send email: ' . $mail->ErrorInfo);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An unexpected error occurred. Please try again later.'
    ]);
}
?>
