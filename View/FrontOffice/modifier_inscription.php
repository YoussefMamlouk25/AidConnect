<?php
/**
 * View/FrontOffice/modifier_inscription.php
 * ✏️ Formulaire pour modifier une inscription
 * ✅ CORRECTION: Chemins relatifs corrigés
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

// Traitement de la modification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];
    
    // Validation nom
    if (empty($_POST['nom']) || strlen(trim($_POST['nom'])) < 2) {
        $errors[] = "Le nom doit contenir au moins 2 caractères";
    } elseif (!preg_match('/^[a-zA-ZÀ-ÿ\s-]+$/', trim($_POST['nom']))) {
        $errors[] = "Le nom ne doit contenir que des lettres";
    }
    
    // Validation prénom
    if (empty($_POST['prenom']) || strlen(trim($_POST['prenom'])) < 2) {
        $errors[] = "Le prénom doit contenir au moins 2 caractères";
    } elseif (!preg_match('/^[a-zA-ZÀ-ÿ\s-]+$/', trim($_POST['prenom']))) {
        $errors[] = "Le prénom ne doit contenir que des lettres";
    }
    
    // Validation téléphone
    if (empty($_POST['telephone'])) {
        $errors[] = "Le téléphone est obligatoire";
    } elseif (!preg_match('/^[0-9]{8}$/', $_POST['telephone'])) {
        $errors[] = "Le téléphone doit contenir exactement 8 chiffres";
    }
    
    // Validation motivation
    if (empty($_POST['pourquoi']) || strlen(trim($_POST['pourquoi'])) < 20) {
        $errors[] = "La motivation doit contenir au moins 20 caractères";
    } elseif (strlen(trim($_POST['pourquoi'])) > 500) {
        $errors[] = "La motivation ne peut pas dépasser 500 caractères";
    }
    
    // Si pas d'erreurs, mettre à jour
    if (empty($errors)) {
        $inscriptionModel->setIdInscription($inscription_id);
        $inscriptionModel->setEmail($email);
        $inscriptionModel->setNom($_POST['nom']);
        $inscriptionModel->setPrenom($_POST['prenom']);
        $inscriptionModel->setTelephone($_POST['telephone']);
        $inscriptionModel->setPourquoi($_POST['pourquoi']);
        
        if ($inscriptionModel->update()) {
            $_SESSION['success'] = "✅ Inscription modifiée avec succès !";
            header('Location: mes_inscriptions.php?email=' . urlencode($email));
            exit;
        } else {
            $errors[] = "Erreur lors de la modification";
        }
    }
    
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
    }
}

// Récupérer l'inscription
$inscriptionModel->setIdInscription($inscription_id);
if (!$inscriptionModel->readOne()) {
    $_SESSION['error'] = "Inscription introuvable";
    header('Location: mes_inscriptions.php?email=' . urlencode($email));
    exit;
}

// Vérifier que l'email correspond
if ($inscriptionModel->getEmail() !== $email) {
    $_SESSION['error'] = "Vous n'avez pas l'autorisation de modifier cette inscription";
    header('Location: mes_inscriptions.php?email=' . urlencode($email));
    exit;
}

// Récupérer l'événement
$eventModel->setIdEvent($inscriptionModel->getIdEvent());
$eventModel->readOne();

// Vérifier si l'événement est passé
$event_date = strtotime($eventModel->getDateEvent());
$is_past = $event_date < time();

if ($is_past) {
    $_SESSION['error'] = "Impossible de modifier une inscription pour un événement passé";
    header('Location: mes_inscriptions.php?email=' . urlencode($email));
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier mon inscription</title>
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
            padding: 20px;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .header {
            background: white;
            padding: 30px;
            border-radius: 12px 12px 0 0;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .header h1 {
            color: #333;
            margin-bottom: 10px;
        }
        
        .event-info {
            background: #f8f9fa;
            padding: 20px;
            margin: 20px 0;
            border-left: 4px solid #667eea;
            border-radius: 5px;
        }
        
        .event-info h2 {
            color: #667eea;
            margin-bottom: 15px;
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
        
        .form-container {
            background: white;
            padding: 30px;
            border-radius: 0 0 12px 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .alert-error {
            background: #fee;
            border: 1px solid #fcc;
            color: #c00;
        }
        
        .alert-error ul {
            margin: 10px 0 0 20px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }
        
        .required {
            color: #e74c3c;
        }
        
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #dee2e6;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s;
        }
        
        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .form-group input.error,
        .form-group textarea.error {
            border-color: #e74c3c !important;
        }
        
        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .btn {
            display: inline-block;
            padding: 14px 30px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
        }
        
        .btn-primary {
            background: #28a745;
            color: white;
        }
        
        .btn-primary:hover {
            background: #218838;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
        }
        
        .form-actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }
        
        .field-error {
            display: block;
            color: #e74c3c;
            font-size: 0.85rem;
            margin-top: 5px;
            font-weight: 600;
        }
        
        .char-counter {
            text-align: right;
            font-size: 0.9em;
            margin-top: 5px;
            font-weight: 600;
        }
        
        .info-box {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px 20px;
            margin: 20px 0;
            border-radius: 5px;
        }
        
        .info-box strong {
            color: #856404;
            display: block;
            margin-bottom: 8px;
        }
        
        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .form-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✏️ Modifier mon inscription</h1>
            <p>Modifiez vos informations pour cet événement</p>
        </div>
        
        <!-- Informations sur l'événement -->
        <div class="event-info">
            <h2><?php echo htmlspecialchars($eventModel->getTitre()); ?></h2>
            
            <div class="event-detail">
                <span class="label">📅 Date :</span>
                <?php echo date('d/m/Y à H:i', strtotime($eventModel->getDateEvent())); ?>
            </div>
            
            <div class="event-detail">
                <span class="label">📍 Lieu :</span>
                <?php echo htmlspecialchars($eventModel->getLieu()); ?>
            </div>
        </div>
        
        <div class="form-container">
            <!-- Messages d'erreur -->
            <?php if(isset($_SESSION['errors'])): ?>
                <div class="alert alert-error">
                    <strong>⚠️ Erreurs de validation :</strong>
                    <ul>
                        <?php foreach($_SESSION['errors'] as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php unset($_SESSION['errors']); ?>
            <?php endif; ?>
            
            <div class="info-box">
                <strong>ℹ️ Information importante</strong>
                Vous pouvez modifier vos informations personnelles et votre motivation. L'email ne peut pas être modifié.
            </div>
            
            <form id="edit-inscription-form" method="POST" novalidate>
                <div class="form-row">
                    <div class="form-group">
                        <label for="nom">
                            Nom <span class="required">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="nom" 
                            name="nom" 
                            value="<?php echo htmlspecialchars($inscriptionModel->getNom()); ?>"
                            required>
                    </div>
                    
                    <div class="form-group">
                        <label for="prenom">
                            Prénom <span class="required">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="prenom" 
                            name="prenom" 
                            value="<?php echo htmlspecialchars($inscriptionModel->getPrenom()); ?>"
                            required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="email">
                        Email (non modifiable)
                    </label>
                    <input 
                        type="email" 
                        id="email" 
                        value="<?php echo htmlspecialchars($inscriptionModel->getEmail()); ?>"
                        disabled
                        style="background: #f8f9fa; cursor: not-allowed;">
                </div>
                
                <div class="form-group">
                    <label for="telephone">
                        Téléphone <span class="required">*</span>
                    </label>
                    <input 
                        type="tel" 
                        id="telephone" 
                        name="telephone" 
                        value="<?php echo htmlspecialchars($inscriptionModel->getTelephone()); ?>"
                        maxlength="8"
                        pattern="[0-9]{8}"
                        required>
                    <small style="color: #666; font-size: 0.85rem;">
                        📱 Format : 8 chiffres uniquement
                    </small>
                </div>
                
                <div class="form-group">
                    <label for="pourquoi">
                        Pourquoi souhaitez-vous participer ? <span class="required">*</span>
                    </label>
                    <textarea 
                        id="pourquoi" 
                        name="pourquoi" 
                        minlength="20"
                        maxlength="500"
                        required><?php echo htmlspecialchars($inscriptionModel->getPourquoi()); ?></textarea>
                    <small style="color: #666; font-size: 0.85rem;">
                        ✏️ Minimum 20 caractères, maximum 500 caractères
                    </small>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        ✅ Enregistrer les modifications
                    </button>
                    <a href="mes_inscriptions.php?email=<?php echo urlencode($email); ?>" class="btn btn-secondary">
                        ❌ Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
<!-- À ajouter avant </body> dans CHAQUE page -->
<script src="assets/js/notifications.js"></script>
<script src="assets/js/confirmation-handler.js"></script>
<script src="assets/js/inscription_validation.js"></script></body>
</html>