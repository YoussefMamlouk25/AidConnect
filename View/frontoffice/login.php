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

<style>
    /* Consistent with profile pages */
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
        max-width: 450px;
        position: relative;
        z-index: 1;
        overflow: hidden;
    }
    
    .auth-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 3rem 2rem;
        color: white;
        text-align: center;
    }
    
    .auth-icon-container {
        width: 100px;
        height: 100px;
        background: rgba(255,255,255,0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        border: 4px solid white;
    }
    
    .auth-icon-container i {
        font-size: 3rem;
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
        padding: 3rem;
    }
    
    .form-group-enhanced {
        margin-bottom: 2rem;
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
    }
    
    .input-group-enhanced {
        position: relative;
    }
    
    .form-control-enhanced {
        border: 2px solid #e0e0e0;
        border-radius: 15px;
        padding: 1.1rem 1.5rem;
        font-size: 1.1rem;
        transition: all 0.3s ease;
        width: 100%;
    }
    
    .form-control-enhanced:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.2);
        transform: translateY(-2px);
    }
    
    .input-icon {
        position: absolute;
        right: 20px;
        top: 50%;
        transform: translateY(-50%);
        color: #6c757d;
        font-size: 1.2rem;
    }
    
    .password-toggle {
        position: absolute;
        right: 50px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #6c757d;
        cursor: pointer;
        font-size: 1.2rem;
    }
    
    .password-toggle:hover {
        color: #667eea;
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
    
    .auth-link {
        color: #667eea;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
    }
    
    .auth-link:hover {
        color: #764ba2;
        transform: translateX(5px);
    }
    
    .auth-link i {
        margin-right: 8px;
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
        margin-bottom: 2rem;
    }
    
    .app-name {
        font-size: 2.5rem;
        font-weight: 800;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 0.5rem;
    }
    
    .app-tagline {
        color: #6c757d;
        font-size: 1.1rem;
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
    }
    
    @media (max-width: 576px) {
        .auth-main-container {
            padding: 1rem;
        }
        
        .auth-body {
            padding: 1.5rem;
        }
    }
    
    /* Remember me checkbox */
    .remember-me {
        display: flex;
        align-items: center;
        margin-bottom: 2rem;
    }
    
    .remember-me input[type="checkbox"] {
        width: 20px;
        height: 20px;
        margin-right: 10px;
        accent-color: #667eea;
    }
    
    .remember-me label {
        color: #6c757d;
        font-weight: 500;
        cursor: pointer;
    }
    
    /* Action buttons container */
    .action-buttons {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        margin-top: 2rem;
    }
    
    .btn-home {
        padding: 1rem !important;
        font-size: 1.1rem !important;
        border-radius: 12px;
        font-weight: 600;
        border: 2px solid #667eea;
        color: #667eea;
        background: white;
        width: 100%;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .btn-home:hover {
        background: #667eea;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.2);
    }
    
    .btn-signup {
        padding: 1rem !important;
        font-size: 1.1rem !important;
        border-radius: 12px;
        font-weight: 600;
        border: 2px solid #28a745;
        color: #28a745;
        background: white;
        width: 100%;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .btn-signup:hover {
        background: #28a745;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(40, 167, 69, 0.2);
    }
</style>

<div class="auth-main-container">
    <div class="auth-mega-card">
        <!-- Auth Header -->
        <div class="auth-header">
            <div class="auth-icon-container">
                <i class="ri-login-box-line"></i>
            </div>
            <h1 class="auth-title">Bienvenue</h1>
            <p class="auth-subtitle">Connectez-vous à votre compte AidConnect</p>
        </div>

        <!-- App Brand -->
        <div class="app-brand">
            <div class="app-name">AidConnect</div>
            <p class="app-tagline">Votre plateforme communautaire d'entraide</p>
        </div>

        <!-- Auth Body -->
        <div class="auth-body">
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

            <!-- Login Form -->
            <form method="post" action="../../Controller/logincontroller.php" id="loginForm">
                <!-- Email Field -->
                <div class="form-group-enhanced">
                    <label class="form-label-enhanced">
                        <i class="ri-mail-line"></i>
                        Adresse email
                    </label>
                    <div class="input-group-enhanced">
                        <input type="email" 
                               name="email" 
                               class="form-control-enhanced"
                               required
                               placeholder="votre@email.com"
                               autocomplete="email">
                        <div class="input-icon">
                            <i class="ri-user-line"></i>
                        </div>
                    </div>
                </div>

                <!-- Password Field -->
                <div class="form-group-enhanced">
                    <label class="form-label-enhanced">
                        <i class="ri-lock-line"></i>
                        Mot de passe
                    </label>
                    <div class="input-group-enhanced">
                        <input type="password" 
                               name="password" 
                               id="password"
                               class="form-control-enhanced"
                               required
                               placeholder="Votre mot de passe"
                               autocomplete="current-password">
                        <button type="button" class="password-toggle" onclick="togglePassword()">
                            <i class="ri-eye-line"></i>
                        </button>
                        <div class="input-icon">
                            <i class="ri-key-2-line"></i>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-lg w-100 mb-3">
                    <i class="ri-login-box-line me-2"></i>Se connecter
                </button>
        </form>

            <div class="auth-card-footer text-center mt-4">
                <p class="mb-0">
                    Pas de compte ? <a href="signup.php" class="fw-bold">Créer un compte</a>
        </p>
                <!-- Remember me -->
                <div class="remember-me">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Se souvenir de moi</label>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-auth">
                    <i class="ri-login-box-line me-2"></i>
                    SE CONNECTER
                </button>
            </form>

            <!-- Action Buttons -->
            <div class="action-buttons">
                <!-- Signup Button -->
                <a href="signup.php" class="btn btn-signup">
                    <i class="ri-user-add-line me-2"></i>
                    CRÉER UN NOUVEAU COMPTE
                </a>
                
                <!-- Home Button -->
                <a href="index.php" class="btn btn-home">
                    <i class="ri-home-line me-2"></i>
                    RETOUR À L'ACCUEIL
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    // Toggle password visibility
    function togglePassword() {
        const passwordField = document.getElementById('password');
        const toggleIcon = document.querySelector('.password-toggle i');
        
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            toggleIcon.classList.remove('ri-eye-line');
            toggleIcon.classList.add('ri-eye-off-line');
        } else {
            passwordField.type = 'password';
            toggleIcon.classList.remove('ri-eye-off-line');
            toggleIcon.classList.add('ri-eye-line');
        }
    }
    
    // Form validation and enhancement
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('loginForm');
        const emailInput = form.querySelector('input[name="email"]');
        const passwordInput = document.getElementById('password');
        
        // Add focus effects
        const inputs = [emailInput, passwordInput];
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.parentElement.classList.add('focused');
            });
            
            input.addEventListener('blur', function() {
                if (this.value === '') {
                    this.parentElement.parentElement.classList.remove('focused');
                }
            });
            
            // Check if input has value on load (for browser autofill)
            if (input.value !== '') {
                input.parentElement.parentElement.classList.add('focused');
            }
        });
        
        // Form submission animation
        form.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            // Add loading state
            submitBtn.innerHTML = '<i class="ri-loader-4-line ri-spin me-2"></i> Connexion en cours...';
            submitBtn.disabled = true;
            
            // Allow form submission
            return true;
        });
    });
    
    // Auto-focus email field
    window.onload = function() {
        document.querySelector('input[name="email"]').focus();
    };
</script>

<?php
$content = ob_get_clean();

$additional_styles = ['assets/css/auth.css'];
renderLayout('Connexion', $content, $additional_styles);
$styles = [
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css',
    'https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css'
];

$scripts = [
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js'
];

renderTemplate('Connexion | AidConnect', $content, $styles, $scripts);
