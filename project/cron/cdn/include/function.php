<?php
/*
 * inofity监控接口
 * 用于接收服务器上通过命令行传递的参数
 * 支持三个参数，必须依次提交，至少提交第一个参数
 * @param string $path 文件路径（绝对），参数形式 --path=/www/cmstop/test.php
 * @param string $event 发生的事件，参数形式 --event=MODIFY
 * @param string $time 事件发生时间，参数形式 --time=2011-11-11 10:10:10
 * @return array
 */
function parseEvent()
{
	$info = array('ext','path','event','time');
	for ($i=1;$i<4;$i++)
	{
		$argn = $i + 1;	// 要读取的参数从第三个开始（即索引 2）：notify.php --do=write --path=
		if ($_SERVER['argc'] > $i + 1)
		{
			$str = $_SERVER['argv'][$i + 1];
			// 组成参数形式前缀 --path=
			$key = $info[$i];
			$vkey = '--' .$info[$i] .'=';
			$vlen = strlen($vkey);
			if (substr($str, 0, $vlen) == $vkey)
			{
				$$key = substr($str, $vlen);
			}
			else
			{
				$$key = '';
			}
		}
	}
	$ext = getfileExt($path);
	foreach ($info as $key) {
		$data[$key] = $$key;
		unset($$key);
	}
	return $data;
}

/* 
 * 获取一个指定文件路径的文件扩展名
 */
function getfileExt($fileName)
{
	if (empty($fileName))
	{
		return ;
	}
	if (strpos($fileName,'.') == FALSE)
	{
		return ;
	}
	$info = pathinfo($fileName);
	return $info['extension'];
}

/*
 * 验证是否是允许传输的文件类型
 */
function filterExt($fileExt)
{
	global $allowExt;
	if (empty($fileExt))
	{
		return FALSE;	
	}
	if (strpos($allowExt, strtolower($fileExt) .';') === FALSE)
	{
		return FALSE;	
	}
	return TRUE;
}
