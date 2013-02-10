-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Хост: 127.0.0.1
-- Время создания: Фев 10 2013 г., 15:24
-- Версия сервера: 5.5.25
-- Версия PHP: 5.3.13

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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `artist` varchar(100) NOT NULL,
  `title` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;

--
-- Дамп данных таблицы `album`
--

INSERT INTO `album` (`id`, `artist`, `title`) VALUES
(8, 'тест', 'тестик2'),
(2, 'привет234', 'медвед234'),
(6, 'sdf', 'sdfs'),
(3, 'привет пок', 'пока'),
(4, 'test', 'test'),
(7, 'gh', 'fgh'),
(10, 'fu', 'fru');

-- --------------------------------------------------------

--
-- Структура таблицы `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `display_name` varchar(50) DEFAULT NULL,
  `password` varchar(128) NOT NULL,
  `state` smallint(6) DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=22 ;

--
-- Дамп данных таблицы `user`
--

INSERT INTO `user` (`user_id`, `username`, `email`, `display_name`, `password`, `state`) VALUES
(1, '45', 'ruslan.piskarev@gmail.com', 'Ruslan Piskarev', 'zenkov77', 1),
(2, NULL, 'mamont@rapvokzal.com', 'Mamont', '$2y$14$DKaHb.TwevOiufuPqoC7OOO5KS55P2fySQXvAzBImK7SeBlxHQpH.', 1),
(3, 'test', 'test', 'test', 'test', 1),
(4, 'test2', 'test2', 'test2', 'test2', 0),
(5, 'Фамилия Имя', 'Email', 'EmailОтображаемое имя', 'Пароль', 0),
(8, 'Имя', 'Email2', 'Отображаемое', '1234', 0),
(9, 'b', '345000@test.com', 'b', 'b', 1),
(14, '4574574', '4567456', '4564564', '3465345', 0),
(15, 'еукеуке', '123@gmail.com', NULL, 'кеуке', 0),
(16, 'куеуке', 'dsff@dfgsdf.com', NULL, '123456', 0),
(18, 'rtetert', 'reter@dfsdf.com', NULL, '123456', 0),
(19, 'кенкуен', 'erytery@dsf.com', NULL, '123456', 0),
(20, '65456456', 'ertrtr@safased.com', NULL, '$2y$14$dq5tPKadsdRfxYXl3i1Lyue../.G6mgXYB.EarQmCKQi9.73Mhd4C', 1),
(21, '56456', '123@123.com', NULL, '$2y$14$Cuxlz3BFfY32ZYqehMl4LO0E.nV6d.n5O9hK8Vt33yZ.1Ta6zHrry', 1);

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
('admin', 0, NULL),
('guest', 1, NULL),
('manager', 0, NULL);

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
(2, 'manager');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
