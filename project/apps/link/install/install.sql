SET FOREIGN_KEY_CHECKS=0;


INSERT INTO `cmstop_aca` (`acaid`, `parentid`, `app`, `controller`, `action`, `name`) VALUES
(300, NULL, 'link', NULL, NULL, '链接');

INSERT INTO `cmstop_model` (`modelid`, `name`, `alias`, `description`, `template_list`, `template_show`, `posts`, `comments`, `pv`, `sort`, `disabled`) VALUES
(3, '链接', 'link', '', 'link/list.html', '', 0, 0, 0, 0, 0);