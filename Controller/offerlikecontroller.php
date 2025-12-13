<?php
require_once __DIR__ . '/../config.php';

class OfferLikeController {
    
    // Créer la table si elle n'existe pas
    public function createTableIfNotExists() {
        $bd = Config::getConnexion();
        try {
            // Vérifier si la table existe
            $checkTable = $bd->query("SHOW TABLES LIKE 'offer_like'");
            if ($checkTable->rowCount() > 0) {
                return true; // Table existe déjà
            }
            
            $sql = "CREATE TABLE offer_like (
                id_like INT AUTO_INCREMENT PRIMARY KEY,
                id_offer INT NOT NULL,
                id_user INT NOT NULL,
                date_like TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY unique_like (id_offer, id_user),
                INDEX idx_offer (id_offer),
                INDEX idx_user (id_user)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
            $bd->exec($sql);
            return true;
        } catch (Exception $e) {
            error_log("Erreur création table offer_like : " . $e->getMessage());
            // Essayer de créer sans contraintes si erreur
            try {
                $sql = "CREATE TABLE IF NOT EXISTS offer_like (
                    id_like INT AUTO_INCREMENT PRIMARY KEY,
                    id_offer INT NOT NULL,
                    id_user INT NOT NULL,
                    date_like TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    UNIQUE KEY unique_like (id_offer, id_user)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
                $bd->exec($sql);
                return true;
            } catch (Exception $e2) {
                error_log("Erreur création table offer_like (sans FK) : " . $e2->getMessage());
                return false;
            }
        }
    }
    
    // Ajouter un like
    public function addLike($id_offer, $id_user) {
        $this->createTableIfNotExists();
        $bd = Config::getConnexion();
        try {
            $sql = "INSERT INTO offer_like (id_offer, id_user) VALUES (:id_offer, :id_user)
                    ON DUPLICATE KEY UPDATE date_like = CURRENT_TIMESTAMP";
            $query = $bd->prepare($sql);
            $result = $query->execute([
                ':id_offer' => (int)$id_offer,
                ':id_user' => (int)$id_user
            ]);
            return $result;
        } catch (Exception $e) {
            error_log("Erreur addLike : " . $e->getMessage());
            return false;
        }
    }
    
    // Supprimer un like
    public function removeLike($id_offer, $id_user) {
        $bd = Config::getConnexion();
        try {
            $sql = "DELETE FROM offer_like WHERE id_offer = :id_offer AND id_user = :id_user";
            $query = $bd->prepare($sql);
            $result = $query->execute([
                ':id_offer' => (int)$id_offer,
                ':id_user' => (int)$id_user
            ]);
            return $result;
        } catch (Exception $e) {
            error_log("Erreur removeLike : " . $e->getMessage());
            return false;
        }
    }
    
    // Vérifier si un utilisateur a liké une offre
    public function isLiked($id_offer, $id_user) {
        $bd = Config::getConnexion();
        try {
            $sql = "SELECT COUNT(*) as count FROM offer_like WHERE id_offer = :id_offer AND id_user = :id_user";
            $query = $bd->prepare($sql);
            $query->execute([
                ':id_offer' => (int)$id_offer,
                ':id_user' => (int)$id_user
            ]);
            $result = $query->fetch(PDO::FETCH_ASSOC);
            return $result['count'] > 0;
        } catch (Exception $e) {
            error_log("Erreur isLiked : " . $e->getMessage());
            return false;
        }
    }
    
    // Obtenir le nombre de likes d'une offre
    public function getLikeCount($id_offer) {
        $bd = Config::getConnexion();
        try {
            $sql = "SELECT COUNT(*) as count FROM offer_like WHERE id_offer = :id_offer";
            $query = $bd->prepare($sql);
            $query->execute([':id_offer' => (int)$id_offer]);
            $result = $query->fetch(PDO::FETCH_ASSOC);
            return (int)$result['count'];
        } catch (Exception $e) {
            error_log("Erreur getLikeCount : " . $e->getMessage());
            return 0;
        }
    }
    
    // Toggle like (ajouter ou supprimer)
    public function toggleLike($id_offer, $id_user) {
        if ($this->isLiked($id_offer, $id_user)) {
            return $this->removeLike($id_offer, $id_user) ? 'removed' : false;
        } else {
            return $this->addLike($id_offer, $id_user) ? 'added' : false;
        }
    }
    
    // Obtenir la liste des utilisateurs qui ont liké une offre
    public function getUsersWhoLiked($id_offer) {
        $bd = Config::getConnexion();
        try {
            $sql = "SELECT ol.id_user, ol.date_like, u.name, u.email, u.id_user
                    FROM offer_like ol
                    LEFT JOIN user u ON ol.id_user = u.id_user
                    WHERE ol.id_offer = :id_offer
                    ORDER BY ol.date_like DESC";
            $query = $bd->prepare($sql);
            $query->execute([':id_offer' => (int)$id_offer]);
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erreur getUsersWhoLiked : " . $e->getMessage());
            return [];
        }
    }
}

