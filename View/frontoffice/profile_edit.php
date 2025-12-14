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
$success = $_GET['success'] ?? '';

// ===== PROFILE PIC =====
$storedProfile = trim($user['profile_pic'] ?? '');
$profilePic = 'assets/images/default_profile.png';

if ($storedProfile !== '') {
    $normalized = ltrim($storedProfile, '/');
    $absolutePath = realpath(__DIR__ . '/../../' . $normalized);
    if ($absolutePath !== false && file_exists($absolutePath)) {
        $profilePic = $relativePath;
    if ($absolutePath && file_exists($absolutePath)) {
        $profilePic = '../../' . $normalized;
    }
}

// Format date for input field
$birthDate = !empty($user['date_naissance']) ? date('Y-m-d', strtotime($user['date_naissance'])) : '';

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

<style>
    /* Consistent with profile.php */
    .profile-main-container {
        min-height: 80vh;
        padding: 2rem 0;
        background: #f8f9fa;
    }
    
    .profile-mega-card {
        border-radius: 25px;
        border: none;
        box-shadow: 0 15px 50px rgba(0,0,0,0.1);
        overflow: hidden;
        margin-bottom: 2rem;
    }
    
    .profile-mega-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 4rem 2rem !important;
        color: white;
        position: relative;
        overflow: hidden;
    }
    
    .profile-mega-picture {
        width: 200px !important;
        height: 200px !important;
        border: 8px solid rgba(255,255,255,0.3);
        box-shadow: 0 15px 35px rgba(0,0,0,0.2);
        margin-bottom: 1.5rem;
        object-fit: cover;
    }
    
    .profile-mega-name {
        font-size: 2.8rem !important;
        font-weight: 800;
        margin-bottom: 0.5rem;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
    }
    
    .edit-section-title {
        font-size: 1.8rem !important;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 4px solid #667eea;
    }
    
    .edit-form-card {
        background: white;
        border-radius: 20px;
        padding: 2.5rem !important;
        margin-bottom: 2rem;
        border: none;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
    }
    
    .form-group-enhanced {
        margin-bottom: 1.8rem;
    }
    
    .form-label-enhanced {
        font-size: 1rem;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 0.8rem;
        display: flex;
        align-items: center;
    }
    
    .form-label-enhanced i {
        margin-right: 10px;
        color: #667eea;
        font-size: 1.2rem;
    }
    
    .form-control-enhanced {
        border: 2px solid #e0e0e0;
        border-radius: 12px;
        padding: 0.9rem 1.2rem;
        font-size: 1.1rem;
        transition: all 0.3s ease;
    }
    
    .form-control-enhanced:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.2);
        transform: translateY(-2px);
    }
    
    .file-upload-container {
        border: 2px dashed #667eea;
        border-radius: 15px;
        padding: 2rem;
        text-align: center;
        background: #f8f9ff;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-top: 0.5rem;
    }
    
    .file-upload-container:hover {
        background: #eef2ff;
        border-color: #764ba2;
    }
    
    .file-upload-label {
        display: flex;
        flex-direction: column;
        align-items: center;
        cursor: pointer;
    }
    
    .file-upload-label i {
        font-size: 3rem;
        color: #667eea;
        margin-bottom: 1rem;
    }
    
    .file-upload-text {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 0.5rem;
    }
    
    .file-upload-hint {
        color: #6c757d;
        font-size: 0.9rem;
    }
    
    .btn-mega {
        padding: 1.2rem 3rem !important;
        font-size: 1.3rem !important;
        border-radius: 15px;
        font-weight: 700;
        min-width: 220px;
        text-align: center;
        transition: all 0.3s ease;
    }
    
    .btn-save {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: white;
    }
    
    .btn-save:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        color: white;
    }
    
    .btn-cancel {
        border: 2px solid #667eea;
        color: #667eea;
    }
    
    .btn-cancel:hover {
        background: #667eea;
        color: white;
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(102, 126, 234, 0.2);
    }
    
    .alert-mega-success {
        border-radius: 20px;
        padding: 1.8rem;
        font-size: 1.2rem;
        box-shadow: 0 10px 30px rgba(40, 167, 69, 0.2);
        margin-bottom: 3rem;
    }
    
    .alert-mega-danger {
        border-radius: 20px;
        padding: 1.8rem;
        font-size: 1.2rem;
        box-shadow: 0 10px 30px rgba(220, 53, 69, 0.2);
        margin-bottom: 3rem;
    }
    
    .current-pic-info {
        background: #f8f9fa;
        padding: 1rem;
        border-radius: 10px;
        margin-top: 1rem;
        font-size: 0.9rem;
        color: #6c757d;
    }
    
    @media (max-width: 768px) {
        .profile-mega-picture {
            width: 150px !important;
            height: 150px !important;
        }
        
        .profile-mega-name {
            font-size: 2.2rem !important;
        }
        
        .btn-mega {
            min-width: 100%;
            margin-bottom: 1rem;
        }
    }
</style>

<div class="profile-main-container">
    <div class="container">
        <!-- Success Message -->
        <?php if ($success): ?>
            <div class="alert alert-success alert-mega-success alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-center">
                    <i class="ri-checkbox-circle-fill me-3" style="font-size: 2rem;"></i>
                    <div class="flex-grow-1">
                        <h4 class="mb-1">Succès !</h4>
                        <p class="mb-0"><?= htmlspecialchars($success) ?></p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <!-- Error Message -->
        <?php if ($error): ?>
            <div class="alert alert-danger alert-mega-danger alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-center">
                    <i class="ri-error-warning-fill me-3" style="font-size: 2rem;"></i>
                    <div class="flex-grow-1">
                        <h4 class="mb-1">Erreur !</h4>
                        <p class="mb-0"><?= htmlspecialchars($error) ?></p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Main Edit Card -->
        <div class="card profile-mega-card">
            <!-- Profile Header -->
            <div class="profile-mega-header text-center">
                <img src="<?= htmlspecialchars($profilePic) ?>" 
                     alt="Photo de profil" 
                     class="profile-mega-picture rounded-circle">
                
                <h1 class="profile-mega-name">Modifier le profil</h1>
                
                <p class="mt-3" style="font-size: 1.2rem; opacity: 0.9;">
                    <i class="ri-edit-2-line me-2"></i>
                    Mettez à jour vos informations personnelles
                </p>
            </div>

            <!-- Card Body -->
            <div class="card-body p-4 p-md-5">
                <!-- Edit Form -->
                <div class="edit-form-card">
                    <h2 class="edit-section-title">
                        <i class="ri-user-settings-line me-3"></i>
                        INFORMATIONS PERSONNELLES
                    </h2>
                    
                    <form method="post"
                          action="../../Controller/profile_update_controller.php"
                          enctype="multipart/form-data"
                          class="row g-4">

                        <input type="hidden" name="current_pic"
                               value="<?= htmlspecialchars($user['profile_pic'] ?? '') ?>">

                        <!-- Name -->
                        <div class="col-md-6">
                            <div class="form-group-enhanced">
                                <label class="form-label-enhanced">
                                    <i class="ri-user-line"></i>
                                    Nom complet
                                </label>
                                <input type="text" 
                                       name="name" 
                                       class="form-control form-control-enhanced"
                                       value="<?= htmlspecialchars($user['name']) ?>" 
                                       required
                                       placeholder="Votre nom complet">
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="col-md-6">
                            <div class="form-group-enhanced">
                                <label class="form-label-enhanced">
                                    <i class="ri-mail-line"></i>
                                    Email
                                </label>
                                <input type="email" 
                                       name="email" 
                                       class="form-control form-control-enhanced"
                                       value="<?= htmlspecialchars($user['email']) ?>" 
                                       required
                                       placeholder="votre@email.com">
                            </div>
                        </div>

                        <!-- Phone -->
                        <div class="col-md-6">
                            <div class="form-group-enhanced">
                                <label class="form-label-enhanced">
                                    <i class="ri-phone-line"></i>
                                    Téléphone
                                </label>
                                <input type="text" 
                                       name="tel" 
                                       class="form-control form-control-enhanced"
                                       value="<?= htmlspecialchars($user['tel'] ?? '') ?>"
                                       placeholder="+212 6 XX XX XX XX">
                            </div>
                        </div>

                        <!-- CIN -->
                        <div class="col-md-6">
                            <div class="form-group-enhanced">
                                <label class="form-label-enhanced">
                                    <i class="ri-id-card-line"></i>
                                    CIN
                                </label>
                                <input type="text" 
                                       name="cin" 
                                       class="form-control form-control-enhanced"
                                       value="<?= htmlspecialchars($user['cin'] ?? '') ?>"
                                       placeholder="Votre numéro CIN">
                            </div>
                        </div>

                        <!-- Region -->
                        <div class="col-md-6">
                            <div class="form-group-enhanced">
                                <label class="form-label-enhanced">
                                    <i class="ri-map-pin-line"></i>
                                    Région
                                </label>
                                <input type="text" 
                                       name="region" 
                                       class="form-control form-control-enhanced"
                                       value="<?= htmlspecialchars($user['region'] ?? '') ?>"
                                       placeholder="Votre région">
                            </div>
                        </div>

                        <!-- Birth Date -->
                        <div class="col-md-6">
                            <div class="form-group-enhanced">
                                <label class="form-label-enhanced">
                                    <i class="ri-calendar-line"></i>
                                    Date de naissance
                                </label>
                                <input type="date" 
                                       name="date_naissance" 
                                       class="form-control form-control-enhanced"
                                       value="<?= htmlspecialchars($birthDate) ?>">
                            </div>
                        </div>

                        <!-- Profile Picture Upload -->
                        <div class="col-12">
                            <div class="form-group-enhanced">
                                <label class="form-label-enhanced">
                                    <i class="ri-camera-line"></i>
                                    Photo de profil
                                </label>
                                
                                <div class="file-upload-container">
                                    <label class="file-upload-label">
                                        <input type="file" 
                                               name="profile_pic" 
                                               class="d-none" 
                                               accept="image/*"
                                               onchange="previewImage(event)">
                                        <i class="ri-upload-cloud-2-line"></i>
                                        <div class="file-upload-text">
                                            Cliquez pour télécharger une nouvelle photo
                                        </div>
                                        <div class="file-upload-hint">
                                            Formats acceptés : JPG, PNG, GIF • Taille max : 5MB
                                        </div>
                                    </label>
                                </div>
                                
                                <!-- Image Preview -->
                                <div id="imagePreview" class="text-center mt-3 d-none">
                                    <img id="preview" 
                                         class="rounded-circle border"
                                         style="width: 120px; height: 120px; object-fit: cover;"
                                         alt="Aperçu de la nouvelle photo">
                                    <p class="text-muted mt-2">Aperçu de la nouvelle photo</p>
                                </div>
                                
                                <!-- Current Picture Info -->
                                <?php if (!empty($user['profile_pic'])): ?>
                                    <div class="current-pic-info">
                                        <i class="ri-information-line me-2"></i>
                                        Photo actuelle : <?= htmlspecialchars(basename($user['profile_pic'])) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="col-12 mt-5">
                            <div class="d-flex flex-wrap gap-4 justify-content-center">
                                <a href="profile.php" class="btn btn-cancel btn-mega">
                                    <i class="ri-close-line me-3"></i>
                                    ANNULER
                                </a>
                                <button type="submit" class="btn btn-save btn-mega">
                                    <i class="ri-save-3-line me-3"></i>
                                    ENREGISTRER LES MODIFICATIONS
                                </button>
                            </div>
                        </div>

                    </form>
                </div>
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
    // Image preview function
    function previewImage(event) {
        const input = event.target;
        const preview = document.getElementById('preview');
        const previewContainer = document.getElementById('imagePreview');
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                preview.src = e.target.result;
                previewContainer.classList.remove('d-none');
            }
            
            reader.readAsDataURL(input.files[0]);
        } else {
            previewContainer.classList.add('d-none');
        }
    }
    
    // Add animation to form inputs
    document.addEventListener('DOMContentLoaded', function() {
        const inputs = document.querySelectorAll('.form-control-enhanced');
        
        inputs.forEach(input => {
            // Add focus/blur effects
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });
            
            input.addEventListener('blur', function() {
                if (this.value === '') {
                    this.parentElement.classList.remove('focused');
                }
            });
            
            // Check if input has value on load
            if (input.value !== '') {
                input.parentElement.classList.add('focused');
            }
        });
    });
</script>

<?php
$content = ob_get_clean();

$styles = [
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css',
    'https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css'
];

$scripts = [
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js'
];

renderTemplate('Modifier le profil | AidConnect', $content, $styles, $scripts);
