SET FOREIGN_KEY_CHECKS=0;

DELETE FROM `cmstop_cron` WHERE `cronid` IN (SELECT `cronid` FROM `cmstop_history`);
DROP TABLE IF EXISTS `cmstop_history`;
