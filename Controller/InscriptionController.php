<?php
/**
 * Controller/InscriptionController.php
 * ✅ VERSION FINALE CORRIGÉE - Toutes les redirections fonctionnelles
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
        
        // Passer les variables à la vue
        $event = $this->event;
        
        require_once __DIR__ . '/../View/FrontOffice/inscription_form.php';
    }
    
    /**
     * FRONTOFFICE - Enregistrer une inscription
     * ✅ CORRECTION: Redirection error=duplicate vers le formulaire
     */
    public function store() {
        if($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=event&action=liste');
            exit;
        }
        
        $id_event = $_POST['id_event'];
        $email = trim($_POST['email']);
        
        // Validation des données
        $errors = $this->validateInscriptionData($_POST);
        
        if(!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_data'] = $_POST;
            header('Location: index.php?controller=inscription&action=create&id_event=' . $id_event);
            exit;
        }
        
        // ✅ VÉRIFIER SI EMAIL EXISTE DÉJÀ
        $query = "SELECT COUNT(*) as count FROM inscriptions WHERE id_event = :id_event AND email = :email";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id_event', $id_event);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($result['count'] > 0) {
            // ✅ CORRECTION: Redirige vers le FORMULAIRE avec error=duplicate
            error_log("⚠️ Email déjà inscrit: $email pour événement $id_event");
            $_SESSION['error'] = "❌ Vous êtes déjà inscrit à cet événement avec cet email !";
            header('Location: index.php?controller=inscription&action=create&id_event=' . $id_event . '&error=duplicate');
            exit;
        }
        
        // ✅ VÉRIFIER LES PLACES
        $this->event->setIdEvent($id_event);
        $this->event->readOne();
        $inscriptions_count = $this->event->countInscriptions();
        $places_restantes = $this->event->getNombrePlaces() - $inscriptions_count;
        
        if($places_restantes <= 0) {
            error_log("⚠️ Événement complet: $id_event");
            header('Location: index.php?controller=event&action=detail&id=' . $id_event . '&error=full');
            exit;
        }
        
        // ✅ CRÉER L'INSCRIPTION
        $this->inscription->setIdEvent($id_event);
        $this->inscription->setNom($_POST['nom']);
        $this->inscription->setPrenom($_POST['prenom']);
        $this->inscription->setEmail($email);
        $this->inscription->setTelephone($_POST['telephone']);
        $this->inscription->setPourquoi($_POST['pourquoi']);
        
        if($this->inscription->create()) {
            unset($_SESSION['old_data']);
            unset($_SESSION['errors']);
            
            error_log("✅ Inscription créée avec succès pour: $email");
            
            // ✅ REDIRECTION AVEC success=inscription
            header('Location: index.php?controller=event&action=detail&id=' . $id_event . '&success=inscription');
            exit;
        } else {
            $_SESSION['errors'] = ["Erreur lors de l'enregistrement"];
            header('Location: index.php?controller=inscription&action=create&id_event=' . $id_event);
            exit;
        }
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