<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Model/updateoffer.php';

class UpdateOfferController
{
    public function addUpdateOffer(UpdateOffer $updateOffer)
    {
        $sql = "INSERT INTO update_offer
        (id_offer, id_user, texte, media)
        VALUES
        (:id_offer, :id_user, :texte, :media)";
        $bd = Config::getConnexion();
        $q = null;
        try {
            $q = $bd->prepare($sql);
            $q->bindValue(':id_offer', $updateOffer->getIdOffer(), $updateOffer->getIdOffer() === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            // Use NULL for anonymous users (foreign key constraint requires valid user ID or NULL)
            $id_user = $updateOffer->getIdUser();
            if ($id_user !== null && $id_user !== '' && $id_user > 0) {
                $q->bindValue(':id_user', (int)$id_user, PDO::PARAM_INT);
            } else {
                $q->bindValue(':id_user', null, PDO::PARAM_NULL);
            }
            $q->bindValue(':texte', $updateOffer->getTexte(), PDO::PARAM_STR);
            $media = $updateOffer->getMedia();
            if ($media === null || $media === '') {
                $q->bindValue(':media', null, PDO::PARAM_NULL);
            } else {
                $q->bindValue(':media', $media, PDO::PARAM_STR);
            }
            if ($q->execute()) { 
                return $bd->lastInsertId(); 
            }
            // If execute returns false, check for errors
            if ($q) {
                $errorInfo = $q->errorInfo();
                error_log("SQL Execute failed: " . print_r($errorInfo, true));
            }
            return false;
        } catch (PDOException $e) {
            error_log("Erreur addUpdateOffer PDO: " . $e->getMessage());
            if ($q) {
                error_log("SQL Error Info: " . print_r($q->errorInfo(), true));
            }
            throw $e; // Re-throw to get more details
        } catch (Exception $e) {
            error_log("Erreur addUpdateOffer: " . $e->getMessage());
            throw $e;
        }
    }

    public function getAllUpdateOffers()
    {
        $bd = Config::getConnexion();
        try {
            $q = $bd->prepare("SELECT * FROM update_offer ORDER BY id_update_offer DESC");
            $q->execute();
            return $q->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erreur getAllUpdateOffers : " . $e->getMessage());
            return [];
        }
    }

    public function getUpdateOfferById($id)
    {
        $bd = Config::getConnexion();
        try {
            $q = $bd->prepare("SELECT * FROM update_offer WHERE id_update_offer = :id");
            $q->bindValue(':id', (int)$id, PDO::PARAM_INT);
            $q->execute();
            return $q->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erreur getUpdateOfferById : " . $e->getMessage());
            return false;
        }
    }

    public function getUpdateOffersByOfferId($id_offer)
    {
        $bd = Config::getConnexion();
        try {
            $q = $bd->prepare("SELECT * FROM update_offer WHERE id_offer = :id_offer ORDER BY id_update_offer DESC");
            $q->bindValue(':id_offer', (int)$id_offer, PDO::PARAM_INT);
            $q->execute();
            return $q->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erreur getUpdateOffersByOfferId : " . $e->getMessage());
            return [];
        }
    }

    public function checkUserHasReplied($id_offer, $id_user)
    {
        // Only check if user is logged in (id_user > 0)
        if ($id_user <= 0 || $id_user === null) {
            return false;
        }
        
        $bd = Config::getConnexion();
        try {
            $q = $bd->prepare("SELECT COUNT(*) as count FROM update_offer WHERE id_offer = :id_offer AND id_user = :id_user");
            $q->bindValue(':id_offer', (int)$id_offer, PDO::PARAM_INT);
            $q->bindValue(':id_user', (int)$id_user, PDO::PARAM_INT);
            $q->execute();
            $result = $q->fetch(PDO::FETCH_ASSOC);
            return $result['count'] > 0;
        } catch (Exception $e) {
            error_log("Erreur checkUserHasReplied : " . $e->getMessage());
            return false;
        }
    }

    public function deleteUpdateOffer($id)
    {
        $bd = Config::getConnexion();
        try {
            $q = $bd->prepare("DELETE FROM update_offer WHERE id_update_offer = :id");
            $q->bindValue(':id', (int)$id, PDO::PARAM_INT);
            return $q->execute();
        } catch (Exception $e) {
            error_log("Erreur deleteUpdateOffer : " . $e->getMessage());
            return false;
        }
    }

    public function updateUpdateOffer(UpdateOffer $updateOffer, $id)
    {
        $bd = Config::getConnexion();
        try {
            $sql = "UPDATE update_offer SET
                id_offer = :id_offer,
                id_user = :id_user,
                texte = :texte,
                media = :media
            WHERE id_update_offer = :id";
            $q = $bd->prepare($sql);
            $q->bindValue(':id_offer', $updateOffer->getIdOffer(), $updateOffer->getIdOffer() === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $q->bindValue(':id_user', $updateOffer->getIdUser(), PDO::PARAM_INT);
            $q->bindValue(':texte', $updateOffer->getTexte(), PDO::PARAM_STR);
            $media = $updateOffer->getMedia();
            $q->bindValue(':media', $media !== null ? $media : null, $media !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $q->bindValue(':id', (int)$id, PDO::PARAM_INT);
            return $q->execute();
        } catch (Exception $e) {
            error_log("Erreur updateUpdateOffer : " . $e->getMessage());
            return false;
        }
    }
}

