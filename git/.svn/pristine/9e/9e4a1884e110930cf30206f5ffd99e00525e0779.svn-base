SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `cmstop_cdn`;

CREATE TABLE `cmstop_cdn` (
`cdnid` SMALLINT( 5 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`name` VARCHAR( 20 ) NOT NULL,
`tid` SMALLINT( 5 ) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cmstop_cdn_rules`;

CREATE TABLE `cmstop_cdn_rules` (
`id` SMALLINT( 5 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`cdnid` SMALLINT( 5 ) NOT NULL ,
`path` VARCHAR( 100 ) NULL ,
`url` VARCHAR( 100 ) NULL ,
INDEX ( `cdnid` )
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cmstop_cdn_parameter`;

CREATE TABLE `cmstop_cdn_parameter` (
`id` SMALLINT( 5 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`cdnid` SMALLINT(5) NOT NULL,
`key` VARCHAR( 20 ) NOT NULL,
`value` VARCHAR( 200 ) NULL,
INDEX(`cdnid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `cmstop_cdn_type`;

CREATE TABLE `cmstop_cdn_type` (
`tid` SMALLINT( 5 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`name` VARCHAR( 20 ) NOT NULL,
`parameter` VARCHAR( 200 ) NOT NULL ,
`type` VARCHAR (20) NOT NULL,
`status` TINYINT( 1 ) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

REPLACE INTO `cmstop_cdn_type` (`tid`, `name`, `parameter`, `type`, `status`) VALUES
(1, 'chinacache', '{"user":"\\u7528\\u6237\\u540d:","pswd":"\\u5bc6\\u7801:"}', 'chinacache', 1),
(2, '网宿', '{"user":"\\u7528\\u6237\\u540d:","pswd":"\\u5bc6\\u7801:"}', 'wscp', 1);