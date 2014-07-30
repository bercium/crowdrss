ALTER TABLE `platform` ADD `active` BOOLEAN NOT NULL DEFAULT FALSE ;

ALTER TABLE `subscription` ADD `exclude_orig_category` VARCHAR( 255 ) NULL DEFAULT NULL AFTER `category` ;