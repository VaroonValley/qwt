<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include PHPMailer autoloader
require 'vendor/phpmailer/phpmailer/src/Exception.php';
require 'vendor/phpmailer/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/phpmailer/src/SMTP.php';
function sendMail($message)
{
    $env = parse_ini_file(".env");
    // Create a new PHPMailer instance
    $mail = new PHPMailer();

    // SMTP configuration (for example, using Gmail SMTP)
    $mail->isSMTP();
    $mail->Host = $env['SMTP_HOST'];
    $mail->SMTPAuth = true;
    $mail->Username = $env['USERNAME'];
    $mail->Password = $env['PASSWORD'];
    $mail->SMTPSecure = 'tls';
    $mail->Port = $env['SMTP_PORT'];

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





require_once('class.phpmailer.php');
$mail = new PHPMailer();
$mail->IsSMTP();
$msg ="Control Panel Login Details";

    $mail->SMTPAuth = true;
    $mail->SMTPSecure = "none";
    $mail->Host = "qwebit.com";
    $mail->Port = 25;

    $mail->Username = "dnr@qwebit.com"; // SMTP account username
    $mail->Password = "00760076A*a*";        // SMTP account password
    $mail->AddAddress("quality.web.it.solutions@gmail.com");
    $mail->SetFrom("dnr@qwebit.com", "test@qwebit.com");
    $mail->Subject = "Control Panel Details";
    $mail->MsgHTML($msg);
    
    $mail->Send();
    echo "Message Sent OK";
    
    
    ?>