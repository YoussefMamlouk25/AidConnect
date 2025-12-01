/**
 * Système de validation JavaScript universel pour tous les formulaires
 * Compatible avec Bootstrap et SocialV-Lite
 */

(function() {
    'use strict';

    // Configuration des règles de validation
    const validationRules = {
        // Règles communes
        nom: {
            minLength: 2,
            maxLength: 100,
            pattern: /^[a-zA-ZÀ-ÿ\s'-]+$/,
            message: {
                required: "Le nom est requis.",
                minLength: "Le nom doit contenir au moins 2 caractères.",
                maxLength: "Le nom ne doit pas dépasser 100 caractères.",
                pattern: "Le nom ne doit contenir que des lettres, espaces, tirets et apostrophes."
            }
        },
        email: {
            pattern: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
            message: {
                required: "L'email est requis.",
                pattern: "Veuillez entrer une adresse email valide."
            }
        },
        numero: {
            pattern: /^[0-9]{8,15}$/,
            message: {
                required: "Le numéro de téléphone est requis.",
                pattern: "Le numéro doit contenir entre 8 et 15 chiffres."
            }
        },
        titre: {
            minLength: 3,
            maxLength: 200,
            message: {
                required: "Le titre est requis.",
                minLength: "Le titre doit contenir au moins 3 caractères.",
                maxLength: "Le titre ne doit pas dépasser 200 caractères."
            }
        },
        description: {
            minLength: 10,
            maxLength: 2000,
            message: {
                required: "La description est requise.",
                minLength: "La description doit contenir au moins 10 caractères.",
                maxLength: "La description ne doit pas dépasser 2000 caractères."
            }
        },
        texte: {
            minLength: 10,
            maxLength: 2000,
            message: {
                required: "Le texte est requis.",
                minLength: "Le texte doit contenir au moins 10 caractères.",
                maxLength: "Le texte ne doit pas dépasser 2000 caractères."
            }
        },
        region: {
            message: {
                required: "Veuillez sélectionner une région."
            }
        },
        type_don: {
            message: {
                required: "Veuillez sélectionner un type de don."
            }
        },
        media: {
            allowedTypes: ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'],
            maxSize: 5 * 1024 * 1024, // 5MB
            message: {
                type: "Type de fichier non autorisé. Utilisez JPG, PNG ou GIF.",
                size: "La taille du fichier ne doit pas dépasser 5MB."
            }
        },
        photo: {
            allowedTypes: ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'],
            maxSize: 5 * 1024 * 1024, // 5MB
            message: {
                type: "Type de fichier non autorisé. Utilisez JPG, PNG ou GIF.",
                size: "La taille du fichier ne doit pas dépasser 5MB."
            }
        },
        intervention: {
            minLength: 2,
            maxLength: 100,
            message: {
                required: "Le champ 'Intervention réalisée par' est requis.",
                minLength: "Ce champ doit contenir au moins 2 caractères.",
                maxLength: "Ce champ ne doit pas dépasser 100 caractères."
            }
        },
        joursReponse: {
            min: 0,
            max: 365,
            message: {
                required: "Le temps de réponse est requis.",
                min: "Le temps de réponse doit être supérieur ou égal à 0.",
                max: "Le temps de réponse ne doit pas dépasser 365 jours."
            }
        },
        joursResolution: {
            min: 0,
            max: 365,
            message: {
                required: "Le temps de résolution est requis.",
                min: "Le temps de résolution doit être supérieur ou égal à 0.",
                max: "Le temps de résolution ne doit pas dépasser 365 jours."
            }
        }
    };

    /**
     * Affiche un message de validation
     */
    function showValidationMessage(input, message, isValid) {
        // Supprimer les messages précédents
        const existingFeedback = input.parentElement.querySelector('.validation-feedback');
        if (existingFeedback) {
            existingFeedback.remove();
        }

        // Supprimer les classes Bootstrap existantes
        input.classList.remove('is-valid', 'is-invalid');
        
        // Créer le message de feedback
        const feedback = document.createElement('div');
        feedback.className = `validation-feedback ${isValid ? 'valid-feedback' : 'invalid-feedback'}`;
        feedback.style.display = 'block';
        feedback.innerHTML = isValid 
            ? `<i class="ri-checkbox-circle-line me-1"></i>${message || 'Valide'}` 
            : `<i class="ri-error-warning-line me-1"></i>${message}`;
        
        input.parentElement.appendChild(feedback);
        input.classList.add(isValid ? 'is-valid' : 'is-invalid');
    }

    /**
     * Valide un champ selon ses règles
     */
    function validateField(input, rules) {
        const value = input.value.trim();
        const fieldName = input.name || input.id;
        const rule = validationRules[fieldName] || rules;

        if (!rule) return true;

        // Vérifier si le champ est requis
        if (input.hasAttribute('required') && !value) {
            showValidationMessage(input, rule.message?.required || 'Ce champ est requis.', false);
            return false;
        }

        // Si le champ n'est pas requis et est vide, le considérer comme valide
        if (!input.hasAttribute('required') && !value) {
            return true;
        }

        // Vérifier la longueur minimale
        if (rule.minLength && value.length < rule.minLength) {
            showValidationMessage(input, rule.message?.minLength, false);
            return false;
        }

        // Vérifier la longueur maximale
        if (rule.maxLength && value.length > rule.maxLength) {
            showValidationMessage(input, rule.message?.maxLength, false);
            return false;
        }

        // Vérifier le pattern
        if (rule.pattern && !rule.pattern.test(value)) {
            showValidationMessage(input, rule.message?.pattern, false);
            return false;
        }

        // Vérifier les valeurs min/max pour les nombres
        if (rule.min !== undefined && parseFloat(value) < rule.min) {
            showValidationMessage(input, rule.message?.min, false);
            return false;
        }

        if (rule.max !== undefined && parseFloat(value) > rule.max) {
            showValidationMessage(input, rule.message?.max, false);
            return false;
        }

        // Si tout est valide
        showValidationMessage(input, 'Valide', true);
        return true;
    }

    /**
     * Valide un fichier uploadé
     */
    function validateFile(input) {
        // Chercher la règle correspondant au nom du champ
        const fieldName = input.name || input.id;
        let rule = validationRules[fieldName] || validationRules.media || validationRules.photo;
        if (!rule) {
            // Règle par défaut pour les fichiers
            rule = {
                allowedTypes: ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'],
                maxSize: 5 * 1024 * 1024,
                message: {
                    type: "Type de fichier non autorisé. Utilisez JPG, PNG ou GIF.",
                    size: "La taille du fichier ne doit pas dépasser 5MB."
                }
            };
        }

        const files = input.files;
        if (files.length === 0) {
            // Si le fichier n'est pas requis, c'est OK
            if (!input.hasAttribute('required')) {
                return true;
            }
            showValidationMessage(input, 'Veuillez sélectionner un fichier.', false);
            return false;
        }

        for (let i = 0; i < files.length; i++) {
            const file = files[i];

            // Vérifier le type
            if (rule.allowedTypes && !rule.allowedTypes.includes(file.type)) {
                showValidationMessage(input, rule.message.type, false);
                return false;
            }

            // Vérifier la taille
            if (rule.maxSize && file.size > rule.maxSize) {
                showValidationMessage(input, rule.message.size, false);
                return false;
            }
        }

        showValidationMessage(input, 'Fichier valide', true);
        return true;
    }

    /**
     * Initialise la validation pour un formulaire
     */
    function initFormValidation(formSelector) {
        const form = typeof formSelector === 'string' 
            ? document.querySelector(formSelector) 
            : formSelector;

        if (!form) return;

        // Récupérer tous les champs du formulaire
        const inputs = form.querySelectorAll('input, select, textarea');
        
        // Validation en temps réel
        inputs.forEach(input => {
            // Ignorer les boutons et les champs cachés
            if (input.type === 'submit' || input.type === 'button' || input.type === 'hidden') {
                return;
            }

            // Validation pour les fichiers
            if (input.type === 'file') {
                input.addEventListener('change', () => {
                    validateFile(input);
                });
            }
            // Validation pour les selects
            else if (input.tagName === 'SELECT') {
                input.addEventListener('change', () => {
                    validateField(input);
                });
                input.addEventListener('blur', () => {
                    validateField(input);
                });
            }
            // Validation pour les autres champs
            else {
                // Validation au blur (quand on quitte le champ)
                input.addEventListener('blur', () => {
                    validateField(input);
                });

                // Validation en temps réel pour certains champs
                if (input.type === 'email' || input.type === 'tel' || input.name === 'numero' || 
                    input.name === 'titre' || input.name === 'nom' || input.name === 'description' || 
                    input.name === 'texte' || input.name === 'intervention') {
                    input.addEventListener('input', () => {
                        if (input.value.trim().length > 0) {
                            validateField(input);
                        } else {
                            // Supprimer le message si le champ est vide
                            const existingFeedback = input.parentElement.querySelector('.validation-feedback');
                            if (existingFeedback) {
                                existingFeedback.remove();
                            }
                            input.classList.remove('is-valid', 'is-invalid');
                        }
                    });
                }
            }
        });

        // Validation à la soumission
        form.addEventListener('submit', function(e) {
            let isValid = true;

            inputs.forEach(input => {
                if (input.type === 'submit' || input.type === 'button' || input.type === 'hidden') {
                    return;
                }

                if (input.type === 'file') {
                    if (!validateFile(input)) {
                        isValid = false;
                    }
                } else {
                    if (!validateField(input)) {
                        isValid = false;
                    }
                }
            });

            if (!isValid) {
                e.preventDefault();
                e.stopPropagation();
                
                // Afficher une alerte Bootstrap
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-danger alert-dismissible fade show';
                alertDiv.innerHTML = `
                    <i class="ri-error-warning-line me-2"></i>
                    <strong>Erreur de validation !</strong> Veuillez corriger les erreurs dans le formulaire.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                form.insertBefore(alertDiv, form.firstChild);
                
                // Scroll vers le premier champ invalide
                const firstInvalid = form.querySelector('.is-invalid');
                if (firstInvalid) {
                    firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    firstInvalid.focus();
                }
            } else {
                // Désactiver le bouton de soumission pour éviter les doubles soumissions
                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Envoi en cours...';
                }
            }

            form.classList.add('was-validated');
        });
    }

    // Auto-initialisation quand le DOM est prêt
    document.addEventListener('DOMContentLoaded', function() {
        // Initialiser pour tous les formulaires
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            initFormValidation(form);
        });
    });

    // Exposer la fonction pour une utilisation manuelle
    window.initFormValidation = initFormValidation;
    window.validateField = validateField;
    window.validateFile = validateFile;

})();

