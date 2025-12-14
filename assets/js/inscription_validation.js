/**
 * assets/js/inscription_validation.js
 * ✅ VALIDATION COMPLÈTE avec notification de succès d'inscription
 */

// ============================================
// CONFIGURATION GLOBALE
// ============================================
const ValidationConfig = {
    messages: {
        required: 'Ce champ est obligatoire',
        minLength: 'Minimum {min} caractères requis',
        maxLength: 'Maximum {max} caractères autorisés',
        email: 'Format email invalide',
        phone: 'Le téléphone doit contenir exactement 8 chiffres',
        letters: 'Ce champ ne doit contenir que des lettres',
        futureDate: 'La date doit être dans le futur',
        imageFormat: 'Format d\'image non supporté. Formats acceptés : JPEG, PNG, GIF',
        imageSize: 'La taille de l\'image ne doit pas dépasser 5MB'
    },
    patterns: {
        email: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
        letters: /^[a-zA-ZÀ-ÿ\s-]+$/,
        phone: /^[0-9]{8}$/
    }
};

// ============================================
// CLASSE VALIDATEUR
// ============================================
class FormValidator {
    constructor(formId) {
        this.form = document.getElementById(formId);
        this.errors = [];
        
        if (!this.form) {
            console.error(`❌ Formulaire "${formId}" introuvable`);
            return;
        }
        
        this.init();
        console.log(`✅ Validation initialisée pour "${formId}"`);
    }
    
    init() {
        // Validation à la soumission
        this.form.addEventListener('submit', (e) => this.handleSubmit(e));
        
        // Validation en temps réel
        this.setupRealtimeValidation();
        
        // Compteurs de caractères
        this.setupCharCounters();
    }
    
    handleSubmit(e) {
        this.errors = [];
        this.clearAllErrors();
        
        // Valider tous les champs
        const fields = this.form.querySelectorAll('input, textarea, select');
        fields.forEach(field => this.validateField(field));
        
        if (this.errors.length > 0) {
            e.preventDefault();
            this.showErrors();
            window.scrollTo({ top: 0, behavior: 'smooth' });
            return false;
        }
        
        console.log('✅ Formulaire valide - Envoi en cours...');
        return true;
    }
    
    validateField(field) {
        const value = field.value.trim();
        const name = field.name;
        
        // Champs requis
        if (field.hasAttribute('required') && !value) {
            this.addError(field, ValidationConfig.messages.required);
            return false;
        }
        
        // Validation par type
        switch(name) {
            case 'titre':
                return this.validateTitre(field, value);
            case 'description':
                return this.validateDescription(field, value);
            case 'date_event':
                return this.validateDate(field, value);
            case 'lieu':
                return this.validateLieu(field, value);
            case 'nombre_places':
                return this.validatePlaces(field, value);
            case 'type_event':
                return this.validateType(field, value);
            case 'nom':
            case 'prenom':
                return this.validateName(field, value);
            case 'email':
                return this.validateEmail(field, value);
            case 'telephone':
                return this.validatePhone(field, value);
            case 'pourquoi':
                return this.validateMotivation(field, value);
            case 'image':
                return this.validateImage(field);
        }
        
        return true;
    }
    
    // ============================================
    // VALIDATIONS SPÉCIFIQUES
    // ============================================
    
    validateTitre(field, value) {
        if (value.length < 5) {
            this.addError(field, 'Le titre doit contenir au moins 5 caractères');
            return false;
        }
        this.markValid(field);
        return true;
    }
    
    validateDescription(field, value) {
        if (value.length < 20) {
            this.addError(field, 'La description doit contenir au moins 20 caractères');
            return false;
        }
        this.markValid(field);
        return true;
    }
    
    validateDate(field, value) {
        if (!value) {
            this.addError(field, 'La date est obligatoire');
            return false;
        }
        
        const selectedDate = new Date(value);
        const now = new Date();
        
        if (selectedDate < now) {
            this.addError(field, 'La date doit être dans le futur');
            return false;
        }
        
        this.markValid(field);
        return true;
    }
    
    validateLieu(field, value) {
        if (value.length < 3) {
            this.addError(field, 'Le lieu doit contenir au moins 3 caractères');
            return false;
        }
        this.markValid(field);
        return true;
    }
    
    validatePlaces(field, value) {
        const number = parseInt(value);
        
        if (isNaN(number) || number < 1) {
            this.addError(field, 'Le nombre de places doit être au moins 1');
            return false;
        }
        
        if (number > 1000) {
            this.addError(field, 'Le nombre de places ne peut pas dépasser 1000');
            return false;
        }
        
        this.markValid(field);
        return true;
    }
    
    validateType(field, value) {
        if (!value) {
            this.addError(field, 'Le type d\'événement est obligatoire');
            return false;
        }
        this.markValid(field);
        return true;
    }
    
    validateName(field, value) {
        if (value.length < 2) {
            this.addError(field, 'Ce champ doit contenir au moins 2 caractères');
            return false;
        }
        
        if (!ValidationConfig.patterns.letters.test(value)) {
            this.addError(field, 'Ce champ ne doit contenir que des lettres');
            return false;
        }
        
        this.markValid(field);
        return true;
    }
    
    validateEmail(field, value) {
        if (!ValidationConfig.patterns.email.test(value)) {
            this.addError(field, 'Format email invalide');
            return false;
        }
        this.markValid(field);
        return true;
    }
    
    validatePhone(field, value) {
        if (!ValidationConfig.patterns.phone.test(value)) {
            this.addError(field, 'Le téléphone doit contenir exactement 8 chiffres');
            return false;
        }
        this.markValid(field);
        return true;
    }
    
    validateMotivation(field, value) {
        if (value.length < 20) {
            this.addError(field, 'Veuillez expliquer pourquoi vous souhaitez participer (minimum 20 caractères)');
            return false;
        }
        
        if (value.length > 500) {
            this.addError(field, 'Votre motivation ne peut pas dépasser 500 caractères');
            return false;
        }
        
        this.markValid(field);
        return true;
    }
    
    validateImage(field) {
        if (field.files.length === 0) return true;
        
        const file = field.files[0];
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        const maxSize = 5 * 1024 * 1024; // 5MB
        
        if (!allowedTypes.includes(file.type)) {
            this.addError(field, ValidationConfig.messages.imageFormat);
            return false;
        }
        
        if (file.size > maxSize) {
            this.addError(field, ValidationConfig.messages.imageSize);
            return false;
        }
        
        this.markValid(field);
        return true;
    }
    
    // ============================================
    // GESTION DES ERREURS
    // ============================================
    
    addError(field, message) {
        this.errors.push(message);
        field.classList.add('error');
        field.classList.remove('valid');
        this.showFieldError(field, message);
    }
    
    markValid(field) {
        field.classList.remove('error');
        field.classList.add('valid');
        this.hideFieldError(field);
    }
    
    showFieldError(field, message) {
        this.hideFieldError(field);
        
        const errorDiv = document.createElement('div');
        errorDiv.className = 'field-error-message';
        errorDiv.innerHTML = `<span class="error-icon">⚠️</span> ${message}`;
        
        field.parentNode.appendChild(errorDiv);
    }
    
    hideFieldError(field) {
        const existingError = field.parentNode.querySelector('.field-error-message');
        if (existingError) {
            existingError.remove();
        }
    }
    
    clearAllErrors() {
        const errorMessages = this.form.querySelectorAll('.field-error-message');
        errorMessages.forEach(msg => msg.remove());
        
        const errorFields = this.form.querySelectorAll('.error');
        errorFields.forEach(field => field.classList.remove('error'));
        
        const globalError = document.getElementById('global-error-messages');
        if (globalError) globalError.remove();
    }
    
    showErrors() {
        const existingError = document.getElementById('global-error-messages');
        if (existingError) existingError.remove();
        
        const errorDiv = document.createElement('div');
        errorDiv.id = 'global-error-messages';
        errorDiv.className = 'alert alert-error';
        errorDiv.innerHTML = `
            <strong>⚠️ Erreurs de validation :</strong>
            <ul>${this.errors.map(e => `<li>${e}</li>`).join('')}</ul>
        `;
        
        this.form.parentNode.insertBefore(errorDiv, this.form);
        
        // Auto-hide après 5 secondes
        setTimeout(() => {
            errorDiv.style.transition = 'opacity 0.5s';
            errorDiv.style.opacity = '0';
            setTimeout(() => errorDiv.remove(), 500);
        }, 5000);
    }
    
    // ============================================
    // VALIDATION EN TEMPS RÉEL
    // ============================================
    
    setupRealtimeValidation() {
        // Validation sur blur
        const fields = this.form.querySelectorAll('input, textarea, select');
        fields.forEach(field => {
            field.addEventListener('blur', () => {
                if (field.value.trim()) {
                    this.validateField(field);
                }
            });
        });
        
        // Nettoyage téléphone
        const phoneField = this.form.querySelector('[name="telephone"]');
        if (phoneField) {
            phoneField.addEventListener('input', (e) => {
                let cleaned = e.target.value.replace(/[^0-9]/g, '');
                e.target.value = cleaned.substring(0, 8);
                
                if (cleaned.length === 8) {
                    this.markValid(e.target);
                }
            });
        }
        
        // Limitation places
        const placesField = this.form.querySelector('[name="nombre_places"]');
        if (placesField) {
            placesField.addEventListener('input', (e) => {
                let val = parseInt(e.target.value);
                if (val < 0) e.target.value = 0;
                if (val > 1000) e.target.value = 1000;
            });
        }
    }
    
    // ============================================
    // COMPTEURS DE CARACTÈRES
    // ============================================
    
    setupCharCounters() {
        const textareas = this.form.querySelectorAll('textarea[maxlength]');
        
        textareas.forEach(textarea => {
            const maxLength = textarea.getAttribute('maxlength');
            const minLength = textarea.getAttribute('minlength') || 0;
            
            const counter = document.createElement('div');
            counter.className = 'char-counter';
            textarea.parentNode.appendChild(counter);
            
            const updateCounter = () => {
                const length = textarea.value.length;
                counter.textContent = `${length} / ${maxLength} caractères`;
                
                if (length < minLength) {
                    counter.style.color = '#dc3545';
                    textarea.style.borderColor = '#dc3545';
                } else if (length >= maxLength) {
                    counter.style.color = '#ffc107';
                    textarea.style.borderColor = '#ffc107';
                } else {
                    counter.style.color = '#28a745';
                    textarea.style.borderColor = '#28a745';
                }
            };
            
            textarea.addEventListener('input', updateCounter);
            updateCounter();
        });
    }
}

// ============================================
// PRÉVISUALISATION D'IMAGE
// ============================================
class ImagePreview {
    constructor(inputId) {
        this.input = document.getElementById(inputId);
        if (!this.input) return;
        
        this.input.addEventListener('change', (e) => this.handleChange(e));
    }
    
    handleChange(e) {
        const file = e.target.files[0];
        if (!file) return;
        
        // Vérifier le type
        if (!file.type.startsWith('image/')) {
            alert('⚠️ Veuillez sélectionner une image');
            e.target.value = '';
            return;
        }
        
        // Vérifier la taille (5MB max)
        if (file.size > 5 * 1024 * 1024) {
            alert('⚠️ L\'image ne doit pas dépasser 5MB');
            e.target.value = '';
            return;
        }
        
        // Prévisualiser
        const reader = new FileReader();
        reader.onload = (event) => {
            let preview = document.getElementById('image-preview');
            
            if (!preview) {
                preview = document.createElement('img');
                preview.id = 'image-preview';
                preview.style.cssText = 'max-width: 300px; max-height: 200px; margin-top: 10px; border-radius: 8px; border: 2px solid #ddd;';
                this.input.parentNode.appendChild(preview);
            }
            
            preview.src = event.target.result;
        };
        
        reader.readAsDataURL(file);
    }
}

// ============================================
// 🎉 NOTIFICATION DE SUCCÈS D'INSCRIPTION
// ============================================
function checkInscriptionSuccess() {
    const params = new URLSearchParams(window.location.search);
    
    if (params.get('success') === 'inscription') {
        // Attendre que notificationManager soit prêt
        const checkInterval = setInterval(() => {
            if (window.notificationManager) {
                clearInterval(checkInterval);
                
                setTimeout(() => {
                    window.notificationManager.success(
                        '🎉 Inscription réussie ! Vous recevrez une confirmation par email.',
                        8000
                    );
                }, 500);
                
                // Nettoyer l'URL
                const url = new URL(window.location);
                url.searchParams.delete('success');
                window.history.replaceState({}, document.title, url.pathname + url.search);
            }
        }, 100);
        
        // Timeout après 3 secondes
        setTimeout(() => clearInterval(checkInterval), 3000);
    }
}

// ============================================
// INITIALISATION AUTOMATIQUE
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    console.log('📋 Initialisation du système de validation...');
    
    // Détecter et initialiser les formulaires
    const forms = {
        'event-form': 'Formulaire événement',
        'inscription-form': 'Formulaire inscription',
        'edit-inscription-form': 'Modification inscription'
    };
    
    Object.keys(forms).forEach(formId => {
        if (document.getElementById(formId)) {
            new FormValidator(formId);
            console.log(`✅ ${forms[formId]} initialisé`);
        }
    });
    
    // Initialiser prévisualisation image
    if (document.getElementById('image')) {
        new ImagePreview('image');
        console.log('✅ Prévisualisation image initialisée');
    }
    
    // ✅ Vérifier succès d'inscription
    checkInscriptionSuccess();
    
    console.log('✅ Système de validation prêt');
});

// ============================================
// STYLES CSS POUR LA VALIDATION
// ============================================
const validationStyles = `
<style>
/* Champs en erreur */
.error {
    border-color: #dc3545 !important;
    animation: shake 0.3s;
}

/* Champs valides */
.valid {
    border-color: #28a745 !important;
}

/* Animation shake */
@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-5px); }
    75% { transform: translateX(5px); }
}

/* Messages d'erreur par champ */
.field-error-message {
    display: flex;
    align-items: center;
    gap: 5px;
    color: #dc3545;
    font-size: 0.875rem;
    font-weight: 600;
    margin-top: 5px;
    animation: slideIn 0.3s;
}

.error-icon {
    font-size: 1rem;
}

/* Animation slide in */
@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Messages globaux */
.alert {
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    animation: slideIn 0.3s;
}

.alert-error {
    background: #fee;
    border: 1px solid #fcc;
    color: #c00;
}

.alert-error strong {
    display: block;
    margin-bottom: 10px;
}

.alert-error ul {
    margin: 10px 0 0 20px;
}

.alert-error li {
    margin: 5px 0;
}

/* Compteurs de caractères */
.char-counter {
    text-align: right;
    font-size: 0.875rem;
    font-weight: 600;
    margin-top: 5px;
    transition: color 0.3s;
}
</style>
`;

// Injecter les styles si pas déjà présents
if (!document.getElementById('validation-styles')) {
    const styleElement = document.createElement('div');
    styleElement.id = 'validation-styles';
    styleElement.innerHTML = validationStyles;
    document.head.appendChild(styleElement.firstElementChild);
}