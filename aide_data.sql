CREATE DATABASE IF NOT EXISTS `aide`;

USE `aide`;
SET FOREIGN_KEY_CHECKS= 0;
INSERT INTO `providers` (`provider_id`, `name`, `email_address`, `password`, `profile_picture`, `longitude`, `latitude`, `phone_number`, `address`, `service_type_id`, `api_key`, `created_time`, `modified_time`, `active_status`) VALUES
  (1, 'OAU Health Centre', 'health@oauife.edu.ng', 'admin', '', '4.516332', '7.520190', '', 'Obafemi Awolowo University Health Centre', 3, '', '2015-02-27 05:15:17', '0000-00-00 00:00:00', 1),
  (3, 'OAU Fire Station', 'fire@oauife.edu.ng', 'admin2', '', '4.523549', '7.515575', '', 'OAU''s Fire station close to the banking area', 1, '', '2015-02-28 07:25:20', '0000-00-00 00:00:00', 1),
  (3, 'OAU Crackers', 'crackers@oauife.edu.ng', 'admin', '', '4.524041', '7.518485', '', 'Obafemi Awolowo University Senate Building', 2, '', '2015-02-28 20:50:47', '0000-00-00 00:00:00', 1),
  (4, 'Student Union Government', 'sug@oauife.edu.ng', 'admin', '', '4.521597', '7.518121', '', 'Obafemi Awolowo University (OAU) Student Union Building', 2, '', '2015-02-27 18:43:39', '0000-00-00 00:00:00', 1);

INSERT INTO `request_statuses` (`request_status_id`, `name`) VALUES
  (1, 'Pending'),
  (2, 'Approved'),
  (3, 'Declined');

INSERT INTO `service_statuses` (`service_status_id`, `name`) VALUES
  (1, 'Completed'),
  (2, 'In-Progress');

INSERT INTO `service_types` (`service_type_id`, `name`) VALUES
  (1, 'Fire'),
  (2, 'Theft'),
  (3, 'Medical'),
  (4, 'Auto-Repair'),
  (5, 'SOS');

SET FOREIGN_KEY_CHECKS= 1;