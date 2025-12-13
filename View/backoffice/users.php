<?php
session_start();

require_once '../../Model/user.php';
require_once 'layout.php';

if (!isset($_SESSION['id_user']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: ../frontoffice/login.php?error=' . urlencode('Accès refusé.'));
    exit;
}

$userModel = new UserModel();
$users = $userModel->getAllUsers();

// Get password column name for checking if password is set
$passwordColumnName = $userModel->getPasswordColumn();

$successUpdated = isset($_GET['updated']);
$successDeleted = isset($_GET['deleted']);
$errorMessage = $_GET['error'] ?? '';

ob_start();
?>
<div class="row">
    <div class="col-12">
        <?php if ($successUpdated): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="ri-check-line me-1"></i>Utilisateur mis à jour avec succès.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if ($successDeleted): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="ri-check-line me-1"></i>Utilisateur supprimé avec succès.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if ($errorMessage): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="ri-error-warning-line me-1"></i><?= htmlspecialchars($errorMessage) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="header-title">
                    <h4 class="card-title mb-0">Gestion des utilisateurs</h4>
                    <p class="text-muted mb-0">Liste complète des comptes AidConnect.</p>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">Photo</th>
                                <th scope="col">Nom</th>
                                <th scope="col">Email</th>
                                <th scope="col">Téléphone</th>
                                <th scope="col">Région</th>
                                <th scope="col">Rôle</th>
                                <th scope="col">Mot de passe</th>
                                <th scope="col" class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($users)): ?>
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        Aucun utilisateur pour le moment.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($users as $user): ?>
                                    <?php
                                        $profilePic = '../frontoffice/assets/images/default_profile.png';
                                        $storedProfile = trim($user['profile_pic'] ?? '');
                                        if ($storedProfile !== '') {
                                            $normalized = ltrim($storedProfile, '/');
                                            $relativePath = '../../' . $normalized;
                                            $absolutePath = realpath(__DIR__ . '/../../' . $normalized);
                                            if ($absolutePath !== false && file_exists($absolutePath)) {
                                                $profilePic = $relativePath;
                                            }
                                        }
                                        
                                        // Get password value (hash)
                                        $passwordValue = $user[$passwordColumnName] ?? '';
                                        $hasPassword = !empty($passwordValue);
                                        if ($hasPassword) {
                                            // Show the full password hash
                                            $passwordDisplay = htmlspecialchars($passwordValue);
                                        } else {
                                            $passwordDisplay = '<span class="text-muted">Non défini</span>';
                                        }
                                    ?>
                                    <tr>
                                        <td>
                                            <img
                                                src="<?= htmlspecialchars($profilePic) ?>" 
                                                alt="Profil de <?= htmlspecialchars($user['name']) ?>"
                                                class="rounded-circle border"
                                                style="width:52px;height:52px;object-fit:cover;">
                                        </td>
                                        <td><?= htmlspecialchars($user['name']) ?></td>
                                        <td><?= htmlspecialchars($user['email']) ?></td>
                                        <td><?= htmlspecialchars($user['tel'] ?? '—') ?></td>
                                        <td><?= htmlspecialchars($user['region'] ?? '—') ?></td>
                                        <td>
                                            <span class="badge <?= ($user['role'] ?? '') === 'admin' ? 'bg-primary' : 'bg-secondary'; ?>">
                                                <?= htmlspecialchars($user['role'] ?? 'volunteer') ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="password-display" style="font-family: monospace; font-size: 11px; word-break: break-all; max-width: 250px; display: inline-block;">
                                                    <?= $passwordDisplay ?>
                                                </span>
                                                <?php if ($hasPassword): ?>
                                                    <span class="badge bg-success" title="Mot de passe défini">
                                                        <i class="ri-check-line"></i>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning" title="Mot de passe non défini">
                                                        <i class="ri-alert-line"></i>
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            <a href="edit_user.php?id=<?= urlencode($user['id_user']) ?>"
                                               class="btn btn-sm btn-outline-primary me-1"
                                               title="Modifier">
                                                <i class="ri-edit-line"></i>
                                            </a>
                                            <a href="../../Controller/delete_user.php?id=<?= urlencode($user['id_user']) ?>"
                                               class="btn btn-sm btn-outline-danger"
                                               onclick="return confirm('Supprimer cet utilisateur ?');"
                                               title="Supprimer">
                                                <i class="ri-delete-bin-line"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();

renderBackofficeLayout('Utilisateurs', $content);

