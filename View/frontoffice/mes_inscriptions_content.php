<?php
/**
 * Contenu pour mes inscriptions
 * Utilisé avec layout.php qui inclut le header
 */
?>
<div class="row">
    <div class="col-12">
        <div class="iq-card">
            <div class="iq-card-header">
                <h4 class="card-title">📋 Mes Inscriptions</h4>
                <?php if(isset($user['email'])): ?>
                    <p class="text-muted mb-0">
                        <span class="badge bg-info"><?php echo htmlspecialchars($user['email']); ?></span>
                    </p>
                <?php endif; ?>
            </div>
            <div class="iq-card-body">
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
                
                <?php if(!empty($inscriptions) && count($inscriptions) > 0): ?>
                    <div class="row">
                        <?php 
                        // Récupérer les détails des événements
                        require_once __DIR__ . '/../../Model/Event.php';
                        require_once __DIR__ . '/../../config.php';
                        $eventModel = new Event(Config::getConnexion());
                        
                        foreach($inscriptions as $inscription): 
                            // Récupérer les détails de l'événement
                            $eventModel->setIdEvent($inscription['id_event']);
                            $eventDetails = null;
                            if($eventModel->readOne()) {
                                $eventDetails = clone $eventModel;
                            }
                            
                            $statutClass = 'en-attente';
                            $statutText = 'En attente';
                            if($inscription['statut'] === 'confirme') {
                                $statutClass = 'confirme';
                                $statutText = 'Confirmé';
                            } elseif($inscription['statut'] === 'annule') {
                                $statutClass = 'annule';
                                $statutText = 'Annulé';
                            }
                        ?>
                            <div class="col-md-6 mb-4">
                                <div class="inscription-card">
                                    <div class="inscription-header">
                                        <h5 class="mb-0">
                                            <?php echo $eventDetails ? htmlspecialchars($eventDetails->getTitre()) : 'Événement #' . $inscription['id_event']; ?>
                                        </h5>
                                        <span class="inscription-status <?php echo $statutClass; ?>"><?php echo $statutText; ?></span>
                                    </div>
                                    
                                    <?php if($eventDetails): ?>
                                        <div class="inscription-info">
                                            <i class="ri-calendar-line"></i> 
                                            <strong>Date :</strong>
                                            <?php echo date('d/m/Y à H:i', strtotime($eventDetails->getDateEvent())); ?>
                                        </div>
                                        <div class="inscription-info">
                                            <i class="ri-map-pin-line"></i> 
                                            <strong>Lieu :</strong>
                                            <?php echo htmlspecialchars($eventDetails->getLieu()); ?>
                                        </div>
                                        <div class="inscription-info">
                                            <i class="ri-price-tag-3-line"></i> 
                                            <strong>Type :</strong>
                                            <?php echo htmlspecialchars($eventDetails->getTypeEvent()); ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <hr style="margin: 15px 0;">
                                    
                                    <div class="inscription-info">
                                        <strong>Nom :</strong> <?php echo htmlspecialchars($inscription['nom'] . ' ' . $inscription['prenom']); ?>
                                    </div>
                                    <div class="inscription-info">
                                        <strong>Email :</strong> <?php echo htmlspecialchars($inscription['email']); ?>
                                    </div>
                                    <div class="inscription-info">
                                        <strong>Téléphone :</strong> <?php echo htmlspecialchars($inscription['telephone']); ?>
                                    </div>
                                    
                                    <?php if(!empty($inscription['pourquoi'])): ?>
                                        <div class="inscription-info">
                                            <strong>Motivation :</strong><br>
                                            <small class="text-muted"><?php echo htmlspecialchars($inscription['pourquoi']); ?></small>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="inscription-actions">
                                        <?php if($eventDetails): ?>
                                            <a href="event.php?controller=event&action=detail&id=<?php echo $eventDetails->getIdEvent(); ?>" 
                                               class="btn btn-sm btn-info">
                                                <i class="ri-eye-line"></i> Voir l'événement
                                            </a>
                                        <?php endif; ?>
                                        
                                        <?php if($inscription['statut'] !== 'annule'): ?>
                                            <a href="event.php?controller=inscription&action=edit&id=<?php echo $inscription['id_inscription']; ?>" 
                                               class="btn btn-sm btn-warning">
                                                <i class="ri-edit-line"></i> Modifier
                                            </a>
                                            <a href="event.php?controller=inscription&action=delete&id=<?php echo $inscription['id_inscription']; ?>" 
                                               class="btn btn-sm btn-danger"
                                               onclick="return confirm('Êtes-vous sûr de vouloir annuler cette inscription ?');">
                                                <i class="ri-delete-bin-line"></i> Annuler
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="ri-calendar-line" style="font-size: 4rem; color: #ddd;"></i>
                        <h5 class="mt-3 text-muted">Aucune inscription trouvée</h5>
                        <p class="text-muted">Vous n'êtes inscrit à aucun événement pour le moment.</p>
                        <a href="event.php?controller=event&action=liste" class="btn btn-primary mt-3">
                            <i class="ri-calendar-line"></i> Voir les événements disponibles
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

