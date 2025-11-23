<?php
session_start();
require_once '../../Model/user.php';
require_once 'template.php';

$model = new UserModel();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name           = trim($_POST['name'] ?? '');
    $email          = trim($_POST['email'] ?? '');
    $password       = $_POST['password'] ?? '';
    $confirm        = $_POST['confirm'] ?? '';
    $cin            = trim($_POST['cin'] ?? '');
    $date_naissance = $_POST['date_naissance'] ?? '';
    $tel            = trim($_POST['tel'] ?? '');
    $region         = trim($_POST['region'] ?? '');

    if ($name === '' || $email === '' || $password === '' || $confirm === '') {
        $error = "Veuillez remplir les champs obligatoires.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Email invalide.";
    } elseif ($password !== $confirm) {
        $error = "Les mots de passe ne correspondent pas.";
    } else {
        $result = $model->signup($name, $email, $password, $cin, $date_naissance, $tel, $region);

        if ($result['success']) {
            $success = "Compte créé avec succès. Vous pouvez maintenant vous connecter.";
        } else {
            $error = $result['error'] ?? "Erreur lors de la création du compte.";
        }
    }
}

ob_start();
?>

<h2 class="mb-3">Inscription</h2>

<?php if ($error): ?>
    <div style="color:red;"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<?php if ($success): ?>
    <div style="color:green;"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<form method="post">
    <label>Nom complet *</label><br>
    <input type="text" name="name" required><br><br>

    <label>Email *</label><br>
    <input type="email" name="email" required><br><br>

    <label>Mot de passe *</label><br>
    <input type="password" name="password" required><br><br>

    <label>Confirmer le mot de passe *</label><br>
    <input type="password" name="confirm" required><br><br>

    <label>CIN</label><br>
    <input type="text" name="cin"><br><br>

    <label>Date de naissance</label><br>
    <input type="date" name="date_naissance"><br><br>

    <label>Téléphone</label><br>
    <input type="text" name="tel"><br><br>

    <label>Région</label><br>
    <input type="text" name="region"><br><br>

    <button type="submit">Créer le compte</button>
</form>

<p class="mt-3">
    Déjà un compte ? <a href="login.php">Se connecter</a>
</p>

<?php
$content = ob_get_clean();
renderTemplate('Inscription', $content);
