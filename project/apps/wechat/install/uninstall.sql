SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `cmstop_digg`;
DROP TABLE IF EXISTS `cmstop_digg_log`;


DELETE FROM `cmstop_setting` WHERE `app`='digg';
DELETE FROM `cmstop_aca` WHERE `app`='digg';