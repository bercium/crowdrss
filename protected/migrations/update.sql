
CREATE TABLE IF NOT EXISTS `feed_open_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subscription_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `time_open` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `subscription_id` (`subscription_id`),
  KEY `project_id` (`project_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `feed_open_log`
--
ALTER TABLE `feed_open_log`
  ADD CONSTRAINT `feed_open_log_ibfk_2` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `feed_open_log_ibfk_1` FOREIGN KEY (`subscription_id`) REFERENCES `subscription` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;


CREATE TABLE IF NOT EXISTS `feed_rate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subscription_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `vote` int(11) NOT NULL,
  `time_clicked` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `subscription_id` (`subscription_id`),
  KEY `project_id` (`project_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `feed_rate`
--
ALTER TABLE `feed_rate`
  ADD CONSTRAINT `feed_rate_ibfk_2` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `feed_rate_ibfk_1` FOREIGN KEY (`subscription_id`) REFERENCES `subscription` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;


ALTER TABLE `project` ADD `rating` DOUBLE NULL AFTER `type_of_funding` ;


ALTER TABLE `subscription` ADD `daily_digest` INT NULL AFTER `rss` ,
ADD `weekly_digest` INT NULL AFTER `daily_digest` ;
ADD `rating` INT NULL AFTER `weekly_digest` ;

ALTER TABLE `subscription` CHANGE `daily_digest` `daily_digest` BOOLEAN NULL DEFAULT NULL ,
CHANGE `weekly_digest` `weekly_digest` BOOLEAN NULL DEFAULT NULL ;