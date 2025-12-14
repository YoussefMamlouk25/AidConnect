<?php
/**
 * InscriptionController pour AidConnect
 * Intègre le système d'inscription aux événements
 */

if (session_id() == '') {
    session_start();
}

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Model/Inscription.php';
require_once __DIR__ . '/../Model/Event.php';

class InscriptionController {
    private $db;
    private $inscription;
    private $event;
    
    public function __construct() {
        $this->db = Config::getConnexion();
        $this->inscription = new Inscription($this->db);
        $this->event = new Event($this->db);
    }
    
    /**
     * BACKOFFICE - Liste de toutes les inscriptions
     */
    public function liste() {
        // Vérifier si admin
        if (!isset($_SESSION['id_user']) || ($_SESSION['role'] ?? '') !== 'admin') {
            header('Location: ../View/frontoffice/login.php?error=' . urlencode('Accès refusé.'));
            exit;
        }
        
        // Vérifier si on veut voir les inscriptions d'un événement spécifique ou toutes
        $event_id = isset($_GET['event_id']) ? intval($_GET['event_id']) : null;
        $view_all = isset($_GET['view']) && $_GET['view'] === 'all';
        
        if ($view_all || !$event_id) {
            // Afficher toutes les inscriptions avec les infos du membre
            $query = "SELECT i.*, 
                             e.titre as event_titre, e.date_event, e.lieu, e.type_event,
                             u.id_user, u.name as user_name, 
                             u.email as user_email, u.profile_pic, u.role
                      FROM inscriptions i
                      LEFT JOIN events e ON i.id_event = e.id_event
                      LEFT JOIN user u ON i.email = u.email
                      ORDER BY i.date_inscription DESC";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $inscriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            // Inscriptions pour un événement spécifique avec les infos du membre
            $query = "SELECT i.*, 
                             e.titre as event_titre, e.date_event, e.lieu, e.type_event,
                             u.id_user, u.name as user_name, 
                             u.email as user_email, u.profile_pic, u.role
                      FROM inscriptions i
                      LEFT JOIN events e ON i.id_event = e.id_event
                      LEFT JOIN user u ON i.email = u.email
                      WHERE i.id_event = :id_event
                      ORDER BY i.date_inscription DESC";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id_event', $event_id);
            $stmt->execute();
            $inscriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Récupérer les détails de l'événement
            $this->event->setIdEvent($event_id);
            $event = $this->event->readOne() ? $this->event : null;
        }
        
        // Inclure la vue adaptée pour le backoffice
        require_once __DIR__ . '/../View/backoffice/inscriptions_list_content.php';
    }
    
    /**
     * FRONTOFFICE - Afficher le formulaire d'inscription
     */
    public function create() {
        require_once __DIR__ . '/../View/frontoffice/event_template.php';
        
        if(!isset($_GET['id_event'])) {
            $_SESSION['error'] = "Événement introuvable";
            header('Location: event.php?controller=event&action=liste');
            exit;
        }
        
        $id_event = $_GET['id_event'];
        
        // Récupérer l'événement
        $this->event->setIdEvent($id_event);
        if(!$this->event->readOne()) {
            $_SESSION['error'] = "Événement introuvable";
            header('Location: event.php?controller=event&action=liste');
            exit;
        }
        
        $event = $this->event;
        $inscriptions_count = $this->event->countInscriptions();
        $places_restantes = $this->event->getNombrePlaces() - $inscriptions_count;
        
        // Vérifier si l'utilisateur est déjà inscrit (si connecté)
        $already_inscribed = false;
        if(isset($_SESSION['id_user'])) {
            require_once __DIR__ . '/../Model/user.php';
            $userModel = new UserModel();
            $user = $userModel->getUserById($_SESSION['id_user']);
            if($user && isset($user['email'])) {
                $this->inscription->setIdEvent($event->getIdEvent());
                $this->inscription->setEmail($user['email']);
                $already_inscribed = $this->inscription->checkDuplicate();
            }
        }
        
        // Générer le contenu HTML
        ob_start();
        include __DIR__ . '/../View/frontoffice/inscription_form_content.php';
        $content = ob_get_clean();
        
        // Rendre avec le template personnalisé
        renderEventTemplate('Inscription à l\'événement', $content, ['assets/css/event.css']);
    }
    
    /**
     * FRONTOFFICE - Enregistrer une inscription
     */
    public function store() {
        if($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: event.php?controller=event&action=liste');
            exit;
        }
        
        if(!isset($_POST['id_event'])) {
            $_SESSION['error'] = "Événement introuvable";
            header('Location: event.php?controller=event&action=liste');
            exit;
        }
        
        // Vérifier les places disponibles
        $this->event->setIdEvent($_POST['id_event']);
        if(!$this->event->readOne()) {
            $_SESSION['error'] = "Événement introuvable";
            header('Location: event.php?controller=event&action=liste');
            exit;
        }
        
        $inscriptions_count = $this->event->countInscriptions();
        $places_restantes = $this->event->getNombrePlaces() - $inscriptions_count;
        
        if($places_restantes <= 0) {
            $_SESSION['error'] = "Désolé, il n'y a plus de places disponibles pour cet événement";
            header('Location: event.php?controller=event&action=detail&id=' . $_POST['id_event']);
            exit;
        }
        
        // Validation des champs requis
        $errors = [];
        if(empty($_POST['nom']) || trim($_POST['nom']) === '') {
            $errors[] = "Le nom est obligatoire";
        }
        if(empty($_POST['prenom']) || trim($_POST['prenom']) === '') {
            $errors[] = "Le prénom est obligatoire";
        }
        if(empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "L'email est obligatoire et doit être valide";
        }
        if(empty($_POST['telephone']) || !preg_match('/^[0-9]{8}$/', $_POST['telephone'])) {
            $errors[] = "Le téléphone doit contenir exactement 8 chiffres";
        }
        if(empty($_POST['pourquoi']) || strlen(trim($_POST['pourquoi'])) < 20) {
            $errors[] = "Le champ 'Pourquoi' doit contenir au moins 20 caractères";
        }
        
        if(!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_data'] = $_POST;
            header('Location: event.php?controller=inscription&action=create&id_event=' . $_POST['id_event']);
            exit;
        }
        
        // Si l'utilisateur est connecté, utiliser son email de session
        $email_to_check = trim($_POST['email']);
        if(isset($_SESSION['id_user'])) {
            require_once __DIR__ . '/../Model/user.php';
            $userModel = new UserModel();
            $user = $userModel->getUserById($_SESSION['id_user']);
            if($user && isset($user['email'])) {
                // Utiliser l'email de l'utilisateur connecté
                $email_to_check = $user['email'];
                // Vérifier que l'email du formulaire correspond à celui de l'utilisateur connecté
                if(strtolower(trim($_POST['email'])) !== strtolower($user['email'])) {
                    $_SESSION['error'] = "❌ L'email saisi ne correspond pas à votre compte. Veuillez utiliser votre email de connexion : " . htmlspecialchars($user['email']);
                    $_SESSION['old_data'] = $_POST;
                    header('Location: event.php?controller=inscription&action=create&id_event=' . $_POST['id_event']);
                    exit;
                }
            }
        }
        
        // Vérifier si l'utilisateur est déjà inscrit à cet événement (même email, même événement)
        $this->inscription->setIdEvent($_POST['id_event']);
        $this->inscription->setEmail($email_to_check);
        if($this->inscription->checkDuplicate()) {
            $_SESSION['error'] = "❌ Vous êtes déjà inscrit à cet événement. Chaque utilisateur ne peut s'inscrire qu'une seule fois par événement.";
            $_SESSION['old_data'] = $_POST;
            header('Location: event.php?controller=inscription&action=create&id_event=' . $_POST['id_event']);
            exit;
        }
        
        // Créer l'inscription
        $this->inscription->setIdEvent($_POST['id_event']);
        $this->inscription->setNom(trim($_POST['nom']));
        $this->inscription->setPrenom(trim($_POST['prenom']));
        $this->inscription->setEmail(trim($_POST['email']));
        $this->inscription->setTelephone(trim($_POST['telephone']));
        $this->inscription->setPourquoi(trim($_POST['pourquoi']));
        $this->inscription->setStatut('en_attente');
        
        if($this->inscription->create()) {
            $_SESSION['success'] = "✅ Inscription enregistrée avec succès ! Vous recevrez une confirmation par email.";
            header('Location: event.php?controller=event&action=detail&id=' . $_POST['id_event']);
        } else {
            $_SESSION['error'] = "❌ Erreur lors de l'inscription. Veuillez réessayer.";
            $_SESSION['old_data'] = $_POST;
            header('Location: event.php?controller=inscription&action=create&id_event=' . $_POST['id_event']);
        }
        exit;
    }
    
    /**
     * FRONTOFFICE - Mes inscriptions
     */
    public function mesInscriptions() {
        require_once __DIR__ . '/../View/frontoffice/event_template.php';
        
        if(!isset($_SESSION['id_user'])) {
            header('Location: login.php?error=' . urlencode('Veuillez vous connecter.'));
            exit;
        }
        
        // Récupérer l'email de l'utilisateur connecté depuis la session ou la base de données
        require_once __DIR__ . '/../Model/user.php';
        $userModel = new UserModel();
        $user = $userModel->getUserById($_SESSION['id_user']);
        
        if(!$user || !isset($user['email'])) {
            header('Location: login.php?error=' . urlencode('Utilisateur introuvable.'));
            exit;
        }
        
        $this->inscription->setEmail($user['email']);
        $stmt = $this->inscription->readByEmail();
        $inscriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Générer le contenu HTML
        ob_start();
        include __DIR__ . '/../View/frontoffice/mes_inscriptions_content.php';
        $content = ob_get_clean();
        
        // Rendre avec le template personnalisé
        renderEventTemplate('Mes Inscriptions', $content, ['assets/css/event.css']);
    }
    
    /**
     * FRONTOFFICE - Modifier une inscription
     */
    public function edit() {
        require_once __DIR__ . '/../View/frontoffice/event_template.php';
        
        if(!isset($_SESSION['id_user'])) {
            header('Location: login.php?error=' . urlencode('Veuillez vous connecter.'));
            exit;
        }
        
        if(!isset($_GET['id'])) {
            $_SESSION['error'] = "Inscription introuvable";
            header('Location: event.php?controller=inscription&action=mes_inscriptions');
            exit;
        }
        
        $this->inscription->setIdInscription($_GET['id']);
        if(!$this->inscription->readOne()) {
            $_SESSION['error'] = "Inscription introuvable";
            header('Location: event.php?controller=inscription&action=mes_inscriptions');
            exit;
        }
        
        // Vérifier que l'utilisateur est propriétaire de l'inscription
        require_once __DIR__ . '/../Model/user.php';
        $userModel = new UserModel();
        $user = $userModel->getUserById($_SESSION['id_user']);
        if(!$user || strtolower($user['email']) !== strtolower($this->inscription->getEmail())) {
            $_SESSION['error'] = "Vous n'avez pas l'autorisation de modifier cette inscription";
            header('Location: event.php?controller=inscription&action=mes_inscriptions');
            exit;
        }
        
        $inscription = $this->inscription;
        $this->event->setIdEvent($inscription->getIdEvent());
        $this->event->readOne();
        $event = $this->event;
        
        // Générer le contenu HTML
        ob_start();
        include __DIR__ . '/../View/frontoffice/inscription_edit_content.php';
        $content = ob_get_clean();
        
        // Rendre avec le template personnalisé
        renderEventTemplate('Modifier mon inscription', $content, ['assets/css/event.css']);
    }
    
    /**
     * FRONTOFFICE - Mettre à jour une inscription
     */
    public function update() {
        if($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: event.php?controller=inscription&action=mes_inscriptions');
            exit;
        }
        
        if(!isset($_SESSION['id_user'])) {
            header('Location: login.php?error=' . urlencode('Veuillez vous connecter.'));
            exit;
        }
        
        if(!isset($_POST['id_inscription'])) {
            $_SESSION['error'] = "Inscription introuvable";
            header('Location: event.php?controller=inscription&action=mes_inscriptions');
            exit;
        }
        
        // Charger l'inscription existante pour vérifier les permissions
        $this->inscription->setIdInscription($_POST['id_inscription']);
        if(!$this->inscription->readOne()) {
            $_SESSION['error'] = "Inscription introuvable";
            header('Location: event.php?controller=inscription&action=mes_inscriptions');
            exit;
        }
        
        // Vérifier que l'utilisateur est propriétaire de l'inscription
        require_once __DIR__ . '/../Model/user.php';
        $userModel = new UserModel();
        $user = $userModel->getUserById($_SESSION['id_user']);
        if(!$user || strtolower($user['email']) !== strtolower($this->inscription->getEmail())) {
            $_SESSION['error'] = "Vous n'avez pas l'autorisation de modifier cette inscription";
            header('Location: event.php?controller=inscription&action=mes_inscriptions');
            exit;
        }
        
        // Validation des champs requis
        $errors = [];
        if(empty($_POST['nom']) || trim($_POST['nom']) === '') {
            $errors[] = "Le nom est obligatoire";
        }
        if(empty($_POST['prenom']) || trim($_POST['prenom']) === '') {
            $errors[] = "Le prénom est obligatoire";
        }
        if(empty($_POST['telephone']) || !preg_match('/^[0-9]{8}$/', $_POST['telephone'])) {
            $errors[] = "Le téléphone doit contenir exactement 8 chiffres";
        }
        if(empty($_POST['pourquoi']) || strlen(trim($_POST['pourquoi'])) < 20) {
            $errors[] = "Le champ 'Pourquoi' doit contenir au moins 20 caractères";
        }
        
        if(!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_data'] = $_POST;
            header('Location: event.php?controller=inscription&action=edit&id=' . $_POST['id_inscription']);
            exit;
        }
        
        // Mettre à jour l'inscription
        $this->inscription->setNom(trim($_POST['nom']));
        $this->inscription->setPrenom(trim($_POST['prenom']));
        $this->inscription->setTelephone(trim($_POST['telephone']));
        $this->inscription->setPourquoi(trim($_POST['pourquoi']));
        // L'email ne peut pas être modifié, on garde celui de l'inscription existante
        
        if($this->inscription->update()) {
            $_SESSION['success'] = "✅ Inscription modifiée avec succès !";
            header('Location: event.php?controller=inscription&action=mes_inscriptions');
        } else {
            $_SESSION['error'] = "❌ Erreur lors de la modification";
            $_SESSION['old_data'] = $_POST;
            header('Location: event.php?controller=inscription&action=edit&id=' . $_POST['id_inscription']);
        }
        exit;
    }
    
    /**
     * FRONTOFFICE - Annuler une inscription
     */
    public function delete() {
        if(!isset($_SESSION['id_user'])) {
            header('Location: login.php?error=' . urlencode('Veuillez vous connecter.'));
            exit;
        }
        
        if(!isset($_GET['id'])) {
            $_SESSION['error'] = "Inscription introuvable";
            header('Location: event.php?controller=inscription&action=mes_inscriptions');
            exit;
        }
        
        // Charger l'inscription pour vérifier les permissions
        $this->inscription->setIdInscription($_GET['id']);
        if(!$this->inscription->readOne()) {
            $_SESSION['error'] = "Inscription introuvable";
            header('Location: event.php?controller=inscription&action=mes_inscriptions');
            exit;
        }
        
        // Vérifier que l'utilisateur est propriétaire de l'inscription
        require_once __DIR__ . '/../Model/user.php';
        $userModel = new UserModel();
        $user = $userModel->getUserById($_SESSION['id_user']);
        if(!$user || strtolower($user['email']) !== strtolower($this->inscription->getEmail())) {
            $_SESSION['error'] = "Vous n'avez pas l'autorisation d'annuler cette inscription";
            header('Location: event.php?controller=inscription&action=mes_inscriptions');
            exit;
        }
        
        if($this->inscription->delete()) {
            $_SESSION['success'] = "✅ Inscription annulée avec succès !";
        } else {
            $_SESSION['error'] = "❌ Erreur lors de l'annulation";
        }
        
        header('Location: event.php?controller=inscription&action=mes_inscriptions');
        exit;
    }
    
    /**
     * BACKOFFICE - Confirmer une inscription
     */
    public function confirmer() {
        // Vérifier si admin
        if (!isset($_SESSION['id_user']) || ($_SESSION['role'] ?? '') !== 'admin') {
            header('Location: ../View/frontoffice/login.php?error=' . urlencode('Accès refusé.'));
            exit;
        }
        
        if(!isset($_GET['id'])) {
            $_SESSION['error'] = "Inscription introuvable";
            header('Location: event.php?controller=inscription&action=liste');
            exit;
        }
        
        $this->inscription->setIdInscription($_GET['id']);
        
        // Utiliser la méthode confirmer() du modèle qui met à jour uniquement le statut
        if($this->inscription->confirmer()) {
            $_SESSION['success'] = "✅ Inscription confirmée avec succès !";
        } else {
            $_SESSION['error'] = "❌ Erreur lors de la confirmation";
        }
        
        // Détecter si c'est une requête AJAX (via loadContent)
        $isAjax = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') 
                  || (isset($_GET['ajax']) && $_GET['ajax'] == '1');
        
        // Si un event_id est fourni, rediriger vers la page des inscriptions de l'événement
        if(isset($_GET['event_id']) && !empty($_GET['event_id'])) {
            $event_id = intval($_GET['event_id']);
            // Toujours rediriger vers la page des inscriptions (le JavaScript rechargera la page)
            header('Location: ../View/backoffice/event_inscriptions.php?id_event=' . urlencode($event_id) . '&embed=1');
            exit;
        } else {
            // Pas d'event_id, retourner vers la liste des inscriptions
            if ($isAjax) {
                // Pour les requêtes AJAX, recharger la liste des inscriptions
                // Récupérer les inscriptions mises à jour
                $view_all = isset($_GET['view']) && $_GET['view'] === 'all';
                $event_id_param = isset($_GET['event_id']) ? intval($_GET['event_id']) : null;
                
                if ($view_all || !$event_id_param) {
                    // Afficher toutes les inscriptions
                    $query = "SELECT i.*, 
                                     e.titre as event_titre, e.date_event, e.lieu, e.type_event,
                                     u.id_user, u.name as user_name, 
                                     u.email as user_email, u.profile_pic, u.role
                              FROM inscriptions i
                              LEFT JOIN events e ON i.id_event = e.id_event
                              LEFT JOIN user u ON i.email = u.email
                              ORDER BY i.date_inscription DESC";
                    $stmt = $this->db->prepare($query);
                    $stmt->execute();
                    $inscriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $event = null;
                } else {
                    // Inscriptions pour un événement spécifique
                    $query = "SELECT i.*, 
                                     e.titre as event_titre, e.date_event, e.lieu, e.type_event,
                                     u.id_user, u.name as user_name, 
                                     u.email as user_email, u.profile_pic, u.role
                              FROM inscriptions i
                              LEFT JOIN events e ON i.id_event = e.id_event
                              LEFT JOIN user u ON i.email = u.email
                              WHERE i.id_event = :id_event
                              ORDER BY i.date_inscription DESC";
                    $stmt = $this->db->prepare($query);
                    $stmt->bindParam(':id_event', $event_id_param);
                    $stmt->execute();
                    $inscriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    // Récupérer les détails de l'événement
                    $this->event->setIdEvent($event_id_param);
                    $event = $this->event->readOne() ? $this->event : null;
                }
                
                // Inclure la vue
                include __DIR__ . '/../View/backoffice/inscriptions_list_content.php';
                exit;
            } else {
                header('Location: event.php?controller=inscription&action=liste');
            }
        }
        exit;
    }
    
    /**
     * BACKOFFICE - Annuler/Refuser une inscription
     */
    public function annuler() {
        // Vérifier si admin
        if (!isset($_SESSION['id_user']) || ($_SESSION['role'] ?? '') !== 'admin') {
            header('Location: ../View/frontoffice/login.php?error=' . urlencode('Accès refusé.'));
            exit;
        }
        
        if(!isset($_GET['id'])) {
            $_SESSION['error'] = "Inscription introuvable";
            header('Location: event.php?controller=inscription&action=liste');
            exit;
        }
        
        $this->inscription->setIdInscription($_GET['id']);
        
        // Utiliser la méthode annuler() du modèle qui met à jour uniquement le statut
        if($this->inscription->annuler()) {
            $_SESSION['success'] = "✅ Inscription annulée avec succès !";
            $success = true;
        } else {
            $_SESSION['error'] = "❌ Erreur lors de l'annulation";
            $success = false;
        }
        
        // Détecter si c'est une requête AJAX (via loadContent)
        $isAjax = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') 
                  || (isset($_GET['ajax']) && $_GET['ajax'] == '1');
        
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['success' => $success, 'message' => $success ? 'Inscription annulée avec succès' : 'Erreur lors de l\'annulation']);
            exit;
        }
        
        // Si un event_id est fourni, rediriger vers la page des inscriptions de l'événement
        if(isset($_GET['event_id']) && !empty($_GET['event_id'])) {
            $event_id = intval($_GET['event_id']);
            header('Location: ../View/backoffice/event_inscriptions.php?id_event=' . urlencode($event_id) . '&embed=1');
            exit;
        } else {
            // Pas d'event_id, retourner vers la liste des inscriptions
            header('Location: event.php?controller=inscription&action=liste');
        }
        exit;
    }
}

// Point d'entrée pour les appels directs au contrôleur
if (basename($_SERVER['PHP_SELF']) === 'inscriptioncontroller.php') {
    $action = $_GET['action'] ?? 'liste';
    $controller = new InscriptionController();
    
    if (method_exists($controller, $action)) {
        $controller->$action();
    } else {
        header('HTTP/1.0 404 Not Found');
        echo "Action non trouvée: " . htmlspecialchars($action);
    }
}
?>

