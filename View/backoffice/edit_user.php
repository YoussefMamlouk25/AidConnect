<?php
session_start();

require_once '../../Model/user.php';
require_once 'layout.php';

if (!isset($_SESSION['id_user']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: ../frontoffice/login.php?error=' . urlencode('Accès refusé.'));
    exit;
}

$userId = (int) ($_GET['id'] ?? 0);

if ($userId <= 0) {
    header('Location: users.php?error=' . urlencode('Utilisateur introuvable.'));
    exit;
}

$userModel = new UserModel();
$user = $userModel->getUserById($userId);

if (!$user) {
    header('Location: users.php?error=' . urlencode('Utilisateur introuvable.'));
    exit;
}

$errorMessage = $_GET['error'] ?? '';

ob_start();
?>
<div class="row justify-content-center">
    <div class="col-xl-8">
        <?php if ($errorMessage): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="ri-error-warning-line me-1"></i><?= htmlspecialchars($errorMessage) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Modifier l'utilisateur</h4>
            </div>
            <div class="card-body">
                <form method="post" action="../../Controller/update_user.php" class="row g-3">
                    <input type="hidden" name="id_user" value="<?= htmlspecialchars($user['id_user']) ?>">

                    <div class="col-md-6">
                        <label class="form-label">Nom complet</label>
                        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['name']) ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Téléphone</label>
                        <input type="text" name="tel" class="form-control" value="<?= htmlspecialchars($user['tel'] ?? '') ?>" placeholder="8 chiffres">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Région</label>
                        <input type="text" name="region" class="form-control" value="<?= htmlspecialchars($user['region'] ?? '') ?>">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Rôle</label>
                        <select name="role" class="form-select">
                            <option value="volunteer" <?= (($user['role'] ?? '') === 'volunteer') ? 'selected' : '' ?>>Volunteer</option>
                            <option value="admin" <?= (($user['role'] ?? '') === 'admin') ? 'selected' : '' ?>>Admin</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <hr class="my-3">
                        <h5 class="mb-3"><i class="ri-lock-password-line me-2"></i>Changer le mot de passe</h5>
                        <div class="alert alert-info">
                            <i class="ri-information-line me-2"></i>
                            <small>Laissez vide pour ne pas modifier le mot de passe.</small>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Nouveau mot de passe</label>
                        <input type="password" name="password" class="form-control" placeholder="Laisser vide pour ne pas changer" autocomplete="new-password">
                        <small class="text-muted">Minimum 6 caractères</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Confirmer le mot de passe</label>
                        <input type="password" name="password_confirm" class="form-control" placeholder="Confirmer le nouveau mot de passe" autocomplete="new-password">
                    </div>

                    <div class="col-12 d-flex justify-content-end gap-2">
                        <a href="users.php" class="btn btn-outline-secondary">Annuler</a>
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();

renderBackofficeLayout('Modifier un utilisateur', $content);

