<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../../config.php';
require_once '../../Model/post.php';
require_once '../../Model/offer.php';
require_once '../../Controller/postcontroller.php';
require_once '../../Controller/offercontroller.php';
require_once 'layout.php';

$postController = new PostController();
$offerController = new OfferController();

// Get search parameters
$query = isset($_GET['q']) ? trim($_GET['q']) : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : '';
$region = isset($_GET['region']) ? trim($_GET['region']) : '';
$date_from = isset($_GET['date_from']) ? trim($_GET['date_from']) : '';
$date_to = isset($_GET['date_to']) ? trim($_GET['date_to']) : '';
$status = isset($_GET['status']) ? trim($_GET['status']) : '';
$sort = isset($_GET['sort']) ? trim($_GET['sort']) : 'date_desc';
$content_type = isset($_GET['content_type']) ? trim($_GET['content_type']) : 'all';

// Build filters array
$filters = [
    'region' => $region,
    'category' => $category,
    'date_from' => $date_from,
    'date_to' => $date_to,
    'status' => $status,
    'sort' => $sort
];

// Remove empty filters
$filters = array_filter($filters, function($value) {
    return $value !== '';
});

// Perform search
$posts = [];
$offers = [];

// Check if any search criteria is provided
$hasSearchCriteria = !empty($query) || !empty($filters);

if ($hasSearchCriteria) {
    // Search based on content type
    if ($content_type == 'posts' || $content_type == 'all') {
        $posts = $postController->searchPosts($query, $filters);
    }
    if ($content_type == 'offers' || $content_type == 'all') {
        $offers = $offerController->searchOffers($query, $filters);
    }
} else {
    // If no search criteria, show all based on content type
    if ($content_type == 'posts' || $content_type == 'all') {
        $allPosts = $postController->getAllPosts();
        if ($allPosts && is_object($allPosts) && method_exists($allPosts, 'fetchAll')) {
            $posts = $allPosts->fetchAll(PDO::FETCH_ASSOC);
        }
    }
    if ($content_type == 'offers' || $content_type == 'all') {
        $offers = $offerController->getAllOffers();
    }
}

// Helper functions
function getInitials($name) {
    $words = explode(' ', trim($name));
    $initials = '';
    foreach ($words as $word) {
        if (!empty($word)) {
            $initials .= strtoupper(substr($word, 0, 1));
        }
    }
    return substr($initials, 0, 2);
}

function formatDate($date) {
    if (empty($date)) return '';
    $dateObj = new DateTime($date);
    $now = new DateTime();
    $diff = $now->diff($dateObj);
    
    if ($diff->days == 0) {
        return "Aujourd'hui";
    } elseif ($diff->days == 1) {
        return "Hier";
    } elseif ($diff->days < 7) {
        return "Il y a " . $diff->days . " jours";
    } else {
        return $dateObj->format('d/m/Y');
    }
}

ob_start();
?>
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="mb-4">
            <h2 class="mb-3">
                <i class="ri-search-line me-2"></i>
                Résultats de recherche
                <?php if (!empty($query)): ?>
                    <small class="text-muted">pour "<?php echo htmlspecialchars($query); ?>"</small>
                <?php endif; ?>
            </h2>
            
            <!-- Active Filters Display -->
            <?php if ($hasSearchCriteria): ?>
                <div class="mb-3">
                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        <span class="text-muted">Filtres actifs:</span>
                        <?php if (!empty($region)): ?>
                            <span class="badge bg-primary">
                                <i class="ri-map-pin-line me-1"></i>Région: <?php echo htmlspecialchars($region); ?>
                            </span>
                        <?php endif; ?>
                        <?php if (!empty($category)): ?>
                            <span class="badge bg-primary">
                                <i class="ri-price-tag-3-line me-1"></i><?php echo htmlspecialchars($offerController->getTypeDonLabel($category)); ?>
                            </span>
                        <?php endif; ?>
                        <?php if (!empty($status)): ?>
                            <span class="badge bg-primary">
                                <i class="ri-checkbox-circle-line me-1"></i>Statut: <?php echo $status == 'active' ? 'Actif' : 'Résolu'; ?>
                            </span>
                        <?php endif; ?>
                        <?php if (!empty($date_from) || !empty($date_to)): ?>
                            <span class="badge bg-primary">
                                <i class="ri-calendar-line me-1"></i>
                                <?php 
                                if (!empty($date_from) && !empty($date_to)) {
                                    echo htmlspecialchars($date_from) . ' - ' . htmlspecialchars($date_to);
                                } elseif (!empty($date_from)) {
                                    echo 'À partir de ' . htmlspecialchars($date_from);
                                } else {
                                    echo 'Jusqu\'à ' . htmlspecialchars($date_to);
                                }
                                ?>
                            </span>
                        <?php endif; ?>
                        <a href="search.php" class="btn btn-sm btn-outline-secondary">
                            <i class="ri-close-line me-1"></i>Effacer les filtres
                        </a>
                    </div>
                </div>
            <?php endif; ?>
            
            <p class="text-muted">
                <?php 
                $totalResults = count($posts) + count($offers);
                echo $totalResults . " résultat(s) trouvé(s)";
                ?>
            </p>
        </div>

        <!-- Posts Results -->
        <?php if (count($posts) > 0): ?>
            <div class="mb-5">
                <h4 class="mb-3"><i class="ri-file-list-3-line me-2"></i>Posts (<?php echo count($posts); ?>)</h4>
                <?php foreach ($posts as $row): 
                    $authorName = htmlspecialchars($row['nom'] ?? 'Anonyme');
                    $initials = getInitials($authorName);
                    $titre = htmlspecialchars($row['titre'] ?? '');
                    $description = htmlspecialchars($row['description'] ?? '');
                    $region = htmlspecialchars($row['region'] ?? '');
                    $dateFormatted = formatDate($row['date_creation'] ?? '');
                    $idPost = $row['id_post'];
                    ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex align-items-start mb-3">
                                <div class="avatar-circle me-3">
                                    <span><?php echo $initials; ?></span>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1"><?php echo $authorName; ?></h6>
                                    <small class="text-muted"><?php echo $dateFormatted; ?></small>
                                </div>
                            </div>
                            <h5 class="mb-2"><?php echo $titre; ?></h5>
                            <p class="post-content"><?php echo nl2br($description); ?></p>
                            <?php if (!empty($region)): ?>
                                <p class="mb-2"><i class="ri-map-pin-line me-1"></i><?php echo $region; ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Offers Results -->
        <?php if (count($offers) > 0): ?>
            <div class="mb-5">
                <h4 class="mb-3"><i class="ri-price-tag-3-line me-2"></i>Offres (<?php echo count($offers); ?>)</h4>
                <?php foreach ($offers as $offer): 
                    $typeDon = $offerController->getTypeDonLabel($offer['type_don'] ?? '');
                    $nom = htmlspecialchars($offer['nom'] ?? '');
                    $description = htmlspecialchars($offer['description'] ?? '');
                    $email = htmlspecialchars($offer['email'] ?? '');
                    $num = htmlspecialchars($offer['num'] ?? '');
                    $idOffer = $offer['id_offer'];
                    ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex align-items-start mb-3">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1"><?php echo $nom; ?></h6>
                                    <span class="badge bg-primary"><?php echo $typeDon; ?></span>
                                </div>
                            </div>
                            <p class="post-content"><?php echo nl2br($description); ?></p>
                            <?php if (!empty($email)): ?>
                                <p class="mb-1"><i class="ri-mail-line me-1"></i><?php echo $email; ?></p>
                            <?php endif; ?>
                            <?php if (!empty($num)): ?>
                                <p class="mb-2"><i class="ri-phone-line me-1"></i><?php echo $num; ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- No Results -->
        <?php if (empty($query) && empty($category)): ?>
            <div class="text-center py-5">
                <i class="ri-search-line" style="font-size: 4rem; color: #dadde1;"></i>
                <h4 class="mt-3">Commencez votre recherche</h4>
                <p class="text-muted">Utilisez la barre de recherche en haut pour trouver des posts ou des offres</p>
            </div>
        <?php elseif (count($posts) == 0 && count($offers) == 0): ?>
            <div class="text-center py-5">
                <i class="ri-search-line" style="font-size: 4rem; color: #dadde1;"></i>
                <h4 class="mt-3">Aucun résultat trouvé</h4>
                <p class="text-muted">Essayez avec d'autres mots-clés ou catégories</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
renderLayout('Recherche', $content);
?>

