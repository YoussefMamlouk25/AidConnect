-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : sam. 13 déc. 2025 à 00:05
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `projetweb_db`
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
(1, 'Distribution de repas aux sans-abris', 'Venez nous aider à distribuer des repas chauds aux personnes dans le besoin. Une action solidaire importante pour aider les plus démunis pendant l\'hiver. Nous aurons besoin de bénévoles pour la préparation, le service et la distribution.', '2025-01-15 14:00:00', '123 Rue de la Charité, Tunis', 50, 'distribution', NULL, 'supprime', '2025-12-11 19:33:57'),
(2, 'Collecte de vêtements d\'hiver', 'Grande collecte de vêtements chauds pour les familles démunies. Tous les dons sont les bienvenus : manteaux, pulls, écharpes, gants, bonnets. Les vêtements seront triés puis redistribués aux associations partenaires.', '2026-01-20 10:00:00', '45 Avenue Habib Bourguiba, Tunis', 30, 'collecte', '', 'actif', '2025-12-11 19:33:57'),
(3, 'Soutien médical gratuit', 'Consultations médicales gratuites et distribution de médicaments pour les personnes sans couverture sociale. Des médecins bénévoles seront présents pour effectuer des consultations générales. Apportez vos ordonnances si vous en avez.', '2025-12-25 09:00:00', 'Centre de santé municipal, Ariana', 100, 'soutien', 'uploads/events/693c23bd8038c.jpg', 'actif', '2025-12-11 19:33:57'),
(4, 'Aide au relogement temporaire', 'Programme d\'aide au logement temporaire pour les familles en situation d\'urgence. Nous recherchons des bénévoles pour aider à l\'accueil, l\'inscription et l\'orientation des familles.', '2025-02-01 14:00:00', '78 Rue des Oliviers, La Marsa', 20, 'logement', NULL, 'actif', '2025-12-11 19:33:57'),
(5, 'Journée de sensibilisation à la solidarité', 'Grande journée de sensibilisation aux actions caritatives dans notre ville. Stands d\'information, témoignages, rencontres avec les associations. Ouvert à tous, entrée libre.', '2025-02-10 10:00:00', 'Place de la République, Tunis Centre', 200, 'autre', NULL, 'supprime', '2025-12-11 19:33:57'),
(6, 'Abdelafattah Guenenna', 'kjzckjaopckaopkzxpajkndjiojkdiokazj oazjkoldkazl', '2026-05-21 05:26:00', 'mahdia', 5, 'logement', 'uploads/events/693b271bc4feb.png', 'supprime', '2025-12-11 20:18:35'),
(7, 'karim', 'ijciozejciojkzoizejkcoi', '2025-12-28 21:46:00', 'mahdia', 2, 'collecte', '', 'supprime', '2025-12-11 20:46:57'),
(8, 'ba3raket', 'eznjkdzndjkzezjdjkeldezl kzj,edlkeazdj,', '2025-12-18 02:59:00', 'mahdia852985', 6, 'logement', '', 'supprime', '2025-12-11 21:44:59'),
(9, 'Abdelafattah Guenenna', 'GJFZIOPJFIOLJKLFKJER', '2025-12-28 04:27:00', 'mahdia69', 3, 'logement', 'uploads/events/693c6cbb1596e.png', 'supprime', '2025-12-12 19:27:55'),
(10, 'firas', 'ghntjkg&quot;rklt,n jklj&quot;grlk', '2025-12-12 23:41:00', 'mahdia69', 3, 'logement', 'uploads/events/693c6fdf0bf2e.png', 'actif', '2025-12-12 19:41:19'),
(11, 'karimos', 'loliurfehjfuiajhreijiofaoiakfop ', '2555-12-27 20:47:00', 'mahdia69', 3, 'collecte', '', 'supprime', '2025-12-12 19:48:13'),
(12, 'gzzkjgn\'jk(n', 'zdiajndioazjdsoiadzajdjazdjkaziolk', '2025-12-26 22:39:00', 'mahdia', 6, 'distribution', 'uploads/events/693c8af0999cc.jpg', 'actif', '2025-12-12 21:36:48');

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

--
-- Déchargement des données de la table `inscriptions`
--

INSERT INTO `inscriptions` (`id_inscription`, `id_event`, `nom`, `prenom`, `email`, `telephone`, `pourquoi`, `avis`, `note`, `statut`, `date_inscription`) VALUES
(1, 1, 'Ben Ali', 'Mohamed', 'mohamed.benali@email.com', '20123456', 'Je souhaite participer à cette action car je trouve important d\'aider les personnes en difficulté, surtout pendant l\'hiver.', NULL, NULL, 'confirme', '2025-12-11 19:33:57'),
(2, 1, 'Trabelsi', 'Fatma', 'fatma.trabelsi@email.com', '25987654', 'J\'ai du temps libre et je veux le consacrer à des actions utiles pour la communauté. Cette cause me touche particulièrement.', NULL, NULL, 'en_attente', '2025-12-11 19:33:57'),
(3, 2, 'Gharbi', 'Ahmed', 'ahmed.gharbi@email.com', '98765432', 'Je dispose de nombreux vêtements en bon état que je souhaite donner et j\'aimerais aussi participer à l\'organisation de la collecte.', NULL, NULL, 'confirme', '2025-12-11 19:33:57'),
(4, 3, 'Mejri', 'Sonia', 'sonia.mejri@email.com', '54321098', 'En tant qu\'infirmière, je pense pouvoir être utile lors de cette journée de consultations gratuites. Je souhaite mettre mes compétences au service de ceux qui en ont besoin.', NULL, NULL, 'confirme', '2025-12-11 19:33:57'),
(8, 6, 'Fares', 'Guenenna', 'jean.dupont@test.com', '20023104', 'h uhiojoij iujoijiolo', NULL, NULL, 'en_attente', '2025-12-12 13:06:04'),
(12, 9, 'Fares', 'gn', 'guenennafares@gmail.com', '20023104', 'HDUIEZHNUI HJIOAZJHDUIOEZHJNF', NULL, NULL, 'en_attente', '2025-12-12 19:29:05'),
(13, 2, 'abdel', 'afbrhj', 'jean.dupont@test.com', '20023104', 'NDEKZJDIE?NFILERJCIOLJK LOJ3LCKRJ', NULL, NULL, 'en_attente', '2025-12-12 19:30:09'),
(18, 2, 'Fares', 'Guenenna', 'guenennafares@gmail.com', '20023104', 'jodkio jopjopfdkpjodjkopdpo ', NULL, NULL, 'en_attente', '2025-12-12 21:50:31');

--
-- Déclencheurs `inscriptions`
--
DELIMITER $$
CREATE TRIGGER `before_inscription_insert` BEFORE INSERT ON `inscriptions` FOR EACH ROW BEGIN
    DECLARE nb_places INT;
    DECLARE nb_inscriptions INT;
    
    -- Récupérer le nombre de places
    SELECT nombre_places INTO nb_places
    FROM events 
    WHERE id_event = NEW.id_event;
    
    -- Compter les inscriptions confirmées
    SELECT COUNT(*) INTO nb_inscriptions
    FROM inscriptions 
    WHERE id_event = NEW.id_event AND statut = 'confirme';
    
    -- Vérifier si complet
    IF nb_inscriptions >= nb_places THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Événement complet - Plus de places disponibles';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Structure de la table `notifications`
--

CREATE TABLE `notifications` (
  `id_notification` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `type_notification` varchar(50) NOT NULL,
  `id_event` int(11) DEFAULT NULL,
  `id_inscription` int(11) DEFAULT NULL,
  `statut_envoi` enum('envoye','echec','en_attente') DEFAULT 'en_attente',
  `date_envoi` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Historique des notifications par email';

-- --------------------------------------------------------

--
-- Doublure de structure pour la vue `v_events_statistiques`
-- (Voir ci-dessous la vue réelle)
--
CREATE TABLE `v_events_statistiques` (
`id_event` int(11)
,`titre` varchar(255)
,`description` text
,`date_event` datetime
,`lieu` varchar(255)
,`nombre_places` int(11)
,`type_event` enum('collecte','distribution','soutien','logement','autre')
,`image` varchar(255)
,`statut` enum('actif','supprime')
,`date_creation` timestamp
,`nb_inscriptions` bigint(21)
,`places_restantes` bigint(22)
,`etat_places` varchar(15)
);

-- --------------------------------------------------------

--
-- Doublure de structure pour la vue `v_inscriptions_details`
-- (Voir ci-dessous la vue réelle)
--
CREATE TABLE `v_inscriptions_details` (
`id_inscription` int(11)
,`id_event` int(11)
,`nom` varchar(100)
,`prenom` varchar(100)
,`email` varchar(255)
,`telephone` varchar(8)
,`pourquoi` text
,`avis` text
,`note` int(11)
,`statut` enum('en_attente','confirme','annule')
,`date_inscription` timestamp
,`event_titre` varchar(255)
,`date_event` datetime
,`event_lieu` varchar(255)
,`type_event` enum('collecte','distribution','soutien','logement','autre')
);

-- --------------------------------------------------------

--
-- Structure de la vue `v_events_statistiques`
--
DROP TABLE IF EXISTS `v_events_statistiques`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_events_statistiques`  AS SELECT `e`.`id_event` AS `id_event`, `e`.`titre` AS `titre`, `e`.`description` AS `description`, `e`.`date_event` AS `date_event`, `e`.`lieu` AS `lieu`, `e`.`nombre_places` AS `nombre_places`, `e`.`type_event` AS `type_event`, `e`.`image` AS `image`, `e`.`statut` AS `statut`, `e`.`date_creation` AS `date_creation`, count(`i`.`id_inscription`) AS `nb_inscriptions`, `e`.`nombre_places`- count(`i`.`id_inscription`) AS `places_restantes`, CASE WHEN count(`i`.`id_inscription`) >= `e`.`nombre_places` THEN 'Complet' WHEN count(`i`.`id_inscription`) >= `e`.`nombre_places` * 0.8 THEN 'Presque complet' ELSE 'Disponible' END AS `etat_places` FROM (`events` `e` left join `inscriptions` `i` on(`e`.`id_event` = `i`.`id_event` and `i`.`statut` = 'confirme')) WHERE `e`.`statut` = 'actif' GROUP BY `e`.`id_event` ;

-- --------------------------------------------------------

--
-- Structure de la vue `v_inscriptions_details`
--
DROP TABLE IF EXISTS `v_inscriptions_details`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_inscriptions_details`  AS SELECT `i`.`id_inscription` AS `id_inscription`, `i`.`id_event` AS `id_event`, `i`.`nom` AS `nom`, `i`.`prenom` AS `prenom`, `i`.`email` AS `email`, `i`.`telephone` AS `telephone`, `i`.`pourquoi` AS `pourquoi`, `i`.`avis` AS `avis`, `i`.`note` AS `note`, `i`.`statut` AS `statut`, `i`.`date_inscription` AS `date_inscription`, `e`.`titre` AS `event_titre`, `e`.`date_event` AS `date_event`, `e`.`lieu` AS `event_lieu`, `e`.`type_event` AS `type_event` FROM (`inscriptions` `i` join `events` `e` on(`i`.`id_event` = `e`.`id_event`)) ORDER BY `i`.`date_inscription` DESC ;

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
-- Index pour la table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id_notification`),
  ADD KEY `id_event` (`id_event`),
  ADD KEY `id_inscription` (`id_inscription`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_statut` (`statut_envoi`),
  ADD KEY `idx_type` (`type_notification`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `events`
--
ALTER TABLE `events`
  MODIFY `id_event` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT pour la table `inscriptions`
--
ALTER TABLE `inscriptions`
  MODIFY `id_inscription` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT pour la table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id_notification` int(11) NOT NULL AUTO_INCREMENT;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `inscriptions`
--
ALTER TABLE `inscriptions`
  ADD CONSTRAINT `inscriptions_ibfk_1` FOREIGN KEY (`id_event`) REFERENCES `events` (`id_event`) ON DELETE CASCADE;

--
-- Contraintes pour la table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`id_event`) REFERENCES `events` (`id_event`) ON DELETE SET NULL,
  ADD CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`id_inscription`) REFERENCES `inscriptions` (`id_inscription`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
