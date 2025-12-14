<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
session_start();
}
require_once '../../Model/user.php';
require_once 'layout.php';

if (!isset($_SESSION['id_user'])) {
    header('Location: login.php');
    exit;
}

$model = new UserModel();
$user  = $model->getUserById($_SESSION['id_user']);

if (!$user || !is_array($user) || empty($user['id_user'])) {
    session_unset();
    session_destroy();
    header('Location: login.php?error=' . urlencode('Utilisateur introuvable.'));
    exit;
}

$error = $_GET['error'] ?? '';
$storedProfile = trim($user['profile_pic'] ?? '');
$profilePic = 'assets/images/default_profile.png';

if ($storedProfile !== '') {
    $normalized = ltrim($storedProfile, '/');
    $relativePath = '../../' . $normalized;
    $absolutePath = realpath(__DIR__ . '/../../' . $normalized);
    if ($absolutePath !== false && file_exists($absolutePath)) {
        $profilePic = $relativePath;
    }
}

ob_start();
?>
<div class="row justify-content-center">
    <div class="col-lg-10 col-xl-8">
        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <i class="ri-error-warning-line me-2"></i><?= htmlspecialchars($error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Profile Edit Cardd -->
        <div class="card iq-card shadow-sm border-0">
            <div class="card-header bg-white border-0 pb-3">
                <div class="d-flex align-items-center justify-content-between">
                    <h5 class="mb-0 fw-bold">
                        <i class="ri-edit-line me-2 text-primary"></i>Modifier mon profil
                    </h5>
                    <a href="profile.php" class="btn btn-sm btn-outline-secondary">
                        <i class="ri-arrow-left-line me-1"></i>Retour
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Profile Picture Section -->
                <div class="text-center mb-4 pb-4 border-bottom">
                    <div class="profile-edit-avatar-wrapper d-inline-block position-relative">
                        <img src="<?= htmlspecialchars($profilePic) ?>" 
                             alt="Photo de profil" 
                             class="profile-edit-avatar rounded-circle border border-4 border-white shadow-lg" 
                             style="width: 150px; height: 150px; object-fit: cover; background: #f8f9fa;">
                        <label for="profile_pic" class="profile-edit-overlay rounded-circle d-flex align-items-center justify-content-center">
                            <i class="ri-camera-line fs-4 text-white"></i>
                        </label>
                    </div>
                    <p class="text-muted small mt-3 mb-0">Cliquez sur l'image pour changer votre photo de profil</p>
                </div>

                <form method="post" action="../../Controller/profile_update_controller.php" enctype="multipart/form-data" id="profileEditForm">
                    <input type="hidden" name="current_pic" value="<?= htmlspecialchars($user['profile_pic'] ?? '') ?>">
                    <input type="file" name="profile_pic" id="profile_pic" class="d-none" accept="image/*" onchange="previewProfilePic(this)">

                    <div class="row g-4">
                    <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label fw-semibold">
                                    <i class="ri-user-line me-1 text-primary"></i>Nom complet <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       name="name" 
                                       class="form-control form-control-lg" 
                                       value="<?= htmlspecialchars($user['name']) ?>" 
                                       required
                                       placeholder="Votre nom complet">
                            </div>
                    </div>

                    <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label fw-semibold">
                                    <i class="ri-mail-line me-1 text-primary"></i>Email <span class="text-danger">*</span>
                                </label>
                                <input type="email" 
                                       name="email" 
                                       class="form-control form-control-lg" 
                                       value="<?= htmlspecialchars($user['email']) ?>" 
                                       required
                                       placeholder="votre@email.com">
                            </div>
                    </div>

                    <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label fw-semibold">
                                    <i class="ri-phone-line me-1 text-primary"></i>Téléphone
                                </label>
                                <input type="text" 
                                       name="tel" 
                                       class="form-control form-control-lg" 
                                       value="<?= htmlspecialchars($user['tel'] ?? '') ?>" 
                                       placeholder="8 chiffres"
                                       pattern="[0-9]{8}"
                                       maxlength="8">
                                <small class="text-muted">Format: 8 chiffres</small>
                            </div>
                    </div>

                    <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label fw-semibold">
                                    <i class="ri-id-card-line me-1 text-primary"></i>CIN
                                </label>
                                <input type="text" 
                                       name="cin" 
                                       class="form-control form-control-lg" 
                                       value="<?= htmlspecialchars($user['cin'] ?? '') ?>" 
                                       placeholder="8 chiffres"
                                       pattern="[0-9]{8}"
                                       maxlength="8">
                                <small class="text-muted">Format: 8 chiffres</small>
                            </div>
                    </div>

                    <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label fw-semibold">
                                    <i class="ri-map-pin-line me-1 text-primary"></i>Région
                                </label>
                                <input type="text" 
                                       name="region" 
                                       class="form-control form-control-lg" 
                                       value="<?= htmlspecialchars($user['region'] ?? '') ?>" 
                                       placeholder="Votre région">
                            </div>
                    </div>

                    <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label fw-semibold">
                                    <i class="ri-calendar-2-line me-1 text-primary"></i>Date de naissance
                                </label>
                                <input type="date" 
                                       name="date_naissance" 
                                       class="form-control form-control-lg" 
                                       value="<?= htmlspecialchars($user['date_naissance'] ?? '') ?>">
        </div>
    </div>
</div>

                    <div class="d-flex gap-2 justify-content-end mt-4 pt-4 border-top">
                        <a href="profile.php" class="btn btn-outline-secondary btn-lg">
                            <i class="ri-close-line me-1"></i>Annuler
                        </a>
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="ri-save-line me-1"></i>Enregistrer les modifications
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function previewProfilePic(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.querySelector('.profile-edit-avatar').src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

document.querySelector('.profile-edit-overlay').addEventListener('click', function(e) {
    e.preventDefault();
    document.getElementById('profile_pic').click();
});
</script>
<?php
$content = ob_get_clean();
renderLayout('Modifier le profil', $content, ['assets/css/profile.css'], ['assets/js/profile-fix.js']);
