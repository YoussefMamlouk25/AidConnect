<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../../config.php';
require_once 'layout.php';

ob_start();
?>

<!-- Hero Section -->
<div class="hero-section">
    <div class="hero-content text-center">
        <h1 class="hero-title">
            <i class="ri-heart-pulse-fill me-2"></i>
            Bienvenue sur AidConnect
        </h1>
        <p class="hero-subtitle">
            Votre plateforme de connexion pour gérer les besoins, connecter les bénévoles<br>
            et apporter de l'aide à ceux qui en ont besoin
        </p>
        <div class="hero-buttons">
            <a href="showPosts.php" class="btn-hero btn-hero-primary">
                <i class="ri-file-list-3-line"></i>
                Voir les Posts
            </a>
            <a href="volunteer-view.php" class="btn-hero btn-hero-outline">
                <i class="ri-price-tag-3-line"></i>
                Voir les Offres
            </a>
        </div>
    </div>
</div>

<!-- Introduction Section -->
<div class="intro-section">
    <h2 class="intro-title">
        <i class="ri-heart-pulse-fill me-2" style="color: #667eea;"></i>
        Qu'est-ce qu'AidConnect ?
    </h2>
    <p class="intro-text">
        AidConnect est une plateforme communautaire innovante qui facilite la connexion entre les personnes 
        dans le besoin et ceux qui souhaitent apporter leur aide. Que vous ayez besoin d'assistance ou que vous 
        souhaitiez contribuer à votre communauté, AidConnect vous permet de créer des liens significatifs et 
        de faire une différence réelle dans la vie des autres.
    </p>
    
    <div class="features-grid">
        <div class="feature-item">
            <div class="feature-icon help">
                <i class="ri-hand-heart-line"></i>
            </div>
            <h3 class="feature-title">Demander de l'Aide</h3>
            <p class="feature-description">
                Partagez vos besoins et vos demandes d'assistance avec une communauté bienveillante prête à vous aider.
            </p>
        </div>
        <div class="feature-item">
            <div class="feature-icon connect">
                <i class="ri-team-line"></i>
            </div>
            <h3 class="feature-title">Se Connecter</h3>
            <p class="feature-description">
                Mettez en relation ceux qui ont besoin d'aide avec ceux qui peuvent offrir leur soutien.
            </p>
        </div>
        <div class="feature-item">
            <div class="feature-icon impact">
                <i class="ri-lightbulb-flash-line"></i>
            </div>
            <h3 class="feature-title">Faire la Différence</h3>
            <p class="feature-description">
                Chaque action compte. Ensemble, nous créons un impact positif dans notre communauté.
            </p>
        </div>
        <div class="feature-item">
            <div class="feature-icon community">
                <i class="ri-community-line"></i>
            </div>
            <h3 class="feature-title">Communauté Solidaire</h3>
            <p class="feature-description">
                Rejoignez une communauté unie par l'entraide et la solidarité, où chacun peut contribuer.
            </p>
        </div>
    </div>
</div>

<!-- How It Works Section -->
<div class="how-it-works">
    <h2 class="section-title" style="color: #333;">Comment ça fonctionne ?</h2>
    <div class="row">
        <div class="col-lg-6 mb-3">
            <div class="step-item">
                <div class="step-number">1</div>
                <div class="step-content">
                    <h4>Créez un Post ou une Offre</h4>
                    <p>
                        Si vous avez besoin d'aide, créez un post décrivant votre situation. 
                        Si vous souhaitez aider, créez une offre avec ce que vous pouvez proposer.
                    </p>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-3">
            <div class="step-item">
                <div class="step-number">2</div>
                <div class="step-content">
                    <h4>Explorez les Opportunités</h4>
                    <p>
                        Parcourez les posts et les offres disponibles pour trouver des correspondances 
                        qui correspondent à vos besoins ou à vos capacités d'aide.
                    </p>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-3">
            <div class="step-item">
                <div class="step-number">3</div>
                <div class="step-content">
                    <h4>Connectez-vous</h4>
                    <p>
                        Utilisez les informations de contact fournies pour entrer en relation 
                        directement avec les personnes concernées.
                    </p>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-3">
            <div class="step-item">
                <div class="step-number">4</div>
                <div class="step-content">
                    <h4>Marquez comme Résolu</h4>
                    <p>
                        Une fois que l'aide a été apportée, marquez le post comme résolu pour 
                        partager le succès avec la communauté.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
renderLayout('Accueil', $content, ['assets/css/index.css']);
?>

