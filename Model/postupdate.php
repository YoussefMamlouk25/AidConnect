<?php

class PostUpdate {
    private $id_update_post;
    private $id_post;
    private $id_user;
    private $texte;
    private $media;
    private $realiser;
    private $temp_reponse;
    private $temp_resolue;

    public function __construct(
        $id_post,
        $texte,
        $id_update_post = null,
        $id_user = null,
        $media = '',
        $realiser = '',
        $temp_reponse = '',
        $temp_resolue = ''
    ) {
        $this->id_update_post = $id_update_post;
        $this->id_post = $id_post;
        $this->id_user = $id_user;
        $this->texte = $texte;
        $this->media = $media;
        $this->realiser = $realiser;
        $this->temp_reponse = $temp_reponse;
        $this->temp_resolue = $temp_resolue;
    }

    // --- Getters ---
    public function getIdUpdatePost() { return $this->id_update_post; }
    public function getIdPost() { return $this->id_post; }
    public function getIdUser() { return $this->id_user; }
    public function getTexte() { return $this->texte; }
    public function getMedia() { return $this->media; }
    public function getRealiser() { return $this->realiser; }
    public function getTempReponse() { return $this->temp_reponse; }
    public function getTempResolue() { return $this->temp_resolue; }

    // --- Setters ---
    public function setIdUpdatePost($id_update_post) { $this->id_update_post = $id_update_post; }
    public function setIdPost($id_post) { $this->id_post = $id_post; }
    public function setIdUser($id_user) { $this->id_user = $id_user; }
    public function setTexte($texte) { $this->texte = $texte; }
    public function setMedia($media) { $this->media = $media; }
    public function setRealiser($realiser) { $this->realiser = $realiser; }
    public function setTempReponse($temp_reponse) { $this->temp_reponse = $temp_reponse; }
    public function setTempResolue($temp_resolue) { $this->temp_resolue = $temp_resolue; }
}

?>
