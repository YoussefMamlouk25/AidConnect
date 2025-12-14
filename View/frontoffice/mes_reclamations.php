<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['id_user'])) {
    header('Location: login.php?error=' . urlencode('Veuillez vous connecter pour accéder à vos réclamations.'));
    exit;
}

require_once __DIR__ . '/../../Controller/reclamationcontroller.php';
require_once __DIR__ . '/../../Model/Reclamation.php';

$controller = new ReclamationController();
$reclamations = $controller->getReclamationsByUser($_SESSION['id_user']);

require_once 'layout.php';

ob_start();
?>
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="ri-mail-line me-2"></i>Mes Réclamations
                    </h4>
                </div>
                <div class="card-body">
                    <?php if (empty($reclamations)): ?>
                        <div class="alert alert-info text-center">
                            <i class="ri-inbox-line me-2"></i>
                            Vous n'avez pas encore de réclamations. 
                            <a href="reclamation.php" class="alert-link">Créer une réclamation</a>
                        </div>
                    <?php else: ?>
                        <div class="list-group">
                            <?php foreach ($reclamations as $reclamation): ?>
                                <div class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h5 class="mb-2">
                                                <i class="ri-file-text-line me-2 text-primary"></i>
                                                <?= htmlspecialchars($reclamation['titre']) ?>
                                            </h5>
                                            <p class="mb-2 text-muted">
                                                <?= htmlspecialchars($reclamation['description']) ?>
                                            </p>
                                            <?php if (!empty($reclamation['media'])): ?>
                                                <div class="mb-2">
                                                    <i class="ri-attachment-line me-2"></i>
                                                    <a href="../../public/uploads/<?= htmlspecialchars($reclamation['media']) ?>" 
                                                       target="_blank" class="text-primary">
                                                        Voir la pièce jointe
                                                    </a>
                                                </div>
                                            <?php endif; ?>
                                            <small class="text-muted">
                                                <i class="ri-time-line me-1"></i>
                                                <?= date('d/m/Y H:i', strtotime($reclamation['date'])) ?>
                                            </small>
                                        </div>
                                        <span class="badge bg-primary">#<?= $reclamation['id_reclamation'] ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="text-center">
                <a href="reclamation.php" class="btn btn-primary">
                    <i class="ri-add-circle-line me-2"></i>Nouvelle réclamation
                </a>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
renderLayout('Mes Réclamations', $content);
?>

