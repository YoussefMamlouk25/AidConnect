-- Script SQL pour créer la table offer_like
-- Cette table stocke les likes des utilisateurs sur les offres

CREATE TABLE IF NOT EXISTS offer_like (
    id_like INT AUTO_INCREMENT PRIMARY KEY,
    id_offer INT NOT NULL,
    id_user INT NOT NULL,
    date_like TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_like (id_offer, id_user),
    INDEX idx_offer (id_offer),
    INDEX idx_user (id_user)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Explication des colonnes :
-- id_like : Identifiant unique du like
-- id_offer : ID de l'offre likée (référence à la table offer)
-- id_user : ID de l'utilisateur qui a liké (référence à la table user)
-- date_like : Date et heure du like
-- unique_like : Contrainte pour éviter qu'un utilisateur like plusieurs fois la même offre

