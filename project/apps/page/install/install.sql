SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `cmstop_page`;
CREATE TABLE `cmstop_page` (
  `pageid` smallint(5) unsigned NOT NULL auto_increment,
  `parentid` smallint(5) unsigned default NULL,
  `parentids` text,
  `childids` varchar(255) default NULL,
  `name` varchar(20) NOT NULL,
  `template` varchar(100) NOT NULL,
  `path` varchar(100) NOT NULL,
  `url` varchar(255) NOT NULL,
  `frequency` smallint(5) unsigned NOT NULL default '3600',
  `published` int(10) unsigned default NULL,
  `nextpublish` int(10) unsigned default NULL,
  `updated` int(10) unsigned default NULL,
  `updatedby` mediumint(8) unsigned default NULL,
  `created` int(10) unsigned default NULL,
  `createdby` mediumint(8) unsigned default NULL,
  `sort` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`pageid`),
  KEY `parentid` (`parentid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=16 ;

DROP TABLE IF EXISTS `cmstop_page_priv`;
CREATE TABLE `cmstop_page_priv` (
  `pageid` smallint(5) unsigned NOT NULL default '0',
  `userid` mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`pageid`,`userid`),
  KEY `userid` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cmstop_page_stat`;
CREATE TABLE `cmstop_page_stat` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `pageid` smallint(5) unsigned NOT NULL,
  `date` date NOT NULL,
  `pv` mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `pageid` (`pageid`,`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


INSERT INTO `cmstop_aca` (`acaid`, `parentid`, `app`, `controller`, `action`, `name`) VALUES
(175, NULL, 'page', NULL, NULL, '页面'),
(176, 175, 'page', 'page', NULL, '页面管理'),
(177, 175, 'page', 'page_priv', NULL, '页面权限'),
(178, 175, 'page', 'section', NULL, '区块管理'),
(179, 175, 'page', 'section_priv', NULL, '区块权限'),
(180, 175, 'page', 'content', NULL, '手动区块内容'),
(183, 176, 'page', 'page', 'index,tree,sections,property,sectionlog', '浏览'),
(184, 176, 'page', 'page', 'add', '添加'),
(185, 176, 'page', 'page', 'edit', '编辑'),
(186, 176, 'page', 'page', 'publish', '发布'),
(187, 176, 'page', 'page', 'delete', '删除'),
(188, 176, 'page', 'page', 'exportTemplate', '导出'),
(189, 178, 'page', 'section', 'edit,visual,grap,publish,view,loadViewHtml,preview,search,lock,unlock,unsave,section_state,delrow,uprow,downrow,addrow,additem,delitem,edititem,replaceitem,leftitem,rightitem,viewlog,restorelog,getlog', '维护'),
(190, 178, 'page', 'section', 'add,delete,property', '设置');

INSERT INTO `cmstop_page` (`pageid`, `parentid`, `parentids`, `childids`, `name`, `template`, `path`, `url`, `frequency`, `published`, `nextpublish`, `updated`, `updatedby`, `created`, `createdby`, `sort`) VALUES
(1, NULL, NULL, NULL, '首页', 'index.html', '{PSN:1}index.shtml', 'http://www.cmstop.loc/index.shtml', 300, 1290483937, 1290484237, NULL, NULL, 1268731277, 1, 0),
(2, NULL, NULL, NULL, '新闻', 'page/news.html', '{PSN:2}index.shtml', 'http://news.cmstop.loc/index.shtml', 600, 1290154455, 1290155055, NULL, NULL, 1268731633, 1, 0),
(3, NULL, NULL, NULL, '访谈', 'page/talk.html', '{PSN:6}index.shtml', 'http://talk.cmstop.loc/index.shtml', 600, 1290154460, 1290155060, NULL, NULL, 1268731954, 1, 0),
(4, NULL, NULL, NULL, '图片', 'page/photo.html', '{PSN:7}index.shtml', 'http://photo.cmstop.loc/index.shtml', 600, 1290154462, 1290155062, NULL, NULL, 1268765396, 1, 0),
(5, NULL, NULL, NULL, '视频', 'page/video.html', '{PSN:8}index.shtml', 'http://video.cmstop.loc/index.shtml', 600, 1290154463, 1290155063, NULL, NULL, 1268767893, 1, 0),
(6, NULL, NULL, NULL, '专题', 'page/special.html', '{PSN:5}index.shtml', 'http://special.cmstop.loc/index.shtml', 600, 1290154466, 1290155066, NULL, NULL, 1268768478, 1, 0),
(7, NULL, NULL, NULL, '内容页推荐', 'system/right.html', '{PSN:1}include/right.shtml', 'http://www.cmstop.loc/include/right.shtml', 3600, 1290154467, 1290158067, NULL, NULL, 1268880342, 1, 0),
(8, NULL, NULL, NULL, '广告', 'system/ad.html', '{PSN:1}/ad.shtml', 'http://www.cmstop.loc/ad.shtml', 3600, 1290154469, 1290158069, NULL, NULL, 1288162116, 1, 0),
(14, NULL, NULL, NULL, '关于', 'system/ad.html', '{PSN:1}/about/join.shtml', 'http://www.cmstop.loc/about/join.shtml', 3600, 1290134954, 1290138554, NULL, NULL, 1288579383, 1, 0),
(15, NULL, NULL, NULL, '专栏', 'page/space.html', '{PSN:1}/space/index.shtml', 'http://www.cmstop.loc/space/index.shtml', 3600, 1290154472, 1290158072, NULL, NULL, 1288685699, 1, 0);


ALTER TABLE `cmstop_page`
  ADD CONSTRAINT `cmstop_page_ibfk_1` FOREIGN KEY (`parentid`) REFERENCES `cmstop_page` (`pageid`) ON DELETE CASCADE;

ALTER TABLE `cmstop_page_priv`
  ADD CONSTRAINT `cmstop_page_priv_ibfk_1` FOREIGN KEY (`pageid`) REFERENCES `cmstop_page` (`pageid`) ON DELETE CASCADE;
  
ALTER TABLE `cmstop_page_priv`
  ADD CONSTRAINT `cmstop_page_priv_ibfk_2` FOREIGN KEY (`userid`) REFERENCES `cmstop_admin` (`userid`) ON DELETE CASCADE;
  
ALTER TABLE `cmstop_page_stat`
  ADD CONSTRAINT `cmstop_page_stat_ibfk_1` FOREIGN KEY (`pageid`) REFERENCES `cmstop_page` (`pageid`) ON DELETE CASCADE;