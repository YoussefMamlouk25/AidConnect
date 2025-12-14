<?php
session_start();

require_once '../Model/user.php';
require_once '../Model/EmailService.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../View/frontoffice/verify_email.php');
    exit;
}

$email = trim($_POST['email'] ?? '');
$code = trim($_POST['code'] ?? '');
$action = $_POST['action'] ?? 'verify';

if (empty($email)) {
    header('Location: ../View/frontoffice/verify_email.php?error=' . urlencode('Email manquant.'));
    exit;
}

$userModel = new UserModel();

// Action : Vérifier le code
if ($action === 'verify') {
    if (empty($code)) {
        header('Location: ../View/frontoffice/verify_email.php?email=' . urlencode($email) . '&error=' . urlencode('Veuillez entrer le code de vérification.'));
        exit;
    }

    $result = $userModel->verifyEmail($email, $code);

    if ($result['success']) {
        // Email vérifié avec succès
        unset($_SESSION['verification_email']);
        unset($_SESSION['verification_name']);
        header('Location: ../View/frontoffice/login.php?success=' . urlencode('Email vérifié avec succès ! Vous pouvez maintenant vous connecter.'));
        exit;
    } else {
        header('Location: ../View/frontoffice/verify_email.php?email=' . urlencode($email) . '&error=' . urlencode($result['error'] ?? 'Erreur de vérification.'));
        exit;
    }
}

// Action : Renvoyer le code
if ($action === 'resend') {
    $result = $userModel->resendVerificationCode($email);

    if ($result['success']) {
        // Envoyer le nouveau code par email
        $emailService = new EmailService();
        $emailSent = $emailService->sendVerificationEmail($email, $result['name'], $result['verification_code']);

        $message = $emailSent
            ? 'Un nouveau code de vérification a été envoyé à votre email.'
            : 'Nouveau code : ' . $result['verification_code'] . ' (Email non envoyé - mode développement)';

        header('Location: ../View/frontoffice/verify_email.php?email=' . urlencode($email) . '&success=' . urlencode($message));
        exit;
    } else {
        header('Location: ../View/frontoffice/verify_email.php?email=' . urlencode($email) . '&error=' . urlencode($result['error'] ?? 'Erreur lors de l\'envoi du code.'));
        exit;
    }
}

header('Location: ../View/frontoffice/verify_email.php?error=' . urlencode('Action invalide.'));
exit;

