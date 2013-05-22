# MySQL-Front 5.0  (Build 1.0)

/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE */;
/*!40101 SET SQL_MODE='' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES */;
/*!40103 SET SQL_NOTES='ON' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS */;
/*!40014 SET FOREIGN_KEY_CHECKS=0 */;


# Host: localhost    Database: fcontrol
# ------------------------------------------------------
# Server version 5.5.30-log

DROP DATABASE IF EXISTS `fcontrol`;
CREATE DATABASE `fcontrol` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `fcontrol`;

#
# Table structure for table album
#

CREATE TABLE `album` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `artist` varchar(100) NOT NULL,
  `title` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;
INSERT INTO `album` VALUES (2,'привет234','медвед234');
INSERT INTO `album` VALUES (3,'привет пок','пока');
INSERT INTO `album` VALUES (4,'test','test');
INSERT INTO `album` VALUES (6,'sdf','sdfs');
INSERT INTO `album` VALUES (7,'gh','fgh');
INSERT INTO `album` VALUES (8,'тест','тестик2');
INSERT INTO `album` VALUES (10,'fu','fru');
INSERT INTO `album` VALUES (13,'szdfsdf','fdsf');
INSERT INTO `album` VALUES (14,'fdsgdsgh','xfg');
/*!40000 ALTER TABLE `album` ENABLE KEYS */;
UNLOCK TABLES;

#
# Table structure for table flightBaseForm
#

CREATE TABLE `flightBaseForm` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `refNumberOrder` varchar(255) NOT NULL DEFAULT '',
  `dateOrder` int(11) DEFAULT NULL,
  `kontragent` int(11) DEFAULT NULL,
  `airOperator` int(11) DEFAULT NULL,
  `aircraft` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `refNumberOrder` (`refNumberOrder`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;
INSERT INTO `flightBaseForm` VALUES (7,'7ORD-20130522',2013,2,3,'123');
INSERT INTO `flightBaseForm` VALUES (8,'ORD-2013052248/1',2013,2,1,'123');
INSERT INTO `flightBaseForm` VALUES (9,'ORD-2013052204/1',2013,3,4,'12345');
INSERT INTO `flightBaseForm` VALUES (10,'ORD-2013052207/1',2013,3,4,'2132132456');
INSERT INTO `flightBaseForm` VALUES (11,'11ORD-2013052234/1',2013,2,4,'2132132456');
INSERT INTO `flightBaseForm` VALUES (12,'ORD-2013052243/1',2013,2,1,'123');
/*!40000 ALTER TABLE `flightBaseForm` ENABLE KEYS */;
UNLOCK TABLES;

#
# Table structure for table library_air_operator
#

CREATE TABLE `library_air_operator` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) DEFAULT NULL,
  `short_name` varchar(15) DEFAULT NULL,
  `code_icao` varchar(3) NOT NULL DEFAULT '',
  `code_iata` varchar(2) NOT NULL DEFAULT '',
  `country` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `shot_name` (`short_name`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
INSERT INTO `library_air_operator` VALUES (1,'кпыуп','кпфыукпу','1ку','2у',11);
INSERT INTO `library_air_operator` VALUES (3,'вапвап','апвап','вап','',12);
INSERT INTO `library_air_operator` VALUES (4,'укецфе','кекуе','куе','ке',12);
/*!40000 ALTER TABLE `library_air_operator` ENABLE KEYS */;
UNLOCK TABLES;

#
# Table structure for table library_aircraft
#

CREATE TABLE `library_aircraft` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `aircraft_type` int(10) DEFAULT NULL,
  `reg_number` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `reg_number` (`reg_number`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
INSERT INTO `library_aircraft` VALUES (2,4,'222');
INSERT INTO `library_aircraft` VALUES (3,2,'1000000000');
INSERT INTO `library_aircraft` VALUES (4,7,'123');
INSERT INTO `library_aircraft` VALUES (5,10,'12345');
INSERT INTO `library_aircraft` VALUES (6,2,'2132132456');
/*!40000 ALTER TABLE `library_aircraft` ENABLE KEYS */;
UNLOCK TABLES;

#
# Table structure for table library_aircraft_type
#

CREATE TABLE `library_aircraft_type` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(10) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
INSERT INTO `library_aircraft_type` VALUES (2,'szgsdgsdfd');
INSERT INTO `library_aircraft_type` VALUES (7,'vxzcvxc');
INSERT INTO `library_aircraft_type` VALUES (8,'xcvxcvv');
INSERT INTO `library_aircraft_type` VALUES (10,'ваВа');
/*!40000 ALTER TABLE `library_aircraft_type` ENABLE KEYS */;
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
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;
INSERT INTO `library_airport` VALUES (4,10,'223333333333333','23333333333333333333','1234','234');
INSERT INTO `library_airport` VALUES (5,10,'2345235234','235421354','2355','523');
INSERT INTO `library_airport` VALUES (6,2,'234','21342134','3242','23q');
INSERT INTO `library_airport` VALUES (7,2,'235432qq5','2352','5443','234');
INSERT INTO `library_airport` VALUES (9,2,'twetrawer6e57','tawetwaet','twer','ert');
INSERT INTO `library_airport` VALUES (10,11,'Борисполь','МАО','0100','123');
INSERT INTO `library_airport` VALUES (12,11,'Жуляны','МАО2','0101','111');
INSERT INTO `library_airport` VALUES (13,0,'sdfgsd','sdg','sdfz','szd');
INSERT INTO `library_airport` VALUES (14,10,'rszg','szdgzsdg','SDGS','SGd');
INSERT INTO `library_airport` VALUES (15,12,'aewtrawerf','werwaer','wear','wae');
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
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;
INSERT INTO `library_country` VALUES (1,1,'test','aa');
INSERT INTO `library_country` VALUES (2,2,'Россия','RU');
INSERT INTO `library_country` VALUES (3,3,'2232','222');
INSERT INTO `library_country` VALUES (4,1,'4352345','345');
INSERT INTO `library_country` VALUES (5,1,'1112','112');
INSERT INTO `library_country` VALUES (6,1,'екнкн','tyr');
INSERT INTO `library_country` VALUES (7,1,'365456','447');
INSERT INTO `library_country` VALUES (8,1,'ryt','ry');
INSERT INTO `library_country` VALUES (11,66,'Украина','UA');
INSERT INTO `library_country` VALUES (12,6,'уФУАУ','sdf');
INSERT INTO `library_country` VALUES (13,17,'sefAWEfAE','ASF');
INSERT INTO `library_country` VALUES (14,5,'sfsafSDAf','SZD');
INSERT INTO `library_country` VALUES (15,5,'asdsd','aSd');
INSERT INTO `library_country` VALUES (16,13,'ASDDaSd','354');
INSERT INTO `library_country` VALUES (17,4,'sytysty','trs');
INSERT INTO `library_country` VALUES (18,6,'вавыфав','das');
/*!40000 ALTER TABLE `library_country` ENABLE KEYS */;
UNLOCK TABLES;

#
# Table structure for table library_currency
#

CREATE TABLE `library_currency` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) DEFAULT NULL,
  `currency` varchar(3) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `currency` (`name`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
INSERT INTO `library_currency` VALUES (2,'sds111','444');
INSERT INTO `library_currency` VALUES (3,'1112','122');
INSERT INTO `library_currency` VALUES (4,'sadas','ыва');
INSERT INTO `library_currency` VALUES (5,'фывафыаываыва','ваы');
/*!40000 ALTER TABLE `library_currency` ENABLE KEYS */;
UNLOCK TABLES;

#
# Table structure for table library_fixed_service_type
#

CREATE TABLE `library_fixed_service_type` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40000 ALTER TABLE `library_fixed_service_type` ENABLE KEYS */;
UNLOCK TABLES;

#
# Table structure for table library_kontragent
#

CREATE TABLE `library_kontragent` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) DEFAULT NULL,
  `short_name` varchar(15) DEFAULT NULL,
  `address` text,
  `phone1` varchar(30) DEFAULT NULL,
  `phone2` varchar(30) DEFAULT NULL,
  `phone3` varchar(30) DEFAULT NULL,
  `fax` varchar(30) DEFAULT NULL,
  `mail` varchar(30) DEFAULT NULL,
  `sita` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `short_name` (`short_name`),
  UNIQUE KEY `mail` (`mail`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
INSERT INTO `library_kontragent` VALUES (2,'fasdffgs','1115465','ertrtsdfsa','ertewrtsdfsaf','ertewrtsadf','eretr','ertewrtsdf','sdferter@dsfsdf.com','er');
INSERT INTO `library_kontragent` VALUES (3,'qwewq','qwewqe','rqer\r\nweqe','wqeqwe','qweqwe','qwee','wqeqw','wqeqw@sdafedf.com','wewe');
/*!40000 ALTER TABLE `library_kontragent` ENABLE KEYS */;
UNLOCK TABLES;

#
# Table structure for table library_region
#

CREATE TABLE `library_region` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT 'Region of the world',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=123 DEFAULT CHARSET=utf8;
INSERT INTO `library_region` VALUES (2,'444');
INSERT INTO `library_region` VALUES (4,'777');
INSERT INTO `library_region` VALUES (5,'tyuyutu');
INSERT INTO `library_region` VALUES (6,'sdfgsdg');
INSERT INTO `library_region` VALUES (7,'fth');
INSERT INTO `library_region` VALUES (8,'yutu');
INSERT INTO `library_region` VALUES (9,'987');
INSERT INTO `library_region` VALUES (12,'SDGDGSDG');
INSERT INTO `library_region` VALUES (13,'345');
INSERT INTO `library_region` VALUES (14,'tyty');
INSERT INTO `library_region` VALUES (17,'456');
INSERT INTO `library_region` VALUES (18,'345235');
INSERT INTO `library_region` VALUES (19,'wet');
INSERT INTO `library_region` VALUES (20,'57w4574567');
INSERT INTO `library_region` VALUES (21,'erteartyerst');
INSERT INTO `library_region` VALUES (22,'sfaZsdf');
INSERT INTO `library_region` VALUES (23,'sdfsa');
INSERT INTO `library_region` VALUES (24,'sdfs');
INSERT INTO `library_region` VALUES (25,'asfdasf');
INSERT INTO `library_region` VALUES (26,'mnb');
INSERT INTO `library_region` VALUES (27,'dsf');
INSERT INTO `library_region` VALUES (28,'вапывап');
INSERT INTO `library_region` VALUES (29,'укафуае');
INSERT INTO `library_region` VALUES (30,'111sgfsdg');
INSERT INTO `library_region` VALUES (31,'варр');
INSERT INTO `library_region` VALUES (32,'527');
INSERT INTO `library_region` VALUES (33,'esf');
INSERT INTO `library_region` VALUES (34,'5y67we5yry');
INSERT INTO `library_region` VALUES (35,'енр');
INSERT INTO `library_region` VALUES (36,'rfgrsd');
INSERT INTO `library_region` VALUES (37,'yutruytyu');
INSERT INTO `library_region` VALUES (38,'```');
INSERT INTO `library_region` VALUES (39,'666');
INSERT INTO `library_region` VALUES (40,'4w5763q47');
INSERT INTO `library_region` VALUES (41,'1116787rtserg__65667e7');
INSERT INTO `library_region` VALUES (42,'111sfasefda');
INSERT INTO `library_region` VALUES (43,'ertewrt');
INSERT INTO `library_region` VALUES (44,'sadfASFASFd');
INSERT INTO `library_region` VALUES (45,'32142134');
INSERT INTO `library_region` VALUES (46,'etyeryt');
INSERT INTO `library_region` VALUES (47,'xfdgbsf');
INSERT INTO `library_region` VALUES (48,'saff');
INSERT INTO `library_region` VALUES (49,'gf');
INSERT INTO `library_region` VALUES (50,'erte');
INSERT INTO `library_region` VALUES (51,'1116uytu');
INSERT INTO `library_region` VALUES (52,'65465');
INSERT INTO `library_region` VALUES (54,'11156465про');
INSERT INTO `library_region` VALUES (55,'4w56w4356');
INSERT INTO `library_region` VALUES (56,'fgbdxfgdg');
INSERT INTO `library_region` VALUES (57,'sdfgzsdfg');
INSERT INTO `library_region` VALUES (58,'111xvgxcv');
INSERT INTO `library_region` VALUES (59,'111dfsfdsf');
INSERT INTO `library_region` VALUES (60,'123');
INSERT INTO `library_region` VALUES (61,'111654465');
INSERT INTO `library_region` VALUES (62,'111sdfsd');
INSERT INTO `library_region` VALUES (63,'45465465');
INSERT INTO `library_region` VALUES (65,'sfsdf');
INSERT INTO `library_region` VALUES (66,'SAfAS');
INSERT INTO `library_region` VALUES (67,'674567');
INSERT INTO `library_region` VALUES (68,'tsrtsret');
INSERT INTO `library_region` VALUES (69,'111xfbcf');
INSERT INTO `library_region` VALUES (71,'11');
INSERT INTO `library_region` VALUES (72,'tyudrt');
INSERT INTO `library_region` VALUES (73,'11165446544654');
INSERT INTO `library_region` VALUES (74,'кенкенкен');
INSERT INTO `library_region` VALUES (75,'atrwet');
INSERT INTO `library_region` VALUES (76,'srgszdg');
INSERT INTO `library_region` VALUES (77,'thydsfth');
INSERT INTO `library_region` VALUES (78,'sfgASDF');
INSERT INTO `library_region` VALUES (79,'eyraery');
INSERT INTO `library_region` VALUES (80,'111dhgzdfhgfxzbg');
INSERT INTO `library_region` VALUES (81,'ghrfhgdf');
INSERT INTO `library_region` VALUES (82,'dgfsdgd');
INSERT INTO `library_region` VALUES (83,'etawertewrt');
INSERT INTO `library_region` VALUES (84,'rgsrg');
INSERT INTO `library_region` VALUES (85,'dfSADGf');
INSERT INTO `library_region` VALUES (86,'dgSG');
INSERT INTO `library_region` VALUES (87,'sgfdsg');
INSERT INTO `library_region` VALUES (88,'gzdfgdfgdfg');
INSERT INTO `library_region` VALUES (89,'sdfszdf');
INSERT INTO `library_region` VALUES (90,'sfdsdf');
INSERT INTO `library_region` VALUES (91,'sfgsdf');
INSERT INTO `library_region` VALUES (92,'sgfaszxcvzxc');
INSERT INTO `library_region` VALUES (93,'zvxzxvc');
INSERT INTO `library_region` VALUES (94,'111dff');
INSERT INTO `library_region` VALUES (95,'rtyseryterdfgf');
INSERT INTO `library_region` VALUES (96,'gffdg');
INSERT INTO `library_region` VALUES (97,'111gsdfg');
INSERT INTO `library_region` VALUES (98,'sdfgsdfdf');
INSERT INTO `library_region` VALUES (100,'dgsadgfsdfg');
INSERT INTO `library_region` VALUES (101,'222');
INSERT INTO `library_region` VALUES (102,'111dfgfgf');
INSERT INTO `library_region` VALUES (103,'fsdf');
INSERT INTO `library_region` VALUES (104,'sgfsdggh');
INSERT INTO `library_region` VALUES (105,'sdfsdfdfsdfsdf');
INSERT INTO `library_region` VALUES (106,'111');
INSERT INTO `library_region` VALUES (107,'ertert');
INSERT INTO `library_region` VALUES (108,'879089589');
INSERT INTO `library_region` VALUES (109,'укенуыкеке');
INSERT INTO `library_region` VALUES (110,'врвар');
INSERT INTO `library_region` VALUES (111,'кенкенкен8');
INSERT INTO `library_region` VALUES (112,'цЦЙУВЫФВЫФ');
INSERT INTO `library_region` VALUES (113,'111rtert');
INSERT INTO `library_region` VALUES (114,'erter');
INSERT INTO `library_region` VALUES (115,'ryserysery');
INSERT INTO `library_region` VALUES (116,'dsrgdsfg');
INSERT INTO `library_region` VALUES (117,'urut6rt');
INSERT INTO `library_region` VALUES (118,'zfdgsdfg');
INSERT INTO `library_region` VALUES (119,'urtyry');
INSERT INTO `library_region` VALUES (120,'ваа');
INSERT INTO `library_region` VALUES (121,'sadfdsaf');
INSERT INTO `library_region` VALUES (122,'asdf');
/*!40000 ALTER TABLE `library_region` ENABLE KEYS */;
UNLOCK TABLES;

#
# Table structure for table library_unit
#

CREATE TABLE `library_unit` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
INSERT INTO `library_unit` VALUES (2,'1112');
INSERT INTO `library_unit` VALUES (3,'111');
/*!40000 ALTER TABLE `library_unit` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=utf8;
INSERT INTO `user` VALUES (1,'ОТМОРОЖЕННЫЙ МАМОНТ','ruslan.piskarev@gmail.com',NULL,'$2y$14$OaNgN6Zlnw/NDmVEkJdKlOzQxvGiJNBMfYfuwP7/c1Httjnu5TnJ6',1);
INSERT INTO `user` VALUES (53,'Крамаренко Владислав','kramarenko_vladislav@ukr.net',NULL,'$2y$14$3q5w7yr2UoKI9h6Moba.x.XtBZzQ6uhvr2a37hN7GQ3nfnDFudLjy',1);
INSERT INTO `user` VALUES (54,'test test','sdfsdfsd@sdfgsdf.com',NULL,'$2y$14$7lLQU/oDoReGOK1a/0Ou4Oq5hPo8VjpWhuxep4R6WIPqPGscg.4WW',1);
INSERT INTO `user` VALUES (56,'456456456346532456','456465@ertert.com',NULL,'$2y$14$Wv1rgyacLixWKjeb9o2PNeeEkVBJf1cMlYBltaR3N4NbsQbSKCIne',1);
INSERT INTO `user` VALUES (57,'erwearwerwer','erwerearw@aresre.com',NULL,'$2y$14$wDK7sNWujOzyaSCrLaMsGuHDUl318ALpWlJ6CntrwsqIFRr65MHMa',1);
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
INSERT INTO `user_role_linker` VALUES (57,'manager');
/*!40000 ALTER TABLE `user_role_linker` ENABLE KEYS */;
UNLOCK TABLES;

/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
