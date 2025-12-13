<?php
/**
 * View/FrontOffice/liste_events.php
 * Liste des événements disponibles
 * ✅ Ajout: Gestion des événements expirés
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Événements Disponibles</title>
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
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .header {
            background: white;
            padding: 30px;
            margin-bottom: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .header h1 {
            color: #333;
            margin-bottom: 10px;
        }
        
        .search-bar {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        
        .search-bar input {
            flex: 1;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        
        .search-bar button {
            padding: 12px 30px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        
        .search-bar button:hover {
            background: #5568d3;
        }
        
        .events-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
            margin-top: 30px;
        }
        
        .event-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        
        .event-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }
        
        /* ✅ Style pour les événements expirés */
        .event-card.expired {
            opacity: 0.6;
            position: relative;
        }
        
        .event-card.expired::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.1);
            pointer-events: none;
        }
        
        .event-image {
            width: 100%;
            height: 200px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 60px;
        }
        
        .event-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .event-content {
            padding: 20px;
        }
        
        .event-title {
            font-size: 20px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }
        
        .event-info {
            color: #666;
            font-size: 14px;
            margin: 8px 0;
        }
        
        .event-info strong {
            color: #333;
        }
        
        .event-description {
            color: #666;
            font-size: 14px;
            line-height: 1.5;
            margin: 15px 0;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .event-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }
        
        .places-info {
            font-size: 14px;
            font-weight: bold;
        }
        
        .places-available {
            color: #28a745;
        }
        
        .places-limited {
            color: #ff9800;
        }
        
        .places-full {
            color: #dc3545;
        }
        
        /* ✅ Style pour événement expiré */
        .event-expired {
            color: #dc3545;
            font-weight: bold;
        }
        
        .btn-detail {
            padding: 10px 20px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
            font-weight: bold;
        }
        
        .btn-detail:hover {
            background: #5568d3;
        }
        
        /* ✅ Bouton désactivé pour événements expirés */
        .btn-detail.disabled {
            background: #ccc;
            color: #666;
            cursor: not-allowed;
            pointer-events: none;
        }
        
        .no-events {
            background: white;
            padding: 60px;
            text-align: center;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .no-events h3 {
            color: #666;
            margin-bottom: 10px;
        }
        
        .filter-section {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .filter-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }
        
        .filter-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }
        
        .filter-group select,
        .filter-group input {
            width: 100%;
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
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
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
            margin-right: 5px;
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
        
        /* ✅ Badge pour événement expiré */
        .badge-expired {
            background: #f8d7da;
            color: #721c24;
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
        
        .nav-link.active {
            background: #667eea;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="nav-links">
            <a href="index.php?controller=event&action=liste" class="nav-link active">📅 Liste des événements</a>
            <a href="View/FrontOffice/mes_inscriptions.php" class="nav-link">📋 Mes Inscriptions</a>
        </div>
        
        <div class="header">
            <h1>🎯 Événements Caritatifs Disponibles</h1>
            <p>Trouvez un événement et inscrivez-vous pour participer</p>
            
            <form method="GET" action="index.php" class="search-bar">
                <input type="hidden" name="controller" value="event">
                <input type="hidden" name="action" value="search">
                <input type="text" name="keyword" placeholder="Rechercher un événement..." 
                       value="<?php echo htmlspecialchars($_GET['keyword'] ?? ''); ?>">
                <button type="submit">🔍 Rechercher</button>
            </form>
        </div>
        
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
        
        <div class="filter-section">
            <h3>🔎 Filtrer les événements</h3>
            <form method="GET" action="index.php">
                <input type="hidden" name="controller" value="event">
                <input type="hidden" name="action" value="search">
                
                <div class="filter-row">
                    <div class="filter-group">
                        <label for="type">Type d'événement</label>
                        <select name="type" id="type">
                            <option value="">Tous les types</option>
                            <option value="collecte">Collecte de nourriture</option>
                            <option value="distribution">Distribution de repas</option>
                            <option value="soutien">Soutien médical</option>
                            <option value="logement">Aide au logement</option>
                            <option value="autre">Autre</option>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label for="date_min">Date minimum</label>
                        <input type="date" name="date_min" id="date_min">
                    </div>
                    
                    <div class="filter-group">
                        <label for="lieu">Lieu</label>
                        <input type="text" name="lieu" id="lieu" placeholder="Ville...">
                    </div>
                    
                    <div class="filter-group">
                        <label>&nbsp;</label>
                        <button type="submit" class="search-bar button" style="width: 100%;">
                            Appliquer les filtres
                        </button>
                    </div>
                </div>
            </form>
        </div>
        
        <?php if(!empty($events) && count($events) > 0): ?>
            <div class="events-grid">
                <?php 
                // ✅ Date actuelle pour vérifier l'expiration
                $now = new DateTime();
                
                foreach($events as $event): 
                    $inscriptions_count = $event['inscriptions_count'] ?? 0;
                    $places_restantes = $event['nombre_places'] - $inscriptions_count;
                    
                    // ✅ Vérifier si l'événement est expiré
                    $date_event = new DateTime($event['date_event']);
                    $is_expired = $date_event < $now;
                    
                    $placesClass = 'places-available';
                    if($is_expired) {
                        $placesClass = 'event-expired';
                    } elseif($places_restantes <= 0) {
                        $placesClass = 'places-full';
                    } elseif($places_restantes <= 10) {
                        $placesClass = 'places-limited';
                    }
                ?>
                    <div class="event-card <?php echo $is_expired ? 'expired' : ''; ?>">
                        <div class="event-image">
                            <?php if(!empty($event['image']) && file_exists($event['image'])): ?>
                                <img src="<?php echo htmlspecialchars($event['image']); ?>" 
                                     alt="<?php echo htmlspecialchars($event['titre']); ?>">
                            <?php else: ?>
                                📅
                            <?php endif; ?>
                        </div>
                        
                        <div class="event-content">
                            <div class="event-title">
                                <?php echo htmlspecialchars($event['titre']); ?>
                            </div>
                            
                            <span class="badge badge-<?php echo htmlspecialchars($event['type_event']); ?>">
                                <?php echo htmlspecialchars($event['type_event']); ?>
                            </span>
                            
                            <?php if($is_expired): ?>
                                <span class="badge badge-expired">⏰ EXPIRÉ</span>
                            <?php endif; ?>
                            
                            <div class="event-info">
                                <strong>📅 Date :</strong> 
                                <?php echo date('d/m/Y à H:i', strtotime($event['date_event'])); ?>
                            </div>
                            
                            <div class="event-info">
                                <strong>📍 Lieu :</strong> 
                                <?php echo htmlspecialchars($event['lieu']); ?>
                            </div>
                            
                            <div class="event-description">
                                <?php echo htmlspecialchars($event['description']); ?>
                            </div>
                            
                            <div class="event-footer">
                                <div class="places-info <?php echo $placesClass; ?>">
                                    <?php if($is_expired): ?>
                                        ⏰ Événement terminé
                                    <?php elseif($places_restantes > 0): ?>
                                        👥 <?php echo $places_restantes; ?> places restantes
                                    <?php else: ?>
                                        ❌ Complet
                                    <?php endif; ?>
                                </div>
                                
                                <a href="index.php?controller=event&action=detail&id=<?php echo $event['id_event']; ?>" 
                                   class="btn-detail <?php echo $is_expired ? 'disabled' : ''; ?>">
                                    <?php echo $is_expired ? 'Terminé' : 'Voir détails'; ?>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-events">
                <h3>😕 Aucun événement trouvé</h3>
                <p>Il n'y a pas d'événements disponibles pour le moment.</p>
                <p>Revenez plus tard ou modifiez vos critères de recherche.</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>