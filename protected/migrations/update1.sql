ALTER TABLE `project` CHANGE `time_added` `time_added` DATETIME NULL DEFAULT NULL;

ALTER TABLE `mail_click_log` CHANGE `time_clicked` `time_clicked` DATETIME NULL DEFAULT NULL ;

ALTER TABLE `mail_log` CHANGE `time_send` `time_send` DATETIME NULL DEFAULT NULL ,
CHANGE `time_open` `time_open` DATETIME NULL DEFAULT NULL ;