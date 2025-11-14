<?php
session_start();
require_once '../../Model/user.php';
require_once 'template.php';

if (!isset($_SESSION['id_user'])) {
    header('Location: login.php');
    exit;
}

$model = new UserModel();
$user  = $model->getUserById($_SESSION['id_user']);

if (!$user) {
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit;
}

$success = $_GET['success'] ?? '';
$storedProfile = trim($user['profile_pic'] ?? '');
$profilePic = 'assets/images/default_profile.png';

if ($storedProfile !== '') {
    $normalized = ltrim($storedProfile, '/');
    $relativePath = '../../' . $normalized;
    $absolutePath = realpath(__DIR__ . '/../../' . $normalized);
    if ($absolutePath !== false && file_exists($absolutePath)) {
        $profilePic = $relativePath;
    }
}

ob_start();
?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <?php if ($success): ?>
            <div class="alert alert-success mb-3"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="text-center mb-4">
                    <img src="<?= htmlspecialchars($profilePic) ?>" alt="Photo de profil" class="rounded-circle border" style="width:140px;height:140px;object-fit:cover;">
                    <h3 class="mt-3 mb-0"><?= htmlspecialchars($user['name']) ?></h3>
                    <p class="text-muted">Membre depuis : <?= htmlspecialchars($user['date_creation'] ?? '-') ?></p>
                </div>

                <div class="table-responsive">
                    <table class="table table-borderless align-middle">
                        <tbody>
                            <tr>
                                <th scope="row" class="text-muted" style="width:35%;">Email</th>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-muted">Téléphone</th>
                                <td><?= htmlspecialchars($user['tel'] ?? '-') ?></td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-muted">CIN</th>
                                <td><?= htmlspecialchars($user['cin'] ?? '-') ?></td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-muted">Région</th>
                                <td><?= htmlspecialchars($user['region'] ?? '-') ?></td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-muted">Date de naissance</th>
                                <td><?= htmlspecialchars($user['date_naissance'] ?? '-') ?></td>
                            </tr>
                            <tr>
                                <th scope="row" class="text-muted">Rôle</th>
                                <td><?= htmlspecialchars($user['role'] ?? 'volunteer') ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex gap-2 flex-wrap justify-content-end">
                    <a href="profile_edit.php" class="btn btn-primary">Modifier le profil</a>
                    <a href="change_password.php" class="btn btn-outline-primary">Changer le mot de passe</a>
                    <a href="../../Controller/logout.php" class="btn btn-outline-danger">Se déconnecter</a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
$styles = ['https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css'];
renderTemplate('Mon Profil', $content, $styles);
