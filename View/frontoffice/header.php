<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Déterminer la page actuelle pour mettre en évidence le lien actif
$current_page = basename($_SERVER['PHP_SELF']);
$isAdmin = (($_SESSION['role'] ?? '') === 'admin');
?>
<header class="header bg-white shadow-sm p-3 mb-4">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-md-3">
                <a href="index.php" class="text-decoration-none d-flex align-items-center header-logo">
                    <div class="logo-icon-header">
                        <i class="ri-heart-pulse-fill"></i>
                    </div>
                    <h4 class="text-primary mb-0 ms-2">AidConnect</h4>
                </a>
            </div>
            <div class="col-md-6">
                <nav class="d-flex justify-content-center align-items-center flex-wrap gap-2">
                    <a href="index.php" class="btn btn-sm <?php echo ($current_page == 'index.php') ? 'btn-primary' : 'btn-outline-primary'; ?>">
                        <i class="ri-home-line me-1"></i> Accueil
                    </a>
                    
                    <!-- Posts Dropdown -->
                    <div class="nav-dropdown">
                        <button class="btn btn-sm <?php echo (in_array($current_page, ['showPosts.php', 'showpostupdate.php'])) ? 'btn-primary' : 'btn-outline-primary'; ?> nav-dropdown-toggle" type="button">
                            <i class="ri-file-list-3-line me-1"></i> Posts
                            <i class="ri-arrow-down-s-line ms-1"></i>
                        </button>
                        <div class="nav-dropdown-menu">
                            <a href="showPosts.php" class="nav-dropdown-item <?php echo ($current_page == 'showPosts.php') ? 'active' : ''; ?>">
                                <i class="ri-file-list-3-line me-2"></i> Tous les Posts
                            </a>
                            <a href="showpostupdate.php" class="nav-dropdown-item <?php echo ($current_page == 'showpostupdate.php') ? 'active' : ''; ?>">
                                <i class="ri-checkbox-circle-line me-2"></i> Posts Résolus
                            </a>
                        </div>
                    </div>
                    
                    <!-- Offres Dropdown -->
                    <div class="nav-dropdown">
                        <button class="btn btn-sm <?php echo (in_array($current_page, ['volunteer-view.php', 'update-offer-view.php'])) ? 'btn-primary' : 'btn-outline-primary'; ?> nav-dropdown-toggle" type="button">
                            <i class="ri-price-tag-3-line me-1"></i> Offres
                            <i class="ri-arrow-down-s-line ms-1"></i>
                        </button>
                        <div class="nav-dropdown-menu">
                            <a href="volunteer-view.php" class="nav-dropdown-item <?php echo ($current_page == 'volunteer-view.php') ? 'active' : ''; ?>">
                                <i class="ri-price-tag-3-line me-2"></i> Toutes les Offres
                            </a>
                            <a href="update-offer-view.php" class="nav-dropdown-item <?php echo ($current_page == 'update-offer-view.php') ? 'active' : ''; ?>">
                                <i class="ri-edit-box-line me-2"></i> Updates Offres
                            </a>
                        </div>
                    </div>
                </nav>
            </div>
            <div class="col-md-3 text-end">
                <div class="d-flex align-items-center gap-2 justify-content-end">
                    <button type="button" class="btn btn-sm btn-outline-primary search-icon-btn" id="searchIconBtn" onclick="toggleSearchCard()">
                        <i class="ri-search-line"></i>
                    </button>
                    <?php if ($isAdmin): ?>
                        <a href="../backoffice/dashboard.php" class="btn btn-sm btn-backoffice">
                            <i class="ri-dashboard-line me-1"></i> Backoffice
                        </a>
                    <?php endif; ?>
                    <a href="profile.php" class="btn btn-sm btn-outline-primary">
                        <i class="ri-user-line me-1"></i> Profile
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>

<style>
/* Backoffice Button Styling */
.btn-backoffice {
    border-radius: 20px !important;
    font-weight: 600 !important;
    padding: 16px 26px !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    border: none !important;
    box-sizing: border-box !important;
    line-height: 1.5 !important;
    font-size: 16px !important;
    min-height: 46px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    background: linear-gradient(135deg, #1a1a1a 0%, #000000 100%) !important;
    color: #ffffff !important;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.3) !important;
    position: relative !important;
    overflow: hidden !important;
    text-decoration: none !important;
}

.btn-backoffice::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.15), transparent);
    transition: left 0.5s;
}

.btn-backoffice:hover::before {
    left: 100%;
}

.btn-backoffice:hover {
    background: linear-gradient(135deg, #000000 0%, #1a1a1a 100%) !important;
    color: #ffffff !important;
    transform: translateY(-2px) !important;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.4) !important;
    text-decoration: none !important;
}

.btn-backoffice:active {
    transform: translateY(0) !important;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3) !important;
}

.btn-backoffice:focus {
    outline: none !important;
    box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.3) !important;
}

.btn-backoffice i {
    margin-right: 4px;
}

/* Header Logo Styles */
.header-logo {
    transition: transform 0.3s ease;
}

.header-logo:hover {
    transform: scale(1.05);
    text-decoration: none;
}

.logo-icon-header {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #0f5fcc 0%, #0d4fb0 100%);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    color: white;
    box-shadow: 0 3px 10px rgba(15, 95, 204, 0.3);
    transition: all 0.3s ease;
}

.header-logo:hover .logo-icon-header {
    transform: rotate(5deg) scale(1.1);
    box-shadow: 0 5px 15px rgba(15, 95, 204, 0.4);
}

.header-logo h4 {
    transition: color 0.3s ease;
}

.header-logo:hover h4 {
    color: #0d4fb0 !important;
}

/* Navigation Dropdown Styles */
.nav-dropdown {
    position: relative;
    display: inline-block;
}

.nav-dropdown-toggle {
    position: relative;
}

.nav-dropdown-toggle i.ri-arrow-down-s-line {
    font-size: 14px;
    transition: transform 0.3s ease;
}

.nav-dropdown.active .nav-dropdown-toggle i.ri-arrow-down-s-line {
    transform: rotate(180deg);
}

.nav-dropdown-menu {
    position: absolute;
    top: calc(100% + 8px);
    left: 0;
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
    min-width: 200px;
    opacity: 0;
    visibility: hidden;
    transform: translateY(-10px);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    z-index: 1000;
    padding: 8px 0;
    border: 1px solid #e4e6eb;
}

.nav-dropdown.active .nav-dropdown-menu {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.nav-dropdown-item {
    display: flex;
    align-items: center;
    padding: 12px 20px;
    color: #1c1e21;
    text-decoration: none;
    font-weight: 500;
    font-size: 15px;
    transition: all 0.2s ease;
    border-left: 3px solid transparent;
}

.nav-dropdown-item:hover {
    background-color: #f0f2f5;
    color: #1877f2;
    border-left-color: #1877f2;
    text-decoration: none;
}

.nav-dropdown-item.active {
    background-color: #e7f3ff;
    color: #1877f2;
    border-left-color: #1877f2;
    font-weight: 600;
}

.nav-dropdown-item i {
    font-size: 18px;
    width: 24px;
    text-align: center;
}
</style>

<script>
// Navigation Dropdown Toggle
document.addEventListener('DOMContentLoaded', function() {
    const dropdownToggles = document.querySelectorAll('.nav-dropdown-toggle');
    
    dropdownToggles.forEach(function(toggle) {
        toggle.addEventListener('click', function(e) {
            e.stopPropagation();
            const dropdown = this.closest('.nav-dropdown');
            const isActive = dropdown.classList.contains('active');
            
            // Close all other dropdowns
            document.querySelectorAll('.nav-dropdown').forEach(function(dd) {
                if (dd !== dropdown) {
                    dd.classList.remove('active');
                }
            });
            
            // Toggle current dropdown
            if (isActive) {
                dropdown.classList.remove('active');
            } else {
                dropdown.classList.add('active');
            }
        });
    });
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function(event) {
        if (!event.target.closest('.nav-dropdown')) {
            document.querySelectorAll('.nav-dropdown').forEach(function(dropdown) {
                dropdown.classList.remove('active');
            });
        }
    });
    
    // Close dropdowns on Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            document.querySelectorAll('.nav-dropdown').forEach(function(dropdown) {
                dropdown.classList.remove('active');
            });
        }
    });
});
</script>
