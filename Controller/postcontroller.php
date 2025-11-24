<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Model/post.php';

class PostController {

    // Ajouter un post
   public function addPost(Post $post, $id_user = null) {
    $sql = "INSERT INTO post 
        (id_user, nom, `Numéro`, email, titre, region, description, date_creation, media)
        VALUES (:id_user, :nom, :numero, :email, :titre, :region, :description, :date_creation, :media)";

    $bd = Config::getConnexion();

    try {
        $query = $bd->prepare($sql);
        
        // Gestion de la date
        $dateCreation = $post->getDateCreation();
        if ($dateCreation instanceof DateTime) {
            $dateCreation = $dateCreation->format('Y-m-d');
        } elseif ($dateCreation === null) {
            $dateCreation = date('Y-m-d');
        }
        
        // Gérer media : si null, utiliser chaîne vide
        $media = $post->getMedia();
        if ($media === null) {
            $media = '';
        }
        
        $result = $query->execute([
            ':id_user' => $id_user, // peut être null
            ':nom' => $post->getNom(),
            ':numero' => $post->getNumero(),
            ':email' => $post->getEmail(),
            ':titre' => $post->getTitre(),
            ':region' => $post->getRegion(),
            ':description' => $post->getDescription(),
            ':date_creation' => $dateCreation,
            ':media' => $media
        ]);
        
        return $result;
    } catch (PDOException $e) {
        error_log("Erreur PDO lors de l'ajout du post : " . $e->getMessage());
        // Pour le débogage, on peut aussi afficher l'erreur si display_errors est activé
        if (ini_get('display_errors')) {
            echo "Erreur SQL : " . $e->getMessage() . "<br>";
        }
        return false;
    } catch (Exception $e) {
        error_log("Erreur lors de l'ajout du post : " . $e->getMessage());
        if (ini_get('display_errors')) {
            echo "Erreur : " . $e->getMessage() . "<br>";
        }
        return false;
    }
}

    // Récupérer tous les posts
    public function getAllPosts() {
        $sql = "SELECT * FROM post ORDER BY id_post DESC";
        $bd = Config::getConnexion();
        try {
            $list = $bd->query($sql);
            return $list;
        } catch (Exception $e) {
            error_log('Erreur getAllPosts : ' . $e->getMessage());
            // Retourner un PDOStatement vide plutôt qu'un tableau
            return $bd->query("SELECT * FROM post WHERE 1=0");
        }
    }

    // Supprimer un post par ID
    public function deletePost($id, $ownerId = null) {
        $sql = "DELETE FROM post WHERE id_post = :id";
        if ($ownerId !== null) {
            $sql .= " AND id_user = :owner_id";
        }
        $bd = Config::getConnexion();
        try {
            $rep = $bd->prepare($sql);
            $rep->bindValue(':id', $id);
            if ($ownerId !== null) {
                $rep->bindValue(':owner_id', (int) $ownerId, PDO::PARAM_INT);
            }
            $rep->execute();
            return $rep->rowCount() > 0;
        } catch (Exception $e) {
            echo 'Erreur : ' . $e->getMessage();
            return false;
        }
    }

    // Récupérer un post par ID
    public function getPostById($id) {
        $sql = "SELECT * FROM post WHERE id_post = :id";
        $bd = Config::getConnexion();
        try {
            $query = $bd->prepare($sql);
            $query->bindValue(':id', $id);
            $query->execute();
            return $query->fetch(); // retourne un seul post
        } catch (Exception $e) {
            echo 'Erreur : ' . $e->getMessage();
            return false;
        }
    }

    // Rechercher des posts avec filtres avancés
    public function searchPosts($query = '', $filters = []) {
        $bd = Config::getConnexion();
        try {
            $sql = "SELECT p.*, 
                    (SELECT COUNT(*) FROM postupdate WHERE id_post = p.id_post) as updates_count
                    FROM post p WHERE 1=1";
            $params = [];
            
            // Search query
            if (!empty($query)) {
                $sql .= " AND (p.titre LIKE :query OR p.description LIKE :query OR p.nom LIKE :query OR p.region LIKE :query)";
                $params[':query'] = '%' . $query . '%';
            }
            
            // Region filter
            if (!empty($filters['region'])) {
                $sql .= " AND p.region = :region";
                $params[':region'] = $filters['region'];
            }
            
            // Date range filter
            if (!empty($filters['date_from'])) {
                $sql .= " AND p.date_creation >= :date_from";
                $params[':date_from'] = $filters['date_from'];
            }
            if (!empty($filters['date_to'])) {
                $sql .= " AND p.date_creation <= :date_to";
                $params[':date_to'] = $filters['date_to'];
            }
            
            // Status filter (resolved posts have updates)
            if (isset($filters['status'])) {
                if ($filters['status'] == 'resolved') {
                    $sql .= " AND EXISTS (SELECT 1 FROM postupdate WHERE id_post = p.id_post)";
                } elseif ($filters['status'] == 'active') {
                    $sql .= " AND NOT EXISTS (SELECT 1 FROM postupdate WHERE id_post = p.id_post)";
                }
            }
            
            // Sorting
            $sort = $filters['sort'] ?? 'date_desc';
            switch ($sort) {
                case 'date_asc':
                    $sql .= " ORDER BY p.date_creation ASC";
                    break;
                case 'region':
                    $sql .= " ORDER BY p.region ASC, p.date_creation DESC";
                    break;
                case 'popularity':
                    $sql .= " ORDER BY updates_count DESC, p.date_creation DESC";
                    break;
                case 'date_desc':
                default:
                    $sql .= " ORDER BY p.date_creation DESC";
                    break;
            }
            
            $q = $bd->prepare($sql);
            $q->execute($params);
            return $q->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('Erreur searchPosts : ' . $e->getMessage());
            return [];
        }
    }

    // Mettre à jour un post
    public function updatePost(Post $post, $id = null, $ownerId = null) {
        $sql = "UPDATE post 
                SET titre = :titre, 
                    nom = :nom, 
                    `Numéro` = :numero, 
                    email = :email, 
                    region = :region, 
                    description = :description, 
                    media = :media, 
                    date_creation = :date_creation
                WHERE id_post = :id";
        if ($ownerId !== null) {
            $sql .= " AND id_user = :owner_id";
        }

        $bd = Config::getConnexion();

        try {
            // Gestion de la date
            $dateCreation = $post->getDateCreation();
            if ($dateCreation instanceof DateTime) {
                $dateCreation = $dateCreation->format('Y-m-d');
            } elseif ($dateCreation === null) {
                $dateCreation = date('Y-m-d');
            }
            
            // Gérer media : si null, utiliser chaîne vide
            $media = $post->getMedia();
            if ($media === null) {
                $media = '';
            }
            
            $query = $bd->prepare($sql);
            if ($id === null) {
                $id = $post->getId();
            }
            $params = [
                ':titre' => $post->getTitre(),
                ':nom' => $post->getNom(),
                ':numero' => $post->getNumero(),
                ':email' => $post->getEmail(),
                ':region' => $post->getRegion(),
                ':description' => $post->getDescription(),
                ':media' => $media,
                ':date_creation' => $dateCreation,
                ':id' => $id
            ];

            if ($ownerId !== null) {
                $params[':owner_id'] = (int) $ownerId;
            }
            
            $result = $query->execute($params);
            
            return $result && $query->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Erreur PDO lors de la mise à jour du post : " . $e->getMessage());
            if (ini_get('display_errors')) {
                echo "Erreur SQL : " . $e->getMessage() . "<br>";
            }
            return false;
        } catch (Exception $e) {
            error_log("Erreur lors de la mise à jour du post : " . $e->getMessage());
            if (ini_get('display_errors')) {
                echo "Erreur : " . $e->getMessage() . "<br>";
            }
            return false;
        }
    }
}
