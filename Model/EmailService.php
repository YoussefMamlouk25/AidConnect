<?php
/**
 * Service d'envoi d'emails pour AidConnect
 * Supporte SMTP (avec PHPMailer) et mail() de PHP
 */

class EmailService
{
    private $fromEmail;
    private $fromName;
    private $config;
    private $useSMTP = false;

    public function __construct()
    {
        // Charger la configuration
        $configFile = __DIR__ . '/EmailConfig.php';
        if (file_exists($configFile)) {
            $this->config = require $configFile;
        } else {
            // Configuration par défaut si le fichier n'existe pas
            $this->config = [
                'from_email' => 'votre-email@gmail.com',
                'from_name' => 'AidConnect',
                'method' => 'mail',
                'smtp' => []
            ];
        }
        
        // Configuration de l'expéditeur
        $this->fromEmail = $this->config['from_email'];
        $this->fromName = $this->config['from_name'];
        
        // Vérifier si on doit utiliser SMTP
        if ($this->config['method'] === 'smtp') {
            $this->useSMTP = $this->checkPHPMailer();
        }
    }
    
    /**
     * Vérifie si PHPMailer est disponible
     */
    private function checkPHPMailer()
    {
        // Si déjà chargé, retourner true
        if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
            return true;
        }
        
        // Vérifier si PHPMailer est installé via Composer
        $composerPath = __DIR__ . '/../vendor/autoload.php';
        if (file_exists($composerPath)) {
            require_once $composerPath;
            if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
                return true;
            }
        }
        
        // Vérifier si PHPMailer est dans vendor/phpmailer/phpmailer/src/
        $phpmailerPath = __DIR__ . '/../vendor/phpmailer/phpmailer/src/PHPMailer.php';
        if (file_exists($phpmailerPath)) {
            require_once $phpmailerPath;
            require_once __DIR__ . '/../vendor/phpmailer/phpmailer/src/Exception.php';
            require_once __DIR__ . '/../vendor/phpmailer/phpmailer/src/SMTP.php';
            if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
                return true;
            }
        }
        
        // Vérifier si PHPMailer est dans PHPMailer-master/src/ (extraction manuelle)
        $phpmailerMasterPath = __DIR__ . '/../PHPMailer-master/src/PHPMailer.php';
        if (file_exists($phpmailerMasterPath)) {
            require_once $phpmailerMasterPath;
            require_once __DIR__ . '/../PHPMailer-master/src/Exception.php';
            require_once __DIR__ . '/../PHPMailer-master/src/SMTP.php';
            if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Envoie un email de vérification avec un code
     * 
     * @param string $toEmail Email du destinataire
     * @param string $name Nom du destinataire
     * @param string $verificationCode Code de vérification (6 chiffres)
     * @return bool True si l'email est envoyé avec succès
     */
    public function sendVerificationEmail($toEmail, $name, $verificationCode)
    {
        if ($this->useSMTP) {
            return $this->sendWithSMTP($toEmail, $name, $verificationCode);
        } else {
            return $this->sendWithMailFunction($toEmail, $name, $verificationCode);
        }
    }
    
    /**
     * Envoie un email via SMTP (PHPMailer)
     */
    private function sendWithSMTP($toEmail, $name, $verificationCode)
    {
        // S'assurer que PHPMailer est chargé
        if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
            $this->checkPHPMailer();
        }
        
        // Si PHPMailer n'est toujours pas disponible, utiliser mail()
        if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
            error_log("PHPMailer non disponible, utilisation de mail()");
            return $this->sendWithMailFunction($toEmail, $name, $verificationCode);
        }
        
        $mail = null;
        try {
            $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
            
            // Configuration SMTP
            $mail->isSMTP();
            $mail->Host = $this->config['smtp']['host'];
            $mail->SMTPAuth = true;
            $mail->Username = $this->config['smtp']['username'];
            $mail->Password = $this->config['smtp']['password'];
            $mail->SMTPSecure = $this->config['smtp']['secure'];
            $mail->Port = $this->config['smtp']['port'];
            $mail->CharSet = 'UTF-8';
            $mail->Encoding = 'base64';
            
            // Timeout pour éviter les blocages
            $mail->Timeout = 30;
            $mail->SMTPKeepAlive = false;
            
            // Expéditeur
            $mail->setFrom($this->fromEmail, $this->fromName);
            $mail->addReplyTo($this->fromEmail, $this->fromName);
            
            // Destinataire
            $mail->addAddress($toEmail, $name);
            
            // Contenu
            $mail->isHTML(true);
            $mail->Subject = 'Vérification de votre compte AidConnect';
            $mail->Body = $this->getVerificationEmailTemplate($name, $verificationCode);
            $mail->AltBody = "Bonjour $name,\n\nVotre code de vérification est : $verificationCode\n\nCe code est valide pendant 15 minutes.";
            
            // Envoyer
            $mail->send();
            return true;
            
        } catch (\PHPMailer\PHPMailer\Exception $e) {
            $errorInfo = $mail ? $mail->ErrorInfo : $e->getMessage();
            $errorMsg = "Erreur PHPMailer: $errorInfo";
            error_log($errorMsg);
            
            // Afficher plus de détails pour le debugging
            if ($mail) {
                error_log("SMTP Debug: " . $mail->getSMTPInstance()->getError()['error']);
            }
            
            // Ne pas utiliser mail() en fallback, retourner false pour voir l'erreur
            throw new Exception($errorMsg);
        } catch (Exception $e) {
            $errorMsg = "Erreur envoi email: " . $e->getMessage();
            error_log($errorMsg);
            throw new Exception($errorMsg);
        }
    }
    
    /**
     * Envoie un email via la fonction mail() de PHP
     */
    private function sendWithMailFunction($toEmail, $name, $verificationCode)
    {
        $subject = 'Vérification de votre compte AidConnect';
        $message = $this->getVerificationEmailTemplate($name, $verificationCode);
        
        $headers = [
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=UTF-8',
            'From: ' . $this->fromName . ' <' . $this->fromEmail . '>',
            'Reply-To: ' . $this->fromEmail,
            'X-Mailer: PHP/' . phpversion()
        ];

        return @mail($toEmail, $subject, $message, implode("\r\n", $headers));
    }

    /**
     * Génère le template HTML de l'email de vérification
     */
    private function getVerificationEmailTemplate($name, $code)
    {
        return "
<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Vérification Email</title>
</head>
<body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;'>
    <div style='background: linear-gradient(135deg, #52a0f2, #1a75e8); padding: 30px; text-align: center; border-radius: 10px 10px 0 0;'>
        <h1 style='color: white; margin: 0;'>AidConnect</h1>
    </div>
    
    <div style='background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; border: 1px solid #e0e0e0;'>
        <h2 style='color: #1a75e8; margin-top: 0;'>Bonjour " . htmlspecialchars($name) . ",</h2>
        
        <p>Merci de vous être inscrit sur AidConnect ! Pour activer votre compte, veuillez utiliser le code de vérification ci-dessous :</p>
        
        <div style='background: white; border: 2px dashed #1a75e8; padding: 20px; text-align: center; margin: 30px 0; border-radius: 8px;'>
            <div style='font-size: 36px; font-weight: bold; color: #1a75e8; letter-spacing: 8px; font-family: monospace;'>
                " . htmlspecialchars($code) . "
            </div>
        </div>
        
        <p style='color: #666; font-size: 14px;'>
            <strong>⚠️ Important :</strong> Ce code est valide pendant <strong>15 minutes</strong> uniquement.
        </p>
        
        <p style='color: #666; font-size: 14px;'>
            Si vous n'avez pas créé de compte sur AidConnect, veuillez ignorer cet email.
        </p>
        
        <hr style='border: none; border-top: 1px solid #e0e0e0; margin: 30px 0;'>
        
        <p style='color: #999; font-size: 12px; text-align: center; margin: 0;'>
            © " . date('Y') . " AidConnect. Tous droits réservés.
        </p>
    </div>
</body>
</html>";
    }

    /**
     * Génère un code de vérification aléatoire de 6 chiffres
     * 
     * @return string Code de 6 chiffres
     */
    public static function generateVerificationCode()
    {
        return str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
    }
}

