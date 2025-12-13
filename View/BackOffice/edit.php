<?php
/**
 * edit.php - Page de modification d'événement
 * Version SANS authentification admin
 */

session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../Model/Event.php';

// Initialiser la base de données
$database = new Database();
$db = $database->getConnection();
$eventModel = new Event($db);

// Vérifier qu'un ID est fourni
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = "❌ Événement introuvable - ID manquant";
    header('Location: index.php?controller=event&action=liste');
    exit;
}

$event_id = intval($_GET['id']);

// Traitement du formulaire de modification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validation des données
    $errors = [];
    
    // Titre
    if (empty($_POST['titre']) || strlen(trim($_POST['titre'])) < 5) {
        $errors[] = "Le titre doit contenir au moins 5 caractères";
    }
    
    // Description
    if (empty($_POST['description']) || strlen(trim($_POST['description'])) < 20) {
        $errors[] = "La description doit contenir au moins 20 caractères";
    }
    
    // Date
    if (empty($_POST['date_event'])) {
        $errors[] = "La date est obligatoire";
    } else {
        $date = strtotime($_POST['date_event']);
        if ($date < time()) {
            $errors[] = "La date doit être dans le futur";
        }
    }
    
    // Lieu
    if (empty($_POST['lieu']) || strlen(trim($_POST['lieu'])) < 3) {
        $errors[] = "Le lieu doit contenir au moins 3 caractères";
    }
    
    // Nombre de places
    if (empty($_POST['nombre_places']) || $_POST['nombre_places'] < 1) {
        $errors[] = "Le nombre de places doit être supérieur à 0";
    } elseif ($_POST['nombre_places'] > 1000) {
        $errors[] = "Le nombre de places ne peut pas dépasser 1000";
    }
    
    // Type
    if (empty($_POST['type_event'])) {
        $errors[] = "Le type d'événement est obligatoire";
    }
    
    // S'il n'y a pas d'erreurs, mettre à jour
    if (empty($errors)) {
        // Récupérer l'événement existant
        $eventModel->setIdEvent($event_id);
        if ($eventModel->readOne()) {
            $old_image = $eventModel->getImage();
            
            // Gestion de l'upload d'image
            $image = $old_image;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                $filename = $_FILES['image']['name'];
                $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                
                if (in_array($ext, $allowed)) {
                    if ($_FILES['image']['size'] <= 5 * 1024 * 1024) {
                        $newname = uniqid() . '.' . $ext;
                        $upload_dir = __DIR__ . '/uploads/events/';
                        
                        if (!file_exists($upload_dir)) {
                            mkdir($upload_dir, 0777, true);
                        }
                        
                        if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $newname)) {
                            // Supprimer l'ancienne image
                            if ($old_image && file_exists(__DIR__ . '/' . $old_image)) {
                                unlink(__DIR__ . '/' . $old_image);
                            }
                            $image = 'uploads/events/' . $newname;
                        }
                    } else {
                        $errors[] = "L'image ne doit pas dépasser 5MB";
                    }
                } else {
                    $errors[] = "Format d'image non valide";
                }
            }
            
            // Mettre à jour l'événement
            if (empty($errors)) {
                $eventModel->setTitre($_POST['titre']);
                $eventModel->setDescription($_POST['description']);
                $eventModel->setDateEvent($_POST['date_event']);
                $eventModel->setLieu($_POST['lieu']);
                $eventModel->setNombrePlaces($_POST['nombre_places']);
                $eventModel->setTypeEvent($_POST['type_event']);
                $eventModel->setImage($image);
                
                if ($eventModel->update()) {
                    $_SESSION['success'] = "✅ Événement modifié avec succès !";
                    header('Location: index.php?controller=event&action=liste');
                    exit;
                } else {
                    $errors[] = "Erreur lors de la modification dans la base de données";
                }
            }
        } else {
            $errors[] = "Événement introuvable dans la base de données (ID: $event_id)";
        }
    }
    
    // Stocker les erreurs
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        $_SESSION['old_data'] = $_POST;
    }
}

// Récupérer les données de l'événement
$eventModel->setIdEvent($event_id);
if (!$eventModel->readOne()) {
    $_SESSION['error'] = "❌ Événement introuvable dans la base de données (ID: $event_id)";
    header('Location: index.php?controller=event&action=liste');
    exit;
}

// Récupérer le nombre d'inscriptions
$inscriptions_count = $eventModel->countInscriptions();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier - <?php echo htmlspecialchars($eventModel->getTitre()); ?></title>
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
            max-width: 900px;
            margin: 0 auto;
        }
        
        .nav-links {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        
        .nav-link {
            padding: 10px 20px;
            background: white;
            border-radius: 8px;
            text-decoration: none;
            color: #333;
            transition: all 0.3s;
            font-weight: 500;
        }
        
        .nav-link:hover {
            background: rgba(255, 255, 255, 0.9);
            transform: translateY(-2px);
        }
        
        .card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            margin-bottom: 20px;
        }
        
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
        }
        
        .card-header h1 {
            font-size: 28px;
            margin-bottom: 8px;
        }
        
        .card-body {
            padding: 40px;
        }
        
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 500;
        }
        
        .alert-error {
            background: #fee;
            border: 1px solid #fcc;
            color: #c00;
        }
        
        .alert-error ul {
            margin: 10px 0 0 20px;
        }
        
        .alert-warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
        }
        
        .stats-box {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-around;
            text-align: center;
        }
        
        .stat-item {
            flex: 1;
        }
        
        .stat-value {
            font-size: 28px;
            font-weight: bold;
            color: #667eea;
        }
        
        .stat-label {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
            font-size: 15px;
        }
        
        .required {
            color: #dc3545;
        }
        
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 15px;
            font-family: inherit;
            transition: all 0.3s;
        }
        
        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .form-group textarea {
            resize: vertical;
            min-height: 120px;
        }
        
        .form-group small {
            display: block;
            margin-top: 5px;
            color: #666;
            font-size: 13px;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .image-preview {
            max-width: 100%;
            max-height: 200px;
            margin-top: 10px;
            border-radius: 8px;
            border: 2px solid #ddd;
        }
        
        .btn {
            padding: 14px 30px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
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
            transform: translateY(-2px);
        }
        
        .btn-info {
            background: #17a2b8;
            color: white;
        }
        
        .btn-info:hover {
            background: #138496;
            transform: translateY(-2px);
        }
        
        .form-actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            padding-top: 30px;
            border-top: 2px solid #eee;
            flex-wrap: wrap;
        }
        
        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .stats-box {
                flex-direction: column;
            }
            
            .stat-item {
                margin: 10px 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Navigation -->
        <div class="nav-links">
            <a href="index.php?controller=event&action=liste" class="nav-link">
                📅 Liste des événements
            </a>
            <a href="inscriptions_list.php?id_event=<?php echo $event_id; ?>" class="nav-link">
                👥 Voir les inscriptions
            </a>
        </div>
        
        <!-- Carte principale -->
        <div class="card">
            <div class="card-header">
                <h1>✏️ Modifier l'événement</h1>
                <p><?php echo htmlspecialchars($eventModel->getTitre()); ?></p>
            </div>
            
            <div class="card-body">
                <!-- Messages d'erreur -->
                <?php if (isset($_SESSION['errors'])): ?>
                    <div class="alert alert-error">
                        <strong>⚠️ Erreurs :</strong>
                        <ul>
                            <?php foreach ($_SESSION['errors'] as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php unset($_SESSION['errors']); ?>
                <?php endif; ?>
                
                <!-- Statistiques -->
                <div class="stats-box">
                    <div class="stat-item">
                        <div class="stat-value"><?php echo $eventModel->getNombrePlaces(); ?></div>
                        <div class="stat-label">Places totales</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value"><?php echo $inscriptions_count; ?></div>
                        <div class="stat-label">Inscrits</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">
                            <?php echo max(0, $eventModel->getNombrePlaces() - $inscriptions_count); ?>
                        </div>
                        <div class="stat-label">Places restantes</div>
                    </div>
                </div>
                
                <!-- Avertissement -->
                <?php if ($inscriptions_count > 0): ?>
                    <div class="alert alert-warning">
                        ⚠️ Cet événement a <strong><?php echo $inscriptions_count; ?> inscription(s)</strong>. 
                        Ne réduisez pas le nombre de places en dessous de ce nombre.
                    </div>
                <?php endif; ?>
                
                <!-- Formulaire -->
                <form method="POST" action="" enctype="multipart/form-data" novalidate>
                    
                    <!-- Titre -->
                    <div class="form-group">
                        <label for="titre">Titre <span class="required">*</span></label>
                        <input 
                            type="text" 
                            id="titre" 
                            name="titre" 
                            value="<?php echo htmlspecialchars($eventModel->getTitre()); ?>"
                            required>
                        <small>Minimum 5 caractères</small>
                    </div>
                    
                    <!-- Description -->
                    <div class="form-group">
                        <label for="description">Description <span class="required">*</span></label>
                        <textarea 
                            id="description" 
                            name="description" 
                            required><?php echo htmlspecialchars($eventModel->getDescription()); ?></textarea>
                        <small>Minimum 20 caractères</small>
                    </div>
                    
                    <!-- Date et Lieu -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="date_event">Date <span class="required">*</span></label>
                            <input 
                                type="datetime-local" 
                                id="date_event" 
                                name="date_event" 
                                value="<?php echo date('Y-m-d\TH:i', strtotime($eventModel->getDateEvent())); ?>"
                                required>
                        </div>
                        
                        <div class="form-group">
                            <label for="lieu">Lieu <span class="required">*</span></label>
                            <input 
                                type="text" 
                                id="lieu" 
                                name="lieu" 
                                value="<?php echo htmlspecialchars($eventModel->getLieu()); ?>"
                                required>
                        </div>
                    </div>
                    
                    <!-- Places et Type -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="nombre_places">Nombre de places <span class="required">*</span></label>
                            <input 
                                type="number" 
                                id="nombre_places" 
                                name="nombre_places" 
                                value="<?php echo $eventModel->getNombrePlaces(); ?>"
                                min="<?php echo $inscriptions_count; ?>"
                                max="1000"
                                required>
                            <small>Min: <?php echo $inscriptions_count; ?> - Max: 1000</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="type_event">Type <span class="required">*</span></label>
                            <select id="type_event" name="type_event" required>
                                <option value="">-- Sélectionnez --</option>
                                <option value="collecte" <?php echo $eventModel->getTypeEvent() == 'collecte' ? 'selected' : ''; ?>>Collecte</option>
                                <option value="distribution" <?php echo $eventModel->getTypeEvent() == 'distribution' ? 'selected' : ''; ?>>Distribution</option>
                                <option value="soutien" <?php echo $eventModel->getTypeEvent() == 'soutien' ? 'selected' : ''; ?>>Soutien médical</option>
                                <option value="logement" <?php echo $eventModel->getTypeEvent() == 'logement' ? 'selected' : ''; ?>>Aide au logement</option>
                                <option value="autre" <?php echo $eventModel->getTypeEvent() == 'autre' ? 'selected' : ''; ?>>Autre</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Image -->
                    <div class="form-group">
                        <label for="image">Image (optionnel)</label>
                        <input 
                            type="file" 
                            id="image" 
                            name="image" 
                            accept="image/*">
                        <small>Formats: JPEG, PNG, GIF - Max: 5MB</small>
                        
                        <?php if (!empty($eventModel->getImage()) && file_exists(__DIR__ . '/' . $eventModel->getImage())): ?>
                            <div style="margin-top: 15px;">
                                <strong>Image actuelle :</strong>
                                <img src="<?php echo htmlspecialchars($eventModel->getImage()); ?>" 
                                     alt="Image actuelle" 
                                     class="image-preview">
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Boutons -->
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            ✅ Enregistrer
                        </button>
                        <a href="index.php?controller=event&action=liste" class="btn btn-secondary">
                            ❌ Annuler
                        </a>
                        <a href="inscriptions_list.php?id_event=<?php echo $event_id; ?>" class="btn btn-info">
                            👥 Voir les inscriptions (<?php echo $inscriptions_count; ?>)
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
<script src="assets/js/inscription_validation.js"></script>
<?php unset($_SESSION['old_data']); ?>