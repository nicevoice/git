SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `cmstop_history`;
CREATE TABLE `cmstop_history` (
  `hid` smallint(5) unsigned NOT NULL auto_increment,
  `cronid` smallint(5) unsigned default NULL,
  `alias` varchar(30) NOT NULL,
  `url` varchar(64) NOT NULL,
  `userid` mediumint(8) unsigned default NULL,
  `addtime` int(10) unsigned default NULL,
  PRIMARY KEY  (`hid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='历史页面,name,disabled,day,week等信息在计划任务表; ' AUTO_INCREMENT=3 ;