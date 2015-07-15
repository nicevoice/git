ALTER TABLE `cmstop_dms_log` CHANGE `operator` `operator` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '';
ALTER TABLE `cmstop_dms_tag` CHANGE `total` `total` MEDIUMINT( 5 ) UNSIGNED NOT NULL DEFAULT '1';
ALTER TABLE `cmstop_dms_picture_group` CHANGE `cover` `cover` INT( 11 ) UNSIGNED NOT NULL DEFAULT '0';
-- 2011.12.19 IP改为int型
ALTER TABLE `cmstop_dms_log` CHANGE `ip` `ip` INT(10) NOT NULL;
-- 2011.12.20 增加quote表
CREATE TABLE IF NOT EXISTS `cmstop_dms_quote` (
  `quoteid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `target` int(10) unsigned NOT NULL,
  `modelid` smallint(3) unsigned NOT NULL,
  `appid` smallint(3) unsigned NOT NULL,
  `time` int(10) unsigned NOT NULL,
  `operator` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`quoteid`),
  KEY `target` (`target`,`appid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
-- 2011.12.21 app新增priv字段
ALTER TABLE `cmstop_dms_app` ADD `priv` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '';
-- 2011.12.21 新增priv表
CREATE TABLE IF NOT EXISTS `cmstop_dms_priv` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `source` smallint(3) unsigned NOT NULL,
  `target` smallint(3) unsigned NOT NULL,
  `priv` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `target` (`target`,`priv`,`source`),
  KEY `source` (`source`,`target`,`priv`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
-- 2011.12.21 模型表新加appid字段
ALTER TABLE `cmstop_dms_article` ADD `appid` SMALLINT( 3 ) UNSIGNED NOT NULL DEFAULT '0' AFTER `articleid`;
ALTER TABLE `cmstop_dms_picture` ADD `appid` SMALLINT( 3 ) UNSIGNED NOT NULL DEFAULT '0' AFTER `pictureid` ;
ALTER TABLE `cmstop_dms_picture_group` ADD `appid` SMALLINT( 3 ) UNSIGNED NOT NULL DEFAULT '0' AFTER `groupid`;

-- 2012.2.9 增加附件表
CREATE TABLE IF NOT EXISTS `cmstop_dms_attachment` (
  `attachmentid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `source` varchar(40) NOT NULL,
  `author` varchar(30) NOT NULL,
  `description` text NOT NULL,
  `createtime` int(10) unsigned NOT NULL,
  `updatetime` int(10) unsigned NOT NULL,
  `size` int(10) unsigned NOT NULL DEFAULT '0',
  `ext` char(4) DEFAULT NULL,
  `status` tinyint(1) NOT NULL,
  `expand` text NOT NULL,
  `path` varchar(255) NOT NULL,
  `tags` int(100) unsigned NOT NULL,
  PRIMARY KEY (`attachmentid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--2012.2.22  引用表新增状态字段,文章表添加引用数字段
ALTER TABLE `cmstop_dms_article` ADD `quote` SMALLINT( 3 ) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `cmstop_dms_quote` ADD `disable` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT '0',
ADD `status` VARCHAR( 10 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ;

--2012.2.23 附件表添加appid字段
ALTER TABLE `cmstop_dms_attachment` ADD `appid` SMALLINT( 3 ) UNSIGNED NOT NULL AFTER `attachmentid`;
--2012.2.23 附件表增加serverid字段
ALTER TABLE `cmstop_dms_attachment` ADD `serverid` SMALLINT( 3 ) UNSIGNED NOT NULL DEFAULT '1'

--2012.3.9  应用表权限字段增加长度到500
ALTER TABLE `cmstop_dms_app` CHANGE `priv` `priv` VARCHAR( 500 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;