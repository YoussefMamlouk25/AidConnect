<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Model/Reclamation.php';

class ReclamationController {

    // Ajouter une réclamation
    public function addReclamation(Reclamation $reclamation, $id_user = null) {
        $sql = "INSERT INTO reclamation
            (id_user, titre, description, media, date)
            VALUES (:id_user, :titre, :description, :media, :date)";

        $bd = Config::getConnexion();

        try {
            $query = $bd->prepare($sql);

            // Gestion de la date
            $dateCreation = $reclamation->getDateCreation();
            if ($dateCreation instanceof DateTime) {
                $dateCreation = $dateCreation->format('Y-m-d H:i:s');
            } elseif ($dateCreation === null) {
                $dateCreation = date('Y-m-d H:i:s');
            }

            // Gérer media : si null, utiliser chaîne vide
            $media = $reclamation->getMedia();
            if ($media === null) {
                $media = '';
            }

            $id_user_param = $id_user;
            $titre_param = $reclamation->getTitre();
            $description_param = $reclamation->getDescription();
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
            error_log("Erreur PDO lors de l'ajout de la réclamation : " . $e->getMessage());
            if (ini_get('display_errors')) {
                echo "Erreur SQL : " . $e->getMessage() . "<br>";
            }
            return false;
        } catch (Exception $e) {
            error_log("Erreur lors de l'ajout de la réclamation : " . $e->getMessage());
            if (ini_get('display_errors')) {
                echo "Erreur : " . $e->getMessage() . "<br>";
            }
            return false;
        }
    }

    // Récupérer toutes les réclamations
    public function getAllReclamations() {
        $sql = "SELECT * FROM reclamation ORDER BY id_reclamation DESC";
        $bd = Config::getConnexion();
        try {
            $list = $bd->query($sql);
            return $list;
        } catch (Exception $e) {
            error_log('Erreur getAllReclamations : ' . $e->getMessage());
            return $bd->query("SELECT * FROM reclamation WHERE 1=0");
        }
    }

    // Supprimer une réclamation par ID
    public function deleteReclamation($id) {
        $id = (int) $id;
        error_log("Attempting to delete reclamation with id_reclamation: " . $id);
        $sql = "DELETE FROM reclamation WHERE id_reclamation = :id";
        $bd = Config::getConnexion();
        try {
            $rep = $bd->prepare($sql);
            $rep->bindParam(':id', $id);
            $rep->execute();
            return true;
        } catch (Exception $e) {
            error_log('Erreur deleteReclamation : ' . $e->getMessage());
            return false;
        }
    }

    // Récupérer une réclamation par ID
    public function getReclamationById($id) {
        $sql = "SELECT * FROM reclamation WHERE id_reclamation = :id";
        $bd = Config::getConnexion();
        try {
            $query = $bd->prepare($sql);
            $query->bindValue(':id', $id);
            $query->execute();
            return $query->fetch();
        } catch (Exception $e) {
            error_log('Erreur getReclamationById : ' . $e->getMessage());
            return false;
        }
    }

    // Mettre à jour une réclamation
    public function updateReclamation(Reclamation $reclamation, $id) {
        $sql = "UPDATE reclamation
                SET titre = :titre,
                    description = :description,
                    media = :media,
                    date = :date
                WHERE id_reclamation = :id";

        $bd = Config::getConnexion();

        try {
            // Gestion de la date
            $dateCreation = $reclamation->getDateCreation();
            if ($dateCreation instanceof DateTime) {
                $dateCreation = $dateCreation->format('Y-m-d H:i:s');
            } elseif ($dateCreation === null) {
                $dateCreation = date('Y-m-d H:i:s');
            }

            // Gérer media : si null, utiliser chaîne vide
            $media = $reclamation->getMedia();
            if ($media === null) {
                $media = '';
            }

            $titre_value = $reclamation->getTitre();
            $desc_value = $reclamation->getDescription();
            $query = $bd->prepare($sql);
            $query->bindParam(':titre', $titre_value);
            $query->bindParam(':description', $desc_value);
            $query->bindParam(':media', $media);
            $query->bindParam(':date', $dateCreation);
            $query->bindParam(':id', $id);
            $result = $query->execute();

            return $result;
        } catch (PDOException $e) {
            error_log("Erreur PDO lors de la mise à jour de la réclamation : " . $e->getMessage());
            if (ini_get('display_errors')) {
                echo "Erreur SQL : " . $e->getMessage() . "<br>";
            }
            return false;
        } catch (Exception $e) {
            error_log("Erreur lors de la mise à jour de la réclamation : " . $e->getMessage());
            if (ini_get('display_errors')) {
                echo "Erreur : " . $e->getMessage() . "<br>";
            }
            return false;
        }
    }

    // Récupérer les réclamations par id_user
    public function getReclamationsByUser($id_user) {
        $sql = "SELECT * FROM reclamation WHERE id_user = :id_user ORDER BY date DESC";
        $bd = Config::getConnexion();
        try {
            $query = $bd->prepare($sql);
            $query->bindValue(':id_user', $id_user);
            $query->execute();
            return $query->fetchAll();
        } catch (Exception $e) {
            error_log('Erreur getReclamationsByUser : ' . $e->getMessage());
            return array();
        }
    }

    // Gérer les actions
    public function handleAction() {
        $action = $_GET['action'] ?? $_POST['action'] ?? 'list';

        switch ($action) {
            case 'add':
                $this->add();
                break;
            case 'list':
                $this->list();
                break;
            case 'delete':
                $this->delete();
                break;
            case 'mes_reclamations':
                $this->mesReclamations();
                break;
            default:
                $this->list();
                break;
        }
    }

    // Action: Ajouter une réclamation
    private function add() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            session_start();
            if (!isset($_SESSION['id_user'])) {
                header('Location: ../View/frontoffice/login.php?error=' . urlencode('Veuillez vous connecter.'));
                exit;
            }

            $media = '';
            if (isset($_FILES['media']) && $_FILES['media']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../public/uploads/';
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $mediaName = time() . '_' . preg_replace('/[^A-Za-z0-9._-]/', '_', basename($_FILES['media']['name']));
                $media = $mediaName;
                move_uploaded_file($_FILES['media']['tmp_name'], $uploadDir . $mediaName);
            }

            $reclamation = new Reclamation(
                null,
                $_POST['sujet'] ?? '',
                null,
                $_POST['description'] ?? '',
                $media,
                new DateTime()
            );

            $result = $this->addReclamation($reclamation, $_SESSION['id_user']);
            
            if ($result) {
                header('Location: ../View/frontoffice/reclamation.php?success=' . urlencode('Réclamation envoyée avec succès!'));
            } else {
                header('Location: ../View/frontoffice/reclamation.php?error=' . urlencode('Erreur lors de l\'envoi de la réclamation.'));
            }
            exit;
        }
    }

    // Action: Lister toutes les réclamations (backoffice)
    private function list() {
        session_start();
        if (!isset($_SESSION['id_user']) || ($_SESSION['role'] ?? '') !== 'admin') {
            if (isset($_GET['embed']) || isset($_GET['ajax'])) {
                http_response_code(403);
                echo json_encode(['error' => 'Non autorisé']);
            } else {
                header('Location: ../View/frontoffice/login.php');
            }
            exit;
        }

        // Ajouter le paramètre embed si c'est une requête AJAX
        if (isset($_GET['ajax']) || isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            $_GET['embed'] = '1';
        }

        require_once __DIR__ . '/../View/backoffice/reclamations_list.php';
    }

    // Action: Mes réclamations (frontoffice)
    private function mesReclamations() {
        session_start();
        if (!isset($_SESSION['id_user'])) {
            header('Location: ../View/frontoffice/login.php?error=' . urlencode('Veuillez vous connecter.'));
            exit;
        }

        // Rediriger vers la page dédiée
        header('Location: ../View/frontoffice/mes_reclamations.php');
        exit;
    }

    // Action: Supprimer une réclamation
    private function delete() {
        session_start();
        if (!isset($_SESSION['id_user']) || ($_SESSION['role'] ?? '') !== 'admin') {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Non autorisé']);
            exit;
        }

        $id = $_POST['id'] ?? $_GET['id'] ?? 0;
        $result = $this->deleteReclamation($id);
        
        if (isset($_GET['embed']) && $_GET['embed'] == '1') {
            // Si c'est une requête AJAX, recharger la liste
            $this->list();
        } else {
            header('Location: reclamationcontroller.php?action=list');
        }
        exit;
    }
}

// Exécuter le contrôleur si appelé directement
if (basename($_SERVER['PHP_SELF']) == 'reclamationcontroller.php') {
    $controller = new ReclamationController();
    $controller->handleAction();
}
?>

