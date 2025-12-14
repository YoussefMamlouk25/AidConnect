<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Model/postupdate.php';

class PostUpdateController {
    public function addPostUpdate(PostUpdate $postUpdate) {
        $sql = "INSERT INTO update_post 
                (id_post, id_user, texte, media, realiser, temp_reponse, temp_resolue)
                VALUES (:id_post, :id_user, :texte, :media, :realiser, :temp_reponse, :temp_resolue)";
        $bd = Config::getConnexion();

        try {
            $query = $bd->prepare($sql);
            
            // Gérer media : si null, utiliser chaîne vide
            $media = $postUpdate->getMedia();
            if ($media === null) {
                $media = '';
            }

            // Gérer realiser : garder comme chaîne vide pour éviter l'insertion de NULL qui utilise la valeur par défaut 0
            $realiser = $postUpdate->getRealiser();

            $result = $query->execute([
                ':id_post' => $postUpdate->getIdPost(),
                ':id_user' => $postUpdate->getIdUser(),
                ':texte' => $postUpdate->getTexte(),
                ':media' => $media,
                ':realiser' => $realiser,
                ':temp_reponse' => $postUpdate->getTempReponse(),
                ':temp_resolue' => $postUpdate->getTempResolue()
            ]);
            
            return $result;
        } catch (PDOException $e) {
            error_log("Erreur PDO lors de l'ajout du post update : " . $e->getMessage());
            if (ini_get('display_errors')) {
                echo "Erreur SQL : " . $e->getMessage() . "<br>";
            }
            return false;
        } catch (Exception $e) {
            error_log("Erreur lors de l'ajout du post update : " . $e->getMessage());
            if (ini_get('display_errors')) {
                echo "Erreur : " . $e->getMessage() . "<br>";
            }
            return false;
        }
    }

    public function getAllPostUpdates() {
        $sql = "SELECT up.*, p.titre, p.nom, p.region, u.profile_pic 
                FROM update_post up
                INNER JOIN post p ON up.id_post = p.id_post
                LEFT JOIN user u ON up.id_user = u.id_user
                ORDER BY up.id_update_post DESC";
        $bd = Config::getConnexion();
        try {
            $list = $bd->query($sql);
            return $list;
        } catch (Exception $e) {
            error_log("Erreur lors de la récupération des posts update : " . $e->getMessage());
            if (ini_get('display_errors')) {
                echo 'Erreur : ' . $e->getMessage();
            }
            return [];
        }
    }
    
    public function getPostUpdatesByPostId($id_post) {
        $sql = "SELECT up.*, u.profile_pic 
                FROM update_post up
                LEFT JOIN user u ON up.id_user = u.id_user
                WHERE up.id_post = :id_post 
                ORDER BY up.id_update_post DESC";
        $bd = Config::getConnexion();
        try {
            $stmt = $bd->prepare($sql);
            $stmt->execute([':id_post' => $id_post]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erreur lors de la récupération des updates du post : " . $e->getMessage());
            if (ini_get('display_errors')) {
                echo 'Erreur : ' . $e->getMessage();
            }
            return [];
        }
    }
    
    public function deletePostUpdate($id) {
        $sql = "DELETE FROM update_post WHERE id_update_post = :id";
        $bd = Config::getConnexion();
        try {
            $rep = $bd->prepare($sql);
            $rep->bindValue(':id', $id);
            $rep->execute();
            return true;
        } catch (Exception $e) {
            error_log("Erreur lors de la suppression du post update : " . $e->getMessage());
            if (ini_get('display_errors')) {
                echo 'Erreur : ' . $e->getMessage();
            }
            return false;
        }
    }

    public function getPostUpdateById($id) {
        $sql = "SELECT * FROM update_post WHERE id_update_post = :id";
        $bd = Config::getConnexion();
        try {
            $query = $bd->prepare($sql);
            $query->bindValue(':id', $id);
            $query->execute();
            return $query->fetch(); // retourne une seule ligne (le postupdate)
        } catch (Exception $e) {
            error_log("Erreur lors de la récupération du post update : " . $e->getMessage());
            if (ini_get('display_errors')) {
                echo 'Erreur : ' . $e->getMessage();
            }
            return false;
        }
    }

    public function updatePostUpdate(PostUpdate $postUpdate, $id = null, $ownerId = null) {
        $sql = "UPDATE update_post 
                SET id_post = :id_post,
                    id_user = :id_user,
                    texte = :texte, 
                    media = :media, 
                    realiser = :realiser, 
                    temp_reponse = :temp_reponse, 
                    temp_resolue = :temp_resolue 
                WHERE id_update_post = :id";
        if ($ownerId !== null) {
            $sql .= " AND id_user = :owner_id";
        }

        $bd = Config::getConnexion();

        try {
            $query = $bd->prepare($sql);

            $media = $postUpdate->getMedia();
            if ($media === null) {
                $media = '';
            }
            if ($id === null) {
                $id = $postUpdate->getIdUpdatePost();
            }
            $params = [
                ':id_post' => $postUpdate->getIdPost(),
                ':id_user' => $postUpdate->getIdUser(),
                ':texte' => $postUpdate->getTexte(),
                ':media' => $media,
                ':realiser' => $postUpdate->getRealiser(),
                ':temp_reponse' => $postUpdate->getTempReponse(),
                ':temp_resolue' => $postUpdate->getTempResolue(),
                ':id' => $id
            ];
            
            if ($ownerId !== null) {
                $params[':owner_id'] = (int) $ownerId;
            }
            
            $result = $query->execute($params);
            
            return $result && $query->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Erreur PDO lors de la mise à jour du post update : " . $e->getMessage());
            if (ini_get('display_errors')) {
                echo "Erreur SQL : " . $e->getMessage() . "<br>";
            }
            return false;
        } catch (Exception $e) {
            error_log("Erreur lors de la mise à jour du post update : " . $e->getMessage());
            if (ini_get('display_errors')) {
                echo 'Erreur : ' . $e->getMessage();
            }
            return false;
        }
    }
}

?>
