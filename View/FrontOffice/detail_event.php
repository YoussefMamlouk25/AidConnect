<?php
/**
 * View/FrontOffice/detail_event.php
 * Détails d'un événement
 */

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($event->getTitre()); ?> - Détails</title>
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
        
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            padding: 10px 20px;
            background: white;
            color: #667eea;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        
        .back-link:hover {
            background: #667eea;
            color: white;
        }
        
        .event-detail-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .event-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px;
            text-align: center;
        }
        
        .event-header h1 {
            font-size: 32px;
            margin-bottom: 15px;
        }
        
        .event-image {
            width: 100%;
            height: 400px;
            background: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 100px;
        }
        
        .event-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .event-content {
            padding: 40px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }
        
        .info-box {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #667eea;
        }
        
        .info-box-label {
            font-size: 14px;
            color: #666;
            font-weight: bold;
            margin-bottom: 8px;
        }
        
        .info-box-value {
            font-size: 18px;
            color: #333;
            font-weight: bold;
        }
        
        .description-section {
            margin: 30px 0;
            padding: 30px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        
        .description-section h3 {
            color: #333;
            margin-bottom: 15px;
        }
        
        .description-section p {
            color: #666;
            line-height: 1.8;
            font-size: 16px;
        }
        
        .places-alert {
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            font-weight: bold;
            text-align: center;
        }
        
        .places-available {
            background: #d4edda;
            border: 2px solid #c3e6cb;
            color: #155724;
        }
        
        .places-limited {
            background: #fff3cd;
            border: 2px solid #ffeaa7;
            color: #856404;
        }
        
        .places-full {
            background: #f8d7da;
            border: 2px solid #f5c6cb;
            color: #721c24;
        }
        
        .action-buttons {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            justify-content: center;
        }
        
        .btn {
            padding: 15px 40px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
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
        
        .btn-primary:disabled {
            background: #ccc;
            cursor: not-allowed;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
        }
        
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
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
        
        .badge {
            display: inline-block;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
            margin: 10px 0;
        }
        
        .badge-collecte {
            background: #e3f2fd;
            color: #1976d2;
        }
        
        .badge-distribution {
            background: #fff3e0;
            color: #f57c00;
        }
        
        .badge-soutien {
            background: #f3e5f5;
            color: #7b1fa2;
        }
        
        .badge-logement {
            background: #e8f5e9;
            color: #388e3c;
        }
        
        .badge-autre {
            background: #fce4ec;
            color: #c2185b;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php?controller=event&action=liste" class="back-link">
            ← Retour à la liste
        </a>
        
        <!-- Messages -->
        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                ✅ <?php echo htmlspecialchars($_SESSION['success']); ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        
        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                ❌ <?php echo htmlspecialchars($_SESSION['error']); ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        
        <div class="event-detail-card">
            <!-- En-tête -->
            <div class="event-header">
                <span class="badge badge-<?php echo htmlspecialchars($event->getTypeEvent()); ?>">
                    <?php echo htmlspecialchars($event->getTypeEvent()); ?>
                </span>
                <h1><?php echo htmlspecialchars($event->getTitre()); ?></h1>
            </div>
            
            <!-- Image -->
            <?php if(!empty($event->getImage()) && file_exists($event->getImage())): ?>
                <div class="event-image">
                    <img src="<?php echo htmlspecialchars($event->getImage()); ?>" 
                         alt="<?php echo htmlspecialchars($event->getTitre()); ?>">
                </div>
            <?php else: ?>
                <div class="event-image">
                    📅
                </div>
            <?php endif; ?>
            
            <div class="event-content">
                <!-- Informations principales -->
                <div class="info-grid">
                    <div class="info-box">
                        <div class="info-box-label">📅 Date de l'événement</div>
                        <div class="info-box-value">
                            <?php echo date('d/m/Y', strtotime($event->getDateEvent())); ?>
                        </div>
                        <div style="margin-top: 5px; color: #666; font-size: 14px;">
                            à <?php echo date('H:i', strtotime($event->getDateEvent())); ?>
                        </div>
                    </div>
                    
                    <div class="info-box">
                        <div class="info-box-label">📍 Lieu</div>
                        <div class="info-box-value">
                            <?php echo htmlspecialchars($event->getLieu()); ?>
                        </div>
                    </div>
                    
                    <div class="info-box">
                        <div class="info-box-label">👥 Capacité</div>
                        <div class="info-box-value">
                            <?php echo $event->getNombrePlaces(); ?> places
                        </div>
                    </div>
                    
                    <div class="info-box">
                        <div class="info-box-label">✅ Inscrits</div>
                        <div class="info-box-value">
                            <?php echo $inscriptions_count; ?> personnes
                        </div>
                    </div>
                </div>
                
                <!-- Alerte places -->
                <?php
                $placesClass = 'places-available';
                $placesMessage = '✅ Places disponibles';
                
                if($places_restantes <= 0) {
                    $placesClass = 'places-full';
                    $placesMessage = '❌ Événement complet - Plus de places disponibles';
                } elseif($places_restantes <= 10) {
                    $placesClass = 'places-limited';
                    $placesMessage = '⚠️ Attention : Plus que ' . $places_restantes . ' places disponibles !';
                } else {
                    $placesMessage = '✅ ' . $places_restantes . ' places disponibles sur ' . $event->getNombrePlaces();
                }
                ?>
                
                <div class="places-alert <?php echo $placesClass; ?>">
                    <?php echo $placesMessage; ?>
                </div>
                
                <!-- Description -->
                <div class="description-section">
                    <h3>📋 Description de l'événement</h3>
                    <p><?php echo nl2br(htmlspecialchars($event->getDescription())); ?></p>
                </div>
                
                <!-- Boutons d'action -->
                <div class="action-buttons">
                    <?php if($places_restantes > 0): ?>
                        <a href="index.php?controller=inscription&action=create&id_event=<?php echo $event->getIdEvent(); ?>" 
                           class="btn btn-primary">
                            ✅ S'inscrire à cet événement
                        </a>
                    <?php else: ?>
                        <button class="btn btn-primary" disabled>
                            ❌ Événement complet
                        </button>
                    <?php endif; ?>
                    
                    <a href="index.php?controller=event&action=liste" class="btn btn-secondary">
                        Retour à la liste
                    </a>
                </div>
            </div>
        </div>
    </div>
<!-- Scripts JS -->
    <script src="assets/js/notifications.js"></script>
    <script src="assets/js/confirmation-handler.js"></script>
</body>
</html>