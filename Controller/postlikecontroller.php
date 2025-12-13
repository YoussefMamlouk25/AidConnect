<?php
require_once __DIR__ . '/../config.php';

class PostLikeController {
    
    // Créer la table si elle n'existe pas
    public function createTableIfNotExists() {
        $bd = Config::getConnexion();
        try {
            // Vérifier si la table existe
            $checkTable = $bd->query("SHOW TABLES LIKE 'post_like'");
            if ($checkTable->rowCount() > 0) {
                return true; // Table existe déjà
            }
            
            $sql = "CREATE TABLE post_like (
                id_like INT AUTO_INCREMENT PRIMARY KEY,
                id_post INT NOT NULL,
                id_user INT NOT NULL,
                date_like TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY unique_like (id_post, id_user),
                INDEX idx_post (id_post),
                INDEX idx_user (id_user)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
            $bd->exec($sql);
            return true;
        } catch (Exception $e) {
            error_log("Erreur création table post_like : " . $e->getMessage());
            // Essayer de créer sans contraintes si erreur
            try {
                $sql = "CREATE TABLE IF NOT EXISTS post_like (
                    id_like INT AUTO_INCREMENT PRIMARY KEY,
                    id_post INT NOT NULL,
                    id_user INT NOT NULL,
                    date_like TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    UNIQUE KEY unique_like (id_post, id_user)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
                $bd->exec($sql);
                return true;
            } catch (Exception $e2) {
                error_log("Erreur création table post_like (sans FK) : " . $e2->getMessage());
                return false;
            }
        }
    }
    
    // Ajouter un like
    public function addLike($id_post, $id_user) {
        $this->createTableIfNotExists();
        $bd = Config::getConnexion();
        try {
            $sql = "INSERT INTO post_like (id_post, id_user) VALUES (:id_post, :id_user)
                    ON DUPLICATE KEY UPDATE date_like = CURRENT_TIMESTAMP";
            $query = $bd->prepare($sql);
            $result = $query->execute([
                ':id_post' => (int)$id_post,
                ':id_user' => (int)$id_user
            ]);
            return $result;
        } catch (Exception $e) {
            error_log("Erreur addLike : " . $e->getMessage());
            return false;
        }
    }
    
    // Supprimer un like
    public function removeLike($id_post, $id_user) {
        $bd = Config::getConnexion();
        try {
            $sql = "DELETE FROM post_like WHERE id_post = :id_post AND id_user = :id_user";
            $query = $bd->prepare($sql);
            $result = $query->execute([
                ':id_post' => (int)$id_post,
                ':id_user' => (int)$id_user
            ]);
            return $result;
        } catch (Exception $e) {
            error_log("Erreur removeLike : " . $e->getMessage());
            return false;
        }
    }
    
    // Vérifier si un utilisateur a liké un post
    public function isLiked($id_post, $id_user) {
        $bd = Config::getConnexion();
        try {
            $sql = "SELECT COUNT(*) as count FROM post_like WHERE id_post = :id_post AND id_user = :id_user";
            $query = $bd->prepare($sql);
            $query->execute([
                ':id_post' => (int)$id_post,
                ':id_user' => (int)$id_user
            ]);
            $result = $query->fetch(PDO::FETCH_ASSOC);
            return $result['count'] > 0;
        } catch (Exception $e) {
            error_log("Erreur isLiked : " . $e->getMessage());
            return false;
        }
    }
    
    // Obtenir le nombre de likes d'un post
    public function getLikeCount($id_post) {
        $bd = Config::getConnexion();
        try {
            $sql = "SELECT COUNT(*) as count FROM post_like WHERE id_post = :id_post";
            $query = $bd->prepare($sql);
            $query->execute([':id_post' => (int)$id_post]);
            $result = $query->fetch(PDO::FETCH_ASSOC);
            return (int)$result['count'];
        } catch (Exception $e) {
            error_log("Erreur getLikeCount : " . $e->getMessage());
            return 0;
        }
    }
    
    // Toggle like (ajouter ou supprimer)
    public function toggleLike($id_post, $id_user) {
        if ($this->isLiked($id_post, $id_user)) {
            return $this->removeLike($id_post, $id_user) ? 'removed' : false;
        } else {
            return $this->addLike($id_post, $id_user) ? 'added' : false;
        }
    }
    
    // Obtenir la liste des utilisateurs qui ont liké un post
    public function getUsersWhoLiked($id_post) {
        $bd = Config::getConnexion();
        try {
            $sql = "SELECT pl.id_user, pl.date_like, u.name, u.email, u.id_user
                    FROM post_like pl
                    LEFT JOIN user u ON pl.id_user = u.id_user
                    WHERE pl.id_post = :id_post
                    ORDER BY pl.date_like DESC";
            $query = $bd->prepare($sql);
            $query->execute([':id_post' => (int)$id_post]);
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erreur getUsersWhoLiked : " . $e->getMessage());
            return [];
        }
    }
}

