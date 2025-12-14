<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once '../../config.php';
require_once '../../Model/Event.php';
require_once '../../Model/Inscription.php';

$db = Config::getConnexion();
$eventModel = new Event($db);
$inscriptionModel = new Inscription($db);

// Vérifier si on veut voir les inscriptions d'un événement spécifique
$event_id = isset($_GET['event_id']) ? intval($_GET['event_id']) : null;
$view_all = isset($_GET['view']) && $_GET['view'] === 'all';

// Si on demande les inscriptions d'un événement spécifique, rediriger vers le contrôleur
if ($event_id) {
    header('Location: ../Controller/inscriptioncontroller.php?action=liste&event_id=' . $event_id);
    exit;
}

// Si on demande toutes les inscriptions, rediriger vers le contrôleur
if ($view_all) {
    header('Location: ../Controller/inscriptioncontroller.php?action=liste&view=all');
    exit;
}

// Récupérer tous les événements
$stmt = $eventModel->readAll();
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer toutes les inscriptions pour calculer les stats
$stmt = $inscriptionModel->readAll();
$all_inscriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculer les statistiques par événement
foreach ($events as &$event) {
    $eventModel->setIdEvent($event['id_event']);
    $event['inscriptions_count'] = $eventModel->countInscriptions();
    
    // Compter par statut
    $query = "SELECT statut, COUNT(*) as count 
              FROM inscriptions 
              WHERE id_event = :id_event 
              GROUP BY statut";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id_event', $event['id_event']);
    $stmt->execute();
    $stats = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $event['stats'] = [
        'en_attente' => 0,
        'confirme' => 0,
        'annule' => 0
    ];
    
    foreach ($stats as $stat) {
        $event['stats'][$stat['statut']] = (int)$stat['count'];
    }
}

// Statistiques globales
$total_inscriptions = count($all_inscriptions);
$en_attente_total = count(array_filter($all_inscriptions, fn($i) => $i['statut'] === 'en_attente'));
$confirme_total = count(array_filter($all_inscriptions, fn($i) => $i['statut'] === 'confirme'));
$annule_total = count(array_filter($all_inscriptions, fn($i) => $i['statut'] === 'annule'));

$embed = isset($_GET['embed']) && $_GET['embed'] === '1';
$isAjax = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') 
          || (isset($_GET['ajax']) && $_GET['ajax'] == '1');
$showFullPage = !$embed && !$isAjax;

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
                    <h4 class="card-title">📋 Gestion des Inscriptions</h4>
                </div>
                <div>
                    <a href="#" onclick="loadContent('../Controller/inscriptioncontroller.php?action=liste&view=all'); return false;" class="btn btn-primary btn-sm">
                        <i class="ri-list-check"></i> Voir toutes les inscriptions
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Statistiques globales -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h6 class="card-title">Total Inscriptions</h6>
                                <h3 class="mb-0"><?php echo $total_inscriptions; ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <h6 class="card-title">En Attente</h6>
                                <h3 class="mb-0"><?php echo $en_attente_total; ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h6 class="card-title">Confirmées</h6>
                                <h3 class="mb-0"><?php echo $confirme_total; ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-danger text-white">
                            <div class="card-body">
                                <h6 class="card-title">Annulées</h6>
                                <h3 class="mb-0"><?php echo $annule_total; ?></h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Liste des événements avec boutons -->
                <h5 class="mb-3">📅 Inscriptions par Événement</h5>
                <?php if (!empty($events) && count($events) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Titre de l'Événement</th>
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
                                <?php foreach ($events as $event): 
                                    $idEvent = isset($event['id_event']) ? $event['id_event'] : '';
                                    $titre = isset($event['titre']) ? htmlspecialchars($event['titre']) : 'N/A';
                                    $dateEvent = isset($event['date_event']) ? date('d/m/Y H:i', strtotime($event['date_event'])) : 'N/A';
                                    $lieu = isset($event['lieu']) ? htmlspecialchars($event['lieu']) : 'N/A';
                                    $typeEvent = isset($event['type_event']) ? htmlspecialchars($event['type_event']) : 'N/A';
                                    $nombrePlaces = isset($event['nombre_places']) ? $event['nombre_places'] : 'N/A';
                                    $inscriptionsCount = isset($event['inscriptions_count']) ? $event['inscriptions_count'] : 0;
                                    $statut = isset($event['statut']) ? htmlspecialchars($event['statut']) : 'N/A';
                                    
                                    $places_restantes = $nombrePlaces - $inscriptionsCount;
                                    $pourcentage = $nombrePlaces > 0 ? round(($inscriptionsCount / $nombrePlaces) * 100, 1) : 0;
                                ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($idEvent); ?></td>
                                        <td><strong><?php echo $titre; ?></strong></td>
                                        <td><?php echo $dateEvent; ?></td>
                                        <td><?php echo $lieu; ?></td>
                                        <td><span class="badge bg-secondary"><?php echo $typeEvent; ?></span></td>
                                        <td><?php echo $nombrePlaces; ?></td>
                                        <td>
                                            <strong class="text-<?php echo ($places_restantes > 0) ? 'success' : 'danger'; ?>">
                                                <?php echo $inscriptionsCount; ?>
                                            </strong>
                                            <small class="text-muted">(<?php echo $pourcentage; ?>%)</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php echo ($statut === 'actif') ? 'success' : 'secondary'; ?>">
                                                <?php echo ucfirst($statut); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="#" 
                                               onclick="loadContent('../Controller/inscriptioncontroller.php?action=liste&event_id=<?php echo urlencode($idEvent); ?>'); return false;" 
                                               class="btn btn-sm btn-primary">
                                                <i class="ri-eye-line me-1"></i> Voir Inscriptions
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="ri-calendar-line" style="font-size: 3rem; color: #dadde1;"></i>
                        <h5 class="mt-3 text-muted">Aucun événement trouvé.</h5>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php
if ($showFullPage) {
    $content = ob_get_clean();
    renderBackofficeLayout('Gestion des Inscriptions', $content, [], ['assets/js/inscriptions_dashboard.js']);
}
?>

