<?php

class PostUpdate {
    private $id;
    private $titre;
    private $description;
    private $photo;
    private $ville;
    private $intervention;
    private $joursReponse;
    private $joursResolution;

    public function __construct(
        $id,
        $titre,
        $description,
        $photo,
        $ville,
        $intervention,
        $joursReponse,
        $joursResolution
    ) {
        $this->id = $id;
        $this->titre = $titre;
        $this->description = $description;
        $this->photo = $photo;
        $this->ville = $ville;
        $this->intervention = $intervention;
        $this->joursReponse = $joursReponse;
        $this->joursResolution = $joursResolution;
    }

    // --- Getters ---
    public function getId() { return $this->id; }
    public function getTitre() { return $this->titre; }
    public function getDescription() { return $this->description; }
    public function getPhoto() { return $this->photo; }
    public function getVille() { return $this->ville; }
    public function getIntervention() { return $this->intervention; }
    public function getJoursReponse() { return $this->joursReponse; }
    public function getJoursResolution() { return $this->joursResolution; }

    // --- Setters ---
    public function setId($id) { $this->id = $id; }
    public function setTitre($titre) { $this->titre = $titre; }
    public function setDescription($description) { $this->description = $description; }
    public function setPhoto($photo) { $this->photo = $photo; }
    public function setVille($ville) { $this->ville = $ville; }
    public function setIntervention($intervention) { $this->intervention = $intervention; }
    public function setJoursReponse($joursReponse) { $this->joursReponse = $joursReponse; }
    public function setJoursResolution($joursResolution) { $this->joursResolution = $joursResolution; }
}

?>

