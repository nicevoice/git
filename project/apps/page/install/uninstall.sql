SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `cmstop_page`;
DROP TABLE IF EXISTS `cmstop_page_priv`;
DROP TABLE IF EXISTS `cmstop_page_stat`;

DELETE FROM `cmstop_aca` WHERE `app`='page';