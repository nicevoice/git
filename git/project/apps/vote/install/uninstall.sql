SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `cmstop_vote`;
DROP TABLE IF EXISTS `cmstop_vote_log`;
DROP TABLE IF EXISTS `cmstop_vote_log_data`;
DROP TABLE IF EXISTS `cmstop_vote_option`;

DELETE FROM `cmstop_aca` WHERE `app`='vote';
DELETE FROM `cmstop_model` WHERE `modelid`='8';