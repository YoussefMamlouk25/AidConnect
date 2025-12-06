<?php
class Post {
    private $id;
    private $iduser;
    private $titre;
    private $description;
    private $media;
    private $dateCreation;

    public function __construct($id = null, $titre = null, $iduser = null, $description = null, $media = null, $dateCreation = null) {
        $this->id = $id;
        $this->titre = $titre;
        $this->iduser = $iduser;
        $this->description = $description;
        $this->media = $media;
        $this->dateCreation = $dateCreation;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getIduser() { return $this->iduser; }
    public function getTitre() { return $this->titre; }
    public function getDescription() { return $this->description; }
    public function getMedia() { return $this->media; }
    public function getDateCreation() { return $this->dateCreation; }


    // Setters
    public function setId($id) { $this->id = $id; }
    public function setIduser($iduser) { $this->iduser = $iduser; }
    public function setTitre($titre) { $this->titre = $titre; }

    public function setDescription($description) { $this->description = $description; }
    public function setMedia($media) { $this->media = $media; }
    public function setDateCreation($dateCreation) { $this->dateCreation = $dateCreation; }
}
?>
