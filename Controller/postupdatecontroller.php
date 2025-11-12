<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Model/postupdate.php';

class PostUpdateController {
    public function addPostUpdate(PostUpdate $postUpdate) {
        $sql = "INSERT INTO postupdate VALUES (NULL, :titre, :description, :photo, :ville, :intervention, :joursReponse, :joursResolution)";
        $bd = Config::getConnexion();

        try {
            $query = $bd->prepare($sql);
            $query->execute([
                            ':titre' => $postUpdate->getTitre(),
                            ':description' => $postUpdate->getDescription(),
                            ':photo' => $postUpdate->getPhoto(),
                            ':ville' => $postUpdate->getVille(),
                            ':intervention' => $postUpdate->getIntervention(),
                            ':joursReponse' => $postUpdate->getJoursReponse(),
                            ':joursResolution' => $postUpdate->getJoursResolution()]);
             
        } catch (Exception $e) {
            echo 'Erreur : ' . $e->getMessage();
        }
       

    }

    public function getAllPostUpdates() {
        $sql = "SELECT * FROM postupdate";
        $bd = Config::getConnexion();
        try {
            $list = $bd->query($sql);
            return $list;
        } catch (Exception $e) {
            echo 'Erreur : ' . $e->getMessage();
            return [];
        }
    }
    
    public function deletePostUpdate($id) {
        $sql = "DELETE FROM postupdate WHERE id = :id";
        $bd = Config::getConnexion();
        try {
            $rep = $bd->prepare($sql);
            $rep->bindValue(':id', $id);
            $rep->execute();
        } catch (Exception $e) {
            echo 'Erreur : ' . $e->getMessage();
        }
        
    }

    public function getPostUpdateById($id) {
        $sql = "SELECT * FROM postupdate WHERE id = :id";
        $bd = Config::getConnexion();
        try {
            $query = $bd->prepare($sql);
            $query->bindValue(':id', $id);
            $query->execute();
            return $query->fetch(); // retourne une seule ligne (le postupdate)
        } catch (Exception $e) {
            echo 'Erreur : ' . $e->getMessage();
            return false;
        }
    }

    public function updatePostUpdate(PostUpdate $postUpdate, $id) {
        $sql = "UPDATE postupdate 
                SET titre = :titre, 
                    description = :description, 
                    photo = :photo, 
                    ville = :ville, 
                    intervention = :intervention, 
                    joursReponse = :joursReponse, 
                    joursResolution = :joursResolution 
                WHERE id = :id";

        $bd = Config::getConnexion();

        try {
            $query = $bd->prepare($sql);
            $query->execute([
                ':titre' => $postUpdate->getTitre(),
                ':description' => $postUpdate->getDescription(),
                ':photo' => $postUpdate->getPhoto(),
                ':ville' => $postUpdate->getVille(),
                ':intervention' => $postUpdate->getIntervention(),
                ':joursReponse' => $postUpdate->getJoursReponse(),
                ':joursResolution' => $postUpdate->getJoursResolution(),
                ':id' => $id
            ]);
        } catch (Exception $e) {
            echo 'Erreur : ' . $e->getMessage();
        }
    }


}
