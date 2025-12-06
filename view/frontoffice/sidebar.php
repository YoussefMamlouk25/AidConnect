<div class="d-flex flex-column flex-shrink-0 p-3 text-white bg-primary" style="width: 280px; min-height: 100vh;">
    <a href="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
        <i class="fas fa-user-circle fa-2x me-2"></i>
        <span class="fs-4">My Portal</span>
    </a>
    <hr>
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
            <a href="#" class="nav-link text-white" id="nav-reclamations" onclick="showSection('reclamations'); return false;">
                <i class="fas fa-list me-2"></i>
                My Reclamations
            </a>
        </li>
        <li>
            <a href="#" class="nav-link text-white" id="nav-create" onclick="showSection('create'); return false;">
                <i class="fas fa-plus-circle me-2"></i>
                Create New
            </a>
        </li>
    </ul>
    <hr>
    <div class="dropdown">
        <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
            <img src="https://github.com/mdo.png" alt="" width="32" height="32" class="rounded-circle me-2">
            <strong>User</strong>
        </a>
        <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser1">
            <li><a class="dropdown-item" href="#">Profile</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="#">Sign out</a></li>
        </ul>
    </div>
</div>

<style>
    .nav-link.active {
        background-color: rgba(255, 255, 255, 0.2) !important;
        border-radius: 0.375rem;
    }
    .nav-link:hover {
        background-color: rgba(255, 255, 255, 0.1);
        border-radius: 0.375rem;
    }
</style>
