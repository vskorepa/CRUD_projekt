
SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

USE `CRUD_Projekt`;

SET NAMES utf8mb4;

CREATE TABLE `room` (
  `room_id` int(11) NOT NULL AUTO_INCREMENT,
  `no` varchar(15) COLLATE utf8mb4_czech_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_czech_ci NOT NULL,
  `phone` varchar(15) COLLATE utf8mb4_czech_ci DEFAULT NULL,
  PRIMARY KEY (`room_id`),
  UNIQUE KEY `no` (`no`),
  UNIQUE KEY `phone` (`phone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;
INSERT INTO `room` (`room_id`, `no`, `name`, `phone`) VALUES
(1,	'101',	'Example_room',	'1111');

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

INSERT INTO `employee` (`login`, `password`, `admin`, `employee_id`, `name`, `surname`, `job`, `wage`, `room`) VALUES
('admin',	'$2y$10$DNM21BAcIwyP4NRngUL.Q./5iICqdzY6Y71geoxtNESdmzn/nhiRe',	1,	1,	'Admin',	'Example',	'admin',	65000,	1),
('user',	'$2y$10$Hw9w1EBBPCM3dkqmM5enceQ0X/IqX7NiQZ/YZp3jnliodpPtoy3D2',	0,	2,	'User',	'Example',	'user',	42000,	1);


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
INSERT INTO `key` (`key_id`, `employee`, `room`) VALUES
(1,	1,	1),
(2,	2,	1);

