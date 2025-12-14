<?php
/**
 * Contenu pour le formulaire de modification d'inscription
 * Utilisé avec event_template.php
 */
?>
<div class="row">
    <div class="col-12">
        <div class="iq-card">
            <div class="iq-card-header">
                <h4 class="card-title">✏️ Modifier mon inscription</h4>
                <p class="text-muted">Modifiez les informations de votre inscription</p>
            </div>
            <div class="iq-card-body">
                <!-- Informations sur l'événement -->
                <div class="alert alert-info mb-4">
                    <h5><?php echo htmlspecialchars($event->getTitre()); ?></h5>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <strong>📅 Date :</strong> <?php echo date('d/m/Y', strtotime($event->getDateEvent())); ?>
                        </div>
                        <div class="col-md-6">
                            <strong>📍 Lieu :</strong> <?php echo htmlspecialchars($event->getLieu()); ?>
                        </div>
                        <div class="col-md-6 mt-2">
                            <strong>🎯 Type :</strong> <?php echo htmlspecialchars($event->getTypeEvent()); ?>
                        </div>
                    </div>
                </div>
                
                <!-- Messages d'erreur serveur -->
                <?php if(isset($_SESSION['errors'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>⚠️ Erreurs de validation :</strong>
                        <ul class="mb-0">
                            <?php foreach($_SESSION['errors'] as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['errors']); ?>
                <?php endif; ?>
                
                <?php if(isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($_SESSION['error']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>
                
                <form id="inscription-edit-form" method="POST" action="event.php?controller=inscription&action=update" novalidate>
                    <input type="hidden" name="id_inscription" value="<?php echo $inscription->getIdInscription(); ?>">
                    <input type="hidden" name="id_event" value="<?php echo $event->getIdEvent(); ?>">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nom" class="form-label">
                                Nom <span class="text-danger">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="nom" 
                                name="nom" 
                                class="form-control"
                                value="<?php echo htmlspecialchars($inscription->getNom() ?? ($_SESSION['old_data']['nom'] ?? '')); ?>"
                                placeholder="Votre nom de famille"
                                required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="prenom" class="form-label">
                                Prénom <span class="text-danger">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="prenom" 
                                name="prenom" 
                                class="form-control"
                                value="<?php echo htmlspecialchars($inscription->getPrenom() ?? ($_SESSION['old_data']['prenom'] ?? '')); ?>"
                                placeholder="Votre prénom"
                                required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">
                            Email <span class="text-danger">*</span>
                        </label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            class="form-control"
                            value="<?php echo htmlspecialchars($inscription->getEmail() ?? ($_SESSION['old_data']['email'] ?? '')); ?>"
                            placeholder="exemple@email.com"
                            readonly
                            required>
                        <small class="form-text text-muted">
                            📧 L'email ne peut pas être modifié
                        </small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="telephone" class="form-label">
                            Téléphone <span class="text-danger">*</span>
                        </label>
                        <input 
                            type="tel" 
                            id="telephone" 
                            name="telephone" 
                            class="form-control"
                            value="<?php echo htmlspecialchars($inscription->getTelephone() ?? ($_SESSION['old_data']['telephone'] ?? '')); ?>"
                            placeholder="12345678"
                            maxlength="8"
                            pattern="[0-9]{8}"
                            required>
                        <small class="form-text text-muted">
                            📱 Format : 8 chiffres uniquement
                        </small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="pourquoi" class="form-label">
                            Pourquoi souhaitez-vous participer ? <span class="text-danger">*</span>
                        </label>
                        <textarea 
                            id="pourquoi" 
                            name="pourquoi" 
                            class="form-control"
                            rows="5"
                            placeholder="Expliquez en quelques mots pourquoi vous souhaitez participer à cet événement..."
                            minlength="20"
                            maxlength="500"
                            required><?php echo htmlspecialchars($inscription->getPourquoi() ?? ($_SESSION['old_data']['pourquoi'] ?? '')); ?></textarea>
                        <small class="form-text text-muted">
                            ✍️ Minimum 20 caractères, maximum 500 caractères
                        </small>
                    </div>
                    
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="ri-check-line"></i> Enregistrer les modifications
                        </button>
                        <a href="event.php?controller=inscription&action=mes_inscriptions" class="btn btn-secondary">
                            <i class="ri-close-line"></i> Annuler
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

