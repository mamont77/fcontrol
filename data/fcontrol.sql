-- phpMyAdmin SQL Dump
-- version 3.5.7
-- http://www.phpmyadmin.net
--
-- Хост: 127.0.0.1:3306
-- Время создания: Май 28 2013 г., 18:58
-- Версия сервера: 5.5.30-log
-- Версия PHP: 5.3.23

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `fcontrol`
--

-- --------------------------------------------------------

--
-- Структура таблицы `album`
--

CREATE TABLE IF NOT EXISTS `album` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `artist` varchar(100) NOT NULL,
  `title` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=15 ;

--
-- Дамп данных таблицы `album`
--

INSERT INTO `album` (`id`, `artist`, `title`) VALUES
(2, 'привет234', 'медвед234'),
(3, 'привет пок', 'пока'),
(4, 'test', 'test'),
(6, 'sdf', 'sdfs'),
(7, 'gh', 'fgh'),
(8, 'тест', 'тестик2'),
(10, 'fu', 'fru'),
(13, 'szdfsdf', 'fdsf'),
(14, 'fdsgdsgh', 'xfg');

-- --------------------------------------------------------

--
-- Структура таблицы `flightBaseForm`
--

CREATE TABLE IF NOT EXISTS `flightBaseForm` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `refNumberOrder` varchar(255) NOT NULL DEFAULT '',
  `dateOrder` int(11) DEFAULT NULL,
  `kontragent` int(11) DEFAULT NULL,
  `airOperator` int(11) DEFAULT NULL,
  `aircraft` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `refNumberOrder` (`refNumberOrder`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=14 ;

--
-- Дамп данных таблицы `flightBaseForm`
--

INSERT INTO `flightBaseForm` (`id`, `refNumberOrder`, `dateOrder`, `kontragent`, `airOperator`, `aircraft`) VALUES
(8, 'ORD-2013052248/1', 2013, 2, 5, '12345'),
(9, 'ORD-2013052204/1', 2013, 3, 4, '12345'),
(10, 'ORD-2013052207/1', 2013, 3, 4, '2132132456'),
(12, 'ORD-2013052243/1', 2013, 2, 1, '123'),
(13, 'ORD-2013052322/1', 2013, 2, 3, '2132132456');

-- --------------------------------------------------------

--
-- Структура таблицы `library_aircraft`
--

CREATE TABLE IF NOT EXISTS `library_aircraft` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `aircraft_type` int(10) DEFAULT NULL,
  `reg_number` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `reg_number` (`reg_number`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Дамп данных таблицы `library_aircraft`
--

INSERT INTO `library_aircraft` (`id`, `aircraft_type`, `reg_number`) VALUES
(2, 4, '222'),
(6, 2, '2132132456'),
(3, 2, '1000000000'),
(4, 7, '123'),
(5, 10, '12345');

-- --------------------------------------------------------

--
-- Структура таблицы `library_aircraft_type`
--

CREATE TABLE IF NOT EXISTS `library_aircraft_type` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(10) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Дамп данных таблицы `library_aircraft_type`
--

INSERT INTO `library_aircraft_type` (`id`, `name`) VALUES
(7, 'vxzcvxc'),
(2, 'szgsdgsdfd'),
(8, 'xcvxcvv'),
(10, 'ваВа');

-- --------------------------------------------------------

--
-- Структура таблицы `library_airport`
--

CREATE TABLE IF NOT EXISTS `library_airport` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `country` int(10) DEFAULT NULL,
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT 'name of airport',
  `short_name` varchar(30) NOT NULL DEFAULT '',
  `code_icao` varchar(4) NOT NULL DEFAULT '',
  `code_iata` varchar(3) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=17 ;

--
-- Дамп данных таблицы `library_airport`
--

INSERT INTO `library_airport` (`id`, `country`, `name`, `short_name`, `code_icao`, `code_iata`) VALUES
(4, 10, '223333333333333', '23333333333333333333', '1234', '234'),
(10, 11, 'Борисполь', 'МАО', '0100', '123'),
(5, 10, '2345235234', '235421354', '2355', '523'),
(6, 2, '234', '21342134', '3242', '23q'),
(7, 2, '235432qq5', '2352', '5443', '234'),
(9, 2, 'twetrawer6e57', 'tawetwaet', 'twer', 'ert'),
(13, 0, 'sdfgsd', 'sdg', 'sdfz', 'szd'),
(12, 11, 'Жуляны', 'МАО2', '0101', '111'),
(14, 10, 'rszg', 'szdgzsdg', 'SDGS', 'SGd'),
(15, 12, 'aewtrawerf', 'werwaer', 'wear', 'wae');

-- --------------------------------------------------------

--
-- Структура таблицы `library_air_operator`
--

CREATE TABLE IF NOT EXISTS `library_air_operator` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) DEFAULT NULL,
  `short_name` varchar(15) DEFAULT NULL,
  `code_icao` varchar(3) NOT NULL DEFAULT '',
  `code_iata` varchar(2) NOT NULL DEFAULT '',
  `country` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `shot_name` (`short_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Дамп данных таблицы `library_air_operator`
--

INSERT INTO `library_air_operator` (`id`, `name`, `short_name`, `code_icao`, `code_iata`, `country`) VALUES
(1, 'кпыуп', 'кпфыукпу', '1ку', '2у', 11),
(3, 'вапвап', 'апвап', 'вап', '', 12),
(4, 'укецфе', 'кекуе', 'куе', 'ке', 12),
(5, 'Name of Air Operator', 'Short name of A', 'ICA', 'IA', 11);

-- --------------------------------------------------------

--
-- Структура таблицы `library_country`
--

CREATE TABLE IF NOT EXISTS `library_country` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `region` int(10) DEFAULT NULL,
  `name` varchar(30) DEFAULT NULL,
  `code` varchar(3) DEFAULT NULL COMMENT 'code of country',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `code` (`code`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=19 ;

--
-- Дамп данных таблицы `library_country`
--

INSERT INTO `library_country` (`id`, `region`, `name`, `code`) VALUES
(1, 1, 'test', 'aa'),
(2, 2, 'Россия', 'RU'),
(3, 3, '2232', '222'),
(4, 1, '4352345', '345'),
(5, 1, '1112', '112'),
(6, 1, 'екнкн', 'tyr'),
(7, 1, '365456', '447'),
(8, 1, 'ryt', 'ry'),
(18, 6, 'вавыфав', 'das'),
(11, 66, 'Украина', 'UA'),
(12, 6, 'уФУАУ', 'sdf'),
(13, 17, 'sefAWEfAE', 'ASF'),
(14, 5, 'sfsafSDAf', 'SZD'),
(15, 5, 'asdsd', 'aSd'),
(16, 13, 'ASDDaSd', '354'),
(17, 4, 'sytysty', 'trs');

-- --------------------------------------------------------

--
-- Структура таблицы `library_currency`
--

CREATE TABLE IF NOT EXISTS `library_currency` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) DEFAULT NULL,
  `currency` varchar(3) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `currency` (`name`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Дамп данных таблицы `library_currency`
--

INSERT INTO `library_currency` (`id`, `name`, `currency`) VALUES
(3, '1112', '122'),
(2, 'sds111', '444'),
(4, 'sadas', 'ыва'),
(5, 'фывафыаываыва', 'ваы');

-- --------------------------------------------------------

--
-- Структура таблицы `library_fixed_service_type`
--

CREATE TABLE IF NOT EXISTS `library_fixed_service_type` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `library_kontragent`
--

CREATE TABLE IF NOT EXISTS `library_kontragent` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Дамп данных таблицы `library_kontragent`
--

INSERT INTO `library_kontragent` (`id`, `name`, `short_name`, `address`, `phone1`, `phone2`, `phone3`, `fax`, `mail`, `sita`) VALUES
(2, 'fasdffgs', '1115465', 'ertrtsdfsa', 'ertewrtsdfsaf', 'ertewrtsadf', 'eretr', 'ertewrtsdf', 'sdferter@dsfsdf.com', 'er'),
(3, 'qwewq', 'qwewqe', 'rqer\r\nweqe', 'wqeqwe', 'qweqwe', 'qwee', 'wqeqw', 'wqeqw@sdafedf.com', 'wewe');

-- --------------------------------------------------------

--
-- Структура таблицы `library_region`
--

CREATE TABLE IF NOT EXISTS `library_region` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT 'Region of the world',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=123 ;

--
-- Дамп данных таблицы `library_region`
--

INSERT INTO `library_region` (`id`, `name`) VALUES
(6, 'sdfgsdg'),
(2, '444'),
(18, '345235'),
(4, '777'),
(5, 'tyuyutu'),
(7, 'fth'),
(8, 'yutu'),
(9, '987'),
(17, '456'),
(37, 'yutruytyu'),
(12, 'SDGDGSDG'),
(13, '345'),
(14, 'tyty'),
(43, 'ertewrt'),
(19, 'wet'),
(20, '57w4574567'),
(21, 'erteartyerst'),
(22, 'sfaZsdf'),
(23, 'sdfsa'),
(24, 'sdfs'),
(25, 'asfdasf'),
(26, 'mnb'),
(27, 'dsf'),
(28, 'вапывап'),
(29, 'укафуае'),
(30, '111sgfsdg'),
(31, 'варр'),
(32, '527'),
(33, 'esf'),
(34, '5y67we5yry'),
(35, 'енр'),
(36, 'rfgrsd'),
(38, '```'),
(39, '666'),
(40, '4w5763q47'),
(41, '1116787rtserg__65667e7'),
(42, '111sfasefda'),
(44, 'sadfASFASFd'),
(45, '32142134'),
(46, 'etyeryt'),
(47, 'xfdgbsf'),
(48, 'saff'),
(49, 'gf'),
(50, 'erte'),
(51, '1116uytu'),
(52, '65465'),
(60, '123'),
(54, '11156465про'),
(55, '4w56w4356'),
(56, 'fgbdxfgdg'),
(57, 'sdfgzsdfg'),
(58, '111xvgxcv'),
(59, '111dfsfdsf'),
(61, '111654465'),
(62, '111sdfsd'),
(63, '45465465'),
(66, 'SAfAS'),
(65, 'sfsdf'),
(67, '674567'),
(68, 'tsrtsret'),
(69, '111xfbcf'),
(114, 'erter'),
(71, '11'),
(72, 'tyudrt'),
(73, '11165446544654'),
(74, 'кенкенкен'),
(75, 'atrwet'),
(76, 'srgszdg'),
(77, 'thydsfth'),
(78, 'sfgASDF'),
(79, 'eyraery'),
(80, '111dhgzdfhgfxzbg'),
(81, 'ghrfhgdf'),
(82, 'dgfsdgd'),
(83, 'etawertewrt'),
(84, 'rgsrg'),
(85, 'dfSADGf'),
(86, 'dgSG'),
(87, 'sgfdsg'),
(88, 'gzdfgdfgdfg'),
(89, 'sdfszdf'),
(90, 'sfdsdf'),
(91, 'sfgsdf'),
(92, 'sgfaszxcvzxc'),
(93, 'zvxzxvc'),
(94, '111dff'),
(95, 'rtyseryterdfgf'),
(96, 'gffdg'),
(97, '111gsdfg'),
(98, 'sdfgsdfdf'),
(113, '111rtert'),
(100, 'dgsadgfsdfg'),
(101, '222'),
(102, '111dfgfgf'),
(103, 'fsdf'),
(104, 'sgfsdggh'),
(105, 'sdfsdfdfsdfsdf'),
(106, '111'),
(107, 'ertert'),
(108, '879089589'),
(109, 'укенуыкеке'),
(110, 'врвар'),
(111, 'кенкенкен8'),
(112, 'цЦЙУВЫФВЫФ'),
(115, 'ryserysery'),
(116, 'dsrgdsfg'),
(117, 'urut6rt'),
(118, 'zfdgsdfg'),
(119, 'urtyry'),
(120, 'ваа'),
(121, 'sadfdsaf'),
(122, 'asdf');

-- --------------------------------------------------------

--
-- Структура таблицы `library_unit`
--

CREATE TABLE IF NOT EXISTS `library_unit` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Дамп данных таблицы `library_unit`
--

INSERT INTO `library_unit` (`id`, `name`) VALUES
(2, '1112'),
(3, '111');

-- --------------------------------------------------------

--
-- Структура таблицы `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `user_id` int(10) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `display_name` varchar(50) DEFAULT NULL,
  `password` varchar(128) NOT NULL,
  `state` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=58 ;

--
-- Дамп данных таблицы `user`
--

INSERT INTO `user` (`user_id`, `username`, `email`, `display_name`, `password`, `state`) VALUES
(1, 'ОТМОРОЖЕННЫЙ МАМОНТ', 'ruslan.piskarev@gmail.com', NULL, '$2y$14$OaNgN6Zlnw/NDmVEkJdKlOzQxvGiJNBMfYfuwP7/c1Httjnu5TnJ6', 1),
(53, 'Крамаренко Владислав', 'kramarenko_vladislav@ukr.net', NULL, '$2y$14$3q5w7yr2UoKI9h6Moba.x.XtBZzQ6uhvr2a37hN7GQ3nfnDFudLjy', 1),
(54, 'test test', 'sdfsdfsd@sdfgsdf.com', NULL, '$2y$14$7lLQU/oDoReGOK1a/0Ou4Oq5hPo8VjpWhuxep4R6WIPqPGscg.4WW', 1),
(56, '456456456346532456', '456465@ertert.com', NULL, '$2y$14$Wv1rgyacLixWKjeb9o2PNeeEkVBJf1cMlYBltaR3N4NbsQbSKCIne', 1),
(57, 'erwearwerwer', 'erwerearw@aresre.com', NULL, '$2y$14$wDK7sNWujOzyaSCrLaMsGuHDUl318ALpWlJ6CntrwsqIFRr65MHMa', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `user_role`
--

CREATE TABLE IF NOT EXISTS `user_role` (
  `role_id` varchar(255) NOT NULL,
  `default` tinyint(1) NOT NULL,
  `parent` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `user_role`
--

INSERT INTO `user_role` (`role_id`, `default`, `parent`) VALUES
('admin', 0, 'user'),
('guest', 1, NULL),
('user', 0, NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `user_role_linker`
--

CREATE TABLE IF NOT EXISTS `user_role_linker` (
  `user_id` int(11) unsigned NOT NULL,
  `role_id` varchar(255) NOT NULL,
  PRIMARY KEY (`user_id`,`role_id`),
  KEY `role_id` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `user_role_linker`
--

INSERT INTO `user_role_linker` (`user_id`, `role_id`) VALUES
(1, 'admin'),
(52, 'admin'),
(53, 'admin'),
(56, 'manager'),
(57, 'manager'),
(54, 'user');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
