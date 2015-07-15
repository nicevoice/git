SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `cmstop_video`;

DELETE FROM `cmstop_aca` WHERE `app`='video';
DELETE FROM `cmstop_model` WHERE `modelid`='4';