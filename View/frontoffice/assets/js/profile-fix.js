/**
 * Script de correction pour empêcher le contenu de profile.php et profile_edit.php de disparaître
 */
(function() {
    'use strict';
    
    // Détecter si on est sur une page profile
    const isProfilePage = document.querySelector('.profile-header-card') !== null || 
                          document.querySelector('.profile-edit-avatar-wrapper') !== null;
    
    if (isProfilePage) {
        // Ajouter une classe au body pour le CSS
        document.body.classList.add('profile-page');
    }
    
    // Fonction pour s'assurer que le contenu reste visible
    function ensureContentVisible() {
        const wrapper = document.querySelector(".wrapper");
        const container = document.querySelector(".container-fluid");
        const profileCards = document.querySelectorAll(".iq-card");
        
        if (wrapper) {
            wrapper.style.display = "block";
            wrapper.style.visibility = "visible";
            wrapper.style.opacity = "1";
        }
        
        if (container) {
            container.style.display = "block";
            container.style.visibility = "visible";
            container.style.opacity = "1";
        }
        
        profileCards.forEach(function(card) {
            card.style.display = "block";
            card.style.visibility = "visible";
            card.style.opacity = "1";
        });
    }
    
    // Exécuter immédiatement
    ensureContentVisible();
    
    // Exécuter après le chargement du DOM
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', ensureContentVisible);
    } else {
        ensureContentVisible();
    }
    
    // Empêcher jQuery de cacher le contenu
    if (typeof jQuery !== 'undefined') {
        jQuery(document).ready(function() {
            ensureContentVisible();
            
            // Intercepter fadeOut sur wrapper et container-fluid
            const originalFadeOut = jQuery.fn.fadeOut;
            jQuery.fn.fadeOut = function(speed, callback) {
                const $this = jQuery(this);
                const isWrapper = $this.hasClass('wrapper') || $this.hasClass('container-fluid');
                
                if (isWrapper && $this.find('.iq-card').length > 0) {
                    // Ne pas permettre fadeOut sur wrapper/container qui contient profile
                    return $this;
                }
                
                return originalFadeOut.apply(this, arguments);
            };
            
            // Vérifier périodiquement
            setInterval(ensureContentVisible, 500);
        });
    } else {
        // Vérifier périodiquement même sans jQuery
        setInterval(ensureContentVisible, 500);
    }
    
    // Observer les changements de style
    if (typeof MutationObserver !== 'undefined') {
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'attributes' && mutation.attributeName === 'style') {
                    const target = mutation.target;
                    if (target.classList.contains('wrapper') || 
                        target.classList.contains('container-fluid') ||
                        target.classList.contains('iq-card')) {
                        ensureContentVisible();
                    }
                }
            });
        });
        
        const wrapper = document.querySelector(".wrapper");
        if (wrapper) {
            observer.observe(wrapper, {
                attributes: true,
                attributeFilter: ['style', 'class']
            });
        }
    }
})();

