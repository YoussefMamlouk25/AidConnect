<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Model/message.php';

class MessageController {

    // Add a new message
    public function addMessage(Message $message) {
        $sql = "INSERT INTO message (id_reclamation, message, date, isadmin, media) VALUES (:id_reclamation, :message, :date, :isadmin, :media)";
        $bd = Config::getConnexion();

        try {
            $query = $bd->prepare($sql);
            $query->bindValue(':id_reclamation', $message->getIdReclamation());
            $query->bindValue(':message', $message->getMessage());
            $query->bindValue(':date', $message->getDate() ? $message->getDate() : date('Y-m-d'));
            $query->bindValue(':isadmin', $message->getIsadmin());
            $query->bindValue(':media', $message->getMedia() ? $message->getMedia() : '');
            return $query->execute();
        } catch (PDOException $e) {
            error_log('Erreur PDO lors de l\'ajout du message: ' . $e->getMessage());
            if (ini_get('display_errors')) {
                echo "Erreur SQL : " . $e->getMessage() . "<br>";
            }
            return false;
        }
    }

    // Delete a message by id_message, with ownership check by isadmin (0=user, 1=admin)
    // Only the sender can delete their own message
    public function deleteMessage($id_message, $currentIsAdmin) {
        $sqlCheck = "SELECT isadmin FROM message WHERE id_message = :id_message";
        $bd = Config::getConnexion();
        try {
            $queryCheck = $bd->prepare($sqlCheck);
            $queryCheck->bindValue(':id_message', $id_message);
            $queryCheck->execute();
            $messageData = $queryCheck->fetch();
            if (!$messageData) {
                return false;
            }
            // Only the sender can delete their own message
            $currentIsAdminInt = intval($currentIsAdmin);
            $messageIsAdminInt = intval($messageData['isadmin']);
            if ($messageIsAdminInt != $currentIsAdminInt) {
                // Ownership mismatch - only sender can delete
                return false;
            }
            $sql = "DELETE FROM message WHERE id_message = :id_message";
            $query = $bd->prepare($sql);
            $query->bindValue(':id_message', $id_message);
            return $query->execute();
        } catch (PDOException $e) {
            error_log('Erreur PDO lors de la suppression du message: ' . $e->getMessage());
            if (ini_get('display_errors')) {
                echo "Erreur SQL : " . $e->getMessage() . "<br>";
            }
            return false;
        }
    }

    // Update a message by id_message, with ownership check by isadmin (0=user, 1=admin)
    // Only the sender can update their own message
    public function updateMessage(Message $message, $currentIsAdmin) {
        $bd = Config::getConnexion();
        try {
            $sqlCheck = "SELECT isadmin FROM message WHERE id_message = :id_message";
            $queryCheck = $bd->prepare($sqlCheck);
            $queryCheck->bindValue(':id_message', $message->getIdMessage());
            $queryCheck->execute();
            $messageData = $queryCheck->fetch();
            if (!$messageData) {
                return false;
            }
            // Only the sender can update their own message
            $currentIsAdminInt = intval($currentIsAdmin);
            $messageIsAdminInt = intval($messageData['isadmin']);
            if ($messageIsAdminInt != $currentIsAdminInt) {
                // Ownership mismatch - only sender can update
                return false;
            }
            $sql = "UPDATE message SET message = :message, date = :date, media = :media WHERE id_message = :id_message";
            $query = $bd->prepare($sql);
            $query->bindValue(':message', $message->getMessage());
            $query->bindValue(':date', $message->getDate() ? $message->getDate() : date('Y-m-d'));
            $query->bindValue(':media', $message->getMedia() ? $message->getMedia() : '');
            $query->bindValue(':id_message', $message->getIdMessage());
            return $query->execute();
        } catch (PDOException $e) {
            error_log('Erreur PDO lors de la mise à jour du message: ' . $e->getMessage());
            if (ini_get('display_errors')) {
                echo "Erreur SQL : " . $e->getMessage() . "<br>";
            }
            return false;
        }
    }

    // Get messages by id_reclamation
    public function getMessagesByIdReclamation($id_reclamation) {
        $sql = "SELECT * FROM message WHERE id_reclamation = :id_reclamation";
        $bd = Config::getConnexion();

        try {
            $query = $bd->prepare($sql);
            $query->bindValue(':id_reclamation', $id_reclamation);
            $query->execute();
            return $query->fetchAll();
        } catch (PDOException $e) {
            error_log('Erreur PDO lors de la récupération des messages: ' . $e->getMessage());
            return array();
        }
    }

    // Get message by id_message
    public function getMessageById($id_message) {
        $sql = "SELECT * FROM message WHERE id_message = :id_message";
        $bd = Config::getConnexion();

        try {
            $query = $bd->prepare($sql);
            $query->bindValue(':id_message', $id_message);
            $query->execute();
            return $query->fetch();
        } catch (PDOException $e) {
            error_log('Erreur PDO lors de la récupération du message: ' . $e->getMessage());
            return null;
        }
    }
}
?>
