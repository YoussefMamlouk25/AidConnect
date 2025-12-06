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
            if ($q->execute()) { 
                return $bd->lastInsertId(); 
            }
            return false;
        } catch (PDOException $e) {
            error_log("Erreur PDO addOffer : " . $e->getMessage());
            if (ini_get('display_errors')) {
                echo "Erreur SQL (addOffer) : " . htmlspecialchars($e->getMessage()) . "<br>";
            }
            return false;
        } catch (Exception $e) {
            error_log("Erreur addOffer : " . $e->getMessage());
            if (ini_get('display_errors')) {
                echo "Erreur (addOffer) : " . htmlspecialchars($e->getMessage()) . "<br>";
            }
            return false;
        }
    }

    public function getAllOffers()
    {
        $bd = Config::getConnexion();
        try {
            $q = $bd->prepare("SELECT o.*, 
                    (SELECT COUNT(*) FROM offer_like WHERE id_offer = o.id_offer) as likes_count
                    FROM offer o ORDER BY o.id_offer DESC");
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

    public function deleteOffer($id, $ownerId = null)
    {
        $bd = Config::getConnexion();
        try {
            $sql = "DELETE FROM offer WHERE id_offer = :id";
            if ($ownerId !== null) {
                $sql .= " AND id_user = :owner_id";
            }
            $q = $bd->prepare($sql);
            $q->bindValue(':id', (int)$id, PDO::PARAM_INT);
            if ($ownerId !== null) {
                $q->bindValue(':owner_id', (int)$ownerId, PDO::PARAM_INT);
            }
            if (!$q->execute()) {
                return false;
            }
            return $q->rowCount() > 0;
        } catch (Exception $e) {
            error_log("Erreur deleteOffer : " . $e->getMessage());
            return false;
        }
    }

    public function updateOffer(Offer $offer, $id, $ownerId = null)
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
            if ($ownerId !== null) {
                $sql .= " AND id_user = :owner_id";
            }
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
            if ($ownerId !== null) {
                $q->bindValue(':owner_id', (int)$ownerId, PDO::PARAM_INT);
            }
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

    // Rechercher des offres avec filtres avancés
    public function searchOffers($query = '', $filters = []) {
        $bd = Config::getConnexion();
        try {
            $sql = "SELECT o.*, 
                    (SELECT COUNT(*) FROM update_offer WHERE id_offer = o.id_offer) as updates_count,
                    (SELECT COUNT(*) FROM offer_like WHERE id_offer = o.id_offer) as likes_count
                    FROM offer o WHERE 1=1";
            $params = [];
            
            // Search query (description only, nom is handled separately)
            if (!empty($query)) {
                $sql .= " AND o.description LIKE :query";
                $params[':query'] = '%' . $query . '%';
            }
            
            // Advanced: Filter by name (nom)
            if (!empty($filters['nom'])) {
                $sql .= " AND o.nom LIKE :nom";
                $params[':nom'] = '%' . $filters['nom'] . '%';
            }
            
            // Advanced: Filter by email
            if (!empty($filters['email'])) {
                $sql .= " AND o.email LIKE :email";
                $params[':email'] = '%' . $filters['email'] . '%';
            }
            
            // Advanced: Filter by phone number
            if (!empty($filters['num'])) {
                $sql .= " AND o.num LIKE :num";
                $params[':num'] = '%' . $filters['num'] . '%';
            }
            
            // Category filter
            if (!empty($filters['category'])) {
                $sql .= " AND o.type_don = :category";
                $params[':category'] = $filters['category'];
            }
            
            // Advanced: Filter by media presence
            if (isset($filters['has_media'])) {
                if ($filters['has_media'] == 'yes') {
                    $sql .= " AND (o.media IS NOT NULL AND o.media != '' AND o.media != 'null')";
                } elseif ($filters['has_media'] == 'no') {
                    $sql .= " AND (o.media IS NULL OR o.media = '' OR o.media = 'null')";
                }
            }
            
            // Advanced: Filter by minimum number of updates
            if (isset($filters['min_updates']) && $filters['min_updates'] !== '' && is_numeric($filters['min_updates'])) {
                $minUpdates = (int)$filters['min_updates'];
                $sql .= " AND (SELECT COUNT(*) FROM update_offer WHERE id_offer = o.id_offer) >= :min_updates";
                $params[':min_updates'] = $minUpdates;
            }
            
            // Advanced: Filter by maximum number of updates
            if (isset($filters['max_updates']) && $filters['max_updates'] !== '' && is_numeric($filters['max_updates'])) {
                $maxUpdates = (int)$filters['max_updates'];
                $sql .= " AND (SELECT COUNT(*) FROM update_offer WHERE id_offer = o.id_offer) <= :max_updates";
                $params[':max_updates'] = $maxUpdates;
            }
            
            // Advanced: Date range filter (using id_offer as proxy for date, or if date column exists)
            // Check if date_creation column exists by trying to use it
            if (!empty($filters['date_from']) || !empty($filters['date_to'])) {
                // Try to use date_creation if it exists, otherwise use id_offer as proxy
                // For now, we'll use id_offer DESC as proxy (higher id = newer)
                // In a real scenario, you'd want to add a date_creation column
                if (!empty($filters['date_from'])) {
                    // This is a simplified approach - ideally you'd have a date column
                    // For now, we'll skip date filtering or use id_offer range
                }
                if (!empty($filters['date_to'])) {
                    // Same as above
                }
            }
            
            // Status filter (offers with updates are considered "active" or "resolved")
            if (isset($filters['status'])) {
                if ($filters['status'] == 'resolved') {
                    $sql .= " AND EXISTS (SELECT 1 FROM update_offer WHERE id_offer = o.id_offer)";
                } elseif ($filters['status'] == 'active') {
                    $sql .= " AND NOT EXISTS (SELECT 1 FROM update_offer WHERE id_offer = o.id_offer)";
                }
            }
            
            // Sorting
            $sort = $filters['sort'] ?? 'date_desc';
            switch ($sort) {
                case 'date_asc':
                    $sql .= " ORDER BY o.id_offer ASC";
                    break;
                case 'name_asc':
                    $sql .= " ORDER BY o.nom ASC";
                    break;
                case 'name_desc':
                    $sql .= " ORDER BY o.nom DESC";
                    break;
                case 'updates_asc':
                    $sql .= " ORDER BY updates_count ASC, o.id_offer DESC";
                    break;
                case 'updates_desc':
                    $sql .= " ORDER BY updates_count DESC, o.id_offer DESC";
                    break;
                case 'popularity':
                    $sql .= " ORDER BY updates_count DESC, o.id_offer DESC";
                    break;
                case 'date_desc':
                default:
                    $sql .= " ORDER BY o.id_offer DESC";
                    break;
            }
            
            $q = $bd->prepare($sql);
            $q->execute($params);
            return $q->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erreur searchOffers : " . $e->getMessage());
            return [];
        }
    }

}
