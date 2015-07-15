SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `cmstop_push`;
DROP TABLE IF EXISTS `cmstop_push_rule`;
DROP TABLE IF EXISTS `cmstop_push_task`;

DELETE FROM `cmstop_aca` WHERE `app`='push';