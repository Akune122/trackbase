-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : sam. 18 mai 2024 à 15:39
-- Version du serveur : 8.0.31
-- Version de PHP : 8.0.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `trackbase_alpha2`
--

-- --------------------------------------------------------

--
-- Structure de la table `chanteur`
--

DROP TABLE IF EXISTS `chanteur`;
CREATE TABLE IF NOT EXISTS `chanteur` (
  `id_chanteur` int NOT NULL AUTO_INCREMENT,
  `nom_chanteur` varchar(250) NOT NULL,
  `date_naissance` date NOT NULL,
  PRIMARY KEY (`id_chanteur`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb3;

--
-- Déchargement des données de la table `chanteur`
--

INSERT INTO `chanteur` (`id_chanteur`, `nom_chanteur`, `date_naissance`) VALUES
(1, 'Micheal Jackson', '1958-08-29'),
(2, 'France Gall', '1947-10-09'),
(3, 'Orelsan', '1982-08-01'),
(4, 'Snoop Dogg', '1971-10-20'),
(5, 'Stromae', '1985-03-12'),
(6, 'Dalida', '1933-01-17'),
(8, 'Nekfeu', '0000-00-00'),
(9, 'Khali', '0000-00-00'),
(10, 'Mango', '0000-00-00');

-- --------------------------------------------------------

--
-- Structure de la table `compositeur`
--

DROP TABLE IF EXISTS `compositeur`;
CREATE TABLE IF NOT EXISTS `compositeur` (
  `id_compo` int NOT NULL AUTO_INCREMENT,
  `nom_compo` varchar(250) NOT NULL,
  `date_naissance` date NOT NULL,
  PRIMARY KEY (`id_compo`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb3;

--
-- Déchargement des données de la table `compositeur`
--

INSERT INTO `compositeur` (`id_compo`, `nom_compo`, `date_naissance`) VALUES
(1, 'Micheal Jackson', '1958-08-29'),
(2, 'Serge Gainsbourg', '1928-04-02'),
(3, 'Orelsan', '1982-08-01'),
(4, 'Frederic Savio', '1970-05-19'),
(5, 'Calvin Broadus', '1971-10-20'),
(6, 'Jamarr Stamps', '1975-11-27'),
(7, 'Stromae', '1985-03-12'),
(8, 'Sayed Darwich', '1908-03-17'),
(9, 'Toto Cutugno', '1943-07-07'),
(10, 'Cristiano Minellono', '1947-12-12'),
(12, 'Benjamin Seletti', '0000-00-00'),
(13, 'PREZZY', '0000-00-00'),
(14, 'Mango', '0000-00-00');

-- --------------------------------------------------------

--
-- Structure de la table `musique`
--

DROP TABLE IF EXISTS `musique`;
CREATE TABLE IF NOT EXISTS `musique` (
  `id_Musique` int NOT NULL AUTO_INCREMENT,
  `titre_musique` varchar(250) NOT NULL,
  `generation_musique` varchar(30) NOT NULL,
  `genre_musique` varchar(250) NOT NULL,
  `id_chanteur` int NOT NULL,
  `id_compo` int NOT NULL,
  `num_paroles` int NOT NULL,
  PRIMARY KEY (`id_Musique`),
  KEY `id_chanteur` (`id_chanteur`,`id_compo`),
  KEY `id_compo` (`id_compo`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb3;

--
-- Déchargement des données de la table `musique`
--

INSERT INTO `musique` (`id_Musique`, `titre_musique`, `generation_musique`, `genre_musique`, `id_chanteur`, `id_compo`, `num_paroles`) VALUES
(1, 'Bad', '80s', 'Pop/dance-pop/rock/funk/r&b', 1, 1, 0),
(2, 'Smooth Criminal', '80s', 'dance-pop', 1, 1, 0),
(3, 'leave me alone', '80s', 'Funk', 1, 1, 0),
(4, 'Black or white', '90s', 'Pop rock/ dance-pop/hard rock/rap', 1, 1, 0),
(5, 'Poupée de cire, Poupée de son', '60s', 'Pop', 2, 2, 0),
(6, 'La Terre est ronde', '2010s', 'Rap', 3, 3, 0),
(7, 'Wrong Idea', '90s', 'West Coast hip hop / Gangsta rap / G-funk', 4, 5, 0),
(8, 'Papautai', '2010s', 'Hip-hop/electro house', 5, 7, 0),
(9, 'Salma ya Salama', '70s', 'Egyptian folk', 6, 8, 0),
(10, 'Laissez moi danser(Monday, Tuesday)', '70s', 'Disco pop', 6, 9, 0),
(15, 'On verra', '2010', 'French Rap', 8, 12, 1),
(16, 'RIEN NE VA ME SUFFIRE', '2020', 'French Rap', 9, 13, 2),
(17, 'La rondine', '1990', 'Populaire Italienne', 10, 14, 12);

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `pseudo` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `administrateur` int DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `pseudo`, `email`, `mot_de_passe`, `administrateur`) VALUES
(1, '122', 'loiczaercher1@gmail.com', '$2y$10$HZ3c14LvYpkJM0bBH04mG.Hw0bxLoOhrggvsH05z/zVKl2aWGwrm2', 1);

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `musique`
--
ALTER TABLE `musique`
  ADD CONSTRAINT `id_chanteur` FOREIGN KEY (`id_chanteur`) REFERENCES `chanteur` (`id_chanteur`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `id_compo` FOREIGN KEY (`id_compo`) REFERENCES `compositeur` (`id_compo`) ON DELETE RESTRICT ON UPDATE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
