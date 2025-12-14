<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once 'layout.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['id_user'])) {
    header('Location: login.php?error=' . urlencode('Veuillez vous connecter pour accéder aux réclamations.'));
    exit;
}

$success = $_GET['success'] ?? '';
$error = $_GET['error'] ?? '';

ob_start();
?>
<div class="row justify-content-center py-4">
    <div class="col-12 col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">
                    <i class="ri-mail-line me-2"></i>Réclamation
                </h4>
            </div>
            <div class="card-body">
                <?php if ($success): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="ri-checkbox-circle-line me-2"></i>
                        <?= htmlspecialchars($success) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="ri-error-warning-line me-2"></i>
                        <?= htmlspecialchars($error) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <p class="text-muted mb-4">
                    Utilisez ce formulaire pour soumettre une réclamation ou signaler un problème. 
                    Nous examinerons votre demande dans les plus brefs délais.
                </p>

                <form method="POST" action="../../Controller/reclamationcontroller.php" class="reclamation-form" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="add">
                    
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="ri-file-text-line me-2"></i>Sujet <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="sujet" class="form-control form-control-lg" 
                               placeholder="Résumé de votre réclamation" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            <i class="ri-file-edit-line me-2"></i>Description <span class="text-danger">*</span>
                        </label>
                        <textarea name="description" class="form-control" rows="6" 
                                  placeholder="Décrivez votre réclamation en détail..." required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            <i class="ri-attachment-line me-2"></i>Pièce jointe (optionnel)
                        </label>
                        <input type="file" name="media" class="form-control" accept="image/*,video/*,application/pdf">
                        <small class="text-muted">Formats acceptés: images, vidéos, PDF</small>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="ri-send-plane-line me-2"></i>Envoyer la réclamation
                        </button>
                        <a href="index.php" class="btn btn-outline-secondary">
                            <i class="ri-arrow-left-line me-2"></i>Retour à l'accueil
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
renderLayout('Réclamation', $content);
?>

