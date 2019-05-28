/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
SET NAMES utf8mb4;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table kraj
# ------------------------------------------------------------

CREATE TABLE `kraj` (
  `krajId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nuts3` varchar(5) NOT NULL DEFAULT '',
  `nazev` varchar(255) NOT NULL DEFAULT '',
  `okrsky` int(11) unsigned NOT NULL,
  `zpracovano` int(11) unsigned NOT NULL,
  `pocetVolicu` int(11) unsigned NOT NULL,
  `vydaneObalky` int(11) unsigned NOT NULL,
  `ucast` decimal(5,2) unsigned NOT NULL,
  `odevzdaneObalky` int(11) unsigned NOT NULL,
  `platneHlasy` int(11) unsigned NOT NULL,
  PRIMARY KEY (`krajId`),
  UNIQUE KEY `nuts3` (`nuts3`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table krajVysledky
# ------------------------------------------------------------

CREATE TABLE `krajVysledky` (
  `krajVysledkyId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `krajId` int(11) unsigned NOT NULL,
  `stranaId` int(10) unsigned NOT NULL,
  `hlasy` int(11) unsigned NOT NULL,
  `hlasyPct` decimal(5,2) unsigned NOT NULL,
  PRIMARY KEY (`krajVysledkyId`),
  UNIQUE KEY `krajId_stranaId` (`krajId`,`stranaId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table obec
# ------------------------------------------------------------

CREATE TABLE `obec` (
  `obecId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `okresId` int(11) DEFAULT NULL,
  `nuts5` varchar(6) NOT NULL DEFAULT '',
  `nazev` varchar(255) NOT NULL DEFAULT '',
  `okrsky` int(11) unsigned NOT NULL,
  `zpracovano` int(11) unsigned NOT NULL,
  `pocetVolicu` int(11) unsigned NOT NULL,
  `vydaneObalky` int(11) unsigned NOT NULL,
  `ucast` decimal(5,2) unsigned NOT NULL,
  `odevzdaneObalky` int(11) unsigned NOT NULL,
  `platneHlasy` int(11) unsigned NOT NULL,
  PRIMARY KEY (`obecId`),
  UNIQUE KEY `nuts5` (`nuts5`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table obecVysledky
# ------------------------------------------------------------

CREATE TABLE `obecVysledky` (
  `obecVysledkyId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `obecId` int(11) unsigned NOT NULL,
  `stranaId` int(10) unsigned NOT NULL,
  `hlasy` int(11) unsigned NOT NULL,
  `hlasyPct` decimal(5,2) unsigned NOT NULL,
  PRIMARY KEY (`obecVysledkyId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table okres
# ------------------------------------------------------------

CREATE TABLE `okres` (
  `okresId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `krajId` int(11) DEFAULT NULL,
  `nuts4` varchar(6) NOT NULL DEFAULT '',
  `nazev` varchar(255) NOT NULL DEFAULT '',
  `okrsky` int(11) unsigned NOT NULL,
  `zpracovano` int(11) unsigned NOT NULL,
  `pocetVolicu` int(11) unsigned NOT NULL,
  `vydaneObalky` int(11) unsigned NOT NULL,
  `ucast` decimal(5,2) unsigned NOT NULL,
  `odevzdaneObalky` int(11) unsigned NOT NULL,
  `platneHlasy` int(11) unsigned NOT NULL,
  PRIMARY KEY (`okresId`),
  UNIQUE KEY `nuts4` (`nuts4`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table okresVysledky
# ------------------------------------------------------------

CREATE TABLE `okresVysledky` (
  `okresVysledkyId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `okresId` int(11) unsigned NOT NULL,
  `stranaId` int(10) unsigned NOT NULL,
  `hlasy` int(11) unsigned NOT NULL,
  `hlasyPct` decimal(5,2) unsigned NOT NULL,
  PRIMARY KEY (`okresVysledkyId`),
  UNIQUE KEY `okresId_stranaId` (`okresId`,`stranaId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table strana
# ------------------------------------------------------------

CREATE TABLE `strana` (
  `stranaId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `kodCSU` int(11) unsigned NOT NULL,
  `nazev` varchar(255) NOT NULL DEFAULT '',
  `zkratka` varchar(255) NOT NULL DEFAULT '',
  `plnyNazev` text NOT NULL,
  `pocetStranVKoalici` int(11) unsigned NOT NULL,
  `pocetMandatu` int(11) unsigned NOT NULL,
  `slozeniStranKodyCSU` json NOT NULL,
  PRIMARY KEY (`stranaId`),
  UNIQUE KEY `kodCSU` (`kodCSU`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
