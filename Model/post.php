<?php
class Post {
    private $id_post;
    private $id_user;
    private $titre;
    private $description;
    private $region;
    private $media;
    private $date_creation;

    public function __construct(
        $id_post = null,
        $id_user = null,
        $titre = null,
        $description = null,
        $region = null,
        $media = null,
        $date_creation = null
    ) {
        $this->id_post = $id_post;
        $this->id_user = $id_user;
        $this->titre = $titre;
        $this->description = $description;
        $this->region = $region;
        $this->media = $media;
        $this->date_creation = $date_creation;
    }

    // --- Getters ---
    public function getIdPost() { return $this->id_post; }
    public function getIdUser() { return $this->id_user; }
    public function getTitre() { return $this->titre; }
    public function getDescription() { return $this->description; }
    public function getRegion() { return $this->region; }
    public function getMedia() { return $this->media; }
    public function getDateCreation() { return $this->date_creation; }

    // --- Setters ---
    public function setIdPost($id_post) { $this->id_post = $id_post; }
    public function setIdUser($id_user) { $this->id_user = $id_user; }
    public function setTitre($titre) { $this->titre = $titre; }
    public function setDescription($description) { $this->description = $description; }
    public function setRegion($region) { $this->region = $region; }
    public function setMedia($media) { $this->media = $media; }
    public function setDateCreation($date_creation) { $this->date_creation = $date_creation; }
}
?>
