<?php
/*
 * 模型的右键菜单定义,通常复制article模型的配置即可，详细说明及特殊菜单项的设置参考interview
 */
return array(
	array('class' => 'view', 'text' => '查看'),
	array('class' => 'edit', 'text' => '编辑'),
	array('class' => 'code', 'text' => '调用代码', 'handler' => 'vote.code'),

	array('class' => 'remove', 'text' => '删除', 'status' => '!0'),
	array('class' => 'del', 'text' => '删除', 'status' => '0'),
	array('class' => 'restore', 'text' => '还原', 'status' => '0'),
	array('class' => 'createhtml', 'text' => '生成', 'status' => '6'),
	array('class' => 'unpublish', 'text' => '下线', 'status' => '6'),
	array('class' => 'approve', 'text' => '送审', 'status' => '1,2'),
	array('class' => 'pass', 'text' => '通过', 'status' => '2'),
	array('class' => 'publish', 'text' => '发布', 'status' => '1'),
	array('class' => 'reject', 'text' => '退稿', 'status' => '2,3'),

	array('class' => 'forward', 'text' => '转发','status' => '6','separator' => 1),
	array('class' => 'move', 'text' => '移动'),
	array('class' => 'reference', 'text' => '引用'),
	array('class' => 'score', 'text' => '评分'),
	array('class' => 'note', 'text' => '批注'),
	array('class' => 'version', 'text' => '版本'),
	array('class' => 'log', 'text' => '日志'),
);