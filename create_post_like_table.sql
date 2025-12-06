-- Script SQL pour créer la table post_like
-- Cette table stocke les likes des utilisateurs sur les posts

CREATE TABLE IF NOT EXISTS post_like (
    id_like INT AUTO_INCREMENT PRIMARY KEY,
    id_post INT NOT NULL,
    id_user INT NOT NULL,
    date_like TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_like (id_post, id_user),
    INDEX idx_post (id_post),
    INDEX idx_user (id_user)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Explication des colonnes :
-- id_like : Identifiant unique du like
-- id_post : ID du post liké (référence à la table post)
-- id_user : ID de l'utilisateur qui a liké (référence à la table user)
-- date_like : Date et heure du like
-- unique_like : Contrainte pour éviter qu'un utilisateur like plusieurs fois le même post

