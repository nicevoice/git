<?php
define('CMSTOP_START_TIME', microtime(true));
define('RUN_CMSTOP', true);
define('CRON_PATH', dirname(__FILE__));
define('IN_ADMIN', 1);
define('INTERNAL', 1);

echo "usearch indexer begin, now: " .date('Y-m-d H:i:s') . "\r\n";
// 单线程判断
$pid = CRON_PATH . '/dms.pid';
// 清除文件状态缓存
//clearstatcache 
if(file_exists($pid))
{
	if(filemtime($pid) + 3610 < time())
	{
		echo "running over 10 hours, kill now \r\n";
		@unlink($pid);
	}
	else
	{
		exit("Running... \r\n\r\n\r\n");
	}
}
// 记录pid
@file_put_contents($pid,"");

require dirname(CRON_PATH) . '/cmstop.php';

$cmstop = new cmstop('admin');
$cmstop->execute('dms', 'attachment', 'interval');

@unlink($pid);