<?php
/**
 * Contenu pour les détails d'un événement
 * Utilisé avec layout.php qui inclut le header
 */
?>
<div class="row">
    <div class="col-12">
        <a href="event.php?controller=event&action=liste" class="btn btn-secondary mb-3">
            <i class="ri-arrow-left-line"></i> Retour à la liste
        </a>
        
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
        
        <div class="iq-card">
            <!-- En-tête -->
            <div class="iq-card-header">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge bg-secondary"><?php echo htmlspecialchars($event->getTypeEvent()); ?></span>
                </div>
                <h4 class="card-title"><?php echo htmlspecialchars($event->getTitre()); ?></h4>
            </div>
            
            <!-- Image -->
            <?php 
            // Get image from database - always use public/uploads/events/ directory
            $imageUrl = '';
            $event_image = $event->getImage();
            
            if (!empty($event_image)) {
                // Extract filename from any path format
                $imagePath = str_replace('\\', '/', $event_image);
                $filename = basename($imagePath); // Get just the filename
                
                // Always use public/uploads/events/ path
                $imageUrl = '../public/uploads/events/' . $filename;
            }
            ?>
            <div class="event-image-detail">
                <?php if (!empty($imageUrl)): ?>
                    <img src="<?php echo htmlspecialchars($imageUrl); ?>" 
                         alt="<?php echo htmlspecialchars($event->getTitre()); ?>" 
                         class="img-fluid"
                         style="width: 100%; max-height: 500px; object-fit: cover; display: block;"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div style="display: none; width: 100%; height: 300px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 8px; align-items: center; justify-content: center; color: white; font-size: 4rem;">
                        <i class="ri-calendar-event-line"></i>
                    </div>
                <?php else: ?>
                    <div style="width: 100%; height: 300px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-size: 4rem;">
                        <i class="ri-calendar-event-line"></i>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="iq-card-body">
                <!-- Informations principales -->
                <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <div class="info-box">
                            <div class="info-box-label">📅 Date de l'événement</div>
                            <div class="info-box-value">
                                <?php echo date('d/m/Y', strtotime($event->getDateEvent())); ?>
                            </div>
                            <div class="text-muted small">
                                à <?php echo date('H:i', strtotime($event->getDateEvent())); ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <div class="info-box">
                            <div class="info-box-label">📍 Lieu</div>
                            <div class="info-box-value">
                                <?php echo htmlspecialchars($event->getLieu()); ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <div class="info-box">
                            <div class="info-box-label">👥 Capacité</div>
                            <div class="info-box-value">
                                <?php echo $event->getNombrePlaces(); ?> places
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <div class="info-box">
                            <div class="info-box-label">✅ Inscrits</div>
                            <div class="info-box-value">
                                <?php echo $inscriptions_count; ?> personnes
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Alerte places -->
                <?php
                $placesClass = 'alert-success';
                $placesMessage = '✅ Places disponibles';
                
                if($places_restantes <= 0) {
                    $placesClass = 'alert-danger';
                    $placesMessage = '❌ Événement complet - Plus de places disponibles';
                } elseif($places_restantes <= 10) {
                    $placesClass = 'alert-warning';
                    $placesMessage = '⚠️ Attention : Plus que ' . $places_restantes . ' places disponibles !';
                } else {
                    $placesMessage = '✅ ' . $places_restantes . ' places disponibles sur ' . $event->getNombrePlaces();
                }
                ?>
                
                <div class="alert <?php echo $placesClass; ?> mb-4">
                    <?php echo $placesMessage; ?>
                </div>
                
                <!-- Description -->
                <div class="mb-4">
                    <h5 class="mb-3">📋 Description de l'événement</h5>
                    <p class="text-muted"><?php echo nl2br(htmlspecialchars($event->getDescription())); ?></p>
                </div>
                
                <!-- Boutons d'action -->
                <div class="d-flex gap-2">
                    <?php if($places_restantes > 0): ?>
                        <a href="event.php?controller=inscription&action=create&id_event=<?php echo $event->getIdEvent(); ?>" 
                           class="btn btn-primary">
                            <i class="ri-check-line"></i> S'inscrire à cet événement
                        </a>
                    <?php else: ?>
                        <button class="btn btn-primary" disabled>
                            <i class="ri-close-line"></i> Événement complet
                        </button>
                    <?php endif; ?>
                    
                    <a href="event.php?controller=event&action=liste" class="btn btn-secondary">
                        <i class="ri-arrow-left-line"></i> Retour à la liste
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

