<?php
class Inscription {
    private $conn;
    private $table = 'inscriptions';
    
    private $id_inscription;
    private $id_event;
    private $nom;
    private $prenom;
    private $email;
    private $telephone;
    private $pourquoi;
    private $statut;
    private $date_inscription;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    // Getters
    public function getIdInscription() { return $this->id_inscription; }
    public function getIdEvent() { return $this->id_event; }
    public function getNom() { return $this->nom; }
    public function getPrenom() { return $this->prenom; }
    public function getEmail() { return $this->email; }
    public function getTelephone() { return $this->telephone; }
    public function getPourquoi() { return $this->pourquoi; }
    public function getStatut() { return $this->statut; }
    public function getDateInscription() { return $this->date_inscription; }
    
    // Setters
    public function setIdInscription($v) { $this->id_inscription = (int)$v; }
    public function setIdEvent($v) { $this->id_event = (int)$v; }
    public function setNom($v) { $this->nom = $v; }
    public function setPrenom($v) { $this->prenom = $v; }
    public function setEmail($v) { $this->email = $v; }
    public function setTelephone($v) { $this->telephone = $v; }
    public function setPourquoi($v) { $this->pourquoi = $v; }
    public function setStatut($v) { $this->statut = $v; }
    
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
    
    // Vérifier si l'utilisateur est déjà inscrit
    public function checkDuplicate() {
        $query = "SELECT COUNT(*) as count FROM " . $this->table . " 
                  WHERE id_event = :id_event AND email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_event', $this->id_event);
        $stmt->bindParam(':email', $this->email);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return ($result['count'] ?? 0) > 0;
    }
    
    // Lire une inscription
    public function readOne() {
        $query = "SELECT * FROM " . $this->table . " WHERE id_inscription = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id_inscription);
        $stmt->execute();
        
        if($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $this->id_inscription = $row['id_inscription'];
            $this->id_event = $row['id_event'];
            $this->nom = $row['nom'] ?? null;
            $this->prenom = $row['prenom'] ?? null;
            $this->email = $row['email'];
            $this->telephone = $row['telephone'];
            $this->pourquoi = $row['pourquoi'] ?? null;
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
                      email = :email,
                      telephone = :telephone,
                      pourquoi = :pourquoi,
                      statut = :statut
                  WHERE id_inscription = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nom', $this->nom);
        $stmt->bindParam(':prenom', $this->prenom);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':telephone', $this->telephone);
        $stmt->bindParam(':pourquoi', $this->pourquoi);
        $stmt->bindParam(':statut', $this->statut);
        $stmt->bindParam(':id', $this->id_inscription);
        
        return $stmt->execute();
    }
    
    // Lire les inscriptions par email
    public function readByEmail() {
        $query = "SELECT * FROM " . $this->table . " WHERE email = :email ORDER BY date_inscription DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $this->email);
        $stmt->execute();
        return $stmt;
    }
    
    // Confirmer une inscription
    public function confirmer() {
        $query = "UPDATE " . $this->table . "
                  SET statut = 'confirme'
                  WHERE id_inscription = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id_inscription);
        
        if($stmt->execute()) {
            $this->statut = 'confirme';
            return true;
        }
        return false;
    }
    
    // Annuler une inscription
    public function annuler() {
        $query = "UPDATE " . $this->table . "
                  SET statut = 'annule'
                  WHERE id_inscription = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id_inscription);
        
        if($stmt->execute()) {
            $this->statut = 'annule';
            return true;
        }
        return false;
    }
    
    // Supprimer une inscription
    public function delete() {
        $query = "DELETE FROM " . $this->table . " WHERE id_inscription = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id_inscription);
        return $stmt->execute();
    }
}
?>

