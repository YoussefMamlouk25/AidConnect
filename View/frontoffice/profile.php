<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../../Model/user.php';
require_once '../../config.php';
require_once 'layout.php';

if (!isset($_SESSION['id_user'])) {
    header('Location: login.php');
    exit;
}

$model = new UserModel();
$user  = $model->getUserById($_SESSION['id_user']);

if (!$user) {
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit;
}

/* ================= SUCCESS MESSAGE ================= */
$success = $_GET['success'] ?? '';

$profilePic = 'assets/images/default_profile.png';
if (!empty($user['profile_pic'])) {
    $profilePic = '../../' . ltrim($user['profile_pic'], '/');
}

/* ================= USER STATISTICS ================= */
$stats = $model->getUserStats($_SESSION['id_user']);

// Account age (days)
$daysActive = 0;
if (!empty($user['created_at'])) {
    $daysActive = max(
        1,
        floor((time() - strtotime($user['created_at'])) / 86400)
    );
}

// Format dates
$createdDate = !empty($user['created_at']) ? date('d/m/Y', strtotime($user['created_at'])) : '-';
$birthDate = !empty($user['date_naissance']) ? date('d/m/Y', strtotime($user['date_naissance'])) : '-';

// Activity level
$totalActivity = $stats['posts'] + $stats['offers'];
$activityLevel = 'Faible';
$activityColor = 'warning';
if ($totalActivity >= 5) {
    $activityLevel = 'Moyenne';
    $activityColor = 'info';
}
if ($totalActivity >= 15) {
    $activityLevel = 'Élevée';
    $activityColor = 'success';
}

// Role badge color
$roleColors = [
    'admin' => 'danger',
    'moderator' => 'warning',
    'user' => 'primary'
];
$roleColor = $roleColors[$user['role'] ?? 'user'] ?? 'primary';

ob_start();
?>

<!-- Profile Page Custom CSS -->
<style>
    /* Profile Container */
    .profile-main-container {
        min-height: 80vh;
        padding: 2rem 0;
        background: #f8f9fa;
    }
    
    /* Large Profile Card */
    .profile-mega-card {
        border-radius: 25px;
        border: none;
        box-shadow: 0 15px 50px rgba(0,0,0,0.1);
        overflow: hidden;
        margin-bottom: 2rem;
    }
    
    /* Profile Header - BIG */
    .profile-mega-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 4rem 2rem !important;
        color: white;
        position: relative;
        overflow: hidden;
    }
    
    .profile-mega-picture {
        width: 200px !important;
        height: 200px !important;
        border: 8px solid rgba(255,255,255,0.3);
        box-shadow: 0 15px 35px rgba(0,0,0,0.2);
        margin-bottom: 1.5rem;
    }
    
    .profile-mega-name {
        font-size: 2.8rem !important;
        font-weight: 800;
        margin-bottom: 0.5rem;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
    }
    
    .profile-mega-role {
        font-size: 1.4rem !important;
        padding: 0.8rem 2rem;
        border-radius: 50px;
        background: rgba(255,255,255,0.2);
        backdrop-filter: blur(10px);
        border: 2px solid white;
    }
    
    /* Information Cards - LARGER */
    .info-mega-card {
        background: white;
        border-radius: 20px;
        padding: 2.5rem !important;
        margin-bottom: 2rem;
        border: none;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
    }
    
    .info-mega-title {
        font-size: 1.8rem !important;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 4px solid #667eea;
    }
    
    .info-mega-item {
        background: #f8f9fa;
        border-radius: 18px;
        padding: 1.8rem !important;
        margin-bottom: 1.5rem;
        border-left: 6px solid #667eea;
        display: flex;
        align-items: center;
        transition: all 0.3s ease;
    }
    
    .info-mega-item:hover {
        transform: translateX(10px);
        background: #eef2ff;
    }
    
    .info-mega-icon {
        width: 70px;
        height: 70px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 2rem;
        flex-shrink: 0;
    }
    
    .info-mega-icon i {
        font-size: 2.2rem;
        color: white;
    }
    
    .info-mega-label {
        font-size: 1rem;
        color: #6c757d;
        margin-bottom: 0.5rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    
    .info-mega-value {
        font-size: 1.6rem !important;
        font-weight: 700;
        color: #2c3e50;
        margin: 0;
    }
    
    /* Statistics - BIGGER */
    .stats-mega-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 2rem;
        margin-top: 2rem;
    }
    
    .stat-mega-card {
        background: white;
        border-radius: 20px;
        padding: 2.5rem;
        text-align: center;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        border: none;
        transition: transform 0.3s ease;
    }
    
    .stat-mega-card:hover {
        transform: translateY(-10px);
    }
    
    .stat-mega-icon {
        width: 90px;
        height: 90px;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        font-size: 3rem;
    }
    
    .stat-mega-value {
        font-size: 3.5rem !important;
        font-weight: 800;
        margin-bottom: 0.5rem;
        line-height: 1;
    }
    
    .stat-mega-label {
        font-size: 1.3rem;
        color: #6c757d;
        font-weight: 600;
    }
    
    /* Buttons - LARGER */
    .btn-mega {
        padding: 1.2rem 3rem !important;
        font-size: 1.3rem !important;
        border-radius: 15px;
        font-weight: 700;
        min-width: 220px;
        text-align: center;
    }
    
    /* Progress Bar - BIGGER */
    .progress-mega {
        height: 20px;
        border-radius: 10px;
        background: #e9ecef;
        overflow: hidden;
        margin: 1rem 0;
    }
    
    .progress-bar-mega {
        height: 100%;
        border-radius: 10px;
        font-weight: 700;
        font-size: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
    }
    
    /* Success Alert - BIGGER */
    .alert-mega-success {
        border-radius: 20px;
        padding: 1.8rem;
        font-size: 1.2rem;
        box-shadow: 0 10px 30px rgba(40, 167, 69, 0.2);
        margin-bottom: 3rem;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .profile-mega-picture {
            width: 150px !important;
            height: 150px !important;
        }
        
        .profile-mega-name {
            font-size: 2.2rem !important;
        }
        
        .info-mega-icon {
            width: 60px;
            height: 60px;
        }
        
        .info-mega-icon i {
            font-size: 1.8rem;
        }
        
        .info-mega-value {
            font-size: 1.4rem !important;
        }
        
        .stat-mega-value {
            font-size: 2.8rem !important;
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
                        <h4 class="mb-1">Opération réussie !</h4>
                        <p class="mb-0"><?= htmlspecialchars($success) ?></p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Main Profile Card -->
        <div class="card profile-mega-card">
            <!-- Profile Header -->
            <div class="profile-mega-header text-center">
                <img src="<?= htmlspecialchars($profilePic) ?>" 
                     alt="Photo de profil" 
                     class="profile-mega-picture rounded-circle">
                
                <h1 class="profile-mega-name"><?= htmlspecialchars($user['name']) ?></h1>
                
                <span class="badge profile-mega-role">
                    <i class="ri-user-star-fill me-2"></i>
                    <?= ucfirst(htmlspecialchars($user['role'] ?? 'user')) ?>
                </span>
                
                <p class="mt-4" style="font-size: 1.2rem; opacity: 0.9;">
                    <i class="ri-calendar-2-line me-2"></i>
                    Membre depuis <?= $createdDate ?> • <?= $daysActive ?> jours
                </p>
            </div>

            <!-- Card Body -->
            <div class="card-body p-4 p-md-5">
                <!-- Personal Information Section -->
                <div class="row mb-5">
                    <div class="col-12 mb-4">
                        <h2 class="info-mega-title">
                            <i class="ri-profile-line me-3"></i>
                            INFORMATIONS PERSONNELLES
                        </h2>
                    </div>
                    
                    <!-- Left Column -->
                    <div class="col-lg-6 mb-4">
                        <div class="info-mega-item">
                            <div class="info-mega-icon">
                                <i class="ri-mail-line"></i>
                            </div>
                            <div>
                                <div class="info-mega-label">EMAIL</div>
                                <div class="info-mega-value"><?= htmlspecialchars($user['email']) ?></div>
                            </div>
                        </div>
                        
                        <div class="info-mega-item">
                            <div class="info-mega-icon">
                                <i class="ri-phone-line"></i>
                            </div>
                            <div>
                                <div class="info-mega-label">TÉLÉPHONE</div>
                                <div class="info-mega-value"><?= htmlspecialchars($user['tel'] ?? 'Non renseigné') ?></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right Column -->
                    <div class="col-lg-6 mb-4">
                        <div class="info-mega-item">
                            <div class="info-mega-icon">
                                <i class="ri-id-card-line"></i>
                            </div>
                            <div>
                                <div class="info-mega-label">CIN</div>
                                <div class="info-mega-value"><?= htmlspecialchars($user['cin'] ?? 'Non renseigné') ?></div>
                            </div>
                        </div>
                        
                        <div class="info-mega-item">
                            <div class="info-mega-icon">
                                <i class="ri-map-pin-line"></i>
                            </div>
                            <div>
                                <div class="info-mega-label">RÉGION</div>
                                <div class="info-mega-value"><?= htmlspecialchars($user['region'] ?? 'Non renseignée') ?></div>
                            </div>
                        </div>
                        
                        <div class="info-mega-item">
                            <div class="info-mega-icon">
                                <i class="ri-cake-line"></i>
                            </div>
                            <div>
                                <div class="info-mega-label">DATE DE NAISSANCE</div>
                                <div class="info-mega-value"><?= $birthDate ?></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Divider -->
                <hr class="my-5" style="border-top: 3px solid #667eea; opacity: 0.3;">

                <!-- Statistics Section -->
                <div class="row mb-5">
                    <div class="col-12 mb-4">
                        <h2 class="info-mega-title">
                            <i class="ri-bar-chart-box-line me-3"></i>
                            STATISTIQUES D'ACTIVITÉ
                        </h2>
                    </div>
                    
                    <div class="col-12">
                        <div class="stats-mega-grid">
                            <!-- Posts -->
                            <div class="stat-mega-card">
                                <div class="stat-mega-icon bg-primary bg-opacity-10 text-primary">
                                    <i class="ri-article-line"></i>
                                </div>
                                <div class="stat-mega-value text-primary"><?= $stats['posts'] ?></div>
                                <div class="stat-mega-label">Posts publiés</div>
                            </div>
                            
                            <!-- Offers -->
                            <div class="stat-mega-card">
                                <div class="stat-mega-icon bg-success bg-opacity-10 text-success">
                                    <i class="ri-price-tag-3-line"></i>
                                </div>
                                <div class="stat-mega-value text-success"><?= $stats['offers'] ?></div>
                                <div class="stat-mega-label">Offres publiées</div>
                            </div>
                            
                            <!-- Activity Level -->
                            <div class="stat-mega-card">
                                <div class="stat-mega-icon bg-<?= $activityColor ?> bg-opacity-10 text-<?= $activityColor ?>">
                                    <i class="ri-pulse-line"></i>
                                </div>
                                <div class="stat-mega-value text-<?= $activityColor ?>"><?= $activityLevel ?></div>
                                <div class="stat-mega-label">Niveau d'activité</div>
                            </div>
                            
                            <!-- Days Active -->
                            <div class="stat-mega-card">
                                <div class="stat-mega-icon bg-info bg-opacity-10 text-info">
                                    <i class="ri-calendar-check-line"></i>
                                </div>
                                <div class="stat-mega-value text-info"><?= $daysActive ?></div>
                                <div class="stat-mega-label">Jours actifs</div>
                            </div>
                        </div>
                        
                        <!-- Activity Progress -->
                        <div class="mt-5 p-4 bg-light rounded-3">
                            <h4 class="mb-3 text-center">Progression d'activité totale</h4>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Actuel : <?= $totalActivity ?> activités</span>
                                <span class="fw-bold">Objectif : 15 activités</span>
                            </div>
                            <div class="progress-mega">
                                <?php $progress = min(100, ($totalActivity / 15) * 100); ?>
                                <div class="progress-bar-mega bg-<?= $activityColor ?>" 
                                     style="width: <?= $progress ?>%">
                                    <?= round($progress) ?>%
                                </div>
                            </div>
                            <p class="text-center mt-3 text-muted">
                                <?php if ($totalActivity < 5): ?>
                                    Continuez à participer ! Publiez vos premiers posts et offres.
                                <?php elseif ($totalActivity < 15): ?>
                                    Bon travail ! Vous êtes sur la bonne voie.
                                <?php else: ?>
                                    Excellent ! Vous êtes un membre très actif de la communauté.
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Divider -->
                <hr class="my-5" style="border-top: 3px solid #667eea; opacity: 0.3;">

                <!-- Action Buttons -->
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex flex-wrap gap-4 justify-content-center">
                            <a href="profile_edit.php" class="btn btn-primary btn-mega">
                                <i class="ri-edit-box-line me-3"></i>
                                MODIFIER LE PROFIL
                            </a>
                            
                            <a href="change_password.php" class="btn btn-outline-primary btn-mega">
                                <i class="ri-lock-password-line me-3"></i>
                                CHANGER LE MOT DE PASSE
                            </a>
                            
                            <a href="../../Controller/logout.php" class="btn btn-outline-danger btn-mega">
                                <i class="ri-logout-box-r-line me-3"></i>
                                SE DÉCONNECTER
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();

// Check what CSS files you have available
$cssFiles = [
    'https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css', // Remix Icons (like in your home page)
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css'
];

// If you have a custom CSS file for profiles, add it:
// $cssFiles[] = 'assets/css/profile.css';

renderLayout('Mon Profil | AidConnect', $content, $cssFiles);