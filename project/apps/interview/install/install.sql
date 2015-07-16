SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `cmstop_interview`;
CREATE TABLE IF NOT EXISTS `cmstop_interview` (
  `contentid` mediumint(8) unsigned NOT NULL,
  `number` smallint(5) unsigned default NULL,
  `description` varchar(255) default NULL,
  `address` varchar(100) default NULL,
  `starttime` int(10) unsigned default NULL,
  `endtime` int(10) unsigned default NULL,
  `compere` varchar(20) default NULL,
  `mode` enum('text','video') NOT NULL default 'text',
  `photo` varchar(100) default NULL,
  `video` varchar(255) default NULL,
  `allowchat` tinyint(1) unsigned NOT NULL default '1',
  `ischeck` tinyint(1) unsigned NOT NULL default '0',
  `startchat` int(10) unsigned default NULL,
  `endchat` int(10) unsigned default NULL,
  `review` mediumtext,
  `editor` varchar(15) default NULL,
  `notice` text,
  `picture` mediumint(8) unsigned default NULL,
  `state` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`contentid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cmstop_interview_chat`;
CREATE TABLE IF NOT EXISTS `cmstop_interview_chat` (
  `chatid` int(10) unsigned NOT NULL auto_increment,
  `contentid` mediumint(8) unsigned NOT NULL,
  `guestid` mediumint(8) unsigned default NULL,
  `content` mediumtext NOT NULL,
  `created` int(10) unsigned NOT NULL,
  `createdby` mediumint(8) unsigned NOT NULL,
  `ip` varchar(15) NOT NULL,
  PRIMARY KEY  (`chatid`),
  KEY `contentid` (`contentid`),
  KEY `guestid` (`guestid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;


DROP TABLE IF EXISTS `cmstop_interview_guest`;
CREATE TABLE IF NOT EXISTS `cmstop_interview_guest` (
  `guestid` mediumint(8) unsigned NOT NULL auto_increment,
  `contentid` mediumint(8) unsigned NOT NULL,
  `name` varchar(30) NOT NULL,
  `color` varchar(7) default NULL,
  `initial` varchar(1) NOT NULL,
  `photo` varchar(100) default NULL,
  `aid` int(10) unsigned default NULL,
  `resume` mediumtext,
  `url` varchar(200) NOT NULL,
  `sort` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`guestid`),
  KEY `contentid` (`contentid`),
  KEY `initial` (`initial`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

DROP TABLE IF EXISTS `cmstop_interview_question`;
CREATE TABLE IF NOT EXISTS `cmstop_interview_question` (
  `questionid` int(10) unsigned NOT NULL auto_increment,
  `contentid` mediumint(8) unsigned NOT NULL,
  `nickname` varchar(20) NOT NULL,
  `content` mediumtext NOT NULL,
  `created` int(10) unsigned NOT NULL,
  `createdby` mediumint(8) unsigned default NULL,
  `ip` varchar(15) NOT NULL,
  `iplocked` int(10) unsigned NOT NULL default '0',
  `state` tinyint(1) unsigned NOT NULL default '1',
  PRIMARY KEY  (`questionid`),
  KEY `ip` (`ip`),
  KEY `contentid` (`contentid`,`state`,`questionid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;


INSERT INTO `cmstop_aca` (`acaid`, `parentid`, `app`, `controller`, `action`, `name`) VALUES
(92, NULL, 'interview', NULL, NULL, '访谈'),
(93, 92, 'interview', 'interview', NULL, '访谈管理'),
(94, 92, 'interview', 'chat', NULL, '文字实录'),
(95, 92, 'interview', 'question', NULL, '网友互动'),
(96, 92, 'interview', 'html', NULL, '生成html'),
(97, 93, 'interview', 'interview', 'index', '浏览'),
(98, 93, 'interview', 'interview', 'add,approve,related', '添加'),
(99, 93, 'interview', 'interview', 'edit,approve,related', '修改'),
(100, 93, 'interview', 'interview', 'view', '查看'),
(101, 93, 'interview', 'interview', 'remove', '删除'),
(102, 93, 'interview', 'interview', 'publish', '发布'),
(103, 93, 'interview', 'interview', 'unpublish', '撤稿'),
(104, 93, 'interview', 'interview', 'reference', '引用'),
(105, 93, 'interview', 'interview', 'move', '移动'),
(106, 93, 'interview', 'interview', 'search', '搜索'),
(107, 93, 'interview', 'interview', 'pass,reject', '审核'),
(108, 93, 'interview', 'interview', 'delete,clear,restore,restores', '回收站');

INSERT INTO `cmstop_model` (`modelid`, `name`, `alias`, `description`, `template_list`, `template_show`, `posts`, `comments`, `pv`, `sort`, `disabled`) VALUES
(5, '访谈', 'interview', '', 'interview/list.html', 'interview/show.html', 8, 0, 0, 0, 0);

ALTER TABLE `cmstop_interview`
  ADD CONSTRAINT `cmstop_interview_ibfk_1` FOREIGN KEY (`contentid`) REFERENCES `cmstop_content` (`contentid`) ON DELETE CASCADE;

ALTER TABLE `cmstop_interview_chat`
  ADD CONSTRAINT `cmstop_interview_chat_ibfk_1` FOREIGN KEY (`contentid`) REFERENCES `cmstop_interview` (`contentid`) ON DELETE CASCADE;
  
ALTER TABLE `cmstop_interview_chat`
  ADD CONSTRAINT `cmstop_interview_chat_ibfk_2` FOREIGN KEY (`guestid`) REFERENCES `cmstop_interview_guestid` (`guestid`) ON DELETE CASCADE;
  
ALTER TABLE `cmstop_interview_guest`
  ADD CONSTRAINT `cmstop_interview_guest_ibfk_1` FOREIGN KEY (`guestid`) REFERENCES `cmstop_interview` (`contentid`) ON DELETE CASCADE;
  
ALTER TABLE `cmstop_interview_question`
  ADD CONSTRAINT `cmstop_interview_question_ibfk_1` FOREIGN KEY (`contentid`) REFERENCES `cmstop_interview` (`contentid`) ON DELETE CASCADE;