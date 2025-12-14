<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
require_once 'layout.php';

$email = $_GET['email'] ?? $_SESSION['verification_email'] ?? '';
$error = $_GET['error'] ?? '';
$success = $_GET['success'] ?? '';
$message = $_GET['message'] ?? '';

ob_start();
?>
<div class="row justify-content-center py-5">
    <div class="col-12 col-md-6 col-lg-5 col-xl-4">
        <div class="auth-card shadow-lg">
            <div class="auth-card-header text-center mb-4">
                <div class="auth-logo mb-3">
                    <i class="ri-mail-check-line"></i>
                </div>
                <h2 class="mb-1">Vérification de l'email</h2>
                <p class="text-muted mb-0">Entrez le code reçu par email</p>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="ri-information-line me-2"></i>
                    <?= htmlspecialchars($message) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

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

            <?php if (empty($email)): ?>
                <div class="alert alert-warning">
                    <i class="ri-alert-line me-2"></i>
                    <p class="mb-2">Aucun email trouvé. Veuillez vous inscrire d'abord.</p>
                    <a href="signup.php" class="btn btn-primary btn-sm">
                        <i class="ri-user-add-line me-1"></i>S'inscrire
                    </a>
                </div>
            <?php else: ?>
                <div class="mb-4 text-center">
                    <div class="mb-3">
                        <i class="ri-mail-send-line" style="font-size: 3rem; color: var(--primary-color, #0f5fcc);"></i>
                    </div>
                    <p class="text-muted mb-2">
                        Un code de vérification a été envoyé à :
                    </p>
                    <p class="fw-bold text-primary mb-2">
                        <?= htmlspecialchars($email) ?>
                    </p>
                    <p class="text-muted small">
                        <i class="ri-time-line me-1"></i>Le code est valide pendant <strong>15 minutes</strong>
                    </p>
                </div>

                <form method="post" action="../../Controller/verifyemailcontroller.php" class="auth-form" id="verifyForm">
                    <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
                    <input type="hidden" name="action" value="verify">

                    <div class="mb-4">
                        <label class="form-label text-center d-block">
                            <i class="ri-key-line me-2"></i>Code de vérification (6 chiffres)
                        </label>
                        <input 
                            type="text" 
                            name="code" 
                            id="verificationCode"
                            class="form-control form-control-lg text-center verification-code-input" 
                            placeholder="000000"
                            maxlength="6"
                            pattern="[0-9]{6}"
                            required
                            autofocus>
                        <small class="form-text text-muted text-center d-block mt-2">
                            <i class="ri-information-line me-1"></i>Entrez le code reçu par email
                        </small>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100 mb-3">
                        <i class="ri-check-line me-2"></i>Vérifier l'email
                    </button>
                </form>

                <hr class="my-4">

                <form method="post" action="../../Controller/verifyemailcontroller.php" class="auth-form">
                    <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
                    <input type="hidden" name="action" value="resend">

                    <p class="text-center text-muted small mb-2">
                        <i class="ri-question-line me-1"></i>Vous n'avez pas reçu le code ?
                    </p>
                    <button type="submit" class="btn btn-outline-secondary w-100">
                        <i class="ri-refresh-line me-2"></i>Renvoyer le code
                    </button>
                </form>

                <div class="auth-card-footer text-center mt-4">
                    <p class="mb-0">
                        <a href="login.php" class="text-decoration-none">
                            <i class="ri-arrow-left-line me-1"></i>Retour à la connexion
                        </a>
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.verification-code-input {
    font-size: 28px !important;
    letter-spacing: 12px !important;
    font-family: 'Courier New', monospace !important;
    font-weight: 600 !important;
    padding: 16px !important;
    border: 2px solid var(--border-color, #e4e6eb) !important;
}

.verification-code-input:focus {
    border-color: var(--primary-color, #0f5fcc) !important;
    box-shadow: 0 0 0 3px rgba(15, 95, 204, 0.1) !important;
}

body.dark-mode .verification-code-input {
    background-color: var(--card-bg) !important;
    color: var(--text-color) !important;
    border-color: var(--border-color) !important;
}
</style>

<script>
// Auto-focus et formatage du code
document.addEventListener('DOMContentLoaded', function() {
    const codeInput = document.getElementById('verificationCode');
    if (codeInput) {
        codeInput.addEventListener('input', function(e) {
            // Ne garder que les chiffres
            this.value = this.value.replace(/[^0-9]/g, '');
        });
        
        // Auto-submit quand 6 chiffres sont entrés
        codeInput.addEventListener('input', function(e) {
            if (this.value.length === 6) {
                // Optionnel : auto-submit après un court délai
                setTimeout(() => {
                    const form = document.getElementById('verifyForm');
                    if (form) {
                        form.submit();
                    }
                }, 500);
            }
        });
    }
});
</script>

<?php
$content = ob_get_clean();

$additional_styles = ['assets/css/auth.css'];
renderLayout('Vérification Email', $content, $additional_styles);
?>

