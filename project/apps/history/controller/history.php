<?php
class controller_history extends history_controller_abstract
{
	function getHours()
	{
		$dir = WWW_PATH."history/{$_GET['alias']}";
		substr($dir, -1) == '!' && $dir = substr($dir, 0, -1)	;
		$dir .= "/{$_GET['year']}-{$_GET['month']}";
		$dir = realpath($dir).DS;
		if(strpos($dir, WWW_PATH) == -1 || !is_dir($dir)) exit('path error');	//路径检验
		$day = intval($_GET['day']);
		if($day < 10) $day = '0'.$day;
		$files = glob($dir."$day*.*");
		$hours = array();
		foreach ($files as $f)
		{
            //[extention] 可能有问题，原来是
            //preg_match('#\d\d-(\d\d)\.shtml'#', $f, $temp);
            preg_match('#\d\d-(\d\d)'.SHTML.'#', $f, $temp);
			$hours[] = $temp[1];
		}
		$hours = $this->json->encode($hours);
		exit($_GET['jsoncallback']."($hours);");
	}
}