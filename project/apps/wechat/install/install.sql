SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `cmstop_digg`;
CREATE TABLE `cmstop_digg` (
  `contentid` mediumint(8) unsigned NOT NULL,
  `supports` mediumint(8) unsigned NOT NULL default '0',
  `againsts` mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`contentid`),
  KEY `supports` (`supports`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cmstop_digg_log`;
CREATE TABLE `cmstop_digg_log` (
  `contentid` mediumint(8) unsigned NOT NULL,
  `flag` tinyint(1) unsigned NOT NULL default '0',
  `userid` mediumint(8) unsigned default NULL,
  `username` char(15) default NULL,
  `ip` char(15) NOT NULL,
  `datetime` int(10) unsigned NOT NULL,
  KEY `contentid` (`contentid`,`flag`,`datetime`),
  KEY `userid` (`userid`,`contentid`),
  KEY `ip` (`ip`,`contentid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `cmstop_aca` (`acaid`, `parentid`, `app`, `controller`, `action`, `name`) VALUES
(901, NULL, 'digg', NULL, NULL, 'DIGG');

REPLACE INTO `cmstop_setting` (`app`, `var`, `value`) VALUES
('digg', 'pagesize', '15'),
('digg', 'refresh', '30');

ALTER TABLE `cmstop_digg`
  ADD CONSTRAINT `cmstop_digg_ibfk_1` FOREIGN KEY (`contentid`) REFERENCES `cmstop_article` (`contentid`) ON DELETE CASCADE;
ALTER TABLE `cmstop_digg_log`
  ADD CONSTRAINT `cmstop_digg_log_ibfk_1` FOREIGN KEY (`contentid`) REFERENCES `cmstop_article` (`contentid`) ON DELETE CASCADE;