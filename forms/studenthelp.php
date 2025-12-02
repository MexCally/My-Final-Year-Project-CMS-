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
$name = trim($_POST['name'] ?? $_POST['your_name'] ?? '');
$email = trim($_POST['email'] ?? $_POST['your_email'] ?? '');
$subject = trim($_POST['subject'] ?? 'Student Account Assistance');
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

    // Minimal inline styling and only the user's message in the email body
    $mail->Body = "<div style=\"font-family: Arial, Helvetica, sans-serif; color: #222; line-height:1.5; max-width:600px; margin:16px auto; padding:18px; border-radius:8px; background:#f8f9fb; border:1px solid #e3e6ea;\">{$safe_message}</div>";

    // Plain text alternative should contain only the raw message (sanitized)
    $mail->AltBody = strip_tags(str_replace(["\r\n","\r"], "\n", $safe_message));

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
