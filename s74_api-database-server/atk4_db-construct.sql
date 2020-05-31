-- phpMyAdmin SQL Dump
-- version 4.6.6deb4
-- https://www.phpmyadmin.net/
--
-- Client :  localhost:3306
-- Généré le :  Mer 27 Mai 2020 à 19:33
-- Version du serveur :  10.1.44-MariaDB-0+deb9u1
-- Version de PHP :  7.0.33-0+deb9u7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `atk4_test`
--

-- --------------------------------------------------------

--
-- Structure de la table `Box`
--

CREATE TABLE `Box` (
  `id` int(11) NOT NULL,
  `boxID` int(6) NOT NULL,
  `datetime` datetime DEFAULT NULL,
  `temperature` float(4,1) DEFAULT NULL,
  `humidity` float(4,1) DEFAULT NULL,
  `pressure` float(6,2) DEFAULT NULL,
  `gas` float(6,2) DEFAULT NULL,
  `PM2` float(6,4) DEFAULT NULL,
  `PM10` float(6,4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Contenu de la table `Box`
--

INSERT INTO `Box` (`id`, `boxID`, `datetime`, `temperature`, `humidity`, `pressure`, `gas`, `PM2`, `PM10`) VALUES
(1, 0, '2020-05-27 19:29:53', 0.0, 0.0, 0.00, 0.00, 0.0000, 0.0000);
(2, 0, '2020-05-28 03:17:54', 0.0, 0.0, 0.00, 0.00, 2.5000, 5.7000);
(3, 0, '2020-05-28 03:22:34', 25.2, 40.1, 1020.94, 9999.99, 0.0000, 0.0000);
(4, 0, '2020-05-28 03:28:26', 0.0, 0.0, 0.00, 0.00, 2.3000, 4.3000);
(5, 0, '2020-05-28 03:32:34', 25.1, 40.2, 1020.86, 9999.99, 0.0000, 0.0000);

-- --------------------------------------------------------

--
-- Structure de la table `log`
--

CREATE TABLE `log` (
  `id` int(11) NOT NULL,
  `txt` longtext NOT NULL,
  `datee` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Contenu de la table `log`
--

INSERT INTO `log` (`id`, `txt`, `datee`) VALUES
(1, 'a:3:{s:4:\"pm10\";s:3:\"2.8\";s:4:\"pm25\";s:3:\"1.3\";s:8:\"datetime\";s:19:\"2020-05-27 19:29:53\";}', '2020-05-27 19:29:53');

-- --------------------------------------------------------

--
-- Structure de la table `test-client`
--

CREATE TABLE `test-client` (
  `id` int(11) NOT NULL,
  `full_name` varchar(200) NOT NULL,
  `company` varchar(200) NOT NULL,
  `added` date NOT NULL,
  `balance` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `email` varchar(50) NOT NULL DEFAULT '',
  `password` text NOT NULL,
  `name` varchar(32) DEFAULT NULL,
  `firstname` varchar(32) NOT NULL DEFAULT '',
  `address` varchar(32) DEFAULT NULL,
  `phone` varchar(12) DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `dateRegistered` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `device` tinyint(1) DEFAULT '0',
  `deviceOutdoor` tinyint(1) DEFAULT '0',
  `sensors` enum('0','BMP280','BME280','BME680','SDS011') DEFAULT '0',
  `is_admin` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Contenu de la table `user`
--

INSERT INTO `user` (`id`, `email`, `password`, `name`, `firstname`, `address`, `phone`, `birthdate`, `dateRegistered`, `device`, `deviceOutdoor`, `sensors`, `is_admin`) VALUES
(1, 'info@cwb.ovh', 'ciney', 'denis', '', NULL, NULL, '2019-10-31', '0000-00-00 00:00:00', 0, 0, '0', 0),
(2, 'denis@cyber-web.be', '$2y$10$wPk7UuG.veCB0UGDjWkOrOBjnxTr7t/wfOmjmv/9rMnLOCHItWG8S', NULL, '', NULL, NULL, '2019-10-31', '0000-00-00 00:00:00', 0, 0, '0', NULL),
(3, 'test@cwb.ovh', '$2y$10$g6GXj9l/8tthe6gvn0r6j.XJski28aEKrBsH/1dcJbaj.8r75YhGO', NULL, '', NULL, NULL, '2019-10-31', '0000-00-00 00:00:00', 0, 0, '0', NULL),
(5, 'm.camposcasares@students.ephec.be', '$2y$10$7BB/wvgK0/UVAfln57WRJOM8MgMLXLqu1wHLZaEKUgnOw..afkz7m', 'Campos Casares', 'Melvin', NULL, NULL, '1995-11-11', '2020-05-24 15:43:02', 0, 0, '0', 1),
(6, 'denis@cwb.ovh', '$2y$10$qBDqtp1UVxIrv3iwCNdpqO3Y/naFNOFvzEXNqVrcKZIq1oLPXsWHe', NULL, '', NULL, NULL, NULL, '2020-05-25 18:14:52', 0, 0, '0', NULL);

--
-- Index pour les tables exportées
--

--
-- Index pour la table `Box`
--
ALTER TABLE `Box`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Index pour la table `log`
--
ALTER TABLE `log`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `Box`
--
ALTER TABLE `Box`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1057;
--
-- AUTO_INCREMENT pour la table `log`
--
ALTER TABLE `log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1057;
--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
