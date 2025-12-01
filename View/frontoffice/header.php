<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Déterminer la page actuelle pour mettre en évidence le lien actif
$current_page = basename($_SERVER['PHP_SELF']);
$isAdmin = (($_SESSION['role'] ?? '') === 'admin');
?>
<header class="header bg-white shadow-sm p-3 mb-4">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-md-3">
                <a href="index.php" class="text-decoration-none">
                    <h4 class="text-primary mb-0">AidConnect</h4>
                </a>
            </div>
            <div class="col-md-6">
                <nav class="d-flex justify-content-center align-items-center flex-wrap gap-2">
                    <a href="index.php" class="btn btn-sm <?php echo ($current_page == 'index.php') ? 'btn-primary' : 'btn-outline-primary'; ?>">
                        <i class="ri-home-line me-1"></i> Accueil
                    </a>
                    <a href="showPosts.php" class="btn btn-sm <?php echo ($current_page == 'showPosts.php') ? 'btn-primary' : 'btn-outline-primary'; ?>">
                        <i class="ri-file-list-3-line me-1"></i> Posts
                    </a>
                    <a href="showpostupdate.php" class="btn btn-sm <?php echo ($current_page == 'showpostupdate.php') ? 'btn-primary' : 'btn-outline-primary'; ?>">
                        <i class="ri-checkbox-circle-line me-1"></i> Posts Résolus
                    </a>
                    <a href="volunteer-view.php" class="btn btn-sm <?php echo ($current_page == 'volunteer-view.php') ? 'btn-primary' : 'btn-outline-primary'; ?>">
                        <i class="ri-price-tag-3-line me-1"></i> Offres
                    </a>
                    <a href="update-offer-view.php" class="btn btn-sm <?php echo ($current_page == 'update-offer-view.php') ? 'btn-primary' : 'btn-outline-primary'; ?>">
                        <i class="ri-edit-box-line me-1"></i> Updates Offres
                    </a>
                </nav>
            </div>
            <div class="col-md-3 text-end">
                <div class="d-flex align-items-center gap-2 justify-content-end">
                    <button type="button" class="btn btn-sm btn-outline-primary search-icon-btn" id="searchIconBtn" onclick="toggleSearchCard()">
                        <i class="ri-search-line"></i>
                    </button>
                    <?php if ($isAdmin): ?>
                        <a href="../backoffice/dashboard.php" class="btn btn-sm btn-outline-dark">
                            <i class="ri-dashboard-line me-1"></i> Backoffice
                        </a>
                    <?php endif; ?>
                    <a href="profile.php" class="btn btn-sm btn-outline-primary">
                        <i class="ri-user-line me-1"></i> Profile
                    </a>
                </div>
            </div>
        </div>
        <!-- Search Card -->
        <div class="search-card" id="searchCard" style="display: none;">
            <div class="search-card-content">
                <form action="search.php" method="GET" class="search-form">
                    <div class="search-header mb-3">
                        <h5 class="mb-0"><i class="ri-search-line me-2"></i>Rechercher & Filtrer</h5>
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
                               placeholder="Rechercher par titre, description, région..." 
                               value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>">
                        <i class="ri-search-line search-input-icon"></i>
                    </div>
                    
                    <!-- Content Type Filter -->
                    <div class="mb-3">
                        <label class="form-label fw-bold"><i class="ri-file-list-3-line me-1"></i>Type de contenu</label>
                        <select name="content_type" class="form-select category-select">
                            <option value="all" <?php echo (!isset($_GET['content_type']) || $_GET['content_type'] == 'all') ? 'selected' : ''; ?>>Tous (Posts & Offres)</option>
                            <option value="posts" <?php echo (isset($_GET['content_type']) && $_GET['content_type'] == 'posts') ? 'selected' : ''; ?>>Posts uniquement</option>
                            <option value="offers" <?php echo (isset($_GET['content_type']) && $_GET['content_type'] == 'offers') ? 'selected' : ''; ?>>Offres uniquement</option>
                        </select>
                    </div>
                    
                    <!-- Filters Section -->
                    <div class="filters-section">
                        <!-- Region Filter -->
                        <div class="mb-3">
                            <label class="form-label fw-bold"><i class="ri-map-pin-line me-1"></i>Région</label>
                            <select name="region" class="form-select category-select">
                                <option value="">Toutes les régions</option>
                                <option value="Ariana" <?php echo (isset($_GET['region']) && $_GET['region'] == 'Ariana') ? 'selected' : ''; ?>>Ariana</option>
                                <option value="Béja" <?php echo (isset($_GET['region']) && $_GET['region'] == 'Béja') ? 'selected' : ''; ?>>Béja</option>
                                <option value="Ben Arous" <?php echo (isset($_GET['region']) && $_GET['region'] == 'Ben Arous') ? 'selected' : ''; ?>>Ben Arous</option>
                                <option value="Bizerte" <?php echo (isset($_GET['region']) && $_GET['region'] == 'Bizerte') ? 'selected' : ''; ?>>Bizerte</option>
                                <option value="Gabes" <?php echo (isset($_GET['region']) && $_GET['region'] == 'Gabes') ? 'selected' : ''; ?>>Gabes</option>
                                <option value="Gafsa" <?php echo (isset($_GET['region']) && $_GET['region'] == 'Gafsa') ? 'selected' : ''; ?>>Gafsa</option>
                                <option value="Jendouba" <?php echo (isset($_GET['region']) && $_GET['region'] == 'Jendouba') ? 'selected' : ''; ?>>Jendouba</option>
                                <option value="Kairouan" <?php echo (isset($_GET['region']) && $_GET['region'] == 'Kairouan') ? 'selected' : ''; ?>>Kairouan</option>
                                <option value="Kasserine" <?php echo (isset($_GET['region']) && $_GET['region'] == 'Kasserine') ? 'selected' : ''; ?>>Kasserine</option>
                                <option value="Kébili" <?php echo (isset($_GET['region']) && $_GET['region'] == 'Kébili') ? 'selected' : ''; ?>>Kébili</option>
                                <option value="Le Kef" <?php echo (isset($_GET['region']) && $_GET['region'] == 'Le Kef') ? 'selected' : ''; ?>>Le Kef</option>
                                <option value="Mahdia" <?php echo (isset($_GET['region']) && $_GET['region'] == 'Mahdia') ? 'selected' : ''; ?>>Mahdia</option>
                                <option value="Manouba" <?php echo (isset($_GET['region']) && $_GET['region'] == 'Manouba') ? 'selected' : ''; ?>>Manouba</option>
                                <option value="Médenine" <?php echo (isset($_GET['region']) && $_GET['region'] == 'Médenine') ? 'selected' : ''; ?>>Médenine</option>
                                <option value="Monastir" <?php echo (isset($_GET['region']) && $_GET['region'] == 'Monastir') ? 'selected' : ''; ?>>Monastir</option>
                                <option value="Nabeul" <?php echo (isset($_GET['region']) && $_GET['region'] == 'Nabeul') ? 'selected' : ''; ?>>Nabeul</option>
                                <option value="Sfax" <?php echo (isset($_GET['region']) && $_GET['region'] == 'Sfax') ? 'selected' : ''; ?>>Sfax</option>
                                <option value="Sidi Bouzid" <?php echo (isset($_GET['region']) && $_GET['region'] == 'Sidi Bouzid') ? 'selected' : ''; ?>>Sidi Bouzid</option>
                                <option value="Siliana" <?php echo (isset($_GET['region']) && $_GET['region'] == 'Siliana') ? 'selected' : ''; ?>>Siliana</option>
                                <option value="Sousse" <?php echo (isset($_GET['region']) && $_GET['region'] == 'Sousse') ? 'selected' : ''; ?>>Sousse</option>
                                <option value="Tataouine" <?php echo (isset($_GET['region']) && $_GET['region'] == 'Tataouine') ? 'selected' : ''; ?>>Tataouine</option>
                                <option value="Tozeur" <?php echo (isset($_GET['region']) && $_GET['region'] == 'Tozeur') ? 'selected' : ''; ?>>Tozeur</option>
                                <option value="Tunis" <?php echo (isset($_GET['region']) && $_GET['region'] == 'Tunis') ? 'selected' : ''; ?>>Tunis</option>
                                <option value="Zaghouan" <?php echo (isset($_GET['region']) && $_GET['region'] == 'Zaghouan') ? 'selected' : ''; ?>>Zaghouan</option>
                            </select>
                        </div>
                        
                        <!-- Donation Type Filter -->
                        <div class="mb-3">
                            <label class="form-label fw-bold"><i class="ri-price-tag-3-line me-1"></i>Type de don</label>
                            <select name="category" class="form-select category-select">
                                <option value="">Tous les types</option>
                                <option value="money" <?php echo (isset($_GET['category']) && $_GET['category'] == 'money') ? 'selected' : ''; ?>>Argent</option>
                                <option value="food" <?php echo (isset($_GET['category']) && $_GET['category'] == 'food') ? 'selected' : ''; ?>>Nourriture</option>
                                <option value="clothing" <?php echo (isset($_GET['category']) && $_GET['category'] == 'clothing') ? 'selected' : ''; ?>>Vêtements</option>
                                <option value="medicines" <?php echo (isset($_GET['category']) && $_GET['category'] == 'medicines') ? 'selected' : ''; ?>>Médicaments</option>
                                <option value="equipment" <?php echo (isset($_GET['category']) && $_GET['category'] == 'equipment') ? 'selected' : ''; ?>>Matériel</option>
                                <option value="services" <?php echo (isset($_GET['category']) && $_GET['category'] == 'services') ? 'selected' : ''; ?>>Services</option>
                            </select>
                        </div>
                        
                        <!-- Date Range Filter -->
                        <div class="mb-3">
                            <label class="form-label fw-bold"><i class="ri-calendar-line me-1"></i>Période</label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label small text-muted mb-1">Date début</label>
                                    <input type="date" name="date_from" class="form-control" value="<?php echo isset($_GET['date_from']) ? htmlspecialchars($_GET['date_from']) : ''; ?>" placeholder="Date début">
                                </div>
                                <div class="col-6">
                                    <label class="form-label small text-muted mb-1">Date fin</label>
                                    <input type="date" name="date_to" class="form-control" value="<?php echo isset($_GET['date_to']) ? htmlspecialchars($_GET['date_to']) : ''; ?>" placeholder="Date fin">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Status Filter -->
                        <div class="mb-3">
                            <label class="form-label fw-bold"><i class="ri-checkbox-circle-line me-1"></i>Statut</label>
                            <select name="status" class="form-select category-select">
                                <option value="">Tous les statuts</option>
                                <option value="active" <?php echo (isset($_GET['status']) && $_GET['status'] == 'active') ? 'selected' : ''; ?>>Actif</option>
                                <option value="resolved" <?php echo (isset($_GET['status']) && $_GET['status'] == 'resolved') ? 'selected' : ''; ?>>Résolu</option>
                            </select>
                        </div>
                        
                        <!-- Sorting -->
                        <div class="mb-3">
                            <label class="form-label fw-bold"><i class="ri-sort-asc me-1"></i>Trier par</label>
                            <select name="sort" class="form-select category-select">
                                <option value="date_desc" <?php echo (!isset($_GET['sort']) || $_GET['sort'] == 'date_desc') ? 'selected' : ''; ?>>Date (Plus récent)</option>
                                <option value="date_asc" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'date_asc') ? 'selected' : ''; ?>>Date (Plus ancien)</option>
                                <option value="region" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'region') ? 'selected' : ''; ?>>Région</option>
                                <option value="popularity" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'popularity') ? 'selected' : ''; ?>>Popularité</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1 search-submit-btn">
                            <i class="ri-search-line me-2"></i>Rechercher
                        </button>
                        <a href="search.php" class="btn btn-outline-secondary">
                            <i class="ri-refresh-line"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</header>

<script>
function toggleSearchCard() {
    const searchCard = document.getElementById('searchCard');
    if (searchCard.style.display === 'none' || searchCard.style.display === '') {
        searchCard.style.display = 'block';
        setTimeout(() => {
            searchCard.classList.add('active');
            document.getElementById('headerSearchInput').focus();
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
</script>
