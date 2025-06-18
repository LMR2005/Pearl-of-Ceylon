<?php

require 'mailer/PHPMailer.php';
require 'mailer/SMTP.php';
require 'mailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer();

    try {
        // Server settings
        $mail->isSMTP();  // Use SMTP
        $mail->Host = 'smtp.gmail.com';  // Set the SMTP server to send through
        $mail->SMTPAuth = true;  // Enable SMTP authentication
        $mail->Username = 'lmrjayasinghe@gmail.com';  // SMTP username
        $mail->Password = 'sfsj pvtt pbjd gulc';  // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;  // Enable TLS encryption
        $mail->Port = 587;  // TCP port to connect to

        // Recipients
        $mail->setFrom('lmrjayasinghe@gmail.com', 'Methul Jayasinghe');
        $mail->addAddress('lmrjayasinghe@gmail.com');  // Add a recipient

        // Content
        $mail->isHTML(true);  // Set email format to HTML
        $mail->Subject = 'Test Email from PHPMailer';
        $mail->Body    = 'This is a <b>test email</b> sent using PHPMailer.';

        // Send the email
        if ($mail->send()) {
            echo 'Message has been sent';
        } else {
            echo 'Mailer Error: ' . $mail->ErrorInfo;
        }
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
?>