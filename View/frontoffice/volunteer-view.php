<?php
session_start();
require_once __DIR__ . '/../../Controller/offercontroller.php';
require_once __DIR__ . '/../../Controller/updateoffercontroller.php';
require_once __DIR__ . '/../../Controller/offerlikecontroller.php';
require_once __DIR__ . '/layout.php';

$offerC = new OfferController();
$updateOfferC = new UpdateOfferController();
$likeController = new OfferLikeController();
// Créer la table si elle n'existe pas
$likeController->createTableIfNotExists();

// Get filter parameters for offers only
$query = isset($_GET['q']) ? trim($_GET['q']) : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : '';
$status = isset($_GET['status']) ? trim($_GET['status']) : '';
$sort = isset($_GET['sort']) ? trim($_GET['sort']) : 'date_desc';

// Advanced filter parameters
$nom = isset($_GET['nom']) ? trim($_GET['nom']) : '';
$email = isset($_GET['email']) ? trim($_GET['email']) : '';
$num = isset($_GET['num']) ? trim($_GET['num']) : '';
$has_media = isset($_GET['has_media']) ? trim($_GET['has_media']) : '';
$min_updates = isset($_GET['min_updates']) ? trim($_GET['min_updates']) : '';
$max_updates = isset($_GET['max_updates']) ? trim($_GET['max_updates']) : '';

// Check if a search was actually performed (has GET parameters)
$hasSearchParams = isset($_GET['q']) || isset($_GET['category']) || isset($_GET['status']) || 
                   isset($_GET['nom']) || isset($_GET['email']) || isset($_GET['num']) || 
                   isset($_GET['has_media']) || isset($_GET['min_updates']) || isset($_GET['max_updates']) ||
                   (isset($_GET['sort']) && $_GET['sort'] != 'date_desc');

// Build filters array
$filters = [
    'category' => $category,
    'status' => $status,
    'sort' => $sort,
    'nom' => $nom,
    'email' => $email,
    'num' => $num,
    'has_media' => $has_media,
    'min_updates' => $min_updates,
    'max_updates' => $max_updates
];

// Remove empty filters
$filters = array_filter($filters, function($value) {
    return $value !== '';
});

// Get offers with filters
if (!empty($query) || !empty($filters)) {
    $offers = $offerC->searchOffers($query, $filters);
} else {
    $offers = $offerC->getAllOffers();
}

$current_user_id = isset($_SESSION['id_user']) ? (int)$_SESSION['id_user'] : 0;
$isAdmin = (($_SESSION['role'] ?? '') === 'admin');

foreach ($offers as &$offer) {
    $offer['updates_count'] = count($updateOfferC->getUpdateOffersByOfferId($offer['id_offer']));
    $offer['is_creator'] = ($current_user_id > 0 && $offer['id_user'] == $current_user_id);
    $offer['can_manage'] = $offer['is_creator'] || $isAdmin;
}
unset($offer);

$deleted = isset($_GET['deleted']) && $_GET['deleted'] == '1';
$flashError = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_error']);
$error = $_GET['error'] ?? null;

// Start output buffering to capture content
ob_start();
?>

<div class="row justify-content-center">
  <div class="col-lg-6 col-xl-5">
      <div class="text-center mb-4">
        <div class="d-flex gap-2 justify-content-center align-items-center flex-wrap">
          <a href="volunteer-form.php" class="btn-add-offer">
            <i class="ri-add-circle-line me-2"></i> Ajouter une Offre
          </a>
          <button type="button" class="btn btn-primary search-icon-btn" id="searchIconBtn" onclick="toggleSearchCard()">
            <i class="ri-search-line me-2"></i>Rechercher des Offres
          </button>
        </div>
      </div>
      
      <h5 class="mb-3 text-center">Volunteer Posts</h5>
      
      <!-- Search Card for Offers -->
      <div class="search-card mb-4" id="searchCard" style="display: none;">
        <div class="search-card-content">
          <form action="volunteer-view.php" method="GET" class="search-form">
            <div class="search-header mb-3">
              <h5 class="mb-0"><i class="ri-search-line me-2"></i>Rechercher des Offres</h5>
              <button type="button" class="btn-close-search" onclick="toggleSearchCard()">
                <i class="ri-close-line"></i>
              </button>
            </div>
            
            <!-- Search Input -->
            <div class="search-input-wrapper mb-3">
              <input type="text" 
                     name="q" 
                     id="headerSearchInput" 
                     class="form-control search-input" 
                     placeholder="Rechercher par description..." 
                     value="<?= htmlspecialchars($query) ?>">
              <i class="ri-search-line search-input-icon"></i>
            </div>
            
            <!-- Basic Filters Section -->
            <div class="filters-section">
              <!-- Donation Type Filter -->
              <div class="mb-3">
                <label class="form-label fw-bold"><i class="ri-price-tag-3-line me-1"></i>Type de don</label>
                <select name="category" class="form-select category-select">
                  <option value="">Tous les types</option>
                  <option value="money" <?= $category == 'money' ? 'selected' : '' ?>>Argent</option>
                  <option value="food" <?= $category == 'food' ? 'selected' : '' ?>>Nourriture</option>
                  <option value="clothing" <?= $category == 'clothing' ? 'selected' : '' ?>>Vêtements</option>
                  <option value="medicines" <?= $category == 'medicines' ? 'selected' : '' ?>>Médicaments</option>
                  <option value="equipment" <?= $category == 'equipment' ? 'selected' : '' ?>>Matériel</option>
                  <option value="services" <?= $category == 'services' ? 'selected' : '' ?>>Services</option>
                </select>
              </div>
              
              <!-- Status Filter -->
              <div class="mb-3">
                <label class="form-label fw-bold"><i class="ri-checkbox-circle-line me-1"></i>Statut</label>
                <select name="status" class="form-select category-select">
                  <option value="">Tous les statuts</option>
                  <option value="active" <?= $status == 'active' ? 'selected' : '' ?>>Actives (sans réponses)</option>
                  <option value="resolved" <?= $status == 'resolved' ? 'selected' : '' ?>>Avec réponses</option>
                </select>
              </div>
              
              <!-- Sorting -->
              <div class="mb-3">
                <label class="form-label fw-bold"><i class="ri-sort-asc me-1"></i>Trier par</label>
                <select name="sort" class="form-select category-select">
                  <option value="date_desc" <?= $sort == 'date_desc' ? 'selected' : '' ?>>Date (Plus récent)</option>
                  <option value="date_asc" <?= $sort == 'date_asc' ? 'selected' : '' ?>>Date (Plus ancien)</option>
                  <option value="name_asc" <?= $sort == 'name_asc' ? 'selected' : '' ?>>Nom (A-Z)</option>
                  <option value="name_desc" <?= $sort == 'name_desc' ? 'selected' : '' ?>>Nom (Z-A)</option>
                  <option value="popularity" <?= $sort == 'popularity' ? 'selected' : '' ?>>Popularité</option>
                  <option value="updates_desc" <?= $sort == 'updates_desc' ? 'selected' : '' ?>>Plus de réponses</option>
                  <option value="updates_asc" <?= $sort == 'updates_asc' ? 'selected' : '' ?>>Moins de réponses</option>
                </select>
              </div>
            </div>
            
            <!-- Advanced Filters Section -->
            <div class="advanced-filters-section">
              <button type="button" class="btn btn-link advanced-toggle-btn" onclick="toggleAdvancedFilters()">
                <i class="ri-settings-3-line me-2"></i>
                <span id="advancedToggleText">Afficher les filtres avancés</span>
                <i class="ri-arrow-down-s-line ms-2" id="advancedToggleIcon"></i>
              </button>
              
              <div class="advanced-filters-content" id="advancedFiltersContent" style="display: none;">
                <div class="advanced-filters-inner">
                  <!-- Filter by Name -->
                  <div class="mb-3">
                    <label class="form-label fw-bold"><i class="ri-user-line me-1"></i>Nom du volontaire</label>
                    <input type="text" 
                           name="nom" 
                           class="form-control search-input" 
                           placeholder="Rechercher par nom..." 
                           value="<?= htmlspecialchars($nom) ?>">
                  </div>
                  
                  <!-- Filter by Email -->
                  <div class="mb-3">
                    <label class="form-label fw-bold"><i class="ri-mail-line me-1"></i>Email</label>
                    <input type="text" 
                           name="email" 
                           class="form-control search-input" 
                           placeholder="Rechercher par email..." 
                           value="<?= htmlspecialchars($email) ?>">
                  </div>
                  
                  <!-- Filter by Phone -->
                  <div class="mb-3">
                    <label class="form-label fw-bold"><i class="ri-phone-line me-1"></i>Téléphone</label>
                    <input type="text" 
                           name="num" 
                           class="form-control search-input" 
                           placeholder="Rechercher par téléphone..." 
                           value="<?= htmlspecialchars($num) ?>">
                  </div>
                  
                  <!-- Filter by Media -->
                  <div class="mb-3">
                    <label class="form-label fw-bold"><i class="ri-image-line me-1"></i>Contient des médias</label>
                    <select name="has_media" class="form-select category-select">
                      <option value="">Tous</option>
                      <option value="yes" <?= $has_media == 'yes' ? 'selected' : '' ?>>Avec médias</option>
                      <option value="no" <?= $has_media == 'no' ? 'selected' : '' ?>>Sans médias</option>
                    </select>
                  </div>
                  
                  <!-- Filter by Updates Count -->
                  <div class="row mb-3">
                    <div class="col-6">
                      <label class="form-label fw-bold"><i class="ri-message-3-line me-1"></i>Min. réponses</label>
                      <input type="number" 
                             name="min_updates" 
                             class="form-control search-input" 
                             placeholder="0" 
                             min="0"
                             value="<?= htmlspecialchars($min_updates) ?>">
                    </div>
                    <div class="col-6">
                      <label class="form-label fw-bold"><i class="ri-message-3-line me-1"></i>Max. réponses</label>
                      <input type="number" 
                             name="max_updates" 
                             class="form-control search-input" 
                             placeholder="∞" 
                             min="0"
                             value="<?= htmlspecialchars($max_updates) ?>">
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="d-flex gap-2">
              <button type="submit" class="btn btn-primary flex-grow-1 search-submit-btn">
                <i class="ri-search-line me-2"></i>Rechercher
              </button>
              <a href="volunteer-view.php" class="btn btn-outline-secondary">
                <i class="ri-refresh-line"></i>
              </a>
            </div>
          </form>
        </div>
      </div>
      
      <!-- Results Count -->
      <?php if ($hasSearchParams): ?>
        <div class="alert alert-info mb-3">
          <i class="ri-information-line me-2"></i>
          <strong><?= count($offers) ?></strong> offre(s) trouvée(s)
          <?php if (!empty($query)): ?>
            pour "<strong><?= htmlspecialchars($query) ?></strong>"
          <?php endif; ?>
        </div>
      <?php endif; ?>
      <?php if ($deleted): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          Volunteer deleted successfully!
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php endif; ?>
      <?php if (!empty($flashError)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <?= htmlspecialchars($flashError) ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php endif; ?>
      <?php if (!empty($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <?= OfferController::escape($error) ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php endif; ?>
      <?php if (empty($offers)): ?>
        <div class="card">
          <div class="card-body text-center py-5">
            <i class="ri-inbox-line" style="font-size: 4rem; color: #ccc;"></i>
            <h5 class="mt-3">
              <?php if ($hasSearchParams): ?>
                Aucune offre trouvée
              <?php else: ?>
                Aucune offre pour le moment
              <?php endif; ?>
            </h5>
            <p class="text-muted">
              <?php if ($hasSearchParams): ?>
                Essayez de modifier vos critères de recherche.
              <?php else: ?>
                Soyez le premier à créer une offre !
              <?php endif; ?>
            </p>
            <?php if (!$hasSearchParams): ?>
              <a href="volunteer-form.php" class="btn btn-primary">
                <i class="ri-add-circle-line me-2"></i> Créer la première offre
              </a>
            <?php else: ?>
              <a href="volunteer-view.php" class="btn btn-outline-primary">
                <i class="ri-refresh-line me-2"></i> Voir toutes les offres
              </a>
            <?php endif; ?>
          </div>
        </div>
      <?php else: ?>
            <?php foreach ($offers as $offer): 
              $nom = OfferController::escape($offer['nom']);
              $email = OfferController::escape($offer['email']);
              $typeDon = OfferController::escape($offer['type_don']);
              $typeDonLabel = $offerC->getTypeDonLabel($typeDon);
              $num = OfferController::escape($offer['num']);
              $description = OfferController::escape($offer['description']);
              $initials = strtoupper(substr($nom, 0, 1));
              
              // Parse media files
              $mediaFiles = [];
              if (!empty($offer['media'])) {
                $decoded = json_decode($offer['media'], true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                  $mediaFiles = array_values(array_filter($decoded));
                } else {
                  $mediaFiles[] = $offer['media'];
                }
              }
              $mediaCount = count($mediaFiles);
              
              // Récupérer les informations de l'utilisateur connecté
              $current_user_id = isset($_SESSION['id_user']) ? (int)$_SESSION['id_user'] : 0;
              $idOffer = (int)$offer['id_offer'];
              
              // Vérifier si l'utilisateur a liké cette offre
              $isLiked = false;
              if ($current_user_id > 0) {
                  $isLiked = $likeController->isLiked($idOffer, $current_user_id);
              }
              $likesCount = isset($offer['likes_count']) ? (int)$offer['likes_count'] : 0;
            ?>
              <div class="post-card mb-3">
                <!-- Post Header -->
                <div class="post-header">
                  <div class="avatar" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);"><?= $initials ?></div>
                  <div class="post-info">
                    <div class="post-author">
                      <?= $nom ?>
                      <?php if ($offer['is_creator']): ?>
                        <i class="ri-verified-badge-fill text-primary ms-1" title="Post Creator"></i>
                      <?php endif; ?>
                    </div>
                    <div class="post-meta">
                      <span class="badge bg-primary">
                        <i class="ri-gift-line me-1"></i><?= $typeDonLabel ?>
                      </span>
                      <span class="badge bg-success ms-2">
                        <i class="ri-user-heart-line me-1"></i>Volunteer
                      </span>
                    </div>
                  </div>
                </div>

                <!-- Post Content -->
                <div class="post-content">
                  <p class="post-description"><?= nl2br($description) ?></p>
                  
                  <!-- Media Gallery -->
                  <?php if ($mediaCount > 0): ?>
                    <div class="post-media-container mb-3">
                      <div class="fb-media-grid count-<?= $mediaCount >= 4 ? 4 : $mediaCount ?>">
                        <?php foreach ($mediaFiles as $index => $mediaFile): ?>
                          <?php if ($index >= 4) break; ?>
                          <?php 
                            $mediaUrl = '../../public/uploads/' . htmlspecialchars($mediaFile);
                            $ext = strtolower(pathinfo($mediaFile, PATHINFO_EXTENSION));
                            $isVideo = in_array($ext, ['mp4','mov','webm']);
                          ?>
                          <div class="grid-item">
                            <?php if ($isVideo): ?>
                              <video src="<?= $mediaUrl ?>" controls class="img-fluid"></video>
                            <?php else: ?>
                              <img src="<?= $mediaUrl ?>" alt="Offer media" class="img-fluid">
                            <?php endif; ?>
                          </div>
                        <?php endforeach; ?>
                      </div>
                    </div>
                  <?php endif; ?>

                  <!-- Contact Info -->
                  <div class="post-contact">
                    <div class="contact-item">
                      <i class="ri-mail-line me-2 text-primary"></i>
                      <strong>Email:</strong> 
                      <a href="mailto:<?= $email ?>"><?= $email ?></a>
                    </div>
                    <div class="contact-item">
                      <i class="ri-phone-line me-2 text-success"></i>
                      <strong>Téléphone:</strong> 
                      <a href="tel:<?= $num ?>"><?= $num ?></a>
                    </div>
                    <div class="mt-2">
                      <button class="btn btn-sm btn-outline-primary" onclick="copyToClipboard('<?= $email ?>')" title="Copy Email">
                        <i class="ri-file-copy-line me-1"></i>Copy Email
                      </button>
                      <button class="btn btn-sm btn-outline-success" onclick="copyToClipboard('<?= $num ?>')" title="Copy Phone">
                        <i class="ri-file-copy-line me-1"></i>Copy Phone
                      </button>
                    </div>
                  </div>
                </div>

                <!-- Engagement Bar -->
                <div class="post-engagement">
                  <div class="engagement-stats">
                    <?php if ($current_user_id > 0): ?>
                    <button type="button" class="btn-link like-offer-btn <?php echo $isLiked ? 'liked' : ''; ?>" 
                            data-offer-id="<?= $idOffer ?>" 
                            title="J'aime cette offre"
                            style="color: <?php echo $isLiked ? '#e74c3c' : '#6c757d'; ?>; cursor: pointer; position: relative; z-index: 10; border: none; background: transparent;">
                      <i class="ri-heart-<?php echo $isLiked ? 'fill' : 'line'; ?> me-1"></i>J'aime
                      <span class="like-count ms-1" data-offer-id="<?= $idOffer ?>">
                        <?php echo $likesCount; ?>
                      </span>
                    </button>
                    <?php else: ?>
                    <span class="btn-link" 
                          title="Connectez-vous pour aimer cette offre"
                          style="color: #6c757d; cursor: not-allowed;">
                      <i class="ri-heart-line me-1"></i>J'aime
                      <span class="like-count ms-1">
                        <?php echo $likesCount; ?>
                      </span>
                    </span>
                    <?php endif; ?>
                    <button class="btn-link" onclick="sharePost(<?= (int)$offer['id_offer'] ?>)" title="Share this post">
                      <i class="ri-share-line me-1"></i>Share
                    </button>
                  </div>
                  <div class="engagement-counts">
                    <?php if ($offer['updates_count'] > 0): ?>
                      <span class="badge bg-info">
                        <i class="ri-message-3-line me-1"></i><?= $offer['updates_count'] ?> Response<?= $offer['updates_count'] > 1 ? 's' : '' ?>
                      </span>
                    <?php endif; ?>
                  </div>
                </div>

                <!-- Post Actions -->
                <div class="post-actions">
                  <?php if ($offer['is_creator']): ?>
                    <a href="update-offer-form.php?id_offer=<?= (int)$offer['id_offer'] ?>" class="btn btn-sm btn-outline-primary">
                      <i class="ri-edit-line me-1"></i> Update
                    </a>
                  <?php else: ?>
                    <a href="update-offer-form.php?id_offer=<?= (int)$offer['id_offer'] ?>" class="btn btn-sm btn-primary">
                      <i class="ri-reply-line me-1"></i> Reply
                    </a>
                  <?php endif; ?>
                  <a href="update-offer-view.php?id_offer=<?= (int)$offer['id_offer'] ?>" class="btn btn-sm btn-outline-info">
                    <i class="ri-file-list-3-line me-1"></i> View Replies
                    <?php if ($offer['updates_count'] > 0): ?>
                      <span class="badge bg-primary ms-1"><?= $offer['updates_count'] ?></span>
                    <?php endif; ?>
                  </a>
                  <?php if ($offer['can_manage']): ?>
                    <a href="volunteer-edit.php?id=<?= (int)$offer['id_offer'] ?>" class="btn btn-sm btn-outline-warning">
                      <i class="ri-pencil-line me-1"></i> Edit
                    </a>
                    <a href="volunteer-delete.php?id=<?= (int)$offer['id_offer'] ?>" class="btn btn-sm btn-outline-danger">
                      <i class="ri-delete-bin-line me-1"></i> Delete
                    </a>
                  <?php endif; ?>
                  <button class="btn btn-sm btn-outline-secondary" onclick="window.print()" title="Print this post">
                    <i class="ri-printer-line me-1"></i> Print
                  </button>
                </div>
              </div>
            <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</div>

<script>
function toggleSearchCard() {
    const searchCard = document.getElementById('searchCard');
    if (!searchCard) return; // Exit if search card doesn't exist on this page
    if (searchCard.style.display === 'none' || searchCard.style.display === '') {
        searchCard.style.display = 'block';
        setTimeout(() => {
            searchCard.classList.add('active');
            const searchInput = document.getElementById('headerSearchInput');
            if (searchInput) {
                searchInput.focus();
            }
        }, 10);
    } else {
        searchCard.classList.remove('active');
        setTimeout(() => {
            searchCard.style.display = 'none';
        }, 300);
    }
}

// Close search card when clicking outside
document.addEventListener('click', function(event) {
    const searchCard = document.getElementById('searchCard');
    const searchIconBtn = document.getElementById('searchIconBtn');
    
    if (searchCard && searchCard.style.display !== 'none') {
        if (!searchCard.contains(event.target) && !searchIconBtn.contains(event.target)) {
            toggleSearchCard();
        }
    }
});

// Close search card with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const searchCard = document.getElementById('searchCard');
        if (searchCard && searchCard.style.display !== 'none') {
            toggleSearchCard();
        }
    }
});

// Toggle advanced filters
function toggleAdvancedFilters() {
    const content = document.getElementById('advancedFiltersContent');
    const toggleText = document.getElementById('advancedToggleText');
    const toggleIcon = document.getElementById('advancedToggleIcon');
    
    if (content.style.display === 'none') {
        content.style.display = 'block';
        toggleText.textContent = 'Masquer les filtres avancés';
        toggleIcon.classList.remove('ri-arrow-down-s-line');
        toggleIcon.classList.add('ri-arrow-up-s-line');
    } else {
        content.style.display = 'none';
        toggleText.textContent = 'Afficher les filtres avancés';
        toggleIcon.classList.remove('ri-arrow-up-s-line');
        toggleIcon.classList.add('ri-arrow-down-s-line');
    }
}
</script>

<?php
$content = ob_get_clean();

// Define additional styles and scripts
$additional_styles = ['assets/css/template.css', 'assets/css/showpost.css', 'assets/css/volunteer-view.css'];
$additional_scripts = ['assets/js/volunteer-view.js', 'assets/js/form.js', 'assets/js/javascript.js'];

// Render the layout
renderLayout('Volunteers', $content, $additional_styles, $additional_scripts);
?>