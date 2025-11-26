<?php
require_once 'layout.php';

$content = '
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <div class="header-title">
                    <h4 class="card-title">Bienvenue dans le Dashboard AidConnect 👋</h4>
                </div>
            </div>
            <div class="card-body">
                <p>Sélectionnez un module dans le menu à gauche.</p>
                
                <div class="d-flex gap-2 flex-wrap mt-3">
                    <a href="addpost.php" class="btn btn-primary">
                        <i class="ri-add-line me-1"></i> Ajouter un Post
                    </a>
                    <a href="addoffer.php" class="btn btn-primary">
                        <i class="ri-add-line me-1"></i> Ajouter une Offre
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
';

renderBackofficeLayout('Dashboard', $content, [], ['offer.js', 'updateoffer.js']);
?>

