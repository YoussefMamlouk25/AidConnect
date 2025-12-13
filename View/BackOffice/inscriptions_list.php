<?php
/**
 * inscriptions_list.php - Liste des inscriptions pour un événement
 * ✅ CORRECTION: Recherche complète fonctionnelle
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../Model/Event.php';
require_once __DIR__ . '/../../Model/Inscription.php';

$database = new Database();
$db = $database->getConnection();
$eventModel = new Event($db);
$inscriptionModel = new Inscription($db);

$event_id = isset($_GET['id_event']) ? intval($_GET['id_event']) : null;

$event = null;
if ($event_id) {
    $eventModel->setIdEvent($event_id);
    if ($eventModel->readOne()) {
        $event = $eventModel;
    }
}

if ($event_id) {
    $query = "SELECT i.*, e.titre as event_titre, e.date_event, e.lieu
              FROM inscriptions i
              LEFT JOIN events e ON i.id_event = e.id_event
              WHERE i.id_event = :id_event
              ORDER BY i.date_inscription DESC";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id_event', $event_id);
    $stmt->execute();
} else {
    $query = "SELECT i.*, e.titre as event_titre, e.date_event, e.lieu
              FROM inscriptions i
              LEFT JOIN events e ON i.id_event = e.id_event
              ORDER BY i.date_inscription DESC";
    
    $stmt = $db->prepare($query);
    $stmt->execute();
}

$inscriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total_inscriptions = count($inscriptions);
$en_attente = count(array_filter($inscriptions, fn($i) => $i['statut'] === 'en_attente'));
$confirme = count(array_filter($inscriptions, fn($i) => $i['statut'] === 'confirme'));
$annule = count(array_filter($inscriptions, fn($i) => $i['statut'] === 'annule'));
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscriptions<?php echo $event ? ' - ' . htmlspecialchars($event->getTitre()) : ''; ?></title>
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
            max-width: 1400px;
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
            margin-bottom: 5px;
        }
        
        .header p {
            color: #666;
            font-size: 16px;
        }
        
        .event-info-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 25px;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }
        
        .event-info-box h2 {
            margin-bottom: 15px;
        }
        
        .event-detail {
            margin: 8px 0;
            font-size: 15px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }
        
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-value {
            font-size: 36px;
            font-weight: bold;
            color: #667eea;
            margin: 10px 0;
        }
        
        .stat-label {
            color: #666;
            font-size: 14px;
            font-weight: 500;
        }
        
        .filter-section {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .filter-section h3 {
            color: #333;
            margin-bottom: 15px;
        }
        
        .filter-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
        }
        
        .filter-group {
            position: relative;
        }
        
        .filter-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
            font-size: 14px;
        }
        
        .filter-group input,
        .filter-group select {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s;
        }
        
        .filter-group input:focus,
        .filter-group select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .search-info {
            margin-top: 15px;
            padding: 12px;
            background: #e7f3ff;
            border-left: 4px solid #2196f3;
            border-radius: 5px;
            display: none;
        }
        
        .search-info.show {
            display: block;
            animation: slideDown 0.3s;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .search-info strong {
            color: #1976d2;
        }
        
        .table-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        thead {
            background: #667eea;
            color: white;
        }
        
        th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
            font-size: 14px;
            white-space: nowrap;
        }
        
        td {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }
        
        tbody tr {
            transition: all 0.3s;
        }
        
        tbody tr:hover {
            background: #f8f9fa;
        }
        
        tbody tr.highlight {
            background: #fffbeb;
            animation: pulse 0.5s;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.01); }
        }
        
        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
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
        
        .no-data, .no-results {
            padding: 60px;
            text-align: center;
            color: #666;
        }
        
        .no-data h3, .no-results h3 {
            margin-bottom: 10px;
            font-size: 24px;
        }
        
        .no-results {
            display: none;
        }
        
        .no-results.show {
            display: block;
        }
        
        .participant-name {
            font-weight: 600;
            color: #333;
        }
        
        .time-badge {
            background: #e3f2fd;
            color: #1976d2;
            padding: 3px 8px;
            border-radius: 10px;
            font-size: 11px;
            margin-left: 5px;
        }
        
        .detail-text {
            color: #666;
            font-size: 13px;
        }
        
        @media (max-width: 768px) {
            .table-container {
                overflow-x: scroll;
            }
            
            table {
                min-width: 800px;
            }
            
            .filter-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="nav-links">
            <a href="index.php?controller=event&action=manage" class="nav-link">📅 Événements</a>
            <a href="index.php?controller=inscription&action=liste" class="nav-link active">📋 Inscriptions</a>
            <?php if ($event_id): ?>
                <a href="edit.php?id=<?php echo $event_id; ?>" class="nav-link">✏️ Modifier l'événement</a>
                <a href="index.php?controller=event&action=detail&id=<?php echo $event_id; ?>" class="nav-link">👁️ Voir l'événement</a>
            <?php endif; ?>
        </div>
        
        <div class="header">
            <h1>👥 Inscriptions</h1>
            <p>
                <?php if ($event): ?>
                    Inscriptions pour l'événement
                <?php else: ?>
                    Toutes les inscriptions
                <?php endif; ?>
            </p>
        </div>
        
        <?php if ($event): ?>
            <div class="event-info-box">
                <h2>📌 <?php echo htmlspecialchars($event->getTitre()); ?></h2>
                <div class="event-detail">
                    📅 <strong>Date:</strong> <?php echo date('d/m/Y à H:i', strtotime($event->getDateEvent())); ?>
                </div>
                <div class="event-detail">
                    📍 <strong>Lieu:</strong> <?php echo htmlspecialchars($event->getLieu()); ?>
                </div>
                <div class="event-detail">
                    🎯 <strong>Type:</strong> <?php echo htmlspecialchars($event->getTypeEvent()); ?>
                </div>
                <div class="event-detail">
                    👺 <strong>Capacité:</strong> <?php echo $event->getNombrePlaces(); ?> places
                </div>
            </div>
        <?php endif; ?>
        
        <!-- ✅ Section de recherche simplifiée -->
        <div class="filter-section">
            <h3>🔍 Rechercher une inscription</h3>
            <div class="filter-row">
                <div class="filter-group">
                    <label for="search-input">Recherche globale</label>
                    <input type="text" 
                           id="search-input" 
                           placeholder="🔎 Nom, prénom, email, téléphone, événement...">
                </div>
            </div>
            
            <!-- Info de recherche -->
            <div id="search-info" class="search-info"></div>
        </div>
        
        <div class="table-container">
            <?php if (!empty($inscriptions)): ?>
                <table id="inscriptions-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Participant</th>
                            <th>Contact</th>
                            <?php if (!$event_id): ?>
                                <th>Événement</th>
                            <?php endif; ?>
                            <th>Date d'inscription</th>
                            <th>Heure</th>
                            <th>Motivation</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($inscriptions as $inscription): 
                            // ✅ CORRECTION: Inclure TOUTES les données dans data-search
                            $searchData = strtolower(
                                $inscription['nom'] . ' ' . 
                                $inscription['prenom'] . ' ' . 
                                $inscription['email'] . ' ' . 
                                $inscription['telephone'] . ' ' . 
                                ($inscription['event_titre'] ?? '') . ' ' .
                                $inscription['statut'] . ' ' .
                                $inscription['pourquoi']
                            );
                        ?>
                        <tr data-status="<?php echo $inscription['statut']; ?>"
                            data-search="<?php echo htmlspecialchars($searchData); ?>">
                            
                            <td>
                                <strong>#<?php echo $inscription['id_inscription']; ?></strong>
                            </td>
                            
                            <td>
                                <div class="participant-name">
                                    <?php echo htmlspecialchars($inscription['nom'] . ' ' . $inscription['prenom']); ?>
                                </div>
                            </td>
                            
                            <td>
                                <div class="detail-text">
                                    📧 <?php echo htmlspecialchars($inscription['email']); ?>
                                </div>
                                <div class="detail-text">
                                    📱 <?php echo htmlspecialchars($inscription['telephone']); ?>
                                </div>
                            </td>
                            
                            <?php if (!$event_id): ?>
                            <td>
                                <strong><?php echo htmlspecialchars($inscription['event_titre'] ?? 'N/A'); ?></strong>
                                <div class="detail-text">
                                    <?php echo !empty($inscription['date_event']) ? date('d/m/Y', strtotime($inscription['date_event'])) : 'N/A'; ?>
                                </div>
                            </td>
                            <?php endif; ?>
                            
                            <td>
                                <?php 
                                $date_inscription = new DateTime($inscription['date_inscription']);
                                echo $date_inscription->format('d/m/Y');
                                ?>
                            </td>
                            
                            <td>
                                <span class="time-badge">
                                    🕐 <?php echo $date_inscription->format('H:i'); ?>
                                </span>
                            </td>
                            
                            <td>
                                <div style="max-width: 300px; font-size: 13px; color: #666;">
                                    <?php 
                                    $pourquoi = htmlspecialchars($inscription['pourquoi']);
                                    echo strlen($pourquoi) > 100 ? substr($pourquoi, 0, 100) . '...' : $pourquoi;
                                    ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <!-- Message "Aucun résultat" -->
                <div class="no-results" id="no-results">
                    <h3>😕 Aucun résultat trouvé</h3>
                    <p>Aucune inscription ne correspond à votre recherche.</p>
                    <p>Essayez avec d'autres mots-clés.</p>
                </div>
            <?php else: ?>
                <div class="no-data">
                    <h3>😕 Aucune inscription</h3>
                    <p>Il n'y a pas encore d'inscriptions pour cet événement.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        // ✅ Recherche simple sans filtre de statut
        const searchInput = document.getElementById('search-input');
        const searchInfo = document.getElementById('search-info');
        const noResults = document.getElementById('no-results');
        const tableBody = document.querySelector('#inscriptions-table tbody');
        
        // Attacher l'événement de recherche
        if (searchInput) {
            searchInput.addEventListener('input', filterTable);
        }
        
        function filterTable() {
            const searchTerm = searchInput ? searchInput.value.toLowerCase().trim() : '';
            
            const rows = document.querySelectorAll('#inscriptions-table tbody tr');
            let visibleCount = 0;
            const totalCount = rows.length;
            
            console.log('🔍 Recherche:', searchTerm);
            
            rows.forEach(row => {
                const searchData = row.getAttribute('data-search');
                
                // Vérifier la correspondance
                const matchesSearch = !searchTerm || searchData.includes(searchTerm);
                
                if (matchesSearch) {
                    row.style.display = '';
                    row.classList.add('highlight');
                    visibleCount++;
                    
                    // Retirer l'animation après 500ms
                    setTimeout(() => {
                        row.classList.remove('highlight');
                    }, 500);
                } else {
                    row.style.display = 'none';
                }
            });
            
            // Afficher/masquer "Aucun résultat"
            if (visibleCount === 0 && searchTerm) {
                if (noResults) noResults.classList.add('show');
                if (tableBody) tableBody.style.display = 'none';
            } else {
                if (noResults) noResults.classList.remove('show');
                if (tableBody) tableBody.style.display = '';
            }
            
            // Mettre à jour l'info de recherche
            updateSearchInfo(visibleCount, totalCount, searchTerm);
            
            console.log(`📊 Résultats: ${visibleCount}/${totalCount}`);
        }
        
        function updateSearchInfo(visible, total, search) {
            if (!searchInfo) return;
            
            if (!search) {
                searchInfo.classList.remove('show');
                searchInfo.innerHTML = '';
                return;
            }
            
            let message = `<strong>📊 ${visible}</strong> résultat(s) sur <strong>${total}</strong> pour "<strong>${search}</strong>"`;
            
            searchInfo.innerHTML = message;
            searchInfo.classList.add('show');
        }
        
        // Message de démarrage
        console.log('✅ Page des inscriptions chargée');
        console.log('📊 Total inscriptions:', <?php echo $total_inscriptions; ?>);
        
        // Focus automatique
        if (searchInput) {
            searchInput.focus();
        }
    </script>
</body>
</html>