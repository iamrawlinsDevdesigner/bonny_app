<?php

require_once __DIR__ . '/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/SMTP.php';
require_once __DIR__ . '/PHPMailer/Exception.php';


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

 // If using Composer
// OR manually require files if you downloaded PHPMailer

$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->isSMTP();
    $mail->Host       = 'sandbox.smtp.mailtrap.io';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'e778ef0dbfb17f'; // Your Gmail address
    $mail->Password   = 'd7c1ed016d3d15';   // App password (not your login password)
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    // Recipients
    $mail->setFrom('bonnyapp@gmail.com', 'BonnyAPP');
    $mail->addAddress('rawcoaster@gmail.com', 'Rawlins'); // The email to test

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'Test Email from Laragon';
    $mail->Body    = '<h1>It works!</h1><p>This email was sent from PHPMailer on localhost.</p>';

    $mail->send();
    echo '✅ Email has been sent successfully!';
} catch (Exception $e) {
    echo "❌ Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
?>
