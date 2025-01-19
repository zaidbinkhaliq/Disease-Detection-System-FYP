

<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

function sendVerificationEmail($email, $token) {
    $mail = new PHPMailer(true);
    try {
        // // Server settings
        // $mail->isSMTP();
        // $mail->Host = 'smtp.gmail.com'; // Use Gmail's SMTP server
        // $mail->SMTPAuth = true;
        // $mail->Username = 'abdulmujeebkhan70@gmail.com'; // SMTP username
        // $mail->Password = 'vxsf kfyg cnbl asyu'; // SMTP password
        // $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Encryption
        // $mail->Port = 587; // TCP port for TLS

        // // Recipients
        // $mail->setFrom('abdulmujeebkhan70@gmail.com', 'Verification purposes');
        // $mail->addAddress($email); // Add the user's email


        $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'malikusamaaliawan@gmail.com';
            $mail->Password   = 'hvdx qouo icwu cmox'; 
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Recipients
            $mail->setFrom('malikusamaaliawan@gmail.com', 'Verification purposes'); // Sender email
            $mail->addAddress($email); // Company email




        // $mail->isSMTP();
        //     $mail->Host       = 'smtp.gmail.com';
        //     $mail->SMTPAuth   = true;
        //     $mail->Username   = 'malikusamaaliawan@gmail.com';
        //     $mail->Password   = 'hvdx qouo icwu cmox'; 
        //     $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        //     $mail->Port       = 587;

        //     // Recipients
        //     $mail->setFrom('malikusamaaliawan@gmail.com', 'Usama Ali'); // Sender email
        //     $mail->addAddress('malikusamaaliawan@gmail.com'); // Company email




        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Verify your email address';
        $mail->Body = 'Please click the link below to verify your email address:<br>
<a href="http://localhost/log/verify.php?token=' . urlencode($token) . '">Verify Email</a>';

 $mail->send();
        $message = "<div class='alert alert-success success-alert'>Verification email has been sent!</div>";
    } catch (Exception $e) {
        $message = "<div class='alert alert-danger'>Message could not be sent. Mailer Error: {$mail->ErrorInfo}</div>";
    }
}
?>
