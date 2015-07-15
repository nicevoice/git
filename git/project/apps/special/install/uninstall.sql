SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `cmstop_place`;
DROP TABLE IF EXISTS `cmstop_place_data`
DROP TABLE IF EXISTS `cmstop_special`;
DROP TABLE IF EXISTS `cmstop_special_page`;

DELETE FROM `cmstop_aca` WHERE `app`='special';
DELETE FROM `cmstop_model` WHERE `modelid`='10';