<div class="d-flex flex-column flex-shrink-0 p-3 text-white bg-dark" style="width: 280px; min-height: 100vh;">
    <a href="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
      <span class="fs-4">Admin Panel</span>
    </a>
    <hr>
    <ul class="nav nav-pills flex-column mb-auto">
      <li class="nav-item">
        <a href="#" class="nav-link text-white" id="nav-dashboard" onclick="showSection('dashboard'); return false;">
          <i class="fas fa-chart-line me-2"></i>
          Dashboard
        </a>
      </li>
      <li class="nav-item">
        <a href="#" class="nav-link active" id="nav-reclamations" onclick="showSection('reclamations'); return false;">
          <i class="fas fa-list me-2"></i>
          Reclamations
        </a>
      </li>
      <li>
        <a href="#" class="nav-link text-white">
          <i class="fas fa-users me-2"></i>
          Users
        </a>
      </li>
      <li>
        <a href="#" class="nav-link text-white">
          <i class="fas fa-cog me-2"></i>
          Settings
        </a>
      </li>
    </ul>
    <hr>
    <div class="dropdown">
      <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
        <img src="https://github.com/mdo.png" alt="" width="32" height="32" class="rounded-circle me-2">
        <strong>Admin</strong>
      </a>
      <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser1">
        <li><a class="dropdown-item" href="#">Profile</a></li>
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item" href="#">Sign out</a></li>
      </ul>
    </div>
</div>
