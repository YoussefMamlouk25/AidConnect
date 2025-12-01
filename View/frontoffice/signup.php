<?php
session_start();
<<<<<<< HEAD
require_once 'template.php';

$error = $_GET['error'] ?? '';

ob_start();
?>
<div class="auth-wrapper">
    <div class="auth-card shadow-sm">
        <h2 class="text-center mb-3">Créer un compte</h2>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form id="signupForm" method="post" action="../../Controller/signupcontroller.php" class="auth-form">
            <div class="mb-3">
                <label class="form-label">Nom complet</label>
                <input type="text" name="name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">CIN</label>
                <input type="text" name="cin" class="form-control" placeholder="8 chiffres">
            </div>

            <div class="mb-3">
                <label class="form-label">Date de naissance</label>
                <input type="date" name="date_naissance" class="form-control">
            </div>

            <div class="mb-3">
                <label class="form-label">Téléphone</label>
                <input type="text" name="tel" class="form-control" placeholder="8 chiffres">
            </div>

            <div class="mb-3">
                <label class="form-label">Région</label>
                <select name="region" class="form-select">
                    <option value="">-- Sélectionnez une région --</option>
                    <option value="Tunis">Tunis</option>
                    <option value="Ariana">Ariana</option>
                    <option value="Ben Arous">Ben Arous</option>
                    <option value="Manouba">Manouba</option>
                    <option value="Nabeul">Nabeul</option>
                    <option value="Zaghouan">Zaghouan</option>
                    <option value="Bizerte">Bizerte</option>
                    <option value="Béja">Béja</option>
                    <option value="Jendouba">Jendouba</option>
                    <option value="Le Kef">Le Kef</option>
                    <option value="Siliana">Siliana</option>
                    <option value="Sousse">Sousse</option>
                    <option value="Monastir">Monastir</option>
                    <option value="Mahdia">Mahdia</option>
                    <option value="Sfax">Sfax</option>
                    <option value="Kairouan">Kairouan</option>
                    <option value="Kasserine">Kasserine</option>
                    <option value="Sidi Bouzid">Sidi Bouzid</option>
                    <option value="Gabès">Gabès</option>
                    <option value="Medenine">Medenine</option>
                    <option value="Tataouine">Tataouine</option>
                    <option value="Gafsa">Gafsa</option>
                    <option value="Tozeur">Tozeur</option>
                    <option value="Kebili">Kebili</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Mot de passe</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Confirmer le mot de passe</label>
                <input type="password" name="confirm" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">Créer un compte</button>
        </form>

        <p class="text-center mt-3">
            Déjà un compte ? <a href="login.php">Se connecter</a>
        </p>
    </div>
</div>
<?php
$content = ob_get_clean();

$styles = [
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css',
    'assets/css/auth.css'
];
$scripts = ['assets/js/signup-validation.js'];

renderTemplate('Inscription', $content, $styles, $scripts);
=======
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
>>>>>>> f7906b3475c8aebfef6357d1f7f030c593b879fb
