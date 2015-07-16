SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `cmstop_spider`;
DROP TABLE IF EXISTS `cmstop_spider_history`;
DROP TABLE IF EXISTS `cmstop_spider_rules`;
DROP TABLE IF EXISTS `cmstop_spider_site`;
DROP TABLE IF EXISTS `cmstop_spider_task`;


DELETE FROM `cmstop_aca` WHERE `app`='spider';
