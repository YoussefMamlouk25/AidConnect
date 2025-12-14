<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
session_start();
}
require_once 'layout.php';

$error = $_GET['error'] ?? '';

ob_start();
?>
<div class="row justify-content-center py-5">
    <div class="col-12 col-md-8 col-lg-7 col-xl-6">
        <div class="auth-card shadow-lg">
            <div class="auth-card-header text-center mb-4">
                <div class="auth-logo mb-3">
                    <i class="ri-heart-pulse-fill"></i>
                </div>
                <h2 class="mb-1">Créer un compte</h2>
                <p class="text-muted mb-0">Rejoignez la communauté AidConnect</p>
            </div>

        <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="ri-error-warning-line me-2"></i>
                    <?= htmlspecialchars($error) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
        <?php endif; ?>

        <form id="signupForm" method="post" action="../../Controller/signupcontroller.php" class="auth-form">
                <div class="row">
                    <div class="col-12 mb-3">
                        <label class="form-label">
                            <i class="ri-user-line me-2"></i>Nom complet
                        </label>
                        <input type="text" name="name" class="form-control form-control-lg" placeholder="Votre nom complet" required autofocus>
                    </div>
            </div>

                <div class="row">
                    <div class="col-12 mb-3">
                        <label class="form-label">
                            <i class="ri-mail-line me-2"></i>Email
                        </label>
                        <input type="email" name="email" class="form-control form-control-lg" placeholder="votre@email.com" required>
                    </div>
            </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            <i class="ri-id-card-line me-2"></i>CIN
                        </label>
                        <input type="text" name="cin" class="form-control form-control-lg" placeholder="8 chiffres" maxlength="8">
            </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            <i class="ri-calendar-line me-2"></i>Date de naissance
                        </label>
                        <input type="date" name="date_naissance" class="form-control form-control-lg">
                    </div>
            </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            <i class="ri-phone-line me-2"></i>Téléphone
                        </label>
                        <input type="text" name="tel" class="form-control form-control-lg" placeholder="8 chiffres" maxlength="8">
            </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            <i class="ri-map-pin-line me-2"></i>Région
                        </label>
                        <select name="region" class="form-select form-select-lg">
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

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            <i class="ri-lock-line me-2"></i>Mot de passe
                        </label>
                        <input type="password" name="password" class="form-control form-control-lg" placeholder="••••••••" required>
            </div>

                    <div class="col-md-6 mb-4">
                        <label class="form-label">
                            <i class="ri-lock-password-line me-2"></i>Confirmer le mot de passe
                        </label>
                        <input type="password" name="confirm" class="form-control form-control-lg" placeholder="••••••••" required>
                    </div>
            </div>

                <button type="submit" class="btn btn-primary btn-lg w-100 mb-3">
                    <i class="ri-user-add-line me-2"></i>Créer un compte
                </button>
        </form>

            <div class="auth-card-footer text-center mt-4">
                <p class="mb-0">
                    Déjà un compte ? <a href="login.php" class="fw-bold">Se connecter</a>
        </p>
    </div>
</div>
    </div>
</div>
<?php
$content = ob_get_clean();

$additional_styles = ['assets/css/auth.css'];
$additional_scripts = ['assets/js/signup-validation.js'];
renderLayout('Inscription', $content, $additional_styles, $additional_scripts);
