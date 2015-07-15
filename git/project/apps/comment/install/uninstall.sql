SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `cmstop_comment`;


DELETE FROM `cmstop_aca` WHERE `app`='comment';
DELETE FROM `cmstop_setting` WHERE `app`='comment';