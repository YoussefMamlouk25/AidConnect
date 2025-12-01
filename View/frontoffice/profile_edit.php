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

<<<<<<< HEAD
$error = $_GET['error'] ?? '';
$storedProfile = trim($user['profile_pic'] ?? '');
$profilePic = 'assets/images/default_profile.png';

if ($storedProfile !== '') {
    $normalized = ltrim($storedProfile, '/');
    $relativePath = '../../' . $normalized;
    $absolutePath = realpath(__DIR__ . '/../../' . $normalized);
    if ($absolutePath !== false && file_exists($absolutePath)) {
        $profilePic = $relativePath;
=======
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name   = trim($_POST['name'] ?? '');
    $email  = trim($_POST['email'] ?? '');
    $tel    = trim($_POST['tel'] ?? '');
    $region = trim($_POST['region'] ?? '');

    if ($name === '' || $email === '') {
        $error = "Nom et email sont obligatoires.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Email invalide.";
    } else {
        $ok = $model->updateProfile($_SESSION['id_user'], $name, $email, $tel, $region);
        if ($ok) {
            $_SESSION['name'] = $name;
            $success = "Profil mis à jour.";
            $user = $model->getUserById($_SESSION['id_user']);
        } else {
            $error = "Erreur lors de la mise à jour.";
        }
>>>>>>> f7906b3475c8aebfef6357d1f7f030c593b879fb
    }
}

ob_start();
?>
<<<<<<< HEAD
<div class="row justify-content-center">
    <div class="col-lg-8">
        <?php if ($error): ?>
            <div class="alert alert-danger mb-3"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h3 class="mb-3">Modifier mon profil</h3>

                <div class="text-center mb-4">
                    <img src="<?= htmlspecialchars($profilePic) ?>" alt="Photo de profil" class="rounded-circle border" style="width:160px;height:160px;object-fit:cover;">
                </div>

                <form method="post" action="../../Controller/profile_update_controller.php" enctype="multipart/form-data" class="row g-3">
                    <input type="hidden" name="current_pic" value="<?= htmlspecialchars($user['profile_pic'] ?? '') ?>">

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
                        <label class="form-label">CIN</label>
                        <input type="text" name="cin" class="form-control" value="<?= htmlspecialchars($user['cin'] ?? '') ?>" placeholder="8 chiffres">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Région</label>
                        <input type="text" name="region" class="form-control" value="<?= htmlspecialchars($user['region'] ?? '') ?>">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Date de naissance</label>
                        <input type="date" name="date_naissance" class="form-control" value="<?= htmlspecialchars($user['date_naissance'] ?? '') ?>">
                    </div>

                    <div class="col-12">
                        <label class="form-label">Photo de profil</label>
                        <input type="file" name="profile_pic" class="form-control" accept="image/*">
                    </div>

                    <div class="col-12 d-flex justify-content-end gap-2 mt-3">
                        <a href="profile.php" class="btn btn-outline-secondary">Annuler</a>
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
$styles = ['https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css'];
renderTemplate('Modifier le profil', $content, $styles);
=======

<h2 class="mb-3">Modifier mon profil</h2>

<?php if ($error): ?>
    <div style="color:red;"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<?php if ($success): ?>
    <div style="color:green;"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<form method="post">
    <label>Nom</label><br>
    <input type="text" name="name" value="<?= htmlspecialchars($user['name']); ?>" required><br><br>

    <label>Email</label><br>
    <input type="email" name="email" value="<?= htmlspecialchars($user['email']); ?>" required><br><br>

    <label>Téléphone</label><br>
    <input type="text" name="tel" value="<?= htmlspecialchars($user['tel'] ?? ''); ?>"><br><br>

    <label>Région</label><br>
    <input type="text" name="region" value="<?= htmlspecialchars($user['region'] ?? ''); ?>"><br><br>

    <button type="submit">Sauvegarder</button>
</form>

<p class="mt-3">
    <a href="profile.php">Retour au profil</a>
</p>

<?php
$content = ob_get_clean();
renderTemplate('Modifier le profil', $content);
>>>>>>> f7906b3475c8aebfef6357d1f7f030c593b879fb
