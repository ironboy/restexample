# ************************************************************
# Sequel Pro SQL dump
# Version 4096
#
# http://www.sequelpro.com/
# http://code.google.com/p/sequel-pro/
#
# Värd: 127.0.0.1 (MySQL 5.6.17)
# Databas: catshow
# Genereringstid: 2014-06-12 14:23:57 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Tabelldump catowners
# ------------------------------------------------------------

DROP TABLE IF EXISTS `catowners`;

CREATE TABLE `catowners` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

LOCK TABLES `catowners` WRITE;
/*!40000 ALTER TABLE `catowners` DISABLE KEYS */;

INSERT INTO `catowners` (`id`, `name`)
VALUES
	(1,'Thomas'),
	(2,'Conny'),
	(3,'Andreas');

/*!40000 ALTER TABLE `catowners` ENABLE KEYS */;
UNLOCK TABLES;


# Tabelldump cats
# ------------------------------------------------------------

DROP TABLE IF EXISTS `cats`;

CREATE TABLE `cats` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `weight` int(11) DEFAULT NULL,
  `gender` varchar(11) DEFAULT NULL,
  `owner_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

LOCK TABLES `cats` WRITE;
/*!40000 ALTER TABLE `cats` DISABLE KEYS */;

INSERT INTO `cats` (`id`, `name`, `age`, `weight`, `gender`, `owner_id`)
VALUES
	(1,'Gustav',15,10,'male',1),
	(2,'Smulan',7,8,'female',1),
	(3,'Billie',4,3,'female',3),
	(4,'Moxie',8,7,'female',2);

/*!40000 ALTER TABLE `cats` ENABLE KEYS */;
UNLOCK TABLES;


# Tabelldump cats_with_owners
# ------------------------------------------------------------

DROP VIEW IF EXISTS `cats_with_owners`;

CREATE TABLE `cats_with_owners` (
   `cat_id` INT(11) UNSIGNED NOT NULL DEFAULT '0',
   `catname` VARCHAR(255) NULL DEFAULT NULL,
   `cat_age` INT(11) NULL DEFAULT NULL,
   `cat_weight` INT(11) NULL DEFAULT NULL,
   `owner_id` INT(11) UNSIGNED NOT NULL DEFAULT '0',
   `owner_name` VARCHAR(255) NULL DEFAULT NULL
) ENGINE=MyISAM;





# Replace placeholder table for cats_with_owners with correct view syntax
# ------------------------------------------------------------

DROP TABLE `cats_with_owners`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `cats_with_owners`
AS SELECT
   `cats`.`id` AS `cat_id`,
   `cats`.`name` AS `catname`,
   `cats`.`age` AS `cat_age`,
   `cats`.`weight` AS `cat_weight`,
   `catowners`.`id` AS `owner_id`,
   `catowners`.`name` AS `owner_name`
FROM (`catowners` join `cats`) where (`catowners`.`id` = `cats`.`owner_id`);

/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
