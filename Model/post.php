<?php
class Post {
    private $id;
    private $titre;
    private $nom;
    private $numero;
    private $email;
    private $region;
    private $description;
    private $media;
    private $dateCreation;

    public function __construct($id = null, $titre = null, $nom = null, $numero = null, $email = null, $region = null, $description = null, $media = null, $dateCreation = null) {
        $this->id = $id;
        $this->titre = $titre;
        $this->nom = $nom;
        $this->numero = $numero;
        $this->email = $email;
        $this->region = $region;
        $this->description = $description;
        $this->media = $media;
        $this->dateCreation = $dateCreation;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getTitre() { return $this->titre; }
    public function getNom() { return $this->nom; }
    public function getNumero() { return $this->numero; }
    public function getEmail() { return $this->email; }
    public function getRegion() { return $this->region; }
    public function getDescription() { return $this->description; }
    public function getMedia() { return $this->media; }
    public function getDateCreation() { return $this->dateCreation; }

    // Setters
    public function setId($id) { $this->id = $id; }
    public function setTitre($titre) { $this->titre = $titre; }
    public function setNom($nom) { $this->nom = $nom; }
    public function setNumero($numero) { $this->numero = $numero; }
    public function setEmail($email) { $this->email = $email; }
    public function setRegion($region) { $this->region = $region; }
    public function setDescription($description) { $this->description = $description; }
    public function setMedia($media) { $this->media = $media; }
    public function setDateCreation($dateCreation) { $this->dateCreation = $dateCreation; }
}
?>
