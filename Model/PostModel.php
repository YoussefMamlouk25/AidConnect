<?php

class PostModel {

    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function addPost($id_user, $titre, $description, $region, $media) {
        $sql = "INSERT INTO posts (id_user, titre, description, region, media, date_creation)
                VALUES (:id_user, :titre, :description, :region, :media, NOW())";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(':id_user', $id_user);
        $stmt->bindValue(':titre', $titre);
        $stmt->bindValue(':description', $description);
        $stmt->bindValue(':region', $region);
        $stmt->bindValue(':media', $media);

        return $stmt->execute();
    }

    public function getAllPosts() {
        return $this->conn->query("SELECT * FROM posts ORDER BY date_creation DESC")->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
