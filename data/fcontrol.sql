# MySQL-Front 5.0  (Build 1.0)

/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE */;
/*!40101 SET SQL_MODE='' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES */;
/*!40103 SET SQL_NOTES='ON' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS */;
/*!40014 SET FOREIGN_KEY_CHECKS=0 */;


# Host: localhost    Database: zf2tutorial
# ------------------------------------------------------
# Server version 5.5.25

DROP DATABASE IF EXISTS `zf2tutorial`;
CREATE DATABASE `zf2tutorial` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `zf2tutorial`;

#
# Table structure for table album
#

CREATE TABLE `album` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `artist` varchar(100) NOT NULL,
  `title` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;
INSERT INTO `album` VALUES (2,'привет234','медвед234');
INSERT INTO `album` VALUES (3,'привет пок','пока');
INSERT INTO `album` VALUES (4,'test','test');
INSERT INTO `album` VALUES (6,'sdf','sdfs');
INSERT INTO `album` VALUES (7,'gh','fgh');
INSERT INTO `album` VALUES (8,'тест','тестик2');
INSERT INTO `album` VALUES (10,'fu','fru');
/*!40000 ALTER TABLE `album` ENABLE KEYS */;
UNLOCK TABLES;

#
# Table structure for table library_airport
#

CREATE TABLE `library_airport` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `country` int(10) DEFAULT NULL,
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT 'name of airport',
  `short_name` varchar(30) NOT NULL DEFAULT '',
  `code_icao` varchar(4) NOT NULL DEFAULT '',
  `code_iata` varchar(3) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40000 ALTER TABLE `library_airport` ENABLE KEYS */;
UNLOCK TABLES;

#
# Table structure for table library_country
#

CREATE TABLE `library_country` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `region` int(10) DEFAULT NULL,
  `name` varchar(30) DEFAULT NULL,
  `code` varchar(3) DEFAULT NULL COMMENT 'code of country',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `code` (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40000 ALTER TABLE `library_country` ENABLE KEYS */;
UNLOCK TABLES;

#
# Table structure for table library_region
#

CREATE TABLE `library_region` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT 'Region of the world',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40000 ALTER TABLE `library_region` ENABLE KEYS */;
UNLOCK TABLES;

#
# Table structure for table user
#

CREATE TABLE `user` (
  `user_id` int(10) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `display_name` varchar(50) DEFAULT NULL,
  `password` varchar(128) NOT NULL,
  `state` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8;
INSERT INTO `user` VALUES (1,'ОТМОРОЖЕННЫЙ МАМОНТ','ruslan.piskarev@gmail.com',NULL,'$2y$14$OaNgN6Zlnw/NDmVEkJdKlOzQxvGiJNBMfYfuwP7/c1Httjnu5TnJ6',1);
INSERT INTO `user` VALUES (53,'Крамаренко Владислав','vlad@examle.com',NULL,'$2y$14$yFW7Of3yRT5NVTK8sWaMhONRQZtdRBWoftay2SnzP.qZPmUvWgxj.',1);
INSERT INTO `user` VALUES (54,'test test','sdfsdfsd@sdfgsdf.com',NULL,'$2y$14$7lLQU/oDoReGOK1a/0Ou4Oq5hPo8VjpWhuxep4R6WIPqPGscg.4WW',1);
INSERT INTO `user` VALUES (56,'456456456346532456','456465@ertert.com',NULL,'$2y$14$Wv1rgyacLixWKjeb9o2PNeeEkVBJf1cMlYBltaR3N4NbsQbSKCIne',1);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

#
# Table structure for table user_role
#

CREATE TABLE `user_role` (
  `role_id` varchar(255) NOT NULL,
  `default` tinyint(1) NOT NULL,
  `parent` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
INSERT INTO `user_role` VALUES ('admin',0,'user');
INSERT INTO `user_role` VALUES ('guest',1,NULL);
INSERT INTO `user_role` VALUES ('user',0,NULL);
/*!40000 ALTER TABLE `user_role` ENABLE KEYS */;
UNLOCK TABLES;

#
# Table structure for table user_role_linker
#

CREATE TABLE `user_role_linker` (
  `user_id` int(11) unsigned NOT NULL,
  `role_id` varchar(255) NOT NULL,
  PRIMARY KEY (`user_id`,`role_id`),
  KEY `role_id` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
INSERT INTO `user_role_linker` VALUES (1,'admin');
INSERT INTO `user_role_linker` VALUES (52,'admin');
INSERT INTO `user_role_linker` VALUES (53,'admin');
INSERT INTO `user_role_linker` VALUES (54,'user');
INSERT INTO `user_role_linker` VALUES (56,'manager');
/*!40000 ALTER TABLE `user_role_linker` ENABLE KEYS */;
UNLOCK TABLES;

/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
