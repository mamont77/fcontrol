CREATE TABLE IF NOT EXISTS `invoiceIncomePermissionData` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `invoiceId` int(10) NOT NULL,
  `typeOfServiceId` int(10) NOT NULL,
  `itemPrice` varchar(40) NOT NULL DEFAULT '',
  `quantity` varchar(40) NOT NULL DEFAULT '',
  `unitId` varchar(40) NOT NULL,
  `priceTotal` varchar(40) NOT NULL DEFAULT '',
  `priceTotalExchangedToUsd` varchar(40) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `invoiceId` (`invoiceId`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `invoiceIncomePermissionMain` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `preInvoiceId` int(10) NOT NULL,
  `number` varchar(40) NOT NULL,
  `date` int(10) NOT NULL,
  `currency` varchar(3) NOT NULL,
  `exchangeRate` varchar(10) NOT NULL,
  `flightNumber` varchar(40) NOT NULL,
  `dateArr` int(10) NOT NULL,
  `dateDep` int(10) NOT NULL,
  `typeOfServiceId` int(10) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `preInvoiceId` (`preInvoiceId`),
  KEY `date` (`date`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `invoiceOutcomePermissionData` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `invoiceId` int(10) NOT NULL,
  `typeOfServiceId` int(10) NOT NULL,
  `itemPrice` varchar(40) NOT NULL DEFAULT '',
  `quantity` varchar(40) NOT NULL DEFAULT '',
  `unitId` varchar(40) NOT NULL,
  `priceTotal` varchar(40) NOT NULL DEFAULT '',
  `priceTotalExchangedToUsd` varchar(40) NOT NULL DEFAULT '',
  `isAdditionalInfo` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `invoiceId` (`invoiceId`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `invoiceOutcomePermissionMain` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `incomeInvoiceId` int(10) NOT NULL,
  `number` varchar(40) NOT NULL,
  `date` int(10) NOT NULL,
  `currency` varchar(3) NOT NULL,
  `exchangeRate` varchar(10) NOT NULL,
  `flightNumber` varchar(40) NOT NULL,
  `dateArr` int(10) NOT NULL,
  `dateDep` int(10) NOT NULL,
  `typeOfServiceId` int(10) NOT NULL,
  `disbursement` varchar(40) NOT NULL,
  `disbursementTotalExchangedToUsd` varchar(40) NOT NULL,
  `disbursementTotal` varchar(40) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `incomeInvoiceId` (`incomeInvoiceId`),
  KEY `date` (`date`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;