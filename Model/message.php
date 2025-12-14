<?php
class Message {
    private $id_message;
    private $id_reclamation;
    private $message;
    private $date;
    private $isadmin;
    private $media;

    public function __construct($id_reclamation = null, $message = null, $date = null, $isadmin = 0, $id_message = null, $media = null) {
        $this->id_reclamation = $id_reclamation;
        $this->message = $message;
        $this->date = $date;
        $this->isadmin = $isadmin;
        $this->id_message = $id_message;
        $this->media = $media;
    }

    // Getters
    public function getIdMessage() { return $this->id_message; }
    public function getIdReclamation() { return $this->id_reclamation; }
    public function getMessage() { return $this->message; }
    public function getDate() { return $this->date; }
    public function getIsadmin() { return $this->isadmin; }
    public function getMedia() { return $this->media; }

    // Setters
    public function setIdMessage($id_message) { $this->id_message = $id_message; }
    public function setIdReclamation($id_reclamation) { $this->id_reclamation = $id_reclamation; }
    public function setMessage($message) { $this->message = $message; }
    public function setDate($date) { $this->date = $date; }
    public function setIsadmin($isadmin) { $this->isadmin = $isadmin; }
    public function setMedia($media) { $this->media = $media; }
}
?>
