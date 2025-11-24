<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AidConnect Dashboard</title>

    <!-- CSS -->
    <link rel="stylesheet" href="dashboard.css">
</head>

<body>

<div class="layout">

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <h3>AidConnect</h3>
        <nav>

            <!-- USER MODULE -->
            <button class="sidebar-link" onclick="loadContent('/projet-web/Projet/Controller/usercontroller.php?action=list')">
                👥 Utilisateurs
            </button>

            <!-- POST MODULE -->
            <button class="sidebar-link" onclick="loadContent('showpostback.php')">
                📝 Posts
            </button>

            <!-- UPDATE POST -->
            <button class="sidebar-link" onclick="loadContent('showpostupdateback.php')">
                ✅ Update Posts
            </button>

            <!-- OFFER MODULE -->
            <button class="sidebar-link" onclick="loadContent('showoffer.php')">
                🏷️ Offres
            </button>

            <!-- UPDATE OFFER RESPONSES -->
            <button class="sidebar-link" onclick="loadContent('showupdateoffer.php?embed=1')">
                ✏️ Updates Offres
            </button>

            <!-- RECLAMATIONS -->
            <button class="sidebar-link" onclick="loadContent('/projet-web/Projet/Controller/reclamationcontroller.php?action=list')">
                📩 Réclamations
            </button>

            <!-- EVENTS -->
            <button class="sidebar-link" onclick="loadContent('/projet-web/Projet/Controller/eventcontroller.php?action=list')">
                📅 Événements
            </button>

        </nav>
    </aside>

    <!-- MAIN CONTENT AREA -->
    <div class="main-content" id="content-area">
        <h1>Bienvenue dans le Dashboard AidConnect 👋</h1>
        <p>Sélectionnez un module dans le menu à gauche.</p>
    </div>

</div>


<!-- JAVASCRIPT -->
<script>
function loadContent(page) {
    document.getElementById("content-area").innerHTML = 
        "<h2 style='opacity:.6'>Chargement...</h2>";

    fetch(page)
        .then(response => {
            if (!response.ok) {
                throw new Error("Erreur " + response.status);
            }
            return response.text();
        })
        .then(data => {
            document.getElementById("content-area").innerHTML = data;
            if (typeof initOfferFragments === 'function') {
                initOfferFragments();
            }
            if (typeof initUpdateOfferFragments === 'function') {
                initUpdateOfferFragments();
            }
            if (typeof initAddOfferFragments === 'function') {
                initAddOfferFragments();
            }
        })
        .catch(err => {
            document.getElementById("content-area").innerHTML =
                "<p style='color:red; font-size:18px;'>Erreur : " + err + "</p>";
        });
}
</script>
<script src="offer.js"></script>
<script src="updateoffer.js"></script>
<script src="add_offer.js"></script>

<!-- No auto-load: offers will load when you click the 'Offres' button -->

</body>
</html>
