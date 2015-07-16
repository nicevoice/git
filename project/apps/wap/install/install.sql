SET FOREIGN_KEY_CHECKS=0;

INSERT INTO `#table_aca` (`acaid`, `parentid`, `app`, `controller`, `action`, `name`) VALUES
(909, NULL, 'wap', NULL, NULL, 'Wap');

REPLACE INTO `cmstop_setting` (`app`, `var`, `value`) VALUES
('wap', 'category_pagesize', '5'),
('wap', 'catids', 'array (\n  0 => ''1'',\n  1 => ''4'',\n  2 => ''5'',\n  3 => ''6'',\n  4 => ''2'',\n  5 => ''7'',\n  6 => ''8'',\n  7 => ''9'',\n  8 => ''10'',\n  9 => ''3'',\n  10 => ''11'',\n  11 => ''12'',\n  12 => ''13'',\n)'),
('wap', 'comment_pagesize', '10'),
('wap', 'content_expires', '0'),
('wap', 'content_words', '1000'),
('wap', 'image_height', '0'),
('wap', 'image_size', 'array(''width''=>''100'',''height''=>''100'')'),
('wap', 'image_width', '128'),
('wap', 'index_pagesize', '15'),
('wap', 'index_weight', '80'),
('wap', 'list_pagesize', '20'),
('wap', 'list_weight', '60'),
('wap', 'logo', 'wap_logo.jpg'),
('wap', 'modelids', 'array (\n  0 => ''1'',\n  1 => ''2'',\n)'),
('wap', 'open', '1'),
('wap', 'template_article', 'wap/article.html'),
('wap', 'template_comment', 'wap/comment.html'),
('wap', 'template_index', 'wap/index.html'),
('wap', 'template_list', 'wap/list.html'),
('wap', 'template_picture', 'wap/picture.html'),
('wap', 'webname', 'CmsTop');