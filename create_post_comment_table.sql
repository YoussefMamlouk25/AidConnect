-- Script SQL pour créer la table post_comment
-- Cette table stocke les commentaires des utilisateurs sur les posts

CREATE TABLE IF NOT EXISTS post_comment (
    id_comment INT AUTO_INCREMENT PRIMARY KEY,
    id_post INT NOT NULL,
    id_user INT NOT NULL,
    comment_text TEXT NOT NULL,
    date_comment TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_post (id_post),
    INDEX idx_user (id_user)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Explication des colonnes :
-- id_comment : Identifiant unique du commentaire
-- id_post : ID du post commenté (référence à la table post)
-- id_user : ID de l'utilisateur qui a commenté (référence à la table user)
-- comment_text : Texte du commentaire
-- date_comment : Date et heure du commentaire

