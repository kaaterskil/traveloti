-- phpMyAdmin SQL Dump
-- version 3.4.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 06, 2013 at 07:16 AM
-- Server version: 5.5.15
-- PHP Version: 5.4.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `traveloti`
--

DELIMITER $$
--
-- Procedures
--
DROP PROCEDURE IF EXISTS `sp_next_insert_id`$$
CREATE DEFINER=`dev`@`localhost` PROCEDURE `sp_next_insert_id`(in in_seq_name varchar(32))
begin
--
update cfg_sequences set
current_value = (@nextval := current_value + 1)
, hash_value = (@hash := round(round(mod((@nextval * factor), divisor) / 1000000000, 9) * 1000000000, 0))
where sequence_name = in_seq_name;
--
select round(@hash);
--
end$$

--
-- Functions
--
DROP FUNCTION IF EXISTS `sf_next_insert_id`$$
CREATE DEFINER=`dev`@`localhost` FUNCTION `sf_next_insert_id`(in_seq_name varchar(32)) RETURNS int(11)
begin
--
update cfg_sequences set
current_value = (@nextval := current_value + 1)
, hash_value = (@hash := round(round(mod((@nextval * factor), divisor) / 1000000000, 9) * 1000000000, 0))
where sequence_name = in_seq_name;
--
return round(@hash);
--
end$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `albums`
--

DROP TABLE IF EXISTS `albums`;
CREATE TABLE IF NOT EXISTS `albums` (
  `album_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `name` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `description` mediumtext COLLATE utf8_bin,
  `location` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `visibility` varchar(20) COLLATE utf8_bin DEFAULT NULL,
  `creation_date` datetime DEFAULT NULL,
  `last_update_date` datetime DEFAULT NULL,
  PRIMARY KEY (`album_id`),
  UNIQUE KEY `albums_uk1` (`user_id`,`name`),
  KEY `albums_fk1` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=28 ;

-- --------------------------------------------------------

--
-- Table structure for table `cfg_countries`
--

DROP TABLE IF EXISTS `cfg_countries`;
CREATE TABLE IF NOT EXISTS `cfg_countries` (
  `country_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `iso_code` char(2) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `name` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`country_id`),
  UNIQUE KEY `COUNTRIES_FK1` (`iso_code`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=252 ;

-- --------------------------------------------------------

--
-- Table structure for table `cfg_locations`
--

DROP TABLE IF EXISTS `cfg_locations`;
CREATE TABLE IF NOT EXISTS `cfg_locations` (
  `location_id` int(10) unsigned NOT NULL,
  `country_id` char(2) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `region` char(2) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `city` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `postal_code` char(5) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `latitude` float DEFAULT NULL,
  `longitude` float DEFAULT NULL,
  `metro_code` int(11) DEFAULT NULL,
  `area_code` int(11) DEFAULT NULL,
  PRIMARY KEY (`location_id`),
  KEY `LOCATION_FK1` (`country_id`),
  KEY `LOCATION_IDX1` (`city`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `cfg_profiles`
--

DROP TABLE IF EXISTS `cfg_profiles`;
CREATE TABLE IF NOT EXISTS `cfg_profiles` (
  `cfg_profile_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `cfg_profile_category_id` int(11) unsigned NOT NULL,
  `name` varchar(50) COLLATE utf8_bin NOT NULL,
  `display_name` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `description` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  `creation_date` datetime DEFAULT NULL,
  PRIMARY KEY (`cfg_profile_id`),
  KEY `cfg_profiles_uk1` (`cfg_profile_category_id`,`name`),
  KEY `cfg_profiles_idx1` (`name`),
  KEY `cfg_profiles_fk1` (`cfg_profile_category_id`),
  KEY `cfg_profiles_idx2` (`display_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=54 ;

-- --------------------------------------------------------

--
-- Table structure for table `cfg_profile_categories`
--

DROP TABLE IF EXISTS `cfg_profile_categories`;
CREATE TABLE IF NOT EXISTS `cfg_profile_categories` (
  `cfg_profile_category_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) COLLATE utf8_bin NOT NULL,
  `display_name` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`cfg_profile_category_id`),
  KEY `cfg_profile_categories_idx1` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- Table structure for table `cfg_sequences`
--

DROP TABLE IF EXISTS `cfg_sequences`;
CREATE TABLE IF NOT EXISTS `cfg_sequences` (
  `sequence_name` varchar(32) COLLATE utf8_bin NOT NULL,
  `hash_value` int(11) unsigned NOT NULL,
  `current_value` int(11) unsigned NOT NULL,
  `factor` int(11) unsigned NOT NULL,
  `divisor` int(11) unsigned NOT NULL,
  PRIMARY KEY (`sequence_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

DROP TABLE IF EXISTS `comments`;
CREATE TABLE IF NOT EXISTS `comments` (
  `comment_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `status_id` int(11) unsigned DEFAULT NULL,
  `photo_id` int(11) unsigned DEFAULT NULL,
  `link_id` int(11) unsigned DEFAULT NULL,
  `parent_id` int(11) unsigned DEFAULT NULL,
  `from_id` int(11) unsigned NOT NULL,
  `message` text COLLATE utf8_bin,
  `user_likes` tinyint(1) NOT NULL DEFAULT '1',
  `can_like` tinyint(1) DEFAULT '1',
  `can_remove` tinyint(1) DEFAULT '1',
  `is_private` tinyint(1) DEFAULT '0',
  `type` varchar(10) COLLATE utf8_bin NOT NULL DEFAULT 'comment',
  `creation_date` datetime DEFAULT NULL,
  PRIMARY KEY (`comment_id`),
  KEY `comment_idx1` (`creation_date`),
  KEY `comments_fk1` (`status_id`),
  KEY `comments_fk2` (`from_id`),
  KEY `comments_fk3` (`photo_id`),
  KEY `comments_fk4` (`link_id`),
  KEY `comments_fk5` (`parent_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=10 ;

-- --------------------------------------------------------

--
-- Table structure for table `conversation`
--

DROP TABLE IF EXISTS `conversation`;
CREATE TABLE IF NOT EXISTS `conversation` (
  `conversation_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `creation_date` datetime DEFAULT NULL,
  `last_update_date` datetime DEFAULT NULL,
  PRIMARY KEY (`conversation_id`),
  KEY `conversation_idx1` (`last_update_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `friend`
--

DROP TABLE IF EXISTS `friend`;
CREATE TABLE IF NOT EXISTS `friend` (
  `user1_id` int(11) unsigned NOT NULL,
  `user2_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`user1_id`,`user2_id`),
  KEY `friend_fk1` (`user1_id`),
  KEY `user2_id` (`user2_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `friend_list`
--

DROP TABLE IF EXISTS `friend_list`;
CREATE TABLE IF NOT EXISTS `friend_list` (
  `friend_list_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `owner` int(11) unsigned NOT NULL,
  `name` varchar(25) COLLATE utf8_bin DEFAULT NULL,
  `type` varchar(25) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`friend_list_id`),
  KEY `friend_list_fk1` (`owner`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `friend_request`
--

DROP TABLE IF EXISTS `friend_request`;
CREATE TABLE IF NOT EXISTS `friend_request` (
  `friend_request_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_from_id` int(11) unsigned NOT NULL,
  `user_to_id` int(11) unsigned NOT NULL,
  `unread` tinyint(1) DEFAULT '1',
  `message` mediumtext COLLATE utf8_bin,
  `creation_date` datetime DEFAULT NULL,
  PRIMARY KEY (`friend_request_id`),
  KEY `friend_request_idx1` (`unread`),
  KEY `user_from_id` (`user_from_id`),
  KEY `user_to_id` (`user_to_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `interests`
--

DROP TABLE IF EXISTS `interests`;
CREATE TABLE IF NOT EXISTS `interests` (
  `interest_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `trip_id` int(11) unsigned DEFAULT NULL,
  `cfg_profile_id` int(10) unsigned NOT NULL,
  `creation_date` datetime DEFAULT NULL,
  PRIMARY KEY (`interest_id`),
  KEY `interests_fk1` (`cfg_profile_id`),
  KEY `interests_fk2` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=61 ;

-- --------------------------------------------------------

--
-- Table structure for table `likes`
--

DROP TABLE IF EXISTS `likes`;
CREATE TABLE IF NOT EXISTS `likes` (
  `like_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `from_id` int(11) unsigned NOT NULL,
  `status_id` int(11) unsigned DEFAULT NULL,
  `comment_id` int(11) unsigned DEFAULT NULL,
  `link_id` int(11) unsigned DEFAULT NULL,
  `photo_id` int(11) unsigned DEFAULT NULL,
  `type` varchar(10) COLLATE utf8_bin NOT NULL DEFAULT 'like',
  `creation_date` datetime DEFAULT NULL,
  PRIMARY KEY (`like_id`),
  KEY `like_idx1` (`creation_date`),
  KEY `likes_fk4` (`photo_id`),
  KEY `likes_fk1` (`from_id`),
  KEY `likes_fk2` (`status_id`),
  KEY `likes_fk3` (`comment_id`),
  KEY `likes_fk5` (`link_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=33 ;

-- --------------------------------------------------------

--
-- Table structure for table `links`
--

DROP TABLE IF EXISTS `links`;
CREATE TABLE IF NOT EXISTS `links` (
  `link_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `from_id` int(11) unsigned NOT NULL,
  `link` varchar(255) COLLATE utf8_bin NOT NULL,
  `name` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `description` text COLLATE utf8_bin,
  `icon` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `picture` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `message` text COLLATE utf8_bin,
  `visibility` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `type` varchar(10) COLLATE utf8_bin DEFAULT 'link',
  `creation_date` datetime DEFAULT NULL,
  `last_update_date` datetime DEFAULT NULL,
  PRIMARY KEY (`link_id`),
  KEY `links_idx1` (`visibility`),
  KEY `links_idx2` (`creation_date`),
  KEY `links_fk1` (`from_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `log_administrator`
--

DROP TABLE IF EXISTS `log_administrator`;
CREATE TABLE IF NOT EXISTS `log_administrator` (
  `log_administrator_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned DEFAULT NULL,
  `travel_log_id` int(11) unsigned DEFAULT NULL,
  `role` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `created_by` int(11) unsigned DEFAULT NULL,
  `creation_date` datetime DEFAULT NULL,
  `last_update_date` datetime DEFAULT NULL,
  PRIMARY KEY (`log_administrator_id`),
  UNIQUE KEY `log_administrator_uk1` (`user_id`,`travel_log_id`),
  KEY `log_administrator_fk1` (`user_id`),
  KEY `log_administrator_fk2` (`travel_log_id`),
  KEY `log_administrator_fk3` (`created_by`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
CREATE TABLE IF NOT EXISTS `notifications` (
  `notification_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `sender_id` int(11) unsigned NOT NULL,
  `recipient_id` int(11) unsigned NOT NULL,
  `object_type` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `title_html` text COLLATE utf8_bin,
  `title_text` text COLLATE utf8_bin,
  `body_html` text COLLATE utf8_bin,
  `body_text` text COLLATE utf8_bin,
  `is_unread` tinyint(1) DEFAULT '1',
  `is_hidden` tinyint(1) DEFAULT '0',
  `creation_date` datetime DEFAULT NULL,
  `last_update_date` datetime DEFAULT NULL,
  PRIMARY KEY (`notification_id`),
  KEY `notifications_idx1` (`is_unread`),
  KEY `notifications_idx2` (`is_hidden`),
  KEY `notifications_idx3` (`creation_date`),
  KEY `notifications_fk1` (`sender_id`),
  KEY `notifications_fk2` (`recipient_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=23 ;

-- --------------------------------------------------------

--
-- Table structure for table `offers`
--

DROP TABLE IF EXISTS `offers`;
CREATE TABLE IF NOT EXISTS `offers` (
  `offer_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `from_id` int(11) unsigned DEFAULT NULL,
  `title` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `message` text COLLATE utf8_bin,
  `expiration_date` datetime DEFAULT NULL,
  `terms` text COLLATE utf8_bin,
  `image_id` int(11) unsigned DEFAULT NULL,
  `image_url` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `claim_limit` int(3) unsigned DEFAULT '1',
  `coupon_type` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `redemption_link` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `redemption_code` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `visibility` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `type` varchar(25) COLLATE utf8_bin DEFAULT 'offer',
  `creation_date` datetime DEFAULT NULL,
  `last_update_date` datetime DEFAULT NULL,
  PRIMARY KEY (`offer_id`),
  UNIQUE KEY `offers_uk1` (`from_id`,`title`),
  KEY `offers_idx1` (`creation_date`),
  KEY `offers_fk1` (`from_id`),
  KEY `image_id` (`image_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `photos`
--

DROP TABLE IF EXISTS `photos`;
CREATE TABLE IF NOT EXISTS `photos` (
  `photo_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `album_id` int(11) unsigned DEFAULT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `caption` mediumtext COLLATE utf8_bin,
  `place` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `icon` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `picture` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `src` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `src_height` int(4) DEFAULT NULL,
  `src_width` int(4) DEFAULT NULL,
  `link` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `position` int(5) DEFAULT NULL,
  `visibility` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `type` varchar(20) COLLATE utf8_bin NOT NULL DEFAULT 'photo',
  `creation_date` datetime DEFAULT NULL,
  `last_update_date` datetime DEFAULT NULL,
  PRIMARY KEY (`photo_id`),
  UNIQUE KEY `photos_uk1` (`album_id`,`src`),
  KEY `photos_idx1` (`creation_date`),
  KEY `photos_fk1` (`album_id`),
  KEY `photos_fk2` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=37 ;

-- --------------------------------------------------------

--
-- Table structure for table `photo_tags`
--

DROP TABLE IF EXISTS `photo_tags`;
CREATE TABLE IF NOT EXISTS `photo_tags` (
  `tag_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `photo_id` int(11) unsigned NOT NULL,
  `subject_id` int(11) unsigned NOT NULL,
  `message` text COLLATE utf8_bin,
  `x_coord` int(5) DEFAULT '0',
  `y_coord` int(5) DEFAULT '0',
  `type` varchar(10) COLLATE utf8_bin DEFAULT 'tag',
  `creation_date` datetime DEFAULT NULL,
  PRIMARY KEY (`tag_id`),
  KEY `tags_idx1` (`creation_date`),
  KEY `tags_fk1` (`photo_id`),
  KEY `tags_fk2` (`subject_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `status`
--

DROP TABLE IF EXISTS `status`;
CREATE TABLE IF NOT EXISTS `status` (
  `status_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `from_id` int(11) unsigned NOT NULL,
  `to_id` int(11) unsigned DEFAULT NULL,
  `message` text COLLATE utf8_bin NOT NULL,
  `place` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `visibility` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `type` varchar(10) COLLATE utf8_bin DEFAULT 'status',
  `creation_date` datetime DEFAULT NULL,
  `last_update_date` datetime DEFAULT NULL,
  PRIMARY KEY (`status_id`),
  KEY `status_idx1` (`visibility`),
  KEY `status_idx2` (`creation_date`),
  KEY `status_fk1` (`from_id`),
  KEY `status_fk2` (`to_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Table structure for table `traveloti`
--

DROP TABLE IF EXISTS `traveloti`;
CREATE TABLE IF NOT EXISTS `traveloti` (
  `traveloti_id` int(11) unsigned NOT NULL DEFAULT '1',
  `username` varchar(75) COLLATE utf8_bin DEFAULT NULL,
  `type` varchar(32) COLLATE utf8_bin DEFAULT NULL,
  `picture_id` int(11) unsigned DEFAULT NULL,
  `cover_picture_id` int(11) unsigned DEFAULT NULL,
  `creation_date` datetime DEFAULT NULL,
  `last_update_date` datetime DEFAULT NULL,
  PRIMARY KEY (`traveloti_id`),
  UNIQUE KEY `traveloti_idx2` (`username`),
  KEY `traveloti_idx1` (`type`),
  KEY `traveloti_fk1` (`picture_id`),
  KEY `cover_picture_id` (`cover_picture_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Triggers `traveloti`
--
DROP TRIGGER IF EXISTS `traveloti_trg_bri`;
DELIMITER //
CREATE TRIGGER `traveloti_trg_bri` BEFORE INSERT ON `traveloti`
 FOR EACH ROW begin
--
declare l_sysdate datetime default sysdate();
--
-- Set creation dates
--
if new.creation_date is null then
	set new.creation_date := l_sysdate;
end if;
if new.last_update_date is null then
	set new.last_update_date := l_sysdate;
end if;
--
end
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `travel_log`
--

DROP TABLE IF EXISTS `travel_log`;
CREATE TABLE IF NOT EXISTS `travel_log` (
  `traveloti_id` int(11) unsigned NOT NULL,
  `name` varchar(50) COLLATE utf8_bin NOT NULL,
  `link` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `category` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `is_published` tinyint(1) DEFAULT '1',
  `can_post` tinyint(1) DEFAULT '1',
  `num_likes` int(5) DEFAULT '0',
  `location` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8_bin DEFAULT NULL,
  `num_checkins` int(5) DEFAULT '0',
  `website` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `about` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `general_info` text COLLATE utf8_bin,
  PRIMARY KEY (`traveloti_id`),
  UNIQUE KEY `travel_log_uk1` (`traveloti_id`,`name`),
  KEY `travel_log_idx2` (`category`),
  KEY `travel_log_idx1` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `traveloti_id` int(11) unsigned NOT NULL,
  `user_type` varchar(50) COLLATE utf8_bin NOT NULL DEFAULT 'Traveler',
  `first_name` varchar(20) COLLATE utf8_bin DEFAULT NULL,
  `middle_name` varchar(20) COLLATE utf8_bin DEFAULT NULL,
  `last_name` varchar(20) COLLATE utf8_bin NOT NULL,
  `alternate_name` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `show_alternate_name` tinyint(1) DEFAULT '0',
  `display_name` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `display_name_order` varchar(4) COLLATE utf8_bin DEFAULT 'fnln',
  `email` varchar(255) COLLATE utf8_bin NOT NULL,
  `password` varchar(128) COLLATE utf8_bin NOT NULL,
  `has_silhouette` tinyint(1) DEFAULT '0',
  `gender` varchar(2) COLLATE utf8_bin DEFAULT NULL,
  `email_preset` tinyint(1) DEFAULT '0',
  `notifications` tinyint(1) DEFAULT '0',
  `tag_notifications` tinyint(1) DEFAULT '0',
  `certification` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `specialization` text COLLATE utf8_bin,
  `expert_since` date DEFAULT NULL,
  `expert_name` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `address1` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `address2` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `city` varchar(60) COLLATE utf8_bin DEFAULT NULL,
  `region` varchar(2) COLLATE utf8_bin DEFAULT NULL,
  `postal_code` varchar(9) COLLATE utf8_bin DEFAULT NULL,
  `telephone` varchar(15) COLLATE utf8_bin DEFAULT NULL,
  `business_hours` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `biography` text COLLATE utf8_bin,
  `testimonial` text COLLATE utf8_bin,
  `link_website` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `link_facebook` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `link_twitter` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `link_linkedin` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `link_youtube` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `link_blog` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`traveloti_id`),
  UNIQUE KEY `users_uk1` (`email`),
  KEY `users_idx3` (`gender`),
  KEY `users_idx2` (`last_name`),
  KEY `users_idx1` (`first_name`),
  KEY `users_idx4` (`user_type`),
  KEY `users_idx5` (`expert_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `user_provider`
--

DROP TABLE IF EXISTS `user_provider`;
CREATE TABLE IF NOT EXISTS `user_provider` (
  `user_id` int(11) unsigned NOT NULL,
  `provider_id` varchar(50) COLLATE utf8_bin NOT NULL,
  `provider` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`user_id`,`provider_id`),
  UNIQUE KEY `user_provider_uk1` (`provider_id`,`provider`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `albums`
--
ALTER TABLE `albums`
  ADD CONSTRAINT `albums_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `traveloti` (`traveloti_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `cfg_locations`
--
ALTER TABLE `cfg_locations`
  ADD CONSTRAINT `cfg_locations_ibfk_1` FOREIGN KEY (`country_id`) REFERENCES `cfg_countries` (`iso_code`);

--
-- Constraints for table `cfg_profiles`
--
ALTER TABLE `cfg_profiles`
  ADD CONSTRAINT `cfg_profiles_ibfk_1` FOREIGN KEY (`cfg_profile_category_id`) REFERENCES `cfg_profile_categories` (`cfg_profile_category_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_10` FOREIGN KEY (`parent_id`) REFERENCES `comments` (`comment_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `comments_ibfk_11` FOREIGN KEY (`status_id`) REFERENCES `status` (`status_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `comments_ibfk_12` FOREIGN KEY (`from_id`) REFERENCES `traveloti` (`traveloti_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `comments_ibfk_3` FOREIGN KEY (`photo_id`) REFERENCES `photos` (`photo_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `comments_ibfk_9` FOREIGN KEY (`link_id`) REFERENCES `links` (`link_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `friend`
--
ALTER TABLE `friend`
  ADD CONSTRAINT `friend_ibfk_1` FOREIGN KEY (`user1_id`) REFERENCES `user` (`traveloti_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `friend_ibfk_2` FOREIGN KEY (`user2_id`) REFERENCES `user` (`traveloti_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `friend_list`
--
ALTER TABLE `friend_list`
  ADD CONSTRAINT `friend_list_ibfk_1` FOREIGN KEY (`owner`) REFERENCES `user` (`traveloti_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `friend_request`
--
ALTER TABLE `friend_request`
  ADD CONSTRAINT `friend_request_ibfk_1` FOREIGN KEY (`user_from_id`) REFERENCES `user` (`traveloti_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `friend_request_ibfk_2` FOREIGN KEY (`user_to_id`) REFERENCES `user` (`traveloti_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `interests`
--
ALTER TABLE `interests`
  ADD CONSTRAINT `interests_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `traveloti` (`traveloti_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `interests_ibfk_4` FOREIGN KEY (`cfg_profile_id`) REFERENCES `cfg_profiles` (`cfg_profile_id`);

--
-- Constraints for table `likes`
--
ALTER TABLE `likes`
  ADD CONSTRAINT `likes_ibfk_10` FOREIGN KEY (`comment_id`) REFERENCES `comments` (`comment_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `likes_ibfk_11` FOREIGN KEY (`status_id`) REFERENCES `status` (`status_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `likes_ibfk_12` FOREIGN KEY (`link_id`) REFERENCES `links` (`link_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `likes_ibfk_13` FOREIGN KEY (`from_id`) REFERENCES `traveloti` (`traveloti_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `likes_ibfk_4` FOREIGN KEY (`photo_id`) REFERENCES `photos` (`photo_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `links`
--
ALTER TABLE `links`
  ADD CONSTRAINT `links_ibfk_1` FOREIGN KEY (`from_id`) REFERENCES `traveloti` (`traveloti_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `log_administrator`
--
ALTER TABLE `log_administrator`
  ADD CONSTRAINT `log_administrator_ibfk_5` FOREIGN KEY (`user_id`) REFERENCES `user` (`traveloti_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `log_administrator_ibfk_6` FOREIGN KEY (`travel_log_id`) REFERENCES `travel_log` (`traveloti_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `log_administrator_ibfk_7` FOREIGN KEY (`created_by`) REFERENCES `user` (`traveloti_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_10` FOREIGN KEY (`recipient_id`) REFERENCES `traveloti` (`traveloti_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `notifications_ibfk_9` FOREIGN KEY (`sender_id`) REFERENCES `traveloti` (`traveloti_id`);

--
-- Constraints for table `offers`
--
ALTER TABLE `offers`
  ADD CONSTRAINT `offers_ibfk_5` FOREIGN KEY (`from_id`) REFERENCES `travel_log` (`traveloti_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `offers_ibfk_6` FOREIGN KEY (`image_id`) REFERENCES `photos` (`photo_id`);

--
-- Constraints for table `photos`
--
ALTER TABLE `photos`
  ADD CONSTRAINT `photos_ibfk_6` FOREIGN KEY (`user_id`) REFERENCES `traveloti` (`traveloti_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `photos_ibfk_7` FOREIGN KEY (`album_id`) REFERENCES `albums` (`album_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `photo_tags`
--
ALTER TABLE `photo_tags`
  ADD CONSTRAINT `photo_tags_ibfk_5` FOREIGN KEY (`photo_id`) REFERENCES `photos` (`photo_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `photo_tags_ibfk_6` FOREIGN KEY (`subject_id`) REFERENCES `user` (`traveloti_id`);

--
-- Constraints for table `status`
--
ALTER TABLE `status`
  ADD CONSTRAINT `status_ibfk_1` FOREIGN KEY (`from_id`) REFERENCES `traveloti` (`traveloti_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `status_ibfk_2` FOREIGN KEY (`to_id`) REFERENCES `traveloti` (`traveloti_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `traveloti`
--
ALTER TABLE `traveloti`
  ADD CONSTRAINT `traveloti_ibfk_1` FOREIGN KEY (`picture_id`) REFERENCES `photos` (`photo_id`),
  ADD CONSTRAINT `traveloti_ibfk_2` FOREIGN KEY (`cover_picture_id`) REFERENCES `photos` (`photo_id`);

--
-- Constraints for table `travel_log`
--
ALTER TABLE `travel_log`
  ADD CONSTRAINT `travel_log_ibfk_2` FOREIGN KEY (`traveloti_id`) REFERENCES `traveloti` (`traveloti_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `user_ibfk_1` FOREIGN KEY (`traveloti_id`) REFERENCES `traveloti` (`traveloti_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_provider`
--
ALTER TABLE `user_provider`
  ADD CONSTRAINT `user_provider_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `traveloti` (`traveloti_id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
