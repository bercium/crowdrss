
CREATE TABLE IF NOT EXISTS `rating_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `data` text COLLATE utf8_unicode_ci NOT NULL,
  `time_rated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `rating_history`
--
ALTER TABLE `rating_history`
  ADD CONSTRAINT `rating_history_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;




CREATE TABLE IF NOT EXISTS `project_origcategory` (
`id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `orig_category_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`),
  KEY `orig_category_id` (`orig_category_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Indexes for dumped tables
--


--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `project_origcategory`
--
ALTER TABLE `project_origcategory`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;


ALTER TABLE `project_origcategory` ADD FOREIGN KEY ( `project_id` ) REFERENCES `project` (
`id`
) ON DELETE CASCADE ON UPDATE CASCADE ;

ALTER TABLE `project_origcategory` ADD FOREIGN KEY ( `orig_category_id` ) REFERENCES `orig_category` (
`id`
) ON DELETE CASCADE ON UPDATE CASCADE ;



------------------

ALTER TABLE `project` ADD `removed` BOOLEAN NOT NULL DEFAULT FALSE ;


-
-- Table structure for table `project_featured`
--

CREATE TABLE IF NOT EXISTS `project_featured` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `feature_date` datetime NOT NULL,
  `feature_where` int(11) NOT NULL,
  `show_count` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

ALTER TABLE `project_featured` ADD `active` INT NOT NULL DEFAULT '0';

--
-- Constraints for dumped tables
--

--
-- Constraints for table `project_featured`
--
ALTER TABLE `project_featured`
  ADD CONSTRAINT `project_featured_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;


ALTER TABLE `project` ADD `internal_link` VARCHAR(255) NULL DEFAULT NULL AFTER `link`;


CREATE TABLE IF NOT EXISTS `outside_links` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(128) NOT NULL,
  `sub_category` varchar(128) NOT NULL,
  `title` varchar(256) NOT NULL,
  `link` varchar(256) NOT NULL,
  `position` int(11) DEFAULT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  `time_created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

ALTER TABLE `outside_links` ADD `keywords` VARCHAR( 500 ) NULL DEFAULT NULL AFTER `position` ;
