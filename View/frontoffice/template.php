<?php
/**
 * Template commun pour toutes les pages du frontoffice
 * 
 * @param string $page_title Titre de la page
 * @param string $content Contenu HTML de la page
 * @param array $styles Tableau des fichiers CSS additionnels à inclure
 * @param array $scripts Tableau des fichiers JS additionnels à inclure
 */
function renderTemplate($page_title, $content, $styles = [], $scripts = []) {
    ?>
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo htmlspecialchars($page_title); ?> - AidConnect</title>
        
        <!-- Template CSS principal -->
        <link rel="stylesheet" href="assets/css/template.css">
        
        <!-- CSS additionnels -->
        <?php foreach ($styles as $style): ?>
            <link rel="stylesheet" href="<?php echo htmlspecialchars($style); ?>">
        <?php endforeach; ?>
    </head>
    <body>
        <?php include 'header.php'; ?>
        
        <main class="container-narrow">
            <?php echo $content; ?>
        </main>
        
        <!-- Scripts additionnels -->
        <?php foreach ($scripts as $script): ?>
            <script src="<?php echo htmlspecialchars($script); ?>"></script>
        <?php endforeach; ?>
    </body>
    </html>
    <?php
}
?>

