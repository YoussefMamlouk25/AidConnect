// Fonction pour charger le contenu dans la zone principale (pour dashboard.php)
function loadContent(page) {
    const contentArea = document.getElementById("content-area");
    if (!contentArea) {
        // Si on n'est pas dans dashboard.php, rediriger vers la page
        window.location.href = page;
        return;
    }
    
    // Afficher un loader Bootstrap
    contentArea.innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Chargement...</span>
            </div>
            <p class="mt-3 text-muted">Chargement...</p>
        </div>
    `;

    // Ajouter le paramètre ajax=1 pour une détection plus fiable
    const separator = page.includes('?') ? '&' : '?';
    const urlWithAjax = page + separator + 'ajax=1';
    
    fetch(urlWithAjax, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(response => {
            if (!response.ok) {
                throw new Error("Erreur " + response.status);
            }
            return response.text();
        })
        .then(data => {
            contentArea.innerHTML = data;
            // Initialiser les fragments si nécessaire
            if (typeof initOfferFragments === 'function') {
                initOfferFragments();
            }
            if (typeof initUpdateOfferFragments === 'function') {
                initUpdateOfferFragments();
            }
        })
        .catch(err => {
            contentArea.innerHTML = `
                <div class="alert alert-danger" role="alert">
                    <i class="ri-error-warning-line me-2"></i>
                    <strong>Erreur !</strong> ${err.message}
                </div>
            `;
        });
}

