<?php
/**
 * EventController pour AidConnect
 * Intègre le système event dans aidconnect!!
 */

if (session_id() == '') {
    session_start();
}

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Model/Event.php';

class EventController {
    private $db;
    private $event;
    
    public function __construct() {
        $this->db = Config::getConnexion();
        $this->event = new Event($this->db);
    }
    
    /**
     * FRONTOFFICE - Liste des événements
     */
    public function liste() {
        require_once __DIR__ . '/../View/frontoffice/event_template.php';
        
        $stmt = $this->event->readAll();
        $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Ajouter le nombre d'inscriptions pour chaque événement
        foreach($events as &$event) {
            $this->event->setIdEvent($event['id_event']);
            $event['inscriptions_count'] = $this->event->countInscriptions();
        }
        
        // Générer le contenu HTML
        ob_start();
        include __DIR__ . '/../View/frontoffice/event_list_content.php';
        $content = ob_get_clean();
        
        // Rendre avec le template personnalisé
        renderEventTemplate('Événements Disponibles', $content, ['assets/css/event.css']);
    }
    
    /**
     * FRONTOFFICE - Détail d'un événement
     */
    public function detail() {
        require_once __DIR__ . '/../View/frontoffice/event_template.php';
        
        if(!isset($_GET['id'])) {
            $_SESSION['error'] = "Événement introuvable";
            header('Location: event.php?controller=event&action=liste');
            exit;
        }
        
        $this->event->setIdEvent($_GET['id']);
        
        if(!$this->event->readOne()) {
            $_SESSION['error'] = "Événement introuvable";
            header('Location: event.php?controller=event&action=liste');
            exit;
        }
        
        $event = $this->event;
        $inscriptions_count = $this->event->countInscriptions();
        $places_restantes = $this->event->getNombrePlaces() - $inscriptions_count;
        
        // Générer le contenu HTML
        ob_start();
        include __DIR__ . '/../View/frontoffice/detail_event_content.php';
        $content = ob_get_clean();
        
        // Rendre avec le template personnalisé
        renderEventTemplate('Détails de l\'événement', $content, ['assets/css/event.css']);
    }
    
    /**
     * FRONTOFFICE - Recherche d'événements
     */
    public function search() {
        require_once __DIR__ . '/../View/frontoffice/event_template.php';
        
        $keyword = $_GET['keyword'] ?? '';
        $type = $_GET['type'] ?? '';
        $date_min = $_GET['date_min'] ?? '';
        $lieu = $_GET['lieu'] ?? '';
        
        // Requête de base
        $query = "SELECT * FROM events WHERE statut = 'actif'";
        $params = [];
        
        // Filtres
        if(!empty($keyword)) {
            $query .= " AND (titre LIKE :keyword OR description LIKE :keyword)";
            $params[':keyword'] = "%{$keyword}%";
        }
        
        if(!empty($type)) {
            $query .= " AND type_event = :type";
            $params[':type'] = $type;
        }
        
        if(!empty($date_min)) {
            $query .= " AND date_event >= :date_min";
            $params[':date_min'] = $date_min;
        }
        
        if(!empty($lieu)) {
            $query .= " AND lieu LIKE :lieu";
            $params[':lieu'] = "%{$lieu}%";
        }
        
        $query .= " ORDER BY date_event ASC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Ajouter le nombre d'inscriptions
        foreach($events as &$event) {
            $this->event->setIdEvent($event['id_event']);
            $event['inscriptions_count'] = $this->event->countInscriptions();
        }
        
        // Générer le contenu HTML
        ob_start();
        include __DIR__ . '/../View/frontoffice/event_list_content.php';
        $content = ob_get_clean();
        
        // Rendre avec le template personnalisé
        renderEventTemplate('Recherche d\'événements', $content, ['assets/css/event.css']);
    }
    
    /**
     * BACKOFFICE - Gestion des événements
     */
    public function manage() {
        // Vérifier si admin
        if (!isset($_SESSION['id_user']) || ($_SESSION['role'] ?? '') !== 'admin') {
            header('Location: ../View/frontoffice/login.php?error=' . urlencode('Accès refusé.'));
            exit;
        }
        
        $stmt = $this->event->readAll();
        $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Ajouter les inscriptions pour chaque événement
        foreach($events as &$event) {
            $this->event->setIdEvent($event['id_event']);
            $event['inscriptions_count'] = $this->event->countInscriptions();
        }
        
        // Détecter si c'est une requête AJAX (pour dashboard.php)
        $isAjax = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') 
                  || (isset($_GET['ajax']) && $_GET['ajax'] == '1');
        
        // Redirect to showevent.php (backoffice view)
        header('Location: ../View/backoffice/showevent.php');
        exit;
    }
    
    /**
     * BACKOFFICE - Afficher le formulaire de création
     */
    public function create() {
        // Vérifier si admin
        if (!isset($_SESSION['id_user']) || ($_SESSION['role'] ?? '') !== 'admin') {
            header('Location: ../View/frontoffice/login.php?error=' . urlencode('Accès refusé.'));
            exit;
        }
        
        // Redirect to addevent.php (backoffice view)
        header('Location: ../View/backoffice/addevent.php');
        exit;
    }
    
    /**
     * Add Event - Similar to addOffer pattern
     * Handles event creation with proper null handling for image
     */
    public function addEvent($titre, $description, $date_event, $lieu, $nombre_places, $type_event, $image = null) {
        $sql = "INSERT INTO events 
                (titre, description, date_event, lieu, nombre_places, type_event, image, statut)
                VALUES 
                (:titre, :description, :date_event, :lieu, :nombre_places, :type_event, :image, 'actif')";
        
        $bd = Config::getConnexion();
        
        try {
            $q = $bd->prepare($sql);
            $q->bindValue(':titre', $titre, PDO::PARAM_STR);
            $q->bindValue(':description', $description, PDO::PARAM_STR);
            $q->bindValue(':date_event', $date_event, PDO::PARAM_STR);
            $q->bindValue(':lieu', $lieu, PDO::PARAM_STR);
            $q->bindValue(':nombre_places', $nombre_places, PDO::PARAM_INT);
            $q->bindValue(':type_event', $type_event, PDO::PARAM_STR);
            
            // Handle image like media in offercontroller (lines 24-28)
            if ($image === null) {
                $q->bindValue(':image', null, PDO::PARAM_NULL);
            } else {
                $q->bindValue(':image', $image, PDO::PARAM_STR);
            }
            
            if ($q->execute()) {
                return $bd->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            error_log("Erreur PDO addEvent : " . $e->getMessage());
            if (ini_get('display_errors')) {
                echo "Erreur SQL (addEvent) : " . htmlspecialchars($e->getMessage()) . "<br>";
            }
            return false;
        } catch (Exception $e) {
            error_log("Erreur addEvent : " . $e->getMessage());
            if (ini_get('display_errors')) {
                echo "Erreur (addEvent) : " . htmlspecialchars($e->getMessage()) . "<br>";
            }
            return false;
        }
    }
    
    /**
     * BACKOFFICE - Enregistrer un nouvel événement
     */
    public function store() {
        // Vérifier si admin
        if (!isset($_SESSION['id_user']) || ($_SESSION['role'] ?? '') !== 'admin') {
            header('Location: ../View/frontoffice/login.php?error=' . urlencode('Accès refusé.'));
            exit;
        }
        
        if($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ../View/frontoffice/event.php?action=create');
            exit;
        }
        
        // Validation côté serveur
        $errors = $this->validateEventData($_POST);
        
        if(!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_data'] = $_POST;
            header('Location: ../View/frontoffice/event.php?action=create');
            exit;
        }
        
        // Upload de l'image
        $image = $this->uploadImage();
        
        // Use addEvent function with proper null handling (like addOffer)
        $eventId = $this->addEvent(
            $_POST['titre'],
            $_POST['description'],
            $_POST['date_event'],
            $_POST['lieu'],
            $_POST['nombre_places'],
            $_POST['type_event'],
            $image // Can be null, handled properly in addEvent
        );
        
        if($eventId !== false) {
            $_SESSION['success'] = "✅ Événement créé avec succès ! Il est maintenant visible sur le site.";
            header('Location: ../View/frontoffice/event.php?action=manage');
        } else {
            $_SESSION['error'] = "❌ Erreur lors de la création de l'événement";
            $_SESSION['old_data'] = $_POST;
            header('Location: ../View/frontoffice/event.php?action=create');
        }
        exit;
    }
    
    /**
     * BACKOFFICE - Afficher le formulaire d'édition
     */
    public function edit() {
        // Vérifier si admin
        if (!isset($_SESSION['id_user']) || ($_SESSION['role'] ?? '') !== 'admin') {
            header('Location: ../View/frontoffice/login.php?error=' . urlencode('Accès refusé.'));
            exit;
        }
        
        if(!isset($_GET['id'])) {
            $_SESSION['error'] = "Événement introuvable";
            header('Location: ../View/frontoffice/event.php?action=manage');
            exit;
        }
        
        $this->event->setIdEvent($_GET['id']);
        
        if(!$this->event->readOne()) {
            $_SESSION['error'] = "Événement introuvable";
            header('Location: ../View/frontoffice/event.php?action=manage');
            exit;
        }
        
        $event = $this->event;
        // Redirect to editevent.php (backoffice view)
        header('Location: ../View/backoffice/editevent.php?id=' . urlencode($_GET['id']));
        exit;
    }
    
    /**
     * BACKOFFICE - Mettre à jour un événement
     */
    public function update() {
        // Vérifier si admin
        if (!isset($_SESSION['id_user']) || ($_SESSION['role'] ?? '') !== 'admin') {
            header('Location: ../View/frontoffice/login.php?error=' . urlencode('Accès refusé.'));
            exit;
        }
        
        if($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ../View/frontoffice/event.php?action=manage');
            exit;
        }
        
        if(!isset($_POST['id_event'])) {
            $_SESSION['error'] = "Événement introuvable";
            header('Location: ../View/frontoffice/event.php?action=manage');
            exit;
        }
        
        // Validation
        $errors = $this->validateEventData($_POST);
        
        if(!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header('Location: ../View/frontoffice/event.php?action=edit&id=' . $_POST['id_event']);
            exit;
        }
        
        // Récupérer l'événement existant
        $this->event->setIdEvent($_POST['id_event']);
        $this->event->readOne();
        $old_image = $this->event->getImage();
        $eventId = $_POST['id_event'];
        
        // Upload nouvelle image si fournie (with event ID for unique filename)
        $image = $this->uploadImage($eventId);
        if($image && $old_image && file_exists(__DIR__ . '/../' . $old_image)) {
            // Delete old image only if new image was successfully uploaded
            @unlink(__DIR__ . '/../' . $old_image);
        }
        if(!$image) {
            // Keep old image if no new image was uploaded
            $image = $old_image;
        }
        
        // Mettre à jour
        $this->event->setTitre($_POST['titre']);
        $this->event->setDescription($_POST['description']);
        $this->event->setDateEvent($_POST['date_event']);
        $this->event->setLieu($_POST['lieu']);
        $this->event->setNombrePlaces($_POST['nombre_places']);
        $this->event->setTypeEvent($_POST['type_event']);
        $this->event->setImage($image);
        
        if($this->event->update()) {
            $_SESSION['success'] = "✅ Événement modifié avec succès !";
            header('Location: ../View/frontoffice/event.php?action=manage');
        } else {
            $_SESSION['error'] = "❌ Erreur lors de la modification";
            header('Location: ../View/frontoffice/event.php?action=edit&id=' . $_POST['id_event']);
        }
        exit;
    }
    
    /**
     * BACKOFFICE - Supprimer un événement
     */
    public function delete() {
        // Vérifier si admin
        if (!isset($_SESSION['id_user']) || ($_SESSION['role'] ?? '') !== 'admin') {
            header('Location: ../View/frontoffice/login.php?error=' . urlencode('Accès refusé.'));
            exit;
        }
        
        if(!isset($_GET['id'])) {
            $_SESSION['error'] = "Événement introuvable";
            // Détecter si AJAX
            $isAjax = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') 
                      || (isset($_GET['ajax']) && $_GET['ajax'] == '1')
                      || (isset($_GET['embed']) && $_GET['embed'] == '1');
            if ($isAjax) {
                header('Content-Type: text/html; charset=utf-8');
                echo '<script>if(typeof loadContent==="function"){ loadContent("showevent.php"); } else { window.location.href="showevent.php"; }</script>';
            } else {
                // Déterminer le bon chemin selon l'emplacement d'appel
                $basePath = dirname(dirname($_SERVER['PHP_SELF']));
                header('Location: ' . $basePath . '/View/backoffice/showevent.php');
            }
            exit;
        }
        
        $this->event->setIdEvent($_GET['id']);
        
        // Récupérer l'image pour la supprimer
        $this->event->readOne();
        $image = $this->event->getImage();
        
        if($this->event->delete()) {
            // Supprimer l'image si elle existe
            if($image && file_exists(__DIR__ . '/../' . $image)) {
                @unlink(__DIR__ . '/../' . $image);
            }
            $_SESSION['success'] = "✅ Événement supprimé avec succès !";
        } else {
            $_SESSION['error'] = "❌ Erreur lors de la suppression";
        }
        
        // Détecter si AJAX
        $isAjax = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') 
                  || (isset($_GET['ajax']) && $_GET['ajax'] == '1')
                  || (isset($_GET['embed']) && $_GET['embed'] == '1');
        
        if ($isAjax) {
            header('Content-Type: text/html; charset=utf-8');
            echo '<script>if(typeof loadContent==="function"){ loadContent("showevent.php"); } else { window.location.href="showevent.php"; }</script>';
        } else {
            // Déterminer le bon chemin selon l'emplacement d'appel
            $basePath = dirname(dirname($_SERVER['PHP_SELF']));
            header('Location: ' . $basePath . '/View/backoffice/showevent.php');
        }
        exit;
    }
    
    /**
     * Valider les données d'un événement
     */
    private function validateEventData($data) {
        $errors = [];
        
        // Titre
        if(empty($data['titre']) || strlen(trim($data['titre'])) < 5) {
            $errors[] = "Le titre doit contenir au moins 5 caractères";
        }
        
        // Description
        if(empty($data['description']) || strlen(trim($data['description'])) < 20) {
            $errors[] = "La description doit contenir au moins 20 caractères";
        }
        
        // Date
        if(empty($data['date_event'])) {
            $errors[] = "La date est obligatoire";
        } else {
            $date = strtotime($data['date_event']);
            if($date < time()) {
                $errors[] = "La date doit être dans le futur";
            }
        }
        
        // Lieu
        if(empty($data['lieu']) || strlen(trim($data['lieu'])) < 3) {
            $errors[] = "Le lieu doit contenir au moins 3 caractères";
        }
        
        // Nombre de places
        if(empty($data['nombre_places']) || $data['nombre_places'] < 1) {
            $errors[] = "Le nombre de places doit être supérieur à 0";
        } elseif($data['nombre_places'] > 1000) {
            $errors[] = "Le nombre de places ne peut pas dépasser 1000";
        }
        
        // Type
        if(empty($data['type_event'])) {
            $errors[] = "Le type d'événement est obligatoire";
        }
        
        return $errors;
    }
    
    /**
     * Upload d'image
     * @param int|null $eventId Event ID for unique filename (null for new events)
     */
    private function uploadImage($eventId = null) {
        if(!isset($_FILES['image']) || $_FILES['image']['error'] !== 0) {
            return null;
        }
        
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if(!in_array($ext, $allowed)) {
            $_SESSION['error'] = "Format d'image non valide";
            return null;
        }
        
        if($_FILES['image']['size'] > 5 * 1024 * 1024) {
            $_SESSION['error'] = "L'image ne doit pas dépasser 5MB";
            return null;
        }
        
        // Generate unique filename with event ID if available (like posts/offers)
        if($eventId !== null) {
            $newname = 'event_' . $eventId . '_' . time() . '_' . uniqid() . '.' . $ext;
        } else {
            // For new events, use timestamp + uniqid for uniqueness
            $newname = 'event_' . time() . '_' . uniqid() . '.' . $ext;
        }
        
        $upload_dir = __DIR__ . '/../public/uploads/events/';
        
        if(!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        if(move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $newname)) {
            return 'public/uploads/events/' . $newname;
        }
        
        return null;
    }
}

// Point d'entrée si appelé directement
if (basename($_SERVER['PHP_SELF']) === 'eventcontroller.php') {
    $action = $_GET['action'] ?? 'manage';
    $controller = new EventController();
    
    // Vérifier si admin pour les actions backoffice
    $backoffice_actions = ['manage', 'create', 'store', 'edit', 'update', 'delete'];
    if (in_array($action, $backoffice_actions)) {
        if (!isset($_SESSION['id_user']) || ($_SESSION['role'] ?? '') !== 'admin') {
            header('Location: ../View/frontoffice/login.php?error=' . urlencode('Accès refusé.'));
            exit;
        }
    }
    
    // Détecter si c'est une requête AJAX
    $isAjax = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') 
              || (isset($_GET['ajax']) && $_GET['ajax'] == '1')
              || (isset($_GET['embed']) && $_GET['embed'] == '1');
    
    // Router vers la méthode appropriée
    switch($action) {
        case 'manage':
            $controller->manage();
            break;
        case 'create':
            $controller->create();
            break;
        case 'store':
            $controller->store();
            break;
        case 'edit':
            $controller->edit();
            break;
        case 'update':
            $controller->update();
            break;
        case 'delete':
            $controller->delete();
            break;
        case 'liste':
            $controller->liste();
            break;
        case 'detail':
            $controller->detail();
            break;
        case 'search':
            $controller->search();
            break;
        default:
            $controller->manage();
            break;
    }
}
?>

