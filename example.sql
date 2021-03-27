
SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

USE `ip_3`;

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `room`;
CREATE TABLE `room` (
  `room_id` int(11) NOT NULL AUTO_INCREMENT,
  `no` varchar(15) COLLATE utf8mb4_czech_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_czech_ci NOT NULL,
  `phone` varchar(15) COLLATE utf8mb4_czech_ci DEFAULT NULL,
  PRIMARY KEY (`room_id`),
  UNIQUE KEY `no` (`no`),
  UNIQUE KEY `phone` (`phone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;


DROP TABLE IF EXISTS `employee`;
CREATE TABLE `employee` (
  `login` varchar(255) COLLATE utf8mb4_czech_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_czech_ci NOT NULL,
  `admin` tinyint(4) NOT NULL,
  `employee_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_czech_ci NOT NULL,
  `surname` varchar(255) COLLATE utf8mb4_czech_ci NOT NULL,
  `job` varchar(255) COLLATE utf8mb4_czech_ci NOT NULL,
  `wage` int(11) NOT NULL,
  `room` int(11) NOT NULL,
  PRIMARY KEY (`employee_id`),
  KEY `room` (`room`),
  CONSTRAINT `employee_ibfk_1` FOREIGN KEY (`room`) REFERENCES `room` (`room_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;


DROP TABLE IF EXISTS `key`;
CREATE TABLE `key` (
  `key_id` int(11) NOT NULL AUTO_INCREMENT,
  `employee` int(11) NOT NULL,
  `room` int(11) NOT NULL,
  PRIMARY KEY (`key_id`),
  UNIQUE KEY `employee_room` (`employee`,`room`),
  KEY `room` (`room`),
  CONSTRAINT `key_ibfk_1` FOREIGN KEY (`employee`) REFERENCES `employee` (`employee_id`) ON DELETE CASCADE,
  CONSTRAINT `key_ibfk_2` FOREIGN KEY (`room`) REFERENCES `room` (`room_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;


