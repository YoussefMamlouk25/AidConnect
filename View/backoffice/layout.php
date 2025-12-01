<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!isset($_SESSION['id_user']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: ../frontoffice/login.php?error=' . urlencode('Accès administrateur requis.'));
    exit;
}

/**
 * Layout commun basé sur SocialV-Lite pour le backoffice
 * 
 * @param string $page_title Titre de la page
 * @param string $content Contenu HTML de la page
 * @param array $additional_styles Tableau des fichiers CSS additionnels
 * @param array $additional_scripts Tableau des fichiers JS additionnels
 */
function renderBackofficeLayout($page_title, $content, $additional_styles = [], $additional_scripts = []) {
    // Détecter si c'est une requête AJAX
    $isAjax = (isset($_GET['ajax']) && $_GET['ajax'] == '1') || 
              (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
    
    // Si c'est AJAX, retourner seulement le contenu
    if ($isAjax) {
        echo $content;
        return;
    }
    
    // Chemin relatif vers les assets de socialv-lite (depuis backoffice vers frontoffice/assets)
    $assets_path = '../frontoffice/assets';
    ?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?php echo htmlspecialchars($page_title); ?> - AidConnect Backoffice</title>
    
    <!-- SocialV-Lite CSS -->
    <link rel="stylesheet" href="<?php echo $assets_path; ?>/css/libs.min.css" />
    <link rel="stylesheet" href="<?php echo $assets_path; ?>/css/socialv.css?v=4.0.0" />
    <link rel="stylesheet" href="<?php echo $assets_path; ?>/vendor/@fortawesome/fontawesome-free/css/all.min.css" />
    <link rel="stylesheet" href="<?php echo $assets_path; ?>/vendor/remixicon/fonts/remixicon.css" />
    <link rel="stylesheet" href="<?php echo $assets_path; ?>/vendor/vanillajs-datepicker/dist/css/datepicker.min.css" />
    <link rel="stylesheet" href="<?php echo $assets_path; ?>/vendor/font-awesome-line-awesome/css/all.min.css" />
    <link rel="stylesheet" href="<?php echo $assets_path; ?>/vendor/line-awesome/dist/line-awesome/css/line-awesome.min.css" />
    <!-- CSS backoffice -->
    <link rel="stylesheet" href="dashboard.css" />
    
    <!-- CSS additionnels -->
    <?php foreach ($additional_styles as $style): ?>
        <link rel="stylesheet" href="<?php echo htmlspecialchars($style); ?>" />
    <?php endforeach; ?>
</head>
<body>
    <!-- loader Start -->
    <div id="loading">
        <div id="loading-center"></div>
    </div>
    <!-- loader END -->
    
    <!-- Wrapper Start -->
    <div class="wrapper">
        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>
        
        <!-- Top Navbar -->
        <?php include 'navbar.php'; ?>
        
        <!-- Content Page -->
        <div id="content-page" class="content-page">
            <div class="container-fluid">
                <div id="content-area">
                    <?php echo $content; ?>
                </div>
            </div>
        </div>
    </div>
    <!-- Wrapper End-->
    
    <!-- Footer -->
    <footer class="iq-footer bg-white">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-6">
                    <ul class="list-inline mb-0">
                        <li class="list-inline-item"><a href="#">Privacy Policy</a></li>
                        <li class="list-inline-item"><a href="#">Terms of Use</a></li>
                    </ul>
                </div>
                <div class="col-lg-6 d-flex justify-content-end">
                    Copyright <?php echo date('Y'); ?> <a href="#">AidConnect</a> All Rights Reserved.
                </div>
            </div>
        </div>
    </footer>
    
    <!-- SocialV-Lite JS -->
    <script src="<?php echo $assets_path; ?>/js/libs.min.js"></script>
    <script src="<?php echo $assets_path; ?>/js/app.js"></script>
    
    <!-- Dashboard JS -->
    <script src="dashboard.js"></script>
    
    <!-- Scripts additionnels -->
    <?php foreach ($additional_scripts as $script): ?>
        <script src="<?php echo htmlspecialchars($script); ?>"></script>
    <?php endforeach; ?>
</body>
</html>
<?php
}
?>

