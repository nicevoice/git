<?php
set_time_limit(0);
error_reporting(E_ALL);
$allowExt = "txt;shtml;html;js;css;txt;";	// 定义允许传送的文件格式
define('CMSTOP_START_TIME', microtime(true));
define('RUN_CMSTOP', true);
define('IN_ADMIN', 1);
define('INTERNAL', 1);
define('CDN_ROOT', dirname(__FILE__));

include CDN_ROOT .'/include/memcache.php';
include CDN_ROOT .'/include/queue.php';
include CDN_ROOT .'/include/function.php';
$Queue = new myQueue();

if($_SERVER['argc'] > 1)
{
	$action = $_SERVER['argv'][1];
	if (substr($action,0,5) == '--do=')
	{
		$action = substr($action,5);
	}
	else
	{
		$action = "";	
	}
	switch ($action)
	{
		// 写入队列
		case 'write':
			$info = parseEvent();
			if(!filterExt($info['ext']))
			{
				exit;
			}
			if($id = $Queue->write($info['path']))
			{
				//exit("queue write ok, queueID: $id ,fileName: " .$info['path'] ." \n");
				exit;
			}
			else {
				exit("queue write failed! maybe memcache is crashed! \n");
			}
			break;
		// 执行队列
		case 'exec':
			$stat = $Queue->stat();
			if($stat['curid'] < $stat['maxid'])
			{
				for($i=$stat['curid']+1;$i<=$stat['maxid'];$i++)
				{
					if($info = $Queue->read($i))
					{
						if(cdnControl($info['data']))
						{
							// 更新成功，删除该队列
							$Queue->delete($info['id']);
							$Queue->updateStat($info['id']);
						}
					}
					else
					{
						// 如果队列读取失败，尝试下一个
						//exit("queue execute failed! \n");
					}
				}
				//exit("queue execute complate! Total: " .($stat['maxid'] - $stat['curid']) ."\n");
				exit;
			}
			else {
				//exit("queue is empty! \n");
				exit;
			}
			break;
		// 显示队列状态
		case 'stat':
			$stat = $Queue->stat('detail');
			echo "--- CmsTop Queue Stat --- \n";
			echo "Total: " .($stat['stat']['maxid'] - $stat['stat']['curid']) ."\t";
			echo "maxid: " .$stat['stat']['maxid'] ."\t curid: " .$stat['stat']['curid'] ."\n";
			echo "Detail: \n";
			if(empty($stat['queue']))
			{
				$stat['queue'] = array();
			}
			foreach ($stat['queue'] as $v)
			{
				echo $v['id'] ."\t\t\t" .$v['data'] ."\n";
			}
			break;
		// 清空队列
		case 'clean':
			$stat = $Queue->clean();
			echo "Clean Ok, Total: " .$stat ."\n";
			exit;			
			break;
		// 未定义
		default:
			exit("action not defined! \n");
			break;
	}
}

/*
 * 执行CDN接口，将队列中的文件变化，传递给CDN控制器
 * -- 要求，对端接口不能输出任何数据，只使用 return 返回执行结果
 * @param string $path	文件的绝对路径
 * @return bolean
 */
function cdnControl($path)
{
	require_once CDN_ROOT.'/../../cmstop.php';
	$cmstop = new cmstop('admin');
	$_SERVER['path'] = $path;
	$_SERVER['interval'] = 1;
	$result = $cmstop->execute('cdn', 'cdn', 'interval');
	if (isset($result['state']))
	{
		if($result['state'])
		{
			//echo date('Y-m-d H:i:s') ."\t" .$path ."\t";
			//echo "cdn send ok! \n";
			return TRUE;
		}
		else
		{
			echo date('Y-m-d H:i:s') ."\t" .$path ."\t";
			echo "cdn send error: " .$result['error'] ."\n";
			return FALSE;
		}
	}
	else
	{
		echo "cdn interface error \n";
		return FALSE;
	}
}
