<?php
class Event {
    private $conn;
    private $table = 'events';
    
    private $id_event;
    private $titre;
    private $description;
    private $date_event;
    private $lieu;
    private $nombre_places;
    private $type_event;
    private $image;
    private $statut;
    private $date_creation;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    // Getters
    public function getIdEvent() { return $this->id_event; }
    public function getTitre() { return $this->titre; }
    public function getDescription() { return $this->description; }
    public function getDateEvent() { return $this->date_event; }
    public function getLieu() { return $this->lieu; }
    public function getNombrePlaces() { return $this->nombre_places; }
    public function getTypeEvent() { return $this->type_event; }
    public function getImage() { return $this->image; }
    public function getStatut() { return $this->statut; }
    public function getDateCreation() { return $this->date_creation; }
    
    // Setters
    public function setIdEvent($v) { $this->id_event = (int)$v; }
    public function setTitre($v) { $this->titre = $v; }
    public function setDescription($v) { $this->description = $v; }
    public function setDateEvent($v) { $this->date_event = $v; }
    public function setLieu($v) { $this->lieu = $v; }
    public function setNombrePlaces($v) { $this->nombre_places = (int)$v; }
    public function setTypeEvent($v) { $this->type_event = $v; }
    public function setImage($v) { $this->image = $v; }
    public function setStatut($v) { $this->statut = $v; }
    
    // Créer un événement
    public function create() {
        $query = "INSERT INTO " . $this->table . "
                  SET titre = :titre,
                      description = :description,
                      date_event = :date_event,
                      lieu = :lieu,
                      nombre_places = :nombre_places,
                      type_event = :type_event,
                      image = :image,
                      statut = 'actif'";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':titre', $this->titre);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':date_event', $this->date_event);
        $stmt->bindParam(':lieu', $this->lieu);
        $stmt->bindParam(':nombre_places', $this->nombre_places);
        $stmt->bindParam(':type_event', $this->type_event);
        
        // Handle image like media in offercontroller - proper null handling
        if ($this->image === null || $this->image === '') {
            $stmt->bindValue(':image', null, PDO::PARAM_NULL);
        } else {
            $stmt->bindParam(':image', $this->image);
        }
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }
    
    // Lire tous les événements
    public function readAll() {
        // Simple query - duplicates will be filtered in PHP
        $query = "SELECT * FROM " . $this->table . " WHERE statut = 'actif' ORDER BY id_event DESC, date_event ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
    
    // Lire un événement
    public function readOne() {
        $query = "SELECT * FROM " . $this->table . " WHERE id_event = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id_event);
        $stmt->execute();
        
        if($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $this->id_event = $row['id_event'];
            $this->titre = $row['titre'];
            $this->description = $row['description'];
            $this->date_event = $row['date_event'];
            $this->lieu = $row['lieu'];
            $this->nombre_places = $row['nombre_places'];
            $this->type_event = $row['type_event'];
            $this->image = $row['image'];
            $this->statut = $row['statut'];
            $this->date_creation = $row['date_creation'];
            return true;
        }
        return false;
    }
    
    // Mettre à jour un événement
    public function update() {
        $query = "UPDATE " . $this->table . "
                  SET titre = :titre,
                      description = :description,
                      date_event = :date_event,
                      lieu = :lieu,
                      nombre_places = :nombre_places,
                      type_event = :type_event,
                      image = :image
                  WHERE id_event = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':titre', $this->titre);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':date_event', $this->date_event);
        $stmt->bindParam(':lieu', $this->lieu);
        $stmt->bindParam(':nombre_places', $this->nombre_places);
        $stmt->bindParam(':type_event', $this->type_event);
        
        // Handle image like media in offercontroller - proper null handling
        if ($this->image === null || $this->image === '') {
            $stmt->bindValue(':image', null, PDO::PARAM_NULL);
        } else {
            $stmt->bindParam(':image', $this->image);
        }
        
        $stmt->bindParam(':id', $this->id_event);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }
    
    // Supprimer un événement
    public function delete() {
        $query = "UPDATE " . $this->table . " SET statut = 'supprime' WHERE id_event = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id_event);
        return $stmt->execute();
    }
    
    // Compter les inscriptions pour cet événement
    public function countInscriptions() {
        $query = "SELECT COUNT(*) as count FROM inscriptions WHERE id_event = :id_event AND statut = 'confirme'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_event', $this->id_event);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return isset($result['count']) ? $result['count'] : 0;
    }
}
?>

