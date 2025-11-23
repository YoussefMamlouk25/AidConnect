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
    }
}

ob_start();
?>

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
