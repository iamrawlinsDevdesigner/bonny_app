<?php
require_once __DIR__ . '/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/SMTP.php';
require_once __DIR__ . '/PHPMailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function send_mail($to, $subject, $body, $from = 'noreply@bonnyhub.com', $fromName = 'BonnyHub') {
  $mail = new PHPMailer(true);

  try {
    // SMTP settings
    $mail->isSMTP();
    $mail->Host       = 'sandbox.smtp.mailtrap.io'; // Replace with Gmail SMTP if needed
    $mail->SMTPAuth   = true;
    $mail->Username   = 'e778ef0dbfb17f'; // Replace with your Mailtrap username
    $mail->Password   = 'd7c1ed016d3d15'; // Replace with your Mailtrap password
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    // Email content
    $mail->setFrom($from, $fromName);
    $mail->addAddress($to);
    $mail->isHTML(false); // or true if you want HTML
    $mail->Subject = $subject;
    $mail->Body    = $body;

    $mail->send();
    return true;
  } catch (Exception $e) {
    error_log("Mailer Error: " . $mail->ErrorInfo);
    return false;
  }
}
