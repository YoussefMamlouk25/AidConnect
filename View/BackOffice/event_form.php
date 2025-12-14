<?php
/**
 * View/BackOffice/event_form.php
 * ✅ CORRECTION: Scripts JS et validation fonctionnels
 */

$isEdit = isset($event) && $event->getIdEvent();
$pageTitle = $isEdit ? 'Modifier l\'événement' : 'Créer un nouvel événement';
$submitAction = $isEdit ? 'update' : 'store';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - BackOffice</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
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
        }
        
        .nav-link {
            padding: 10px 20px;
            background: white;
            border-radius: 8px;
            text-decoration: none;
            color: #333;
            transition: all 0.3s;
        }
        
        .nav-link:hover {
            background: #667eea;
            color: white;
        }
        
        .header {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 25px;
        }
        
        .header h1 {
            color: #333;
            margin-bottom: 10px;
        }
        
        .form-container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
        }
        
        .required {
            color: #dc3545;
        }
        
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            font-family: Arial, sans-serif;
            transition: border-color 0.3s;
        }
        
        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .form-group input.error,
        .form-group textarea.error,
        .form-group select.error {
            border-color: #dc3545 !important;
            animation: shake 0.3s;
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
        
        .form-group textarea {
            resize: vertical;
            min-height: 120px;
        }
        
        .form-group small {
            display: block;
            margin-top: 5px;
            color: #666;
            font-size: 14px;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .btn {
            padding: 14px 30px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
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
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
        }
        
        .form-actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            padding-top: 30px;
            border-top: 2px solid #eee;
        }
        
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            animation: slideIn 0.3s;
        }
        
        @keyframes slideIn {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        .alert-success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        
        .alert-error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        
        .alert-error ul {
            margin: 10px 0 0 20px;
        }
        
        .field-error {
            display: block;
            color: #dc3545;
            font-size: 14px;
            margin-top: 5px;
            font-weight: 600;
        }
        
        .image-preview {
            max-width: 300px;
            max-height: 200px;
            margin-top: 10px;
            border-radius: 8px;
            border: 2px solid #ddd;
        }
        
        .info-box {
            background: #e7f3ff;
            border-left: 4px solid #2196f3;
            padding: 15px;
            margin-bottom: 25px;
            border-radius: 5px;
        }
        
        .info-box h3 {
            color: #1976d2;
            margin-bottom: 10px;
        }
        
        .info-box ul {
            margin-left: 20px;
        }
        
        .info-box li {
            margin: 5px 0;
            color: #333;
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
        <!-- Navigation -->
        <div class="nav-links">
            <a href="index.php?controller=event&action=manage" class="nav-link">📅 Gestion Événements</a>
            <a href="index.php?controller=inscription&action=liste" class="nav-link">📋 Inscriptions</a>
        </div>
        
        <!-- En-tête -->
        <div class="header">
            <h1><?php echo $isEdit ? '✏️' : '➕'; ?> <?php echo $pageTitle; ?></h1>
            <p><?php echo $isEdit ? 'Modifiez les informations de l\'événement' : 'Créez un nouvel événement caritatif'; ?></p>
        </div>
        
        <!-- Messages serveur -->
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
        
        <!-- Formulaire -->
        <div class="form-container">
            <div class="info-box">
                <h3>ℹ️ Informations importantes</h3>
                <ul>
                    <li>Tous les champs marqués d'un <span class="required">*</span> sont obligatoires</li>
                    <li>L'événement sera immédiatement visible sur le site après création</li>
                    <li>Les participants pourront s'inscrire dès la publication</li>
                </ul>
            </div>
            
            <form id="event-form" method="POST" 
                  action="index.php?controller=event&action=<?php echo $submitAction; ?>" 
                  enctype="multipart/form-data" novalidate>
                
                <?php if($isEdit): ?>
                    <input type="hidden" name="id_event" value="<?php echo $event->getIdEvent(); ?>">
                <?php endif; ?>
                
                <!-- Titre -->
                <div class="form-group">
                    <label for="titre">
                        Titre de l'événement <span class="required">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="titre" 
                        name="titre" 
                        value="<?php echo htmlspecialchars($isEdit ? $event->getTitre() : ($_SESSION['old_data']['titre'] ?? '')); ?>"
                        placeholder="Ex: Distribution de repas pour les sans-abris"
                        required>
                    <small>Minimum 5 caractères - Soyez clair et descriptif</small>
                </div>
                
                <!-- Description -->
                <div class="form-group">
                    <label for="description">
                        Description <span class="required">*</span>
                    </label>
                    <textarea 
                        id="description" 
                        name="description" 
                        placeholder="Décrivez en détail l'événement, ses objectifs, ce qui est prévu..."
                        required><?php echo htmlspecialchars($isEdit ? $event->getDescription() : ($_SESSION['old_data']['description'] ?? '')); ?></textarea>
                    <small>Minimum 20 caractères - Expliquez clairement l'objectif de l'événement</small>
                </div>
                
                <!-- Date et Lieu -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="date_event">
                            Date de l'événement <span class="required">*</span>
                        </label>
                        <input 
                            type="datetime-local" 
                            id="date_event" 
                            name="date_event" 
                            value="<?php echo $isEdit ? date('Y-m-d\TH:i', strtotime($event->getDateEvent())) : ($_SESSION['old_data']['date_event'] ?? ''); ?>"
                            required>
                        <small>La date doit être dans le futur</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="lieu">
                            Lieu <span class="required">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="lieu" 
                            name="lieu" 
                            value="<?php echo htmlspecialchars($isEdit ? $event->getLieu() : ($_SESSION['old_data']['lieu'] ?? '')); ?>"
                            placeholder="Ex: 123 Rue de la République, Paris"
                            required>
                        <small>Minimum 3 caractères - Adresse complète</small>
                    </div>
                </div>
                
                <!-- Nombre de places et Type -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="nombre_places">
                            Nombre de places <span class="required">*</span>
                        </label>
                        <input 
                            type="number" 
                            id="nombre_places" 
                            name="nombre_places" 
                            value="<?php echo $isEdit ? $event->getNombrePlaces() : ($_SESSION['old_data']['nombre_places'] ?? ''); ?>"
                            min="1" 
                            max="1000"
                            placeholder="Ex: 50"
                            required>
                        <small>Entre 1 et 1000 places</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="type_event">
                            Type d'événement <span class="required">*</span>
                        </label>
                        <select id="type_event" name="type_event" required>
                            <option value="">-- Sélectionnez un type --</option>
                            <option value="collecte" <?php echo ($isEdit && $event->getTypeEvent() == 'collecte') || ($_SESSION['old_data']['type_event'] ?? '') == 'collecte' ? 'selected' : ''; ?>>Collecte de nourriture</option>
                            <option value="distribution" <?php echo ($isEdit && $event->getTypeEvent() == 'distribution') || ($_SESSION['old_data']['type_event'] ?? '') == 'distribution' ? 'selected' : ''; ?>>Distribution de repas</option>
                            <option value="soutien" <?php echo ($isEdit && $event->getTypeEvent() == 'soutien') || ($_SESSION['old_data']['type_event'] ?? '') == 'soutien' ? 'selected' : ''; ?>>Soutien médical</option>
                            <option value="logement" <?php echo ($isEdit && $event->getTypeEvent() == 'logement') || ($_SESSION['old_data']['type_event'] ?? '') == 'logement' ? 'selected' : ''; ?>>Aide au logement</option>
                            <option value="autre" <?php echo ($isEdit && $event->getTypeEvent() == 'autre') || ($_SESSION['old_data']['type_event'] ?? '') == 'autre' ? 'selected' : ''; ?>>Autre</option>
                        </select>
                    </div>
                </div>
                
                <!-- Image -->
                <div class="form-group">
                    <label for="image">
                        Image de l'événement (optionnel)
                    </label>
                    <input 
                        type="file" 
                        id="image" 
                        name="image" 
                        accept="image/jpeg,image/jpg,image/png,image/gif">
                    <small>Formats acceptés : JPEG, PNG, GIF - Taille max : 5MB</small>
                    
                    <?php if($isEdit && !empty($event->getImage())): ?>
                        <img src="<?php echo htmlspecialchars($event->getImage()); ?>" 
                             alt="Image actuelle" 
                             class="image-preview"
                             id="current-image">
                        <small style="display: block; margin-top: 5px;">Image actuelle (sera remplacée si vous en uploadez une nouvelle)</small>
                    <?php endif; ?>
                </div>
                
                <!-- Boutons d'action -->
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <?php echo $isEdit ? '✅ Enregistrer les modifications' : '➕ Créer l\'événement'; ?>
                    </button>
                    <a href="index.php?controller=event&action=manage" class="btn btn-secondary">❌ Annuler</a>
                </div>
            </form>
        </div>
    </div>
    
    <!-- ✅ SCRIPT DE VALIDATION INLINE -->
    // Remplacer tout le script de validation par ce code corrigé

<!-- ✅ SCRIPT DE VALIDATION INLINE CORRIGÉ -->
<!-- À ajouter avant </body> dans CHAQUE page -->
<script src="assets/js/inscription_validation.js"></script>
</body>
</html>

<?php 
// Nettoyer les anciennes données de session
if (isset($_SESSION['old_data'])) {
    unset($_SESSION['old_data']);
}