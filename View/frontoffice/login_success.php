<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!isset($_SESSION['id_user'])) {
    header('Location: login.php');
    exit;
}

require_once 'layout.php';

$userName = $_SESSION['name'] ?? 'Utilisateur';
$userRole = $_SESSION['role'] ?? 'volunteer';

ob_start();
?>
<div class="row justify-content-center py-5">
    <div class="col-12 col-md-6 col-lg-5 col-xl-4">
        <div class="auth-card shadow-lg text-center">
            <div class="auth-card-header mb-4">
                <div class="success-icon mb-3">
                    <i class="ri-checkbox-circle-fill"></i>
                </div>
                <div class="mb-3">
                    <span class="badge bg-success px-3 py-2">
                        <i class="ri-check-line me-1"></i>Connexion réussie
                    </span>
                </div>
                <h2 class="mb-2">Bienvenue, <?= htmlspecialchars($userName) ?> 👋</h2>
                <p class="text-muted mb-0">
                    Vous êtes maintenant connecté à votre espace AidConnect.
                </p>
            </div>

            <div class="d-grid gap-3 mb-4">
                <a href="profile.php" class="btn btn-primary btn-lg">
                    <i class="ri-user-line me-2"></i>Voir mon profil
                </a>

                <?php if ($userRole === 'admin'): ?>
                    <a href="../backoffice/dashboard.php" class="btn btn-outline-primary btn-lg">
                        <i class="ri-dashboard-line me-2"></i>Accéder au backoffice
                    </a>
                <?php endif; ?>

                <a href="index.php" class="btn btn-outline-secondary btn-lg">
                    <i class="ri-home-line me-2"></i>Retour à l'accueil
                </a>
            </div>

            <hr class="my-4">

            <div class="auth-card-footer">
                <a href="../../Controller/logout.php" class="text-danger text-decoration-none">
                    <i class="ri-logout-box-line me-1"></i>Se déconnecter
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.success-icon {
    width: 100px;
    height: 100px;
    margin: 0 auto;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    border-radius: 50%;
    font-size: 60px;
    color: white;
    box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
    animation: scaleIn 0.5s ease;
}

@keyframes scaleIn {
    from {
        transform: scale(0);
        opacity: 0;
    }
    to {
        transform: scale(1);
        opacity: 1;
    }
}

body.dark-mode .success-icon {
    box-shadow: 0 4px 12px rgba(40, 167, 69, 0.5);
}
</style>

<?php
$content = ob_get_clean();

$additional_styles = ['assets/css/auth.css'];
renderLayout('Connexion réussie', $content, $additional_styles);
?>

