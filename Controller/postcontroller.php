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
    public function deletePost($id) {
        $sql = "DELETE FROM post WHERE id_post = :id";
        $bd = Config::getConnexion();
        try {
            $rep = $bd->prepare($sql);
            $rep->bindValue(':id', $id);
            $rep->execute();
        } catch (Exception $e) {
            echo 'Erreur : ' . $e->getMessage();
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

    // Mettre à jour un post
    public function updatePost(Post $post, $id) {
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
            $result = $query->execute([
                ':titre' => $post->getTitre(),
                ':nom' => $post->getNom(),
                ':numero' => $post->getNumero(),
                ':email' => $post->getEmail(),
                ':region' => $post->getRegion(),
                ':description' => $post->getDescription(),
                ':media' => $media,
                ':date_creation' => $dateCreation,
                ':id' => $id
            ]);
            
            return $result;
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
