SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `cmstop_space`;
CREATE TABLE `cmstop_space` (
  `spaceid` mediumint(8) unsigned NOT NULL auto_increment,
  `name` varchar(50) NOT NULL,
  `author` varchar(40) default NULL,
  `initial` varchar(1) NOT NULL,
  `alias` varchar(20) default NULL,
  `photo` varchar(200) default NULL,
  `description` varchar(255) NOT NULL,
  `userid` mediumint(8) unsigned default NULL,
  `created` int(10) unsigned NOT NULL,
  `createdby` mediumint(8) unsigned NOT NULL,
  `modified` int(10) unsigned default NULL,
  `modifiedby` mediumint(8) unsigned default NULL,
  `status` tinyint(1) unsigned NOT NULL default '0',
  `sort` tinyint(3) unsigned NOT NULL default '0',
  `posts` smallint(5) unsigned NOT NULL default '0',
  `comments` smallint(5) unsigned NOT NULL default '0',
  `pv` mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`spaceid`),
  UNIQUE KEY `userid` (`userid`),
  UNIQUE KEY `alias` (`alias`),
  UNIQUE KEY `author` (`author`),
  KEY `initial` (`initial`,`status`),
  KEY `status` (`status`,`pv`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `cmstop_aca` (`acaid`, `parentid`, `app`, `controller`, `action`, `name`) VALUES
(907, NULL, 'space', NULL, NULL, '专栏');

ALTER TABLE `cmstop_space`
  ADD CONSTRAINT `cmstop_space_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `cmstop_member` (`userid`) ON DELETE CASCADE;