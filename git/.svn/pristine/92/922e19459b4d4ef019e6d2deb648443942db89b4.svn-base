SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `cmstop_guestbook`;
CREATE TABLE `cmstop_guestbook` (
  `gid` mediumint(8) unsigned NOT NULL auto_increment,
  `typeid` tinyint(2) unsigned NOT NULL,
  `title` varchar(100) NOT NULL,
  `content` mediumtext NOT NULL,
  `userid` mediumint(8) unsigned default NULL,
  `username` varchar(15) default NULL,
  `gender` tinyint(1) unsigned NOT NULL default '0',
  `email` varchar(40) default NULL,
  `qq` varchar(15) default NULL,
  `msn` varchar(40) default NULL,
  `telephone` varchar(20) default NULL,
  `address` varchar(100) default NULL,
  `homepage` varchar(25) default NULL,
  `isview` tinyint(1) unsigned NOT NULL default '0',
  `ip` char(15) NOT NULL,
  `addtime` int(10) unsigned NOT NULL,
  `reply` mediumtext,
  `replyer` varchar(20) default NULL,
  `replytime` int(10) unsigned default NULL,
  PRIMARY KEY  (`gid`),
  KEY `typeid` (`typeid`,`gid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cmstop_guestbook_type`;
CREATE TABLE `cmstop_guestbook_type` (
  `typeid` tinyint(2) unsigned NOT NULL auto_increment,
  `name` varchar(20) NOT NULL,
  `count` mediumint(8) unsigned NOT NULL default '0',
  `sort` tinyint(2) unsigned NOT NULL default '0',
  PRIMARY KEY  (`typeid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


INSERT INTO `cmstop_guestbook_type` (`typeid`, `name`, `count`, `sort`) VALUES
(1, '反馈', 0, 0),
(2, '建议', 0, 0);

INSERT INTO `cmstop_aca` (`acaid`, `parentid`, `app`, `controller`, `action`, `name`) VALUES
(902, NULL, 'guestbook', NULL, NULL, '留言本');

INSERT INTO `cmstop_setting` (`app`, `var`, `value`) VALUES
('guestbook', 'guestbookname', '欢迎留言'),
('guestbook', 'iscode', '1'),
('guestbook', 'managelist', 'cmstop'),
('guestbook', 'memberguest', '1'),
('guestbook', 'option', 'array()'),
('guestbook', 'pagesize', '25'),
('guestbook', 'repliedshow', '0'),
('guestbook', 'replymax', '1000'),
('guestbook', 'sensekeyword', ''),
('guestbook', 'showmanage', '思拓合众'),
('guestbook', 'unguestlist', '1');