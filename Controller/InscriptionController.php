<?php
/**
 * Controller/InscriptionController.php
 * ✅ VERSION CORRIGÉE - Variables passées correctement à la vue
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../Model/Inscription.php';
require_once __DIR__ . '/../Model/Event.php';

class InscriptionController {
    private $db;
    private $inscription;
    private $event;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->inscription = new Inscription($this->db);
        $this->event = new Event($this->db);
    }
    
    /**
     * BACKOFFICE - Liste de toutes les inscriptions
     */
    public function liste() {
        $stmt = $this->inscription->readAll();
        $inscriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        require_once __DIR__ . '/../View/BackOffice/inscriptions_list.php';
    }
    
    /**
     * FRONTOFFICE - Afficher le formulaire d'inscription
     * ✅ CORRECTION: Variables correctement passées à la vue
     */
    public function create() {
        if(!isset($_GET['id_event'])) {
            $_SESSION['error'] = "Événement introuvable";
            header('Location: index.php?controller=event&action=liste');
            exit;
        }
        
        $id_event = $_GET['id_event'];
        
        // Récupérer l'événement
        $this->event->setIdEvent($id_event);
        if(!$this->event->readOne()) {
            $_SESSION['error'] = "Événement introuvable";
            header('Location: index.php?controller=event&action=liste');
            exit;
        }
        
        // Vérifier les places disponibles
        $inscriptions_count = $this->event->countInscriptions();
        $places_restantes = $this->event->getNombrePlaces() - $inscriptions_count;
        
        if($places_restantes <= 0) {
            $_SESSION['error'] = "❌ Désolé, cet événement est complet !";
            header('Location: index.php?controller=event&action=detail&id=' . $id_event);
            exit;
        }
        
        // ✅ CORRECTION: Passer les variables à la vue correctement
        // La vue utilise $this->event, donc on doit le rendre disponible
        $event = $this->event; // Pour compatibilité avec la vue
        
        require_once __DIR__ . '/../View/FrontOffice/inscription_form.php';
    }
    
    /**
     * FRONTOFFICE - Enregistrer une inscription
     */
    public function store() {
        if($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=event&action=liste');
            exit;
        }
        
        if(!isset($_POST['id_event'])) {
            $_SESSION['error'] = "Événement introuvable";
            header('Location: index.php?controller=event&action=liste');
            exit;
        }
        
        // Validation côté serveur
        $errors = $this->validateInscriptionData($_POST);
        
        if(!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_data'] = $_POST;
            header('Location: index.php?controller=inscription&action=create&id_event=' . $_POST['id_event']);
            exit;
        }
        
        // Vérifier si déjà inscrit
        $this->inscription->setIdEvent($_POST['id_event']);
        $this->inscription->setEmail($_POST['email']);
        
        if($this->inscription->checkDuplicate()) {
            $_SESSION['error'] = "❌ Vous êtes déjà inscrit à cet événement !";
            header('Location: index.php?controller=event&action=detail&id=' . $_POST['id_event']);
            exit;
        }
        
        // Vérifier les places disponibles
        $this->event->setIdEvent($_POST['id_event']);
        $this->event->readOne();
        $inscriptions_count = $this->event->countInscriptions();
        $places_restantes = $this->event->getNombrePlaces() - $inscriptions_count;
        
        if($places_restantes <= 0) {
            $_SESSION['error'] = "❌ Désolé, cet événement est maintenant complet !";
            header('Location: index.php?controller=event&action=detail&id=' . $_POST['id_event']);
            exit;
        }
        
        // Sauvegarder l'inscription
        $this->inscription->setNom($_POST['nom']);
        $this->inscription->setPrenom($_POST['prenom']);
        $this->inscription->setTelephone($_POST['telephone']);
        $this->inscription->setPourquoi($_POST['pourquoi']);
        
        if($this->inscription->create()) {
            $_SESSION['success'] = "✅ Inscription réussie ! Vous recevrez une confirmation par email.";
            
            // TODO: Envoyer email de confirmation si PHPMailer est configuré
            // $this->sendConfirmationEmail($_POST['email'], ...);
            
            header('Location: index.php?controller=event&action=detail&id=' . $_POST['id_event']);
        } else {
            $_SESSION['error'] = "❌ Erreur lors de l'inscription";
            $_SESSION['old_data'] = $_POST;
            header('Location: index.php?controller=inscription&action=create&id_event=' . $_POST['id_event']);
        }
        exit;
    }
    
    /**
     * BACKOFFICE - Confirmer une inscription
     */
    public function confirmer() {
        if(!isset($_GET['id'])) {
            $_SESSION['error'] = "Inscription introuvable";
            header('Location: index.php?controller=inscription&action=liste');
            exit;
        }
        
        $this->inscription->setIdInscription($_GET['id']);
        
        if($this->inscription->confirmer()) {
            $_SESSION['success'] = "✅ Inscription confirmée avec succès !";
        } else {
            $_SESSION['error'] = "❌ Erreur lors de la confirmation";
        }
        
        header('Location: index.php?controller=inscription&action=liste');
        exit;
    }
    
    /**
     * BACKOFFICE - Supprimer une inscription
     */
    public function delete() {
        if(!isset($_GET['id'])) {
            $_SESSION['error'] = "Inscription introuvable";
            header('Location: index.php?controller=inscription&action=liste');
            exit;
        }
        
        $this->inscription->setIdInscription($_GET['id']);
        
        if($this->inscription->delete()) {
            $_SESSION['success'] = "✅ Inscription supprimée avec succès !";
        } else {
            $_SESSION['error'] = "❌ Erreur lors de la suppression";
        }
        
        $source = $_GET['source'] ?? 'admin';
        if($source === 'admin') {
            header('Location: index.php?controller=inscription&action=liste');
        } else {
            header('Location: index.php?controller=event&action=liste');
        }
        exit;
    }
    
    /**
     * Valider les données d'inscription
     */
    private function validateInscriptionData($data) {
        $errors = [];
        
        // Nom
        if(empty($data['nom']) || strlen(trim($data['nom'])) < 2) {
            $errors[] = "Le nom doit contenir au moins 2 caractères";
        } elseif(!preg_match('/^[a-zA-ZÀ-ÿ\s-]+$/', trim($data['nom']))) {
            $errors[] = "Le nom ne doit contenir que des lettres";
        }
        
        // Prénom
        if(empty($data['prenom']) || strlen(trim($data['prenom'])) < 2) {
            $errors[] = "Le prénom doit contenir au moins 2 caractères";
        } elseif(!preg_match('/^[a-zA-ZÀ-ÿ\s-]+$/', trim($data['prenom']))) {
            $errors[] = "Le prénom ne doit contenir que des lettres";
        }
        
        // Email
        if(empty($data['email'])) {
            $errors[] = "L'email est obligatoire";
        } elseif(!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Format d'email invalide";
        }
        
        // Téléphone
        if(empty($data['telephone'])) {
            $errors[] = "Le téléphone est obligatoire";
        } elseif(!preg_match('/^[0-9]{8}$/', $data['telephone'])) {
            $errors[] = "Le téléphone doit contenir exactement 8 chiffres";
        }
        
        // Pourquoi
        if(empty($data['pourquoi']) || strlen(trim($data['pourquoi'])) < 20) {
            $errors[] = "Veuillez expliquer pourquoi vous souhaitez participer (minimum 20 caractères)";
        } elseif(strlen(trim($data['pourquoi'])) > 500) {
            $errors[] = "Votre motivation ne peut pas dépasser 500 caractères";
        }
        
        return $errors;
    }
}
?>