<?php
/**
 * View/FrontOffice/mes_inscriptions.php
 * 📋 Page où le client peut voir, modifier et supprimer ses inscriptions
 * ✅ CORRECTION COMPLÈTE: Tous les liens corrigés
 */


require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../Model/Inscription.php';
require_once __DIR__ . '/../../Model/Event.php';

// Initialiser la base de données
$database = new Database();
$db = $database->getConnection();
$inscriptionModel = new Inscription($db);
$eventModel = new Event($db);

// Récupérer l'email depuis le formulaire ou la session
$email = $_GET['email'] ?? $_POST['email'] ?? $_SESSION['user_email'] ?? '';

// Si pas d'email, afficher le formulaire de recherche
if (empty($email)) {
    ?>
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Mes Inscriptions</title>
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }
            
            body {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                background:white; 
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px;
            }
            
            .search-container {
                background: white;
                padding: 40px;
                border-radius: 15px;
                box-shadow: 0 10px 40px rgba(0,0,0,0.2);
                max-width: 500px;
                width: 100%;
            }
            
            .search-container h1 {
                color: #333;
                margin-bottom: 10px;
                text-align: center;
            }
            
            .search-container p {
                color: #666;
                text-align: center;
                margin-bottom: 30px;
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
            
            .form-group input {
                width: 100%;
                padding: 15px;
                border: 2px solid #ddd;
                border-radius: 8px;
                font-size: 16px;
                transition: all 0.3s;
            }
            
            .form-group input:focus {
                outline: none;
                border-color: #667eea;
                box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            }
            
            .btn {
                width: 100%;
                padding: 15px;
                background: #667eea;
                color: white;
                border: none;
                border-radius: 8px;
                font-size: 16px;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.3s;
            }
            
            .btn:hover {
                background: #5568d3;
                transform: translateY(-2px);
                box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
            }
            
            .back-link {
                display: block;
                text-align: center;
                margin-top: 20px;
                color: #667eea;
                text-decoration: none;
                font-weight: 600;
            }
            
            .back-link:hover {
                text-decoration: underline;
            }
            
            .info-box {
                background: #e7f3ff;
                border-left: 4px solid #2196f3;
                padding: 15px;
                margin-bottom: 25px;
                border-radius: 5px;
            }
            
            .info-box ul {
                margin-left: 20px;
                margin-top: 10px;
            }
            
            .info-box li {
                margin: 5px 0;
                color: #333;
            }
        </style>
    </head>
    <body>
        <div class="search-container">
            <h1>📋 Mes Inscriptions</h1>
            <p>Entrez votre email pour voir vos inscriptions</p>
            
            <div class="info-box">
                <strong>ℹ️ Vous pourrez :</strong>
                <ul>
                    <li>Voir toutes vos inscriptions</li>
                    <li>Modifier vos informations</li>
                    <li>Annuler une inscription</li>
                    <li>Voir les détails des événements</li>
                </ul>
            </div>
            
            <!-- ✅ CORRECTION: Formulaire qui soumet à lui-même -->
            <form method="GET" action="" novalidate>
                <div class="form-group">
                    <label for="email">
                        📧 Votre adresse email
                    </label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        placeholder="exemple@email.com"
                        required>
                </div>
                
                <button type="submit" class="btn">
                    🔍 Rechercher mes inscriptions
                </button>
            </form>
            
            <!-- ✅ CORRECTION: Lien vers le routeur principal -->
            <a href="../../index.php?controller=event&action=liste" class="back-link">
                ← Retour aux événements
            </a>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Récupérer les inscriptions pour cet email
$inscriptionModel->setEmail($email);
$stmt = $inscriptionModel->readByEmail();
$inscriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Stocker l'email en session pour faciliter la navigation
$_SESSION['user_email'] = $email;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Inscriptions</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
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
            background: #667eea;
            color: white;
        }
        
        .nav-link.active {
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
        
        .user-email {
            display: inline-block;
            background: #e7f3ff;
            padding: 8px 15px;
            border-radius: 20px;
            color: #1976d2;
            font-weight: 600;
            margin-top: 10px;
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
        
        .inscriptions-grid {
            display: grid;
            gap: 20px;
        }
        
        .inscription-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        
        .inscription-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }
        
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
        }
        
        .card-header h3 {
            margin-bottom: 8px;
        }
        
        .card-body {
            padding: 25px;
        }
        
        .info-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .info-item {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border-left: 3px solid #667eea;
        }
        
        .info-label {
            font-size: 13px;
            color: #666;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .info-value {
            font-size: 15px;
            color: #333;
            font-weight: 500;
        }
        
        .motivation-box {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
        
        .motivation-box strong {
            color: #856404;
            display: block;
            margin-bottom: 8px;
        }
        
        .motivation-box p {
            color: #856404;
            line-height: 1.6;
        }
        
        .status-badge {
            display: inline-block;
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            margin-top: 5px;
        }
        
        .status-en_attente {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-confirme {
            background: #d4edda;
            color: #155724;
        }
        
        .status-annule {
            background: #f8d7da;
            color: #721c24;
        }
        
        .card-actions {
            display: flex;
            gap: 10px;
            padding-top: 20px;
            border-top: 2px solid #eee;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background: #667eea;
            color: white;
        }
        
        .btn-primary:hover {
            background: #5568d3;
        }
        
        .btn-warning {
            background: #ffc107;
            color: #333;
        }
        
        .btn-warning:hover {
            background: #e0a800;
        }
        
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        
        .btn-danger:hover {
            background: #c82333;
        }
        
        .no-inscriptions {
            background: white;
            padding: 60px;
            text-align: center;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .no-inscriptions h3 {
            color: #666;
            margin-bottom: 15px;
        }
        
        .no-inscriptions p {
            color: #999;
            margin-bottom: 25px;
        }
        
        @media (max-width: 768px) {
            .info-row {
                grid-template-columns: 1fr;
            }
            
            .card-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- ✅ CORRECTION: Navigation avec liens corrects -->
        <div class="nav-links">
            <a href="../../index.php?controller=event&action=liste" class="nav-link">
                📅 Liste des événements
            </a>
            <a href="mes_inscriptions.php?email=<?php echo urlencode($email); ?>" class="nav-link active">
                📋 Mes inscriptions
            </a>
            <!-- ✅ CORRECTION: Lien pour changer d'email -->
            <a href="mes_inscriptions.php" class="nav-link">
                🔄 Changer d'email
            </a>
        </div>
        
        <!-- En-tête -->
        <div class="header">
            <h1>📋 Mes Inscriptions</h1>
            <p>Gérez vos inscriptions aux événements</p>
            <div class="user-email">
                📧 <?php echo htmlspecialchars($email); ?>
            </div>
        </div>
        
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
        
        <!-- Liste des inscriptions -->
        <?php if (!empty($inscriptions)): ?>
            <div class="inscriptions-grid">
                <?php foreach($inscriptions as $inscription): 
                    // Calculer si l'événement est passé
                    $event_date = strtotime($inscription['date_event']);
                    $is_past = $event_date < time();
                ?>
                    <div class="inscription-card">
                        <div class="card-header">
                            <h3><?php echo htmlspecialchars($inscription['event_titre']); ?></h3>
                            <span class="status-badge status-<?php echo $inscription['statut']; ?>">
                                <?php 
                                    $statuts = [
                                        'en_attente' => '⏳ En attente',
                                        'confirme' => '✅ Confirmée',
                                        'annule' => '❌ Annulée'
                                    ];
                                    echo $statuts[$inscription['statut']] ?? $inscription['statut'];
                                ?>
                            </span>
                        </div>
                        
                        <div class="card-body">
                            <!-- Informations de l'événement -->
                            <div class="info-row">
                                <div class="info-item">
                                    <div class="info-label">📅 Date de l'événement</div>
                                    <div class="info-value">
                                        <?php 
                                            echo date('d/m/Y à H:i', strtotime($inscription['date_event']));
                                            if ($is_past) {
                                                echo ' <span style="color: #dc3545;">(Passé)</span>';
                                            }
                                        ?>
                                    </div>
                                </div>
                                
                                <div class="info-item">
                                    <div class="info-label">📍 Lieu</div>
                                    <div class="info-value">
                                        <?php echo htmlspecialchars($inscription['lieu']); ?>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Informations personnelles -->
                            <div class="info-row">
                                <div class="info-item">
                                    <div class="info-label">👤 Nom complet</div>
                                    <div class="info-value">
                                        <?php echo htmlspecialchars($inscription['nom'] . ' ' . $inscription['prenom']); ?>
                                    </div>
                                </div>
                                
                                <div class="info-item">
                                    <div class="info-label">📱 Téléphone</div>
                                    <div class="info-value">
                                        <?php echo htmlspecialchars($inscription['telephone']); ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="info-row">
                                <div class="info-item">
                                    <div class="info-label">📅 Date d'inscription</div>
                                    <div class="info-value">
                                        <?php echo date('d/m/Y à H:i', strtotime($inscription['date_inscription'])); ?>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Motivation -->
                            <div class="motivation-box">
                                <strong>💭 Votre motivation :</strong>
                                <p><?php echo nl2br(htmlspecialchars($inscription['pourquoi'])); ?></p>
                            </div>
                            
                            <!-- ✅ CORRECTION: Actions avec les bons liens -->
                            <div class="card-actions">
                                
                                
                                <?php if (!$is_past && $inscription['statut'] !== 'annule'): ?>
                                    <!-- ✅ CORRECTION: Liens vers les fichiers avec email encodé -->
                                    <a href="modifier_inscription.php?id=<?php echo $inscription['id_inscription']; ?>&email=<?php echo urlencode($email); ?>" 
                                       class="btn btn-warning">
                                        ✏️ Modifier
                                    </a>
                                    
                                    <a href="annuler_inscription.php?id=<?php echo $inscription['id_inscription']; ?>&email=<?php echo urlencode($email); ?>" 
                                       class="btn btn-danger"
                                       onclick="return confirm('Êtes-vous sûr de vouloir annuler cette inscription ?')">
                                        🗑️ Annuler
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-inscriptions">
                <h3>😢 Aucune inscription trouvée</h3>
                <p>Vous n'avez pas encore d'inscription à votre nom avec cet email.</p>
                <a href="../../index.php?controller=event&action=liste" class="btn btn-primary">
                    📅 Voir les événements disponibles
                </a>
            </div>
        <?php endif; ?>
    </div>
 <!-- REMPLACER les lignes 380-381 par : -->
<script src="/assets/js/notifications.js"></script>
<script src="/assets/js/confirmation-handler.js"></script>
</body>
</html>