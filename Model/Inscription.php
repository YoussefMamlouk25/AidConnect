<?php
// Model/Inscription.php
class Inscription {
    private $conn;
    private $table = "inscriptions";
    
    // Propriétés
    private $id_inscription;
    private $id_event;
    private $nom;
    private $prenom;
    private $email;
    private $telephone;
    private $pourquoi;
    private $avis;
    private $note;
    private $statut;
    private $date_inscription;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Getters
    public function getIdInscription() { return $this->id_inscription; }
    public function getIdEvent() { return $this->id_event; }
    public function getNom() { return $this->nom; }
    public function getPrenom() { return $this->prenom; }
    public function getEmail() { return $this->email; }
    public function getTelephone() { return $this->telephone; }
    public function getPourquoi() { return $this->pourquoi; }
    public function getAvis() { return $this->avis; }
    public function getNote() { return $this->note; }
    public function getStatut() { return $this->statut; }
    public function getDateInscription() { return $this->date_inscription; }
    
    // Setters
    public function setIdInscription($id) { $this->id_inscription = htmlspecialchars(strip_tags($id)); }
    public function setIdEvent($id) { $this->id_event = intval($id); }
    public function setNom($nom) { $this->nom = htmlspecialchars(strip_tags($nom)); }
    public function setPrenom($prenom) { $this->prenom = htmlspecialchars(strip_tags($prenom)); }
    public function setEmail($email) { $this->email = htmlspecialchars(strip_tags($email)); }
    public function setTelephone($tel) { $this->telephone = htmlspecialchars(strip_tags($tel)); }
    public function setPourquoi($pourquoi) { $this->pourquoi = htmlspecialchars(strip_tags($pourquoi)); }
    public function setAvis($avis) { $this->avis = htmlspecialchars(strip_tags($avis)); }
    public function setNote($note) { $this->note = intval($note); }
    public function setStatut($statut) { $this->statut = htmlspecialchars(strip_tags($statut)); }
    
    // Créer une inscription
    public function create() {
        $query = "INSERT INTO " . $this->table . "
                  SET id_event = :id_event,
                      nom = :nom,
                      prenom = :prenom,
                      email = :email,
                      telephone = :telephone,
                      pourquoi = :pourquoi,
                      statut = 'en_attente'";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':id_event', $this->id_event);
        $stmt->bindParam(':nom', $this->nom);
        $stmt->bindParam(':prenom', $this->prenom);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':telephone', $this->telephone);
        $stmt->bindParam(':pourquoi', $this->pourquoi);
        
        if($stmt->execute()) {
            $this->id_inscription = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }
    
    // Lire toutes les inscriptions (BackOffice)
    public function readAll() {
        $query = "SELECT i.*, e.titre as event_titre, e.date_event
                  FROM " . $this->table . " i
                  LEFT JOIN events e ON i.id_event = e.id_event
                  ORDER BY i.date_inscription DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt;
    }
    
    // Lire les inscriptions par email (FrontOffice)
    public function readByEmail() {
        $query = "SELECT i.*, e.titre as event_titre, e.date_event, e.lieu
                  FROM " . $this->table . " i
                  LEFT JOIN events e ON i.id_event = e.id_event
                  WHERE i.email = :email
                  ORDER BY i.date_inscription DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $this->email);
        $stmt->execute();
        
        return $stmt;
    }
    
    // Lire les inscriptions pour un événement
    public function readByEvent() {
        $query = "SELECT * FROM " . $this->table . "
                  WHERE id_event = :id_event
                  ORDER BY date_inscription DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_event', $this->id_event);
        $stmt->execute();
        
        return $stmt;
    }
    
    // Lire une inscription par ID
    public function readOne() {
        $query = "SELECT i.*, e.titre as event_titre, e.date_event, e.lieu
                  FROM " . $this->table . " i
                  LEFT JOIN events e ON i.id_event = e.id_event
                  WHERE i.id_inscription = :id LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id_inscription);
        $stmt->execute();
        
        $row = $stmt->fetch();
        
        if($row) {
            $this->id_event = $row['id_event'];
            $this->nom = $row['nom'];
            $this->prenom = $row['prenom'];
            $this->email = $row['email'];
            $this->telephone = $row['telephone'];
            $this->pourquoi = $row['pourquoi'];
            $this->avis = $row['avis'];
            $this->note = $row['note'];
            $this->statut = $row['statut'];
            $this->date_inscription = $row['date_inscription'];
            return true;
        }
        return false;
    }
    
    // Mettre à jour une inscription
    public function update() {
        $query = "UPDATE " . $this->table . "
                  SET nom = :nom,
                      prenom = :prenom,
                      telephone = :telephone,
                      pourquoi = :pourquoi
                  WHERE id_inscription = :id 
                  AND email = :email";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':nom', $this->nom);
        $stmt->bindParam(':prenom', $this->prenom);
        $stmt->bindParam(':telephone', $this->telephone);
        $stmt->bindParam(':pourquoi', $this->pourquoi);
        $stmt->bindParam(':id', $this->id_inscription);
        $stmt->bindParam(':email', $this->email);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }
    
    // Ajouter un avis
    public function addAvis() {
        $query = "UPDATE " . $this->table . "
                  SET avis = :avis,
                      note = :note
                  WHERE id_inscription = :id 
                  AND email = :email";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':avis', $this->avis);
        $stmt->bindParam(':note', $this->note);
        $stmt->bindParam(':id', $this->id_inscription);
        $stmt->bindParam(':email', $this->email);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }
    
    // Supprimer une inscription
    public function delete() {
        $query = "DELETE FROM " . $this->table . "
                  WHERE id_inscription = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id_inscription);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }
    
    // Confirmer une inscription (Admin)
    public function confirmer() {
        $query = "UPDATE " . $this->table . "
                  SET statut = 'confirme'
                  WHERE id_inscription = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id_inscription);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }
    
    // Vérifier si l'email est déjà inscrit à cet événement
    public function checkDuplicate() {
        $query = "SELECT COUNT(*) as total 
                  FROM " . $this->table . "
                  WHERE id_event = :id_event 
                  AND email = :email";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_event', $this->id_event);
        $stmt->bindParam(':email', $this->email);
        $stmt->execute();
        
        $row = $stmt->fetch();
        return $row['total'] > 0;
    }
}
?>