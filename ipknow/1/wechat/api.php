<?php

$mysql = new SaeMysql();
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$size = isset($_GET['size']) ? $_GET['size'] : 10;
$offset = ($page - 1) * $size;
$sql = "SELECT ID as id,post_date,post_title FROM `wp_posts` WHERE post_status='publish' AND post_password = '' AND post_type='post' ORDER BY post_date DESC LIMIT {$offset}, {$size} ";
$data = $mysql->getData( $sql );
$mysql->closeDb();
exit(json_encode($data));
?>