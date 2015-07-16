SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `cmstop_paper`;
CREATE TABLE `cmstop_paper` (
  `paperid` smallint(5) unsigned NOT NULL auto_increment,
  `name` varchar(50) NOT NULL,
  `alias` varchar(30) NOT NULL,
  `logo` varchar(100) default NULL,
  `pages` smallint(2) unsigned default NULL,
  `template_content` varchar(100) NOT NULL,
  `created` int(10) unsigned NOT NULL,
  `createdby` mediumint(8) unsigned NOT NULL,
  `ip` varchar(15) NOT NULL,
  `url` varchar(100) default 'javascript:;',
  `disabled` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`paperid`),
  KEY `disabled` (`disabled`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;


DROP TABLE IF EXISTS `cmstop_paper_content`;
CREATE TABLE `cmstop_paper_content` (
  `mapid` int(10) unsigned NOT NULL auto_increment,
  `paperid` int(10) unsigned NOT NULL,
  `editionid` int(10) unsigned NOT NULL,
  `pageid` int(10) unsigned NOT NULL,
  `pageno` tinyint(2) unsigned NOT NULL,
  `contentid` mediumint(8) unsigned NOT NULL,
  `coords` varchar(30) NOT NULL,
  `sort` tinyint(2) unsigned NOT NULL default '0',
  `pv` mediumint(9) unsigned default '0',
  PRIMARY KEY  (`mapid`),
  KEY `pageid` (`pageid`),
  KEY `paperid` (`paperid`,`editionid`,`pageno`),
  KEY `editionid` (`editionid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=16 ;

DROP TABLE IF EXISTS `cmstop_paper_edition`;
CREATE TABLE `cmstop_paper_edition` (
  `editionid` int(10) unsigned NOT NULL auto_increment,
  `paperid` smallint(5) unsigned NOT NULL,
  `number` varchar(10) NOT NULL,
  `total_number` varchar(10) default NULL,
  `date` int(10) unsigned NOT NULL,
  `created` int(10) unsigned NOT NULL,
  `createdby` mediumint(8) unsigned NOT NULL,
  `ip` varchar(15) NOT NULL,
  `url` varchar(100) default 'javascript:;',
  `disabled` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`editionid`),
  KEY `paperid` (`paperid`,`disabled`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

DROP TABLE IF EXISTS `cmstop_paper_edition_page`;
CREATE TABLE `cmstop_paper_edition_page` (
  `pageid` int(10) unsigned NOT NULL auto_increment,
  `paperid` int(10) unsigned NOT NULL,
  `editionid` int(10) unsigned NOT NULL,
  `pageno` tinyint(2) unsigned NOT NULL,
  `name` varchar(30) NOT NULL,
  `image` varchar(100) NOT NULL,
  `pdf` varchar(100) NOT NULL,
  `editor` varchar(30) NOT NULL,
  `arteditor` varchar(30) NOT NULL,
  `url` varchar(100) default 'javascript:;',
  PRIMARY KEY  (`pageid`),
  UNIQUE KEY `editionid` (`editionid`,`pageno`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=17 ;

INSERT INTO `cmstop_aca` (`acaid`, `parentid`, `app`, `controller`, `action`, `name`) VALUES
(201, NULL, 'paper', NULL, NULL, '报纸');


ALTER TABLE `cmstop_paper_content`
  ADD CONSTRAINT `cmstop_paper_content_ibfk_1` FOREIGN KEY (`pageid`) REFERENCES `cmstop_paper_edition_page` (`pageid`) ON DELETE CASCADE;

ALTER TABLE `cmstop_paper_edition`
  ADD CONSTRAINT `cmstop_paper_edition_ibfk_1` FOREIGN KEY (`paperid`) REFERENCES `cmstop_paper` (`paperid`) ON DELETE CASCADE;
  
ALTER TABLE `cmstop_paper_edition_page`
  ADD CONSTRAINT `cmstop_paper_edition_page_ibfk_1` FOREIGN KEY (`editionid`) REFERENCES `cmstop_paper_edition` (`editionid`) ON DELETE CASCADE;