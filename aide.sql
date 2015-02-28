CREATE DATABASE IF NOT EXISTS `aide`;

USE `aide`;

CREATE TABLE IF NOT EXISTS `users` (
  `user_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `first_name` VARCHAR(50) NOT NULL,
  `last_name` VARCHAR(50) NOT NULL,
  `email_address` VARCHAR(255) NOT NULL UNIQUE,
  `password` TEXT NOT NULL,
  `profile_picture` TEXT NOT NULL,
  `sex` CHAR(1) NOT NULL,
#   `longitude` VARCHAR(50) NOT NULL,
#   `latitude` VARCHAR(50) NOT NULL,
  `phone_number` VARCHAR(25) NOT NULL,
  `address` TEXT NOT NULL,
  `api_key` TEXT NOT NULL,
  `created_time` DATETIME NOT NULL,
  `modified_time` DATETIME NOT NULL,
  `active_status` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `kins` (
  `kin_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `first_name` VARCHAR(50) NOT NULL,
  `last_name` VARCHAR(50) NOT NULL,
  `phone_number` VARCHAR(25) NOT NULL,
  `email_address` VARCHAR(255) NOT NULL,
  `address` TEXT NOT NULL,
  `created_time` DATETIME NOT NULL,
  `modified_time` DATETIME NOT NULL,
  `active_status` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
  PRIMARY KEY (`kin_id`),
  CONSTRAINT `fk_kins_user_id` FOREIGN KEY (`user_id`) REFERENCES users(`user_id`)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `service_types` (
  `service_type_id` TINYINT(1) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(15) NOT NULL,
  PRIMARY KEY (`service_type_id`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `service_statuses` (
  `service_status_id` TINYINT(1) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(15) NOT NULL,
  PRIMARY KEY (`service_status_id`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `request_statuses` (
  `request_status_id` TINYINT(1) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(10) NOT NULL,
  PRIMARY KEY (`request_status_id`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `notification_types` (
  `notification_type_id` TINYINT(1) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(10) NOT NULL,
  PRIMARY KEY (`notification_type_id`)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `providers` (
  `provider_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(50) NOT NULL,
  `email_address` VARCHAR(255) NOT NULL UNIQUE,
  `password` TEXT NOT NULL,
  `profile_picture` TEXT NOT NULL,
  `longitude` VARCHAR(50) NOT NULL,
  `latitude` VARCHAR(50) NOT NULL,
  `phone_number` VARCHAR(25) NOT NULL,
  `address` TEXT NOT NULL,
  `service_type_id` TINYINT(1) UNSIGNED NOT NULL,
  `api_key` TEXT NOT NULL,
  `created_time` DATETIME NOT NULL,
  `modified_time` DATETIME NOT NULL,
  `active_status` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
  PRIMARY KEY (`provider_id`),
  CONSTRAINT `fk_providers_service_type_id` FOREIGN KEY (`service_type_id`) REFERENCES service_types(`service_type_id`)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `requests` (
  `request_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `device_id` LONGTEXT NOT NULL,
  `longitude` VARCHAR(50) NOT NULL,
  `latitude` VARCHAR(50) NOT NULL,
  `address` TEXT NOT NULL,
  `service_type_id` TINYINT(1) UNSIGNED NOT NULL,
  `service_status_id` TINYINT(1) UNSIGNED NOT NULL DEFAULT 2,
  `created_time` DATETIME NOT NULL,
  `modified_time` DATETIME NOT NULL,
  `active_status` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
  PRIMARY KEY (`request_id`),
  CONSTRAINT `fk_requests_service_type_id` FOREIGN KEY (`service_type_id`) REFERENCES service_types(`service_type_id`)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `fk_requests_service_status_id` FOREIGN KEY (`service_status_id`) REFERENCES service_statuses(`service_status_id`)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `request_checks` (
  `request_check_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `request_id` INT UNSIGNED NOT NULL,
  `provider_id` INT UNSIGNED NOT NULL,
  `request_status_id` TINYINT(1) UNSIGNED NOT NULL,
  `created_time` DATETIME NOT NULL,
  `modified_time` DATETIME NOT NULL,
  PRIMARY KEY (`request_check_id`),
  CONSTRAINT `fk_request_checks_request_id` FOREIGN KEY (`request_id`) REFERENCES requests(`request_id`)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `fk_request_checks_provider_id` FOREIGN KEY (`provider_id`) REFERENCES providers(`provider_id`)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `fk_request_checks_request_status_id` FOREIGN KEY (`request_status_id`) REFERENCES request_statuses(`request_status_id`)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `request_chats` (
  `request_chat_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `provider_id` INT UNSIGNED NOT NULL,
  `comment` TEXT NOT NULL,
  `created_time` DATETIME NOT NULL,
  `active_status` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
  PRIMARY KEY (`request_chat_id`),
  CONSTRAINT `fk_request_chats_user_id` FOREIGN KEY (`user_id`) REFERENCES users(`user_id`)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `fk_request_chats_provider_id` FOREIGN KEY (`provider_id`) REFERENCES providers(`provider_id`)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `reviews` (
  `review_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `provider_id` INT UNSIGNED NOT NULL,
  `rating` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  `comment` TEXT NOT NULL,
  `created_time` DATETIME NOT NULL,
  `modified_time` DATETIME NOT NULL,
  `active_status` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
  PRIMARY KEY (`review_id`),
  CONSTRAINT `fk_reviews_user_id` FOREIGN KEY (`user_id`) REFERENCES users(`user_id`)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `fk_reviews_provider_id` FOREIGN KEY (`provider_id`) REFERENCES providers(`provider_id`)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `notifications` (
  `notification_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `own_id` INT UNSIGNED NOT NULL,
  `sub_id` INT UNSIGNED NOT NULL,
  `notification_type_id` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
  `created_time` DATETIME NOT NULL,
  `active_status` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
  PRIMARY KEY (`notification_id`),
  CONSTRAINT `fk_notifications_own_id` FOREIGN KEY (`own_id`) REFERENCES users(`user_id`)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `fk_notifications_sub_id` FOREIGN KEY (`sub_id`) REFERENCES users(`user_id`)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `fk_notification_type_id` FOREIGN KEY (`notification_type_id`) REFERENCES notification_types(`notification_type_id`)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `app_credentials` (
  `app_credential_id` TINYINT(1) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(20) NOT NULL,
  `value` LONGTEXT NOT NULL,
  PRIMARY KEY (`app_credential_id`)
) ENGINE=InnoDB;
