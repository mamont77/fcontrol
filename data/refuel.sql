DROP TABLE IF EXISTS `flightRefuelForm`;
CREATE TABLE IF NOT EXISTS `flightRefuelForm` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `headerId` int(11) NOT NULL DEFAULT '0',
  `airport` int(11) NOT NULL,
  `date` int(10) NOT NULL,
  `agent` int(11) NOT NULL,
  `quantity` varchar(10) NOT NULL DEFAULT '',
  `unit` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;