<?php
session_start();
<<<<<<< HEAD
require_once 'template.php';

$error   = $_GET['error'] ?? '';
$success = $_GET['success'] ?? '';

ob_start();
?>
<div class="auth-wrapper">
    <div class="auth-card shadow-sm">
        <h2 class="text-center mb-3">Connexion</h2>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="post" action="../../Controller/logincontroller.php" class="auth-form">
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Mot de passe</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">Se connecter</button>
        </form>

        <p class="text-center mt-3">
            Pas de compte ? <a href="signup.php">Créer un compte</a>
        </p>
    </div>
</div>
<?php
$content = ob_get_clean();

$styles = [
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css',
    'assets/css/auth.css'
];

renderTemplate('Connexion', $content, $styles);
=======
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
>>>>>>> f7906b3475c8aebfef6357d1f7f030c593b879fb
