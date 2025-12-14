<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../../config.php';
require_once '../../Model/post.php';
require_once '../../Controller/postcontroller.php';
require_once '../../Controller/postlikecontroller.php';
require_once '../../Controller/postcommentcontroller.php';

$postController = new PostController();
$likeController = new PostLikeController();
$commentController = new PostCommentController();
// Créer les tables si elles n'existent pas
$likeController->createTableIfNotExists();
$commentController->createTableIfNotExists();
$posts = $postController->getAllPosts(); // Now returns array directly

// Ensure it's an array
if (!is_array($posts)) {
    $posts = [];
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
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="header-title">
                    <h4 class="card-title mb-0">Gestion des Posts</h4>
                    <p class="text-muted mb-0">Liste complète des posts AidConnect.</p>
                </div>
                <div>
                    <a href="addpost.php" class="btn btn-primary" onclick="if(typeof loadContent==='function'){ loadContent('addpost.php?ajax=1'); return false; }">
                        <i class="ri-add-line me-1"></i> Ajouter un Post
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div id="addPostFormContainer" style="display: none; margin-bottom: 1.5rem;"></div>
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Titre</th>
                                <th scope="col">Nom</th>
                                <th scope="col">Email</th>
                                <th scope="col">Téléphone</th>
                                <th scope="col">Région</th>
                                <th scope="col">Description</th>
                                <th scope="col">Média</th>
                                <th scope="col">Date</th>
                                <th scope="col">Likes</th>
                                <th scope="col">Commentaires</th>
                                <th scope="col" class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (is_array($posts) && count($posts) > 0): ?>
                                <?php foreach ($posts as $post): 
                                    $idPost = isset($post['id_post']) ? $post['id_post'] : '';
                                    $titre = isset($post['titre']) ? htmlspecialchars($post['titre']) : 'N/A';
                                    $nom = isset($post['nom']) ? htmlspecialchars($post['nom']) : 'N/A';
                                    $email = isset($post['email']) ? htmlspecialchars($post['email']) : 'N/A';
                                    $numero = isset($post['Numéro']) ? htmlspecialchars($post['Numéro']) : (isset($post['numero']) ? htmlspecialchars($post['numero']) : 'N/A');
                                    $region = isset($post['region']) ? htmlspecialchars($post['region']) : 'N/A';
                                    $description = isset($post['description']) ? substr(htmlspecialchars($post['description']), 0, 100) . '...' : 'N/A';
                                    $dateCreation = isset($post['date_creation']) ? htmlspecialchars($post['date_creation']) : 'N/A';
                                    $media = isset($post['media']) ? $post['media'] : '';
                                    
                                    // Récupérer le nombre de likes et commentaires pour ce post
                                    $likesCount = $likeController->getLikeCount($idPost);
                                    $commentsCount = isset($post['comments_count']) ? (int)$post['comments_count'] : $commentController->getCommentCount($idPost);
                                ?>
                                <tr>
                                    <td><?= htmlspecialchars($idPost) ?></td>
                                    <td><?= $titre ?></td>
                                    <td><?= $nom ?></td>
                                    <td><?= $email ?></td>
                                    <td><?= $numero ?></td>
                                    <td><?= $region ?></td>
                                    <td><?= $description ?></td>
                                    <td>
                                        <?php
                                            if (!empty($media)) {
                                                $mediaFiles = json_decode($media, true);
                                                if (json_last_error() === JSON_ERROR_NONE && is_array($mediaFiles)) {
                                                    // Format JSON (multiple fichiers)
                                                    foreach ($mediaFiles as $mediaFile) {
                                                        $mediaUrl = '../../public/uploads/' . htmlspecialchars($mediaFile);
                                                        $ext = strtolower(pathinfo($mediaFile, PATHINFO_EXTENSION));
                                                        if (in_array($ext, ['mp4','mov','webm'])) {
                                                            echo '<video src="'.htmlspecialchars($mediaUrl).'" controls style="width:52px;height:52px;object-fit:cover;border-radius:4px;"></video>';
                                                        } else {
                                                            echo '<img src="'.htmlspecialchars($mediaUrl).'" alt="Media" style="width:52px;height:52px;object-fit:cover;border-radius:4px;">';
                                                        }
                                                    }
                                                } else {
                                                    // Ancien format: chaîne simple (un seul fichier)
                                                    $mediaUrl = '../../public/uploads/' . htmlspecialchars($media);
                                                    $ext = strtolower(pathinfo($media, PATHINFO_EXTENSION));
                                                    if (in_array($ext, ['mp4','mov','webm'])) {
                                                        echo '<video src="'.htmlspecialchars($mediaUrl).'" controls style="width:52px;height:52px;object-fit:cover;border-radius:4px;"></video>';
                                                    } else {
                                                        echo '<img src="'.htmlspecialchars($mediaUrl).'" alt="Media" style="width:52px;height:52px;object-fit:cover;border-radius:4px;">';
                                                    }
                                                }
                                            } else {
                                                echo '—';
                                            }
                                        ?>
                                    </td>
                                    <td><?= $dateCreation ?></td>
                                    <td>
                                        <span class="badge bg-danger">
                                            <i class="ri-heart-fill me-1"></i><?= $likesCount ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">
                                            <i class="ri-chat-3-fill me-1"></i><?= $commentsCount ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <a href="post_likes.php?id_post=<?= urlencode($idPost) ?>&ajax=1" class="btn btn-sm btn-outline-info me-1" onclick="if(typeof loadContent==='function'){ loadContent('post_likes.php?id_post=<?= urlencode($idPost) ?>&ajax=1'); return false; }" title="Voir les likes">
                                            <i class="ri-heart-fill"></i>
                                        </a>
                                        <a href="post_comments.php?id_post=<?= urlencode($idPost) ?>&ajax=1" class="btn btn-sm btn-outline-primary me-1" onclick="if(typeof loadContent==='function'){ loadContent('post_comments.php?id_post=<?= urlencode($idPost) ?>&ajax=1'); return false; }" title="Voir les commentaires">
                                            <i class="ri-chat-3-fill"></i>
                                        </a>
                                        <a href="addpostupdate.php?id_post=<?= urlencode($idPost) ?>&ajax=1" class="btn btn-sm btn-outline-success me-1" onclick="if(typeof loadContent==='function'){ loadContent('addpostupdate.php?id_post=<?= urlencode($idPost) ?>&ajax=1'); return false; }" title="Résoudre">
                                            <i class="ri-checkbox-circle-line"></i>
                                        </a>
                                        <a href="updatepost.php?id=<?= urlencode($idPost) ?>&ajax=1" class="btn btn-sm btn-outline-primary me-1" onclick="if(typeof loadContent==='function'){ loadContent('updatepost.php?id=<?= urlencode($idPost) ?>&ajax=1'); return false; }" title="Éditer">
                                            <i class="ri-edit-line"></i>
                                        </a>
                                        <a href="deletepost.php?id=<?= urlencode($idPost) ?>&ajax=1" class="btn btn-sm btn-outline-danger" onclick="if(typeof loadContent==='function'){ loadContent('deletepost.php?id=<?= urlencode($idPost) ?>&ajax=1'); return false; }" title="Supprimer">
                                            <i class="ri-delete-bin-line"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="12" class="text-center text-muted py-4">
                                        Aucun post pour le moment.
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
let addPostFormVisible = false;
function toggleAddPostForm() {
	const container = document.getElementById('addPostFormContainer');
	if (!container) {
		console.error('Container addPostFormContainer not found');
		return;
	}
	
	if (!addPostFormVisible) {
		// Afficher le conteneur
		container.style.display = 'block';
		container.innerHTML = '<p style="padding: 1rem; text-align: center;">Chargement du formulaire...</p>';
		
		// Charger le formulaire via AJAX
		fetch('addpost.php?ajax=1', {
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
			addPostFormVisible = true;
		})
		.catch(error => {
			console.error('Erreur lors du chargement du formulaire:', error);
			container.innerHTML = '<p style="padding: 1rem; color: red;">Erreur lors du chargement du formulaire. <a href="addpost.php">Cliquez ici pour ouvrir le formulaire</a></p>';
		});
	} else {
		container.style.display = 'none';
		container.innerHTML = '';
		addPostFormVisible = false;
	}
}
</script>
<?php
if ($showFullPage) {
    $content = ob_get_clean();
    renderBackofficeLayout('Liste des Posts', $content, ['assets/css/showofferback.css']);
} else {
    // Si AJAX, inclure le CSS inline
    echo '<link rel="stylesheet" href="assets/css/showofferback.css">';
}
?>
