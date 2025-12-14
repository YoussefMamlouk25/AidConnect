<?php
/**
 * Layout commun basé sur SocialV-Lite pour tout le frontoffice
 * 
 * @param string $page_title Titre de la page
 * @param string $content Contenu HTML de la page
 * @param array $additional_styles Tableau des fichiers CSS additionnels
 * @param array $additional_scripts Tableau des fichiers JS additionnels
 */
function renderLayout($page_title, $content, $additional_styles = [], $additional_scripts = []) {
    ?>
    <!doctype html>
    <html lang="fr">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title><?php echo htmlspecialchars($page_title); ?> - AidConnect</title>
        
        <!-- SocialV-Lite CSS -->
        <link rel="stylesheet" href="assets/css/libs.min.css" />
        <link rel="stylesheet" href="assets/css/socialv.css?v=4.0.0" />
        <link rel="stylesheet" href="assets/vendor/@fortawesome/fontawesome-free/css/all.min.css" />
        <link rel="stylesheet" href="assets/vendor/remixicon/fonts/remixicon.css" />
        <link rel="stylesheet" href="assets/css/custom.css" />
        <link rel="stylesheet" href="assets/css/header.css" />
        <link rel="stylesheet" href="assets/css/style.css" />
        <link rel="stylesheet" href="assets/css/footer.css" />
        <link rel="stylesheet" href="assets/css/dark-mode.css" />
        
        <!-- CSS additionnels -->
        <?php foreach ($additional_styles as $style): ?>
            <link rel="stylesheet" href="<?php echo htmlspecialchars($style); ?>" />
        <?php endforeach; ?>
        
        <!-- Background Image Style -->
        <style>
            body {
                background-image: url('assets/images/small/background.jpg');
                background-size: cover;
                background-position: center center;
                background-repeat: no-repeat;
                background-attachment: fixed;
                min-height: 100vh;
                display: flex;
                flex-direction: column;
                transition: background-color 0.3s ease, color 0.3s ease;
            }
            
            body.dark-mode {
                background-image: linear-gradient(rgba(24, 25, 26, 0.85), rgba(24, 25, 26, 0.85)), url('assets/images/small/background.jpg');
            }
            
            .wrapper {
                background-color: transparent;
                min-height: calc(100vh - 200px);
                padding-bottom: 20px;
                flex: 1;
            }
            
            @media (max-width: 768px) {
                body {
                    background-attachment: scroll;
                }
            }
        </style>
    </head>
    <body>
        <?php include 'header.php'; ?>
        
        <div class="wrapper">
            <div class="container-fluid">
                
                <?php echo $content; ?>
            </div>
        </div>
        
        <?php include 'footer.php'; ?>
        
        <!-- SocialV-Lite JS -->
        <script src="assets/js/libs.min.js"></script>
        <script src="assets/js/app.js"></script>
        
        <!-- Dark Mode Toggle -->
        <script src="assets/js/dark-mode.js"></script>
        
        <!-- Validation JavaScript -->
        <script src="assets/js/form-validation.js"></script>
        
        <!-- Scripts additionnels -->
        <?php foreach ($additional_scripts as $script): ?>
            <script src="<?php echo htmlspecialchars($script); ?>"></script>
        <?php endforeach; ?>
    </body>
    </html>
    <?php
}
?>

