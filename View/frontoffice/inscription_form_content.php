<?php
/**
 * Contenu pour le formulaire d'inscription
 * Utilisé avec layout.php qui inclut le header
 */
?>
<div class="row">
    <div class="col-12">
        <div class="iq-card">
            <div class="iq-card-header">
                <h4 class="card-title">📝 Inscription à l'événement</h4>
                <p class="text-muted">Remplissez ce formulaire pour vous inscrire</p>
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
                        <div class="col-md-6 mt-2">
                            <strong>👥 Places restantes :</strong> 
                            <span class="badge bg-<?php echo $places_restantes > 10 ? 'success' : 'warning'; ?>">
                                <?php echo $places_restantes; ?> / <?php echo $event->getNombrePlaces(); ?>
                            </span>
                        </div>
                    </div>
                </div>
                
                <!-- Vérifier si déjà inscrit -->
                <?php if(isset($already_inscribed) && $already_inscribed): ?>
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <strong>⚠️ Attention :</strong> Vous êtes déjà inscrit à cet événement. Chaque utilisateur ne peut s'inscrire qu'une seule fois par événement.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <div class="text-center mb-4">
                        <a href="event.php?controller=inscription&action=mes_inscriptions" class="btn btn-info">
                            <i class="ri-file-list-3-line"></i> Voir mes inscriptions
                        </a>
                        <a href="event.php?controller=event&action=detail&id=<?php echo $event->getIdEvent(); ?>" class="btn btn-secondary">
                            <i class="ri-arrow-left-line"></i> Retour aux détails
                        </a>
                    </div>
                <?php endif; ?>
                
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
                
                <div class="alert alert-secondary mb-4">
                    <h6 class="mb-2">ℹ️ Informations importantes</h6>
                    <ul class="mb-0 small">
                        <li>Tous les champs marqués d'un <span class="text-danger">*</span> sont obligatoires</li>
                        <li>Un email de confirmation vous sera envoyé</li>
                        <li>Vous pourrez modifier votre inscription plus tard</li>
                        <li>En cas de problème, contactez-nous</li>
                    </ul>
                </div>
                
                <form id="inscription-form" method="POST" action="event.php?controller=inscription&action=store" novalidate <?php echo (isset($already_inscribed) && $already_inscribed) ? 'style="display:none;"' : ''; ?>>
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
                                value="<?php echo htmlspecialchars($_SESSION['old_data']['nom'] ?? ''); ?>"
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
                                value="<?php echo htmlspecialchars($_SESSION['old_data']['prenom'] ?? ''); ?>"
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
                            value="<?php 
                                if(isset($_SESSION['id_user'])) {
                                    require_once __DIR__ . '/../../Model/user.php';
                                    $userModel = new UserModel();
                                    $user = $userModel->getUserById($_SESSION['id_user']);
                                    echo htmlspecialchars($user['email'] ?? ($_SESSION['old_data']['email'] ?? ''));
                                } else {
                                    echo htmlspecialchars($_SESSION['old_data']['email'] ?? '');
                                }
                            ?>"
                            placeholder="exemple@email.com"
                            <?php echo (isset($_SESSION['id_user'])) ? 'readonly' : ''; ?>
                            required>
                        <small class="form-text text-muted">
                            📧 Vous recevrez une confirmation à cette adresse
                            <?php if(isset($_SESSION['id_user'])): ?>
                                <br><span class="text-info">ℹ️ Email de votre compte (non modifiable)</span>
                            <?php endif; ?>
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
                            value="<?php echo htmlspecialchars($_SESSION['old_data']['telephone'] ?? ''); ?>"
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
                            required><?php echo htmlspecialchars($_SESSION['old_data']['pourquoi'] ?? ''); ?></textarea>
                        <small class="form-text text-muted">
                            ✍️ Minimum 20 caractères, maximum 500 caractères
                        </small>
                    </div>
                    
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="ri-check-line"></i> Confirmer mon inscription
                        </button>
                        <a href="event.php?controller=event&action=detail&id=<?php echo $event->getIdEvent(); ?>" class="btn btn-secondary">
                            <i class="ri-close-line"></i> Annuler
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

