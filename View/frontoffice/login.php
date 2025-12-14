<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
session_start();
}
require_once 'layout.php';

$error   = $_GET['error'] ?? '';
$success = $_GET['success'] ?? '';

ob_start();
?>
<div class="row justify-content-center py-5">
    <div class="col-12 col-md-6 col-lg-5 col-xl-4">
        <div class="auth-card shadow-lg">
            <div class="auth-card-header text-center mb-4">
                <div class="auth-logo mb-3">
                    <i class="ri-heart-pulse-fill"></i>
                </div>
                <h2 class="mb-1">Connexion</h2>
                <p class="text-muted mb-0">Connectez-vous à votre compte AidConnect</p>
            </div>

        <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="ri-error-warning-line me-2"></i>
                    <?= htmlspecialchars($error) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
        <?php endif; ?>

        <?php if ($success): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="ri-checkbox-circle-line me-2"></i>
                    <?= htmlspecialchars($success) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
        <?php endif; ?>

        <form method="post" action="../../Controller/logincontroller.php" class="auth-form">
            <div class="mb-3">
                    <label class="form-label">
                        <i class="ri-mail-line me-2"></i>Email
                    </label>
                    <input type="email" name="email" class="form-control form-control-lg" placeholder="votre@email.com" required autofocus>
            </div>

                <div class="mb-4">
                    <label class="form-label">
                        <i class="ri-lock-line me-2"></i>Mot de passe
                    </label>
                    <input type="password" name="password" class="form-control form-control-lg" placeholder="••••••••" required>
            </div>

                <button type="submit" class="btn btn-primary btn-lg w-100 mb-3">
                    <i class="ri-login-box-line me-2"></i>Se connecter
                </button>
        </form>

            <div class="auth-card-footer text-center mt-4">
                <p class="mb-0">
                    Pas de compte ? <a href="signup.php" class="fw-bold">Créer un compte</a>
        </p>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();

$additional_styles = ['assets/css/auth.css'];
renderLayout('Connexion', $content, $additional_styles);
