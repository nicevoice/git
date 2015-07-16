SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `cmstop_push`;
CREATE TABLE `cmstop_push` (
  `pushid` int(10) unsigned NOT NULL auto_increment,
  `guid` int(10) unsigned NOT NULL,
  `taskid` mediumint(8) unsigned NOT NULL,
  `contentid` mediumint(8) unsigned default NULL,
  `status` enum('pushed','viewed','new') NOT NULL default 'new',
  `pushed` int(10) unsigned default NULL,
  `pushedby` mediumint(8) unsigned default NULL,
  PRIMARY KEY  (`pushid`),
  UNIQUE KEY `guid` (`guid`,`taskid`),
  KEY `taskid` (`taskid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cmstop_push_rule`;
CREATE TABLE `cmstop_push_rule` (
  `ruleid` mediumint(8) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `dsnid` smallint(5) unsigned NOT NULL,
  `maintable` text NOT NULL,
  `jointable` text,
  `primary` varchar(255) default NULL,
  `linkrule` varchar(255) default NULL,
  `fields` text,
  `defaults` text,
  `condition` text NOT NULL,
  `plugin` varchar(20) default NULL,
  `description` text,
  `created` int(10) unsigned NOT NULL,
  `createdby` mediumint(8) unsigned NOT NULL,
  `updated` int(10) unsigned default NULL,
  `updatedby` mediumint(8) unsigned default NULL,
  PRIMARY KEY  (`ruleid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cmstop_push_task`;
CREATE TABLE `cmstop_push_task` (
  `taskid` mediumint(8) unsigned NOT NULL auto_increment,
  `ruleid` mediumint(8) unsigned NOT NULL,
  `extra_condition` text,
  `catid` smallint(5) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `created` int(10) unsigned NOT NULL,
  `createdby` mediumint(8) unsigned NOT NULL,
  `updated` int(10) unsigned default NULL,
  `updatedby` mediumint(8) unsigned default NULL,
  PRIMARY KEY  (`taskid`),
  KEY `catid` (`catid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `cmstop_aca` (`acaid`, `parentid`, `app`, `controller`, `action`, `name`) VALUES
(904, NULL, 'push', NULL, NULL, '文章推送');

ALTER TABLE `cmstop_push`
  ADD CONSTRAINT `cmstop_push_ibfk_1` FOREIGN KEY (`taskid`) REFERENCES `cmstop_push_task` (`taskid`) ON DELETE CASCADE;