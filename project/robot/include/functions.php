<?php
/*
function getDir($contentid) {
	$contentid = sprintf("%06d", $contentid);

	$dir[] = 'data/upload';
	$dir[] = substr($contentid, 0, 2);
	$dir[] = substr($contentid, 2, 2);
	$dir[] = substr($contentid, 4, 2);
	$dir = implode('/',$dir).'/';
	if(!is_dir($dir)) {
		mkdir($dir,0777,true);
	}
	return $dir;
}*/

function write_file($file, $data)
{
	$result = @file_put_contents($file, $data);
	chmod($file, 0777);
	return $result;
}

function cache_write($file, $array, $path = null)
{
	if(!is_array($array)) return false;
	$array = "<?php\nreturn ".var_export($array, true).";";
	$cachefile = ($path ? $path : CACHE_PATH).$file;
	$strlen = write_file($cachefile, $array);
	return $strlen;
}

function cache_read($file, $path = null)
{
	if(!$path) $path = CACHE_PATH;
	$cachefile = $path.$file;
	return @include $cachefile;
}


function str_charset($in_charset, $out_charset, $str_or_arr)
{
	$lang = array(&$in_charset, &$out_charset);
	foreach ($lang as &$l)
	{
		switch (strtolower(substr($l, 0, 2)))
		{
			case 'gb': $l = 'gbk';
			break;
			case 'bi': $l = 'big5';
			break;
			case 'ut': $l = 'utf-8';
			break;
		}
	}
		
	if(is_array($str_or_arr))
	{
		foreach($str_or_arr as &$v)
		{
			$v = str_charset($in_charset, $out_charset.'//IGNORE', $v);
		}
	}
	else
	{
		$str_or_arr = iconv($in_charset, $out_charset.'//IGNORE', $str_or_arr);
	}
	return $str_or_arr;
}

function add_handle(& $handle, $url)
{
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_HEADER, '');
	curl_setopt($curl,CURLOPT_USERAGENT,"Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322; .NET CLR 2.0.50727)");       

	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_multi_add_handle($handle, $curl);
	return $curl;
}

function curl_get_contents($urls) {
		
	//$urls = array('http://www.baidu.com','http://www.soso.com',);
	
	$handle = curl_multi_init();
	$curls = array();
	foreach ($urls as $k=>$url)
	{
		$curls[$k] = add_handle($handle, $url);
	}
	

	$flag = null;
	do {
		curl_multi_exec($handle, $flag);
	} while ($flag > 0);
	
	$result = array();
	foreach($urls as $k=>$url){
		if(200 != curl_getinfo($curls[$k], CURLINFO_HTTP_CODE))
		{	
			curl_multi_remove_handle($handle, $curls[$k]);
			continue;
		}
		$content = curl_multi_getcontent ($curls[$k]) ;
		$result[$url] = $content;
		curl_multi_remove_handle($handle, $curls[$k]);
	}
	curl_multi_close($handle);
	if(count($urls)>1) {
		return $result;
	} else {
		return array_shift($result);;
	}
}