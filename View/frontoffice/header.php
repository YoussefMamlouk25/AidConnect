<?php
// Déterminer la page actuelle pour mettre en évidence le lien actif
$current_page = basename($_SERVER['PHP_SELF']);
?>
<header class="header bg-white shadow-sm p-3 mb-4">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-md-3">
                <a href="showPosts.php" class="text-decoration-none">
                    <h4 class="text-primary mb-0">AidConnect</h4>
                </a>
            </div>
            <div class="col-md-6">
                <nav class="d-flex justify-content-center align-items-center flex-wrap gap-2">
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
                <a href="#" class="btn btn-sm btn-outline-primary">
                    <i class="ri-user-line me-1"></i> Profile
                </a>
            </div>
        </div>
    </div>
</header>
