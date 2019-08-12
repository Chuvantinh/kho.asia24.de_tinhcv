-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               10.0.34-MariaDB-0ubuntu0.16.04.1 - Ubuntu 16.04
-- Server OS:                    debian-linux-gnu
-- HeidiSQL Version:             9.5.0.5277
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Dumping structure for table voxy.voxy_activities
CREATE TABLE IF NOT EXISTS `voxy_activities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mode` char(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `date_created` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Data exporting was unselected.
-- Dumping structure for table voxy.voxy_users_activities
CREATE TABLE IF NOT EXISTS `voxy_users_activities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `activity_id` int(11) NOT NULL,
  `users_lesson_id` int(11) NOT NULL,
  `date_completed` int(11) NOT NULL,
  `date_created` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`,`activity_id`,`users_lesson_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Data exporting was unselected.
-- Dumping structure for table voxy.voxy_users_lessons
CREATE TABLE IF NOT EXISTS `voxy_users_lessons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lesson_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date_started` int(11) NOT NULL,
  `date_completed` int(11) DEFAULT NULL,
  `reading_score` float DEFAULT NULL,
  `grammar_score` float DEFAULT NULL,
  `vocabulary_score` float DEFAULT NULL,
  `listening_score` float DEFAULT NULL,
  `writing_score` float DEFAULT NULL,
  `pronunciation_score` float DEFAULT NULL,
  `spelling_score` float DEFAULT NULL,
  `fluency_score` float DEFAULT NULL,
  `speaking_score` float DEFAULT NULL,
  `total_score` float DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lesson_id` (`lesson_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Data exporting was unselected.
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
