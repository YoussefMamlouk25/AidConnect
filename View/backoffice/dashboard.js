// Fonction pour charger le contenu dans la zone principale (pour dashboard.php)
function loadContent(page) {
    const contentArea = document.getElementById("content-area");
    if (!contentArea) {
        // Si on n'est pas dans dashboard.php, rediriger vers la page
        window.location.href = page;
        return;
    }
    
    contentArea.innerHTML = "<h2 style='opacity:.6'>Chargement...</h2>";

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
        })
        .catch(err => {
            contentArea.innerHTML =
                "<p style='color:red; font-size:18px;'>Erreur : " + err + "</p>";
        });
}

