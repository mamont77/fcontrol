-- phpMyAdmin SQL Dump
-- version 3.5.7
-- http://www.phpmyadmin.net
--
-- Хост: 127.0.0.1:3306
-- Время создания: Мар 28 2013 г., 20:11
-- Версия сервера: 5.5.30-log
-- Версия PHP: 5.3.23

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `zf2tutorial`
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;

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
(10, 'fu', 'fru');

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Дамп данных таблицы `library_country`
--

INSERT INTO `library_country` (`id`, `region`, `name`, `code`) VALUES
(1, 1, 'test', 'aa'),
(2, 2, '111', '111'),
(3, 3, '2232', '222'),
(4, 1, '4352345', '345'),
(5, 1, '1112', '112');

-- --------------------------------------------------------

--
-- Структура таблицы `library_region`
--

CREATE TABLE IF NOT EXISTS `library_region` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT 'Region of the world',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Дамп данных таблицы `library_region`
--

INSERT INTO `library_region` (`id`, `name`) VALUES
(1, 'ewrwr'),
(2, '444'),
(3, '363'),
(4, '344');

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
(53, 'Крамаренко Владислав', 'vlad@examle.com', NULL, '$2y$14$yFW7Of3yRT5NVTK8sWaMhONRQZtdRBWoftay2SnzP.qZPmUvWgxj.', 1),
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
