SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `cmstop_interview`;
DROP TABLE IF EXISTS `cmstop_interview_chat`;
DROP TABLE IF EXISTS `cmstop_interview_guest`;
DROP TABLE IF EXISTS `cmstop_interview_question`;

DELETE FROM `cmstop_aca` WHERE `app`='interview';
DELETE FROM `cmstop_model` WHERE `modelid`='5';