<?php
header('Content-Type: application/json');
// header('Content-Type: text/html'); // Change to text/html or remove this line entirely

// Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require '../PHPMailer-master/src/Exception.php';
require '../PHPMailer-master/src/PHPMailer.php';
require '../PHPMailer-master/src/SMTP.php';

// Recipient email address (where the contact form message will be sent)
$recipient_email = 'emekaolisa232@gmail.com';

// Get form data
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

// Validate input
$errors = [];
if (empty($name)) $errors[] = 'Name is required';
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required';
if (empty($subject)) $errors[] = 'Subject is required';
if (empty($message)) $errors[] = 'Message is required';

if (!empty($errors)) {
    http_response_code(400);
        echo implode(', ', $errors);
    exit;
}

try {
    // Create a new PHPMailer instance
    $mail = new PHPMailer(true);

    // Server settings
    $mail->SMTPDebug = 0; // Set to SMTP::DEBUG_SERVER for debugging
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com'; // Gmail SMTP server
    $mail->SMTPAuth = true;
    $mail->Username = 'your-email@gmail.com'; // Your Gmail address
    $mail->Password = 'your-app-password'; // Your Gmail app-specific password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // Recipients
    $mail->setFrom($email, $name); // Sender (from contact form)
    $mail->addAddress($recipient_email); // Recipient (your email)
    $mail->addReplyTo($email, $name); // Reply-to address

    // Content
    $mail->isHTML(true);
    $mail->Subject = "New Contact Form Submission: {$subject}";
    $mail->Body = buildEmailBody($name, $email, $subject, $message);
    $mail->AltBody = buildAltEmailBody($name, $email, $subject, $message);

    // Send the email
    $mail->send();

    http_response_code(200);
        echo 'OK';
    exit;

} catch (Exception $e) {
    http_response_code(500);
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    exit;
}

/**
 * Build HTML email body
 */
function buildEmailBody($name, $email, $subject, $message) {
    return <<<HTML
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f9f9f9; border: 1px solid #ddd; border-radius: 5px; }
            .header { background-color: #007bff; color: white; padding: 15px; border-radius: 5px 5px 0 0; }
            .header h2 { margin: 0; }
            .content { background-color: white; padding: 20px; }
            .field { margin-bottom: 15px; }
            .field-label { font-weight: bold; color: #007bff; }
            .field-value { padding: 8px; background-color: #f0f0f0; border-left: 3px solid #007bff; padding-left: 10px; }
            .footer { background-color: #f0f0f0; padding: 10px; text-align: center; font-size: 12px; color: #666; border-radius: 0 0 5px 5px; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h2>New Contact Form Submission</h2>
            </div>
            <div class="content">
                <div class="field">
                    <div class="field-label">From:</div>
                    <div class="field-value">{$name} ({$email})</div>
                </div>
                <div class="field">
                    <div class="field-label">Subject:</div>
                    <div class="field-value">{$subject}</div>
                </div>
                <div class="field">
                    <div class="field-label">Message:</div>
                    <div class="field-value">
                        <p>{nl2br(htmlspecialchars($message))}</p>
                    </div>
                </div>
            </div>
            <div class="footer">
                <p>This email was sent from the Course Manager contact form.</p>
            </div>
        </div>
    </body>
    </html>
    HTML;
}

/**
 * Build plain text email body
 */
function buildAltEmailBody($name, $email, $subject, $message) {
    return <<<TEXT
    New Contact Form Submission

    From: {$name} ({$email})
    Subject: {$subject}

    Message:
    {$message}

    ---
    This email was sent from the Course Manager contact form.
    TEXT;
}
?>
