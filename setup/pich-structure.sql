SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;


CREATE TABLE IF NOT EXISTS `rules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `schedule` int(11) NOT NULL,
  `sensor` varchar(50) NOT NULL,
  `targettemp` float NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `schedule_2` (`schedule`),
  KEY `schedule` (`schedule`),
  KEY `sensor` (`sensor`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `schedules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `friendlyname` varchar(50) NOT NULL,
  `dayofweek` varchar(7) NOT NULL DEFAULT '0000000',
  `pretimestart` time NOT NULL,
  `timestart` time NOT NULL,
  `timeend` time NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `friendlyname` (`friendlyname`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `sensors` (
  `uid` varchar(50) NOT NULL,
  `device` int(50) NOT NULL AUTO_INCREMENT,
  `friendlyname` varchar(50) DEFAULT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '0',
  `sequence` int(11) NOT NULL DEFAULT '0',
  `output` bit(1) NOT NULL DEFAULT b'1',
  `offset` float NOT NULL DEFAULT '0',
  PRIMARY KEY (`device`),
  UNIQUE KEY `uid` (`uid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3481 ;

CREATE TABLE IF NOT EXISTS `status` (
  `status` tinyint(1) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `temperature` (
  `sensor` varchar(50) NOT NULL,
  `temperature` float NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY `sensor` (`sensor`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


ALTER TABLE `rules`
  ADD CONSTRAINT `rules_ibfk_2` FOREIGN KEY (`sensor`) REFERENCES `sensors` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `rules_ibfk_3` FOREIGN KEY (`schedule`) REFERENCES `schedules` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `temperature`
  ADD CONSTRAINT `temperature_ibfk_1` FOREIGN KEY (`sensor`) REFERENCES `sensors` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
