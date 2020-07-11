CREATE TABLE IF NOT EXISTS `dsite_bals_transactions`
(
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `USER_ID` int(11) NOT NULL,
  `TYPE` tinyint(1) NOT NULL,
  `BALS` int(11) NOT NULL,
  `DATE` datetime DEFAULT NULL,
  `CODE` varchar(255),
  PRIMARY KEY(`ID`)
);
CREATE TABLE IF NOT EXISTS `dsite_bals_users`
(
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `USER_ID` int(11) NOT NULL,
  `BALS` int(11) NOT NULL,
  `DATE_UPDATE` datetime DEFAULT NULL,
  PRIMARY KEY(`ID`)
);
