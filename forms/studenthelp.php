<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';

if(isset($_POST["submit"])) {
    $name = $_POST["your_name"];
    $email = $_POST["your_email"];
    $message = $_POST["message"];

    // Create a new PHPMailer instance

    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;

        // 🔹 PUT YOUR EMAIL & APP PASSWORD HERE
        $mail->Username   = 'emekaolisa232@gmail.com'; // <-- Your Gmail address
        $mail->Password   = 'vjeo izxb qyqj enwp';     // <-- Your Gmail App password

        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Sender info (must be your real email)
        $mail->setFrom('emekaolisa232@gmail.com', 'User Assistance Forum');

        // Reply-to (user's email from the form)
        if (!empty($email)) {
            $mail->addReplyTo($email, $name);
        }

        // Recipient (admin email)
        $mail->addAddress('emekaolisa232@gmail.com', 'Admin'); // Change to your admin's email

        // Email content
        $mail->isHTML(true);
        $mail->Subject = 'Contact Form Message from ' . $name;
        $mail->Body    = "<b>Name:</b> {$name}<br>
                          <b>Email:</b> {$email}<br>
                          <b>Message:</b><br>{$message}";

        $mail->send();
        echo 'Message has been sent successfully';
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>
