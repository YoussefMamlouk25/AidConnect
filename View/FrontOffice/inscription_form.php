<!-- View/FrontOffice/inscription_form.php - VERSION FINALE -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - <?php echo htmlspecialchars($this->event->getTitre()); ?></title>
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
            animation: shake 0.3s;
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
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
            background: #667eea;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
        }
        
        .btn:hover {
            background: #5568d3;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }
        
        .btn-secondary {
            background: #6c757d;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
        }
        
        .form-actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }
        
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            animation: slideIn 0.3s ease-out;
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
        
        .alert-error {
            background: #fee;
            border: 1px solid #fcc;
            color: #c00;
        }
        
        .alert-error ul {
            margin: 10px 0 0 20px;
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
            background: #e7f3ff;
            border-left: 4px solid #2196f3;
            padding: 15px 20px;
            margin: 20px 0;
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
            <h1>📝 Inscription à l'événement</h1>
            <p>Remplissez ce formulaire pour vous inscrire</p>
        </div>
        
        <!-- Informations sur l'événement -->
        <div class="event-info">
            <h2><?php echo htmlspecialchars($this->event->getTitre()); ?></h2>
            
            <div class="event-detail">
                <span class="label">📅 Date :</span>
                <?php echo date('d/m/Y', strtotime($this->event->getDateEvent())); ?>
            </div>
            
            <div class="event-detail">
                <span class="label">📍 Lieu :</span>
                <?php echo htmlspecialchars($this->event->getLieu()); ?>
            </div>
            
            <div class="event-detail">
                <span class="label">🎯 Type :</span>
                <?php echo htmlspecialchars($this->event->getTypeEvent()); ?>
            </div>
            
            <div class="event-detail">
                <span class="label">👥 Places restantes :</span>
                <strong style="color: <?php echo $places_restantes > 10 ? 'green' : 'orange'; ?>">
                    <?php echo $places_restantes; ?> / <?php echo $this->event->getNombrePlaces(); ?>
                </strong>
            </div>
        </div>
        
        <div class="form-container">
            <!-- Messages d'erreur serveur -->
            <?php if(isset($_SESSION['errors'])): ?>
                <div id="error-messages" class="alert alert-error">
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
                <h3>ℹ️ Informations importantes</h3>
                <ul>
                    <li>Tous les champs marqués d'un <span class="required">*</span> sont obligatoires</li>
                    <li>Un email de confirmation vous sera envoyé</li>
                    <li>Vous pourrez modifier votre inscription plus tard</li>
                    <li>En cas de problème, contactez-nous</li>
                </ul>
            </div>
            
            <form id="inscription-form" method="POST" action="index.php?controller=inscription&action=store" novalidate>
                <input type="hidden" name="id_event" value="<?php echo $this->event->getIdEvent(); ?>">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="nom">
                            Nom <span class="required">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="nom" 
                            name="nom" 
                            value="<?php echo htmlspecialchars($_SESSION['old_data']['nom'] ?? ''); ?>"
                            placeholder="Votre nom de famille"
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
                            value="<?php echo htmlspecialchars($_SESSION['old_data']['prenom'] ?? ''); ?>"
                            placeholder="Votre prénom"
                            required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="email">
                        Email <span class="required">*</span>
                    </label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        value="<?php echo htmlspecialchars($_SESSION['old_data']['email'] ?? ''); ?>"
                        placeholder="exemple@email.com"
                        required>
                    <small style="color: #666; font-size: 0.85rem;">
                        📧 Vous recevrez une confirmation à cette adresse
                    </small>
                </div>
                
                <div class="form-group">
                    <label for="telephone">
                        Téléphone <span class="required">*</span>
                    </label>
                    <input 
                        type="tel" 
                        id="telephone" 
                        name="telephone" 
                        value="<?php echo htmlspecialchars($_SESSION['old_data']['telephone'] ?? ''); ?>"
                        placeholder="12345678"
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
                        placeholder="Expliquez en quelques mots pourquoi vous souhaitez participer à cet événement..."
                        minlength="20"
                        maxlength="500"
                        required><?php echo htmlspecialchars($_SESSION['old_data']['pourquoi'] ?? ''); ?></textarea>
                    <small style="color: #666; font-size: 0.85rem;">
                        ✍️ Minimum 20 caractères, maximum 500 caractères
                    </small>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn">
                        ✅ Confirmer mon inscription
                    </button>
                    <a href="index.php?controller=event&action=detail&id=<?php echo $this->event->getIdEvent(); ?>" class="btn btn-secondary">
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
