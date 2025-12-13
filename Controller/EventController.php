<?php
/**
 * Controller/EventController.php
 * Contrôleur pour la gestion des événements
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../Model/Event.php';

class EventController {
    private $db;
    private $event;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->event = new Event($this->db);
    }
    
    /**
     * FRONTOFFICE - Liste des événements
     */
    public function liste() {
        $stmt = $this->event->readAll();
        $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Ajouter le nombre d'inscriptions pour chaque événement
        foreach($events as &$event) {
            $this->event->setIdEvent($event['id_event']);
            $event['inscriptions_count'] = $this->event->countInscriptions();
        }
        
        require_once __DIR__ . '/../View/FrontOffice/liste_events.php';
    }
    
    /**
     * FRONTOFFICE - Détail d'un événement
     */
    public function detail() {
        if(!isset($_GET['id'])) {
            $_SESSION['error'] = "Événement introuvable";
            header('Location: index.php?controller=event&action=liste');
            exit;
        }
        
        $this->event->setIdEvent($_GET['id']);
        
        if(!$this->event->readOne()) {
            $_SESSION['error'] = "Événement introuvable";
            header('Location: index.php?controller=event&action=liste');
            exit;
        }
        
        $event = $this->event;
        $inscriptions_count = $this->event->countInscriptions();
        $places_restantes = $this->event->getNombrePlaces() - $inscriptions_count;
        
        require_once __DIR__ . '/../View/FrontOffice/detail_event.php';
    }
    
    /**
     * FRONTOFFICE - Recherche d'événements
     */
    public function search() {
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
        
        require_once __DIR__ . '/../View/FrontOffice/liste_events.php';
    }
    
    /**
     * BACKOFFICE - Gestion des événements
     */
    public function manage() {
        $stmt = $this->event->readAll();
        $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Ajouter les inscriptions pour chaque événement
        foreach($events as &$event) {
            $this->event->setIdEvent($event['id_event']);
            $event['inscriptions_count'] = $this->event->countInscriptions();
        }
        
        require_once __DIR__ . '/../View/BackOffice/events_manage.php';
    }
    
    /**
     * BACKOFFICE - Afficher le formulaire de création
     */
    public function create() {
        require_once __DIR__ . '/../View/BackOffice/event_form.php';
    }
    
    /**
     * BACKOFFICE - Enregistrer un nouvel événement
     */
    public function store() {
        if($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=event&action=create');
            exit;
        }
        
        // Validation côté serveur
        $errors = $this->validateEventData($_POST);
        
        if(!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_data'] = $_POST;
            header('Location: index.php?controller=event&action=create');
            exit;
        }
        
        // Upload de l'image
        $image = $this->uploadImage();
        
        // Sauvegarder l'événement
        $this->event->setTitre($_POST['titre']);
        $this->event->setDescription($_POST['description']);
        $this->event->setDateEvent($_POST['date_event']);
        $this->event->setLieu($_POST['lieu']);
        $this->event->setNombrePlaces($_POST['nombre_places']);
        $this->event->setTypeEvent($_POST['type_event']);
        $this->event->setImage($image);
        
        if($this->event->create()) {
            $_SESSION['success'] = "✅ Événement créé avec succès ! Il est maintenant visible sur le site.";
            header('Location: index.php?controller=event&action=manage');
        } else {
            $_SESSION['error'] = "❌ Erreur lors de la création de l'événement";
            $_SESSION['old_data'] = $_POST;
            header('Location: index.php?controller=event&action=create');
        }
        exit;
    }
    
    /**
     * BACKOFFICE - Afficher le formulaire d'édition
     */
    public function edit() {
        if(!isset($_GET['id'])) {
            $_SESSION['error'] = "Événement introuvable";
            header('Location: index.php?controller=event&action=manage');
            exit;
        }
        
        $this->event->setIdEvent($_GET['id']);
        
        if(!$this->event->readOne()) {
            $_SESSION['error'] = "Événement introuvable";
            header('Location: index.php?controller=event&action=manage');
            exit;
        }
        
        $event = $this->event;
        require_once __DIR__ . '/../View/BackOffice/event_form.php';
    }
    
    /**
     * BACKOFFICE - Mettre à jour un événement
     */
    public function update() {
        if($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=event&action=manage');
            exit;
        }
        
        if(!isset($_POST['id_event'])) {
            $_SESSION['error'] = "Événement introuvable";
            header('Location: index.php?controller=event&action=manage');
            exit;
        }
        
        // Validation
        $errors = $this->validateEventData($_POST);
        
        if(!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header('Location: index.php?controller=event&action=edit&id=' . $_POST['id_event']);
            exit;
        }
        
        // Récupérer l'événement existant
        $this->event->setIdEvent($_POST['id_event']);
        $this->event->readOne();
        $old_image = $this->event->getImage();
        
        // Upload nouvelle image si fournie
        $image = $this->uploadImage();
        if($image && $old_image && file_exists(__DIR__ . '/../' . $old_image)) {
            unlink(__DIR__ . '/../' . $old_image);
        }
        if(!$image) {
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
            header('Location: index.php?controller=event&action=manage');
        } else {
            $_SESSION['error'] = "❌ Erreur lors de la modification";
            header('Location: index.php?controller=event&action=edit&id=' . $_POST['id_event']);
        }
        exit;
    }
    
    /**
     * BACKOFFICE - Supprimer un événement
     */
    public function delete() {
        if(!isset($_GET['id'])) {
            $_SESSION['error'] = "Événement introuvable";
            header('Location: index.php?controller=event&action=manage');
            exit;
        }
        
        $this->event->setIdEvent($_GET['id']);
        
        if($this->event->delete()) {
            $_SESSION['success'] = "✅ Événement supprimé avec succès !";
        } else {
            $_SESSION['error'] = "❌ Erreur lors de la suppression";
        }
        
        header('Location: index.php?controller=event&action=manage');
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
     */
    private function uploadImage() {
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
        
        $newname = uniqid() . '.' . $ext;
        $upload_dir = __DIR__ . '/../uploads/events/';
        
        if(!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        if(move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $newname)) {
            return 'uploads/events/' . $newname;
        }
        
        return null;
    }
}
?>