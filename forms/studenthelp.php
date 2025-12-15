<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer-master/src/Exception.php';
require '../PHPMailer-master/src/PHPMailer.php';
require '../PHPMailer-master/src/SMTP.php';

// Recipient/admin email
$recipient_email = 'emekaolisa232@gmail.com';

// Accept POST fields (compatible with AJAX from validate.js)
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$subject = trim($_POST['subject'] ?? 'Student Help Request');
$message = trim($_POST['message'] ?? '');

// Validate
$errors = [];
if (empty($name)) $errors[] = 'Name is required';
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required';
if (empty($message)) $errors[] = 'Message is required';

if (!empty($errors)) {
    http_response_code(400);
    echo implode(', ', $errors);
    exit;
}

try {
    $mail = new PHPMailer(true);
    $mail->SMTPDebug = 0;
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'emekaolisa232@gmail.com';
    $mail->Password = 'vjeo izxb qyqj enwp';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // From should be a valid sender on your SMTP account
    $mail->setFrom($mail->Username, 'CourseManager - Student Help');
    if (!empty($email)) $mail->addReplyTo($email, $name);
    $mail->addAddress($recipient_email, 'Admin');

    $mail->isHTML(true);
    $mail->Subject = "Student Help Request: {$subject}";
    // Sanitize and prepare message
    $safe_message = nl2br(htmlspecialchars($message));

    // Email body with sender info and message
    $mail->Body = "
    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background: #f9f9f9;'>
        <div style='background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);'>
            <h2 style='color: #333; margin-bottom: 20px;'>Student Help Request</h2>
            <div style='margin-bottom: 15px;'>
                <strong>From:</strong> {$name} ({$email})
            </div>
            <div style='margin-bottom: 15px;'>
                <strong>Subject:</strong> {$subject}
            </div>
            <div style='margin-bottom: 15px;'>
                <strong>Message:</strong>
            </div>
            <div style='background: #f8f9fa; padding: 15px; border-left: 4px solid #007bff; margin: 10px 0;'>
                {$safe_message}
            </div>
            <div style='margin-top: 20px; padding-top: 15px; border-top: 1px solid #eee; color: #666; font-size: 12px;'>
                Sent from CourseManager Help System on " . date('Y-m-d H:i:s') . "
            </div>
        </div>
    </div>
    ";

    // Plain text alternative
    $mail->AltBody = "Student Help Request\n\nFrom: {$name} ({$email})\nSubject: {$subject}\n\nMessage:\n" . strip_tags($message);

    $mail->send();
    // validate.js expects plain text 'OK' on success
    echo 'OK';
    exit;
} catch (Exception $e) {
    http_response_code(500);
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    exit;
}
?>
