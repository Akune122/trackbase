-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : mer. 07 août 2024 à 19:35
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
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb3;

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
(10, 'Mango', '0000-00-00'),
(11, 'Théodort', '0000-00-00'),
(12, 'test', '0000-00-00'),
(13, 'Diam', '0000-00-00');

-- --------------------------------------------------------

--
-- Structure de la table `commentaires`
--

DROP TABLE IF EXISTS `commentaires`;
CREATE TABLE IF NOT EXISTS `commentaires` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_utilisateur` int NOT NULL,
  `id_musique` int DEFAULT NULL,
  `id_auteur` int DEFAULT NULL,
  `texte` text NOT NULL,
  `likes` int DEFAULT '0',
  `date_creation` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id_utilisateur` (`id_utilisateur`),
  KEY `id_musique` (`id_musique`),
  KEY `id_auteur` (`id_auteur`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `commentaires`
--

INSERT INTO `commentaires` (`id`, `id_utilisateur`, `id_musique`, `id_auteur`, `texte`, `likes`, `date_creation`) VALUES
(5, 4, 0, 1, 'j\'adore vraiment cet artiste', 0, '2024-06-07 08:46:18'),
(6, 6, 15, 3, 'Un remix sur ça existe ?', 0, '2024-06-07 08:49:10'),
(7, 7, 1, 2, 'une reprise ?', 0, '2024-06-07 13:46:22');

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
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb3;

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
(14, 'Mango', '0000-00-00'),
(15, 'Lowonstage', '0000-00-00'),
(16, 'test', '0000-00-00'),
(17, 'Omar Sy', '0000-00-00');

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
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb3;

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
(17, 'La rondine', '1990', 'Populaire Italienne', 10, 14, 12),
(18, 'Heureusement...', '2020', 'Pop', 11, 15, 0),
(19, 'test', '2000', 'test', 12, 16, 0),
(20, 'Peter Pan', '2009', 'French Rap', 13, 17, 0);

-- --------------------------------------------------------

--
-- Structure de la table `propositions`
--

DROP TABLE IF EXISTS `propositions`;
CREATE TABLE IF NOT EXISTS `propositions` (
  `id_proposition` int NOT NULL AUTO_INCREMENT,
  `id_utilisateur` int NOT NULL,
  `titre` varchar(250) DEFAULT NULL,
  `generation` varchar(250) DEFAULT NULL,
  `chanteur` varchar(250) DEFAULT NULL,
  `compositeur` varchar(250) DEFAULT NULL,
  `genre` varchar(250) DEFAULT NULL,
  `texte_proposition` text,
  `date_proposition` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `likes` int DEFAULT '0',
  PRIMARY KEY (`id_proposition`),
  KEY `id_utilisateur` (`id_utilisateur`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `propositions`
--

INSERT INTO `propositions` (`id_proposition`, `id_utilisateur`, `titre`, `generation`, `chanteur`, `compositeur`, `genre`, `texte_proposition`, `date_proposition`, `likes`) VALUES
(16, 1, 'Estiam', '2', '', '', '', '', '2024-06-07 13:48:40', 0),
(17, 1, 'GEÔLE', '2024', 'FEMTOGO', 'Vilhelm', 'French Rap', 'je n\'ai pas vu si elle y était déjà', '2024-07-05 12:03:12', 0);

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
  `date_creation_compte` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Date_naissance` varchar(250) NOT NULL,
  `photo_profil` varchar(255) DEFAULT NULL,
  `description` text,
  `administrateur` int DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `pseudo`, `email`, `mot_de_passe`, `date_creation_compte`, `Date_naissance`, `photo_profil`, `description`, `administrateur`) VALUES
(1, '122', 'lz.zaercher@gmail.com', '$2y$10$3A8JThJvUClIaz5aX7hzMeGLdYTsohxGqdPd.S8YaLdLTDES37afe', '2023-06-05 09:22:43', '', 'profile_pics/VrSRrRmaCZLpVJINSuww55FNNfDy6MKUR83pdSwT.jpg', 'gtfo', 1),
(4, 'Testeur', 'loiczaercher2@gmail.com', '$2y$10$PmShOiNH1gXNEWT.P8GsuOK4.DjEpfPNnlkF3qIWLVhbmDS1sSS26', '2024-06-05 09:22:43', '', 'profile_pics/big-steve-face.png', 'Description de test ', 0),
(5, 'testtest', 'test@gmail.test', '$2y$10$on4kQokv0Huo2azJhAwg/.wbiRyI0yMHPHGYruhLjIBPP/25Fl0tG', '2024-06-07 10:37:34', '2005-01-07', NULL, NULL, 0),
(6, 'Thomas_user', 'thomas.thomas@gmail.com', '$2y$10$m52X85fw.M4cx.L5x.PzXuX8PgopScOpdD1szHylFOOH3RhcxC4Ii', '2024-06-07 10:47:27', '2005-01-22', NULL, NULL, 0),
(7, 'estiam', 'estiam@estiam.estiam', '$2y$10$OkrY4Nm02z5oAhts2s7z2eY4Gabt72uSwP1CvWc26rjI427Bj1/wO', '2024-06-07 15:33:39', '2005-05-07', 'profile_pics/Eren.Jaeger.full.3748985.jpg', 'metzcampus', 0),
(8, 'test1234', 'loiczaercher6@gmail.com', '$2y$10$kfC2MdsFLYA98nNEX50EvuGy2maxupVgciODWOwGGeIGLvRizM6bW', '2024-07-05 13:56:04', '2000-11-11', NULL, NULL, 0);

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
