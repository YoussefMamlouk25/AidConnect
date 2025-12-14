<?php
/**
 * Contenu pour la liste des inscriptions dans le backoffice
 * Peut afficher toutes les inscriptions ou celles d'un événement spécifique
 */

$embed = isset($_GET['embed']) && $_GET['embed'] === '1';
$isAjax = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') 
          || (isset($_GET['ajax']) && $_GET['ajax'] == '1');
$showFullPage = !$embed && !$isAjax;

if ($showFullPage) {
    require_once 'layout.php';
    ob_start();
}

$event_id = isset($_GET['event_id']) ? intval($_GET['event_id']) : null;
$view_all = isset($_GET['view']) && $_GET['view'] === 'all';

// Calculer les statistiques
$total_inscriptions = count($inscriptions);
$en_attente = count(array_filter($inscriptions, fn($i) => $i['statut'] === 'en_attente'));
$confirme = count(array_filter($inscriptions, fn($i) => $i['statut'] === 'confirme'));
$annule = count(array_filter($inscriptions, fn($i) => $i['statut'] === 'annule'));
?>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <div class="header-title">
                    <h4 class="card-title">
                        <?php if (isset($event) && $event): ?>
                            📋 Inscriptions - <?php echo htmlspecialchars($event->getTitre()); ?>
                        <?php else: ?>
                            📋 Toutes les Inscriptions
                        <?php endif; ?>
                    </h4>
                </div>
                <div>
                    <a href="#" onclick="loadContent('showinscriptions.php'); return false;" class="btn btn-sm btn-secondary">
                        <i class="ri-arrow-left-line me-1"></i> Retour
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Informations de l'événement si spécifique -->
                <?php if (isset($event) && $event): ?>
                    <div class="alert alert-info mb-4">
                        <h5 class="mb-2">📌 <?php echo htmlspecialchars($event->getTitre()); ?></h5>
                        <div class="row">
                            <div class="col-md-3">
                                <strong>📅 Date:</strong> <?php echo date('d/m/Y à H:i', strtotime($event->getDateEvent())); ?>
                            </div>
                            <div class="col-md-3">
                                <strong>📍 Lieu:</strong> <?php echo htmlspecialchars($event->getLieu()); ?>
                            </div>
                            <div class="col-md-3">
                                <strong>🎯 Type:</strong> <?php echo htmlspecialchars($event->getTypeEvent()); ?>
                            </div>
                            <div class="col-md-3">
                                <strong>💺 Capacité:</strong> <?php echo $event->getNombrePlaces(); ?> places
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Statistiques -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h6 class="card-title">Total</h6>
                                <h3 class="mb-0"><?php echo $total_inscriptions; ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <h6 class="card-title">En Attente</h6>
                                <h3 class="mb-0"><?php echo $en_attente; ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h6 class="card-title">Confirmées</h6>
                                <h3 class="mb-0"><?php echo $confirme; ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-danger text-white">
                            <div class="card-body">
                                <h6 class="card-title">Annulées</h6>
                                <h3 class="mb-0"><?php echo $annule; ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Tableau des inscriptions -->
                <?php if (!empty($inscriptions) && count($inscriptions) > 0): ?>
                    <div class="alert alert-success mb-3">
                        <i class="ri-user-line"></i> 
                        <strong><?php echo count($inscriptions); ?> membre(s) inscrit(s)</strong> 
                        <?php if (isset($event) && $event): ?>
                            pour l'événement "<?php echo htmlspecialchars($event->getTitre()); ?>"
                        <?php else: ?>
                            au total
                        <?php endif; ?>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Membre / Participant</th>
                                    <th>Contact</th>
                                    <?php if ($view_all || !$event_id): ?>
                                        <th>Événement</th>
                                    <?php endif; ?>
                                    <th>Date Inscription</th>
                                    <th>Statut</th>
                                    <th>Motivation</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($inscriptions as $inscription): ?>
                                    <tr>
                                        <td><strong>#<?php echo htmlspecialchars($inscription['id_inscription']); ?></strong></td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <?php 
                                                // Afficher la photo de profil si le membre est dans le système
                                                $profilePic = null;
                                                $isMember = !empty($inscription['id_user']);
                                                
                                                if ($isMember && !empty($inscription['profile_pic'])) {
                                                    // Vérifier si le chemin contient déjà 'public/uploads/profile/'
                                                    if (strpos($inscription['profile_pic'], 'public/uploads/profile/') !== false) {
                                                        $profilePic = '../../' . htmlspecialchars($inscription['profile_pic']);
                                                    } else {
                                                        $profilePic = '../../public/uploads/profile/' . htmlspecialchars($inscription['profile_pic']);
                                                    }
                                                }
                                                
                                                // Vérifier si le fichier existe
                                                $profilePicPath = $profilePic ? str_replace('../../', '', $profilePic) : null;
                                                if ($profilePic && $profilePicPath && file_exists(__DIR__ . '/../../' . $profilePicPath)):
                                                ?>
                                                    <img src="<?php echo $profilePic; ?>" 
                                                         alt="Profile" 
                                                         class="rounded-circle" 
                                                         style="width: 40px; height: 40px; object-fit: cover;">
                                                <?php else: ?>
                                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" 
                                                         style="width: 40px; height: 40px; font-weight: bold;">
                                                        <?php 
                                                        $initials = strtoupper(substr($inscription['nom'], 0, 1) . substr($inscription['prenom'], 0, 1));
                                                        echo $initials;
                                                        ?>
                                                    </div>
                                                <?php endif; ?>
                                                <div>
                                                    <strong><?php echo htmlspecialchars($inscription['nom'] . ' ' . $inscription['prenom']); ?></strong>
                                                    <?php if ($isMember): ?>
                                                        <br><small class="text-success">
                                                            <i class="ri-user-line"></i> Membre du système
                                                            <?php if (!empty($inscription['role']) && $inscription['role'] === 'admin'): ?>
                                                                <span class="badge bg-danger ms-1">Admin</span>
                                                            <?php endif; ?>
                                                        </small>
                                                    <?php else: ?>
                                                        <br><small class="text-muted">
                                                            <i class="ri-user-unfollow-line"></i> Visiteur
                                                        </small>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div><i class="ri-mail-line"></i> <?php echo htmlspecialchars($inscription['email']); ?></div>
                                            <div><i class="ri-phone-line"></i> <?php echo htmlspecialchars($inscription['telephone']); ?></div>
                                            <?php if ($isMember && !empty($inscription['user_email'])): ?>
                                                <div class="text-muted small">
                                                    <i class="ri-account-circle-line"></i> ID: <?php echo htmlspecialchars($inscription['id_user']); ?>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <?php if ($view_all || !$event_id): ?>
                                            <td>
                                                <strong><?php echo htmlspecialchars($inscription['event_titre'] ?? 'N/A'); ?></strong>
                                                <div class="text-muted small">
                                                    <?php echo !empty($inscription['date_event']) ? date('d/m/Y', strtotime($inscription['date_event'])) : 'N/A'; ?>
                                                </div>
                                            </td>
                                        <?php endif; ?>
                                        <td>
                                            <?php 
                                            $date_inscription = new DateTime($inscription['date_inscription']);
                                            echo $date_inscription->format('d/m/Y à H:i');
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            $statutClass = 'secondary';
                                            $statutText = 'En attente';
                                            if($inscription['statut'] === 'confirme') {
                                                $statutClass = 'success';
                                                $statutText = 'Confirmé';
                                            } elseif($inscription['statut'] === 'annule') {
                                                $statutClass = 'danger';
                                                $statutText = 'Annulé';
                                            }
                                            ?>
                                            <span class="badge bg-<?php echo $statutClass; ?>">
                                                <?php echo $statutText; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div style="max-width: 300px; font-size: 13px;">
                                                <?php 
                                                $pourquoi = htmlspecialchars($inscription['pourquoi']);
                                                echo strlen($pourquoi) > 100 ? substr($pourquoi, 0, 100) . '...' : $pourquoi;
                                                ?>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if($inscription['statut'] === 'en_attente'): ?>
                                                <a href="#" 
                                                   onclick="confirmInscriptionList(<?php echo $inscription['id_inscription']; ?>, <?php echo isset($event_id) && $event_id ? $event_id : 'null'; ?>); return false;" 
                                                   class="btn btn-sm btn-success">
                                                    <i class="ri-check-line"></i> Confirmer
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="ri-file-list-3-line" style="font-size: 3rem; color: #dadde1;"></i>
                        <h5 class="mt-3 text-muted">Aucune inscription trouvée.</h5>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php
if ($showFullPage) {
    $content = ob_get_clean();
    renderBackofficeLayout('Inscriptions', $content);
}
?>

<script>
function confirmInscriptionList(idInscription, eventId) {
    if (!confirm('Êtes-vous sûr de vouloir confirmer cette inscription ?')) {
        return false;
    }
    
    // Afficher un loader
    const contentArea = document.getElementById('content-area');
    if (contentArea) {
        contentArea.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div><p class="mt-3">Confirmation en cours...</p></div>';
    }
    
    // Construire l'URL de confirmation
    let confirmUrl = '../Controller/inscriptioncontroller.php?action=confirmer&id=' + idInscription + '&ajax=1';
    if (eventId && eventId !== 'null') {
        confirmUrl += '&event_id=' + eventId;
    }
    
    // Construire l'URL de rechargement
    let reloadUrl = '../Controller/inscriptioncontroller.php?action=liste&ajax=1';
    if (eventId && eventId !== 'null') {
        reloadUrl += '&event_id=' + eventId;
    } else {
        reloadUrl += '&view=all';
    }
    
    fetch(confirmUrl, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(function(response) {
        if (!response.ok) {
            throw new Error('Erreur ' + response.status);
        }
        return response.text();
    })
    .then(function(data) {
        // Recharger la page pour voir le statut mis à jour
        if (typeof loadContent === 'function') {
            loadContent(reloadUrl);
        } else {
            window.location.reload();
        }
    })
    .catch(function(error) {
        console.error('Erreur:', error);
        // Recharger quand même la page
        if (typeof loadContent === 'function') {
            loadContent(reloadUrl);
        } else {
            window.location.reload();
        }
    });
    
    return false;
}
</script>

