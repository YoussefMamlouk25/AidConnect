<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once '../../config.php';
require_once '../../Model/Event.php';
require_once 'layout.php';

$error = '';
$success = '';
$event = null;

// Récupérer l'événement à modifier
if (!isset($_GET['id'])) {
    $error = "ID d'événement manquant";
} else {
    $db = Config::getConnexion();
    $eventModel = new Event($db);
    $eventModel->setIdEvent($_GET['id']);
    
    if (!$eventModel->readOne()) {
        $error = "Événement introuvable";
    } else {
        $event = $eventModel;
    }
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['titre']) && isset($_POST['id_event'])) {
    try {
        // Validation
        $errors = [];
        
        if(empty($_POST['titre']) || strlen(trim($_POST['titre'])) < 5) {
            $errors[] = "Le titre doit contenir au moins 5 caractères";
        }
        
        if(empty($_POST['description']) || strlen(trim($_POST['description'])) < 20) {
            $errors[] = "La description doit contenir au moins 20 caractères";
        }
        
        if(empty($_POST['date_event'])) {
            $errors[] = "La date est obligatoire";
        } else {
            $date = strtotime($_POST['date_event']);
            if($date < time()) {
                $errors[] = "La date doit être dans le futur";
            }
        }
        
        if(empty($_POST['lieu']) || strlen(trim($_POST['lieu'])) < 3) {
            $errors[] = "Le lieu doit contenir au moins 3 caractères";
        }
        
        if(empty($_POST['nombre_places']) || $_POST['nombre_places'] < 1) {
            $errors[] = "Le nombre de places doit être supérieur à 0";
        } elseif($_POST['nombre_places'] > 1000) {
            $errors[] = "Le nombre de places ne peut pas dépasser 1000";
        }
        
        if(empty($_POST['type_event'])) {
            $errors[] = "Le type d'événement est obligatoire";
        }
        
        if(!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_data'] = $_POST;
        } else {
            // Récupérer l'événement existant
            $db = Config::getConnexion();
            $eventModel = new Event($db);
            $eventModel->setIdEvent($_POST['id_event']);
            $eventModel->readOne();
            $old_image = $eventModel->getImage();
            
            // Upload nouvelle image si fournie
            $image = $old_image; // Garder l'ancienne par défaut
            if(isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                $filename = $_FILES['image']['name'];
                $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                
                if(in_array($ext, $allowed)) {
                    if($_FILES['image']['size'] <= 5 * 1024 * 1024) {
                        $newname = uniqid() . '.' . $ext;
                        $upload_dir = __DIR__ . '/../../public/uploads/events/';
                        
                        if(!file_exists($upload_dir)) {
                            mkdir($upload_dir, 0777, true);
                        }
                        
                        if(move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $newname)) {
                            $image = 'public/uploads/events/' . $newname;
                            // Supprimer l'ancienne image si elle existe
                            if($old_image && file_exists(__DIR__ . '/../../' . $old_image)) {
                                unlink(__DIR__ . '/../../' . $old_image);
                            }
                        }
                    } else {
                        $errors[] = "L'image ne doit pas dépasser 5MB";
                    }
                } else {
                    $errors[] = "Format d'image non valide (JPG, PNG, GIF uniquement)";
                }
            }
            
            if(empty($errors)) {
                // Mettre à jour l'événement
                $eventModel->setTitre($_POST['titre']);
                $eventModel->setDescription($_POST['description']);
                $eventModel->setDateEvent($_POST['date_event']);
                $eventModel->setLieu($_POST['lieu']);
                $eventModel->setNombrePlaces($_POST['nombre_places']);
                $eventModel->setTypeEvent($_POST['type_event']);
                $eventModel->setImage($image);
                
                if($eventModel->update()) {
                    $_SESSION['success'] = "✅ Événement modifié avec succès !";
                    unset($_SESSION['old_data']);
                    
                    // Rediriger vers la liste des événements
                    $isAjax = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') 
                              || (isset($_GET['ajax']) && $_GET['ajax'] == '1');
                    
                    if ($isAjax) {
                        // Si AJAX, recharger la page des événements dans le dashboard
                        header('Content-Type: text/html; charset=utf-8');
                        echo '<script>
                            if(typeof loadContent === "function") { 
                                loadContent("showevent.php"); 
                            } else { 
                                window.location.href = "showevent.php"; 
                            }
                        </script>';
                        exit;
                    } else {
                        header('Location: showevent.php?success=' . urlencode('Événement modifié avec succès !'));
                        exit;
                    }
                } else {
                    $errors[] = "Erreur lors de la modification de l'événement";
                }
            }
            
            if(!empty($errors)) {
                $_SESSION['errors'] = $errors;
                $_SESSION['old_data'] = $_POST;
            }
        }
    } catch(Exception $e) {
        $_SESSION['errors'] = ["Erreur : " . $e->getMessage()];
        $_SESSION['old_data'] = $_POST;
    }
}

$embed = isset($_GET['embed']) && $_GET['embed'] === '1';
$isAjax = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') 
          || (isset($_GET['ajax']) && $_GET['ajax'] == '1');
$showFullPage = !$embed && !$isAjax;

if ($showFullPage) {
    ob_start();
}

// Utiliser les données de l'événement ou les données POST en cas d'erreur
$old_data = $_SESSION['old_data'] ?? null;
$titre = $old_data['titre'] ?? ($event ? $event->getTitre() : '');
$description = $old_data['description'] ?? ($event ? $event->getDescription() : '');
$date_event = $old_data['date_event'] ?? ($event ? date('Y-m-d\TH:i', strtotime($event->getDateEvent())) : '');
$lieu = $old_data['lieu'] ?? ($event ? $event->getLieu() : '');
$type_event = $old_data['type_event'] ?? ($event ? $event->getTypeEvent() : '');
$nombre_places = $old_data['nombre_places'] ?? ($event ? $event->getNombrePlaces() : '');
$current_image = $event ? $event->getImage() : '';
$id_event = $event ? $event->getIdEvent() : $_GET['id'] ?? '';
?>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <div class="header-title">
                    <h4 class="card-title">
                        <i class="ri-edit-line me-2"></i>Modifier l'Événement
                    </h4>
                </div>
                <div>
                    <a href="#" onclick="loadContent('showevent.php'); return false;" class="btn btn-secondary">
                        <i class="ri-arrow-left-line me-1"></i> Retour
                    </a>
                </div>
            </div>
            <div class="card-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="ri-error-warning-line me-2"></i>
                        <?= htmlspecialchars($error) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="ri-checkbox-circle-line me-2"></i>
                        <?= htmlspecialchars($success) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['errors'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="ri-error-warning-line me-2"></i>
                        <strong>Erreurs de validation :</strong>
                        <ul class="mb-0 mt-2">
                            <?php foreach($_SESSION['errors'] as $err): ?>
                                <li><?= htmlspecialchars($err) ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['errors']); ?>
                <?php endif; ?>

                <?php if ($event || $id_event): ?>
                <form method="POST" action="editevent.php" enctype="multipart/form-data" id="eventForm">
                    <input type="hidden" name="id_event" value="<?= htmlspecialchars($id_event) ?>">
                    <div class="row">
                        <!-- Titre -->
                        <div class="col-md-12 mb-3">
                            <label for="titre" class="form-label">
                                Titre de l'événement <span class="text-danger">*</span>
                            </label>
                            <input 
                                type="text" 
                                class="form-control" 
                                id="titre" 
                                name="titre" 
                                value="<?= htmlspecialchars($titre) ?>"
                                placeholder="Ex: Distribution de repas pour les sans-abris"
                                required>
                            <small class="form-text text-muted">Minimum 5 caractères</small>
                        </div>

                        <!-- Description -->
                        <div class="col-md-12 mb-3">
                            <label for="description" class="form-label">
                                Description <span class="text-danger">*</span>
                            </label>
                            <textarea 
                                class="form-control" 
                                id="description" 
                                name="description" 
                                rows="5"
                                placeholder="Décrivez en détail l'événement, ses objectifs, ce qui est prévu..."
                                required><?= htmlspecialchars($description) ?></textarea>
                            <small class="form-text text-muted">Minimum 20 caractères</small>
                        </div>

                        <!-- Date et Lieu -->
                        <div class="col-md-6 mb-3">
                            <label for="date_event" class="form-label">
                                Date et Heure <span class="text-danger">*</span>
                            </label>
                            <input 
                                type="datetime-local" 
                                class="form-control" 
                                id="date_event" 
                                name="date_event" 
                                value="<?= htmlspecialchars($date_event) ?>"
                                required>
                            <small class="form-text text-muted">La date doit être dans le futur</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="lieu" class="form-label">
                                Lieu <span class="text-danger">*</span>
                            </label>
                            <input 
                                type="text" 
                                class="form-control" 
                                id="lieu" 
                                name="lieu" 
                                value="<?= htmlspecialchars($lieu) ?>"
                                placeholder="Ex: 123 Rue de la Charité, Tunis"
                                required>
                            <small class="form-text text-muted">Adresse complète de l'événement</small>
                        </div>

                        <!-- Type et Places -->
                        <div class="col-md-6 mb-3">
                            <label for="type_event" class="form-label">
                                Type d'événement <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="type_event" name="type_event" required>
                                <option value="">-- Sélectionnez un type --</option>
                                <option value="collecte" <?= ($type_event === 'collecte') ? 'selected' : '' ?>>Collecte de nourriture</option>
                                <option value="distribution" <?= ($type_event === 'distribution') ? 'selected' : '' ?>>Distribution de repas</option>
                                <option value="soutien" <?= ($type_event === 'soutien') ? 'selected' : '' ?>>Soutien médical</option>
                                <option value="logement" <?= ($type_event === 'logement') ? 'selected' : '' ?>>Aide au logement</option>
                                <option value="autre" <?= ($type_event === 'autre') ? 'selected' : '' ?>>Autre</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="nombre_places" class="form-label">
                                Nombre de places <span class="text-danger">*</span>
                            </label>
                            <input 
                                type="number" 
                                class="form-control" 
                                id="nombre_places" 
                                name="nombre_places" 
                                value="<?= htmlspecialchars($nombre_places) ?>"
                                min="1" 
                                max="1000"
                                placeholder="Ex: 50"
                                required>
                            <small class="form-text text-muted">Nombre maximum de participants (1-1000)</small>
                        </div>

                        <!-- Image -->
                        <div class="col-md-12 mb-3">
                            <label for="image" class="form-label">
                                Image de l'événement
                            </label>
                            <?php if ($current_image): 
                                $imageUrl = '../../' . $current_image;
                                if (file_exists(__DIR__ . '/../../' . $current_image)): ?>
                                    <div class="mb-2">
                                        <img src="<?= htmlspecialchars($imageUrl) ?>" alt="Image actuelle" style="max-width: 200px; max-height: 200px; object-fit: cover; border-radius: 8px;">
                                        <p class="text-muted mt-2"><small>Image actuelle</small></p>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                            <input 
                                type="file" 
                                class="form-control" 
                                id="image" 
                                name="image" 
                                accept="image/jpeg,image/jpg,image/png,image/gif">
                            <small class="form-text text-muted">Formats acceptés: JPG, PNG, GIF (max 5MB). Laissez vide pour conserver l'image actuelle.</small>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="ri-save-line me-1"></i> Enregistrer les modifications
                        </button>
                        <a href="#" onclick="loadContent('showevent.php'); return false;" class="btn btn-secondary">
                            <i class="ri-close-line me-1"></i> Annuler
                        </a>
                    </div>
                </form>
                <?php else: ?>
                    <div class="alert alert-danger">
                        <i class="ri-error-warning-line me-2"></i>
                        Impossible de charger l'événement. Veuillez vérifier l'ID.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('eventForm');
    if (!form) return;
    
    const dateInput = document.getElementById('date_event');
    
    // Validation côté client
    form.addEventListener('submit', function(e) {
        const titre = document.getElementById('titre').value.trim();
        const description = document.getElementById('description').value.trim();
        const dateEvent = dateInput.value;
        const lieu = document.getElementById('lieu').value.trim();
        const typeEvent = document.getElementById('type_event').value;
        const nombrePlaces = document.getElementById('nombre_places').value;
        
        let errors = [];
        
        if (titre.length < 5) {
            errors.push('Le titre doit contenir au moins 5 caractères');
        }
        
        if (description.length < 20) {
            errors.push('La description doit contenir au moins 20 caractères');
        }
        
        if (dateEvent) {
            const eventDate = new Date(dateEvent);
            const now = new Date();
            if (eventDate <= now) {
                errors.push('La date doit être dans le futur');
            }
        }
        
        if (lieu.length < 3) {
            errors.push('Le lieu doit contenir au moins 3 caractères');
        }
        
        if (!typeEvent) {
            errors.push('Veuillez sélectionner un type d\'événement');
        }
        
        if (!nombrePlaces || nombrePlaces < 1 || nombrePlaces > 1000) {
            errors.push('Le nombre de places doit être entre 1 et 1000');
        }
        
        if (errors.length > 0) {
            e.preventDefault();
            alert('Erreurs de validation:\n' + errors.join('\n'));
            return false;
        }
    });
    
    // Validation de l'image
    const imageInput = document.getElementById('image');
    if (imageInput) {
        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const maxSize = 5 * 1024 * 1024; // 5MB
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                
                if (file.size > maxSize) {
                    alert('L\'image ne doit pas dépasser 5MB');
                    e.target.value = '';
                    return;
                }
                
                if (!allowedTypes.includes(file.type)) {
                    alert('Format d\'image non valide. Utilisez JPG, PNG ou GIF');
                    e.target.value = '';
                    return;
                }
            }
        });
    }
});
</script>

<?php
if ($showFullPage) {
    $content = ob_get_clean();
    renderBackofficeLayout('Modifier l\'Événement', $content);
} else {
    ob_end_flush();
}
?>

