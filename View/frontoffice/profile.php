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
    // user supprimé ?
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit;
}

ob_start();
?>

<h2 class="mb-3">Mon Profil</h2>

<div class="card p-3 mb-3">
    <p><strong>Nom :</strong> <?= htmlspecialchars($user['name']); ?></p>
    <p><strong>Email :</strong> <?= htmlspecialchars($user['email']); ?></p>
    <p><strong>Téléphone :</strong> <?= htmlspecialchars($user['tel'] ?? ''); ?></p>
    <p><strong>Région :</strong> <?= htmlspecialchars($user['region'] ?? ''); ?></p>
    <p><strong>Date de naissance :</strong> <?= htmlspecialchars($user['date_naissance'] ?? ''); ?></p>
    <p><strong>Rôle :</strong> <?= htmlspecialchars($user['role'] ?? 'volunteer'); ?></p>
    <p><strong>Compte créé le :</strong> <?= htmlspecialchars($user['date_creation'] ?? ''); ?></p>
</div>

<a href="profile_edit.php" class="btn btn-sm btn-primary">Modifier le profil</a>
<a href="change_password.php" class="btn btn-sm btn-outline-primary">Changer le mot de passe</a>
<a href="logout.php" class="btn btn-sm btn-danger">Se déconnecter</a>

<?php
$content = ob_get_clean();
renderTemplate('Mon Profil', $content);
