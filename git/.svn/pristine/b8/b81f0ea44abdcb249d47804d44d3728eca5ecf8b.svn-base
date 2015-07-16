SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `cmstop_magazine`;
CREATE TABLE `cmstop_magazine` (
  `mid` smallint(5) unsigned NOT NULL auto_increment,
  `name` varchar(40) NOT NULL,
  `alias` varchar(40) NOT NULL,
  `logo` varchar(100) default NULL,
  `pages` smallint(2) unsigned default NULL,
  `template_list` varchar(100) NOT NULL,
  `template_content` varchar(100) NOT NULL,
  `type` varchar(10) default NULL COMMENT 'eg:月刊|周刊',
  `publish` varchar(30) default NULL COMMENT '发行时间文字描述',
  `created` int(10) unsigned NOT NULL,
  `createdby` mediumint(8) unsigned NOT NULL,
  `ip` varchar(15) NOT NULL,
  `url` varchar(100) default '' COMMENT '官方网站',
  `memo` text,
  `default_year` smallint(4) unsigned default NULL COMMENT '期使用的默认年份缓存',
  `disabled` tinyint(1) unsigned NOT NULL default '1',
  PRIMARY KEY  (`mid`),
  KEY `disabled` (`disabled`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

DROP TABLE IF EXISTS `cmstop_magazine_content`;
CREATE TABLE `cmstop_magazine_content` (
  `mapid` int(10) unsigned NOT NULL auto_increment,
  `pid` mediumint(8) unsigned NOT NULL,
  `eid` smallint(5) unsigned NOT NULL,
  `mid` smallint(5) unsigned NOT NULL,
  `pageno` tinyint(2) unsigned NOT NULL,
  `contentid` mediumint(8) unsigned NOT NULL,
  `sort` tinyint(2) unsigned NOT NULL default '0',
  `pv` mediumint(8) unsigned default '0',
  PRIMARY KEY  (`mapid`),
  KEY `pep` (`pid`,`eid`,`pageno`),
  KEY `eid` (`eid`),
  KEY `mid` (`mid`),
  KEY `contentid` (`contentid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=41 ;

DROP TABLE IF EXISTS `cmstop_magazine_edition`;
CREATE TABLE `cmstop_magazine_edition` (
  `eid` smallint(5) unsigned NOT NULL auto_increment,
  `mid` smallint(5) unsigned NOT NULL,
  `title` varchar(60) default NULL,
  `number` varchar(10) NOT NULL,
  `total_number` varchar(10) default NULL,
  `year` smallint(4) unsigned default NULL,
  `image` varchar(100) default NULL,
  `pdf` varchar(100) default NULL,
  `publish` int(10) unsigned NOT NULL,
  `created` int(10) unsigned NOT NULL,
  `createdby` mediumint(8) unsigned NOT NULL,
  `ip` varchar(15) NOT NULL,
  `url` varchar(100) default '',
  `disabled` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`eid`),
  KEY `mid` (`mid`,`disabled`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

DROP TABLE IF EXISTS `cmstop_magazine_page`;
CREATE TABLE `cmstop_magazine_page` (
  `pid` mediumint(8) unsigned NOT NULL auto_increment,
  `mid` smallint(5) unsigned NOT NULL,
  `eid` smallint(5) unsigned NOT NULL,
  `pageno` tinyint(2) unsigned NOT NULL,
  `name` varchar(30) NOT NULL,
  `editor` varchar(30) NOT NULL,
  `arteditor` varchar(30) NOT NULL,
  PRIMARY KEY  (`pid`),
  KEY `eid` (`eid`,`pageno`),
  KEY `mid` (`mid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

INSERT INTO `cmstop_aca` (`acaid`, `parentid`, `app`, `controller`, `action`, `name`) VALUES
(200, NULL, 'magazine', NULL, NULL, '杂志');


ALTER TABLE `cmstop_magazine_content`
  ADD CONSTRAINT `cmstop_magazine_content_ibfk_1` FOREIGN KEY (`pid`) REFERENCES `cmstop_magazine_page` (`pid`) ON DELETE CASCADE;

ALTER TABLE `cmstop_magazine_edition`
  ADD CONSTRAINT `cmstop_magazine_edition_ibfk_1` FOREIGN KEY (`mid`) REFERENCES `cmstop_magazine` (`mid`) ON DELETE CASCADE;
  
ALTER TABLE `cmstop_magazine_page`
  ADD CONSTRAINT `cmstop_magazine_page_ibfk_1` FOREIGN KEY (`eid`) REFERENCES `cmstop_magazine_edition` (`eid`) ON DELETE CASCADE;