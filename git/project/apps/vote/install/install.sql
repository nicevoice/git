SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `cmstop_vote`;
CREATE TABLE `cmstop_vote` (
  `contentid` mediumint(8) unsigned NOT NULL,
  `type` enum('radio','checkbox') NOT NULL default 'radio',
  `description` varchar(255) default NULL,
  `starttime` int(10) unsigned default NULL,
  `endtime` int(10) unsigned default NULL,
  `maxoptions` tinyint(2) unsigned NOT NULL default '0',
  `maxvotes` tinyint(3) unsigned NOT NULL default '0',
  `mininterval` tinyint(3) unsigned NOT NULL default '0',
  `total` mediumint(8) unsigned NOT NULL default '0',
  KEY `contentid` (`contentid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cmstop_vote_log`;
CREATE TABLE `cmstop_vote_log` (
  `logid` int(10) unsigned NOT NULL auto_increment,
  `contentid` mediumint(8) unsigned NOT NULL,
  `created` int(10) unsigned NOT NULL,
  `createdby` mediumint(8) unsigned default NULL,
  `ip` varchar(15) NOT NULL,
  PRIMARY KEY  (`logid`),
  KEY `createdby` (`createdby`),
  KEY `contentid` (`contentid`,`ip`,`created`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cmstop_vote_log_data`;
CREATE TABLE `cmstop_vote_log_data` (
  `dataid` int(10) unsigned NOT NULL auto_increment,
  `logid` int(10) unsigned NOT NULL,
  `optionid` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`dataid`),
  KEY `logid` (`logid`),
  KEY `optionid` (`optionid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cmstop_vote_option`;
CREATE TABLE `cmstop_vote_option` (
  `optionid` int(10) unsigned NOT NULL auto_increment,
  `contentid` mediumint(8) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `sort` tinyint(3) unsigned NOT NULL default '0',
  `votes` mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`optionid`),
  KEY `contentid` (`contentid`,`sort`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `cmstop_aca` (`acaid`, `parentid`, `app`, `controller`, `action`, `name`) VALUES
(800, NULL, 'vote', NULL, NULL, '投票'),
(801, 800, 'vote', 'vote', NULL, '投票管理'),
(802, 800, 'vote', 'html', NULL, '生成html'),
(803, 800, 'vote', 'vote', 'index', '浏览'),
(804, 800, 'vote', 'vote', 'add,approve,related', '添加'),
(805, 800, 'vote', 'vote', 'view', '查看'),
(806, 800, 'vote', 'vote', 'remove', '删除'),
(807, 800, 'vote', 'vote', 'publish', '发布'),
(808, 800, 'vote', 'vote', 'unpublish', '撤稿'),
(809, 800, 'vote', 'vote', 'copy', '复制'),
(810, 800, 'vote', 'vote', 'reference', '引用'),
(811, 800, 'vote', 'vote', 'move', '移动'),
(812, 800, 'vote', 'vote', 'search', '搜索'),
(813, 800, 'vote', 'vote', 'pass,reject', '审核'),
(814, 800, 'vote', 'vote', 'delete,clear,restore,restores', '回收站');

INSERT INTO `cmstop_model` (`modelid`, `name`, `alias`, `description`, `template_list`, `template_show`, `posts`, `comments`, `pv`, `sort`, `disabled`) VALUES
(8, '投票', 'vote', '', 'vote/list.html', 'vote/show.html', 0, 0, 0, 0, 0);

INSERT INTO `cmstop_content` (`contentid`, `catid`, `modelid`, `title`, `color`, `thumb`, `tags`, `sourceid`, `url`, `weight`, `status`, `created`, `createdby`, `published`, `publishedby`, `unpublished`, `unpublishedby`, `modified`, `modifiedby`, `checked`, `checkedby`, `locked`, `lockedby`, `noted`, `notedby`, `note`, `workflow_step`, `workflow_roleid`, `iscontribute`, `spaceid`, `related`, `pv`, `allowcomment`, `comments`) VALUES
(1, 4, 8, '你认为网站改版怎么样？', '', '', '', 0, '{WWW_URL}news/2010/0908/1.shtml', 60, 6, 1283923994, 1, 1283923994, 1, NULL, NULL, 1283924085, 1, NULL, NULL, 1283924085, 1, NULL, NULL, 0, NULL, NULL, 0, NULL, 0, 0, 1, 0);

INSERT INTO `cmstop_vote` (`contentid`, `type`, `description`, `starttime`, `endtime`, `maxoptions`, `maxvotes`, `mininterval`, `total`) VALUES
(1, 'radio', '', 1283923995, NULL, 0, 0, 0, 0);

INSERT INTO `cmstop_vote_option` (`optionid`, `contentid`, `name`, `sort`, `votes`) VALUES
(1, 1, '很好', 1, 0),
(2, 1, '还不错', 2, 0),
(3, 1, '一般', 3, 0),
(4, 1, '很差', 4, 0);

ALTER TABLE `cmstop_vote`
  ADD CONSTRAINT `cmstop_vote_ibfk_1` FOREIGN KEY (`contentid`) REFERENCES `cmstop_content` (`contentid`) ON DELETE CASCADE;
ALTER TABLE `cmstop_vote_log`
  ADD CONSTRAINT `cmstop_vote_log_ibfk_1` FOREIGN KEY (`contentid`) REFERENCES `cmstop_vote` (`contentid`) ON DELETE CASCADE;
ALTER TABLE `cmstop_vote_log_data`
  ADD CONSTRAINT `cmstop_vote_log_data_ibfk_1` FOREIGN KEY (`logid`) REFERENCES `cmstop_vote_log` (`logid`) ON DELETE CASCADE;
ALTER TABLE `cmstop_vote_option`
  ADD CONSTRAINT `cmstop_vote_option_ibfk_1` FOREIGN KEY (`contentid`) REFERENCES `cmstop_vote` (`contentid`) ON DELETE CASCADE;