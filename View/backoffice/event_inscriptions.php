<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../../config.php';
require_once '../../Model/Event.php';
require_once '../../Model/Inscription.php';

$db = Config::getConnexion();
$eventModel = new Event($db);
$inscriptionModel = new Inscription($db);

$id_event = isset($_GET['id_event']) ? (int)$_GET['id_event'] : 0;

if ($id_event <= 0) {
    echo '<div class="alert alert-danger">ID événement invalide</div>';
    exit;
}

// Récupérer les informations de l'événement
$eventModel->setIdEvent($id_event);
if (!$eventModel->readOne()) {
    echo '<div class="alert alert-danger">Événement introuvable</div>';
    exit;
}

$event = $eventModel;

// Récupérer les inscriptions pour cet événement avec les infos des membres
$query = "SELECT i.*, 
                 e.titre as event_titre, e.date_event, e.lieu, e.type_event,
                 u.id_user, u.name as user_name, 
                 u.email as user_email, u.profile_pic, u.role
          FROM inscriptions i
          LEFT JOIN events e ON i.id_event = e.id_event
          LEFT JOIN user u ON i.email = u.email
          WHERE i.id_event = :id_event
          ORDER BY i.date_inscription DESC";

$stmt = $db->prepare($query);
$stmt->bindParam(':id_event', $id_event);
$stmt->execute();
$inscriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);
$inscriptionsCount = count($inscriptions);

$embed = isset($_GET['embed']) && $_GET['embed'] === '1';
$isAjax = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') 
          || (isset($_GET['ajax']) && $_GET['ajax'] == '1');

// Si on accède directement à la page (pas via AJAX), rediriger vers dashboard.php
// qui chargera automatiquement le contenu dans content-area
// Même si embed=1 est présent, on redirige vers dashboard pour charger dans content-area
if (!$isAjax) {
    header('Location: dashboard.php?load=event_inscriptions.php?id_event=' . urlencode($id_event));
    exit;
}

// En mode AJAX ou embed, on affiche directement le contenu SANS layout
// Pas de renderBackofficeLayout(), pas de ob_start(), juste le contenu HTML
?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <h4 class="card-title mb-0">
                <i class="ri-file-list-3-line text-info me-2"></i>
                Inscriptions pour cet événement
            </h4>
            <small class="text-muted">Événement: <?php echo htmlspecialchars($event->getTitre()); ?> - <?php echo htmlspecialchars($event->getTypeEvent()); ?></small>
        </div>
        <button type="button" class="btn-close" onclick="if(typeof loadContent==='function'){ loadContent('showevent.php'); return false; } else { window.history.back(); }"></button>
    </div>
    <div class="card-body">
        <div class="mb-3">
            <div class="alert alert-info">
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
            <span class="badge bg-primary" style="font-size: 1rem; padding: 0.5rem 1rem;">
                <i class="ri-user-line me-1"></i>
                <?php echo $inscriptionsCount; ?> inscription<?php echo $inscriptionsCount > 1 ? 's' : ''; ?>
            </span>
        </div>
        
        <?php if ($inscriptionsCount > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Membre / Participant</th>
                            <th>Contact</th>
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
                                    <?php if ($isMember && !empty($inscription['id_user'])): ?>
                                        <div class="text-muted small">
                                            <i class="ri-account-circle-line"></i> ID: <?php echo htmlspecialchars($inscription['id_user']); ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php 
                                    if (!empty($inscription['date_inscription'])) {
                                        $date = new DateTime($inscription['date_inscription']);
                                        echo $date->format('d/m/Y à H:i');
                                    } else {
                                        echo 'N/A';
                                    }
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
                                        $pourquoi = htmlspecialchars($inscription['pourquoi'] ?? '');
                                        echo strlen($pourquoi) > 100 ? substr($pourquoi, 0, 100) . '...' : $pourquoi;
                                        ?>
                                    </div>
                                </td>
                                <td>
                                    <?php if($inscription['statut'] === 'en_attente'): ?>
                                        <div class="btn-group" role="group">
                                            <a href="#" 
                                               onclick="confirmInscriptionEvent(<?php echo $inscription['id_inscription']; ?>, <?php echo $id_event; ?>); return false;" 
                                               class="btn btn-sm btn-success">
                                                <i class="ri-check-line"></i> Confirmer
                                            </a>
                                            <a href="#" 
                                               onclick="declineInscriptionEvent(<?php echo $inscription['id_inscription']; ?>, <?php echo $id_event; ?>); return false;" 
                                               class="btn btn-sm btn-danger">
                                                <i class="ri-close-line"></i> Refuser
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="ri-file-list-3-line" style="font-size: 4rem; color: #ccc;"></i>
                <h5 class="mt-3">Aucune inscription pour cet événement</h5>
                <p class="text-muted">Cet événement n'a pas encore reçu d'inscriptions.</p>
            </div>
        <?php endif; ?>
    </div>
    <div class="card-footer">
        <button type="button" class="btn btn-secondary" onclick="if(typeof loadContent==='function'){ loadContent('showevent.php'); return false; } else { window.history.back(); }">
            <i class="ri-arrow-left-line me-1"></i> Retour
        </button>
    </div>
</div>

<script>
function confirmInscriptionEvent(idInscription, idEvent) {
    if (!confirm('Êtes-vous sûr de vouloir confirmer cette inscription ?')) {
        return false;
    }
    
    // Afficher un loader
    const contentArea = document.getElementById('content-area');
    if (contentArea) {
        contentArea.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div><p class="mt-3">Confirmation en cours...</p></div>';
    }
    
    // Calculer le chemin vers le contrôleur
    // Utiliser un chemin absolu basé sur l'URL actuelle pour éviter les problèmes de chemin relatif
    const currentPath = window.location.pathname;
    let confirmUrl;
    // Trouver la position de /View/backoffice/ dans le chemin
    const viewBackofficeIndex = currentPath.indexOf('/View/backoffice/');
    if (viewBackofficeIndex !== -1) {
        // Extraire le chemin de base jusqu'à /View/
        const basePath = currentPath.substring(0, viewBackofficeIndex);
        confirmUrl = basePath + '/Controller/inscriptioncontroller.php?action=confirmer&id=' + idInscription + '&event_id=' + idEvent + '&ajax=1';
    } else {
        // Fallback: utiliser le chemin relatif
        confirmUrl = '../../Controller/inscriptioncontroller.php?action=confirmer&id=' + idInscription + '&event_id=' + idEvent + '&ajax=1';
    }
    const reloadUrl = 'event_inscriptions.php?id_event=' + idEvent + '&ajax=1';
    
    // Faire la requête de confirmation et recharger immédiatement après
    fetch(confirmUrl, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(function(response) {
        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('application/json')) {
            return response.json().then(function(data) {
                if (!data.success) {
                    throw new Error(data.message || 'Erreur lors de la confirmation');
                }
                return data;
            });
        }
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
            window.location.href = reloadUrl;
        }
    })
    .catch(function(error) {
        console.error('Erreur:', error);
        alert('Erreur lors de la confirmation: ' + error.message);
        // Recharger quand même la page
        if (typeof loadContent === 'function') {
            loadContent(reloadUrl);
        } else {
            window.location.href = reloadUrl;
        }
    });
    
    return false;
}

function declineInscriptionEvent(idInscription, idEvent) {
    if (!confirm('Êtes-vous sûr de vouloir refuser cette inscription ?')) {
        return false;
    }
    
    // Afficher un loader
    const contentArea = document.getElementById('content-area');
    if (contentArea) {
        contentArea.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div><p class="mt-3">Annulation en cours...</p></div>';
    }
    
    // Calculer le chemin vers le contrôleur
    const currentPath = window.location.pathname;
    let declineUrl;
    const viewBackofficeIndex = currentPath.indexOf('/View/backoffice/');
    if (viewBackofficeIndex !== -1) {
        const basePath = currentPath.substring(0, viewBackofficeIndex);
        declineUrl = basePath + '/Controller/inscriptioncontroller.php?action=annuler&id=' + idInscription + '&event_id=' + idEvent + '&ajax=1';
    } else {
        declineUrl = '../../Controller/inscriptioncontroller.php?action=annuler&id=' + idInscription + '&event_id=' + idEvent + '&ajax=1';
    }
    const reloadUrl = 'event_inscriptions.php?id_event=' + idEvent + '&ajax=1';
    
    // Faire la requête d'annulation et recharger immédiatement après
    fetch(declineUrl, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(function(response) {
        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('application/json')) {
            return response.json().then(function(data) {
                if (!data.success) {
                    throw new Error(data.message || 'Erreur lors de l\'annulation');
                }
                return data;
            });
        }
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
            window.location.href = reloadUrl;
        }
    })
    .catch(function(error) {
        console.error('Erreur:', error);
        alert('Erreur lors de l\'annulation: ' + error.message);
        // Recharger quand même la page
        if (typeof loadContent === 'function') {
            loadContent(reloadUrl);
        } else {
            window.location.href = reloadUrl;
        }
    });
    
    return false;
}
</script>

