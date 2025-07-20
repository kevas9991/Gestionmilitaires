-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : dim. 20 juil. 2025 à 00:53
-- Version du serveur : 9.1.0
-- Version de PHP : 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `gdm_db`
--

-- --------------------------------------------------------

--
-- Structure de la table `admins`
--

DROP TABLE IF EXISTS `admins`;
CREATE TABLE IF NOT EXISTS `admins` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`) VALUES
(3, 'Kevas', '$2y$10$ag6xzDi47cY4k1.QBF98yORAjBaabk0DiHl0SandDUf.JAHldpMXO'),
(4, 'flo', '$2y$10$bwWEYzWr2PUBI2fkjSEFCOgeJC98gxsPAOjs7p2wUohZG78LAT.pW');

-- --------------------------------------------------------

--
-- Structure de la table `militaires`
--

DROP TABLE IF EXISTS `militaires`;
CREATE TABLE IF NOT EXISTS `militaires` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) DEFAULT NULL,
  `matricule` varchar(50) NOT NULL,
  `sexe` varchar(10) DEFAULT NULL,
  `etat_civil` varchar(20) DEFAULT NULL,
  `date_naissance` date DEFAULT NULL,
  `lieu_naissance` varchar(100) DEFAULT NULL,
  `nationalite` varchar(50) DEFAULT NULL,
  `grade` varchar(50) NOT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `date_enrolement` date DEFAULT NULL,
  `unite` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `militaires`
--

INSERT INTO `militaires` (`id`, `nom`, `prenom`, `matricule`, `sexe`, `etat_civil`, `date_naissance`, `lieu_naissance`, `nationalite`, `grade`, `telephone`, `email`, `photo`, `date_enrolement`, `unite`) VALUES
(3, 'Lukwitshi Fatuma', 'Florence', 'MIL202507196593', 'Femme', 'Célibataire', '2003-06-10', 'Lushi Rdc', 'Congolaise', 'Sergent', '1234567888', 'flo@gmail.com', 'uploads/687c1a1a8eb0f_femme-soldat-png-se-tient-fierement-debout-vetements-militaires-pour-adultes_53876-796963.webp', '2025-07-20', NULL),
(6, 'Moke Tiba', 'Youni', 'MIL202507193386', 'Femme', 'Divorcé(e)', '2002-02-14', 'Kipushi Rdc', 'Congolaise', 'Lieutenant', '1234567899', 'youni@gmail.com', 'uploads/687c246b6a817_femme-afro-américaine-soldier-series-contre-fond-marron-foncé.webp', '2024-02-14', 'Marine');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
