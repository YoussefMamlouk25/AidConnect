<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Model/offer.php';

class OfferController
{
    public static function escape($value)
    {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }

    public function addOffer(Offer $offer)
    {
        $sql = "INSERT INTO offer
        (id_user, description, media, nom, num, email, type_don)
        VALUES
        (:id_user, :description, :media, :nom, :num, :email, :type_don)";
        $bd = Config::getConnexion();
        try {
            $q = $bd->prepare($sql);
            $q->bindValue(':id_user', $offer->getIdUser(), $offer->getIdUser() === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $q->bindValue(':description', $offer->getDescription(), PDO::PARAM_STR);
            $media = $offer->getMedia();
            if ($media === null) {
                $q->bindValue(':media', null, PDO::PARAM_NULL);
            } else {
                $q->bindValue(':media', $media, PDO::PARAM_STR);
            }
            $q->bindValue(':nom', $offer->getNom(), PDO::PARAM_STR);
            $q->bindValue(':num', $offer->getNum(), PDO::PARAM_STR);
            $q->bindValue(':email', $offer->getEmail(), PDO::PARAM_STR);
            $q->bindValue(':type_don', $offer->getTypeDon(), PDO::PARAM_STR);
            if ($q->execute()) { return $bd->lastInsertId(); }
            return false;
        } catch (Exception $e) {
            error_log("Erreur addOffer : " . $e->getMessage());
            return false;
        }
    }

    public function getAllOffers()
    {
        $bd = Config::getConnexion();
        try {
            $q = $bd->prepare("SELECT * FROM offer ORDER BY id_offer DESC");
            $q->execute();
            return $q->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erreur getAllOffers : " . $e->getMessage());
            return [];
        }
    }

    public function getOfferById($id)
    {
        $bd = Config::getConnexion();
        try {
            $q = $bd->prepare("SELECT * FROM offer WHERE id_offer = :id");
            $q->bindValue(':id', (int)$id, PDO::PARAM_INT);
            $q->execute();
            return $q->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erreur getOfferById : " . $e->getMessage());
            return false;
        }
    }

    public function deleteOffer($id)
    {
        $bd = Config::getConnexion();
        try {
            $q = $bd->prepare("DELETE FROM offer WHERE id_offer = :id");
            $q->bindValue(':id', (int)$id, PDO::PARAM_INT);
            return $q->execute();
        } catch (Exception $e) {
            error_log("Erreur deleteOffer : " . $e->getMessage());
            return false;
        }
    }

    public function updateOffer(Offer $offer, $id)
    {
        $bd = Config::getConnexion();
        try {
            $sql = "UPDATE offer SET
                id_user = :id_user,
                description = :description,
                media = :media,
                nom = :nom,
                num = :num,
                email = :email,
                type_don = :type_don
            WHERE id_offer = :id";
            $q = $bd->prepare($sql);
            $q->bindValue(':id_user', $offer->getIdUser(), $offer->getIdUser() === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
            $q->bindValue(':description', $offer->getDescription(), PDO::PARAM_STR);
            $media = $offer->getMedia();
            $q->bindValue(':media', $media !== null ? $media : null, $media !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $q->bindValue(':nom', $offer->getNom(), PDO::PARAM_STR);
            $q->bindValue(':num', $offer->getNum(), PDO::PARAM_STR);
            $q->bindValue(':email', $offer->getEmail(), PDO::PARAM_STR);
            $q->bindValue(':type_don', $offer->getTypeDon(), PDO::PARAM_STR);
            $q->bindValue(':id', (int)$id, PDO::PARAM_INT);
            return $q->execute();
        } catch (Exception $e) {
            error_log("Erreur updateOffer : " . $e->getMessage());
            return false;
        }
    }

    // Fonction pour obtenir les initiales d'un nom
    public function getInitials($name) {
        $words = explode(' ', trim($name));
        $initials = '';
        foreach ($words as $word) {
            if (!empty($word)) {
                $initials .= strtoupper(substr($word, 0, 1));
            }
        }
        return substr($initials, 0, 2);
    }

    // Fonction pour formater la date
    public function formatDate($date) {
        if (empty($date)) return '';
        $dateObj = new DateTime($date);
        $now = new DateTime();
        $diff = $now->diff($dateObj);

        if ($diff->days == 0) {
            return "Aujourd'hui";
        } elseif ($diff->days == 1) {
            return "Hier";
        } elseif ($diff->days < 7) {
            return "Il y a " . $diff->days . " jours";
        } else {
            return $dateObj->format('d/m/Y');
        }
    }

    // Fonction pour obtenir le libellé du type de don
    public function getTypeDonLabel($type) {
        $types = [
            'money' => 'Argent',
            'food' => 'Nourriture',
            'clothing' => 'Vêtements',
            'medicines' => 'Médicaments',
            'equipment' => 'Matériel',
            'services' => 'Services'
        ];
        return isset($types[$type]) ? $types[$type] : $type;
    }

}
