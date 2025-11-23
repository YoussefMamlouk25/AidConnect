<?php
session_start();
require_once '../../Model/user.php';
require_once 'template.php';

$model = new UserModel();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = "Veuillez remplir tous les champs.";
    } else {
        $result = $model->login($email, $password);

        if ($result['success']) {
            $u = $result['user'];

            $_SESSION['id_user'] = $u['id_user'];
            $_SESSION['name']    = $u['name'];
            $_SESSION['role']    = $u['role'] ?? 'volunteer';

            // Redirection de base → frontoffice posts
            header('Location: showPosts.php');
            exit;
        } else {
            $error = $result['error'] ?? "Erreur de connexion.";
        }
    }
}

ob_start();
?>

<h2 class="mb-3">Connexion</h2>

<?php if ($error): ?>
    <div style="color:red;"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form method="post">
    <label>Email</label><br>
    <input type="email" name="email" required><br><br>

    <label>Mot de passe</label><br>
    <input type="password" name="password" required><br><br>

    <button type="submit">Se connecter</button>
</form>

<p class="mt-3">
    Pas de compte ? <a href="signup.php">Créer un compte</a>
</p>

<?php
$content = ob_get_clean();
renderTemplate('Connexion', $content);
