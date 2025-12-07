<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

$mail = new PHPMailer(true);

try {

    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;

    // IMPORTANT → Username = ton vrai mail EXACT
    $mail->Username   = 'youssefmamlouk72@gmail.com';

    // IMPORTANT → Mot de passe applicatif SANS ESPACES
    $mail->Password   = 'mwdjeiyxkmsjaktp';

    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // FROM = ton mail aussi
    $mail->setFrom('youssefmamlouk72@gmail.com', 'TEST');
    $mail->addAddress('youssefmamlouk72@gmail.com');

    $mail->isHTML(true);
    $mail->Subject = "Test PHPMailer";
    $mail->Body    = "Si tu vois ce message, l'envoi fonctionne.";

    $mail->send();
    echo "MAIL OK ✔";

} catch (Exception $e) {
    echo "ERREUR : " . $mail->ErrorInfo;
}
