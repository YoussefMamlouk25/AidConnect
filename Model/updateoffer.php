<?php
class UpdateOffer {
    private $id_update_offer;   // AUTO_INCREMENT
    private $id_offer;          // int(11) - Foreign key to offer table
    private $id_user;           // int(11) - NOT NULL
    private $texte;             // text
    private $media;             // varchar(255)

    public function __construct(
        $id_offer = null,
        $id_user = null,
        $texte = null,
        $media = null,
        $id_update_offer = null
    ) {
        $this->id_update_offer = $id_update_offer;
        $this->id_offer = $id_offer !== null ? (int)$id_offer : null;
        // Allow id_user to be null for anonymous users (foreign key constraint)
        $this->id_user = ($id_user !== null && $id_user !== '' && $id_user > 0) ? (int)$id_user : null;
        $this->texte = $texte;
        $this->media = $media;
    }

    public function getIdUpdateOffer(){ return $this->id_update_offer; }
    public function getIdOffer(){ return $this->id_offer; }
    public function getIdUser(){ return $this->id_user; }
    public function getTexte(){ return $this->texte; }
    public function getMedia(){ return $this->media; }

    public function setIdUpdateOffer($v){ $this->id_update_offer = (int)$v; }
    public function setIdOffer($v){ $this->id_offer = $v !== null ? (int)$v : null; }
    public function setIdUser($v){ $this->id_user = ($v !== null && $v !== '' && $v > 0) ? (int)$v : null; }
    public function setTexte($v){ $this->texte = $v; }
    public function setMedia($v){ $this->media = $v; }
}

