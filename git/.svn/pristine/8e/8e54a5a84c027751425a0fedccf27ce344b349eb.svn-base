SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `cmstop_mood`;
CREATE TABLE `cmstop_mood` (
  `moodid` tinyint(2) unsigned NOT NULL auto_increment,
  `name` varchar(20) NOT NULL,
  `image` varchar(100) NOT NULL,
  `sort` tinyint(2) unsigned NOT NULL,
  PRIMARY KEY  (`moodid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12;

INSERT INTO `cmstop_mood` (`moodid`, `name`, `image`, `sort`) VALUES
(1, '支持', 'images/zhichi.gif', 1),
(2, '高兴', 'images/gaoxing.gif', 2),
(3, '震惊', 'images/zhenjing.gif', 3),
(4, '愤怒', 'images/fennu.gif', 4),
(5, '无聊', 'images/wuliao.gif', 5),
(6, '无奈', 'images/wunai.gif', 6),
(7, '谎言', 'images/huangyan.gif', 7),
(8, '枪稿', 'images/qianggao.gif', 8),
(9, '不解', 'images/bujie.gif', 9),
(10, '标题党', 'images/biaotidang.gif', 10);


CREATE TABLE IF NOT EXISTS `cmstop_mood_data` (
  `contentid` mediumint(8) unsigned NOT NULL,
  `total` mediumint(8) unsigned NOT NULL default '0',
  `updatetime` int(10) unsigned NOT NULL default '0',
  `m1` mediumint(8) unsigned NOT NULL default '0',
  `m2` mediumint(8) unsigned NOT NULL default '0',
  `m3` mediumint(8) unsigned NOT NULL default '0',
  `m4` mediumint(8) unsigned NOT NULL default '0',
  `m5` mediumint(8) unsigned NOT NULL default '0',
  `m6` mediumint(8) unsigned NOT NULL default '0',
  `m7` mediumint(8) unsigned NOT NULL default '0',
  `m8` mediumint(8) unsigned NOT NULL default '0',
  `m9` mediumint(8) unsigned NOT NULL default '0',
  `m10` mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`contentid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `cmstop_aca` (`acaid`, `parentid`, `app`, `controller`, `action`, `name`) VALUES
(903, NULL, 'mood', NULL, NULL, '心情');

REPLACE INTO `cmstop_setting` (`app`, `var`, `value`) VALUES
('mood', 'votetime', '30');

ALTER TABLE `cmstop_mood_data`
  ADD CONSTRAINT `cmstop_mood_data_ibfk_1` FOREIGN KEY (`contentid`) REFERENCES `cmstop_content` (`contentid`) ON DELETE CASCADE;