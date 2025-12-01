<?php
session_start();
require_once 'template.php';

if (!isset($_SESSION['id_user'])) {
    header('Location: login.php');
    exit;
}

$userName = $_SESSION['name'] ?? 'Utilisateur';
$userRole = $_SESSION['role'] ?? 'volunteer';

ob_start();
?>
<div class="auth-wrapper">
    <div class="auth-card shadow-sm text-center">
        <div class="mb-3">
            <span class="badge bg-success-subtle border border-success text-success px-3 py-2">
                Connexion réussie
            </span>
        </div>

        <h2 class="mb-1">Bienvenue, <?= htmlspecialchars($userName) ?> 👋</h2>
        <p class="text-muted mb-4">
            Vous êtes maintenant connecté à votre espace AidConnect.
        </p>

        <div class="d-grid gap-2">
            <a href="profile.php" class="btn btn-primary">
                Voir mon profil
            </a>

            <?php if ($userRole === 'admin'): ?>
                <a href="../backoffice/dashboard.php" class="btn btn-outline-primary">
                    Accéder au backoffice
                </a>
            <?php endif; ?>

            <a href="index.php" class="btn btn-outline-secondary">
                Retour à l'accueil
            </a>
        </div>

        <hr class="my-4">

        <a href="../../Controller/logout.php" class="text-danger">
            Se déconnecter
        </a>
    </div>
</div>
<?php
$content = ob_get_clean();

$styles = [
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css',
    'assets/css/auth.css'
];

renderTemplate('Connexion réussie', $content, $styles);

