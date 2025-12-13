<!-- ============================================
     FICHIER: Model/Event.php - CORRIGÉ
     Correction du comptage des places
     ============================================ -->
<?php
// Model/Event.php
class Event {
    private $conn;
    private $table = "events";
    
    // Propriétés
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
    
    public function __construct($db) {
        $this->conn = $db;
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
    public function setIdEvent($id) { $this->id_event = htmlspecialchars(strip_tags($id)); }
    public function setTitre($titre) { $this->titre = htmlspecialchars(strip_tags($titre)); }
    public function setDescription($desc) { $this->description = htmlspecialchars(strip_tags($desc)); }
    public function setDateEvent($date) { $this->date_event = htmlspecialchars(strip_tags($date)); }
    public function setLieu($lieu) { $this->lieu = htmlspecialchars(strip_tags($lieu)); }
    public function setNombrePlaces($nb) { $this->nombre_places = intval($nb); }
    public function setTypeEvent($type) { $this->type_event = htmlspecialchars(strip_tags($type)); }
    public function setImage($img) { $this->image = htmlspecialchars(strip_tags($img)); }
    public function setStatut($statut) { $this->statut = htmlspecialchars(strip_tags($statut)); }
    
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
        $stmt->bindParam(':image', $this->image);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }
    
    // Lire tous les événements
    public function readAll() {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE statut = 'actif' 
                  ORDER BY date_event DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt;
    }
    
    // Lire un événement par ID
    public function readOne() {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE id_event = :id LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id_event);
        $stmt->execute();
        
        $row = $stmt->fetch();
        
        if($row) {
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
        $stmt->bindParam(':image', $this->image);
        $stmt->bindParam(':id', $this->id_event);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }
    
    // Supprimer un événement (soft delete)
    public function delete() {
        $query = "UPDATE " . $this->table . "
                  SET statut = 'supprime'
                  WHERE id_event = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id_event);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }
    
    // Rechercher des événements
    public function search($keyword) {
        $query = "SELECT * FROM " . $this->table . "
                  WHERE statut = 'actif' 
                  AND (titre LIKE :keyword 
                       OR description LIKE :keyword 
                       OR lieu LIKE :keyword)
                  ORDER BY date_event DESC";
        
        $stmt = $this->conn->prepare($query);
        $keyword = "%{$keyword}%";
        $stmt->bindParam(':keyword', $keyword);
        $stmt->execute();
        
        return $stmt;
    }
    
    /**
     * ✅ CORRECTION: Compter TOUTES les inscriptions (confirmé + en_attente)
     * ANCIENNE VERSION: ne comptait que 'confirme'
     * NOUVELLE VERSION: compte confirme ET en_attente
     */
    public function countInscriptions() {
        $query = "SELECT COUNT(*) as total 
                  FROM inscriptions 
                  WHERE id_event = :id_event 
                  AND statut IN ('confirme', 'en_attente')";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_event', $this->id_event);
        $stmt->execute();
        
        $row = $stmt->fetch();
        return $row['total'];
    }
    
    /**
     * ✅ NOUVEAU: Compter uniquement les inscriptions confirmées
     */
    public function countInscriptionsConfirmees() {
        $query = "SELECT COUNT(*) as total 
                  FROM inscriptions 
                  WHERE id_event = :id_event 
                  AND statut = 'confirme'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_event', $this->id_event);
        $stmt->execute();
        
        $row = $stmt->fetch();
        return $row['total'];
    }
    
    /**
     * ✅ NOUVEAU: Compter les inscriptions en attente
     */
    public function countInscriptionsEnAttente() {
        $query = "SELECT COUNT(*) as total 
                  FROM inscriptions 
                  WHERE id_event = :id_event 
                  AND statut = 'en_attente'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_event', $this->id_event);
        $stmt->execute();
        
        $row = $stmt->fetch();
        return $row['total'];
    }
    
    /**
     * ✅ NOUVEAU: Vérifier si les places sont disponibles
     */
    public function hasPlacesDisponibles() {
        $total_inscriptions = $this->countInscriptions();
        $places_restantes = $this->nombre_places - $total_inscriptions;
        
        return $places_restantes > 0;
    }
    
    /**
     * ✅ NOUVEAU: Obtenir le nombre de places restantes
     */
    public function getPlacesRestantes() {
        $total_inscriptions = $this->countInscriptions();
        $places_restantes = $this->nombre_places - $total_inscriptions;
        
        return max(0, $places_restantes); // Ne peut pas être négatif
    }
}
?>


<!-- ============================================
     EXPLICATION DU PROBLÈME ET DE LA SOLUTION
     ============================================ -->

