<?php
//ff
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
require_once '../../Model/user.php';
require_once 'layout.php';

if (!isset($_SESSION['id_user'])) {
    header('Location: login.php');
    exit;
}

$model = new UserModel();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm'] ?? '';

    if ($password === '' || $confirm === '') {
        $error = "Veuillez remplir les deux champs.";
    } elseif ($password !== $confirm) {
        $error = "Les mots de passe ne correspondent pas.";
    } elseif (strlen($password) < 6) {
        $error = "Le mot de passe doit contenir au moins 6 caractères.";
    } else {
        $ok = $model->changePassword($_SESSION['id_user'], $password);
        if ($ok) {
            $success = "Mot de passe mis à jour avec succès.";
        } else {
            $error = "Erreur lors de la mise à jour du mot de passe.";
        }
    }
}

ob_start();
?>
<div class="row justify-content-center">
    <div class="col-lg-10 col-xl-8">
        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <i class="ri-error-warning-line me-2"></i><?= htmlspecialchars($error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <i class="ri-checkbox-circle-line me-2"></i><?= htmlspecialchars($success) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Change Password Card -->
        <div class="card iq-card shadow-sm border-0">
            <div class="card-header bg-white border-0 pb-3">
                <div class="d-flex align-items-center justify-content-between">
                    <h5 class="mb-0 fw-bold">
                        <i class="ri-lock-password-line me-2 text-primary"></i>Changer le mot de passe
                    </h5>
                    <a href="profile.php" class="btn btn-sm btn-outline-secondary">
                        <i class="ri-arrow-left-line me-1"></i>Retour
                    </a>
                </div>
            </div>
            <div class="card-body">
                <form method="post" id="changePasswordForm">
                    <div class="row g-4">
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label fw-semibold">
                                    <i class="ri-lock-line me-1 text-primary"></i>Nouveau mot de passe <span class="text-danger">*</span>
                                </label>
                                <input type="password" 
                                       name="password" 
                                       class="form-control form-control-lg" 
                                       required
                                       minlength="6"
                                       placeholder="Entrez votre nouveau mot de passe"
                                       autocomplete="new-password">
                                <small class="text-muted">Le mot de passe doit contenir au moins 6 caractères.</small>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label fw-semibold">
                                    <i class="ri-lock-2-line me-1 text-primary"></i>Confirmer le mot de passe <span class="text-danger">*</span>
                                </label>
                                <input type="password" 
                                       name="confirm" 
                                       class="form-control form-control-lg" 
                                       required
                                       minlength="6"
                                       placeholder="Confirmez votre nouveau mot de passe"
                                       autocomplete="new-password">
                                <small class="text-muted">Répétez le mot de passe pour confirmation.</small>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 justify-content-end mt-4 pt-4 border-top">
                        <a href="profile.php" class="btn btn-outline-secondary btn-lg">
                            <i class="ri-close-line me-1"></i>Annuler
                        </a>
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="ri-save-line me-1"></i>Mettre à jour
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
    const password = document.querySelector('input[name="password"]').value;
    const confirm = document.querySelector('input[name="confirm"]').value;
    
    if (password !== confirm) {
        e.preventDefault();
        alert('Les mots de passe ne correspondent pas.');
        return false;
    }
    
    if (password.length < 6) {
        e.preventDefault();
        alert('Le mot de passe doit contenir au moins 6 caractères.');
        return false;
    }
});
</script>
<?php
$content = ob_get_clean();
renderLayout('Changer le mot de passe', $content, ['assets/css/profile.css'], ['assets/js/profile-fix.js']);
