/**
 * assets/js/notifications.js
 * 🎉 Système de notifications pour les validations et confirmations
 * Messages de succès, erreur, info et avertissement avec animations
 */

// ============================================
// CLASSE NOTIFICATION MANAGER
// ============================================
class NotificationManager {
    constructor() {
        this.container = null;
        this.notifications = [];
        this.init();
    }
    
    init() {
        // Créer le conteneur de notifications
        this.container = document.createElement('div');
        this.container.id = 'notification-container';
        this.container.className = 'notification-container';
        document.body.appendChild(this.container);
        
        console.log('✅ Système de notifications initialisé');
    }
    
    /**
     * Afficher une notification
     * @param {string} message - Le message à afficher
     * @param {string} type - Type: 'success', 'error', 'info', 'warning'
     * @param {number} duration - Durée en ms (0 = permanent)
     */
    show(message, type = 'info', duration = 5000) {
        const notification = this.createNotification(message, type);
        this.container.appendChild(notification);
        this.notifications.push(notification);
        
        // Animation d'entrée
        setTimeout(() => notification.classList.add('show'), 10);
        
        // Auto-suppression
        if (duration > 0) {
            setTimeout(() => this.remove(notification), duration);
        }
        
        return notification;
    }
    
    createNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        
        const icon = this.getIcon(type);
        
        notification.innerHTML = `
            <div class="notification-icon">${icon}</div>
            <div class="notification-content">
                <div class="notification-message">${message}</div>
            </div>
            <button class="notification-close" onclick="window.notificationManager.remove(this.parentElement)">
                ✕
            </button>
        `;
        
        return notification;
    }
    
    getIcon(type) {
        const icons = {
            success: '✅',
            error: '❌',
            warning: '⚠️',
            info: 'ℹ️'
        };
        return icons[type] || icons.info;
    }
    
    remove(notification) {
        notification.classList.remove('show');
        notification.classList.add('hide');
        
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
            this.notifications = this.notifications.filter(n => n !== notification);
        }, 300);
    }
    
    // Méthodes raccourcies
    success(message, duration = 5000) {
        return this.show(message, 'success', duration);
    }
    
    error(message, duration = 7000) {
        return this.show(message, 'error', duration);
    }
    
    warning(message, duration = 6000) {
        return this.show(message, 'warning', duration);
    }
    
    info(message, duration = 5000) {
        return this.show(message, 'info', duration);
    }
    
    // Supprimer toutes les notifications
    clearAll() {
        this.notifications.forEach(n => this.remove(n));
    }
}

// ============================================
// MESSAGES DE VALIDATION SPÉCIFIQUES
// ============================================
const ValidationMessages = {
    // Événements
    event: {
        created: '🎉 Événement créé avec succès ! Il est maintenant visible sur le site.',
        updated: '✅ Événement modifié avec succès !',
        deleted: '🗑️ Événement supprimé avec succès.',
        validating: 'ℹ️ Validation des données en cours...',
        imageUploaded: '📸 Image téléchargée avec succès !',
        dateValid: '✅ Date validée - L\'événement est bien dans le futur',
        placesValid: '✅ Nombre de places validé'
    },
    
    // Inscriptions
    inscription: {
        created: '🎊 Inscription réussie ! Vous recevrez une confirmation par email.',
        updated: '✅ Vos informations ont été mises à jour avec succès !',
        deleted: '🗑️ Inscription annulée avec succès.',
        confirmed: '✅ Inscription confirmée !',
        validating: 'ℹ️ Validation de votre inscription...',
        emailSent: '📧 Email de confirmation envoyé !',
        alreadyRegistered: '⚠️ Vous êtes déjà inscrit à cet événement'
    },
    
    // Validation formulaire
    form: {
        nameValid: '✅ Nom validé',
        emailValid: '✅ Email validé',
        phoneValid: '✅ Téléphone validé',
        descriptionValid: '✅ Description validée',
        allFieldsValid: '🎉 Tous les champs sont valides !',
        checking: 'ℹ️ Vérification des données...'
    },
    
    // Erreurs
    error: {
        nameInvalid: '❌ Le nom doit contenir au moins 2 caractères (lettres uniquement)',
        emailInvalid: '❌ Format d\'email invalide',
        phoneInvalid: '❌ Le téléphone doit contenir exactement 8 chiffres',
        dateInvalid: '❌ La date doit être dans le futur',
        placesInvalid: '❌ Le nombre de places doit être entre 1 et 1000',
        required: '❌ Ce champ est obligatoire',
        tooShort: '❌ Texte trop court (minimum requis: {min} caractères)',
        tooLong: '❌ Texte trop long (maximum autorisé: {max} caractères)',
        eventFull: '❌ Désolé, cet événement est complet !',
        networkError: '❌ Erreur de connexion. Veuillez réessayer.',
        serverError: '❌ Erreur serveur. Contactez l\'administrateur.'
    },
    
    // Actions utilisateur
    user: {
        saved: '💾 Données sauvegardées',
        uploading: '⏳ Téléchargement en cours...',
        processing: '⏳ Traitement en cours...',
        loading: '⏳ Chargement...',
        searching: '🔍 Recherche en cours...'
    }
};

// ============================================
// VALIDATION EN TEMPS RÉEL AVEC NOTIFICATIONS
// ============================================
class RealtimeValidator {
    constructor() {
        this.setupValidation();
    }
    
    setupValidation() {
        document.addEventListener('DOMContentLoaded', () => {
            this.attachFieldValidators();
            this.attachFormValidators();
        });
    }
    
    attachFieldValidators() {
        // Validation nom/prénom
        document.querySelectorAll('input[name="nom"], input[name="prenom"]').forEach(field => {
            field.addEventListener('blur', (e) => this.validateName(e.target));
            field.addEventListener('input', (e) => this.clearFieldNotification(e.target));
        });
        
        // Validation email
        document.querySelectorAll('input[name="email"]').forEach(field => {
            field.addEventListener('blur', (e) => this.validateEmail(e.target));
        });
        
        // Validation téléphone
        document.querySelectorAll('input[name="telephone"]').forEach(field => {
            field.addEventListener('input', (e) => this.validatePhone(e.target));
        });
        
        // Validation date
        document.querySelectorAll('input[name="date_event"]').forEach(field => {
            field.addEventListener('change', (e) => this.validateDate(e.target));
        });
        
        // Validation places
        document.querySelectorAll('input[name="nombre_places"]').forEach(field => {
            field.addEventListener('blur', (e) => this.validatePlaces(e.target));
        });
        
        // Validation description
        document.querySelectorAll('textarea[name="description"], textarea[name="pourquoi"]').forEach(field => {
            field.addEventListener('blur', (e) => this.validateDescription(e.target));
        });
    }
    
    attachFormValidators() {
        // Événement
        const eventForm = document.getElementById('event-form');
        if (eventForm) {
            eventForm.addEventListener('submit', (e) => this.validateEventForm(e));
        }
        
        // Inscription
        const inscriptionForm = document.getElementById('inscription-form');
        if (inscriptionForm) {
            inscriptionForm.addEventListener('submit', (e) => this.validateInscriptionForm(e));
        }
    }
    
    // Validations spécifiques
    validateName(field) {
        const value = field.value.trim();
        const pattern = /^[a-zA-ZÀ-ÿ\s-]+$/;
        
        if (value.length < 2) {
            this.showFieldError(field, ValidationMessages.error.nameInvalid);
            return false;
        }
        
        if (!pattern.test(value)) {
            this.showFieldError(field, ValidationMessages.error.nameInvalid);
            return false;
        }
        
        this.showFieldSuccess(field, ValidationMessages.form.nameValid);
        return true;
    }
    
    validateEmail(field) {
        const value = field.value.trim();
        const pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        if (!pattern.test(value)) {
            this.showFieldError(field, ValidationMessages.error.emailInvalid);
            return false;
        }
        
        this.showFieldSuccess(field, ValidationMessages.form.emailValid);
        return true;
    }
    
    validatePhone(field) {
        // Nettoyer l'entrée
        let cleaned = field.value.replace(/[^0-9]/g, '');
        field.value = cleaned.substring(0, 8);
        
        if (cleaned.length === 8) {
            this.showFieldSuccess(field, ValidationMessages.form.phoneValid);
            field.style.borderColor = '#28a745';
            return true;
        } else if (cleaned.length > 0) {
            field.style.borderColor = '#ffc107';
            return false;
        }
        return false;
    }
    
    validateDate(field) {
        const selectedDate = new Date(field.value);
        const now = new Date();
        
        if (selectedDate < now) {
            this.showFieldError(field, ValidationMessages.error.dateInvalid);
            return false;
        }
        
        this.showFieldSuccess(field, ValidationMessages.event.dateValid);
        return true;
    }
    
    validatePlaces(field) {
        const value = parseInt(field.value);
        
        if (isNaN(value) || value < 1 || value > 1000) {
            this.showFieldError(field, ValidationMessages.error.placesInvalid);
            return false;
        }
        
        this.showFieldSuccess(field, ValidationMessages.event.placesValid);
        return true;
    }
    
    validateDescription(field) {
        const value = field.value.trim();
        const minLength = field.getAttribute('minlength') || 20;
        const maxLength = field.getAttribute('maxlength') || 500;
        
        if (value.length < minLength) {
            const msg = ValidationMessages.error.tooShort.replace('{min}', minLength);
            this.showFieldError(field, msg);
            return false;
        }
        
        if (value.length > maxLength) {
            const msg = ValidationMessages.error.tooLong.replace('{max}', maxLength);
            this.showFieldError(field, msg);
            return false;
        }
        
        this.showFieldSuccess(field, ValidationMessages.form.descriptionValid);
        return true;
    }
    
    // Validation formulaires complets
    validateEventForm(e) {
        window.notificationManager.info(ValidationMessages.event.validating);
        
        // Laisser la validation native se faire
        setTimeout(() => {
            if (e.defaultPrevented) return;
            window.notificationManager.success(ValidationMessages.form.allFieldsValid);
        }, 100);
    }
    
    validateInscriptionForm(e) {
        window.notificationManager.info(ValidationMessages.inscription.validating);
        
        setTimeout(() => {
            if (e.defaultPrevented) return;
            window.notificationManager.success(ValidationMessages.form.allFieldsValid);
        }, 100);
    }
    
    // Affichage des notifications de champ
    showFieldSuccess(field, message) {
        field.classList.remove('error');
        field.classList.add('valid');
        field.style.borderColor = '#28a745';
        
        // Mini notification
        this.showFieldNotification(field, message, 'success');
    }
    
    showFieldError(field, message) {
        field.classList.add('error');
        field.classList.remove('valid');
        field.style.borderColor = '#dc3545';
        
        // Mini notification
        this.showFieldNotification(field, message, 'error');
    }
    
    showFieldNotification(field, message, type) {
        // Supprimer ancienne notification
        this.clearFieldNotification(field);
        
        const notification = document.createElement('div');
        notification.className = `field-notification field-notification-${type}`;
        notification.textContent = message;
        
        field.parentNode.appendChild(notification);
        
        // Auto-suppression après 3 secondes
        setTimeout(() => {
            notification.style.opacity = '0';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }
    
    clearFieldNotification(field) {
        const existing = field.parentNode.querySelector('.field-notification');
        if (existing) existing.remove();
    }
}

// ============================================
// CONFIRMATIONS AVEC CALLBACKS
// ============================================
class ConfirmationDialog {
    show(message, onConfirm, onCancel = null) {
        const overlay = document.createElement('div');
        overlay.className = 'confirmation-overlay';
        
        const dialog = document.createElement('div');
        dialog.className = 'confirmation-dialog';
        
        dialog.innerHTML = `
            <div class="confirmation-icon">⚠️</div>
            <div class="confirmation-message">${message}</div>
            <div class="confirmation-actions">
                <button class="btn-confirm">✅ Confirmer</button>
                <button class="btn-cancel">❌ Annuler</button>
            </div>
        `;
        
        overlay.appendChild(dialog);
        document.body.appendChild(overlay);
        
        // Animation
        setTimeout(() => {
            overlay.classList.add('show');
            dialog.classList.add('show');
        }, 10);
        
        // Événements
        dialog.querySelector('.btn-confirm').addEventListener('click', () => {
            this.close(overlay);
            if (onConfirm) onConfirm();
        });
        
        dialog.querySelector('.btn-cancel').addEventListener('click', () => {
            this.close(overlay);
            if (onCancel) onCancel();
        });
        
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) {
                this.close(overlay);
                if (onCancel) onCancel();
            }
        });
    }
    
    close(overlay) {
        overlay.classList.remove('show');
        setTimeout(() => overlay.remove(), 300);
    }
}

// ============================================
// STYLES CSS
// ============================================
const styles = `
<style>
/* Conteneur de notifications */
.notification-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    display: flex;
    flex-direction: column;
    gap: 10px;
    max-width: 400px;
}

/* Notification */
.notification {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 16px 20px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    transform: translateX(400px);
    opacity: 0;
    transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}

.notification.show {
    transform: translateX(0);
    opacity: 1;
}

.notification.hide {
    transform: translateX(400px);
    opacity: 0;
}

/* Types de notifications */
.notification-success {
    border-left: 4px solid #28a745;
    background: linear-gradient(135deg, #ffffff 0%, #f0fff4 100%);
}

.notification-error {
    border-left: 4px solid #dc3545;
    background: linear-gradient(135deg, #ffffff 0%, #fff0f0 100%);
}

.notification-warning {
    border-left: 4px solid #ffc107;
    background: linear-gradient(135deg, #ffffff 0%, #fff9e6 100%);
}

.notification-info {
    border-left: 4px solid #17a2b8;
    background: linear-gradient(135deg, #ffffff 0%, #e6f7ff 100%);
}

/* Icône */
.notification-icon {
    font-size: 24px;
    line-height: 1;
}

/* Contenu */
.notification-content {
    flex: 1;
}

.notification-message {
    font-size: 14px;
    font-weight: 500;
    color: #333;
    line-height: 1.4;
}

/* Bouton fermer */
.notification-close {
    background: none;
    border: none;
    color: #999;
    font-size: 18px;
    cursor: pointer;
    padding: 0;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: all 0.3s;
}

.notification-close:hover {
    background: rgba(0, 0, 0, 0.1);
    color: #333;
}

/* Notifications de champ */
.field-notification {
    font-size: 12px;
    font-weight: 600;
    margin-top: 5px;
    padding: 5px 10px;
    border-radius: 5px;
    transition: opacity 0.3s;
    animation: slideDown 0.3s ease-out;
}

.field-notification-success {
    background: #d4edda;
    color: #155724;
}

.field-notification-error {
    background: #f8d7da;
    color: #721c24;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Dialog de confirmation */
.confirmation-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 10000;
    transition: background 0.3s;
}

.confirmation-overlay.show {
    background: rgba(0, 0, 0, 0.5);
}

.confirmation-dialog {
    background: white;
    border-radius: 15px;
    padding: 30px;
    max-width: 400px;
    width: 90%;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
    transform: scale(0.8);
    opacity: 0;
    transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}

.confirmation-dialog.show {
    transform: scale(1);
    opacity: 1;
}

.confirmation-icon {
    font-size: 48px;
    text-align: center;
    margin-bottom: 20px;
}

.confirmation-message {
    text-align: center;
    font-size: 16px;
    color: #333;
    margin-bottom: 25px;
    line-height: 1.5;
}

.confirmation-actions {
    display: flex;
    gap: 10px;
    justify-content: center;
}

.confirmation-actions button {
    padding: 12px 30px;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-confirm {
    background: #28a745;
    color: white;
}

.btn-confirm:hover {
    background: #218838;
    transform: translateY(-2px);
}

.btn-cancel {
    background: #6c757d;
    color: white;
}

.btn-cancel:hover {
    background: #5a6268;
    transform: translateY(-2px);
}

/* Responsive */
@media (max-width: 768px) {
    .notification-container {
        top: 10px;
        right: 10px;
        left: 10px;
        max-width: none;
    }
    
    .notification {
        transform: translateY(-100px);
    }
    
    .notification.show {
        transform: translateY(0);
    }
    
    .notification.hide {
        transform: translateY(-100px);
    }
}
</style>
`;

// ============================================
// INITIALISATION
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    // Injecter les styles
    if (!document.getElementById('notification-styles')) {
        const styleElement = document.createElement('div');
        styleElement.id = 'notification-styles';
        styleElement.innerHTML = styles;
        document.head.appendChild(styleElement.firstElementChild);
    }
    
    // Initialiser le gestionnaire de notifications
    window.notificationManager = new NotificationManager();
    
    // Initialiser le validateur temps réel
    window.realtimeValidator = new RealtimeValidator();
    
    // Initialiser les dialogs de confirmation
    window.confirmationDialog = new ConfirmationDialog();
    
    // Exposer les messages pour utilisation externe
    window.ValidationMessages = ValidationMessages;
    
    console.log('🎉 Système de notifications complet initialisé');
    
    // Message de bienvenue (optionnel)
    setTimeout(() => {
        window.notificationManager.info('👋 Bienvenue ! tu doit complete se formulaire', 3000);
    }, 500);
});

// ============================================
// API PUBLIQUE POUR UTILISATION EXTERNE
// ============================================

// Fonction helper pour afficher rapidement une notification
function showNotification(message, type = 'info', duration = 5000) {
    if (window.notificationManager) {
        return window.notificationManager.show(message, type, duration);
    }
    console.warn('Notification manager not initialized');
}

// Fonction helper pour confirmation
function showConfirmation(message, onConfirm, onCancel) {
    if (window.confirmationDialog) {
        window.confirmationDialog.show(message, onConfirm, onCancel);
    } else {
        // Fallback
        if (confirm(message)) {
            onConfirm();
        } else if (onCancel) {
            onCancel();
        }
    }
}

// Exposer globalement
window.showNotification = showNotification;
window.showConfirmation = showConfirmation;