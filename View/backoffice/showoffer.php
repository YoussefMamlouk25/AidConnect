<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../../config.php';
require_once '../../Model/offer.php';
require_once '../../Controller/offercontroller.php';
// no updateoffercontroller here; we only list offers
$offerController = new OfferController();
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
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <div class="header-title">
                    <h4 class="card-title">Offres</h4>
                </div>
                <div>
                    <button onclick="loadContent('addoffer.php')" class="btn btn-primary btn-sm">
                        <i class="ri-add-line me-1"></i> Ajouter une offre
                    </button>
                </div>
            </div>
<div class="offers-fragment">
	<div id="addOfferFormContainer" style="display: none; margin-bottom: 1.5rem;"></div>
	<div id="editOfferFormContainer" style="display: none; margin-bottom: 1.5rem;"></div>
		<table class="post-table">
			<thead>
				<tr>
					<th>ID</th>
					<th>Nom</th>
					<th>Email</th>
					<th>Téléphone</th>
					<th>Type de Don</th>
					<th>Description</th>
					<th>Média</th>
					<th>Actions</th>
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
					$media = isset($offer['media']) ? htmlspecialchars($offer['media']) : 'Aucun';
				?>
				<tr>
					<td><?= htmlspecialchars($idOffer) ?></td>
					<td><?= $nom ?></td>
					<td><?= $email ?></td>
					<td><?= $num ?></td>
					<td><strong><?= $typeDon ?></strong></td>
					<td><?= $description ?></td>
					<td class="media-cell">
					<?php
						$mediaFiles = json_decode($offer['media'], true);
						if (json_last_error() === JSON_ERROR_NONE && is_array($mediaFiles)) {
							foreach ($mediaFiles as $mediaFile) {
								$mediaUrl = '../../public/uploads/' . $mediaFile;
								$ext = strtolower(pathinfo($mediaFile, PATHINFO_EXTENSION));
								if (in_array($ext, ['mp4','mov','webm'])) {
									echo '<video src="'.htmlspecialchars($mediaUrl).'" controls class="media-thumb"></video>';
								} else {
									echo '<img src="'.htmlspecialchars($mediaUrl).'" alt="Media image" class="media-thumb">';
								}
							}
						} elseif (!empty($offer['media'])) {
							$mediaUrl = '../../public/uploads/' . $offer['media'];
							$ext = strtolower(pathinfo($offer['media'], PATHINFO_EXTENSION));
							if (in_array($ext, ['mp4','mov','webm'])) {
								echo '<video src="'.htmlspecialchars($mediaUrl).'" controls class="media-thumb"></video>';
							} else {
								echo '<img src="'.htmlspecialchars($mediaUrl).'" alt="Media image" class="media-thumb">';
							}
						} else {
							echo 'Aucun';
						}
					?>
					</td>
					<td class="table-actions">
						<a href="updateoffer.php?id_offer=<?= urlencode($idOffer) ?>" class="btn btn-sm btn-primary" onclick="openEditOffer(<?= (int)$idOffer ?>); return false;">
							<i class="ri-edit-line me-1"></i> Éditer
						</a>
						<a href="deleteoffer.php?id_offer=<?= urlencode($idOffer) ?>&embed=1" class="btn btn-sm btn-danger" onclick="if(typeof loadContent==='function'){ loadContent('deleteoffer.php?id_offer=<?= urlencode($idOffer) ?>&embed=1'); return false; }">
							<i class="ri-delete-bin-line me-1"></i> Supprimer
						</a>
					</td>
				</tr>
				<?php endforeach; ?>
			<?php else: ?>
				<tr><td colspan="8" class="no-data">Aucune offre trouvée</td></tr>
			<?php endif; ?>
			</tbody>
		</table>
</div>
            </div>
        </div>
    </div>
</div>
<style>
.media-cell {
	display: grid;
	grid-template-columns: repeat(2, 1fr);
	grid-auto-rows: 60px;
	gap: 4px;
	justify-items: center;
	align-items: center;
}
.media-thumb {
	width: 45px;
	height: 45px;
	object-fit: cover;
	border-radius: 4px;
	margin: 0;
	display: block;
}
</style>
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

// Ouvrir le formulaire d'édition d'une offre dans le layout actuel (sans quitter la page)
function openEditOffer(idOffer) {
	const container = document.getElementById('editOfferFormContainer');
	if (!container) {
		console.error('Container editOfferFormContainer not found');
		return;
	}

	container.style.display = 'block';
	container.innerHTML = '<p style="padding: 1rem; text-align: center;">Chargement du formulaire d\'édition...</p>';

	fetch('updateoffer.php?id_offer=' + encodeURIComponent(idOffer) + '&ajax=1', {
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
		container.scrollIntoView({ behavior: 'smooth', block: 'start' });
	})
	.catch(error => {
		console.error('Erreur lors du chargement du formulaire d\'édition:', error);
		container.innerHTML = '<p style="padding: 1rem; color: red;">Erreur lors du chargement du formulaire d\'édition.</p>';
	});
}
</script>
<?php
if ($showFullPage) {
    $content = ob_get_clean();
	renderBackofficeLayout('Liste des Offres', $content, ['showofferback.css', 'addform.css']);
} else {
    // Si AJAX, inclure le CSS inline
    echo '<link rel="stylesheet" href="showofferback.css">';
	echo '<link rel="stylesheet" href="addform.css">';
}
?>

