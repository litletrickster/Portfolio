<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// Create instance of PHPMailer
$mail = new PHPMailer(true);

try {
    // **SMTP Settings**
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com'; // Gmail SMTP
    $mail->SMTPAuth   = true;
    $mail->Username   = 'julesphillipeb@gmail.com'; // Your Gmail
    $mail->Password   = 'aqsy zrxs vyzz ariv'; // Use an App Password!
    $mail->SMTPSecure = 'tls'; // Use 'ssl' for port 465
    $mail->Port       = 587; // TLS port

    // **Email Headers**
    $mail->setFrom('julesphillipeb@gmail.com', 'Jules Bartolome'); // Sender
    $mail->addAddress('litletrickster@gmail.com', 'Miguel Jose Mancenido'); // Recipient
    $mail->addReplyTo('julesphillipeb@gmail.com', 'Your Name');

    // **Email Content**
    $mail->isHTML(true);
    $mail->Subject = 'Test Email from PHPMailer on InfinityFree';
    $mail->Body    = '<h1>Hello!</h1><p>This is a test email sent using PHPMailer on InfinityFree.</p>';
    
    // **Send Email**
    $mail->send();
    echo '✅ Email sent successfully!';
} catch (Exception $e) {
    echo "❌ Email could not be sent. Error: {$mail->ErrorInfo}";
}
?>
