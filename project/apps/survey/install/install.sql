SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `cmstop_survey`;
CREATE TABLE `cmstop_survey` (
  `contentid` mediumint(8) unsigned NOT NULL,
  `description` mediumtext,
  `starttime` int(10) unsigned default NULL,
  `endtime` int(10) unsigned default NULL,
  `maxanswers` mediumint(8) unsigned NOT NULL default '0',
  `minhours` tinyint(3) unsigned NOT NULL default '0',
  `checklogined` tinyint(1) unsigned NOT NULL default '0',
  `questions` tinyint(3) unsigned NOT NULL default '0',
  `answers` mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`contentid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cmstop_survey_answer`;
CREATE TABLE `cmstop_survey_answer` (
  `answerid` int(10) unsigned NOT NULL auto_increment,
  `contentid` mediumint(8) unsigned NOT NULL,
  `created` int(10) unsigned NOT NULL,
  `createdby` mediumint(8) unsigned default NULL,
  `ip` char(15) NOT NULL,
  PRIMARY KEY  (`answerid`),
  KEY `contentid` (`contentid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;

DROP TABLE IF EXISTS `cmstop_survey_answer_option`;
CREATE TABLE `cmstop_survey_answer_option` (
  `answerid` int(10) unsigned NOT NULL,
  `questionid` mediumint(8) unsigned NOT NULL,
  `optionid` mediumint(8) unsigned NOT NULL,
  KEY `answerid` (`answerid`),
  KEY `optionid` (`optionid`),
  KEY `questionid` (`questionid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cmstop_survey_answer_record`;
CREATE TABLE `cmstop_survey_answer_record` (
  `answerid` int(10) unsigned NOT NULL,
  `questionid` mediumint(8) unsigned NOT NULL,
  `content` text,
  KEY `answerid` (`answerid`),
  KEY `questionid` (`questionid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cmstop_survey_question`;
CREATE TABLE `cmstop_survey_question` (
  `questionid` mediumint(8) unsigned NOT NULL auto_increment,
  `contentid` mediumint(8) unsigned NOT NULL,
  `subject` varchar(255) NOT NULL,
  `description` varchar(255) default NULL,
  `image` varchar(255) default NULL,
  `type` enum('radio','checkbox','select','text','textarea','hr') NOT NULL,
  `width` smallint(5) unsigned NOT NULL default '0',
  `height` smallint(5) unsigned NOT NULL default '0',
  `maxlength` mediumint(8) unsigned NOT NULL default '0',
  `validator` varchar(20) default NULL,
  `required` tinyint(1) unsigned NOT NULL default '0',
  `minoptions` tinyint(1) unsigned NOT NULL,
  `maxoptions` tinyint(1) unsigned NOT NULL default '0',
  `allowfill` tinyint(1) unsigned NOT NULL default '0',
  `sort` tinyint(2) unsigned NOT NULL default '0',
  `votes` mediumint(8) unsigned NOT NULL default '0',
  `records` mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`questionid`),
  KEY `contentid` (`contentid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=15 ;


DROP TABLE IF EXISTS `cmstop_survey_question_option`;
CREATE TABLE `cmstop_survey_question_option` (
  `optionid` mediumint(8) unsigned NOT NULL auto_increment,
  `questionid` mediumint(8) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `image` varchar(255) default NULL,
  `isfill` tinyint(1) unsigned NOT NULL default '0',
  `sort` tinyint(2) unsigned NOT NULL default '0',
  `votes` mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`optionid`),
  KEY `questionid` (`questionid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=37 ;

INSERT INTO `cmstop_aca` (`acaid`, `parentid`, `app`, `controller`, `action`, `name`) VALUES
(141, NULL, 'survey', NULL, NULL, '调查'),
(142, 141, 'survey', 'survey', NULL, '调查管理'),
(143, 141, 'survey', 'question', NULL, '设计表单'),
(144, 141, 'survey', 'report', NULL, '分析报告'),
(145, 141, 'survey', 'export', NULL, '导出报告'),
(146, 141, 'survey', 'html', NULL, '生成html'),
(147, 142, 'survey', 'survey', 'index', '浏览'),
(148, 142, 'survey', 'survey', 'add,approve,related', '添加'),
(149, 142, 'survey', 'survey', 'edit,approve,related', '修改'),
(150, 142, 'survey', 'survey', 'view', '查看'),
(151, 142, 'survey', 'survey', 'remove', '删除'),
(152, 142, 'survey', 'survey', 'publish', '发布'),
(153, 142, 'survey', 'survey', 'unpublish', '撤稿'),
(154, 142, 'survey', 'survey', 'reference', '引用'),
(155, 142, 'survey', 'survey', 'move', '移动'),
(156, 142, 'survey', 'survey', 'search', '搜索'),
(157, 142, 'survey', 'survey', 'pass,reject', '审核'),
(158, 142, 'survey', 'survey', 'delete,clear,restore,restores', '回收站');

INSERT INTO `cmstop_model` (`modelid`, `name`, `alias`, `description`, `template_list`, `template_show`, `posts`, `comments`, `pv`, `sort`, `disabled`) VALUES
(9, '调查', 'survey', '', 'survey/list.html', 'survey/show.html', 5, 0, 0, 0, 0);

ALTER TABLE `cmstop_survey`
  ADD CONSTRAINT `cmstop_survey_ibfk_1` FOREIGN KEY (`contentid`) REFERENCES `cmstop_content` (`contentid`) ON DELETE CASCADE;
  
ALTER TABLE `cmstop_survey_answer`
  ADD CONSTRAINT `ccmstop_survey_answer_ibfk_1` FOREIGN KEY (`contentid`) REFERENCES `cmstop_survey` (`contentid`) ON DELETE CASCADE;
  
ALTER TABLE `cmstop_survey_answer_option`
  ADD CONSTRAINT `cmstop_survey_answer_option_ibfk_1` FOREIGN KEY (`answerid`) REFERENCES `cmstop_survey_answer` (`answerid`) ON DELETE CASCADE;

ALTER TABLE `cmstop_survey_answer_option`
  ADD CONSTRAINT `cmstop_survey_answer_option_ibfk_2` FOREIGN KEY (`questionid`) REFERENCES `cmstop_survey_question` (`questionid`) ON DELETE CASCADE;
  
ALTER TABLE `cmstop_survey_answer_option`
  ADD CONSTRAINT `cmstop_survey_answer_option_ibfk_3` FOREIGN KEY (`optionid`) REFERENCES `cmstop_survey_question_option` (`optionid`) ON DELETE CASCADE;

ALTER TABLE `cmstop_survey_answer_record`
  ADD CONSTRAINT `cmstop_survey_answer_record_ibfk_1` FOREIGN KEY (`answerid`) REFERENCES `cmstop_survey_answer` (`answerid`) ON DELETE CASCADE;

ALTER TABLE `cmstop_survey_answer_record`
  ADD CONSTRAINT `cmstop_survey_answer_record_ibfk_2` FOREIGN KEY (`questionid`) REFERENCES `cmstop_survey_question` (`questionid`) ON DELETE CASCADE;
  
ALTER TABLE `cmstop_survey_question`
  ADD CONSTRAINT `cmstop_survey_question_ibfk_1` FOREIGN KEY (`contentid`) REFERENCES `cmstop_survey` (`contentid`) ON DELETE CASCADE;
  
ALTER TABLE `cmstop_survey_question_option`
  ADD CONSTRAINT `cmstop_survey_question_option_ibfk_1` FOREIGN KEY (`questionid`) REFERENCES `cmstop_survey_question` (`questionid`) ON DELETE CASCADE;
