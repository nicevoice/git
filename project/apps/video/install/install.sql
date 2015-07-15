SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `cmstop_video`;
CREATE TABLE `cmstop_video` (
  `contentid` mediumint(8) unsigned NOT NULL,
  `aid` int(10) unsigned default NULL,
  `video` varchar(255) NOT NULL,
  `playtime` smallint(5) unsigned default NULL,
  `description` mediumtext NOT NULL,
  `editor` varchar(15) default NULL,
  PRIMARY KEY  (`contentid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `cmstop_aca` (`acaid`, `parentid`, `app`, `controller`, `action`, `name`) VALUES
(400, NULL, 'video', NULL, NULL, '视频'),
(401, 400, 'video', 'html', NULL, '视频生成'),
(402, 400, 'video', 'video', NULL, '视频管理'),
(403, 400, 'video', 'video', 'index', '浏览'),
(404, 400, 'video', 'video', 'add,approve,related', '添加'),
(405, 400, 'video', 'video', 'view', '查看'),
(406, 400, 'video', 'video', 'remove', '删除'),
(407, 400, 'video', 'video', 'publish', '发布'),
(408, 400, 'video', 'video', 'unpublish', '撤稿'),
(410, 400, 'video', 'video', 'reference', '引用'),
(411, 400, 'video', 'video', 'move', '移动'),
(412, 400, 'video', 'video', 'search', '搜索'),
(413, 400, 'video', 'video', 'pass,reject', '审核'),
(414, 400, 'video', 'video', 'delete,clear,restore,restores', '回收站');

INSERT INTO `cmstop_model` (`modelid`, `name`, `alias`, `description`, `template_list`, `template_show`, `posts`, `comments`, `pv`, `sort`, `disabled`) VALUES
(4, '视频', 'video', '', 'video/list.html', 'video/show.html', 0, 0, 0, 0, 0);

REPLACE INTO `cmstop_setting` (`app`, `var`, `value`) VALUES
('video', 'ccid', '');

ALTER TABLE `cmstop_video`
  ADD CONSTRAINT `cmstop_video_ibfk_1` FOREIGN KEY (`contentid`) REFERENCES `cmstop_content` (`contentid`) ON DELETE CASCADE;