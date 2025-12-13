/**
 * assets/js/confirmation-handler.js
 * 🎯 Gestionnaire de confirmations pour les actions utilisateur
 * Gère les suppressions, modifications, annulations avec notifications
 */

// ============================================
// GESTIONNAIRE DE CONFIRMATIONS D'ACTIONS
// ============================================
class ActionConfirmationHandler {
    constructor() {
        this.init();
    }
    
    init() {
        document.addEventListener('DOMContentLoaded', () => {
            this.attachConfirmationHandlers();
            console.log('✅ Gestionnaire de confirmations initialisé');
        });
    }
    
    attachConfirmationHandlers() {
        // Confirmation de suppression d'événement
        document.querySelectorAll('a[href*="action=delete"]').forEach(link => {
            link.addEventListener('click', (e) => this.confirmDelete(e, 'événement'));
        });
        
        // Confirmation d'annulation d'inscription
        document.querySelectorAll('a[href*="annuler_inscription"]').forEach(link => {
            link.addEventListener('click', (e) => this.confirmCancelInscription(e));
        });
        
        // Confirmation de modification
        document.querySelectorAll('form[id*="edit"]').forEach(form => {
            form.addEventListener('submit', (e) => this.confirmEdit(e));
        });
        
        // Confirmation de création
        document.querySelectorAll('form[id*="form"]').forEach(form => {
            if (!form.id.includes('edit')) {
                form.addEventListener('submit', (e) => this.handleCreation(e));
            }
        });
    }
    
    // ============================================
    // CONFIRMATIONS SPÉCIFIQUES
    // ============================================
    
    confirmDelete(e, itemType = 'élément') {
        e.preventDefault();
        const url = e.currentTarget.href;
        
        const message = `
            <strong>⚠️ Attention !</strong><br><br>
            Êtes-vous sûr de vouloir supprimer cet ${itemType} ?<br>
            <span style="color: #dc3545; font-weight: 600;">Cette action est irréversible.</span>
        `;
        
        window.confirmationDialog.show(message, 
            () => {
                // Confirmé
                window.notificationManager.info('⏳ Suppression en cours...');
                window.location.href = url;
            },
            () => {
                // Annulé
                window.notificationManager.info('ℹ️ Suppression annulée');
            }
        );
    }
    
    confirmCancelInscription(e) {
        e.preventDefault();
        const url = e.currentTarget.href;
        
        const message = `
            <strong>⚠️ Confirmer l'annulation</strong><br><br>
            Vous êtes sur le point d'annuler votre inscription.<br>
            <ul style="text-align: left; margin: 15px 0; color: #666;">
                <li>Votre place sera libérée</li>
                <li>Vous ne recevrez plus de notifications</li>
                <li>Vous devrez vous réinscrire si vous changez d'avis</li>
            </ul>
            Voulez-vous vraiment continuer ?
        `;
        
        window.confirmationDialog.show(message,
            () => {
                window.notificationManager.warning('⏳ Annulation de l\'inscription...');
                window.location.href = url;
            },
            () => {
                window.notificationManager.success('✅ Inscription conservée');
            }
        );
    }
    
    confirmEdit(e) {
        const form = e.target;
        const isValid = form.checkValidity();
        
        if (!isValid) {
            e.preventDefault();
            window.notificationManager.error('❌ Veuillez corriger les erreurs avant d\'enregistrer');
            return false;
        }
        
        // Ne pas empêcher la soumission si valide
        window.notificationManager.info('⏳ Enregistrement des modifications...');
        
        // Afficher succès après soumission
        setTimeout(() => {
            window.notificationManager.success('✅ Modifications enregistrées avec succès !');
        }, 100);
    }
    
    handleCreation(e) {
        const form = e.target;
        const isValid = form.checkValidity();
        
        if (!isValid) {
            e.preventDefault();
            window.notificationManager.error('❌ Veuillez remplir tous les champs obligatoires');
            return false;
        }
        
        window.notificationManager.info('⏳ Création en cours...');
        
        // Le formulaire continue sa soumission normale
    }
}

// ============================================
// GESTION DES NOTIFICATIONS APRÈS REDIRECT
// ============================================
class PostRedirectNotifications {
    constructor() {
        this.init();
    }
    
    init() {
        document.addEventListener('DOMContentLoaded', () => {
            this.checkForSessionMessages();
        });
    }
    
    checkForSessionMessages() {
        // Chercher les messages de session PHP dans les alerts
        const successAlert = document.querySelector('.alert-success');
        const errorAlert = document.querySelector('.alert-error');
        
        if (successAlert) {
            const message = successAlert.textContent.trim();
            successAlert.style.display = 'none'; // Cacher l'alerte PHP
            
            setTimeout(() => {
                window.notificationManager.success(message, 7000);
            }, 300);
        }
        
        if (errorAlert) {
            const message = errorAlert.textContent.trim();
            errorAlert.style.display = 'none'; // Cacher l'alerte PHP
            
            setTimeout(() => {
                window.notificationManager.error(message, 8000);
            }, 300);
        }
    }
}

// ============================================
// NOTIFICATIONS DE PROGRESSION
// ============================================
class ProgressNotifications {
    showUploadProgress(file) {
        const notification = document.createElement('div');
        notification.className = 'progress-notification';
        notification.innerHTML = `
            <div class="progress-content">
                <div class="progress-icon">📤</div>
                <div class="progress-info">
                    <div class="progress-title">Téléchargement de ${file.name}</div>
                    <div class="progress-bar-container">
                        <div class="progress-bar" style="width: 0%"></div>
                    </div>
                    <div class="progress-text">0%</div>
                </div>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        return {
            update: (percent) => {
                const bar = notification.querySelector('.progress-bar');
                const text = notification.querySelector('.progress-text');
                bar.style.width = percent + '%';
                text.textContent = Math.round(percent) + '%';
            },
            complete: () => {
                notification.querySelector('.progress-icon').textContent = '✅';
                notification.querySelector('.progress-title').textContent = 'Téléchargement terminé !';
                setTimeout(() => notification.remove(), 2000);
            },
            error: () => {
                notification.querySelector('.progress-icon').textContent = '❌';
                notification.querySelector('.progress-title').textContent = 'Erreur de téléchargement';
                notification.classList.add('error');
                setTimeout(() => notification.remove(), 3000);
            }
        };
    }
    
    showFormSubmitProgress() {
        const overlay = document.createElement('div');
        overlay.className = 'submit-overlay';
        overlay.innerHTML = `
            <div class="submit-spinner">
                <div class="spinner"></div>
                <div class="submit-message">⏳ Envoi en cours...</div>
            </div>
        `;
        document.body.appendChild(overlay);
        
        return {
            success: () => {
                overlay.querySelector('.spinner').style.display = 'none';
                overlay.querySelector('.submit-message').innerHTML = '✅ Envoi réussi !';
                setTimeout(() => overlay.remove(), 1500);
            },
            error: (message = 'Erreur lors de l\'envoi') => {
                overlay.querySelector('.spinner').style.display = 'none';
                overlay.querySelector('.submit-message').innerHTML = `❌ ${message}`;
                overlay.classList.add('error');
                setTimeout(() => overlay.remove(), 3000);
            }
        };
    }
}

// ============================================
// NOTIFICATIONS DE VALIDATION EN TEMPS RÉEL
// ============================================
class ValidationNotifications {
    notifyFieldValidation(field, isValid, message) {
        if (isValid) {
            this.showFieldSuccess(field, message);
        } else {
            this.showFieldError(field, message);
        }
    }
    
    showFieldSuccess(field, message) {
        // Ajouter icône de succès au champ
        this.addFieldIcon(field, '✅', 'success');
        
        // Animation du champ
        field.style.borderColor = '#28a745';
        field.style.boxShadow = '0 0 0 3px rgba(40, 167, 69, 0.1)';
        
        setTimeout(() => {
            field.style.boxShadow = '';
        }, 1000);
    }
    
    showFieldError(field, message) {
        // Ajouter icône d'erreur au champ
        this.addFieldIcon(field, '❌', 'error');
        
        // Animation shake
        field.style.animation = 'shake 0.3s';
        field.style.borderColor = '#dc3545';
        
        setTimeout(() => {
            field.style.animation = '';
        }, 300);
        
        // Notification mini
        window.notificationManager.error(message, 3000);
    }
    
    addFieldIcon(field, icon, type) {
        // Supprimer ancienne icône
        const oldIcon = field.parentNode.querySelector('.field-status-icon');
        if (oldIcon) oldIcon.remove();
        
        // Ajouter nouvelle icône
        const iconElement = document.createElement('span');
        iconElement.className = `field-status-icon field-status-${type}`;
        iconElement.textContent = icon;
        iconElement.style.cssText = `
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 18px;
            animation: popIn 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        `;
        
        // S'assurer que le parent est en position relative
        if (field.parentNode.style.position !== 'relative') {
            field.parentNode.style.position = 'relative';
        }
        
        field.parentNode.appendChild(iconElement);
        
        // Auto-suppression
        setTimeout(() => {
            iconElement.style.opacity = '0';
            iconElement.style.transform = 'translateY(-50%) scale(0)';
            setTimeout(() => iconElement.remove(), 300);
        }, 3000);
    }
}

// ============================================
// ANIMATIONS CSS ADDITIONNELLES
// ============================================
const additionalStyles = `
<style>
/* Progress notification */
.progress-notification {
    position: fixed;
    bottom: 20px;
    right: 20px;
    background: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
    z-index: 9998;
    min-width: 300px;
    animation: slideUp 0.3s ease-out;
}

.progress-content {
    display: flex;
    gap: 15px;
    align-items: center;
}

.progress-icon {
    font-size: 32px;
}

.progress-info {
    flex: 1;
}

.progress-title {
    font-weight: 600;
    margin-bottom: 10px;
    color: #333;
}

.progress-bar-container {
    width: 100%;
    height: 8px;
    background: #e9ecef;
    border-radius: 4px;
    overflow: hidden;
    margin-bottom: 5px;
}

.progress-bar {
    height: 100%;
    background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
    transition: width 0.3s ease;
}

.progress-text {
    font-size: 12px;
    color: #666;
    text-align: right;
}

/* Submit overlay */
.submit-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 10001;
    animation: fadeIn 0.3s;
}

.submit-spinner {
    background: white;
    padding: 40px 60px;
    border-radius: 15px;
    text-align: center;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
}

.spinner {
    width: 50px;
    height: 50px;
    border: 4px solid #f3f3f3;
    border-top: 4px solid #667eea;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto 20px;
}

.submit-message {
    font-size: 18px;
    font-weight: 600;
    color: #333;
}

.submit-overlay.error .submit-message {
    color: #dc3545;
}

/* Animations */
@keyframes slideUp {
    from {
        transform: translateY(100px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

@keyframes popIn {
    0% {
        transform: translateY(-50%) scale(0);
        opacity: 0;
    }
    50% {
        transform: translateY(-50%) scale(1.2);
    }
    100% {
        transform: translateY(-50%) scale(1);
        opacity: 1;
    }
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-10px); }
    75% { transform: translateX(10px); }
}

/* Field status icons */
.field-status-icon {
    transition: all 0.3s;
}

.field-status-success {
    color: #28a745;
}

.field-status-error {
    color: #dc3545;
}
</style>
`;

// ============================================
// INITIALISATION GLOBALE
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    // Injecter les styles additionnels
    if (!document.getElementById('confirmation-styles')) {
        const styleElement = document.createElement('div');
        styleElement.id = 'confirmation-styles';
        styleElement.innerHTML = additionalStyles;
        document.head.appendChild(styleElement.firstElementChild);
    }
    
    // Initialiser les gestionnaires
    window.actionConfirmationHandler = new ActionConfirmationHandler();
    window.postRedirectNotifications = new PostRedirectNotifications();
    window.progressNotifications = new ProgressNotifications();
    window.validationNotifications = new ValidationNotifications();
    
    console.log('✅ Tous les gestionnaires de confirmation initialisés');
});

// ============================================
// HELPERS GLOBAUX
// ============================================

// Helper pour confirmer une action
window.confirmAction = function(message, onConfirm, onCancel) {
    if (window.confirmationDialog) {
        window.confirmationDialog.show(message, onConfirm, onCancel);
    }
};

// Helper pour notification de succès après action
window.notifySuccess = function(action) {
    const messages = {
        'create': '🎉 Élément créé avec succès !',
        'update': '✅ Modifications enregistrées !',
        'delete': '🗑️ Élément supprimé avec succès !',
        'confirm': '✅ Action confirmée !',
        'cancel': 'ℹ️ Action annulée'
    };
    
    const message = messages[action] || '✅ Action effectuée avec succès !';
    window.notificationManager.success(message);
};

// Helper pour notification d'erreur
window.notifyError = function(message = 'Une erreur est survenue') {
    window.notificationManager.error('❌ ' + message);
};