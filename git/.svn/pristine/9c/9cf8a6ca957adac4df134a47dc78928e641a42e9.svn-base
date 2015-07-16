SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `cmstop_activity`;
CREATE TABLE `cmstop_activity` (
  `contentid` mediumint(8) unsigned NOT NULL,
  `description` varchar(255) default NULL,
  `content` mediumtext,
  `starttime` int(10) unsigned default NULL,
  `endtime` int(10) unsigned default NULL,
  `address` varchar(255) NOT NULL,
  `point` varchar(255) default NULL,
  `type` varchar(50) NOT NULL,
  `maxpersons` smallint(5) unsigned NOT NULL default '0',
  `gender` tinyint(1) unsigned NOT NULL default '0',
  `signstart` int(10) unsigned default NULL,
  `signend` int(10) unsigned default NULL,
  `selected` varchar(255) NOT NULL,
  `required` varchar(255) NOT NULL,
  `displayed` varchar(255) NOT NULL,
  `total` smallint(5) unsigned NOT NULL default '0',
  `checkeds` smallint(5) unsigned NOT NULL default '0',
  `signstoped` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`contentid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cmstop_activity_sign`;
CREATE TABLE `cmstop_activity_sign` (
  `signid` int(10) unsigned NOT NULL auto_increment,
  `contentid` mediumint(8) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `sex` tinyint(1) unsigned default NULL,
  `photo` varchar(100) default NULL,
  `identity` varchar(20) default NULL,
  `company` varchar(100) default NULL,
  `job` varchar(50) default NULL,
  `telephone` varchar(15) default NULL,
  `mobile` varchar(11) default NULL,
  `email` varchar(40) default NULL,
  `qq` varchar(15) default NULL,
  `msn` varchar(40) default NULL,
  `site` varchar(100) default NULL,
  `address` varchar(100) default NULL,
  `zipcode` varchar(6) default NULL,
  `aid` int(10) unsigned NOT NULL default '0',
  `note` mediumtext,
  `created` int(10) unsigned NOT NULL,
  `createdby` mediumint(8) unsigned default NULL,
  `ip` varchar(15) NOT NULL,
  `state` tinyint(1) unsigned NOT NULL default '0',
  `checked` int(10) unsigned default NULL,
  `checkedby` mediumint(8) unsigned default NULL,
  PRIMARY KEY  (`signid`),
  KEY `contentid` (`contentid`,`state`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `cmstop_aca` (`acaid`, `parentid`, `app`, `controller`, `action`, `name`) VALUES
(700, NULL, 'activity', NULL, NULL, '活动'),
(701, 700, 'activity', 'activity', NULL, '活动管理'),
(702, 700, 'activity', 'static', NULL, '报名管理'),
(703, 700, 'activity', 'online', NULL, '生成html'),
(704, 700, 'activity', 'activity', 'index', '浏览'),
(705, 700, 'activity', 'activity', 'add,approve,related', '添加'),
(706, 700, 'activity', 'activity', 'view', '查看'),
(707, 700, 'activity', 'activity', 'remove', '删除'),
(708, 700, 'activity', 'activity', 'publish', '发布'),
(709, 700, 'activity', 'activity', 'unpublish', '撤稿'),
(710, 700, 'activity', 'activity', 'copy', '复制'),
(711, 700, 'activity', 'activity', 'reference', '引用'),
(712, 700, 'activity', 'activity', 'move', '移动'),
(713, 700, 'activity', 'activity', 'search', '搜索'),
(714, 700, 'activity', 'activity', 'pass,reject', '审核'),
(715, 700, 'activity', 'activity', 'delete,clear,restore,restores', '回收站');

INSERT INTO `cmstop_model` (`modelid`, `name`, `alias`, `description`, `template_list`, `template_show`, `posts`, `comments`, `pv`, `sort`, `disabled`) VALUES
(7, '活动', 'activity', '', 'activity/list.html', 'activity/show.html', 1, 0, 0, 0, 0);

ALTER TABLE `cmstop_activity`
  ADD CONSTRAINT `cmstop_activity_ibfk_1` FOREIGN KEY (`contentid`) REFERENCES `cmstop_content` (`contentid`) ON DELETE CASCADE;

ALTER TABLE `cmstop_activity_sign`
  ADD CONSTRAINT `cmstop_activity_sign_ibfk_1` FOREIGN KEY (`contentid`) REFERENCES `cmstop_activity` (`contentid`) ON DELETE CASCADE;