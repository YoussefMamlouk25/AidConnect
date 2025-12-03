<?php
session_start();
<<<<<<< HEAD
=======
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
>>>>>>> b131f5e063ee0fa222fd863afa1a13331c16d1c1
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
<<<<<<< HEAD

$styles = [
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css',
    'assets/css/auth.css'
];

renderTemplate('Connexion', $content, $styles);
=======
renderTemplate('Connexion', $content);
>>>>>>> f7906b3475c8aebfef6357d1f7f030c593b879fb
>>>>>>> b131f5e063ee0fa222fd863afa1a13331c16d1c1
