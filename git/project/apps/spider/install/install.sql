SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `cmstop_spider`;
CREATE TABLE `cmstop_spider` (
  `spiderid` int(10) unsigned NOT NULL auto_increment,
  `guid` varchar(32) NOT NULL,
  `taskid` mediumint(8) unsigned NOT NULL,
  `contentid` mediumint(8) unsigned default NULL,
  `title` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `status` enum('spiden','viewed','new') NOT NULL default 'new',
  `created` int(10) unsigned NOT NULL,
  `createdby` mediumint(8) unsigned NOT NULL,
  `spiden` int(10) unsigned default NULL,
  `spidenby` mediumint(8) unsigned default NULL,
  PRIMARY KEY  (`spiderid`),
  UNIQUE KEY `guid` (`guid`),
  KEY `taskid` (`taskid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cmstop_spider_history`;
CREATE TABLE `cmstop_spider_history` (
  `historyid` int(11) NOT NULL auto_increment,
  `title` varchar(255) default NULL,
  `url` varchar(255) NOT NULL,
  `created` int(10) NOT NULL,
  PRIMARY KEY  (`historyid`),
  UNIQUE KEY `url` (`url`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cmstop_spider_rules`;
CREATE TABLE `cmstop_spider_rules` (
  `ruleid` mediumint(8) unsigned NOT NULL auto_increment,
  `guid` varchar(255) default NULL,
  `siteid` smallint(5) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `author` varchar(100) default NULL,
  `version` int(10) default NULL,
  `charset` varchar(10) NOT NULL,
  `enter_rule` varchar(255) NOT NULL,
  `list_rule` text NOT NULL,
  `content_rule` text NOT NULL,
  `description` text,
  `created` int(10) unsigned NOT NULL,
  `createdby` mediumint(8) unsigned NOT NULL,
  `updated` int(10) unsigned default NULL,
  `updatedby` mediumint(8) unsigned default NULL,
  PRIMARY KEY  (`ruleid`),
  KEY `siteid` (`siteid`),
  KEY `guid` (`guid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cmstop_spider_site`;
CREATE TABLE `cmstop_spider_site` (
  `siteid` smallint(5) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY  (`siteid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cmstop_spider_task`;
CREATE TABLE `cmstop_spider_task` (
  `taskid` mediumint(8) unsigned NOT NULL auto_increment,
  `ruleid` mediumint(8) unsigned NOT NULL,
  `catid` smallint(5) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `frequency` smallint(5) unsigned default NULL,
  `created` int(10) unsigned NOT NULL,
  `createdby` mediumint(8) unsigned NOT NULL,
  `updated` int(10) unsigned default NULL,
  `updatedby` mediumint(8) unsigned default NULL,
  PRIMARY KEY  (`taskid`),
  KEY `ruleid` (`ruleid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `#table_aca` (`acaid`, `parentid`, `app`, `controller`, `action`, `name`) VALUES
(908, NULL, 'spider', NULL, NULL, '文章采集');

INSERT INTO `cmstop_spider_site` (`siteid`, `name`) VALUES
(1, '网易');

INSERT INTO `cmstop_spider_rules` (`ruleid`, `guid`, `siteid`, `name`, `author`, `version`, `charset`, `enter_rule`, `list_rule`, `content_rule`, `description`, `created`, `createdby`, `updated`, `updatedby`) VALUES
(1, 'http://www.cmstop.com/open-rule/1.xml', 1, '网易-新闻', 'CmsTop', 0, 'GBK', 'http://news.163.com/(*)', 'a:7:{s:9:"listStart";s:0:"";s:7:"listEnd";s:0:"";s:8:"listType";s:0:"";s:7:"listUrl";s:0:"";s:10:"urlPattern";s:40:"http://news.163.com/(*)/(*)/(*)/(*).html";s:12:"listNextPage";s:0:"";s:15:"listLimitLength";s:0:"";}', 'a:17:{s:10:"contentUrl";s:0:"";s:10:"rangeStart";s:22:"<div class="endContent";s:8:"rangeEnd";s:21:"<div class="endMore">";s:10:"titleStart";s:17:"<h1 id="h1title">";s:8:"titleEnd";s:5:"</h1>";s:12:"contentStart";s:18:"<div id="endText">";s:10:"contentEnd";s:15:"<!-- 分页 -->";s:8:"nextPage";s:0:"";s:11:"authorStart";s:0:"";s:9:"authorEnd";s:0:"";s:11:"sourceStart";s:0:"";s:9:"sourceEnd";s:0:"";s:12:"pubdateStart";s:19:"<span class="info">";s:10:"pubdateEnd";s:10:"　来源:";s:9:"allowTags";s:24:"a,b,p,br,img,span,strong";s:13:"saveRemoteImg";s:1:"1";s:11:"replacement";a:2:{i:0;a:2:{s:6:"source";s:0:"";s:6:"target";s:0:"";}i:1;a:2:{s:6:"source";s:0:"";s:6:"target";s:0:"";}}}', '国内新闻', 1284115972, 1, NULL, NULL),
(2, 'http://www.cmstop.com/open-rule/2.xml', 1, '网易-娱乐', 'CmsTop', 0, 'GBK', 'http://ent.163.com/(*)', 'a:7:{s:9:"listStart";s:0:"";s:7:"listEnd";s:0:"";s:8:"listType";s:0:"";s:7:"listUrl";s:0:"";s:10:"urlPattern";s:39:"http://ent.163.com/(*)/(*)/(*)/(*).html";s:12:"listNextPage";s:0:"";s:15:"listLimitLength";s:0:"";}', 'a:17:{s:10:"contentUrl";s:0:"";s:10:"rangeStart";s:22:"<div class="endContent";s:8:"rangeEnd";s:21:"<div class="endMore">";s:10:"titleStart";s:17:"<h1 id="h1title">";s:8:"titleEnd";s:5:"</h1>";s:12:"contentStart";s:18:"<div id="endText">";s:10:"contentEnd";s:15:"<!-- 分页 -->";s:8:"nextPage";s:0:"";s:11:"authorStart";s:0:"";s:9:"authorEnd";s:0:"";s:11:"sourceStart";s:0:"";s:9:"sourceEnd";s:0:"";s:12:"pubdateStart";s:19:"<span class="info">";s:10:"pubdateEnd";s:10:"　来源:";s:9:"allowTags";s:24:"a,b,p,br,img,span,strong";s:13:"saveRemoteImg";s:1:"1";s:11:"replacement";a:2:{i:0;a:2:{s:6:"source";s:0:"";s:6:"target";s:0:"";}i:1;a:2:{s:6:"source";s:0:"";s:6:"target";s:0:"";}}}', '网易娱乐', 1284115972, 1, NULL, NULL),
(3, 'http://www.cmstop.com/open-rule/3.xml', 1, '网易-科技-互联网', 'CmsTop', 0, 'GBK', 'http://tech.163.com/', 'a:7:{s:9:"listStart";s:33:"alt="互联网要闻" /></a></h2>";s:7:"listEnd";s:29:"<span class="blank12"></span>";s:8:"listType";s:0:"";s:7:"listUrl";s:0:"";s:10:"urlPattern";s:40:"http://tech.163.com/(*)/(*)/(*)/(*).html";s:12:"listNextPage";s:0:"";s:15:"listLimitLength";s:0:"";}', 'a:17:{s:10:"contentUrl";s:0:"";s:10:"rangeStart";s:22:"<div class="endContent";s:8:"rangeEnd";s:21:"<div class="endMore">";s:10:"titleStart";s:17:"<h1 id="h1title">";s:8:"titleEnd";s:5:"</h1>";s:12:"contentStart";s:18:"<div id="endText">";s:10:"contentEnd";s:15:"<!-- 分页 -->";s:8:"nextPage";s:0:"";s:11:"authorStart";s:0:"";s:9:"authorEnd";s:0:"";s:11:"sourceStart";s:0:"";s:9:"sourceEnd";s:0:"";s:12:"pubdateStart";s:19:"<span class="info">";s:10:"pubdateEnd";s:10:"　来源:";s:9:"allowTags";s:24:"a,b,p,br,img,span,strong";s:13:"saveRemoteImg";s:1:"1";s:11:"replacement";a:2:{i:0;a:2:{s:6:"source";s:0:"";s:6:"target";s:0:"";}i:1;a:2:{s:6:"source";s:0:"";s:6:"target";s:0:"";}}}', '网易-科技-互联网', 1284115972, 1, NULL, NULL),
(4, 'http://www.cmstop.com/open-rule/4.xml', 1, '网易-科技-通信', 'CmsTop', 0, 'GBK', 'http://tech.163.com/', 'a:7:{s:9:"listStart";s:74:"<span class="exp"><a href="http://tech.163.com/telecom/">更多</a></span>";s:7:"listEnd";s:29:"<span class="blank12"></span>";s:8:"listType";s:0:"";s:7:"listUrl";s:0:"";s:10:"urlPattern";s:40:"http://tech.163.com/(*)/(*)/(*)/(*).html";s:12:"listNextPage";s:0:"";s:15:"listLimitLength";s:0:"";}', 'a:17:{s:10:"contentUrl";s:0:"";s:10:"rangeStart";s:22:"<div class="endContent";s:8:"rangeEnd";s:21:"<div class="endMore">";s:10:"titleStart";s:17:"<h1 id="h1title">";s:8:"titleEnd";s:5:"</h1>";s:12:"contentStart";s:18:"<div id="endText">";s:10:"contentEnd";s:15:"<!-- 分页 -->";s:8:"nextPage";s:0:"";s:11:"authorStart";s:0:"";s:9:"authorEnd";s:0:"";s:11:"sourceStart";s:0:"";s:9:"sourceEnd";s:0:"";s:12:"pubdateStart";s:19:"<span class="info">";s:10:"pubdateEnd";s:10:"　来源:";s:9:"allowTags";s:24:"a,b,p,br,img,span,strong";s:13:"saveRemoteImg";s:1:"1";s:11:"replacement";a:2:{i:0;a:2:{s:6:"source";s:0:"";s:6:"target";s:0:"";}i:1;a:2:{s:6:"source";s:0:"";s:6:"target";s:0:"";}}}', '网易-科技-通信', 1284115972, 1, NULL, NULL),
(5, 'http://www.cmstop.com/open-rule/5.xml', 1, '网易-科技-业界', 'CmsTop', 0, 'GBK', 'http://tech.163.com/', 'a:7:{s:9:"listStart";s:69:"<span class="exp"><a href="http://tech.163.com/it/">更多</a></span>";s:7:"listEnd";s:13:"<!--colM.e-->";s:8:"listType";s:0:"";s:7:"listUrl";s:0:"";s:10:"urlPattern";s:40:"http://tech.163.com/(*)/(*)/(*)/(*).html";s:12:"listNextPage";s:0:"";s:15:"listLimitLength";s:0:"";}', 'a:17:{s:10:"contentUrl";s:0:"";s:10:"rangeStart";s:22:"<div class="endContent";s:8:"rangeEnd";s:21:"<div class="endMore">";s:10:"titleStart";s:17:"<h1 id="h1title">";s:8:"titleEnd";s:5:"</h1>";s:12:"contentStart";s:18:"<div id="endText">";s:10:"contentEnd";s:15:"<!-- 分页 -->";s:8:"nextPage";s:0:"";s:11:"authorStart";s:0:"";s:9:"authorEnd";s:0:"";s:11:"sourceStart";s:0:"";s:9:"sourceEnd";s:0:"";s:12:"pubdateStart";s:19:"<span class="info">";s:10:"pubdateEnd";s:10:"　来源:";s:9:"allowTags";s:24:"a,b,p,br,img,span,strong";s:13:"saveRemoteImg";s:1:"1";s:11:"replacement";a:2:{i:0;a:2:{s:6:"source";s:0:"";s:6:"target";s:0:"";}i:1;a:2:{s:6:"source";s:0:"";s:6:"target";s:0:"";}}}', '网易-科技-业界', 1284115972, 1, NULL, NULL);

INSERT INTO `cmstop_spider_task` (`taskid`, `ruleid`, `catid`, `title`, `url`, `frequency`, `created`, `createdby`, `updated`, `updatedby`) VALUES
(1, 1, 4, '网易-新闻-国内', 'http://news.163.com/domestic/', 0, 1283271225, 1, 0, NULL),
(2, 1, 5, '网易-新闻-国际', 'http://news.163.com/world/', 0, 1283271225, 1, 0, NULL),
(3, 1, 6, '网易-新闻-社会', 'http://news.163.com/shehui/', 0, 1283271225, 1, 0, NULL),
(4, 2, 7, '网易-娱乐-明星', 'http://ent.163.com/star/', 0, 1283271225, 1, 0, NULL),
(5, 2, 8, '网易-娱乐-电影', 'http://ent.163.com/movie/', 0, 1283271225, 1, 0, NULL),
(6, 2, 9, '网易-娱乐-电视', 'http://ent.163.com/tv/', 0, 1283271225, 1, 0, NULL),
(7, 2, 10, '网易-娱乐-音乐', 'http://ent.163.com/music/', 0, 1283271225, 1, 0, NULL),
(8, 3, 11, '网易-科技-互联网', 'http://tech.163.com/', 0, 1283271225, 1, 0, NULL),
(9, 4, 12, '网易-科技-通信', 'http://tech.163.com/', 0, 1283271225, 1, 0, NULL),
(10, 5, 13, '网易-科技-业界', 'http://tech.163.com/', 0, 1283271225, 1, 0, NULL);

ALTER TABLE `cmstop_spider_task`
  ADD CONSTRAINT `cmstop_spider_task_ibfk_1` FOREIGN KEY (`ruleid`) REFERENCES `cmstop_spider_rules` (`ruleid`) ON DELETE CASCADE;