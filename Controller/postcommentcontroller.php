<?php
require_once __DIR__ . '/../config.php';

class PostCommentController {
    
    // Créer la table si elle n'existe pas
    public function createTableIfNotExists() {
        $bd = Config::getConnexion();
        try {
            // Vérifier si la table existe
            $checkTable = $bd->query("SHOW TABLES LIKE 'post_comment'");
            if ($checkTable->rowCount() > 0) {
                return true; // Table existe déjà
            }
            
            $sql = "CREATE TABLE post_comment (
                id_comment INT AUTO_INCREMENT PRIMARY KEY,
                id_post INT NOT NULL,
                id_user INT NOT NULL,
                comment_text TEXT NOT NULL,
                date_comment TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_post (id_post),
                INDEX idx_user (id_user)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
            $bd->exec($sql);
            return true;
        } catch (Exception $e) {
            error_log("Erreur création table post_comment : " . $e->getMessage());
            // Essayer de créer sans contraintes si erreur
            try {
                $sql = "CREATE TABLE IF NOT EXISTS post_comment (
                    id_comment INT AUTO_INCREMENT PRIMARY KEY,
                    id_post INT NOT NULL,
                    id_user INT NOT NULL,
                    comment_text TEXT NOT NULL,
                    date_comment TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
                $bd->exec($sql);
                return true;
            } catch (Exception $e2) {
                error_log("Erreur création table post_comment (sans FK) : " . $e2->getMessage());
                return false;
            }
        }
    }
    
    // Ajouter un commentaire
    public function addComment($id_post, $id_user, $comment_text) {
        $this->createTableIfNotExists();
        $bd = Config::getConnexion();
        try {
            $sql = "INSERT INTO post_comment (id_post, id_user, comment_text) VALUES (:id_post, :id_user, :comment_text)";
            $query = $bd->prepare($sql);
            $result = $query->execute([
                ':id_post' => (int)$id_post,
                ':id_user' => (int)$id_user,
                ':comment_text' => trim($comment_text)
            ]);
            if ($result) {
                return $bd->lastInsertId();
            }
            return false;
        } catch (Exception $e) {
            error_log("Erreur addComment : " . $e->getMessage());
            return false;
        }
    }
    
    // Récupérer tous les commentaires d'un post
    public function getCommentsByPost($id_post) {
        $bd = Config::getConnexion();
        try {
            $sql = "SELECT c.*, u.name, u.email, u.id_user
                    FROM post_comment c
                    LEFT JOIN user u ON c.id_user = u.id_user
                    WHERE c.id_post = :id_post
                    ORDER BY c.date_comment ASC";
            $query = $bd->prepare($sql);
            $query->execute([':id_post' => (int)$id_post]);
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erreur getCommentsByPost : " . $e->getMessage());
            return [];
        }
    }
    
    // Obtenir le nombre de commentaires d'un post
    public function getCommentCount($id_post) {
        $bd = Config::getConnexion();
        try {
            $sql = "SELECT COUNT(*) as count FROM post_comment WHERE id_post = :id_post";
            $query = $bd->prepare($sql);
            $query->execute([':id_post' => (int)$id_post]);
            $result = $query->fetch(PDO::FETCH_ASSOC);
            return (int)$result['count'];
        } catch (Exception $e) {
            error_log("Erreur getCommentCount : " . $e->getMessage());
            return 0;
        }
    }
    
    // Supprimer un commentaire
    public function deleteComment($id_comment, $id_user) {
        $bd = Config::getConnexion();
        try {
            $sql = "DELETE FROM post_comment WHERE id_comment = :id_comment AND id_user = :id_user";
            $query = $bd->prepare($sql);
            return $query->execute([
                ':id_comment' => (int)$id_comment,
                ':id_user' => (int)$id_user
            ]);
        } catch (Exception $e) {
            error_log("Erreur deleteComment : " . $e->getMessage());
            return false;
        }
    }
}

