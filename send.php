
<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';


if (isset($_POST['send'])) {
    try {
        $mail = new PHPMailer(true);

        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'sender@gmail.com';
        $mail->Password = 'pass key for above mail'; // not required in this example, but is if you are not
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('sender@gmail.com', 'sender'); // Set your name here

        // Admin email address
        $adminEmail = 'sender@gmail.com';
        $mail->addAddress($adminEmail);

        // User email address
        $userEmail = $_POST["email"];

        $mail->isHTML(true);

        // Subject for admin
        $mail->Subject = 'Contact Form Submission from ' . $_POST["name"];

        // Body for admin (contains all details)
        $mail->Body = '
            <html>
            <head>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                    }
                    h2 {
                        color: #333;
                    }
                    p {
                        color: #666;
                    }
                </style>
            </head>
            <body>
                <h2>Contact Form Submission</h2>
                <p><strong>Name:</strong> ' . $_POST["name"] . '</p>
                <p><strong>Email:</strong> ' . $_POST["email"] . '</p>
                <p><strong>Message:</strong> ' . $_POST["message"] . '</p>
            </body>
            </html>
        ';

        if (isset($_FILES['resume']) && $_FILES['resume']['error'] === UPLOAD_ERR_OK) {
            $resumeTmpName = $_FILES['resume']['tmp_name'];
            $resumeName = $_FILES['resume']['name'];
            $mail->addAttachment($resumeTmpName, $resumeName);
        }

        // Send email to admin
        $mail->send();

        // Clone the $mail object for the second email
        $mailUser = clone $mail;

        // Reset recipients and subject for user
        $mailUser->clearAddresses();
        $mailUser->clearCCs();
        $mailUser->clearBCCs();
        $mailUser->clearReplyTos();
        $mailUser->Subject = 'Thank you for contacting us';
        $mailUser->addAddress($userEmail);

        // Body for user (notification)
        $mailUser->Body = '
            <html>
            <head>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                    }
                    h2 {
                        color: #333;
                    }
                    p {
                        color: #666;
                    }
                </style>
            </head>
            <body>
                <h2>Thank you for contacting us</h2>
                <p>Dear ' . $_POST["name"] . ',</p>
                <p>Thank you for reaching out to us. We will get back to you soon.</p>
            </body>
            </html>
        ';

        // Send email to user
        $mailUser->send();

        echo "<script>alert('Sent successfully'); document.location.href = 'index.html';</script>";
    } catch (Exception $e) {
        echo "<script>alert('Mailer Error: " . $mail->ErrorInfo . "'); document.location.href = 'index.html';</script>";
    }
}
?>
