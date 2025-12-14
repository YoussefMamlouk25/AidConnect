<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../../config.php';
require_once '../../../event/Model/Event.php';
require_once '../../../event/Model/Inscription.php';

$eventModel = new Event(Config::getConnexion());
$inscriptionModel = new Inscription(Config::getConnexion());

// Récupérer tous les événements
$stmt = $eventModel->readAll();
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Ajouter le nombre d'inscriptions pour chaque événement
foreach($events as &$event) {
    $eventModel->setIdEvent($event['id_event']);
    $event['inscriptions_count'] = $eventModel->countInscriptions();
}

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
                                <th>Titre</th>
                                <th>Description</th>
                                <th>Date</th>
                                <th>Lieu</th>
                                <th>Type</th>
                                <th>Places</th>
                                <th>Inscrits</th>
                                <th>Image</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (is_array($events) && count($events) > 0): ?>
                            <?php foreach ($events as $event): 
                                $idEvent = isset($event['id_event']) ? $event['id_event'] : '';
                                $titre = isset($event['titre']) ? htmlspecialchars($event['titre']) : 'N/A';
                                $description = isset($event['description']) ? substr(htmlspecialchars($event['description']), 0, 100) . '...' : 'N/A';
                                $dateEvent = isset($event['date_event']) ? date('d/m/Y H:i', strtotime($event['date_event'])) : 'N/A';
                                $lieu = isset($event['lieu']) ? htmlspecialchars($event['lieu']) : 'N/A';
                                $typeEvent = isset($event['type_event']) ? htmlspecialchars($event['type_event']) : 'N/A';
                                $nombrePlaces = isset($event['nombre_places']) ? $event['nombre_places'] : 0;
                                $inscriptionsCount = isset($event['inscriptions_count']) ? $event['inscriptions_count'] : 0;
                                $placesRestantes = $nombrePlaces - $inscriptionsCount;
                                $image = isset($event['image']) ? $event['image'] : '';
                                $statut = isset($event['statut']) ? $event['statut'] : 'actif';
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($idEvent) ?></td>
                                <td><strong><?= $titre ?></strong></td>
                                <td><?= $description ?></td>
                                <td><?= $dateEvent ?></td>
                                <td><?= $lieu ?></td>
                                <td>
                                    <span class="badge bg-secondary">
                                        <?= $typeEvent ?>
                                    </span>
                                </td>
                                <td><?= $nombrePlaces ?></td>
                                <td class="text-center">
                                    <span class="badge bg-<?= $placesRestantes > 0 ? 'success' : 'danger' ?>">
                                        <?= $inscriptionsCount ?> / <?= $nombrePlaces ?>
                                    </span>
                                </td>
                                <td class="media-cell">
                                <?php
                                    if (!empty($image)) {
                                        // Adapter le chemin de l'image
                                        if (strpos($image, 'event/') === 0) {
                                            $imageUrl = '../../../' . $image;
                                        } else {
                                            $imageUrl = '../../../event/' . $image;
                                        }
                                        
                                        // Vérifier si le fichier existe
                                        $imagePath = __DIR__ . '/../../../event/' . $image;
                                        if (file_exists($imagePath)) {
                                            $ext = strtolower(pathinfo($image, PATHINFO_EXTENSION));
                                            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                                                echo '<img src="'.htmlspecialchars($imageUrl).'" alt="Event image" class="media-thumb" style="max-width: 100px; max-height: 100px; object-fit: cover;">';
                                            } else {
                                                echo 'Fichier image';
                                            }
                                        } else {
                                            echo 'Image non trouvée';
                                        }
                                    } else {
                                        echo 'Aucune';
                                    }
                                ?>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $statut === 'actif' ? 'success' : 'secondary' ?>">
                                        <?= ucfirst($statut) ?>
                                    </span>
                                </td>
                                <td class="table-actions">
                                    <a href="#" onclick="loadContent('../Controller/inscriptioncontroller.php?action=liste&event_id=<?= urlencode($idEvent) ?>'); return false;" class="btn btn-sm btn-info">
                                        <i class="ri-file-list-3-line me-1"></i> Inscriptions
                                    </a>
                                    <a href="#" onclick="loadContent('../Controller/eventcontroller.php?action=edit&id=<?= urlencode($idEvent) ?>'); return false;" class="btn btn-sm btn-warning">
                                        <i class="ri-edit-line me-1"></i> Modifier
                                    </a>
                                    <a href="../Controller/eventcontroller.php?action=delete&id=<?= urlencode($idEvent) ?>&embed=1" class="btn btn-sm btn-danger" onclick="if(confirm(\'Confirmer la suppression ?\')){ if(typeof loadContent===\'function\'){ loadContent(this.href); return false; } } return false;">
                                        <i class="ri-delete-bin-line me-1"></i> Supprimer
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
                                    <a href="#" onclick="loadContent('../Controller/eventcontroller.php?action=create'); return false;" class="btn btn-primary mt-3">
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

