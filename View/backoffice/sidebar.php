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
        <button class="sidebar-link" onclick="loadContent('/projet-web/Projet/Controller/offercontroller.php?action=list')">
            🏷️ Offres
        </button>

        <!-- UPDATE OFFER -->
        <button class="sidebar-link" onclick="loadContent('/projet-web/Projet/Controller/offercontroller.php?action=update')">
            ✏️ Update Offre
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
