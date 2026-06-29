<?php
header('Content-Type: application/json');

// Log all incoming data
error_log("=== FORM SUBMISSION DEBUG ===");
error_log("Request Method: " . $_SERVER['REQUEST_METHOD']);
error_log("POST Data: " . json_encode($_POST));
error_log("PHP Version: " . phpversion());
error_log("PHPMailer exists: " . (file_exists(__DIR__ . '/vendor/autoload.php') ? 'YES' : 'NO'));

// Simple response
echo json_encode([
    'success' => true,
    'message' => 'Debug: Form reached PHP file successfully',
    'data' => $_POST,
    'phpmailer_installed' => file_exists(__DIR__ . '/vendor/autoload.php')
]);
?>
