<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../../config.php';
require_once '../../Model/Event.php';
require_once '../../Model/Inscription.php';

$eventModel = new Event(Config::getConnexion());
$inscriptionModel = new Inscription(Config::getConnexion());

// Récupérer tous les événements
$stmt = $eventModel->readAll();
$allEvents = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Remove duplicates by id_event - keep only the first occurrence (most recent based on ORDER BY)
$uniqueEvents = [];
$seenIds = [];
foreach($allEvents as $event) {
    $id = isset($event['id_event']) ? (int)$event['id_event'] : 0;
    if ($id > 0 && !isset($seenIds[$id])) {
        $seenIds[$id] = true;
        $uniqueEvents[] = $event;
    }
}
$events = $uniqueEvents;

// Ajouter le nombre d'inscriptions pour chaque événement
foreach($events as &$event) {
    if (isset($event['id_event'])) {
    $eventModel->setIdEvent($event['id_event']);
    $event['inscriptions_count'] = $eventModel->countInscriptions();
}
}
unset($event); // Unset reference to avoid issues

// Ensure it's an array
if (!is_array($events)) {
    $events = [];
}

$embed = isset($_GET['embed']) && $_GET['embed'] === '1';
// Détecter si c'est une requête AJAX (pour dashboard.php) ou une page directe
$isAjax = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') 
          || (isset($_GET['ajax']) && $_GET['ajax'] == '1');
$showFullPage = !$embed && !$isAjax;

// Si c'est une page complète, utiliser le layout
if ($showFullPage) {
    require_once 'layout.php';
    ob_start();
}
?>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <div class="header-title">
                    <h4 class="card-title">Événements</h4>
                </div>
                <div>
                    <a href="#" onclick="loadContent('addevent.php'); return false;" class="btn btn-primary">
                        <i class="ri-add-line me-1"></i> Ajouter un Événement
                    </a>
                </div>
            </div>
            <div class="offers-fragment">
                <div class="table-responsive" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
                    <table class="post-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Titre</th>
                                <th>Description</th>
                                <th>Date</th>
                                <th>Lieu</th>
                                <th>Type</th>
                                <th>Places</th>
                                <th>Inscrits</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                <?php if (is_array($events) && count($events) > 0): ?>
                        <?php foreach ($events as $event): 
                            $idEvent = isset($event['id_event']) ? $event['id_event'] : '';
                            $titre = isset($event['titre']) ? htmlspecialchars($event['titre']) : 'N/A';
                            $description = isset($event['description']) ? htmlspecialchars($event['description']) : 'N/A';
                                $descriptionShort = strlen($description) > 80 ? substr($description, 0, 80) . '...' : $description;
                            $dateEvent = isset($event['date_event']) ? date('d/m/Y H:i', strtotime($event['date_event'])) : 'N/A';
                            $lieu = isset($event['lieu']) ? htmlspecialchars($event['lieu']) : 'N/A';
                            $typeEvent = isset($event['type_event']) ? htmlspecialchars($event['type_event']) : 'N/A';
                            $nombrePlaces = isset($event['nombre_places']) ? $event['nombre_places'] : 0;
                            $inscriptionsCount = isset($event['inscriptions_count']) ? $event['inscriptions_count'] : 0;
                            $placesRestantes = $nombrePlaces - $inscriptionsCount;
                            $image = isset($event['image']) ? $event['image'] : '';
                            $statut = isset($event['statut']) ? $event['statut'] : 'actif';
                            
                            // Gestion de l'image
                                // Images are stored in DB as 'public/uploads/events/filename.png' (new) or 'uploads/events/filename.png' (old)
                                // Actual file location: aidconnect!!/public/uploads/events/filename.png
                                // From backoffice (aidconnect!!/View/backoffice/), URL path is: ../../public/uploads/events/filename.png
                            $imageUrl = '';
                            if (!empty($image) && $image !== 'null') {
                                    // Handle backward compatibility: convert old path to new path
                                    $imagePath = str_replace('\\', '/', $image);
                                    if (strpos($imagePath, 'uploads/events/') === 0 && strpos($imagePath, 'public/uploads/events/') !== 0) {
                                        // Old path format: convert to new path
                                        $imagePath = 'public/' . $imagePath;
                                    }
                                    
                                    // Construct URL path (always use forward slashes for URLs)
                                    $imageUrl = '../../' . ltrim($imagePath, '/');
                                    
                                    // Optional: Verify file exists (but don't clear URL if check fails - let browser handle it)
                                    $filePath = __DIR__ . '/../../' . str_replace('\\', DIRECTORY_SEPARATOR, $imagePath);
                                    // Keep the URL even if file check fails - browser will show error and fallback will display
                            }
                        ?>
                            <tr>
                                <td><strong>#<?= htmlspecialchars($idEvent) ?></strong></td>
                                <td style="width: 100px;">
                            <?php if (!empty($imageUrl)): ?>
                                        <img src="<?= htmlspecialchars($imageUrl) ?>" 
                                             alt="<?= htmlspecialchars($titre) ?>" 
                                             style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px;"
                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <div style="width: 80px; height: 80px; background: #f0f0f0; border-radius: 8px; display: none; align-items: center; justify-content: center; font-size: 1.5rem;">📅</div>
                            <?php else: ?>
                                        <div style="width: 80px; height: 80px; background: #f0f0f0; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">📅</div>
                            <?php endif; ?>
                                </td>
                                <td><strong><?= $titre ?></strong></td>
                                <td style="max-width: 200px;"><?= $descriptionShort ?></td>
                                <td><?= $dateEvent ?></td>
                                <td><?= $lieu ?></td>
                                <td><span class="badge bg-secondary"><?= $typeEvent ?></span></td>
                                <td><?= $nombrePlaces ?></td>
                                <td>
                                        <span class="badge bg-<?= $placesRestantes > 0 ? 'success' : 'danger' ?>">
                                            <?= $inscriptionsCount ?> / <?= $nombrePlaces ?>
                                        </span>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $statut === 'actif' ? 'success' : 'secondary' ?>">
                                        <?= ucfirst($statut) ?>
                                    </span>
                                </td>
                                <td class="table-actions">
                                    <a href="event_inscriptions.php?id_event=<?= urlencode($idEvent) ?>&embed=1" 
                                       class="btn btn-sm btn-info" 
                                       title="Voir les inscriptions"
                                       onclick="if(typeof loadContent==='function'){ loadContent('event_inscriptions.php?id_event=<?= urlencode($idEvent) ?>&embed=1'); return false; }">
                                        <i class="ri-file-list-3-line"></i>
                                        </a>
                                    <a href="#" 
                                       onclick="loadContent('editevent.php?id=<?= urlencode($idEvent) ?>'); return false;" 
                                       class="btn btn-sm btn-warning">
                                        <i class="ri-edit-line"></i>
                                        </a>
                                    <a href="deleteevent.php?id=<?= urlencode($idEvent) ?>&embed=1" 
                                       class="btn btn-sm btn-danger" 
                                       onclick="if(typeof loadContent==='function'){ loadContent('deleteevent.php?id=<?= urlencode($idEvent) ?>&embed=1'); return false; }">
                                        <i class="ri-delete-bin-line"></i>
                                        </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                <?php else: ?>
                            <tr>
                                <td colspan="11" class="text-center py-5">
                        <i class="ri-calendar-line" style="font-size: 3rem; color: #dadde1;"></i>
                        <h5 class="mt-3 text-muted">Aucun événement</h5>
                        <p class="text-muted">Commencez par créer votre premier événement</p>
                        <a href="#" onclick="loadContent('addevent.php'); return false;" class="btn btn-primary mt-3">
                            <i class="ri-add-line me-1"></i> Créer un événement
                        </a>
                                </td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                    </div>
            </div>
        </div>
    </div>
</div>
<?php
if ($showFullPage) {
    $content = ob_get_clean();
    renderBackofficeLayout('Événements', $content);
} else {
    ob_end_flush();
}
?>

