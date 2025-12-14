<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Model/alert.php';

class AlertController {

    // Ajouter un alert
    public function addAlert(Post $alert, $id_user = null) {
        $sql = "INSERT INTO reclamation
            (id_user, titre, description, media, date)
            VALUES (:id_user, :titre, :description, :media, :date)";

        $bd = Config::getConnexion();

        try {
            $query = $bd->prepare($sql);

            // Gestion de la date
            $dateCreation = $alert->getDateCreation();
            if ($dateCreation instanceof DateTime) {
                $dateCreation = $dateCreation->format('Y-m-d H:i:s');
            } elseif ($dateCreation === null) {
                $dateCreation = date('Y-m-d H:i:s');
            }

            // Gérer media : si null, utiliser chaîne vide
            $media = $alert->getMedia();
            if ($media === null) {
                $media = '';
            }

            $id_user_param = $id_user;
            $titre_param = $alert->getTitre();
            $description_param = $alert->getDescription();
            $media_param = $media;
            $date_creation_param = $dateCreation;
            $query->bindParam(':id_user', $id_user_param);
            $query->bindParam(':titre', $titre_param);
            $query->bindParam(':description', $description_param);
            $query->bindParam(':media', $media_param);
            $query->bindParam(':date', $date_creation_param);
            $result = $query->execute();

            return $result;
        } catch (PDOException $e) {
            error_log("Erreur PDO lors de l'ajout de l'alert : " . $e->getMessage());
            if (ini_get('display_errors')) {
                echo "Erreur SQL : " . $e->getMessage() . "<br>";
            }
            return false;
        } catch (Exception $e) {
            error_log("Erreur lors de l'ajout de l'alert : " . $e->getMessage());
            if (ini_get('display_errors')) {
                echo "Erreur : " . $e->getMessage() . "<br>";
            }
            return false;
        }
    }

    // Récupérer tous les alerts
    public function getAllAlerts() {
        $sql = "SELECT * FROM reclamation ORDER BY id_reclamation DESC";
        $bd = Config::getConnexion();
        try {
            $list = $bd->query($sql);
            return $list;
        } catch (Exception $e) {
            error_log('Erreur getAllAlerts : ' . $e->getMessage());
            return $bd->query("SELECT * FROM reclamation WHERE 1=0");
        }
    }

    // Supprimer un alert par ID
    public function deleteAlert($id) {
        $id = (int) $id; // Ensure id is integer
        error_log("Attempting to delete alert with id_reclamation: " . $id);
        $sql = "DELETE FROM reclamation WHERE id_reclamation = :id";
        $bd = Config::getConnexion();
        try {
            $rep = $bd->prepare($sql);
            $rep->bindParam(':id', $id);
            $rep->execute();
        } catch (Exception $e) {
            error_log('Erreur deleteAlert : ' . $e->getMessage());
            return false;
        }
    }

    // Récupérer un alert par ID
    public function getAlertById($id) {
        $sql = "SELECT * FROM reclamation WHERE id_reclamation = :id";
        $bd = Config::getConnexion();
        try {
            $query = $bd->prepare($sql);
            $query->bindValue(':id', $id);
            $query->execute();
            return $query->fetch(); // retourne un seul alert
        } catch (Exception $e) {
            echo 'Erreur : ' . $e->getMessage();
            return false;
        }
    }

    // Mettre à jour un alert
    public function updateAlert(Post $alert, $id) {
        $sql = "UPDATE reclamation
                SET titre = :titre,
                    description = :description,
                    media = :media,
                    date = :date
                WHERE id_reclamation = :id";

        $bd = Config::getConnexion();

        try {
            // Gestion de la date
            $dateCreation = $alert->getDateCreation();
            if ($dateCreation instanceof DateTime) {
                $dateCreation = $dateCreation->format('Y-m-d H:i:s');
            } elseif ($dateCreation === null) {
                $dateCreation = date('Y-m-d H:i:s');
            }

            // Gérer media : si null, utiliser chaîne vide
            $media = $alert->getMedia();
            if ($media === null) {
                $media = '';
            }

            $titre_value = $alert->getTitre();
            $desc_value = $alert->getDescription();
            $query = $bd->prepare($sql);
            $query->bindParam(':titre', $titre_value);
            $query->bindParam(':description', $desc_value);
            $query->bindParam(':media', $media);
            $query->bindParam(':date', $dateCreation);
            $query->bindParam(':id', $id);
            $result = $query->execute();

            return $result;
        } catch (PDOException $e) {
            error_log("Erreur PDO lors de la mise à jour de l'alert : " . $e->getMessage());
            if (ini_get('display_errors')) {
                echo "Erreur SQL : " . $e->getMessage() . "<br>";
            }
            return false;
        } catch (Exception $e) {
            error_log("Erreur lors de la mise à jour de l'alert : " . $e->getMessage());
            if (ini_get('display_errors')) {
                echo "Erreur : " . $e->getMessage() . "<br>";
            }
            return false;
        }
    }

    // Récupérer les alerts par id_user
    public function getAlertsByUser($id_user) {
        $sql = "SELECT * FROM reclamation WHERE id_user = :id_user ORDER BY date DESC";
        $bd = Config::getConnexion();
        try {
            $query = $bd->prepare($sql);
            $query->bindValue(':id_user', $id_user);
            $query->execute();
            return $query->fetchAll();
        } catch (Exception $e) {
            error_log('Erreur getAlertsByUser : ' . $e->getMessage());
            return array();
        }
    }
}
?>
