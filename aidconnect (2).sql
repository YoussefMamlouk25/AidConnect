-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : dim. 14 déc. 2025 à 00:52
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `aidconnect`
--

-- --------------------------------------------------------

--
-- Structure de la table `events`
--

CREATE TABLE `events` (
  `id_event` int(11) NOT NULL,
  `titre` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `date_event` datetime NOT NULL,
  `lieu` varchar(255) NOT NULL,
  `nombre_places` int(11) NOT NULL,
  `type_event` enum('collecte','distribution','soutien','logement','autre') NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `statut` enum('actif','supprime') DEFAULT 'actif',
  `date_creation` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Table des événements caritatifs';

--
-- Déchargement des données de la table `events`
--

INSERT INTO `events` (`id_event`, `titre`, `description`, `date_event`, `lieu`, `nombre_places`, `type_event`, `image`, `statut`, `date_creation`) VALUES
(3, 'rarararara', 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaab', '2025-12-28 22:07:00', 'tujnnnkk', 46, 'collecte', 'uploads/events/693dd59c48edc.png', 'actif', '2025-12-13 21:07:40');

-- --------------------------------------------------------

--
-- Structure de la table `inscriptions`
--

CREATE TABLE `inscriptions` (
  `id_inscription` int(11) NOT NULL,
  `id_event` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `telephone` varchar(8) NOT NULL,
  `pourquoi` text NOT NULL,
  `avis` text DEFAULT NULL,
  `note` int(11) DEFAULT NULL CHECK (`note` between 1 and 5),
  `statut` enum('en_attente','confirme','annule') DEFAULT 'en_attente',
  `date_inscription` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Table des inscriptions aux événements';

-- --------------------------------------------------------

--
-- Structure de la table `offer`
--

CREATE TABLE `offer` (
  `id_offer` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `media` varchar(255) DEFAULT NULL,
  `nom` varchar(100) DEFAULT NULL,
  `num` varchar(40) DEFAULT NULL,
  `email` varchar(120) DEFAULT NULL,
  `type_don` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `offer`
--

INSERT INTO `offer` (`id_offer`, `id_user`, `description`, `media`, `nom`, `num`, `email`, `type_don`) VALUES
(14, 2, 'jhhhhhhhhhhhhhhhhhhhhhh', '[\"1765049210_0_logo.png\"]', 'racem', '53825192', 'med.racem@gmail.com', 'equipment'),
(15, 2, 'azertyuzertyui', '[\"1765048680_0_462579851_524672860554893_5394586269373273050_n.jpg\"]', 'racem', '12345678', 'med.racem@gmail.com', 'food'),
(16, 1, 'dfbdfhdfsh', NULL, 'ksy aa', '50544819', 'ksymult@gmail.com', 'food');

-- --------------------------------------------------------

--
-- Structure de la table `offer_like`
--

CREATE TABLE `offer_like` (
  `id_like` int(11) NOT NULL,
  `id_offer` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `date_like` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `offer_like`
--

INSERT INTO `offer_like` (`id_like`, `id_offer`, `id_user`, `date_like`) VALUES
(1, 13, 3, '2025-12-06 19:09:09');

-- --------------------------------------------------------

--
-- Structure de la table `post`
--

CREATE TABLE `post` (
  `id_post` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `Numéro` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `titre` varchar(50) NOT NULL,
  `region` varchar(50) NOT NULL,
  `description` varchar(200) NOT NULL,
  `date_creation` date NOT NULL,
  `media` varchar(400) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `post`
--

INSERT INTO `post` (`id_post`, `id_user`, `nom`, `Numéro`, `email`, `titre`, `region`, `description`, `date_creation`, `media`) VALUES
(36, 1, 'ksy aa', '92133211', 'ksymult@gmail.com', 'maison detruit', 'Béja', 'aaaaaaaaaaaaaaaaaaaaaa', '2025-12-01', ''),
(37, 2, 'racem', '92133211', 'med.racem@gmail.com', 'maison detruit', 'Béja', 'AAAAAAAAAAAAAAAAAAAA', '2025-12-01', ''),
(38, 2, 'racem', '92133211', 'med.racem@gmail.com', 'maison detruit', 'Béja', 'NHHHHHHHHHHHHHHHHHH', '2025-12-06', ''),
(39, 2, 'racem', '92133211', 'med.racem@gmail.com', 'maison detruit', 'Béja', 'aaaaaaaaaaaaaaaaaaaaaadddddd', '2025-12-06', ''),
(40, 2, 'racem', '92133211', 'med.racem@gmail.com', 'maison detruit', 'Nabeul', 'hhhhhhhhhhhhhhhhhhh', '2025-12-06', ''),
(41, 2, 'racem', '92133211', 'med.racem@gmail.com', 'maison detruit', 'Nabeul', 'gadzdaddasdzaggggggggggggggggggggggggg', '2025-12-06', ''),
(42, 3, 'arbi', '23568989', 'med.arbi@gmail.com', 'aze', 'Ariana', 'aaaaaaaaaaaaaaaaaaaaaa', '2025-11-15', ''),
(43, 3, 'arbi', '23568989', 'med.arbi@gmail.com', 'aze', 'Ariana', 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', '2025-11-15', ''),
(44, 3, 'arbi', '13467888', 'med.arbi@gmail.com', 'rarararara', 'Sfax', 'aaaaaaaaaaaaaaaaaaaaaazzzzzzzzzzzzzzzzz', '2025-12-06', ''),
(45, 20, 'aaaa', '13467888', 'projet1a2025v1@gmail.com', 'rarararara', 'Sfax', 'aaaaaaaaaaaaaaaaaazzzzzzzzzzzzzzzzzzzzzzzzzzzz', '2025-12-12', '[\"1765579219_0_IMAGE_BATMAN.jpg\"]');

-- --------------------------------------------------------

--
-- Structure de la table `post_comment`
--

CREATE TABLE `post_comment` (
  `id_comment` int(11) NOT NULL,
  `id_post` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `comment_text` text NOT NULL,
  `date_comment` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `post_comment`
--

INSERT INTO `post_comment` (`id_comment`, `id_post`, `id_user`, `comment_text`, `date_comment`) VALUES
(1, 44, 2, 'parfait', '2025-12-06 19:16:32'),
(2, 42, 2, 'merci', '2025-12-06 19:43:36');

-- --------------------------------------------------------

--
-- Structure de la table `post_like`
--

CREATE TABLE `post_like` (
  `id_like` int(11) NOT NULL,
  `id_post` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `date_like` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `post_like`
--

INSERT INTO `post_like` (`id_like`, `id_post`, `id_user`, `date_like`) VALUES
(1, 44, 3, '2025-12-06 18:47:57'),
(2, 44, 2, '2025-12-06 18:48:27'),
(4, 43, 3, '2025-12-06 19:07:01'),
(6, 43, 2, '2025-12-06 19:43:28'),
(7, 42, 2, '2025-12-06 19:43:30');

-- --------------------------------------------------------

--
-- Structure de la table `reclamation`
--

CREATE TABLE `reclamation` (
  `id_reclamation` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `titre` varchar(200) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `media` varchar(255) DEFAULT NULL,
  `date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `update_offer`
--

CREATE TABLE `update_offer` (
  `id_update_offer` int(11) NOT NULL,
  `id_offer` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `texte` text DEFAULT NULL,
  `media` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `update_offer`
--

INSERT INTO `update_offer` (`id_update_offer`, `id_offer`, `id_user`, `texte`, `media`) VALUES
(7, 14, 2, '📝 Update: ojmojmlkml', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `update_post`
--

CREATE TABLE `update_post` (
  `id_update_post` int(11) NOT NULL,
  `id_post` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `texte` text NOT NULL,
  `media` varchar(255) NOT NULL,
  `realiser` tinyint(1) NOT NULL,
  `temp_reponse` varchar(50) NOT NULL,
  `temp_resolue` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `update_post`
--

INSERT INTO `update_post` (`id_update_post`, `id_post`, `id_user`, `texte`, `media`, `realiser`, `temp_reponse`, `temp_resolue`) VALUES
(27, 42, 3, 'azertyuiop', '', 0, '1', '2');

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `id_user` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(120) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `cin` varchar(20) DEFAULT NULL,
  `date_naissance` date DEFAULT NULL,
  `role` varchar(20) DEFAULT NULL,
  `tel` varchar(30) DEFAULT NULL,
  `date_creation` date DEFAULT NULL,
  `region` varchar(100) DEFAULT NULL,
  `profile_pic` varchar(255) DEFAULT NULL,
  `verified` tinyint(1) DEFAULT 0,
  `verify_token` varchar(64) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `verification_code` varchar(6) DEFAULT NULL COMMENT 'Code de vérification email (6 chiffres)',
  `email_verified` tinyint(1) DEFAULT 0 COMMENT 'Email vérifié (0=non, 1=oui)',
  `verification_code_expires` datetime DEFAULT NULL COMMENT 'Date d''expiration du code de vérification'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id_user`, `name`, `email`, `password_hash`, `cin`, `date_naissance`, `role`, `tel`, `date_creation`, `region`, `profile_pic`, `verified`, `verify_token`, `created_at`, `verification_code`, `email_verified`, `verification_code_expires`) VALUES
(1, 'ksy aa', 'ksymult@gmail.com', '$2y$10$706ZbLQRiD2moxXomWqvhuD7yvDfxyZiYf6VoEYQUNAFvmJ2MwEA.', '12345678', '2005-10-26', 'admin', '92133211', '2025-12-01', 'Kairouan', NULL, 0, NULL, '2025-12-12 23:55:21', NULL, 1, NULL),
(2, 'racem', 'med.racem@gmail.com', '$2y$10$BWvOUk2YMSPpTxS17hmb..IzsO2of86QXpQWvhBxFUFO6ia4vOpZi', '12345678', '2005-07-26', 'admin', '29499774', '2025-12-01', 'Kairouan', NULL, 0, NULL, '2025-12-12 21:04:43', NULL, 0, NULL),
(3, 'arbi', 'med.arbi@gmail.com', '$2y$10$qWtoOjoT073OjRsusb8CX.5Cyqm31l.T6jhPqt7Bf2rzv.jYy6gXC', '12345678', '1997-02-04', 'volunteer', '98765432', '2025-12-06', 'Kairouan', NULL, 0, NULL, '2025-12-12 21:04:43', NULL, 0, NULL),
(20, 'aaaa', 'projet1a2025v1@gmail.com', '$2y$10$6E96NjHHlm.vK00HTNdGluGqkVDf1SogIxyg1eJVesL6jaTFMqNWm', '12345689', '2005-02-25', 'volunteer', '12345678', '2025-12-12', 'Sfax', 'public/uploads/profile/user_20_1765579200.jpg', 0, NULL, '2025-12-12 23:24:34', NULL, 1, NULL);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id_event`),
  ADD KEY `idx_date` (`date_event`),
  ADD KEY `idx_statut` (`statut`),
  ADD KEY `idx_type` (`type_event`);

--
-- Index pour la table `inscriptions`
--
ALTER TABLE `inscriptions`
  ADD PRIMARY KEY (`id_inscription`),
  ADD UNIQUE KEY `unique_inscription` (`id_event`,`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_event` (`id_event`),
  ADD KEY `idx_statut` (`statut`);

--
-- Index pour la table `offer`
--
ALTER TABLE `offer`
  ADD PRIMARY KEY (`id_offer`),
  ADD KEY `id_user` (`id_user`);

--
-- Index pour la table `offer_like`
--
ALTER TABLE `offer_like`
  ADD PRIMARY KEY (`id_like`),
  ADD UNIQUE KEY `unique_like` (`id_offer`,`id_user`),
  ADD KEY `idx_offer` (`id_offer`),
  ADD KEY `idx_user` (`id_user`);

--
-- Index pour la table `post`
--
ALTER TABLE `post`
  ADD PRIMARY KEY (`id_post`),
  ADD KEY `id_user` (`id_user`);

--
-- Index pour la table `post_comment`
--
ALTER TABLE `post_comment`
  ADD PRIMARY KEY (`id_comment`),
  ADD KEY `idx_post` (`id_post`),
  ADD KEY `idx_user` (`id_user`);

--
-- Index pour la table `post_like`
--
ALTER TABLE `post_like`
  ADD PRIMARY KEY (`id_like`),
  ADD UNIQUE KEY `unique_like` (`id_post`,`id_user`),
  ADD KEY `idx_post` (`id_post`),
  ADD KEY `idx_user` (`id_user`);

--
-- Index pour la table `reclamation`
--
ALTER TABLE `reclamation`
  ADD PRIMARY KEY (`id_reclamation`),
  ADD KEY `id_user` (`id_user`);

--
-- Index pour la table `update_offer`
--
ALTER TABLE `update_offer`
  ADD PRIMARY KEY (`id_update_offer`),
  ADD KEY `id_offer` (`id_offer`),
  ADD KEY `id_user` (`id_user`);

--
-- Index pour la table `update_post`
--
ALTER TABLE `update_post`
  ADD PRIMARY KEY (`id_update_post`),
  ADD KEY `id_post` (`id_post`),
  ADD KEY `id_user` (`id_user`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`),
  ADD KEY `idx_email_verified` (`email_verified`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `events`
--
ALTER TABLE `events`
  MODIFY `id_event` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `inscriptions`
--
ALTER TABLE `inscriptions`
  MODIFY `id_inscription` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `offer`
--
ALTER TABLE `offer`
  MODIFY `id_offer` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT pour la table `offer_like`
--
ALTER TABLE `offer_like`
  MODIFY `id_like` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `post`
--
ALTER TABLE `post`
  MODIFY `id_post` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT pour la table `post_comment`
--
ALTER TABLE `post_comment`
  MODIFY `id_comment` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `post_like`
--
ALTER TABLE `post_like`
  MODIFY `id_like` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `reclamation`
--
ALTER TABLE `reclamation`
  MODIFY `id_reclamation` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `update_offer`
--
ALTER TABLE `update_offer`
  MODIFY `id_update_offer` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `update_post`
--
ALTER TABLE `update_post`
  MODIFY `id_update_post` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `inscriptions`
--
ALTER TABLE `inscriptions`
  ADD CONSTRAINT `fk_inscription_event` FOREIGN KEY (`id_event`) REFERENCES `events` (`id_event`) ON DELETE CASCADE;

--
-- Contraintes pour la table `offer`
--
ALTER TABLE `offer`
  ADD CONSTRAINT `offer_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`);

--
-- Contraintes pour la table `post`
--
ALTER TABLE `post`
  ADD CONSTRAINT `post_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`);

--
-- Contraintes pour la table `reclamation`
--
ALTER TABLE `reclamation`
  ADD CONSTRAINT `reclamation_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`);

--
-- Contraintes pour la table `update_offer`
--
ALTER TABLE `update_offer`
  ADD CONSTRAINT `update_offer_ibfk_1` FOREIGN KEY (`id_offer`) REFERENCES `offer` (`id_offer`),
  ADD CONSTRAINT `update_offer_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`);

--
-- Contraintes pour la table `update_post`
--
ALTER TABLE `update_post`
  ADD CONSTRAINT `update_post_ibfk_1` FOREIGN KEY (`id_post`) REFERENCES `post` (`id_post`),
  ADD CONSTRAINT `update_post_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
