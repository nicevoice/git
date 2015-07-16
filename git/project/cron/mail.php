<?php
define('CMSTOP_START_TIME', microtime(true));
define('RUN_CMSTOP', true);
define('CRON_PATH', dirname(__FILE__));
require dirname(CRON_PATH) . '/cmstop.php';

$time_stamp = time();
$time_pretty = date('Y-m-d H:i:s', $time_stamp);

// 检查是否正在运行或已超时
$pid_file = CRON_PATH . '/mail.pid';
if (is_file($pid_file))
{
    // 执行超时，10 分钟
    if ((filemtime($pid_file) + 600) < $time_stamp)
    {
        echo "$time_pretty: Mail Queue running over 10 minutes, now killing it..." . PHP_EOL;
        unlink($pid_file);
    }
    // 正在运行，取消本次操作
    else
    {
        echo "$time_pretty: Mail Queue is running." . PHP_EOL;
        exit;
    }
}

// 锁定，防止起多个进程
file_put_contents($pid_file, '');

$interval = value(config('mail'), 'interval', 20);
$interval_size = value(config('mail'), 'interval_size', 50);

// 以无限循环方式运行，不推荐，容易造成内存泄露
/*while (true)
{
    $queue = & factory::queue('mail');
    echo $queue->interval($interval_size);
    sleep($interval);
}*/

// 以常规方式运行，成功后退出，依赖计划任务
$queue = & factory::queue('mail');
echo $queue->interval($interval_size);

// 删除锁定文件
unlink($pid_file);