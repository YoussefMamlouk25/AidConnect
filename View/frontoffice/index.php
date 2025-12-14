<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../../config.php';
require_once 'layout.php';
require_once '../../Model/Event.php';

// Récupérer les événements à venir (limite à 3)
$db = Config::getConnexion();
$eventModel = new Event($db);

// Requête pour récupérer les événements à venir, triés par date croissante
$query = "SELECT * FROM events 
          WHERE statut = 'actif' 
          AND date_event > NOW()
          ORDER BY date_event ASC 
          LIMIT 3";
$stmt = $db->prepare($query);
$stmt->execute();
$upcomingEvents = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Ajouter le nombre d'inscriptions et places restantes pour chaque événement
foreach($upcomingEvents as &$event) {
    $eventModel->setIdEvent($event['id_event']);
    $event['inscriptions_count'] = $eventModel->countInscriptions();
    $event['places_restantes'] = $event['nombre_places'] - $event['inscriptions_count'];
}

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
            <a href="event.php?controller=event&action=liste" class="btn-hero btn-hero-outline">
                <i class="ri-calendar-event-line"></i>
                Voir les Événements
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

<!-- Events Section -->
<?php if(!empty($upcomingEvents)): ?>
<div class="events-section" style="background: #f8f9fa; padding: 60px 0; margin-top: 60px;">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title" style="color: #333; margin-bottom: 15px;">
                <i class="ri-calendar-event-fill me-2" style="color: #667eea;"></i>
                Événements à Venir
            </h2>
            <p class="text-muted">Découvrez les prochains événements organisés par notre communauté</p>
        </div>
        
        <div class="row">
            <?php foreach($upcomingEvents as $event): 
                $dateEvent = new DateTime($event['date_event']);
                $placesClass = 'text-success';
                if($event['places_restantes'] <= 0) {
                    $placesClass = 'text-danger';
                } elseif($event['places_restantes'] <= 10) {
                    $placesClass = 'text-warning';
                }
                
                // Chemin de l'image
                // Always use public/uploads/events/ directory
                $image_path = '';
                $image_full_path = '';
                if(!empty($event['image'])) {
                    // Extract filename from any path format
                    $imagePath = str_replace('\\', '/', $event['image']);
                    $filename = basename($imagePath); // Get just the filename
                    
                    // Always use public/uploads/events/ path
                    $image_path = '../public/uploads/events/' . $filename;
                    $image_full_path = __DIR__ . '/../../public/uploads/events/' . $filename;
                }
            ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100 shadow-sm" style="border: none; border-radius: 15px; overflow: hidden; transition: transform 0.3s;">
                        <div style="height: 200px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); position: relative; overflow: hidden;">
                            <!-- Placeholder (background layer) -->
                            <div class="d-flex align-items-center justify-content-center h-100 text-white" style="font-size: 4rem; position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 1; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                <i class="ri-calendar-event-line"></i>
                            </div>
                            <!-- Event Image (foreground layer) -->
                            <?php if(!empty($image_path)): ?>
                                <img src="<?php echo htmlspecialchars($image_path); ?>" 
                                     alt="<?php echo htmlspecialchars($event['titre']); ?>"
                                     style="width: 100%; height: 100%; object-fit: cover; position: absolute; top: 0; left: 0; z-index: 2;"
                                     onerror="this.style.display='none';">
                            <?php endif; ?>
                            <div class="position-absolute top-0 end-0 m-2">
                                <span class="badge bg-light text-dark">
                                    <?php echo htmlspecialchars($event['type_event']); ?>
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title" style="color: #333; font-weight: 600; margin-bottom: 15px;">
                                <?php echo htmlspecialchars($event['titre']); ?>
                            </h5>
                            <p class="card-text text-muted" style="font-size: 14px; margin-bottom: 20px;">
                                <?php 
                                $description = htmlspecialchars($event['description']);
                                echo strlen($description) > 100 ? substr($description, 0, 100) . '...' : $description;
                                ?>
                            </p>
                            <div class="event-details mb-3" style="font-size: 13px;">
                                <div class="mb-2">
                                    <i class="ri-calendar-line me-2" style="color: #667eea;"></i>
                                    <strong>Date:</strong> <?php echo $dateEvent->format('d/m/Y à H:i'); ?>
                                </div>
                                <div class="mb-2">
                                    <i class="ri-map-pin-line me-2" style="color: #667eea;"></i>
                                    <strong>Lieu:</strong> <?php echo htmlspecialchars($event['lieu']); ?>
                                </div>
                                <div>
                                    <i class="ri-group-line me-2" style="color: #667eea;"></i>
                                    <strong>Places:</strong> 
                                    <span class="<?php echo $placesClass; ?>">
                                        <?php if($event['places_restantes'] > 0): ?>
                                            <?php echo $event['places_restantes']; ?> restantes sur <?php echo $event['nombre_places']; ?>
                                        <?php else: ?>
                                            Complet
                                        <?php endif; ?>
                                    </span>
                                </div>
                            </div>
                            <a href="event.php?controller=event&action=detail&id=<?php echo $event['id_event']; ?>" 
                               class="btn btn-primary w-100" 
                               style="background: #667eea; border: none; border-radius: 8px; padding: 10px;">
                                <i class="ri-eye-line me-1"></i>
                                Voir les détails
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-4">
            <a href="event.php?controller=event&action=liste" 
               class="btn btn-outline-primary" 
               style="padding: 12px 30px; border-radius: 8px; border-color: #667eea; color: #667eea;">
                <i class="ri-calendar-event-line me-2"></i>
                Voir tous les événements
            </a>
        </div>
    </div>
</div>
<?php endif; ?>

<?php
$content = ob_get_clean();
renderLayout('Accueil', $content, ['assets/css/index.css']);
?>

