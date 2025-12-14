/**
 * Dark Mode Toggle Script
 * Gère le basculement entre mode clair et mode sombre
 */

(function() {
    'use strict';

    // Vérifier la préférence sauvegardée ou utiliser le mode clair par défaut
    const getStoredTheme = () => {
        const stored = localStorage.getItem('theme');
        return stored || 'light';
    };

    // Convertir les couleurs blanches en couleurs sombres
    const convertWhiteToDark = (element) => {
        if (!element || !element.style) return;
        
        const style = element.style;
        const whitePatterns = [
            { pattern: /white/gi, replacement: '#242526' },
            { pattern: /#fff\b/gi, replacement: '#242526' },
            { pattern: /#ffffff/gi, replacement: '#242526' },
            { pattern: /rgb\(255,\s*255,\s*255\)/gi, replacement: 'rgb(36, 37, 38)' },
            { pattern: /rgba\(255,\s*255,\s*255,\s*[^)]+\)/gi, replacement: 'rgba(36, 37, 38, 1)' }
        ];
        
        // Vérifier background
        if (style.background || style.backgroundColor) {
            let bg = style.background || style.backgroundColor;
            whitePatterns.forEach(({ pattern, replacement }) => {
                bg = bg.replace(pattern, replacement);
            });
            if (bg !== (style.background || style.backgroundColor)) {
                style.background = bg;
                style.backgroundColor = bg;
            }
        }
    };

    // Appliquer le thème
    const applyTheme = (theme) => {
        const body = document.body;
        const html = document.documentElement;
        
        if (theme === 'dark') {
            body.classList.add('dark-mode');
            html.setAttribute('data-theme', 'dark');
            
            // Convertir les styles inline blancs après un court délai pour laisser le CSS s'appliquer
            setTimeout(() => {
                const allElements = document.querySelectorAll('*');
                allElements.forEach(convertWhiteToDark);
            }, 100);
        } else {
            body.classList.remove('dark-mode');
            html.setAttribute('data-theme', 'light');
        }
        
        // Sauvegarder la préférence
        localStorage.setItem('theme', theme);
        
        // Mettre à jour l'icône
        updateThemeIcon(theme);
    };

    // Mettre à jour l'icône du bouton
    const updateThemeIcon = (theme) => {
        const themeIcon = document.getElementById('themeIcon');
        if (themeIcon) {
            if (theme === 'dark') {
                themeIcon.classList.remove('ri-moon-line');
                themeIcon.classList.add('ri-sun-line');
            } else {
                themeIcon.classList.remove('ri-sun-line');
                themeIcon.classList.add('ri-moon-line');
            }
        }
    };

    // Basculer le thème
    const toggleTheme = () => {
        const currentTheme = getStoredTheme();
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        applyTheme(newTheme);
    };

    // Initialiser le thème au chargement de la page
    const initTheme = () => {
        const theme = getStoredTheme();
        applyTheme(theme);
    };

    // Attendre que le DOM soit chargé
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initTheme);
    } else {
        initTheme();
    }

    // Ajouter l'événement au bouton de toggle
    document.addEventListener('DOMContentLoaded', function() {
        const themeToggleBtn = document.getElementById('themeToggleBtn');
        if (themeToggleBtn) {
            themeToggleBtn.addEventListener('click', toggleTheme);
        }
    });

    // Exposer la fonction toggleTheme globalement si nécessaire
    window.toggleTheme = toggleTheme;
})();

