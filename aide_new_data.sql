CREATE DATABASE IF NOT EXISTS `aide`;
USE `aide`;
#SET FOREIGN_KEY_CHECKS= 0;

CREATE TABLE IF NOT EXISTS `app_credentials` (
  `app_credential_id` tinyint(1) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `value` longtext NOT NULL,
  PRIMARY KEY (`app_credential_id`)
) ENGINE=InnoDB ;

CREATE TABLE IF NOT EXISTS `kins` (
  `kin_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `phone_number` varchar(25) NOT NULL,
  `email_address` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `created_time` datetime NOT NULL,
  `modified_time` datetime NOT NULL,
  `active_status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`kin_id`),
  KEY `fk_kins_user_id` (`user_id`)
) ENGINE=InnoDB ;

CREATE TABLE IF NOT EXISTS `notifications` (
  `notification_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `own_id` int(10) unsigned NOT NULL,
  `sub_id` int(10) unsigned NOT NULL,
  `obj_id` int(10) unsigned NOT NULL,
  `notification_type_id` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `created_time` datetime NOT NULL,
  `active_status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`notification_id`),
  KEY `fk_notifications_own_id` (`own_id`),
  KEY `fk_notifications_sub_id` (`sub_id`),
  KEY `fk_notifications_obj_id` (`obj_id`),
  KEY `fk_notification_type_id` (`notification_type_id`)
) ENGINE=InnoDB ;

CREATE TABLE IF NOT EXISTS `notification_types` (
  `notification_type_id` tinyint(1) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(15) NOT NULL,
  PRIMARY KEY (`notification_type_id`)
) ENGINE=InnoDB ;


INSERT INTO `notification_types` (`notification_type_id`, `name`) VALUES
  (1, 'Approve'),
  (2, 'Decline'),
  (3, 'Request'),
  (4, 'Chat'),
  (5, 'Abort');

CREATE TABLE IF NOT EXISTS `providers` (
  `provider_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `email_address` varchar(255) NOT NULL,
  `password` text NOT NULL,
  `profile_picture` text NOT NULL,
  `longitude` varchar(50) NOT NULL,
  `latitude` varchar(50) NOT NULL,
  `phone_number` varchar(25) NOT NULL,
  `address` text NOT NULL,
  `service_type_id` tinyint(1) unsigned NOT NULL,
  `api_key` text NOT NULL,
  `created_time` datetime NOT NULL,
  `modified_time` datetime NOT NULL,
  `active_status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`provider_id`),
  UNIQUE KEY `email_address` (`email_address`),
  UNIQUE KEY `email_address_2` (`email_address`),
  KEY `fk_providers_service_type_id` (`service_type_id`)
) ENGINE=InnoDB ;

INSERT INTO `providers` (`provider_id`, `name`, `email_address`, `password`, `profile_picture`, `longitude`, `latitude`, `phone_number`, `address`, `service_type_id`, `api_key`, `created_time`, `modified_time`, `active_status`) VALUES
  (1, 'OAU Health Centre', 'odumuyiwaleye@yahoo.com', '$2a$10$2548eb1079fd5c65feacduhwsUBDRQjfoGIC54n/69CUzQOBA2ATa', '', '4.516332', '7.520190', '08059509015', 'Obafemi Awolowo University Health Centre', 3, '74bd5ea2f1367ab03f25298789789c54', '2015-02-27 05:15:17', '0000-00-00 00:00:00', 1),
  (2, 'OAU Fire Station', 'odumuyiwaleye@gmail.com', '$2a$10$2548eb1079fd5c65feacduhwsUBDRQjfoGIC54n/69CUzQOBA2ATa', '', '4.523549', '7.515575', '08160301215', 'OAU''s Fire station close to the banking area', 1, '39bd5ea2f1367ab03f25298789789c54', '2015-02-28 07:25:20', '0000-00-00 00:00:00', 1),
  (3, 'OAU Crackers', 'dynamicmax5000@gmail.com', '$2a$10$2548eb1079fd5c65feacduhwsUBDRQjfoGIC54n/69CUzQOBA2ATa', '', '4.524041', '7.518485', '07062802882', 'Obafemi Awolowo University Senate Building', 2, '45bd5ea2f1367ab03f25298789789c54', '2015-02-28 20:50:47', '0000-00-00 00:00:00', 1),
  (4, 'Student Union Government', 'mayaki.matthew005@gmail.com', '$2a$10$2548eb1079fd5c65feacduhwsUBDRQjfoGIC54n/69CUzQOBA2ATa', '', '4.521597', '7.518121', '08024071610', 'Obafemi Awolowo University (OAU) Student Union Building', 2, '86bd5ea2f1367ab03f25298789789c54', '2015-02-27 18:43:39', '0000-00-00 00:00:00', 1),
  (5, 'Water', 'odumuyiwaleye@yahooer.com', '$2a$10$2548eb1079fd5c65feacduhwsUBDRQjfoGIC54n/69CUzQOBA2ATa', '', '4.521565', '7.518158', '08059509015', 'Testing a dummy provider\'s credentials', 3, '29bd5ea2f1367ab03f25298789789c54', '2015-03-03 20:28:26', '0000-00-00 00:00:00', 1);

CREATE TABLE IF NOT EXISTS `requests` (
  `request_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `device_id` longtext NOT NULL,
  `longitude` varchar(50) NOT NULL,
  `latitude` varchar(50) NOT NULL,
  `address` text NOT NULL,
  `service_type_id` tinyint(1) unsigned NOT NULL,
  `service_status_id` tinyint(1) unsigned NOT NULL DEFAULT '2',
  `created_time` datetime NOT NULL,
  `modified_time` datetime NOT NULL,
  `active_status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`request_id`),
  KEY `fk_requests_user_id` (`user_id`),
  KEY `fk_requests_service_type_id` (`service_type_id`),
  KEY `fk_requests_service_status_id` (`service_status_id`)
) ENGINE=InnoDB ;

CREATE TABLE IF NOT EXISTS `request_chats` (
  `request_chat_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `provider_id` int(10) unsigned NOT NULL,
  `comment` text NOT NULL,
  `created_time` datetime NOT NULL,
  `active_status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`request_chat_id`),
  KEY `fk_request_chats_user_id` (`user_id`),
  KEY `fk_request_chats_provider_id` (`provider_id`)
) ENGINE=InnoDB ;

CREATE TABLE IF NOT EXISTS `request_checks` (
  `request_check_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `request_id` int(10) unsigned NOT NULL,
  `provider_id` int(10) unsigned NOT NULL,
  `request_status_id` tinyint(1) unsigned NOT NULL,
  `created_time` datetime NOT NULL,
  `modified_time` datetime NOT NULL,
  PRIMARY KEY (`request_check_id`),
  KEY `fk_request_checks_request_id` (`request_id`),
  KEY `fk_request_checks_provider_id` (`provider_id`),
  KEY `fk_request_checks_request_status_id` (`request_status_id`)
) ENGINE=InnoDB ;

CREATE TABLE IF NOT EXISTS `request_statuses` (
  `request_status_id` tinyint(1) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(15) NOT NULL,
  PRIMARY KEY (`request_status_id`)
) ENGINE=InnoDB ;

INSERT INTO `request_statuses` (`request_status_id`, `name`) VALUES
  (1, 'Pending'),
  (2, 'Approved'),
  (3, 'Declined'),
  (4, 'Aborted');

CREATE TABLE IF NOT EXISTS `reviews` (
  `review_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `request_id` int(10) unsigned NOT NULL,
  `provider_id` int(10) unsigned NOT NULL,
  `rating` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `comment` text NOT NULL,
  `created_time` datetime NOT NULL,
  `modified_time` datetime NOT NULL,
  `active_status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`review_id`),
  KEY `fk_reviews_user_id` (`user_id`),
  KEY `fk_reviews_request_id` (`request_id`),
  KEY `fk_reviews_provider_id` (`provider_id`)
) ENGINE=InnoDB ;

CREATE TABLE IF NOT EXISTS `service_statuses` (
  `service_status_id` tinyint(1) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(15) NOT NULL,
  PRIMARY KEY (`service_status_id`)
) ENGINE=InnoDB ;

INSERT INTO `service_statuses` (`service_status_id`, `name`) VALUES
  (1, 'Completed'),
  (2, 'In-Progress'),
  (3, 'Cancelled');

CREATE TABLE IF NOT EXISTS `service_types` (
  `service_type_id` tinyint(1) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(15) NOT NULL,
  PRIMARY KEY (`service_type_id`)
) ENGINE=InnoDB ;

INSERT INTO `service_types` (`service_type_id`, `name`) VALUES
  (1, 'Fire'),
  (2, 'Crime'),
  (3, 'Medical'),
  (4, 'Automobile'),
  (5, 'SOS');

CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email_address` varchar(255) NOT NULL,
  `password` text NOT NULL,
  `profile_picture` text NOT NULL,
  `sex` varchar(6) NOT NULL,
  `phone_number` varchar(25) NOT NULL,
  `address` text NOT NULL,
  `api_key` text NOT NULL,
  `created_time` datetime NOT NULL,
  `modified_time` datetime NOT NULL,
  `active_status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email_address` (`email_address`)
) ENGINE=InnoDB ;

ALTER TABLE `kins`
ADD CONSTRAINT `fk_kins_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `notifications`
ADD CONSTRAINT `fk_notifications_obj_id` FOREIGN KEY (`obj_id`) REFERENCES `requests` (`request_id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `fk_notifications_own_id` FOREIGN KEY (`own_id`) REFERENCES `providers` (`provider_id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `fk_notifications_sub_id` FOREIGN KEY (`sub_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `fk_notification_type_id` FOREIGN KEY (`notification_type_id`) REFERENCES `notification_types` (`notification_type_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `providers`
ADD CONSTRAINT `fk_providers_service_type_id` FOREIGN KEY (`service_type_id`) REFERENCES `service_types` (`service_type_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `requests`
ADD CONSTRAINT `fk_requests_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `fk_requests_service_status_id` FOREIGN KEY (`service_status_id`) REFERENCES `service_statuses` (`service_status_id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `fk_requests_service_type_id` FOREIGN KEY (`service_type_id`) REFERENCES `service_types` (`service_type_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `request_chats`
ADD CONSTRAINT `fk_request_chats_provider_id` FOREIGN KEY (`provider_id`) REFERENCES `providers` (`provider_id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `fk_request_chats_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `request_checks`
ADD CONSTRAINT `fk_request_checks_provider_id` FOREIGN KEY (`provider_id`) REFERENCES `providers` (`provider_id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `fk_request_checks_request_id` FOREIGN KEY (`request_id`) REFERENCES `requests` (`request_id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `fk_request_checks_request_status_id` FOREIGN KEY (`request_status_id`) REFERENCES `request_statuses` (`request_status_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `reviews`
ADD CONSTRAINT `fk_reviews_provider_id` FOREIGN KEY (`provider_id`) REFERENCES `providers` (`provider_id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `fk_reviews_request_id` FOREIGN KEY (`request_id`) REFERENCES `requests` (`request_id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `fk_reviews_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;