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
	<h2>Offres</h2>
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
					<td><?= $media ?></td>
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

