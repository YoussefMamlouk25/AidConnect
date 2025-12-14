<?php
/**
 * Fichier d'exemple de configuration email
 * Copiez ce fichier en EmailConfig.php et modifiez les valeurs
 * 
 * Commande : cp EmailConfig.example.php EmailConfig.php
 */

return [
    // ============================================
    // CONFIGURATION DE VOTRE EMAIL
    // ============================================
    
    // Votre adresse email (expéditeur)
    'from_email' => 'votre-email@gmail.com', // ⚠️ CHANGEZ ICI avec votre email
    'from_name' => 'AidConnect',
    
    // ============================================
    // CONFIGURATION SMTP
    // ============================================
    
    // Méthode d'envoi : 'smtp' ou 'mail'
    // 'smtp' = Utilise SMTP (recommandé, nécessite PHPMailer)
    // 'mail' = Utilise la fonction mail() de PHP (simple mais moins fiable)
    'method' => 'mail', // Changez en 'smtp' si vous installez PHPMailer
    
    // Configuration SMTP (si method = 'smtp')
    'smtp' => [
        // Pour Gmail
        'host' => 'smtp.gmail.com',
        'port' => 587,
        'secure' => 'tls', // 'tls' ou 'ssl'
        'username' => 'votre-email@gmail.com', // ⚠️ CHANGEZ ICI
        'password' => 'votre-mot-de-passe-app', // ⚠️ CHANGEZ ICI (mot de passe d'application pour Gmail)
        
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

