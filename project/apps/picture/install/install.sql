SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `cmstop_picture`;
CREATE TABLE `cmstop_picture` (
  `contentid` mediumint(8) unsigned NOT NULL,
  `author` mediumint(8) unsigned default NULL,
  `editor` varchar(15) default NULL,
  `description` varchar(255) default NULL,
  `total` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`contentid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cmstop_picture_group`;
CREATE TABLE `cmstop_picture_group` (
  `pictureid` int(10) unsigned NOT NULL auto_increment,
  `contentid` mediumint(8) unsigned NOT NULL,
  `aid` int(10) unsigned NOT NULL,
  `image` varchar(100) NOT NULL,
  `note` varchar(255) NOT NULL,
  `url` varchar(100) NOT NULL,
  `sort` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`pictureid`),
  KEY `contentid` (`contentid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `cmstop_aca` (`acaid`, `parentid`, `app`, `controller`, `action`, `name`) VALUES
(200, NULL, 'picture', NULL, NULL, '组图'),
(201, 200, 'picture', 'picture', NULL, '组图管理'),
(202, 200, 'picture', 'html', NULL, '组图生成'),
(203, 200, 'picture', 'picture', 'index', '浏览'),
(204, 200, 'picture', 'picture', 'add,approve,related', '添加'),
(205, 200, 'picture', 'picture', 'view', '查看'),
(206, 200, 'picture', 'picture', 'remove', '删除'),
(207, 200, 'picture', 'picture', 'publish', '发布'),
(208, 200, 'picture', 'picture', 'unpublish', '撤稿'),
(210, 200, 'picture', 'picture', 'reference', '引用'),
(211, 200, 'picture', 'picture', 'move', '移动'),
(212, 200, 'picture', 'picture', 'search', '搜索'),
(213, 200, 'picture', 'picture', 'pass,reject', '审核'),
(214, 200, 'picture', 'picture', 'delete,clear,restore,restores', '回收站');

INSERT INTO `cmstop_model` (`modelid`, `name`, `alias`, `description`, `template_list`, `template_show`, `posts`, `comments`, `pv`, `sort`, `disabled`) VALUES
(2, '组图', 'picture', '', 'picture/list.html', 'picture/show.html', 0, 0, 0, 0, 0);

REPLACE INTO `cmstop_setting` (`app`, `var`, `value`) VALUES
('picture', 'source', ''),
('picture', 'thumb_height', '0'),
('picture', 'thumb_width', '940'),
('picture', 'watermark', '1'),
('picture', 'weight', '60');

ALTER TABLE `cmstop_picture`
  ADD CONSTRAINT `cmstop_picture_ibfk_1` FOREIGN KEY (`contentid`) REFERENCES `cmstop_content` (`contentid`) ON DELETE CASCADE;
ALTER TABLE `cmstop_picture_group`
  ADD CONSTRAINT `cmstop_picture_group_ibfk_1` FOREIGN KEY (`contentid`) REFERENCES `cmstop_picture` (`contentid`) ON DELETE CASCADE;