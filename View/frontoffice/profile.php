<?php
// Activer l'affichage des erreurs pour le débogage (temporaire)
error_reporting(E_ALL);
ini_set('display_errors', 0); // Désactiver l'affichage pour éviter les problèmes
ini_set('log_errors', 1);

// Démarrer la session
if (session_status() !== PHP_SESSION_ACTIVE) {
session_start();
}

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
    session_destroy();
    header('Location: login.php?error=' . urlencode('Session expirée. Veuillez vous reconnecter.'));
    exit;
}

$success = $_GET['success'] ?? '';
$storedProfile = trim($user['profile_pic'] ?? '');
$profilePic = 'assets/images/default_profile.png';

// Générer l'URL du site pour le QR code
// Utiliser l'adresse IP locale du réseau pour que le téléphone puisse y accéder
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';

// Obtenir l'adresse IP locale du serveur
function getLocalIP() {
    // Essayer différentes méthodes pour obtenir l'IP locale
    $ip = $_SERVER['SERVER_ADDR'] ?? '';
    
    // Si c'est localhost ou 127.0.0.1, essayer de trouver l'IP du réseau
    if (empty($ip) || $ip === '127.0.0.1' || $ip === '::1') {
        // Essayer d'obtenir l'IP depuis les interfaces réseau
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            // Windows - utiliser ipconfig
            $output = @shell_exec('ipconfig 2>nul');
            if ($output) {
                // Chercher toutes les adresses IPv4
                if (preg_match_all('/IPv4[^:]*:\s*([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3})/', $output, $matches)) {
                    // Prendre la première IP qui n'est pas 127.0.0.1 et qui commence par 192.168 ou 10. ou 172.
                    foreach ($matches[1] as $matchIP) {
                        if ($matchIP !== '127.0.0.1' && 
                            (strpos($matchIP, '192.168.') === 0 || 
                             strpos($matchIP, '10.') === 0 || 
                             strpos($matchIP, '172.') === 0)) {
                            $ip = $matchIP;
                            break;
                        }
                    }
                    // Si aucune IP privée trouvée, prendre la première non-localhost
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
            // Linux/Mac
            $output = @shell_exec('hostname -I 2>/dev/null');
            if ($output) {
                $ips = explode(' ', trim($output));
                foreach ($ips as $testIP) {
                    if (filter_var($testIP, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
                        // C'est une IP privée
                        $ip = trim($testIP);
                        break;
                    }
                }
            }
        }
    }
    
    // Si toujours pas d'IP valide, essayer de récupérer depuis HTTP_HOST
    if (empty($ip) || $ip === '127.0.0.1' || $ip === '::1') {
        $host = $_SERVER['HTTP_HOST'] ?? '';
        // Extraire l'IP si HTTP_HOST contient une IP
        if (preg_match('/([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3})/', $host, $hostMatches)) {
            $ip = $hostMatches[1];
        }
    }
    
    return $ip;
}

$localIP = getLocalIP();
$port = $_SERVER['SERVER_PORT'] ?? '80';

// Construire le chemin correct depuis la racine du serveur web
// Utiliser $_SERVER['SCRIPT_NAME'] qui contient le chemin complet depuis la racine
$scriptPath = $_SERVER['SCRIPT_NAME'] ?? '';

// Obtenir le répertoire de base (sans profile.php)
// dirname() donne: /aaaaaaaaaaaaaaaaaaaaaaa/aidconnect!!/View/frontoffice
$baseDir = dirname($scriptPath);

// Construire le chemin vers index.php dans le même répertoire
$pathToIndex = $baseDir . '/index.php';

// Normaliser les séparateurs de chemin (Windows utilise \)
$pathToIndex = str_replace('\\', '/', $pathToIndex);

// S'assurer que le chemin commence par /
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
}

ob_start();
?>
<div class="row justify-content-center">
    <div class="col-lg-10 col-xl-8">
        <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <i class="ri-checkbox-circle-line me-2"></i><?= htmlspecialchars($success) ?>
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
        <div class="card iq-card shadow-sm border-0">
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
                                <div class="alert alert-warning mt-2 mb-0 p-2">
                                    <small>
                                        <i class="ri-error-warning-line me-1"></i>
                                        <strong>Débogage :</strong> Si l'URL ne fonctionne pas, vérifiez que le chemin est correct. 
                                        Le chemin attendu devrait être : <code><?= htmlspecialchars($pathToIndex) ?></code>
                                        <br>Essayez d'accéder à cette URL directement depuis votre navigateur sur l'ordinateur pour vérifier qu'elle fonctionne.
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
renderLayout('Mon Profil', $content, ['assets/css/profile.css'], ['assets/js/profile-fix.js']);
