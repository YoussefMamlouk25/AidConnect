<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../../config.php';
require_once '../../Model/offer.php';
require_once '../../Controller/offercontroller.php';
require_once '../../Controller/offerlikecontroller.php';
require_once 'layout.php';
// no updateoffercontroller here; we only list offers
$offerController = new OfferController();
$likeController = new OfferLikeController();
// Créer la table si elle n'existe pas
$likeController->createTableIfNotExists();
$list = $offerController->getAllOffers();

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
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="header-title">
                    <h4 class="card-title mb-0">Gestion des Offres</h4>
                    <p class="text-muted mb-0">Liste complète des offres AidConnect.</p>
                </div>
                <div>
                    <a href="addoffer.php" class="btn btn-primary" onclick="if(typeof loadContent==='function'){ loadContent('addoffer.php?ajax=1'); return false; }">
                        <i class="ri-add-line me-1"></i> Ajouter une Offre
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div id="addOfferFormContainer" style="display: none; margin-bottom: 1.5rem;"></div>
                <div id="editOfferFormContainer" style="display: none; margin-bottom: 1.5rem;"></div>
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Nom</th>
                                <th scope="col">Email</th>
                                <th scope="col">Téléphone</th>
                                <th scope="col">Type de Don</th>
                                <th scope="col">Description</th>
                                <th scope="col">Média</th>
                                <th scope="col">Likes</th>
                                <th scope="col" class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (is_array($list) && count($list) > 0): ?>
                                <?php foreach ($list as $offer): 
                                    $idOffer = isset($offer['id_offer']) ? $offer['id_offer'] : '';
                                    $nom = isset($offer['nom']) ? htmlspecialchars($offer['nom']) : 'N/A';
                                    $email = isset($offer['email']) ? htmlspecialchars($offer['email']) : 'N/A';
                                    $num = isset($offer['num']) ? htmlspecialchars($offer['num']) : 'N/A';
                                    $typeDon = isset($offer['type_don']) ? $offerController->getTypeDonLabel($offer['type_don']) : 'N/A';
                                    $description = isset($offer['description']) ? substr(htmlspecialchars($offer['description']), 0, 100) . '...' : 'N/A';
                                    $media = isset($offer['media']) ? $offer['media'] : '';
                                    
                                    // Récupérer le nombre de likes pour cette offre
                                    $likesCount = $likeController->getLikeCount($idOffer);
                                ?>
                                <tr>
                                    <td><?= htmlspecialchars($idOffer) ?></td>
                                    <td><?= $nom ?></td>
                                    <td><?= $email ?></td>
                                    <td><?= $num ?></td>
                                    <td>
                                        <span class="badge bg-info">
                                            <?= $typeDon ?>
                                        </span>
                                    </td>
                                    <td><?= $description ?></td>
                                    <td>
                                        <?php
                                            $mediaFiles = json_decode($offer['media'], true);
                                            if (json_last_error() === JSON_ERROR_NONE && is_array($mediaFiles) && !empty($mediaFiles)) {
                                                foreach (array_slice($mediaFiles, 0, 1) as $mediaFile) {
                                                    $mediaUrl = '../../public/uploads/' . $mediaFile;
                                                    $ext = strtolower(pathinfo($mediaFile, PATHINFO_EXTENSION));
                                                    if (in_array($ext, ['mp4','mov','webm'])) {
                                                        echo '<video src="'.htmlspecialchars($mediaUrl).'" controls style="width:52px;height:52px;object-fit:cover;border-radius:4px;"></video>';
                                                    } else {
                                                        echo '<img src="'.htmlspecialchars($mediaUrl).'" alt="Media" style="width:52px;height:52px;object-fit:cover;border-radius:4px;">';
                                                    }
                                                }
                                            } elseif (!empty($offer['media'])) {
                                                $mediaUrl = '../../public/uploads/' . $offer['media'];
                                                $ext = strtolower(pathinfo($offer['media'], PATHINFO_EXTENSION));
                                                if (in_array($ext, ['mp4','mov','webm'])) {
                                                    echo '<video src="'.htmlspecialchars($mediaUrl).'" controls style="width:52px;height:52px;object-fit:cover;border-radius:4px;"></video>';
                                                } else {
                                                    echo '<img src="'.htmlspecialchars($mediaUrl).'" alt="Media" style="width:52px;height:52px;object-fit:cover;border-radius:4px;">';
                                                }
                                            } else {
                                                echo '—';
                                            }
                                        ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-danger">
                                            <i class="ri-heart-fill me-1"></i><?= $likesCount ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <a href="offer_likes.php?id_offer=<?= urlencode($idOffer) ?>&ajax=1" class="btn btn-sm btn-outline-info me-1" onclick="if(typeof loadContent==='function'){ loadContent('offer_likes.php?id_offer=<?= urlencode($idOffer) ?>&ajax=1'); return false; }" title="Voir les likes">
                                            <i class="ri-heart-fill"></i>
                                        </a>
                                        <a href="updateoffer.php?id_offer=<?= urlencode($idOffer) ?>&ajax=1" class="btn btn-sm btn-outline-primary me-1" onclick="if(typeof loadContent==='function'){ loadContent('updateoffer.php?id_offer=<?= urlencode($idOffer) ?>&ajax=1'); return false; }" title="Éditer">
                                            <i class="ri-edit-line"></i>
                                        </a>
                                        <a href="deleteoffer.php?id_offer=<?= urlencode($idOffer) ?>&ajax=1" class="btn btn-sm btn-outline-danger" onclick="if(typeof loadContent==='function'){ loadContent('deleteoffer.php?id_offer=<?= urlencode($idOffer) ?>&ajax=1'); return false; }" title="Supprimer">
                                            <i class="ri-delete-bin-line"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">
                                        Aucune offre pour le moment.
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
<script>
let addOfferFormVisible = false;
function toggleAddOfferForm() {
	const container = document.getElementById('addOfferFormContainer');
	if (!container) {
		console.error('Container addOfferFormContainer not found');
		return;
	}
	
	if (!addOfferFormVisible) {
		// Afficher le conteneur
		container.style.display = 'block';
		container.innerHTML = '<p style="padding: 1rem; text-align: center;">Chargement du formulaire...</p>';
		
		// Charger le formulaire via AJAX
		fetch('addoffer.php?ajax=1', {
			headers: {
				'X-Requested-With': 'XMLHttpRequest'
			}
		})
		.then(response => {
			if (!response.ok) {
				throw new Error('Erreur HTTP: ' + response.status);
			}
			return response.text();
		})
		.then(html => {
			container.innerHTML = html;
			addOfferFormVisible = true;
		})
		.catch(error => {
			console.error('Erreur lors du chargement du formulaire:', error);
			container.innerHTML = '<p style="padding: 1rem; color: red;">Erreur lors du chargement du formulaire. <a href="addoffer.php">Cliquez ici pour ouvrir le formulaire</a></p>';
		});
	} else {
		container.style.display = 'none';
		container.innerHTML = '';
		addOfferFormVisible = false;
	}
}

</script>
<?php
if ($showFullPage) {
    $content = ob_get_clean();
	renderBackofficeLayout('Liste des Offres', $content, ['assets/css/showofferback.css', 'assets/css/addform.css']);
} else {
    // Si AJAX, inclure le CSS inline
    echo '<link rel="stylesheet" href="assets/css/showofferback.css">';
	echo '<link rel="stylesheet" href="assets/css/addform.css">';
}
?>

