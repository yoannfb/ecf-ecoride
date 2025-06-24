-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3307
-- Généré le : jeu. 15 mai 2025 à 08:39
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12



/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `ecoride`
--

-- --------------------------------------------------------

--
-- Structure de la table `covoiturages`
--

DROP TABLE IF EXISTS `covoiturages`;
CREATE TABLE `covoiturages` (
  `id` int(11) NOT NULL,
  `chauffeur_id` int(11) NOT NULL,
  `ville_depart` varchar(100) DEFAULT NULL,
  `ville_arrivee` varchar(100) DEFAULT NULL,
  `date_depart` datetime DEFAULT NULL,
  `date_arrivee` datetime DEFAULT NULL,
  `prix` int(11) DEFAULT NULL,
  `places_disponibles` int(11) DEFAULT NULL,
  `est_ecolo` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `participations`
--

DROP TABLE IF EXISTS `participations`;
CREATE TABLE `participations` (
  `id` int(11) NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  `covoiturage_id` int(11) NOT NULL,
  `date_participation` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `participations`
--

INSERT INTO `participations` (`id`, `utilisateur_id`, `covoiturage_id`, `date_participation`) VALUES
(9, 2, 13, '2025-05-09 14:39:46');

-- --------------------------------------------------------

--
-- Structure de la table `trajets`
--

DROP TABLE IF EXISTS `trajets`;
CREATE TABLE `trajets` (
  `id` int(11) NOT NULL,
  `conducteur_id` int(11) NOT NULL,
  `vehicule_id` int(11) NOT NULL,
  `adresse_depart` varchar(255) NOT NULL,
  `adresse_arrivee` varchar(255) NOT NULL,
  `date_depart` datetime NOT NULL,
  `date_arrivee` datetime NOT NULL,
  `prix` decimal(6,2) NOT NULL,
  `places_disponibles` int(11) DEFAULT NULL,
  `statut` enum('à venir','en cours','terminé','annulé') DEFAULT 'à venir'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `trajets`
--

INSERT INTO `trajets` (`id`, `conducteur_id`, `vehicule_id`, `adresse_depart`, `adresse_arrivee`, `date_depart`, `date_arrivee`, `prix`, `places_disponibles`, `statut`) VALUES
(13, 3, 4, '1 rue de la mairie 74000 annecy', '10 Av. Simone Veil, 69150 Décines-Charpieu', '2025-05-15 14:00:00', '2025-05-15 16:00:00', 1.00, 2, 'à venir');

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

DROP TABLE IF EXISTS `utilisateurs`;
CREATE TABLE `utilisateurs` (
  `id` int(11) NOT NULL,
  `pseudo` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `credits` int(11) DEFAULT 20,
  `role` varchar(20) DEFAULT 'passager',
  `photo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id`, `pseudo`, `email`, `mot_de_passe`, `credits`, `role`, `photo`) VALUES
(1, 'JeanCovoit', 'jean@example.com', '$2y$10$7idN19AF5c2BOpFybR5Dg.2I.1U9b8zG9pQj1DZFZMLYu1IydvlCe\r\n', 20, 'passager', NULL),
(2, 'yoannfb', 'yoannfb@hotmail.com', '$2y$10$j5znnfpDXNZTtkkEpqzG6.ldqjV.uKhX8VLpVsexp2iLo3a0BjU8i', 48, 'passager', NULL),
(3, 'admin', 'admin@ecoride.fr', '$2y$10$ifE.66GnW1r/wr/dVISKze3xSVlj7Usq5OsncW7eeJBdCCWYErtHi', 22, 'chauffeur', 'profil_681879af5e8d0.jpg');

-- --------------------------------------------------------

--
-- Structure de la table `vehicules`
--

DROP TABLE IF EXISTS `vehicules`;
CREATE TABLE `vehicules` (
  `id` int(11) NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  `plaque` varchar(20) NOT NULL,
  `date_immat` date NOT NULL,
  `modele` varchar(50) NOT NULL,
  `marque` varchar(50) NOT NULL,
  `couleur` varchar(30) NOT NULL,
  `places` int(11) NOT NULL,
  `fumeur` tinyint(1) DEFAULT 0,
  `animaux` tinyint(1) DEFAULT 0,
  `preferences_perso` text DEFAULT NULL,
  `eco` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `vehicules`
--

INSERT INTO `vehicules` (`id`, `utilisateur_id`, `plaque`, `date_immat`, `modele`, `marque`, `couleur`, `places`, `fumeur`, `animaux`, `preferences_perso`, `eco`) VALUES
(1, 3, 'xx-000-xx', '2020-01-01', 'Twingo', 'Renault', '', 1, 0, 0, '', 0),
(4, 3, 'xx-001-xx', '2022-01-01', 'Kona', 'Hyundai', 'blanc', 3, 0, 0, 'je ne prend pas de bagages', 1);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `covoiturages`
--
ALTER TABLE `covoiturages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `chauffeur_id` (`chauffeur_id`);

--
-- Index pour la table `participations`
--
ALTER TABLE `participations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `utilisateur_id` (`utilisateur_id`),
  ADD KEY `covoiturage_id` (`covoiturage_id`);

--
-- Index pour la table `trajets`
--
ALTER TABLE `trajets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `conducteur_id` (`conducteur_id`),
  ADD KEY `vehicule_id` (`vehicule_id`);

--
-- Index pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Index pour la table `vehicules`
--
ALTER TABLE `vehicules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `utilisateur_id` (`utilisateur_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `covoiturages`
--
ALTER TABLE `covoiturages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `participations`
--
ALTER TABLE `participations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pour la table `trajets`
--
ALTER TABLE `trajets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `vehicules`
--
ALTER TABLE `vehicules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `covoiturages`
--
ALTER TABLE `covoiturages`
  ADD CONSTRAINT `covoiturages_ibfk_1` FOREIGN KEY (`chauffeur_id`) REFERENCES `utilisateurs` (`id`);

--
-- Contraintes pour la table `participations`
--
ALTER TABLE `participations`
  ADD CONSTRAINT `participations_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`),
  ADD CONSTRAINT `participations_ibfk_2` FOREIGN KEY (`covoiturage_id`) REFERENCES `trajets` (`id`);

--
-- Contraintes pour la table `trajets`
--
ALTER TABLE `trajets`
  ADD CONSTRAINT `trajets_ibfk_1` FOREIGN KEY (`conducteur_id`) REFERENCES `utilisateurs` (`id`),
  ADD CONSTRAINT `trajets_ibfk_2` FOREIGN KEY (`vehicule_id`) REFERENCES `vehicules` (`id`);

--
-- Contraintes pour la table `vehicules`
--
ALTER TABLE `vehicules`
  ADD CONSTRAINT `vehicules_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
