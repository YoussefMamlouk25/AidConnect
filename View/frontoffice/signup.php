<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
session_start();
}
require_once 'layout.php';

$error = $_GET['error'] ?? '';
$success = $_GET['success'] ?? '';

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

<style>
    /* Consistent with other pages */
    .auth-main-container {
        min-height: 100vh;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem;
        position: relative;
        overflow: hidden;
    }
    
    .auth-main-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="rgba(255,255,255,0.1)" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,112C672,96,768,96,864,112C960,128,1056,160,1152,160C1248,160,1344,128,1392,112L1440,96L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>');
        background-size: cover;
        opacity: 0.2;
        animation: wave 20s linear infinite;
    }
    
    @keyframes wave {
        0% { background-position-x: 0; }
        100% { background-position-x: 1440px; }
    }
    
    .auth-mega-card {
        background: white;
        border-radius: 30px;
        border: none;
        box-shadow: 0 25px 50px rgba(0,0,0,0.2);
        width: 100%;
        max-width: 550px;
        position: relative;
        z-index: 1;
        overflow: hidden;
    }
    
    .auth-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 2.5rem 2rem;
        color: white;
        text-align: center;
    }
    
    .auth-icon-container {
        width: 90px;
        height: 90px;
        background: rgba(255,255,255,0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        border: 4px solid white;
    }
    
    .auth-icon-container i {
        font-size: 2.8rem;
    }
    
    .auth-title {
        font-size: 2.2rem;
        font-weight: 800;
        margin-bottom: 0.5rem;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
    }
    
    .auth-subtitle {
        font-size: 1.1rem;
        opacity: 0.9;
    }
    
    .auth-body {
        padding: 2.5rem;
    }
    
    .form-group-enhanced {
        margin-bottom: 1.8rem;
    }
    
    .form-label-enhanced {
        font-size: 1.1rem;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 0.8rem;
        display: flex;
        align-items: center;
    }
    
    .form-label-enhanced i {
        margin-right: 10px;
        color: #667eea;
        font-size: 1.3rem;
        min-width: 24px;
    }
    
    .form-label-enhanced .required {
        color: #dc3545;
        margin-left: 4px;
    }
    
    .input-group-enhanced {
        position: relative;
    }
    
    .form-control-enhanced {
        border: 2px solid #e0e0e0;
        border-radius: 12px;
        padding: 1rem 1.2rem;
        font-size: 1.1rem;
        transition: all 0.3s ease;
        width: 100%;
    }
    
    .form-control-enhanced:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.2);
        transform: translateY(-2px);
    }
    
    .form-control-enhanced.select-enhanced {
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%236c757d' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 1.2rem center;
        background-size: 16px;
        padding-right: 3rem;
    }
    
    .password-input-group {
        position: relative;
    }
    
    .password-toggle {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #6c757d;
        cursor: pointer;
        font-size: 1.2rem;
        z-index: 2;
    }
    
    .password-toggle:hover {
        color: #667eea;
    }
    
    .input-icon {
        position: absolute;
        right: 45px;
        top: 50%;
        transform: translateY(-50%);
        color: #6c757d;
        font-size: 1.2rem;
    }
    
    .form-hint {
        font-size: 0.85rem;
        color: #6c757d;
        margin-top: 0.5rem;
        display: flex;
        align-items: center;
    }
    
    .form-hint i {
        margin-right: 5px;
    }
    
    .btn-auth {
        padding: 1.2rem !important;
        font-size: 1.2rem !important;
        border-radius: 15px;
        font-weight: 700;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: white;
        width: 100%;
        transition: all 0.3s ease;
        margin-top: 1rem;
    }
    
    .btn-auth:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        color: white;
    }
    
    .auth-links {
        text-align: center;
        margin-top: 2rem;
        padding-top: 2rem;
        border-top: 2px solid #f0f0f0;
    }
    
    .alert-mega-success {
        border-radius: 15px;
        padding: 1.5rem;
        font-size: 1.1rem;
        box-shadow: 0 10px 30px rgba(40, 167, 69, 0.2);
        margin-bottom: 2rem;
        border: none;
    }
    
    .alert-mega-danger {
        border-radius: 15px;
        padding: 1.5rem;
        font-size: 1.1rem;
        box-shadow: 0 10px 30px rgba(220, 53, 69, 0.2);
        margin-bottom: 2rem;
        border: none;
    }
    
    .app-brand {
        text-align: center;
        margin-bottom: 1.5rem;
    }
    
    .app-name {
        font-size: 2rem;
        font-weight: 800;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 0.5rem;
    }
    
    .app-tagline {
        color: #6c757d;
        font-size: 1rem;
    }
    
    .btn-secondary-action {
        padding: 1rem !important;
        font-size: 1.1rem !important;
        border-radius: 12px;
        font-weight: 600;
        border: 2px solid #6c757d;
        color: #6c757d;
        background: white;
        width: 100%;
        transition: all 0.3s ease;
        margin-top: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .btn-secondary-action:hover {
        background: #6c757d;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(108, 117, 125, 0.2);
    }
    
    /* Password strength indicator */
    .password-strength {
        margin-top: 0.5rem;
        display: none;
    }
    
    .strength-bar {
        height: 6px;
        background: #e9ecef;
        border-radius: 3px;
        margin-bottom: 0.5rem;
        overflow: hidden;
    }
    
    .strength-fill {
        height: 100%;
        width: 0%;
        border-radius: 3px;
        transition: width 0.3s ease;
    }
    
    .strength-text {
        font-size: 0.9rem;
        font-weight: 600;
    }
    
    /* Two-column layout for some fields */
    .row-cols-md-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
    }
    
    @media (max-width: 768px) {
        .auth-body {
            padding: 2rem;
        }
        
        .auth-title {
            font-size: 1.8rem;
        }
        
        .auth-icon-container {
            width: 80px;
            height: 80px;
        }
        
        .auth-icon-container i {
            font-size: 2.5rem;
        }
        
        .row-cols-md-2 {
            grid-template-columns: 1fr;
            gap: 1rem;
        }
    }
    
    @media (max-width: 576px) {
        .auth-main-container {
            padding: 1rem;
        }
        
        .auth-body {
            padding: 1.5rem;
        }
    }
</style>

<div class="auth-main-container">
    <div class="auth-mega-card">
        <!-- Auth Header -->
        <div class="auth-header">
            <div class="auth-icon-container">
                <i class="ri-user-add-line"></i>
            </div>
            <h1 class="auth-title">Rejoignez AidConnect</h1>
            <p class="auth-subtitle">Créez votre compte en quelques étapes simples</p>
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
        <!-- App Brand -->
        <div class="app-brand">
            <div class="app-name">AidConnect</div>
            <p class="app-tagline">Devenez membre de notre communauté d'entraide</p>
        </div>

        <!-- Auth Body -->
        <div class="auth-body">
            <!-- Error Message -->
            <?php if ($error): ?>
                <div class="alert alert-danger alert-mega-danger alert-dismissible fade show" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="ri-error-warning-fill me-3"></i>
                        <div class="flex-grow-1"><?= htmlspecialchars($error) ?></div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <!-- Success Message -->
            <?php if ($success): ?>
                <div class="alert alert-success alert-mega-success alert-dismissible fade show" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="ri-checkbox-circle-fill me-3"></i>
                        <div class="flex-grow-1"><?= htmlspecialchars($success) ?></div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- Signup Form -->
            <form id="signupForm" method="post" action="../../Controller/signupcontroller.php">
                <!-- Name -->
                <div class="form-group-enhanced">
                    <label class="form-label-enhanced">
                        <i class="ri-user-line"></i>
                        Nom complet
                        <span class="required">*</span>
                    </label>
                    <input type="text" 
                           name="name" 
                           class="form-control-enhanced"
                           required
                           placeholder="Votre nom et prénom"
                           autocomplete="name">
                </div>

                <!-- Email -->
                <div class="form-group-enhanced">
                    <label class="form-label-enhanced">
                        <i class="ri-mail-line"></i>
                        Adresse email
                        <span class="required">*</span>
                    </label>
                    <div class="input-group-enhanced">
                        <input type="email" 
                               name="email" 
                               class="form-control-enhanced"
                               required
                               placeholder="votre@email.com"
                               autocomplete="email">
                        <div class="input-icon">
                            <i class="ri-at-line"></i>
                        </div>
                    </div>
                </div>

                <!-- CIN and Birth Date in two columns -->
                <div class="row-cols-md-2">
                    <!-- CIN -->
                    <div class="form-group-enhanced">
                        <label class="form-label-enhanced">
                            <i class="ri-id-card-line"></i>
                            Numéro CIN
                        </label>
                        <input type="text" 
                               name="cin" 
                               class="form-control-enhanced"
                               placeholder="12345678"
                               pattern="\d{8}"
                               maxlength="8">
                        <div class="form-hint">
                            <i class="ri-information-line"></i>
                            8 chiffres uniquement
                        </div>
                    </div>

                    <!-- Birth Date -->
                    <div class="form-group-enhanced">
                        <label class="form-label-enhanced">
                            <i class="ri-calendar-line"></i>
                            Date de naissance
                        </label>
                        <input type="date" 
                               name="date_naissance" 
                               class="form-control-enhanced"
                               max="<?= date('Y-m-d'); ?>">
                    </div>
                </div>

                <!-- Phone and Region in two columns -->
                <div class="row-cols-md-2">
                    <!-- Phone -->
                    <div class="form-group-enhanced">
                        <label class="form-label-enhanced">
                            <i class="ri-phone-line"></i>
                            Téléphone
                        </label>
                        <div class="input-group-enhanced">
                            <input type="tel" 
                                   name="tel" 
                                   class="form-control-enhanced"
                                   placeholder="01234567"
                                   pattern="\d{8}"
                                   maxlength="8">
                            <div class="input-icon">
                                <i class="ri-smartphone-line"></i>
                            </div>
                        </div>
                        <div class="form-hint">
                            <i class="ri-information-line"></i>
                            8 chiffres tunisiens
                        </div>
                    </div>

                    <!-- Region -->
                    <div class="form-group-enhanced">
                        <label class="form-label-enhanced">
                            <i class="ri-map-pin-line"></i>
                            Région
                        </label>
                        <select name="region" class="form-control-enhanced select-enhanced">
                            <option value="">-- Sélectionnez votre région --</option>
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
                </div>

                <!-- Password -->
                <div class="form-group-enhanced">
                    <label class="form-label-enhanced">
                        <i class="ri-lock-line"></i>
                        Mot de passe
                        <span class="required">*</span>
                    </label>
                    <div class="password-input-group">
                        <input type="password" 
                               name="password" 
                               id="password"
                               class="form-control-enhanced"
                               required
                               placeholder="Créez un mot de passe sécurisé"
                               autocomplete="new-password"
                               oninput="checkPasswordStrength()">
                        <button type="button" class="password-toggle" onclick="togglePassword('password')">
                            <i class="ri-eye-line"></i>
                        </button>
                        <div class="input-icon">
                            <i class="ri-key-2-line"></i>
                        </div>
                    </div>
                    
                    <!-- Password Strength Indicator -->
                    <div id="passwordStrength" class="password-strength">
                        <div class="strength-bar">
                            <div id="strengthFill" class="strength-fill"></div>
                        </div>
                        <div id="strengthText" class="strength-text"></div>
                    </div>
                    
                    <div class="form-hint">
                        <i class="ri-information-line"></i>
                        Minimum 8 caractères avec lettres, chiffres et caractères spéciaux
                    </div>
                </div>

                <!-- Confirm Password -->
                <div class="form-group-enhanced">
                    <label class="form-label-enhanced">
                        <i class="ri-lock-unlock-line"></i>
                        Confirmer le mot de passe
                        <span class="required">*</span>
                    </label>
                    <div class="password-input-group">
                        <input type="password" 
                               name="confirm" 
                               id="confirmPassword"
                               class="form-control-enhanced"
                               required
                               placeholder="Retapez votre mot de passe"
                               autocomplete="new-password"
                               oninput="checkPasswordMatch()">
                        <button type="button" class="password-toggle" onclick="togglePassword('confirmPassword')">
                            <i class="ri-eye-line"></i>
                        </button>
                        <div class="input-icon">
                            <i class="ri-key-2-fill"></i>
                        </div>
                    </div>
                    <div id="passwordMatch" class="form-hint"></div>
                </div>

                <!-- Terms Agreement -->
                <div class="form-group-enhanced">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="terms" name="terms" required style="width: 20px; height: 20px; accent-color: #667eea;">
                        <label class="form-check-label" for="terms" style="color: #6c757d; font-weight: 500;">
                            J'accepte les <a href="#" style="color: #667eea; text-decoration: none;">conditions d'utilisation</a> 
                            et la <a href="#" style="color: #667eea; text-decoration: none;">politique de confidentialité</a>
                        </label>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-auth">
                    <i class="ri-user-add-line me-3"></i>
                    CRÉER MON COMPTE
                </button>
            </form>

            <!-- Action Links -->
            <div class="auth-links">
                <p class="mb-3">Déjà membre de notre communauté ?</p>
                <a href="login.php" class="btn btn-secondary-action">
                    <i class="ri-login-box-line me-3"></i>
                    SE CONNECTER À MON COMPTE
                </a>
                
                <div class="mt-4">
                    <a href="../index.php" class="d-inline-flex align-items-center" style="color: #667eea; text-decoration: none; font-weight: 500;">
                        <i class="ri-arrow-left-line me-2"></i>
                        Retour à l'accueil
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Toggle password visibility
    function togglePassword(fieldId) {
        const field = document.getElementById(fieldId);
        const toggle = field.nextElementSibling.querySelector('i');
        
        if (field.type === 'password') {
            field.type = 'text';
            toggle.classList.remove('ri-eye-line');
            toggle.classList.add('ri-eye-off-line');
        } else {
            field.type = 'password';
            toggle.classList.remove('ri-eye-off-line');
            toggle.classList.add('ri-eye-line');
        }
    }
    
    // Check password strength
    function checkPasswordStrength() {
        const password = document.getElementById('password').value;
        const strengthBar = document.getElementById('strengthFill');
        const strengthText = document.getElementById('strengthText');
        const strengthContainer = document.getElementById('passwordStrength');
        
        if (password.length === 0) {
            strengthContainer.style.display = 'none';
            return;
        }
        
        strengthContainer.style.display = 'block';
        
        let strength = 0;
        let color = '#dc3545';
        let text = 'Faible';
        
        // Length check
        if (password.length >= 8) strength += 25;
        
        // Lowercase check
        if (/[a-z]/.test(password)) strength += 25;
        
        // Uppercase check
        if (/[A-Z]/.test(password)) strength += 25;
        
        // Special character check
        if (/[!@#$%^&*]/.test(password)) strength += 25;
        
        // Set color and text based on strength
        if (strength >= 75) {
            color = '#28a745';
            text = 'Fort';
        } else if (strength >= 50) {
            color = '#ffc107';
            text = 'Moyen';
        } else if (strength >= 25) {
            color = '#fd7e14';
            text = 'Faible';
        } else {
            color = '#dc3545';
            text = 'Très faible';
        }
        
        strengthBar.style.width = strength + '%';
        strengthBar.style.backgroundColor = color;
        strengthText.textContent = 'Force du mot de passe : ' + text;
        strengthText.style.color = color;
    }
    
    // Check password match
    function checkPasswordMatch() {
        const password = document.getElementById('password').value;
        const confirm = document.getElementById('confirmPassword').value;
        const matchDiv = document.getElementById('passwordMatch');
        
        if (confirm.length === 0) {
            matchDiv.innerHTML = '';
            matchDiv.className = 'form-hint';
            return;
        }
        
        if (password === confirm) {
            matchDiv.innerHTML = '<i class="ri-check-line" style="color: #28a745;"></i> Les mots de passe correspondent';
            matchDiv.style.color = '#28a745';
            matchDiv.style.fontWeight = '600';
        } else {
            matchDiv.innerHTML = '<i class="ri-close-line" style="color: #dc3545;"></i> Les mots de passe ne correspondent pas';
            matchDiv.style.color = '#dc3545';
            matchDiv.style.fontWeight = '600';
        }
    }
    
    // Form validation
    document.getElementById('signupForm').addEventListener('submit', function(e) {
        const password = document.getElementById('password').value;
        const confirm = document.getElementById('confirmPassword').value;
        const terms = document.getElementById('terms');
        
        // Password validation
        if (password.length < 8) {
            e.preventDefault();
            alert('Le mot de passe doit contenir au moins 8 caractères.');
            return false;
        }
        
        if (password !== confirm) {
            e.preventDefault();
            alert('Les mots de passe ne correspondent pas.');
            return false;
        }
        
        // Terms validation
        if (!terms.checked) {
            e.preventDefault();
            alert('Vous devez accepter les conditions d\'utilisation pour créer un compte.');
            return false;
        }
        
        // CIN validation (if provided)
        const cin = document.querySelector('input[name="cin"]').value;
        if (cin && !/^\d{8}$/.test(cin)) {
            e.preventDefault();
            alert('Le numéro CIN doit contenir exactement 8 chiffres.');
            return false;
        }
        
        // Phone validation (if provided)
        const tel = document.querySelector('input[name="tel"]').value;
        if (tel && !/^\d{8}$/.test(tel)) {
            e.preventDefault();
            alert('Le numéro de téléphone doit contenir exactement 8 chiffres.');
            return false;
        }
        
        // Add loading animation
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.innerHTML = '<i class="ri-loader-4-line ri-spin me-2"></i> Création du compte...';
        submitBtn.disabled = true;
        
        return true;
    });
    
    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Format phone input for Tunisia
        const phoneInput = document.querySelector('input[name="tel"]');
        if (phoneInput) {
            phoneInput.addEventListener('input', function(e) {
                this.value = this.value.replace(/\D/g, '').slice(0, 8);
            });
        }
        
        // Format CIN input
        const cinInput = document.querySelector('input[name="cin"]');
        if (cinInput) {
            cinInput.addEventListener('input', function(e) {
                this.value = this.value.replace(/\D/g, '').slice(0, 8);
            });
        }
        
        // Set max date for birth date (minimum 18 years old)
        const birthDateInput = document.querySelector('input[name="date_naissance"]');
        if (birthDateInput) {
            const maxDate = new Date();
            maxDate.setFullYear(maxDate.getFullYear() - 18);
            birthDateInput.max = maxDate.toISOString().split('T')[0];
            
            // Set min date (100 years ago)
            const minDate = new Date();
            minDate.setFullYear(minDate.getFullYear() - 100);
            birthDateInput.min = minDate.toISOString().split('T')[0];
        }
        
        // Auto-focus first input
        document.querySelector('input[name="name"]').focus();
    });
</script>

<?php
$content = ob_get_clean();

$styles = [
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css',
    'https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css'
];

$scripts = [
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js'
];

renderTemplate('Inscription | AidConnect', $content, $styles, $scripts);
