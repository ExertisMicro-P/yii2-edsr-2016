-- --------------------------------------------------------
-- Host:                         localhost
-- Server version:               5.5.27 - MySQL Community Server (GPL)
-- Server OS:                    Win32
-- HeidiSQL Version:             8.3.0.4694
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Dumping structure for table yii2-edsr2.cupboard
CREATE TABLE IF NOT EXISTS `cupboard` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reselleruser_id` int(11) NOT NULL,
  `enduser_id` int(11) NOT NULL,
  `name` varchar(128) NOT NULL,
  `notification_emails` varchar(4096) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_cupboard_enduser` (`enduser_id`),
  KEY `fk_cupboard_reslleruser` (`reselleruser_id`),
  CONSTRAINT `fk_cupboard_enduser` FOREIGN KEY (`enduser_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_cupboard_reslleruser` FOREIGN KEY (`reselleruser_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table yii2-edsr2.cupboard: ~0 rows (approximately)
/*!40000 ALTER TABLE `cupboard` DISABLE KEYS */;
/*!40000 ALTER TABLE `cupboard` ENABLE KEYS */;


-- Dumping structure for table yii2-edsr2.cupboard_item
CREATE TABLE IF NOT EXISTS `cupboard_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cupboard_id` int(11) NOT NULL,
  `digital_product_id` int(11) NOT NULL,
  `timestamp_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_cupboarditemledger_cupboard1` (`cupboard_id`),
  KEY `fk_cupboard_item_digital_product1` (`digital_product_id`),
  CONSTRAINT `fk_cupboarditemledger_cupboard1` FOREIGN KEY (`cupboard_id`) REFERENCES `cupboard` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_cupboard_item_digital_product1` FOREIGN KEY (`digital_product_id`) REFERENCES `digital_product` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table yii2-edsr2.cupboard_item: ~0 rows (approximately)
/*!40000 ALTER TABLE `cupboard_item` DISABLE KEYS */;
/*!40000 ALTER TABLE `cupboard_item` ENABLE KEYS */;


-- Dumping structure for table yii2-edsr2.digital_product
CREATE TABLE IF NOT EXISTS `digital_product` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `partcode` varchar(45) NOT NULL,
  `description` varchar(45) DEFAULT NULL,
  `is_digital` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

-- Dumping data for table yii2-edsr2.digital_product: ~2 rows (approximately)
/*!40000 ALTER TABLE `digital_product` DISABLE KEYS */;
INSERT INTO `digital_product` (`id`, `partcode`, `description`, `is_digital`) VALUES
	(1, 'MST5D-01574', 'Microsoft Office Home and Business 2013 32-Bi', 1),
	(2, 'MS79G-03549', 'Microsoft Office Home and Student 2013 32-Bit', 1);
/*!40000 ALTER TABLE `digital_product` ENABLE KEYS */;


-- Dumping structure for table yii2-edsr2.migration
CREATE TABLE IF NOT EXISTS `migration` (
  `version` varchar(180) NOT NULL,
  `apply_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table yii2-edsr2.migration: ~4 rows (approximately)
/*!40000 ALTER TABLE `migration` DISABLE KEYS */;
/*INSERT INTO `migration` (`version`, `apply_time`) VALUES
	('m000000_000000_base', 1416405680),
	('m130524_201442_init', 1416405685),
	('m140524_153638_init_user', 1416408131),
	('m140524_153642_init_user_auth', 1416408131);
*/
/*!40000 ALTER TABLE `migration` ENABLE KEYS */;



-- Dumping structure for table yii2-edsr2.profile
CREATE TABLE IF NOT EXISTS `profile` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `create_time` timestamp NULL DEFAULT NULL,
  `update_time` timestamp NULL DEFAULT NULL,
  `full_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `profile_user_id` (`user_id`),
  CONSTRAINT `profile_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Dumping data for table yii2-edsr2.profile: ~2 rows (approximately)
/*!40000 ALTER TABLE `profile` DISABLE KEYS */;
INSERT INTO `profile` (`id`, `user_id`, `create_time`, `update_time`, `full_name`) VALUES
	(2, 2, '2014-11-19 15:49:11', NULL, NULL),
	(3, 3, '2014-11-24 12:49:51', '2014-11-24 15:48:11', 'XYZ Ltd');
/*!40000 ALTER TABLE `profile` ENABLE KEYS */;


-- Dumping structure for table yii2-edsr2.role
CREATE TABLE IF NOT EXISTS `role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `create_time` timestamp NULL DEFAULT NULL,
  `update_time` timestamp NULL DEFAULT NULL,
  `can_admin` smallint(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Dumping data for table yii2-edsr2.role: ~2 rows (approximately)
/*!40000 ALTER TABLE `role` DISABLE KEYS */;
INSERT INTO `role` (`id`, `name`, `create_time`, `update_time`, `can_admin`) VALUES
	(1, 'Admin', '2014-11-19 15:42:11', NULL, 1),
	(2, 'User', '2014-11-19 15:42:11', NULL, 0);
/*!40000 ALTER TABLE `role` ENABLE KEYS */;


-- Dumping structure for table yii2-edsr2.stockroom
CREATE TABLE IF NOT EXISTS `stockroom` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `name` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_stockroom_user1` (`user_id`),
  CONSTRAINT `fk_stockroom_user1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- Dumping data for table yii2-edsr2.stockroom: ~1 rows (approximately)
/*!40000 ALTER TABLE `stockroom` DISABLE KEYS */;
INSERT INTO `stockroom` (`id`, `user_id`, `name`) VALUES
	(1, 3, 'Main Stock Room');
/*!40000 ALTER TABLE `stockroom` ENABLE KEYS */;


-- Dumping structure for table yii2-edsr2.stock_item
CREATE TABLE IF NOT EXISTS `stock_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `stockroom_id` int(11) NOT NULL,
  `digital_product_id` int(11) NOT NULL,
  `timestamp_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_stockitem_stockroom1` (`stockroom_id`),
  KEY `fk_stock_item_digital_product1` (`digital_product_id`),
  CONSTRAINT `fk_stockitem_stockroom1` FOREIGN KEY (`stockroom_id`) REFERENCES `stockroom` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_stock_item_digital_product1` FOREIGN KEY (`digital_product_id`) REFERENCES `digital_product` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

-- Dumping data for table yii2-edsr2.stock_item: ~2 rows (approximately)
/*!40000 ALTER TABLE `stock_item` DISABLE KEYS */;
INSERT INTO `stock_item` (`id`, `stockroom_id`, `digital_product_id`, `timestamp_added`) VALUES
	(1, 1, 1, '2014-11-24 16:41:06'),
	(2, 1, 2, '2014-11-24 17:29:11');
/*!40000 ALTER TABLE `stock_item` ENABLE KEYS */;


-- Dumping structure for table yii2-edsr2.user
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL,
  `status` smallint(6) NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `new_email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `username` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `auth_key` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `api_key` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `login_ip` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `login_time` timestamp NULL DEFAULT NULL,
  `create_ip` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `create_time` timestamp NULL DEFAULT NULL,
  `update_time` timestamp NULL DEFAULT NULL,
  `ban_time` timestamp NULL DEFAULT NULL,
  `ban_reason` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_email` (`email`),
  UNIQUE KEY `user_username` (`username`),
  KEY `user_role_id` (`role_id`),
  CONSTRAINT `user_role_id` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Dumping data for table yii2-edsr2.user: ~2 rows (approximately)
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` (`id`, `role_id`, `status`, `email`, `new_email`, `username`, `password`, `auth_key`, `api_key`, `login_ip`, `login_time`, `create_ip`, `create_time`, `update_time`, `ban_time`, `ban_reason`) VALUES
	(2, 1, 1, 'russell@feeed.com', NULL, NULL, '$2y$13$yY0kmYYEulCcYRSXwxf3Pea2T4ZjV65G9TwgSred84rUzI0Mj8owS', 'bKhY6Odo6ZO5XEHi1I2o5sMb2iiQP0A4', 's6Z_z37WZvtAzNUGxYKq4zVz4xICR1aK', '127.0.0.1', '2014-12-01 10:42:58', '127.0.0.1', '2014-11-19 15:49:11', NULL, NULL, NULL),
	(3, 2, 2, 'russellh@micro-p.com', NULL, 'reseller', '$2y$13$d5DKmQhq.o6f0WUiOQiyy.G7tzi.JuoOTlSPN3rZi4Q728xcEqtJW', NULL, NULL, '127.0.0.1', '2014-11-24 12:50:20', NULL, '2014-11-24 12:49:51', '2014-11-24 15:48:11', NULL, NULL);
	
/*!40000 ALTER TABLE `user` ENABLE KEYS */;


-- Dumping structure for table yii2-edsr2.user_auth
CREATE TABLE IF NOT EXISTS `user_auth` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `provider` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `provider_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `provider_attributes` text COLLATE utf8_unicode_ci NOT NULL,
  `create_time` timestamp NULL DEFAULT NULL,
  `update_time` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_auth_provider_id` (`provider_id`),
  KEY `user_auth_user_id` (`user_id`),
  CONSTRAINT `user_auth_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Dumping data for table yii2-edsr2.user_auth: ~0 rows (approximately)
/*!40000 ALTER TABLE `user_auth` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_auth` ENABLE KEYS */;


-- Dumping structure for table yii2-edsr2.user_key
CREATE TABLE IF NOT EXISTS `user_key` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `type` smallint(6) NOT NULL,
  `key` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `create_time` timestamp NULL DEFAULT NULL,
  `consume_time` timestamp NULL DEFAULT NULL,
  `expire_time` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_key_key` (`key`),
  KEY `user_key_user_id` (`user_id`),
  CONSTRAINT `user_key_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Dumping data for table yii2-edsr2.user_key: ~1 rows (approximately)
/*!40000 ALTER TABLE `user_key` DISABLE KEYS */;
INSERT INTO `user_key` (`id`, `user_id`, `type`, `key`, `create_time`, `consume_time`, `expire_time`) VALUES
	(1, 2, 1, 'HcwMrxse425I03ANB3Gz1Us9lJoVWwkN', '2014-11-19 15:49:11', '2014-11-19 16:02:09', NULL);
/*!40000 ALTER TABLE `user_key` ENABLE KEYS */;


-- Dumping structure for table yii2-edsr2.user_setting
CREATE TABLE IF NOT EXISTS `user_setting` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `notification_emails` varchar(4096) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_usersetting_user1` (`user_id`),
  CONSTRAINT `fk_usersetting_user1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table yii2-edsr2.user_setting: ~0 rows (approximately)
/*!40000 ALTER TABLE `user_setting` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_setting` ENABLE KEYS */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
