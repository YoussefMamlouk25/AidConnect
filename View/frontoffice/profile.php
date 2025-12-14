<?php
// Activer l'affichage des erreurs pour le débogage (temporaire)
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Démarrer la session
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../../Model/user.php';
require_once '../../config.php';
require_once 'layout.php';

// Vérifier la session AVANT tout traitement
if (!isset($_SESSION['id_user']) || empty($_SESSION['id_user'])) {
    header('Location: login.php');
    exit;
}

// Inclure les dépendances
require_once '../../Model/user.php';
require_once 'layout.php';

// Récupérer l'utilisateur
$model = new UserModel();
$user  = $model->getUserById($_SESSION['id_user']);

// Vérifier si l'utilisateur existe
if ($user === false || !is_array($user) || empty($user['id_user'])) {
    // Log l'erreur
    error_log("Profile Error: getUserById failed for id_user=" . $_SESSION['id_user'] . ", result=" . var_export($user, true));
    // Nettoyer la session et rediriger
    $_SESSION = [];
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time()-3600, '/');
    }
if (!$user) {
    session_unset();
    session_destroy();
    header('Location: login.php?error=' . urlencode('Session expirée. Veuillez vous reconnecter.'));
    exit;
}

// Récupérer les statistiques de l'utilisateur depuis la base de données
$stats = $model->getUserStats($_SESSION['id_user']);

/* ================= SUCCESS MESSAGE ================= */
$success = $_GET['success'] ?? '';

$profilePic = 'assets/images/default_profile.png';
if (!empty($user['profile_pic'])) {
    $profilePic = '../../' . ltrim($user['profile_pic'], '/');
}

// Générer l'URL du site pour le QR code
// Utiliser l'adresse IP locale du réseau pour que le téléphone puisse y accéder
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';

// Fonction pour obtenir l'adresse IP locale
function getLocalIP() {
    $ip = $_SERVER['SERVER_ADDR'] ?? '';
    
    if (empty($ip) || $ip === '127.0.0.1' || $ip === '::1') {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $output = @shell_exec('ipconfig 2>nul');
            if ($output) {
                if (preg_match_all('/IPv4[^:]*:\s*([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3})/', $output, $matches)) {
                    foreach ($matches[1] as $matchIP) {
                        if ($matchIP !== '127.0.0.1' && 
                            (strpos($matchIP, '192.168.') === 0 || 
                             strpos($matchIP, '10.') === 0 || 
                             strpos($matchIP, '172.') === 0)) {
                            $ip = $matchIP;
                            break;
                        }
                    }
                    if ($ip === '127.0.0.1' || $ip === '::1') {
                        foreach ($matches[1] as $matchIP) {
                            if ($matchIP !== '127.0.0.1') {
                                $ip = $matchIP;
                                break;
                            }
                        }
                    }
                }
            }
        } else {
            $output = @shell_exec('hostname -I 2>/dev/null');
            if ($output) {
                $ips = explode(' ', trim($output));
                foreach ($ips as $testIP) {
                    if (filter_var($testIP, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
                        $ip = trim($testIP);
                        break;
                    }
                }
            }
        }
    }
    
    if (empty($ip) || $ip === '127.0.0.1' || $ip === '::1') {
        $host = $_SERVER['HTTP_HOST'] ?? '';
        if (preg_match('/([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3})/', $host, $hostMatches)) {
            $ip = $hostMatches[1];
        }
    }
    
    return $ip;
}

$localIP = getLocalIP();
$port = $_SERVER['SERVER_PORT'] ?? '80';

// Construire le chemin correct depuis la racine du serveur web
$scriptPath = $_SERVER['SCRIPT_NAME'] ?? '';
$baseDir = dirname($scriptPath);
$pathToIndex = $baseDir . '/index.php';
$pathToIndex = str_replace('\\', '/', $pathToIndex);

if (substr($pathToIndex, 0, 1) !== '/') {
    $pathToIndex = '/' . $pathToIndex;
}

// Construire l'URL complète
if ($port != '80' && $port != '443') {
    $siteUrl = $protocol . $localIP . ':' . $port . $pathToIndex;
} else {
    $siteUrl = $protocol . $localIP . $pathToIndex;
}

$qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($siteUrl);

if ($storedProfile !== '') {
    $normalized = ltrim($storedProfile, '/');
    $relativePath = '../../' . $normalized;
    $absolutePath = realpath(__DIR__ . '/../../' . $normalized);
    if ($absolutePath !== false && file_exists($absolutePath)) {
        $profilePic = $relativePath;
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
<div class="row justify-content-center">
    <div class="col-lg-10 col-xl-8">
        <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <i class="ri-checkbox-circle-line me-2"></i><?= htmlspecialchars($success) ?>

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

        <!-- Profile Header Card -->
        <div class="card iq-card shadow-sm border-0 mb-4 profile-header-card">
            <div class="card-body p-0">
                <div class="profile-cover" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); height: 200px; border-radius: 15px 15px 0 0;"></div>
                <div class="profile-info p-4">
                    <div class="d-flex flex-column flex-md-row align-items-md-center gap-4">
                        <div class="profile-avatar-wrapper">
                            <img src="<?= htmlspecialchars($profilePic) ?>" 
                                 alt="Photo de profil" 
                                 class="profile-avatar rounded-circle border border-4 border-white shadow-lg" 
                                 style="width: 150px; height: 150px; object-fit: cover; margin-top: -75px; background: #fff;">
                        </div>
                        <div class="flex-grow-1 mt-md-0 mt-3">
                            <h2 class="mb-1 fw-bold"><?= htmlspecialchars($user['name']) ?></h2>
                            <p class="text-muted mb-2">
                                <i class="ri-calendar-line me-1"></i>
                                Membre depuis <?= htmlspecialchars($user['date_creation'] ?? '-') ?>
                            </p>
                            <div class="d-flex gap-2 flex-wrap">
                                <a href="profile_edit.php" class="btn btn-primary btn-sm">
                                    <i class="ri-edit-line me-1"></i>Modifier le profil
                                </a>
                                <a href="change_password.php" class="btn btn-outline-primary btn-sm">
                                    <i class="ri-lock-password-line me-1"></i>Changer le mot de passe
                                </a>
                                <a href="../../Controller/logout.php" class="btn btn-outline-danger btn-sm">
                                    <i class="ri-logout-box-line me-1"></i>Se déconnecter
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profile Details Card -->
        <div class="card iq-card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-0 pb-0">
                <h5 class="mb-0 fw-bold">
                    <i class="ri-user-line me-2 text-primary"></i>Informations personnelles
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="profile-info-item">
                            <div class="d-flex align-items-center mb-2">
                                <i class="ri-mail-line text-primary me-2 fs-5"></i>
                                <span class="text-muted small">Email</span>
                            </div>
                            <p class="mb-0 fw-semibold"><?= htmlspecialchars($user['email']) ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="profile-info-item">
                            <div class="d-flex align-items-center mb-2">
                                <i class="ri-phone-line text-primary me-2 fs-5"></i>
                                <span class="text-muted small">Téléphone</span>
                            </div>
                            <p class="mb-0 fw-semibold"><?= htmlspecialchars($user['tel'] ?? '-') ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="profile-info-item">
                            <div class="d-flex align-items-center mb-2">
                                <i class="ri-id-card-line text-primary me-2 fs-5"></i>
                                <span class="text-muted small">CIN</span>
                            </div>
                            <p class="mb-0 fw-semibold"><?= htmlspecialchars($user['cin'] ?? '-') ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="profile-info-item">
                            <div class="d-flex align-items-center mb-2">
                                <i class="ri-map-pin-line text-primary me-2 fs-5"></i>
                                <span class="text-muted small">Région</span>
                            </div>
                            <p class="mb-0 fw-semibold"><?= htmlspecialchars($user['region'] ?? '-') ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="profile-info-item">
                            <div class="d-flex align-items-center mb-2">
                                <i class="ri-calendar-2-line text-primary me-2 fs-5"></i>
                                <span class="text-muted small">Date de naissance</span>
                            </div>
                            <p class="mb-0 fw-semibold"><?= htmlspecialchars($user['date_naissance'] ?? '-') ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="profile-info-item">
                            <div class="d-flex align-items-center mb-2">
                                <i class="ri-shield-user-line text-primary me-2 fs-5"></i>
                                <span class="text-muted small">Rôle</span>
                            </div>
                            <p class="mb-0">
                                <span class="badge bg-primary"><?= htmlspecialchars($user['role'] ?? 'volunteer') ?></span>
                            </p>
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
            </div>
        </div>

        <!-- Statistiques d'activité Card -->
        <div class="card iq-card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-0 pb-0">
                <h5 class="mb-0 fw-bold">
                    <i class="ri-bar-chart-line me-2 text-primary"></i>Statistiques d'activité
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-4 mb-4">
                    <!-- Posts publics -->
                    <div class="col-md-3 col-sm-6">
                        <div class="stats-item text-center p-3 rounded-3 border h-100 bg-light">
                            <div class="stats-number display-6 fw-bold text-primary mb-2"><?= htmlspecialchars($stats['public_posts']) ?></div>
                            <p class="text-muted mb-0 small">Posts publics</p>
                        </div>
                    </div>
                    
                    <!-- Offres publiques -->
                    <div class="col-md-3 col-sm-6">
                        <div class="stats-item text-center p-3 rounded-3 border h-100 bg-light">
                            <div class="stats-number display-6 fw-bold text-success mb-2"><?= htmlspecialchars($stats['public_offers']) ?></div>
                            <p class="text-muted mb-0 small">Offres publiques</p>
                        </div>
                    </div>
                    
                    <!-- Niveau d'activité -->
                    <div class="col-md-3 col-sm-6">
                        <div class="stats-item text-center p-3 rounded-3 border h-100 bg-light">
                            <div class="stats-level mb-2">
                                <?php 
                                $badgeClass = 'bg-warning';
                                if ($stats['activity_level'] === 'Moyen') $badgeClass = 'bg-info';
                                if ($stats['activity_level'] === 'Élevé') $badgeClass = 'bg-success';
                                ?>
                                <span class="badge <?= $badgeClass ?>">
                                    <?= htmlspecialchars($stats['activity_level']) ?>
                                </span>
                            </div>
                            <p class="text-muted mb-0 small">Niveau d'activité</p>
                        </div>
                    </div>
                    
                    <!-- Jours actifs -->
                    <div class="col-md-3 col-sm-6">
                        <div class="stats-item text-center p-3 rounded-3 border h-100 bg-light">
                            <div class="stats-number display-6 fw-bold text-info mb-2"><?= htmlspecialchars($stats['active_days']) ?></div>
                            <p class="text-muted mb-0 small">Jours actifs</p>
                        </div>
                    </div>
                </div>
                
                <!-- Progression d'activité totale -->
                <div class="activity-progress-section p-4 rounded-3 border bg-light">
                    <h6 class="fw-bold mb-3 text-center">
                        <i class="ri-progress-4-line me-2 text-primary"></i>Progression d'activité totale
                    </h6>
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="current-activity">
                            <span class="text-muted small">Actuel : </span>
                            <span class="fw-bold text-primary"><?= htmlspecialchars($stats['total_activities']) ?> activités</span>
                        </div>
                        <div class="activity-goal">
                            <span class="text-muted small">Objectif : </span>
                            <span class="fw-bold text-success"><?= htmlspecialchars($stats['activity_goal']) ?> activités</span>
                        </div>
                    </div>
                    
                    <!-- Barre de progression -->
                    <div class="progress mb-3" style="height: 20px; border-radius: 10px;">
                        <div class="progress-bar bg-primary" role="progressbar" 
                             style="width: <?= $stats['activity_progress'] ?>%; border-radius: 10px;" 
                             aria-valuenow="<?= $stats['activity_progress'] ?>" 
                             aria-valuemin="0" 
                             aria-valuemax="100">
                            <?= $stats['activity_progress'] ?>%
                        </div>
                    </div>
                    
                    <!-- Message d'encouragement -->
                    <div class="text-center mt-3">
                        <p class="text-muted mb-0">
                            <i class="ri-megaphone-line me-1 text-warning"></i>
                            <?php if ($stats['activity_progress'] < 30): ?>
                                <strong>Commencez votre parcours !</strong> Publiez votre premier post ou offre.
                            <?php elseif ($stats['activity_progress'] < 70): ?>
                                <strong>Continuez comme ça !</strong> Vous êtes sur la bonne voie.
                            <?php else: ?>
                                <strong>Excellent travail !</strong> Vous êtes très actif sur la plateforme.
                            <?php endif; ?>
                        </p>
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
            </div>
        </div>

        <!-- QR Code Card -->
        <div class="card iq-card shadow-sm border-0 mt-4">
            <div class="card-header bg-white border-0 pb-0">
                <h5 class="mb-0 fw-bold">
                    <i class="ri-qr-code-line me-2 text-primary"></i>Code QR du site
                </h5>
            </div>
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-6 text-center mb-3 mb-md-0">
                        <div class="qr-code-wrapper d-inline-block p-3 bg-white rounded shadow-sm">
                            <img src="<?= htmlspecialchars($qrCodeUrl) ?>" 
                                 alt="Code QR AidConnect" 
                                 class="qr-code-image"
                                 style="max-width: 250px; width: 100%; height: auto;">
                        </div>
                        <p class="text-muted small mt-3 mb-0">
                            <i class="ri-information-line me-1"></i>
                            Scannez avec votre téléphone pour accéder au site
                        </p>
                    </div>
                    <div class="col-md-6">
                        <div class="qr-code-info">
                            <h6 class="fw-semibold mb-3">
                                <i class="ri-smartphone-line me-2 text-primary"></i>Comment utiliser le code QR ?
                            </h6>
                            <ol class="list-unstyled mb-0">
                                <li class="mb-2">
                                    <i class="ri-checkbox-circle-line text-success me-2"></i>
                                    Ouvrez l'application caméra de votre téléphone
                                </li>
                                <li class="mb-2">
                                    <i class="ri-checkbox-circle-line text-success me-2"></i>
                                    Pointez la caméra vers le code QR ci-dessus
                                </li>
                                <li class="mb-2">
                                    <i class="ri-checkbox-circle-line text-success me-2"></i>
                                    Appuyez sur la notification qui apparaît
                                </li>
                                <li class="mb-0">
                                    <i class="ri-checkbox-circle-line text-success me-2"></i>
                                    Le site s'ouvrira automatiquement dans votre navigateur
                                </li>
                            </ol>
                            <div class="mt-3 p-3 bg-light rounded">
                                <small class="text-muted d-block mb-2">
                                    <strong>URL du site :</strong>
                                </small>
                                <code class="text-primary small d-block mb-2" style="word-break: break-all;"><?= htmlspecialchars($siteUrl) ?></code>
                                <div class="d-flex gap-2 flex-wrap">
                                    <button class="btn btn-sm btn-outline-primary" 
                                            onclick="navigator.clipboard.writeText('<?= htmlspecialchars($siteUrl) ?>').then(() => alert('URL copiée !'))">
                                        <i class="ri-file-copy-line me-1"></i>Copier l'URL
                                    </button>
                                    <a href="<?= htmlspecialchars($siteUrl) ?>" target="_blank" class="btn btn-sm btn-primary">
                                        <i class="ri-external-link-line me-1"></i>Tester l'URL
                                    </a>
                                </div>
                                <div class="alert alert-info mt-3 mb-0 p-2">
                                    <small>
                                        <i class="ri-information-line me-1"></i>
                                        <strong>Important :</strong> Assurez-vous que votre téléphone et votre ordinateur sont sur le même réseau Wi-Fi. 
                                        Si l'URL ne fonctionne pas, vérifiez l'adresse IP de votre serveur (ex: 192.168.1.XXX).
                                    </small>
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

<style>
.stats-item {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    background: linear-gradient(135deg, rgba(255,255,255,0.9) 0%, rgba(248,249,250,0.9) 100%);
}

.stats-item:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1) !important;
}

.stats-number {
    font-size: 2.5rem;
    line-height: 1;
}

.activity-progress-section {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border: 1px solid #dee2e6;
}

.progress {
    background-color: #e9ecef;
    border-radius: 10px;
    overflow: hidden;
}

.progress-bar {
    border-radius: 10px;
    transition: width 0.6s ease;
    font-size: 0.8rem;
    font-weight: bold;
    display: flex;
    align-items: center;
    justify-content: center;
}

.badge {
    font-size: 0.9rem;
    padding: 0.4rem 0.8rem;
}

@media (max-width: 768px) {
    .stats-number {
        font-size: 2rem;
    }
    
    .stats-item {
        margin-bottom: 1rem;
    }
}
</style>
<?php
$content = ob_get_clean();
renderLayout('Mon Profil', $content, ['assets/css/profile.css'], ['assets/js/profile-fix.js']);
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
