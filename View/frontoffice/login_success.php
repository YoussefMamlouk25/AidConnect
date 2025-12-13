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

<style>
    /* Consistent with other pages */
    .welcome-main-container {
        min-height: 100vh;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem;
        position: relative;
        overflow: hidden;
    }
    
    .welcome-main-container::before {
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
    
    .welcome-mega-card {
        background: white;
        border-radius: 30px;
        border: none;
        box-shadow: 0 25px 50px rgba(0,0,0,0.2);
        width: 100%;
        max-width: 500px;
        position: relative;
        z-index: 1;
        overflow: hidden;
    }
    
    .welcome-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 3rem 2rem;
        color: white;
        text-align: center;
    }
    
    .welcome-icon-container {
        width: 120px;
        height: 120px;
        background: rgba(255,255,255,0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        border: 6px solid white;
    }
    
    .welcome-icon-container i {
        font-size: 4rem;
    }
    
    .success-badge {
        background: rgba(40, 167, 69, 0.15);
        border: 2px solid #28a745;
        color: #28a745;
        padding: 0.8rem 2rem;
        border-radius: 50px;
        font-weight: 700;
        font-size: 1.1rem;
        display: inline-flex;
        align-items: center;
        margin-bottom: 1.5rem;
    }
    
    .success-badge i {
        margin-right: 10px;
        font-size: 1.3rem;
    }
    
    .welcome-title {
        font-size: 2.5rem;
        font-weight: 800;
        margin-bottom: 0.5rem;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
    }
    
    .welcome-subtitle {
        font-size: 1.2rem;
        opacity: 0.9;
        margin-bottom: 0.5rem;
    }
    
    .welcome-body {
        padding: 3rem;
    }
    
    .user-greeting {
        text-align: center;
        margin-bottom: 3rem;
    }
    
    .user-name {
        font-size: 2.2rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 0.5rem;
    }
    
    .user-role-badge {
        font-size: 1.1rem;
        padding: 0.6rem 1.5rem;
        border-radius: 50px;
        display: inline-block;
        margin-bottom: 1rem;
    }
    
    .user-message {
        color: #6c757d;
        font-size: 1.1rem;
        max-width: 400px;
        margin: 0 auto;
    }
    
    .action-buttons {
        display: flex;
        flex-direction: column;
        gap: 1.2rem;
        margin-bottom: 3rem;
    }
    
    .btn-welcome {
        padding: 1.2rem !important;
        font-size: 1.2rem !important;
        border-radius: 15px;
        font-weight: 700;
        width: 100%;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .btn-profile {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: white;
    }
    
    .btn-profile:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        color: white;
    }
    
    .btn-admin {
        border: 2px solid #ffc107;
        color: #ffc107;
        background: white;
    }
    
    .btn-admin:hover {
        background: #ffc107;
        color: #212529;
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(255, 193, 7, 0.2);
    }
    
    .btn-home {
        border: 2px solid #6c757d;
        color: #6c757d;
        background: white;
    }
    
    .btn-home:hover {
        background: #6c757d;
        color: white;
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(108, 117, 125, 0.2);
    }
    
    .logout-section {
        text-align: center;
        padding-top: 2rem;
        border-top: 2px solid #f0f0f0;
    }
    
    .logout-link {
        color: #dc3545;
        text-decoration: none;
        font-weight: 600;
        font-size: 1.1rem;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
    }
    
    .logout-link:hover {
        color: #c82333;
        transform: translateY(-2px);
    }
    
    .logout-link i {
        margin-right: 8px;
    }
    
    .app-brand {
        text-align: center;
        margin-bottom: 2rem;
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
    
    @media (max-width: 768px) {
        .welcome-body {
            padding: 2rem;
        }
        
        .welcome-title {
            font-size: 2rem;
        }
        
        .user-name {
            font-size: 1.8rem;
        }
        
        .welcome-icon-container {
            width: 100px;
            height: 100px;
        }
        
        .welcome-icon-container i {
            font-size: 3rem;
        }
    }
    
    @media (max-width: 576px) {
        .welcome-main-container {
            padding: 1rem;
        }
        
        .welcome-body {
            padding: 1.5rem;
        }
    }
    
    /* Quick stats */
    .quick-stats {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
        margin: 2rem 0;
    }
    
    .stat-item {
        background: #f8f9ff;
        border-radius: 15px;
        padding: 1.5rem;
        text-align: center;
        border: 2px solid #eef2ff;
    }
    
    .stat-number {
        font-size: 2rem;
        font-weight: 700;
        color: #667eea;
        margin-bottom: 0.5rem;
    }
    
    .stat-label {
        color: #6c757d;
        font-size: 0.9rem;
        font-weight: 500;
    }
</style>

<div class="welcome-main-container">
    <div class="welcome-mega-card">
        <!-- Welcome Header -->
        <div class="welcome-header">
            <div class="welcome-icon-container">
                <i class="ri-checkbox-circle-fill"></i>
            </div>
            
            <div class="success-badge">
                <i class="ri-check-double-line"></i>
                Connexion réussie
            </div>
            
            <h1 class="welcome-title">Bienvenue !</h1>
            <p class="welcome-subtitle">Prêt à faire la différence ?</p>
        </div>

        <!-- App Brand -->
        <div class="app-brand">
            <div class="app-name">AidConnect</div>
            <p class="app-tagline">Connecter ceux qui ont besoin d'aide avec ceux qui peuvent aider</p>
        </div>

        <!-- Welcome Body -->
        <div class="welcome-body">
            <!-- User Greeting -->
            <div class="user-greeting">
                <div class="user-name"><?= htmlspecialchars($userName) ?></div>
                
                <?php 
                $roleColors = [
                    'admin' => 'danger',
                    'moderator' => 'warning',
                    'volunteer' => 'primary',
                    'user' => 'info'
                ];
                $roleColor = $roleColors[$userRole] ?? 'primary';
                $roleDisplay = ucfirst($userRole);
                ?>
                
                <span class="badge bg-<?= $roleColor ?> user-role-badge">
                    <i class="ri-user-star-fill me-2"></i>
                    <?= htmlspecialchars($roleDisplay) ?>
                </span>
                
                <p class="user-message">
                    Vous êtes maintenant connecté à votre espace personnel AidConnect.
                    Explorez les fonctionnalités et commencez à faire la différence dès maintenant !
                </p>
            </div>
            <!-- Action Buttons -->
            <div class="action-buttons">
                <!-- View Profile Button -->
                <a href="profile.php" class="btn btn-profile btn-welcome">
                    <i class="ri-user-line me-3"></i>
                    VOIR MON PROFIL
                </a>

                <!-- Admin Access Button -->
                <?php if ($userRole === 'admin'): ?>
                    <a href="../backoffice/dashboard.php" class="btn btn-admin btn-welcome">
                        <i class="ri-dashboard-line me-3"></i>
                        ACCÉDER AU BACKOFFICE
                    </a>
                <?php endif; ?>

                <!-- Home Button -->
                <a href="index.php" class="btn btn-home btn-welcome">
                    <i class="ri-home-line me-3"></i>
                    RETOUR À L'ACCUEIL
                </a>
            </div>

            <!-- Logout Section -->
            <div class="logout-section">
                <a href="../../Controller/logout.php" class="logout-link">
                    <i class="ri-logout-box-r-line"></i>
                    Se déconnecter
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    // Add some animations
    document.addEventListener('DOMContentLoaded', function() {
        // Animate the success icon
        const icon = document.querySelector('.welcome-icon-container i');
        icon.style.transform = 'scale(0)';
        
        setTimeout(() => {
            icon.style.transition = 'transform 0.5s ease-out';
            icon.style.transform = 'scale(1)';
        }, 300);
        
        // Add hover effects to buttons
        const buttons = document.querySelectorAll('.btn-welcome');
        buttons.forEach(button => {
            button.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-3px)';
            });
            
            button.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
        
        // Auto-redirect after 10 seconds (optional)
        // setTimeout(() => {
        //     window.location.href = 'profile.php';
        // }, 10000);
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

renderTemplate('Connexion réussie | AidConnect', $content, $styles, $scripts);