SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `cmstop_paper`;
DROP TABLE IF EXISTS `cmstop_paper_content`;
DROP TABLE IF EXISTS `cmstop_paper_edition`;
DROP TABLE IF EXISTS `cmstop_paper_edition_page`;

DELETE FROM `cmstop_aca` WHERE `app`='paper';