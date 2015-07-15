SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `cmstop_activity`;
DROP TABLE IF EXISTS `cmstop_activity_sign`;

DELETE FROM `cmstop_aca` WHERE `app`='activity';
DELETE FROM `cmstop_model` WHERE `modelid`='7';