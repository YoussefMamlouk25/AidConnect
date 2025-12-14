<?php
/**
 * Custom Event Template for AidConnect
 * Clean, modern template without SocialV-Lite dependencies
 * 
 * @param string $page_title Titre de la page
 * @param string $content Contenu HTML de la page
 * @param array $additional_styles Tableau des fichiers CSS additionnels
 * @param array $additional_scripts Tableau des fichiers JS additionnels
 */
function renderEventTemplate($page_title, $content, $additional_styles = [], $additional_scripts = []) {
    ?>
    <!doctype html>
    <html lang="fr">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title><?php echo htmlspecialchars($page_title); ?> - AidConnect</title>
        
        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        
        <!-- RemixIcon -->
        <link rel="stylesheet" href="assets/vendor/remixicon/fonts/remixicon.css" />
        
        <!-- Custom CSS -->
        <link rel="stylesheet" href="assets/css/header.css" />
        <link rel="stylesheet" href="assets/css/style.css" />
        <link rel="stylesheet" href="assets/css/footer.css" />
        <link rel="stylesheet" href="assets/css/dark-mode.css" />
        <link rel="stylesheet" href="assets/css/event.css" />
        
        <!-- CSS additionnels -->
        <?php foreach ($additional_styles as $style): ?>
            <link rel="stylesheet" href="<?php echo htmlspecialchars($style); ?>" />
        <?php endforeach; ?>
        
        <!-- Custom Event Template Styles -->
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }
            
            body {
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
                background-image: url('assets/images/small/background.jpg');
                background-size: cover;
                background-position: center;
                background-repeat: no-repeat;
                background-attachment: fixed;
                min-height: 100vh;
                color: #333;
                position: relative;
            }
            
            body::before {
                content: '';
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.3);
                z-index: 0;
                pointer-events: none;
            }
            
            body.dark-mode {
                background-image: url('assets/images/small/background.jpg');
                background-size: cover;
                background-position: center;
                background-repeat: no-repeat;
                background-attachment: fixed;
                color: #e4e6eb;
            }
            
            body.dark-mode::before {
                background: rgba(0, 0, 0, 0.6);
            }
            
            .event-wrapper {
                min-height: calc(100vh - 200px);
                padding: 2rem 0;
                position: relative;
                z-index: 1;
            }
            
            .event-container {
                max-width: 1200px;
                margin: 0 auto;
                padding: 0 15px;
                position: relative;
                z-index: 1;
            }
            
            .event-card {
                background: rgba(255, 255, 255, 0.95);
                border-radius: 16px;
                box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
                backdrop-filter: blur(10px);
                border: 1px solid rgba(255, 255, 255, 0.3);
                margin-bottom: 2rem;
                overflow: hidden;
                transition: transform 0.3s ease, box-shadow 0.3s ease;
            }
            
            body.dark-mode .event-card {
                background: rgba(36, 37, 38, 0.95);
                border-color: rgba(255, 255, 255, 0.1);
            }
            
            .event-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
            }
            
            .event-card-header {
                padding: 1.5rem;
                border-bottom: 1px solid rgba(0, 0, 0, 0.1);
            }
            
            body.dark-mode .event-card-header {
                border-bottom-color: rgba(255, 255, 255, 0.1);
            }
            
            .event-card-body {
                padding: 1.5rem;
            }
            
            /* Nested divs styling for detail page - all nested divs */
            .event-wrapper .event-container .row,
            .event-wrapper .event-container .row > div,
            .event-wrapper .event-container .row > div > div,
            .event-wrapper .event-container .row > div > div > div,
            .event-wrapper .event-container .row > div > div > div > div,
            .event-wrapper .event-container .row > div > div > div > div > div {
                background-color: white !important;
                border-radius: 12px;
            }
            
            body.dark-mode .event-wrapper .event-container .row,
            body.dark-mode .event-wrapper .event-container .row > div,
            body.dark-mode .event-wrapper .event-container .row > div > div,
            body.dark-mode .event-wrapper .event-container .row > div > div > div,
            body.dark-mode .event-wrapper .event-container .row > div > div > div > div,
            body.dark-mode .event-wrapper .event-container .row > div > div > div > div > div {
                background-color: #2d2d2d !important;
                border-radius: 12px;
            }
            
            /* Specific styling for iq-card and nested elements */
            .iq-card {
                background-color: white !important;
                border-radius: 16px;
                box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            }
            
            body.dark-mode .iq-card {
                background-color: #2d2d2d !important;
            }
            
            .iq-card-header,
            .iq-card-body {
                background-color: white !important;
                border-radius: 16px;
            }
            
            body.dark-mode .iq-card-header,
            body.dark-mode .iq-card-body {
                background-color: #2d2d2d !important;
            }
            
            .info-box {
                background-color: white;
                padding: 1rem;
                border-radius: 12px;
                border: 1px solid #e4e6eb;
            }
            
            body.dark-mode .info-box {
                background-color: #2d2d2d;
                border-color: #3a3b3c;
            }
            
            .btn {
                border-radius: 8px;
                padding: 0.5rem 1.5rem;
                font-weight: 500;
                transition: all 0.3s ease;
            }
            
            .btn-primary {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                border: none;
            }
            
            .btn-primary:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
            }
            
            .alert {
                border-radius: 12px;
                border: none;
                padding: 1rem 1.5rem;
            }
            
            .form-control, .form-select {
                border-radius: 8px;
                border: 2px solid #e4e6eb;
                padding: 0.75rem 1rem;
                transition: all 0.3s ease;
            }
            
            .form-control:focus, .form-select:focus {
                border-color: #667eea;
                box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            }
            
            @media (max-width: 768px) {
                .event-wrapper {
                    padding: 1rem 0;
                }
                
                .event-container {
                    padding: 0 10px;
                }
                
                .event-card {
                    border-radius: 12px;
                    margin-bottom: 1rem;
                }
            }
        </style>
    </head>
    <body>
        <?php include 'header.php'; ?>
        
        <div class="event-wrapper">
            <div class="event-container">
                <?php echo $content; ?>
            </div>
        </div>
        
        <?php include 'footer.php'; ?>
        
        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        
        <!-- Dark Mode Toggle -->
        <script src="assets/js/dark-mode.js"></script>
        
        <!-- Scripts additionnels -->
        <?php foreach ($additional_scripts as $script): ?>
            <script src="<?php echo htmlspecialchars($script); ?>"></script>
        <?php endforeach; ?>
    </body>
    </html>
    <?php
}
?>

