<?php
/**
 * Configuration de l'envoi d'emails
 * Modifiez ces valeurs selon votre compte email
 */

return [
    // ============================================
    // CONFIGURATION DE VOTRE EMAIL
    // ============================================
    
    // Votre adresse email (expéditeur)
    'from_email' => 'med.racem@gmail.com', // Email Gmail configuré
    'from_name' => 'AidConnect',
    
    // ============================================
    // CONFIGURATION SMTP
    // ============================================
    
    // Méthode d'envoi : 'smtp' ou 'mail'
    // 'smtp' = Utilise SMTP (recommandé, nécessite PHPMailer)
    // 'mail' = Utilise la fonction mail() de PHP (simple mais moins fiable)
    'method' => 'smtp', // Utilise SMTP avec PHPMailer (plus fiable)
    
    // Configuration SMTP (si method = 'smtp')
    'smtp' => [
        // Pour Gmail
        'host' => 'smtp.gmail.com',
        'port' => 587,
        'secure' => 'tls', // 'tls' ou 'ssl'
        'username' => 'med.racem@gmail.com', // Votre email Gmail
        'password' => 'ioitbpallxvodufp', // Mot de passe d'application Gmail (16 caractères sans espaces)
        
        // Pour Outlook/Hotmail, décommentez et utilisez :
        // 'host' => 'smtp-mail.outlook.com',
        // 'port' => 587,
        // 'secure' => 'tls',
        // 'username' => 'votre-email@outlook.com',
        // 'password' => 'votre-mot-de-passe',
        
        // Pour Yahoo, décommentez et utilisez :
        // 'host' => 'smtp.mail.yahoo.com',
        // 'port' => 587,
        // 'secure' => 'tls',
        // 'username' => 'votre-email@yahoo.com',
        // 'password' => 'votre-mot-de-passe',
    ],
    
    // ============================================
    // CONFIGURATION XAMPP (si method = 'mail')
    // ============================================
    // Si vous utilisez mail() de PHP, configurez dans php.ini :
    // [mail function]
    // SMTP = smtp.gmail.com
    // smtp_port = 587
    // sendmail_from = votre-email@gmail.com
];
