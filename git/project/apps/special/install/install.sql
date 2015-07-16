SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `cmstop_place`;
CREATE TABLE `cmstop_place` (
  `placeid` int(10) unsigned NOT NULL,
  `pageid` mediumint(8) unsigned default NULL,
  `name` varchar(100) NOT NULL,
  `description` text,
  PRIMARY KEY  (`placeid`),
  KEY `pageid` (`pageid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cmstop_place_data`;
CREATE TABLE IF NOT EXISTS `cmstop_place_data` (
  `dataid` int(10) unsigned NOT NULL auto_increment,
  `placeid` int(10) unsigned NOT NULL,
  `contentid` mediumint(8) unsigned default NULL,
  `title` varchar(255) NOT NULL,
  `color` varchar(7) default NULL,
  `url` varchar(255) NOT NULL,
  `thumb` varchar(255) default NULL,
  `description` text,
  `time` int(10) unsigned default NULL,
  `sort` tinyint(3) unsigned NOT NULL default '0',
  `created` int(10) unsigned NOT NULL,
  `createdby` mediumint(8) unsigned NOT NULL,
  `status` tinyint(4) default NULL,
  PRIMARY KEY  (`dataid`),
  UNIQUE KEY `placeid_2` (`placeid`,`contentid`),
  KEY `contentid` (`contentid`),
  KEY `createdby` (`createdby`),
  KEY `placeid` (`placeid`,`sort`,`time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cmstop_special`;
CREATE TABLE IF NOT EXISTS `cmstop_special` (
  `contentid` mediumint(8) unsigned NOT NULL,
  `path` varchar(255) NOT NULL,
  `description` char(255) default NULL,
  `mode` varchar(20) NOT NULL,
  `morelist_template` varchar(100) NULL COMMENT '更多列表模板',
  `morelist_pagesize` smallint(5) unsigned NULL DEFAULT '50' COMMENT '更多列表分页数',
  `morelist_maxpage` mediumint(8) unsigned NULL DEFAULT '100' COMMENT '更多列表最多显示多少页',
  PRIMARY KEY  (`contentid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cmstop_special_page`;
CREATE TABLE IF NOT EXISTS `cmstop_special_page` (
  `pageid` mediumint(8) unsigned NOT NULL auto_increment,
  `contentid` mediumint(8) unsigned default NULL,
  `data` longtext,
  `setting` longtext NULL,
  `name` varchar(20) NOT NULL,
  `file` varchar(100) NOT NULL,
  `url` varchar(255) NOT NULL,
  `template` varchar(100) default NULL,
  `locked` int(10) unsigned default NULL,
  `lockedby` mediumint(8) unsigned default NULL,
  `updated` int(10) unsigned default NULL,
  `updatedby` mediumint(8) unsigned default NULL,
  `published` int(10) unsigned default NULL,
  `frequency` smallint(5) unsigned default NULL,
  `created` int(10) unsigned NOT NULL,
  `createdby` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY  (`pageid`),
  KEY `contentid` (`contentid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cmstop_widget`;
CREATE TABLE IF NOT EXISTS `cmstop_widget` (
  `widgetid` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(20) default NULL,
  `engine` varchar(20) NOT NULL,
  `data` longtext,
  `shared` tinyint(1) default '0',
  `updated` int(10) unsigned default NULL,
  `updatedby` mediumint(8) unsigned default NULL,
  `published` int(10) unsigned default NULL,
  `created` int(10) unsigned NOT NULL,
  `createdby` mediumint(8) unsigned NOT NULL,
  `description` varchar(255) default NULL,
  PRIMARY KEY  (`widgetid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cmstop_widget_engine`;
CREATE TABLE IF NOT EXISTS `cmstop_widget_engine` (
  `engineid` smallint(5) unsigned NOT NULL auto_increment,
  `name` varchar(20) NOT NULL,
  `description` varchar(100) NOT NULL,
  `version` varchar(5) NOT NULL,
  `author` varchar(255) NOT NULL,
  `updateurl` varchar(255) default NULL,
  `installed` int(10) unsigned default NULL,
  `disabled` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`engineid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=20;

INSERT INTO `cmstop_widget_engine` (`engineid`, `name`, `description`, `version`, `author`, `updateurl`, `installed`, `disabled`) VALUES
(1, 'code', '代码', '1.0.0', 'cmstop', 'http://update.cmstop.com', NULL, 0),
(2, 'title', '标题', '1.0.0', 'cmstop', 'http://update.cmstop.com', NULL, 0),
(3, 'list', '列表', '1.0.0', 'cmstop', 'http://update.cmstop.com', NULL, 0),
(4, 'piclist', '图片列表', '1.0.0', 'cmstop', 'http://update.cmstop.com', NULL, 0),
(5, 'palist', '图文列表', '1.0.0', 'cmstop', 'http://update.cmstop.com', NULL, 0),
(6, 'slider', '幻灯片', '1.0.0', 'cmstop', 'http://update.cmstop.com', NULL, 0),
(7, 'menu', '专题菜单', '1.0.0', 'cmstop', 'http://update.cmstop.com', NULL, 0),
(8, 'flash', 'Flash', '1.0.0', 'cmstop', 'http://update.cmstop.com', NULL, 0),
(9, 'picture', '图片', '1.0.0', 'cmstop', 'http://update.cmstop.com', NULL, 0),
(10, 'video', '视频', '1.0.0', 'cmstop', 'http://update.cmstop.com', NULL, 0),
(11, 'comment', '评论', '1.0.0', 'cmstop', 'http://update.cmstop.com', NULL, 0),
(12, 'activity', '活动', '1.0.0', 'cmstop', 'http://update.cmstop.com', NULL, 0),
(13, 'survey', '调查', '1.0.0', 'cmstop', 'http://update.cmstop.com', NULL, 0),
(14, 'vote', '投票', '1.0.0', 'cmstop', 'http://update.cmstop.com', NULL, 0),
(15, 'weibo', '微博', '1.0.0', 'cmstop', 'http://update.cmstop.com', NULL, 0),
(16, 'map', '百度地图', '1.0.0', 'cmstop', 'http://update.cmstop.com', NULL, 0),
(17, 'html', 'HTML', '1.0.0', 'cmstop', 'http://update.cmstop.com', NULL, 0),
(18, 'share', '分享', '1.0.0', 'cmstop', 'http://update.cmstop.com', NULL, 0),
(19, 'weather', '天气预报', '1.0.0', 'cmstop', 'http://update.cmstop.com', NULL, 0);

ALTER TABLE `cmstop_place`
  ADD CONSTRAINT `cmstop_place_ibfk_2` FOREIGN KEY (`placeid`) REFERENCES `cmstop_widget` (`widgetid`) ON DELETE CASCADE,
  ADD CONSTRAINT `cmstop_place_ibfk_1` FOREIGN KEY (`pageid`) REFERENCES `cmstop_special_page` (`pageid`) ON DELETE SET NULL;
ALTER TABLE `cmstop_place_data`
  ADD CONSTRAINT `cmstop_place_data_ibfk_2` FOREIGN KEY (`contentid`) REFERENCES `cmstop_content` (`contentid`) ON DELETE CASCADE,
  ADD CONSTRAINT `cmstop_place_data_ibfk_3` FOREIGN KEY (`placeid`) REFERENCES `cmstop_place` (`placeid`) ON DELETE CASCADE;
ALTER TABLE `cmstop_special`
  ADD CONSTRAINT `cmstop_special_ibfk_1` FOREIGN KEY (`contentid`) REFERENCES `cmstop_content` (`contentid`) ON DELETE CASCADE;
ALTER TABLE `cmstop_special_page`
  ADD CONSTRAINT `cmstop_special_page_ibfk_1` FOREIGN KEY (`contentid`) REFERENCES `cmstop_content` (`contentid`) ON DELETE CASCADE;

INSERT INTO `cmstop_model` (`modelid`, `name`, `alias`, `description`, `template_list`, `template_show`, `posts`, `comments`, `pv`, `sort`, `disabled`) VALUES
(10, '专题', 'special', '', 'special/list.html', '', 0, 0, 0, 0, 0);