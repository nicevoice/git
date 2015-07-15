SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE IF NOT EXISTS `cmstop_dms_app` (
  `appid` smallint(3) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(32) NOT NULL,
  `name` varchar(100) NOT NULL,
  `domain` varchar(30) NOT NULL,
  `ip` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`appid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `cmstop_dms_article` (
  `articleid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `source` varchar(40) DEFAULT '',
  `author` varchar(30) DEFAULT '',
  `description` text,
  `content` mediumtext,
  `createtime` int(10) unsigned NOT NULL,
  `updatetime` int(10) unsigned NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `expand` text,
  `tags` varchar(100) DEFAULT '',
  PRIMARY KEY (`articleid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `cmstop_dms_log` (
  `logid` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `appid` smallint(3) unsigned NOT NULL,
  `operator` varchar(255) DEFAULT '',
  `modelid` smallint(3) unsigned NOT NULL,
  `target` mediumint(8) unsigned NOT NULL,
  `action` varchar(30) NOT NULL,
  `data` text,
  `time` int(10) unsigned NOT NULL,
  `ip` varchar(16) NOT NULL,
  PRIMARY KEY (`logid`),
  KEY `appid` (`appid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



CREATE TABLE IF NOT EXISTS `cmstop_dms_model` (
  `modelid` smallint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `alias` varchar(30) NOT NULL DEFAULT '',
  `mainindex` varchar(30) DEFAULT NULL,
  `deltaindex` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`modelid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `cmstop_dms_picture` (
  `pictureid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `source` varchar(40) NOT NULL DEFAULT '',
  `author` varchar(30) NOT NULL DEFAULT '',
  `description` text,
  `createtime` int(10) unsigned NOT NULL,
  `updatetime` int(10) unsigned NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `expand` text,
  `tags` varchar(100) NOT NULL,
  `path` varchar(255) NOT NULL,
  `serverid` smallint(3) NOT NULL,
  PRIMARY KEY (`pictureid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `cmstop_dms_picture_group` (
  `groupid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `source` varchar(40) NOT NULL DEFAULT '',
  `author` varchar(30) NOT NULL DEFAULT '',
  `description` text,
  `createtime` int(10) unsigned NOT NULL,
  `updatetime` int(10) unsigned NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `expand` text,
  `pictures` text,
  `tags` varchar(100) NOT NULL DEFAULT '',
  `cover` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`groupid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



CREATE TABLE IF NOT EXISTS `cmstop_dms_server` (
  `serverid` smallint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `url` varchar(100) NOT NULL,
  PRIMARY KEY (`serverid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



CREATE TABLE IF NOT EXISTS `cmstop_dms_setting` (
  `app` varchar(15) NOT NULL DEFAULT 'dms',
  `var` varchar(30) NOT NULL,
  `value` text,
  KEY `var` (`var`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `cmstop_dms_tag` (
  `tagid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `total` mediumint(5) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`tagid`),
  KEY `name` (`name`,`total`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


INSERT INTO `cmstop_dms_model` (`modelid`, `name`, `alias`, `mainindex`, `deltaindex`) VALUES
(1, '文章', 'article', 'dms_article', 'delta_dms_article'),
(2, '图片', 'picture', NULL, NULL),
(3, '图组', 'picture_group', 'dms_picture_group', 'delta_dms_picture_group')
