SET FOREIGN_KEY_CHECKS=0;

INSERT INTO `cmstop_aca` (`acaid`, `parentid`, `app`, `controller`, `action`, `name`) VALUES
(905, NULL, 'rss', NULL, NULL, 'RSS');

REPLACE INTO `cmstop_setting` (`app`, `var`, `value`) VALUES
('rss', 'category', 'array (\n  0 => ''1'',\n  1 => ''4'',\n  2 => ''5'',\n  3 => ''6'',\n  4 => ''2'',\n  5 => ''7'',\n  6 => ''8'',\n  7 => ''9'',\n  8 => ''10'',\n  9 => ''3'',\n  10 => ''11'',\n  11 => ''12'',\n  12 => ''13'',\n)'),
('rss', 'catid', 'array (\n  0 => ''4'',\n  1 => ''5'',\n  2 => ''18'',\n  3 => ''16'',\n  4 => ''27'',\n  5 => ''2'',\n  6 => ''9'',\n  7 => ''10'',\n  8 => ''11'',\n  9 => ''12'',\n  10 => ''32'',\n)'),
('rss', 'output', 'digest'),
('rss', 'size', '30'),
('rss', 'weight', 'array (\n  ''min'' => 10,\n  ''max'' => 100,\n)');