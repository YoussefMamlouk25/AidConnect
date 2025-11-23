<?php
class Offer {
    private $id_offer;   // AUTO_INCREMENT
    private $id_user;    // int
    private $description;// text
    private $media;      // varchar
    private $nom;        // varchar
    private $num;        // varchar
    private $email;      // varchar
    private $type_don;   // varchar

    public function __construct(
        $id_user = null,
        $description = null,
        $media = null,
        $nom = null,
        $num = null,
        $email = null,
        $type_don = null,
        $id_offer = null
    ) {
        $this->id_offer   = $id_offer;
        $this->id_user    = $id_user !== null ? (int)$id_user : null;
        $this->description= $description;
        $this->media      = $media;
        $this->nom        = $nom;
        $this->num        = $num;
        $this->email      = $email;
        $this->type_don   = $type_don;
    }

    public function getIdOffer(){ return $this->id_offer; }
    public function getIdUser(){ return $this->id_user; }
    public function getDescription(){ return $this->description; }
    public function getMedia(){ return $this->media; }
    public function getNom(){ return $this->nom; }
    public function getNum(){ return $this->num; }
    public function getEmail(){ return $this->email; }
    public function getTypeDon(){ return $this->type_don; }

    public function setIdOffer($v){ $this->id_offer = (int)$v; }
    public function setIdUser($v){ $this->id_user = (int)$v; }
    public function setDescription($v){ $this->description = $v; }
    public function setMedia($v){ $this->media = $v; }
    public function setNom($v){ $this->nom = $v; }
    public function setNum($v){ $this->num = $v; }
    public function setEmail($v){ $this->email = $v; }
    public function setTypeDon($v){ $this->type_don = $v; }
}