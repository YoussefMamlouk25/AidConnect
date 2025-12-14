<?php
/**
 * Contenu pour la liste des événements
 * Utilisé avec layout.php qui inclut le header
 */
?>
<div class="row">
    <div class="col-12">
        <div class="iq-card">
            <div class="iq-card-header d-flex justify-content-between">
                <div class="header-title">
                    <h4 class="card-title">🎯 Événements Caritatifs Disponibles</h4>
                    <p class="text-muted">Trouvez un événement et inscrivez-vous pour participer</p>
                </div>
            </div>
            <div class="iq-card-body">
                <!-- Barre de recherche -->
                <form method="GET" action="event.php" class="search-bar mb-4">
                    <input type="hidden" name="controller" value="event">
                    <input type="hidden" name="action" value="search">
                    <div class="input-group">
                        <input type="text" name="keyword" class="form-control" placeholder="Rechercher un événement..." 
                               value="<?php echo htmlspecialchars($_GET['keyword'] ?? ''); ?>">
                        <button type="submit" class="btn btn-primary">🔍 Rechercher</button>
                    </div>
                </form>
                
                <!-- Messages -->
                <?php if(isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        ✅ <?php echo htmlspecialchars($_SESSION['success']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>
                
                <?php if(isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        ❌ <?php echo htmlspecialchars($_SESSION['error']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>
                
                <!-- Filtres -->
                <div class="filter-section mb-4">
                    <h5 class="mb-3">🔎 Filtrer les événements</h5>
                    <form method="GET" action="event.php">
                        <input type="hidden" name="controller" value="event">
                        <input type="hidden" name="action" value="search">
                        
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="type" class="form-label">Type d'événement</label>
                                <select name="type" id="type" class="form-select">
                                    <option value="">Tous les types</option>
                                    <option value="collecte">Collecte de nourriture</option>
                                    <option value="distribution">Distribution de repas</option>
                                    <option value="soutien">Soutien médical</option>
                                    <option value="logement">Aide au logement</option>
                                    <option value="autre">Autre</option>
                                </select>
                            </div>
                            
                            <div class="col-md-3">
                                <label for="date_min" class="form-label">Date minimum</label>
                                <input type="date" name="date_min" id="date_min" class="form-control">
                            </div>
                            
                            <div class="col-md-3">
                                <label for="lieu" class="form-label">Lieu</label>
                                <input type="text" name="lieu" id="lieu" class="form-control" placeholder="Ville...">
                            </div>
                            
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <button type="submit" class="btn btn-primary w-100">
                                    Appliquer les filtres
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                
                <!-- Liste des événements (style posts) -->
                <?php if(!empty($events) && count($events) > 0): ?>
                    <div class="row justify-content-center">
                        <div class="col-lg-8">
                            <?php foreach($events as $event): 
                                $inscriptions_count = $event['inscriptions_count'] ?? 0;
                                $places_restantes = $event['nombre_places'] - $inscriptions_count;
                                
                                $placesClass = 'places-available';
                                if($places_restantes <= 0) {
                                    $placesClass = 'places-full';
                                } elseif($places_restantes <= 10) {
                                    $placesClass = 'places-limited';
                                }
                                
                                // Chemin de l'image
                                // Images are stored in DB as 'public/uploads/events/filename.jpg' (new) or 'uploads/events/filename.jpg' (old)
                                // Always use public/uploads/events/ directory
                                $image_path = '';
                                if(!empty($event['image'])) {
                                    // Extract filename from any path format
                                    $imagePath = str_replace('\\', '/', $event['image']);
                                    $filename = basename($imagePath); // Get just the filename
                                    
                                    // Always use public/uploads/events/ path
                                    $image_path = '../public/uploads/events/' . $filename;
                                }
                                
                                // Formater la date
                                $dateEvent = new DateTime($event['date_event']);
                                $now = new DateTime();
                                $diff = $now->diff($dateEvent);
                                
                                if($dateEvent > $now) {
                                    if($diff->days == 0) {
                                        $dateFormatted = "Aujourd'hui à " . $dateEvent->format('H:i');
                                    } elseif($diff->days == 1) {
                                        $dateFormatted = "Demain à " . $dateEvent->format('H:i');
                                    } elseif($diff->days < 7) {
                                        $dateFormatted = "Dans " . $diff->days . " jours";
                                    } else {
                                        $dateFormatted = $dateEvent->format('d/m/Y à H:i');
                                    }
                                } else {
                                    $dateFormatted = $dateEvent->format('d/m/Y à H:i');
                                }
                            ?>
                                <div class="card mb-3 event-item" data-event-id="<?php echo $event['id_event']; ?>">
                                    <div class="card-body">
                                        <!-- Header avec icône événement -->
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="avatar avatar-md bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px; font-weight: 600; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                                <i class="ri-calendar-event-line" style="font-size: 24px;"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">Événement Caritatif</h6>
                                                <small class="text-muted">
                                                    <i class="ri-map-pin-line me-1"></i><?php echo htmlspecialchars($event['lieu']); ?>
                                                    <i class="ri-time-line ms-2 me-1"></i><?php echo $dateFormatted; ?>
                                                </small>
                                            </div>
                                        </div>

                                        <h5 class="mb-2"><?php echo htmlspecialchars($event['titre']); ?></h5>

                                        <?php if(!empty($event['description'])): ?>
                                            <p class="mb-3"><?php echo nl2br(htmlspecialchars($event['description'])); ?></p>
                                        <?php endif; ?>

                                        <!-- Image de l'événement -->
                                        <div class="mb-3" style="position: relative; width: 100%; min-height: 200px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 8px; overflow: hidden;">
                                            <!-- Placeholder (background layer) -->
                                            <div class="d-flex align-items-center justify-content-center" style="width: 100%; min-height: 200px; color: white; font-size: 3rem; position: absolute; top: 0; left: 0; z-index: 1; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 8px;">
                                                <i class="ri-calendar-event-line"></i>
                                            </div>
                                            <!-- Event Image (foreground layer) -->
                                            <?php if(!empty($image_path)): ?>
                                                <img src="<?php echo htmlspecialchars($image_path); ?>" 
                                                     alt="<?php echo htmlspecialchars($event['titre']); ?>" 
                                                     class="img-fluid" 
                                                     style="width: 100%; max-height: 400px; object-fit: cover; display: block; position: relative; z-index: 2;"
                                                     onerror="this.style.display='none';">
                                            <?php endif; ?>
                                        </div>

                                        <!-- Informations de l'événement -->
                                        <div class="bg-light p-3 rounded mb-3">
                                            <div class="row">
                                                <div class="col-md-6 mb-2">
                                                    <div>
                                                        <i class="ri-calendar-line me-2"></i><strong>Date :</strong> 
                                                        <?php echo date('d/m/Y à H:i', strtotime($event['date_event'])); ?>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 mb-2">
                                                    <div>
                                                        <i class="ri-map-pin-line me-2"></i><strong>Lieu :</strong> 
                                                        <?php echo htmlspecialchars($event['lieu']); ?>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 mb-2">
                                                    <div>
                                                        <i class="ri-price-tag-3-line me-2"></i><strong>Type :</strong> 
                                                        <span class="badge badge-<?php echo htmlspecialchars($event['type_event']); ?>">
                                                            <?php echo htmlspecialchars($event['type_event']); ?>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 mb-2">
                                                    <div>
                                                        <i class="ri-user-line me-2"></i><strong>Places :</strong> 
                                                        <span class="<?php echo $placesClass; ?>" style="font-weight: bold;">
                                                            <?php if($places_restantes > 0): ?>
                                                                <?php echo $places_restantes; ?> restantes sur <?php echo $event['nombre_places']; ?>
                                                            <?php else: ?>
                                                                Complet
                                                            <?php endif; ?>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Engagement Bar (style posts) -->
                                        <div class="post-engagement mb-3">
                                            <div class="engagement-stats d-flex gap-3">
                                                <span class="btn btn-sm btn-link text-decoration-none p-0" style="color: #6c757d; cursor: default;">
                                                    <i class="ri-user-line me-1"></i> Inscriptions
                                                    <span class="ms-1">
                                                        <?php echo $inscriptions_count; ?>/<?php echo $event['nombre_places']; ?>
                                                    </span>
                                                </span>
                                                <span class="btn btn-sm btn-link text-decoration-none p-0" style="color: #6c757d; cursor: default;">
                                                    <i class="ri-calendar-line me-1"></i> Événement
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Actions -->
                                        <div class="d-flex gap-2">
                                            <a href="event.php?controller=event&action=detail&id=<?php echo $event['id_event']; ?>" 
                                               class="btn btn-primary btn-sm flex-fill">
                                                <i class="ri-eye-line me-1"></i> Voir détails
                                            </a>
                                            <?php if($places_restantes > 0): ?>
                                                <a href="event.php?controller=inscription&action=create&id_event=<?php echo $event['id_event']; ?>" 
                                                   class="btn btn-success btn-sm flex-fill">
                                                    <i class="ri-user-add-line me-1"></i> S'inscrire
                                                </a>
                                            <?php else: ?>
                                                <button class="btn btn-secondary btn-sm flex-fill" disabled>
                                                    <i class="ri-close-circle-line me-1"></i> Complet
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="row justify-content-center">
                        <div class="col-lg-8">
                            <div class="text-center py-5">
                                <i class="ri-calendar-line" style="font-size: 4rem; color: #ddd;"></i>
                                <h5 class="mt-3 text-muted">😔 Aucun événement trouvé</h5>
                                <p class="text-muted">Il n'y a pas d'événements disponibles pour le moment.</p>
                                <p class="text-muted">Revenez plus tard ou modifiez vos critères de recherche.</p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

