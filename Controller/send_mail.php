<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

function sendVerificationEmail($email, $name, $token) {

    $mail = new PHPMailer(true);

    try {
        // SMTP GMAIL
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'youssefmamlouk72@gmail.com';
        $mail->Password   = 'mwdjeiyxkmsjaktp';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // FROM
        $mail->setFrom('youssefmamlouk72@gmail.com', 'AidConnect');

        // TO = USER
        $mail->addAddress($email, $name);

        $mail->isHTML(true);
        $mail->Subject = "Vérifiez votre email - AidConnect";

        // ADRESSE ACCESSIBLE PAR AUTRES APPAREILS DU RÉSEAU
        $serverIP = "192.168.1.196";
        $verifyUrl = "http://".$serverIP."/AidConnect/Controller/verify.php?token=$token";

        // HTML EMAIL
        $mail->Body = "
        <div style='font-family:Arial;padding:20px;'>
            <h2 style='color:#0d6efd;'>Bienvenue $name 👋</h2>
            <p>Merci de vous être inscrit sur <strong>AidConnect</strong>.</p>
            <p style='font-size:15px;'>Cliquez sur le bouton ci-dessous pour activer votre compte :</p>
            
            <a href='$verifyUrl' 
               style='background:#0d6efd;color:white;padding:12px 20px;
               text-decoration:none;border-radius:6px;font-size:16px;font-weight:bold;'>
               Vérifier mon compte
            </a>

            <p style='margin-top:30px;font-size:13px;color:#555;'>
                Si vous n'avez pas créé ce compte, ignorez simplement cet email.
            </p>
        </div>
        ";

        $mail->send();

    } catch (Exception $e) {
        error_log("Erreur envoi email: " . $mail->ErrorInfo);
    }
}
