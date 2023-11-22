<?php


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include PHPMailer autoloader
require 'vendor/phpmailer/phpmailer/src/Exception.php';
require 'vendor/phpmailer/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/phpmailer/src/SMTP.php';
function sendMail($message)
{



    // Create a new PHPMailer instance
    $mail = new PHPMailer();

    // SMTP configuration (for example, using Gmail SMTP)
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'soephyuphyuhtun99@gmail.com';
    $mail->Password = 'ovpb doci lrit emvp';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    // Sender and recipient details
    $mail->setFrom('soephyuphyuhtun99@gmail.com', 'Soe Phyu Phyu Htun');
    $mail->addAddress('soephyuphyuhtun@varoonvalley.com', 'Soe Phyu Phyu Htun');
    $mail->addAddress('contact@varoonvalley.com', 'Varoon Valley');

    // Email content
    $mail->isHTML(true);
    $mail->Subject = 'Warning';
    $mail->Body = $message;

    // Send email and check for success
    if ($mail->send()) {
        return 'Email has been sent successfully!';
    } else {
        return 'Mailer Error: ' . $mail->ErrorInfo;
    }
}
