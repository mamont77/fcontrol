-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Хост: 127.0.0.1
-- Время создания: Мар 09 2013 г., 18:59
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=52 ;

--
-- Дамп данных таблицы `user`
--

INSERT INTO `user` (`user_id`, `username`, `email`, `display_name`, `password`, `state`) VALUES
(1, 'ОТМОРОЖЕННЫЙ МАМОНТ', 'ruslan.piskarev@gmail.com', NULL, '$2y$14$KbY/UZwU4LWUoIfgtRsqTOF6ZY5MvhoQICOdlnHbB7kFFwF8RwYOi', 1),
(5, 'Фамилия Имя', 'sfsdfsd2@sgfsdfd.com', NULL, '$2y$14$HFHWayoyLI.IFNUC6oVYZu2VwbNnJLjs7SqBos32mZQbdESm760Nm', 1),
(15, 'еукеуке', '123@gmail.com', NULL, 'кеуке', 0),
(16, 'куеуке', 'dsff@dfgsdf.com', NULL, '123456', 0),
(18, 'rtetert', 'reter@dfsdf.com', NULL, '123456', 0),
(19, 'кенкуен', 'erytery@dsf.com', NULL, '123456', 0),
(20, '65456456', 'ertrtr@safased.com', NULL, '$2y$14$dq5tPKadsdRfxYXl3i1Lyue../.G6mgXYB.EarQmCKQi9.73Mhd4C', 1),
(21, '56456', '123@123.com', NULL, '$2y$14$Cuxlz3BFfY32ZYqehMl4LO0E.nV6d.n5O9hK8Vt33yZ.1Ta6zHrry', 1),
(25, '145', 'n1450@test.com', NULL, '$2y$14$pE/LjOLS0LAMeeyBDmmrgebxc5rvRlu/UKFluFPbvrhfYZToCdB9y', 1),
(26, 'руский алфавит2', 'sdfsdf4a@sdfdf.com', NULL, '$2y$14$Kbhi84.wRjYxcPcBbj1S4OOBVmq54gHLcwSBZijb/ptseP.8yB0x2', 1),
(27, 'руский алфавит', 'sfsdfsd22@sgfsdfd.com', NULL, '$2y$14$RMuz4jNzrswGjCvVb3j7u.qTFL3FT.kM75GNhbCo8MY0/UNjnL5a2', 0),
(28, 'test2руский алфавит', 'sfsdfs3d2@sgfsdfd.com', NULL, '$2y$14$SIAwdKiEB1h9sonN/bVpq.VOrQnyFE5TmdYhoVz5iWTLnXu64O8Bq', 1),
(30, 'zf2 select selected', 'sfsdfsd2@sgfsd5fd.com', NULL, '$2y$14$4O76.gFLWxw6avz1uz8SfujhycqJnmXoceG7kvcPEyh8qOv0PeIMm', 1),
(32, 'руский алфавит36534', 'sfsd32fsd2@sgfsdfd.com', NULL, '$2y$14$4hmjlCVRppq2MSzx5OTXgeR33SsZ3nlt93y7MRn5qJTbxts9oB4uC', 1),
(33, 'retertert', 'sfsdfs76d2@sgfsdfd.com', NULL, '$2y$14$ptokaG8Zj3T2ITQYkYehPO36jladBbsnNEdcLusZ0y5STAIc5uyJW', 1),
(35, '$roleUserBridge', 'oleUserBridge@gfdsg.com', NULL, '$2y$14$ULxDg4ye9gWlWlG4iskYfOWV9ndQNcbgWcO2t1KDzjTT5OZGtfqgW', 1),
(37, 'вачпвап', 'sfsdf2344sd2@sgfsdfd.com', NULL, '$2y$14$7CPYqI02zPi7PxPv71wStORPfAlLW8FBxyDssnA1A49/GNNR5JEN6', 1),
(38, '4564356', 'sf22sd23fsd2@sgfsdfd.com', NULL, '$2y$14$Yj/CDTUzNWIEirDVhUvuzuasQRC8qa6bhgphCZ7I8jeq5yPJ/lkk2', 1),
(40, 'руский алфавит34', 'sfsdfs4d2@sgfsdfd.com', NULL, '$2y$14$/292etIN2LS0JSjvY4eJ4OpI/1NO/wMHZZxJP3X2Vo1aJVCZBGYJe', 1),
(41, 'руский алфавит32', 'sfsdfsdt2@sgfsdfd.com', NULL, '$2y$14$rVPGngZqeg3RmlnYD7acTOSwW6bRCC3VYs1IPejh9DJPvVSyOB.wG', 1),
(42, 'n14505', 'sfs4d23fsd2@sgfsdfd.com', NULL, '$2y$14$MuR/v6EG4faFysh1mbjji.IZyezdpQBu5zBs0mahYBGIo0quwy4Cq', 0),
(44, 'руский алф5авит', '12r3@gmail.com', NULL, '$2y$14$D9XgLEPSyH0pePZEe1odmuGFLd.FIiLRm8gKoTB5rZlqm5tr1vdg2', 1),
(46, 'n1450423', 'sf3d23fsd2@sgfsdfd.com', NULL, '$2y$14$iH1J79GdvHmVvXCAh8JZ7.e/oAbMYuDmpks94yqmic.U4fX/IBR3S', 1),
(48, 'fgdxgdsfgdf', 'sfsd423fsd2@sgfsdfd.com', NULL, '$2y$14$DepPwheUmIxMCAQBmvjuI.WLrV4vImW05P2UvR5VkRYTXIieO7Uzu', 1),
(49, 'n145035t', 'sfsdfs234d2@sgfsdfd.com', NULL, '$2y$14$oM0.LATbiptxMIg3asYhIu8.DGbcSPIR636x0TDP/BGe4a.4ZK3oW', 1),
(50, 'руский алфавит4re', 'sfsdfsd42@sgfsdfd.com', NULL, '$2y$14$gLvR0VW7hHz3OWgCL233b.r/txM52TqgqTiK5CMDpb.jBnu.P9Zdm', 1),
(51, '4565xzzvxcv', 'sf3sdfsd2@sgfsdfd.com', NULL, '$2y$14$SEnQnz3kJzp38tM9KLcCw.L3jEsDHz5zK0J9qXAVn4FxdSDLHAd7G', 1);

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
(0, ''),
(1, 'admin'),
(51, 'admin'),
(0, 'manager'),
(5, 'manager'),
(50, 'manager');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
