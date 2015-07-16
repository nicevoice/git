SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `cmstop_search`;
CREATE TABLE `cmstop_search` (
  `contentid` mediumint(8) unsigned NOT NULL,
  `content` mediumtext,
  PRIMARY KEY  (`contentid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `cmstop_aca` (`acaid`, `parentid`, `app`, `controller`, `action`, `name`) VALUES
(906, NULL, 'search', NULL, NULL, '搜索');

INSERT INTO `cmstop_setting` (`app`, `var`, `value`) VALUES
('search', 'contentCut', '320'),
('search', 'limit', '1'),
('search', 'open', '1'),
('search', 'pagesize', '10');

ALTER TABLE `cmstop_search`
  ADD CONSTRAINT `cmstop_search_ibfk_1` FOREIGN KEY (`contentid`) REFERENCES `cmstop_content` (`contentid`) ON DELETE CASCADE;