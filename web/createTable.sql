SET GLOBAL time_zone = 'Europe/Brussels';

DROP TABLE IF EXISTS `User`;
DROP TABLE IF EXISTS `Box`;

/* Need to find how to correctly protect password and if possible with BCrypt */
CREATE TABLE `User` (
      `userID` int(11) NOT NULL AUTO_INCREMENT,
      `mail` varchar(50) NOT NULL DEFAULT '',
      `password` varchar(32) NOT NULL,
      `name` string NOT NULL DEFAULT '',
      `firstname` string NOT NULL DEFAULT '',
      `address` string,
      `phone` varchar(12),
      `birthdate` Date NOT NULL DEFAULT '2019-10-31',
      `dateRegistered` Datetime NOT NULL,
      `device` Boolean DEFAULT '0',
      `deviceOutdoor` Boolean DEFAULT '0',
      `sensors` ENUM('0','BMP280','BME280','BME680','SDS011') DEFAULT '0',
      PRIMARY KEY (`userID`)
)

CREATE TABLE `Box` (
      `boxID` int(6) NOT NULL AUTO_INCREMENT,
      `datetime` Datetime,
      `temperature` float(4,1), /* in celsius */
      `humidity` float(4,1), /* in percentage */
      `pressure` float(6,2), /* in hectopascal */
      `gas` float(6,2), /* in ohm */
      `PM2` float(6,4), /* in micrometer */
      `PM10` float(6,4), /* in micrometer */
      PRIMARY KEY (`boxID`)
)

