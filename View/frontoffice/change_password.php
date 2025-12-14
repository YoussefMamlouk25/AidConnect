<?php
//ff
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
require_once '../../Model/user.php';
require_once 'layout.php';

if (!isset($_SESSION['id_user'])) {
    header('Location: login.php');
    exit;
}
$model = new UserModel();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm'] ?? '';

    if ($password === '' || $confirm === '') {
        $error = "Veuillez remplir les deux champs.";
    } elseif ($password !== $confirm) {
        $error = "Les mots de passe ne correspondent pas.";
    } elseif (strlen($password) < 6) {
        $error = "Le mot de passe doit contenir au moins 6 caractères.";
    } else {
        $ok = $model->changePassword($_SESSION['id_user'], $password);
        if ($ok) {
            $success = "Mot de passe mis à jour avec succès.";
    } elseif (strlen($password) < 8) {
        $error = "Le mot de passe doit contenir au moins 8 caractères.";
    } else {
        $ok = $model->changePassword($_SESSION['id_user'], $password);
        if ($ok) {
            $success = "Mot de passe mis à jour avec succès !";
        } else {
            $error = "Erreur lors de la mise à jour du mot de passe.";
        }
    }
}

ob_start();
?>
<div class="row justify-content-center">
    <div class="col-lg-10 col-xl-8">
        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <i class="ri-error-warning-line me-2"></i><?= htmlspecialchars($error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <i class="ri-checkbox-circle-line me-2"></i><?= htmlspecialchars($success) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Change Password Card -->
        <div class="card iq-card shadow-sm border-0">
            <div class="card-header bg-white border-0 pb-3">
                <div class="d-flex align-items-center justify-content-between">
                    <h5 class="mb-0 fw-bold">
                        <i class="ri-lock-password-line me-2 text-primary"></i>Changer le mot de passe
                    </h5>
                    <a href="profile.php" class="btn btn-sm btn-outline-secondary">
                        <i class="ri-arrow-left-line me-1"></i>Retour
                    </a>
                </div>
            </div>
            <div class="card-body">
                <form method="post" id="changePasswordForm">
                    <div class="row g-4">
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label fw-semibold">
                                    <i class="ri-lock-line me-1 text-primary"></i>Nouveau mot de passe <span class="text-danger">*</span>
                                </label>
                                <input type="password" 
                                       name="password" 
                                       class="form-control form-control-lg" 
                                       required
                                       minlength="6"
                                       placeholder="Entrez votre nouveau mot de passe"
                                       autocomplete="new-password">
                                <small class="text-muted">Le mot de passe doit contenir au moins 6 caractères.</small>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label fw-semibold">
                                    <i class="ri-lock-2-line me-1 text-primary"></i>Confirmer le mot de passe <span class="text-danger">*</span>
                                </label>
                                <input type="password" 
                                       name="confirm" 
                                       class="form-control form-control-lg" 
                                       required
                                       minlength="6"
                                       placeholder="Confirmez votre nouveau mot de passe"
                                       autocomplete="new-password">
                                <small class="text-muted">Répétez le mot de passe pour confirmation.</small>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 justify-content-end mt-4 pt-4 border-top">
                        <a href="profile.php" class="btn btn-outline-secondary btn-lg">
                            <i class="ri-close-line me-1"></i>Annuler
                        </a>
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="ri-save-line me-1"></i>Mettre à jour
                        </button>
                    </div>
                </form>
<style>
    /* Consistent with profile.php */
    .profile-main-container {
        min-height: 80vh;
        padding: 2rem 0;
        background: #f8f9fa;
    }
    
    .profile-mega-card {
        border-radius: 25px;
        border: none;
        box-shadow: 0 15px 50px rgba(0,0,0,0.1);
        overflow: hidden;
        margin-bottom: 2rem;
    }
    
    .profile-mega-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 3rem 2rem !important;
        color: white;
        position: relative;
        overflow: hidden;
    }
    
    .profile-mega-title {
        font-size: 2.5rem !important;
        font-weight: 800;
        margin-bottom: 0.5rem;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
    }
    
    .password-section-title {
        font-size: 1.8rem !important;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 4px solid #667eea;
    }
    
    .password-form-card {
        background: white;
        border-radius: 20px;
        padding: 3rem !important;
        margin-bottom: 2rem;
        border: none;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
    }
    
    .password-requirements {
        background: #f8f9ff;
        border-radius: 15px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        border-left: 4px solid #667eea;
    }
    
    .password-requirements h5 {
        color: #2c3e50;
        margin-bottom: 1rem;
    }
    
    .password-requirements ul {
        margin: 0;
        padding-left: 1.5rem;
    }
    
    .password-requirements li {
        margin-bottom: 0.5rem;
        color: #6c757d;
    }
    
    .password-requirements li i {
        color: #667eea;
        margin-right: 0.5rem;
    }
    
    .password-strength {
        margin-top: 1rem;
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
    
    .password-input-group {
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
    }
    
    .password-toggle:hover {
        color: #667eea;
    }
    
    .btn-mega {
        padding: 1.2rem 3rem !important;
        font-size: 1.3rem !important;
        border-radius: 15px;
        font-weight: 700;
        min-width: 220px;
        text-align: center;
        transition: all 0.3s ease;
    }
    
    .btn-update {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: white;
    }
    
    .btn-update:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        color: white;
    }
    
    .btn-back {
        border: 2px solid #667eea;
        color: #667eea;
    }
    
    .btn-back:hover {
        background: #667eea;
        color: white;
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(102, 126, 234, 0.2);
    }
    
    .alert-mega-success {
        border-radius: 20px;
        padding: 1.8rem;
        font-size: 1.2rem;
        box-shadow: 0 10px 30px rgba(40, 167, 69, 0.2);
        margin-bottom: 3rem;
    }
    
    .alert-mega-danger {
        border-radius: 20px;
        padding: 1.8rem;
        font-size: 1.2rem;
        box-shadow: 0 10px 30px rgba(220, 53, 69, 0.2);
        margin-bottom: 3rem;
    }
    
    .security-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 2rem;
    }
    
    .security-icon i {
        font-size: 2.5rem;
        color: white;
    }
    
    @media (max-width: 768px) {
        .password-form-card {
            padding: 2rem !important;
        }
        
        .btn-mega {
            min-width: 100%;
            margin-bottom: 1rem;
        }
        
        .profile-mega-title {
            font-size: 2rem !important;
        }
    }
</style>

<div class="profile-main-container">
    <div class="container">
        <!-- Success Message -->
        <?php if ($success): ?>
            <div class="alert alert-success alert-mega-success alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-center">
                    <i class="ri-checkbox-circle-fill me-3" style="font-size: 2rem;"></i>
                    <div class="flex-grow-1">
                        <h4 class="mb-1">Succès !</h4>
                        <p class="mb-0"><?= htmlspecialchars($success) ?></p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <!-- Error Message -->
        <?php if ($error): ?>
            <div class="alert alert-danger alert-mega-danger alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-center">
                    <i class="ri-error-warning-fill me-3" style="font-size: 2rem;"></i>
                    <div class="flex-grow-1">
                        <h4 class="mb-1">Attention !</h4>
                        <p class="mb-0"><?= htmlspecialchars($error) ?></p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Main Password Card -->
        <div class="card profile-mega-card">
            <!-- Header -->
            <div class="profile-mega-header text-center">
                <div class="security-icon">
                    <i class="ri-shield-keyhole-line"></i>
                </div>
                
                <h1 class="profile-mega-title">Sécurité du compte</h1>
                
                <p class="mt-3" style="font-size: 1.2rem; opacity: 0.9;">
                    <i class="ri-shield-check-line me-2"></i>
                    Protégez votre compte avec un mot de passe fort
                </p>
            </div>

            <!-- Card Body -->
            <div class="card-body p-4 p-md-5">
                <!-- Password Requirements -->
                <div class="password-requirements">
                    <h5><i class="ri-information-line me-2"></i>Exigences de sécurité</h5>
                    <ul>
                        <li><i class="ri-check-line"></i> Au moins 8 caractères</li>
                        <li><i class="ri-check-line"></i> Combinaison de lettres majuscules et minuscules</li>
                        <li><i class="ri-check-line"></i> Au moins un chiffre</li>
                        <li><i class="ri-check-line"></i> Au moins un caractère spécial (!@#$%^&*)</li>
                    </ul>
                </div>
                
                <!-- Password Form -->
                <div class="password-form-card">
                    <h2 class="password-section-title">
                        <i class="ri-lock-password-line me-3"></i>
                        CHANGER LE MOT DE PASSE
                    </h2>
                    
                    <form method="post" id="passwordForm">
                        <!-- New Password -->
                        <div class="form-group-enhanced">
                            <label class="form-label-enhanced">
                                <i class="ri-key-2-line"></i>
                                Nouveau mot de passe
                            </label>
                            <div class="password-input-group">
                                <input type="password" 
                                       name="password" 
                                       id="password"
                                       class="form-control-enhanced"
                                       required
                                       placeholder="Entrez votre nouveau mot de passe"
                                       oninput="checkPasswordStrength()">
                                <button type="button" class="password-toggle" onclick="togglePassword('password')">
                                    <i class="ri-eye-line"></i>
                                </button>
                            </div>
                            
                            <!-- Password Strength Indicator -->
                            <div id="passwordStrength" class="password-strength">
                                <div class="strength-bar">
                                    <div id="strengthFill" class="strength-fill"></div>
                                </div>
                                <div id="strengthText" class="strength-text"></div>
                            </div>
                        </div>

                        <!-- Confirm Password -->
                        <div class="form-group-enhanced">
                            <label class="form-label-enhanced">
                                <i class="ri-key-2-fill"></i>
                                Confirmer le mot de passe
                            </label>
                            <div class="password-input-group">
                                <input type="password" 
                                       name="confirm" 
                                       id="confirmPassword"
                                       class="form-control-enhanced"
                                       required
                                       placeholder="Confirmez votre nouveau mot de passe"
                                       oninput="checkPasswordMatch()">
                                <button type="button" class="password-toggle" onclick="togglePassword('confirmPassword')">
                                    <i class="ri-eye-line"></i>
                                </button>
                            </div>
                            <div id="passwordMatch" class="mt-2"></div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="row mt-5">
                            <div class="col-12">
                                <div class="d-flex flex-wrap gap-4 justify-content-center">
                                    <a href="profile.php" class="btn btn-back btn-mega">
                                        <i class="ri-arrow-left-line me-3"></i>
                                        RETOUR AU PROFIL
                                    </a>
                                    <button type="submit" class="btn btn-update btn-mega">
                                        <i class="ri-refresh-line me-3"></i>
                                        METTRE À JOUR LE MOT DE PASSE
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                
                <!-- Security Tips -->
                <div class="password-requirements">
                    <h5><i class="ri-lightbulb-flash-line me-2"></i>Conseils de sécurité</h5>
                    <ul>
                        <li><i class="ri-shield-star-line"></i> Utilisez un mot de passe unique que vous n'utilisez nulle part ailleurs</li>
                        <li><i class="ri-shield-star-line"></i> Changez votre mot de passe régulièrement</li>
                        <li><i class="ri-shield-star-line"></i> N'utilisez pas d'informations personnelles évidentes</li>
                        <li><i class="ri-shield-star-line"></i> Envisagez d'utiliser un gestionnaire de mots de passe</li>
                    </ul>
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
        strengthText.textContent = 'Force : ' + text;
        strengthText.style.color = color;
    }
    
    // Check password match
    function checkPasswordMatch() {
        const password = document.getElementById('password').value;
        const confirm = document.getElementById('confirmPassword').value;
        const matchDiv = document.getElementById('passwordMatch');
        
        if (confirm.length === 0) {
            matchDiv.innerHTML = '';
            return;
        }
        
        if (password === confirm) {
            matchDiv.innerHTML = '<span style="color: #28a745;"><i class="ri-check-line"></i> Les mots de passe correspondent</span>';
        } else {
            matchDiv.innerHTML = '<span style="color: #dc3545;"><i class="ri-close-line"></i> Les mots de passe ne correspondent pas</span>';
        }
    }
    
    // Form validation
    document.getElementById('passwordForm').addEventListener('submit', function(e) {
        const password = document.getElementById('password').value;
        const confirm = document.getElementById('confirmPassword').value;
        
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
    });
    
    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        checkPasswordStrength();
        checkPasswordMatch();
    });
</script>

<script>
document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
    const password = document.querySelector('input[name="password"]').value;
    const confirm = document.querySelector('input[name="confirm"]').value;
    
    if (password !== confirm) {
        e.preventDefault();
        alert('Les mots de passe ne correspondent pas.');
        return false;
    }
    
    if (password.length < 6) {
        e.preventDefault();
        alert('Le mot de passe doit contenir au moins 6 caractères.');
        return false;
    }
});
</script>
<?php
$content = ob_get_clean();
renderLayout('Changer le mot de passe', $content, ['assets/css/profile.css'], ['assets/js/profile-fix.js']);

$styles = [
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css',
    'https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css'
];

$scripts = [
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js'
];

renderTemplate('Changer le mot de passe | AidConnect', $content, $styles, $scripts);
