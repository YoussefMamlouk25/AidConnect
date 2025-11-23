<?php
session_start();
require_once '../../Model/user.php';
require_once 'template.php';

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
    } else {
        $ok = $model->changePassword($_SESSION['id_user'], $password);
        if ($ok) {
            $success = "Mot de passe mis à jour.";
        } else {
            $error = "Erreur lors de la mise à jour du mot de passe.";
        }
    }
}

ob_start();
?>

<h2 class="mb-3">Changer le mot de passe</h2>

<?php if ($error): ?>
    <div style="color:red;"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<?php if ($success): ?>
    <div style="color:green;"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<form method="post">
    <label>Nouveau mot de passe</label><br>
    <input type="password" name="password" required><br><br>

    <label>Confirmer le mot de passe</label><br>
    <input type="password" name="confirm" required><br><br>

    <button type="submit">Mettre à jour</button>
</form>

<p class="mt-3">
    <a href="profile.php">Retour au profil</a>
</p>

<?php
$content = ob_get_clean();
renderTemplate('Changer le mot de passe', $content);
