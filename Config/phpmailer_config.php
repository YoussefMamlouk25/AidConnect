<?php
/**
 * Configuration PHPMailer
 * Fichier de configuration centralisé pour l'envoi d'emails
 */

// Configuration SMTP
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_SECURE', 'tls'); // 'tls' ou 'ssl'
define('SMTP_AUTH', true);

// Identifiants SMTP (⚠️ À CONFIGURER)
define('SMTP_USERNAME', 'votre-email@gmail.com');
define('SMTP_PASSWORD', 'votre-mot-de-passe-app'); // Mot de passe d'application Google

// Expéditeur par défaut
define('SMTP_FROM_EMAIL', 'votre-email@gmail.com');
define('SMTP_FROM_NAME', 'ProjetWeb Events');

// Options
define('SMTP_DEBUG', 0); // 0 = pas de debug, 1 = messages client, 2 = messages client et serveur
define('SMTP_CHARSET', 'UTF-8');

/**
 * 📌 INSTRUCTIONS POUR CONFIGURER GMAIL :
 * 
 * 1. Activer la validation en 2 étapes sur votre compte Google
 * 2. Aller sur : https://myaccount.google.com/apppasswords
 * 3. Créer un mot de passe d'application
 * 4. Copier le mot de passe (16 caractères)
 * 5. Remplacer SMTP_PASSWORD ci-dessus
 * 
 * ⚠️ NE JAMAIS commiter ce fichier avec vos vrais identifiants !
 * Utilisez plutôt des variables d'environnement en production.
 */

// Validation de la configuration
function validateSmtpConfig() {
    $required = [
        'SMTP_HOST' => SMTP_HOST,
        'SMTP_USERNAME' => SMTP_USERNAME,
        'SMTP_PASSWORD' => SMTP_PASSWORD
    ];
    
    foreach ($required as $key => $value) {
        if (empty($value) || strpos($value, 'votre-') !== false) {
            error_log("⚠️ Configuration PHPMailer incomplète : $key non configuré");
            return false;
        }
    }
    
    return true;
}
?>