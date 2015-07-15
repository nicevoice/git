SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `cmstop_cdn`;
DROP TABLE IF EXISTS `cmstop_cdn_type`;
DROP TABLE IF EXISTS `cmstop_cdn_rules`;
DROP TABLE IF EXISTS `cmstop_cdn_parameter`;


DELETE FROM `cmstop_setting` WHERE `app`='cdn';
DELETE FROM `cmstop_aca` WHERE `app`='cdn';