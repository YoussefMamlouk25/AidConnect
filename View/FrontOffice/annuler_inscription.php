<?php
/**
 * View/FrontOffice/annuler_inscription.php
 * 🗑️ Page pour annuler (supprimer) une inscription
 * ✅ CORRECTION COMPLÈTE - Redirection avec success=inscription_cancelled
 */

session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../Model/Inscription.php';
require_once __DIR__ . '/../../Model/Event.php';

// Vérifier les paramètres
if (!isset($_GET['id']) || !isset($_GET['email'])) {
    $_SESSION['error'] = "Paramètres manquants";
    header('Location: mes_inscriptions.php');
    exit;
}

$inscription_id = intval($_GET['id']);
$email = $_GET['email'];

// Initialiser la base de données
$database = new Database();
$db = $database->getConnection();
$inscriptionModel = new Inscription($db);
$eventModel = new Event($db);

// Récupérer l'inscription
$inscriptionModel->setIdInscription($inscription_id);
if (!$inscriptionModel->readOne()) {
    $_SESSION['error'] = "Inscription introuvable";
    header('Location: mes_inscriptions.php?email=' . urlencode($email));
    exit;
}

// Vérifier que l'email correspond
if ($inscriptionModel->getEmail() !== $email) {
    $_SESSION['error'] = "Vous n'avez pas l'autorisation de supprimer cette inscription";
    header('Location: mes_inscriptions.php?email=' . urlencode($email));
    exit;
}

// Récupérer l'événement
$eventModel->setIdEvent($inscriptionModel->getIdEvent());
$eventModel->readOne();

// Vérifier si l'événement est passé
$event_date = strtotime($eventModel->getDateEvent());
$is_past = $event_date < time();

// Traitement de la suppression
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['confirm']) && $_POST['confirm'] === 'yes') {
        $inscriptionModel->setIdInscription($inscription_id);
        
        if ($inscriptionModel->delete()) {
            $_SESSION['success'] = "✅ Votre inscription a été annulée avec succès";
            // ✅ CORRECTION: Ajouter le paramètre success=inscription_cancelled
            header('Location: mes_inscriptions.php?email=' . urlencode($email) . '&success=inscription_cancelled');
            exit;
        } else {
            $_SESSION['error'] = "❌ Erreur lors de l'annulation de l'inscription";
        }
    } else {
        header('Location: mes_inscriptions.php?email=' . urlencode($email));
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Annuler mon inscription</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .container {
            max-width: 600px;
            width: 100%;
        }
        
        .confirmation-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        
        .card-header {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .card-header .icon {
            font-size: 60px;
            margin-bottom: 10px;
        }
        
        .card-header h1 {
            font-size: 24px;
            margin-bottom: 10px;
        }
        
        .card-body {
            padding: 30px;
        }
        
        .warning-box {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 20px;
            margin-bottom: 25px;
            border-radius: 5px;
        }
        
        .warning-box h3 {
            color: #856404;
            margin-bottom: 10px;
        }
        
        .warning-box ul {
            margin-left: 20px;
            color: #856404;
        }
        
        .warning-box li {
            margin: 8px 0;
        }
        
        .event-info {
            background: #f8f9fa;
            padding: 20px;
            margin: 20px 0;
            border-left: 4px solid #667eea;
            border-radius: 5px;
        }
        
        .event-info h2 {
            color: #333;
            margin-bottom: 15px;
            font-size: 18px;
        }
        
        .event-detail {
            margin: 10px 0;
            padding: 8px 0;
            border-bottom: 1px solid #dee2e6;
        }
        
        .event-detail:last-child {
            border-bottom: none;
        }
        
        .label {
            font-weight: 600;
            color: #555;
        }
        
        .past-event-notice {
            background: #f8d7da;
            border-left: 4px solid #dc3545;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            color: #721c24;
        }
        
        .form-actions {
            display: flex;
            gap: 15px;
            margin-top: 25px;
        }
        
        .btn {
            flex: 1;
            padding: 15px 30px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        
        .btn-danger:hover {
            background: #c82333;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.3);
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }
        
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .alert-error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        
        @media (max-width: 768px) {
            .form-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="confirmation-card">
            <div class="card-header">
                <div class="icon">⚠️</div>
                <h1>Confirmer l'annulation</h1>
                <p>Vous êtes sur le point d'annuler votre inscription</p>
            </div>
            
            <div class="card-body">
                <!-- Messages d'erreur -->
                <?php if(isset($_SESSION['error'])): ?>
                    <div class="alert alert-error">
                        <?php echo htmlspecialchars($_SESSION['error']); ?>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>
                
                <?php if ($is_past): ?>
                    <div class="past-event-notice">
                        <strong>⚠️ Événement passé</strong><br>
                        Cet événement a déjà eu lieu. Vous ne pouvez pas annuler cette inscription.
                    </div>
                    
                    <div class="form-actions">
                        <a href="mes_inscriptions.php?email=<?php echo urlencode($email); ?>" class="btn btn-secondary">
                            ← Retour à mes inscriptions
                        </a>
                    </div>
                <?php else: ?>
                    <!-- Avertissement -->
                    <div class="warning-box">
                        <h3>⚠️ Attention !</h3>
                        <p>En annulant cette inscription :</p>
                        <ul>
                            <li>Votre place sera libérée pour d'autres participants</li>
                            <li>Vos informations seront supprimées définitivement</li>
                            <li>Vous devrez vous réinscrire si vous changez d'avis</li>
                            <li>Vous ne recevrez plus de notifications pour cet événement</li>
                        </ul>
                    </div>
                    
                    <!-- Informations sur l'événement -->
                    <div class="event-info">
                        <h2>📅 Événement concerné</h2>
                        
                        <div class="event-detail">
                            <span class="label">Titre :</span>
                            <?php echo htmlspecialchars($eventModel->getTitre()); ?>
                        </div>
                        
                        <div class="event-detail">
                            <span class="label">📅 Date :</span>
                            <?php echo date('d/m/Y à H:i', strtotime($eventModel->getDateEvent())); ?>
                        </div>
                        
                        <div class="event-detail">
                            <span class="label">📍 Lieu :</span>
                            <?php echo htmlspecialchars($eventModel->getLieu()); ?>
                        </div>
                        
                        <div class="event-detail">
                            <span class="label">👤 Inscrit sous le nom de :</span>
                            <?php echo htmlspecialchars($inscriptionModel->getNom() . ' ' . $inscriptionModel->getPrenom()); ?>
                        </div>
                    </div>
                    
                    <!-- Formulaire de confirmation -->
                    <form method="POST">
                        <div class="form-actions">
                            <button type="submit" name="confirm" value="yes" class="btn btn-danger">
                                🗑️ Oui, annuler mon inscription
                            </button>
                            <a href="mes_inscriptions.php?email=<?php echo urlencode($email); ?>" class="btn btn-secondary">
                                ❌ Non, conserver mon inscription
                            </a>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Scripts JS -->
    <script src="../../assets/js/notifications.js"></script>
</body>
</html>