<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../../config.php';
require_once '../../Model/offer.php';
require_once '../../Controller/offercontroller.php';
// no updateoffercontroller here; we only list offers
$offerController = new OfferController();
$list = $offerController->getAllOffers();
?>
<link rel="stylesheet" href="showofferback.css">
<div class="offers-fragment">
	<div class="d-flex justify-content-between align-items-center mb-3">
		<h2>Offres</h2>
		<button class="btn btn-primary btn-add-offer" onclick="if(typeof loadContent==='function'){ loadContent('add_offer.php?embed=1'); return false; }">
			Ajouter une Offre
		</button>
	</div>
	<div class="container">
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
					<td>
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
						<a href="updateoffer.php?id_offer=<?= urlencode($idOffer) ?>" class="btn-action btn-edit" onclick="if(typeof loadContent==='function'){ loadContent('updateoffer.php?id_offer=<?= urlencode($idOffer) ?>'); return false; }">✎ Éditer</a>
						<a href="deleteoffer.php?id_offer=<?= urlencode($idOffer) ?>&embed=1" class="btn-action btn-delete" onclick="if(typeof loadContent==='function'){ loadContent('deleteoffer.php?id_offer=<?= urlencode($idOffer) ?>&embed=1'); return false; }">🗑 Supprimer</a>
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
<style>
.media-thumb {
	width: 80px;
	height: 80px;
	object-fit: cover;
	border-radius: 6px;
	margin: 2px;
}

/* Button Add Offer Styles */
.offers-fragment .btn.btn-primary.btn-add-offer,
.offers-fragment button.btn-primary.btn-add-offer {
	background: #ffffff !important;
	color: #1f2937 !important;
	border: 1.5px solid #e5e7eb !important;
	padding: 0.75rem 1.5rem !important;
	border-radius: 0.5rem !important;
	font-size: 0.9375rem !important;
	font-weight: 600 !important;
	cursor: pointer;
	transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
	box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05) !important;
	text-decoration: none;
	display: inline-flex !important;
	align-items: center;
	justify-content: center;
	gap: 0.5rem;
	position: relative;
	overflow: hidden;
	letter-spacing: 0.01em;
	line-height: 1.5;
	white-space: nowrap;
	user-select: none;
}

.offers-fragment .btn.btn-primary.btn-add-offer:hover,
.offers-fragment button.btn-primary.btn-add-offer:hover {
	background: #f9fafb !important;
	border-color: #2563eb !important;
	color: #2563eb !important;
	transform: translateY(-1px);
	box-shadow: 0 4px 12px rgba(37, 99, 235, 0.15), 0 2px 4px rgba(37, 99, 235, 0.1) !important;
}

.offers-fragment .btn.btn-primary.btn-add-offer:active,
.offers-fragment button.btn-primary.btn-add-offer:active {
	transform: translateY(0);
	box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1) !important;
	background: #f3f4f6 !important;
	border-color: #1d4ed8 !important;
	color: #1d4ed8 !important;
}

.offers-fragment .btn.btn-primary.btn-add-offer:focus,
.offers-fragment button.btn-primary.btn-add-offer:focus {
	outline: none !important;
	border-color: #2563eb !important;
	box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1), 0 2px 4px rgba(37, 99, 235, 0.1) !important;
	color: #2563eb !important;
}
</style>

