<?php
/**
 * 转义字符串中html标签,如果参数为数组则遍历
 *	
 * @param mixed $string 待转换的字符
 * @return mixed
 */
function htmlspecialchars_deep($string)
{
	return is_array($string) ? array_map('htmlspecialchars_deep', $string) : htmlspecialchars($string, ENT_QUOTES);
}

/**
 * 使用反斜线引用字符串,如果参数为数组则遍历
 *
 * @param mixed $string 待转换的字符
 * @return mixed
 */
function addslashes_deep($string)
{
	return is_array($string) ? array_map('addslashes_deep', $string) : addslashes($string);
}

/**
 * 使用反斜线引用字符串,如果参数为数组则深度遍历
 *	
 * @param mixed $string 待转换的字符
 * @return mixed
 */
function new_addslashes($string)
{
	if(!is_array($string)) return addslashes($string);
	foreach($string as $key => $val) $string[$key] = new_addslashes($val);
	return $string;
}

/**
 * 使用反斜线引用字符串
 * 
 * @param mixed $string 待转换的对象
 * @return mixed
 */
function addslashes_deep_obj($obj)
{
    if (is_object($obj))
    {
        foreach ($obj as $key => $val)
        {
            $obj->$key = addslashes_deep($val);
        }
    }
    else
    {
        $obj = addslashes_deep($obj);
    }
    return $obj;
}

/**
 * 去掉字符串中的反斜线
 *
 * @param mixed $string 待转换的字符
 * @return mixed
 */
function stripslashes_deep($string)
{
	return is_array($string) ? array_map('stripslashes_deep', $string) : stripslashes($string);
}

/**
 * 清除js数据中的换行与反斜线
 * 
 * @param string $string 待转换的字符
 * @return string
 */
function js_format($string)
{ 
	return addslashes(str_replace(array("\r", "\n"), array('', ''), $string));
}

/**
 * 将html转换成text
 *	
 * @param string $string 待转换的字符
 * @return string
 */
function text_format($string)
{
	return nl2br(str_replace(' ', '&nbsp;', htmlspecialchars($string)));
}

/**
 * 格式化ID
 *	
 * @param mixed $id
 * @return int
 */
function id_format($id)
{
	if (is_numeric($id)) return $id;
	if (is_array($id)) return array_filter($id, 'is_numeric');
	if (strpos($id, ',') !== false) return preg_match("/^([\d]+,)+$/", $id.',') ? explode(',', $id) : false;
    return false;
}

/**
 * 编码转换
 *
 * @param	string	$_in_charset	输入字符集
 * @param	string	$_out_charset	输出字符集
 * @param	mixed	$str_or_ary		内容
 * @return	mixed
 */
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
			$v = str_charset($in_charset, $out_charset, $v);
		}
	}
	else
	{
		$str_or_arr = iconv($in_charset, $out_charset, $str_or_arr);
	}
	return $str_or_arr;
}

/**
 * 实现fputcsv内置函数，将行格式化为 CSV 并写入文件指针
 * 
 * fputcsv() 将一行（用 fields 数组传递）格式化为 CSV 格式并写入由 handle 指定的文件。返回写入字符串的长度，出错则返回 FALSE。 
 * 可选的 delimiter 参数设定字段分界符（只允许一个字符）。默认为逗号：,。 
 * 可选的 enclosure 参数设定字段字段环绕符（只允许一个字符）。默认为双引号："。 
 * 
 * @param resource $fp 存储文件指针
 * @param array $array 数据
 * @param string $delimiter 分界符
 * @param string $enclosure 环绕符
 * @return int
*/
if(!function_exists('fputcsv'))
{
	function fputcsv(&$fp, $array, $delimiter = ',', $enclosure = '"')
	{
		$data = $enclosure.implode($enclosure.$delimiter.$enclosure, $array).$enclosure."\n";
		return fwrite($fp, $data);
	}
}


/**
 * 产生一个随机字符串
 * 
 * @param int $length	字符串长度
 * @param string $chars	随机字符范围
 * @return string
 */
function random($length, $chars = '0123456789')
{
	$hash = '';
	$max = strlen($chars) - 1;
	for($i = 0; $i < $length; $i++)
	{
		$hash .= $chars[mt_rand(0, $max)];
	}
	return $hash;
}

/**
 * 返回文件扩展名
 *
 * @param string $filename 文件路径
 * @return string
 */
function fileext($filename)
{
	return pathinfo($filename, PATHINFO_EXTENSION);
}

/**
 * 将数组转换为字符串
 *
 * @param mixed $array  待转化数组
 * @param string $s		分隔符
 * @return string
 */
function implode_ids($array, $s = ',')
{
	if(empty($array)) return '';
	return is_array($array) ? implode($s, $array) : $array;
}

/**
 * 单词统计
 *
 * @param string $string	待统计文本
 * @param string $charset	字符集
 * @return int
 */
function words_count($string, $charset = 'utf-8')
{
	$string = strip_tags($string);
	$en_count = preg_match_all("/([[:alnum:]]|[[:punct:]])+/", $string, $matches);
	$string = preg_replace("/([[:alnum:]]|[[:space:]]|[[:punct:]])+/", '', $string);
	$zh_count = mb_strlen($string, $charset);
	$count = $en_count + $zh_count;
	return $count;
}

/**
 * 格式化存储单位
 *
 * @param int $size
 * @return string
 */
function size_format($size)
{
	$decimals = 0;
	$suffix = '';
	switch (true)
	{
	case $size >= 1073741824:
		$decimals = 2;
		$size = round($size / 1073741824 * 100) / 100;
		$suffix = 'GB';
		break;
	case $size >= 1048576:
		$decimals = 2;
		$size = round($size / 1048576 * 100) / 100;
		$suffix = 'MB';
		break;
	case $size >= 1024:
		$decimals = 2;
		$size = round($size / 1024 * 100) / 100;
		$suffix = 'KB';
		break;
	default:
		$decimals = 0;
		$suffix = 'B';
	}
	return number_format($size, $decimals) . $suffix;
}

/**
 *	截取字符串
 *	
 * @param string $string	待截取字符串
 * @param int $length		截取长度，每个字符为一个长度，无论中英文
 * @param string $charset	字符字符集
 * @param string $etc		省略符
 * @return string
*/
function str_cutword($string, $length = 80, $charset = "utf-8", $etc = '...')
{
    $start = 0;
    if (! $length) return $string;
    if (function_exists('mb_substr'))
    {
        $slice = mb_substr($string, $start, $length, $charset);
    }
    else
    {
        $re ['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re ['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re ['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re ['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all($re[$charset], $string, $match);
        $slice = join('', array_slice($match [0], $start, $length));
    }
    if ($slice == $string)
    {
        return $slice;
    }
    return $slice . $etc;
}

/**
 * 友好格式化日期：已过去多久
 *
 * @param int $time 输入时间戳
 * @param string $format 时间格式
 * @param boolon $second 是否精确到秒
 * @return string
 */
function time_format($time, $format = 'Y年n月j日 G:i:s', $second = false)
{
	$diff = TIME - $time;
	if ($diff < 60 && $second)
	{
		return $diff.'秒前';
	}
	$diff = ceil($diff/60);
	if ($diff < 60)
	{
		return $diff.'分钟前';
	}
	$d = date('Y,n,j', TIME);
	list($year, $month, $day) = explode(',', $d);
	$today = mktime(0, 0, 0, $month, $day, $year);
	$diff = ($time-$today) / 86400;
	switch (true)
	{
		case $diff < -2:
			break;
		case $diff < -1:
			$format = '前天 '.($second ? 'G:i:s' : 'G:i');
			break;
		case $diff < 0:
			$format = '昨天 '.($second ? 'G:i:s' : 'G:i');
			break;
		default:
			$format = '今天 '.($second ? 'G:i:s' : 'G:i');
	}
	return date($format, $time);
}

/**
 * 友好格式化时时：将转换为时分秒显示
 *
 * @param int $second 秒数
 * @return string
 */
function second_format($second)
 {
	$hour = $minute = 0;
	$str = '';
	if($second > 3600)
	{
		$hour = floor($second / 3600);
		$second = $second % 3600;			
	}
	if($second > 60)
	{
		$minute = floor($second / 60);
		$second = $second % 60;	
	}
	if($hour)
	{
		$str .= $hour ."小时";
	}
	if($minute)
	{
		$str .= $minute ."分";
	}
	if($second)
	{
		$str .= $second ."秒";
	}
	return $str;
 }

/**
 * 截取字符串
 *
 * @param string $string 原始字符串
 * @param int $length 截取长度
 * @param string $dot 省略符
 * @param string $charset 字符集
 * @return string
 */
function str_cut($string, $length, $dot = '...', $charset = 'utf-8')
{
	$strlen = strlen($string);
	if($strlen <= $length) return $string;
	$specialchars = array('&amp;', '&quot;', '&#039;', '&lt;', '&gt;');
	$entities = array('&', '"', "'", '<', '>');
	$string = str_replace($specialchars, $entities, $string);
	$strcut = '';
	if(strtolower($charset) == 'utf-8')
	{
		$n = $tn = $noc = 0;
		while($n < $strlen)
		{
			$t = ord($string[$n]);
			if($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
				$tn = 1; $n++; $noc++;
			} elseif(194 <= $t && $t <= 223) {
				$tn = 2; $n += 2; $noc += 2;
			} elseif(224 <= $t && $t < 239) {
				$tn = 3; $n += 3; $noc += 2;
			} elseif(240 <= $t && $t <= 247) {
				$tn = 4; $n += 4; $noc += 2;
			} elseif(248 <= $t && $t <= 251) {
				$tn = 5; $n += 5; $noc += 2;
			} elseif($t == 252 || $t == 253) {
				$tn = 6; $n += 6; $noc += 2;
			} elseif(bin2hex($string[$n]) >=65281||bin2hex($string[$n])<=65374){
				$tn = 3; $n += 3; $noc += 2;
			} else{
				$n++;
			} 
			if($noc >= $length) break;
		}
		if($noc > $length) $n -= $tn;
		$strcut = substr($string, 0, $n);
	}
	else
	{
		$dotlen = strlen($dot);
		$maxi = $length - $dotlen - 1;
		for($i = 0; $i < $maxi; $i++)
		{
			$strcut .= ord($string[$i]) > 127 ? $string[$i].$string[++$i] : $string[$i];
		}
	}
	if(strlen($strcut) == $strlen)
		return $string;
	else
		return $strcut.$dot;
}

/**
 * 生成缩略图
 *
 * @param string $img     原始图片
 * @param int $width      缩略图宽
 * @param int $height     缩略图高
 * @param int $is_abs     是否为绝对路径
 * @param null $default   当图片不存在时的默认图片
 * @param mixed $cut      new! 是否采用新的裁剪算法，如果为 int，则该值为新算法中对应的裁剪位置
 * @return null|string    缩略图地址
 */
function thumb($img, $width, $height, $is_abs = 1, $default = null, $cut = null)
{
	if(empty($img)) return is_null($default) ? IMG_URL.'images/nopic.gif' : $default;
	if(!extension_loaded('gd')) return $img;
	if (preg_match("/^(".preg_quote(UPLOAD_URL, '/')."|".preg_quote(UPLOAD_PATH, '/').")(.*)$/", $img, $matches)) $img = $matches[2];
	if (strpos($img, '://') || !file_exists(UPLOAD_PATH.$img)) return $img;
    $basename = basename($img);
    $origin_prefix = 'orig_';
    if (strpos($basename, $origin_prefix) === 0)
    {
        $basename = substr($basename, strlen($origin_prefix));
    }
	$newimg = dirname($img).'/thumb_'.$width.'_'.$height.'_'.$basename;
	if(!file_exists(UPLOAD_PATH.$newimg) || filemtime(UPLOAD_PATH.$newimg) < filemtime(UPLOAD_PATH.$img))
	{
		$image = & factory::image();
		$image->set_thumb($width, $height, 100);
        if (! is_null($cut))
        {
            $newimg = $image->thumb_cut(UPLOAD_PATH.$img, UPLOAD_PATH.$newimg, is_int($cut) ? $cut : 0, true) ? $newimg : $img;
        }
        else
        {
            $newimg = $image->thumb(UPLOAD_PATH.$img, UPLOAD_PATH.$newimg) ? $newimg : $img;
        }
	}
	if ($is_abs) $newimg = UPLOAD_URL.$newimg;
	return $newimg;
}

/**
 * 字符编码（对称）
 *
 * @param string $data	待解码内容
 * @param string $key	密钥
 * @return string
 */
function str_encode($data, $key)
{
	return cmstop::encode($data, $key);
}

/**
 * 字符解码（对称）
 *
 * @param string $data	待编码内容
 * @param string $key	密钥
 * @return string
 */
function str_decode($data, $key)
{
	return cmstop::decode($data, $key);
}

/**
 * 数据格式化解码，自动检测是JSON编码还是序列化编码
 *
 * @param string $data	
 * @return string
 */
function decodeData($data)
{
	return $data{0}=='{' ? json_decode($data, true) : unserialize($data);
}

/**
 * 数据格式化编码（JSON）
 *
 * @param string $data
 * @return string
 */
function encodeData($data)
{
	return json_encode($data);
}

/**
 * 字符串编码（escape）
 *
 * @param string $str 待编码字符串
 * @param string $charset 字符集
 * @return string
 */
function escape($str, $charset = 'utf-8')
{
	preg_match_all("/[\x80-\xff].|[\x01-\x7f]+/", $str, $r);
	$ar = $r[0];
	foreach($ar as $k=>$v)
	{
		$ar[$k] = ord($v[0]) < 128 ? rawurlencode($v) : '%u'.bin2hex(iconv($charset, 'UCS-2', $v));
	}
	return join('', $ar);
}

/**
 * 字符串解码（unescape）
 *
 * @param string $str 待解码字符串
 * @param string $charset 字符集
 * @return string
 */
function unescape($str, $charset = 'utf-8')
{
	$str = rawurldecode($str);
	$str = preg_replace("/\%u([0-9A-Z]{4})/es", "iconv('UCS-2', '$charset', pack('H4', '$1'))", $str);
    return $str;
}

/**
 * 路径格式化
 *
 * @param string $dir
 * @return string
 */
function format_dir($dir)
{
	return rtrim(str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $dir), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
}

/**
 * url格式化
 *
 * @param string $url
 * @return string
 */
function format_url($url)
{
	return str_replace("\\", "/", $url);
}

/**
 * 向浏览器输出内容,格式化标准HTTP头
 *
 * @param string $data
 * @return strign
 */
function output($data)
{
	$strlen = strlen($data);
	if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) && $strlen > 255 && extension_loaded('zlib') && !ini_get('zlib.output_compression') && ini_get('output_handler') != 'ob_gzhandler')
	{
		$data = gzencode($data, 4);
		$strlen = strlen($data);
		header('Content-Encoding: gzip');
		header('Vary: Accept-Encoding');
	}
 	header('X-Powered-By: CMSTOP/1.0.0');
	header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
	header('ETag: "'.$strlen.'-'.time().'"');
	header('Accept-Ranges: bytes');
	return $data;
}

/**
 * 获取配置文件一个键的值
 *
 * @param string $file 配置文件
 * @param string $key  配置键
 * @param mixed $default 默认值
 * @return mixed
 */
function config($file, $key = null, $default = null)
{
	return config::get($file, $key, $default);
}

/**
 * 获取app设置
 *
 * @param string $app
 * @param string $var 变量名,默认返回全部
 * @return 
 */
function setting($app, $var = null)
{
	return setting::get($app, $var);
}

/**
 * 查询数据库并缓存
 *
 * @param string $table	表名
 * @param string $id	按主键查询
 * @param string $field	查询字段
 * @return array
 */
function table($table, $id = null, $field = null)
{
	$cache = & factory::cache();

	if ($_cache = $cache->get('table_'.$table))
	{
		if (is_null($id)) return $_cache;
		return is_null($field) ? $_cache[$id] : (isset($_cache[$id][$field]) ? $_cache[$id][$field] : false);
	}
	else 
	{
		if (is_null($id))
		{
			static $result;
			if (!isset($result[$table]))
			{
				$array = array();
				$db = & factory::db();
				$primary = $db->get_primary('#table_'.$table);
				$fields = is_null($field) ? '*' : (strpos($field, $primary) === false ? $primary.','.$field : $field);
				
				$data = $db->select("SELECT $fields FROM `#table_$table` ORDER BY `$primary`");
				if (is_array($data))
				{
					foreach ($data as $k=>$v)
					{
						if (!isset($v[$primary])) break;
						$key = $v[$primary];
						$array[$key] = $v;
					}
				}
				$result[$table] = $array;
			}
			return $result[$table];
		}
		else 
		{
			static $row;
			$key = $table.'_'.$id;
			if (!isset($row[$key]))
			{
				$db = & factory::db();
				$primary = $db->get_primary('#table_'.$table);
				$row[$key] = $db->get("SELECT * FROM `#table_$table` WHERE `$primary`=?", array($id));
			}
			return (is_null($field) && !isset($row[$key][$field])) ? $row[$key] : $row[$key][$field];
		}
	}
}

/**
 * 模拟原db_cache将整表写入缓存
 *
 * @param table 表名
 * @param fields 字段名
 * @return boolean
 */
function table_cache($table = null, $fields="*")
{
	$db		= & factory::db();
	$cache	= & factory::cache();
	
	if (is_null($table))
	{
		$rst	= $db->select('SELECT `tablename`, `allfields` FROM #table_cache');
		foreach ($rst as $item) 
		{
			table_cache($item['tablename'], $item['allfields']);
		}
		$cache->set('cmstop_cache', 1);
		return true;
	}
	if (!$primary = $db->get_primary('#table_'.$table))
	{
		return false;
	}
	$data	= array();
	$rst	= $db->select("SELECT $fields FROM #table_$table ORDER BY `$primary`");
	foreach ($rst as $k=>$v)
	{
		$data[$v[$primary]] = $v;
	}
	return $cache->set("table_$table", $data);
}

/**
 * 生成控制器URL
 *
 * @param string $aca
 * @param string $params
 * @param bool $is_full  是否为绝对路径
 * @return 
 */
function url($aca, $params = null, $is_full = false)
{
	$router = & factory::router();
	return $router->url($aca, $params, $is_full);
}

/**
 * 生成带参数的URL
 *
 * @param string $url 基础url
 * @param array $query 参数
 * @param bool $mode 是否转义
 * @return string
 */
function url_query($url, $query = array(), $mode = false)
{
	if ($query)
	{
		$data = parse_url($url);
		if (!$data) return false;
		if (isset($data['query']))
		{
			parse_str($data['query'], $q);
			$query = array_merge($q, $query);
		}
		$data['query'] = http_build_query($query);
		$url = http_build_url($data, $mode);
	}
	return $url;
}

/**
 * 根据数组创建URL
 *
 * @param array $data
 * @param bool $mode 是否转义
 * @return string
 */
function http_build_url($data, $mode = false)
{
	if (!is_array($data)) return false;
	$url = isset($data['scheme']) ? $data['scheme'].'://' : '';
	if (isset($data['user'])) $url .= $data['user'];
	if (isset($data['pass'])) $url .= ':'.$data['pass'];
	if (isset($data['user'])) $url .= '@';
	if (isset($data['host'])) $url .= $data['host'];
	if (isset($data['port'])) $url .= ':'.$data['port'];
	if (isset($data['path'])) $url .= $data['path'];
	if (isset($data['query'])) $url .= '?'.($mode ? str_replace('&', '&amp;', $data['query']) : $data['query']);
	if (isset($data['fragment'])) $url .= '#'.$data['fragment'];
	return $url;
}

/**
 * 分页函数
 *
 * @param int $total 总条目
 * @param int $page	当前页码
 * @param int $pagesize 每页条数
 * @param int $offset 页码显示数量控制（n*2+1）
 * @param string $url 基础URL
 * @param bool $mode 是否转义
 * @return string
 */
function pages($total, $page = 1, $pagesize = 20, $offset = 2, $url = null, $mode = false)
{
	if($total <= $pagesize) return '';
	$page = max(intval($page), 1);
	$pages = ceil($total/$pagesize);
	$page = min($pages, $page);
	$prepage = max($page-1, 1);
	$nextpage = min($page+1, $pages);
	$from = max($page - $offset, 2);
	if ($pages - $page - $offset < 1) $from = max($pages - $offset*2 - 1, 2);
	$to = min($page + $offset, $pages-1);
	if ($page - $offset < 2) $to = min($offset*2+2, $pages-1);
	$more = 1;
	if ($pages <= ($offset*2+5))
	{
		$from = 2;
		$to = $pages - 1;
		$more = 0;
	}
	$str = '';
	$str .= '<li><a href="'.pages_url($url, $prepage, $mode).'">上一页</a></li>';
	$str .= $page == 1 ? '<li><a href="'.pages_url($url, 1, $mode).'" class="now">1</a></li>' : '<li><a href="'.pages_url($url, 1, $mode).'">1'.($from > 2 && $more ? '...' : '').'</a></li>';
	if ($to >= $from)
	{
		for($i = $from; $i <= $to; $i++)
		{
			$str .= $i == $page ? '<li><a href="'.pages_url($url, $i, $mode).'" class="now">'.$i.'</a></li>' : '<li><a href="'.pages_url($url, $i, $mode).'">'.$i.'</a></li>';
		}
	}
	$str .= $page == $pages ? '<li><a href="'.pages_url($url, $pages, $mode).'" class="now">'.$pages.'</a></li>' : '<li><a href="'.pages_url($url, $pages, $mode).'">'.($to < $pages-1 && $more ? '...' : '').$pages.'</a></li>';
	$str .= '<li><a href="'.pages_url($url, $nextpage, $mode).'">下一页</a></li>';
	return $str;
}

function pages3536($total, $page = 1, $pagesize = 20, $offset = 2, $url = null, $mode = false)
{
	if($total <= $pagesize) return '';
	$page = max(intval($page), 1);
	$pages = ceil($total/$pagesize);
	$page = min($pages, $page);
	$prepage = max($page-1, 1);
	$nextpage = min($page+1, $pages);
	$from = max($page - $offset, 2);
	if ($pages - $page - $offset < 1) $from = max($pages - $offset*2 - 1, 2);
	$to = min($page + $offset, $pages-1);
	if ($page - $offset < 2) $to = min($offset*2+2, $pages-1);
	$more = 1;
	if ($pages <= ($offset*2+5))
	{
		$from = 2;
		$to = $pages - 1;
		$more = 0;
	}
	$str = '';
	$str .= '<a href="'.pages_url($url, $prepage, $mode).'">上一页</a>';
	$str .= $page == 1 ? '<a href="'.pages_url($url, 1, $mode).'" class="cur">1</a>' : '<a href="'.pages_url($url, 1, $mode).'">1'.($from > 2 && $more ? '...' : '').'</a>';
	if ($to >= $from)
	{
		for($i = $from; $i <= $to; $i++)
		{
			$str .= $i == $page ? '<a href="'.pages_url($url, $i, $mode).'" class="cur">'.$i.'</a>' : '<a href="'.pages_url($url, $i, $mode).'">'.$i.'</a>';
		}
	}
	$str .= $page == $pages ? '<a href="'.pages_url($url, $pages, $mode).'" class="cur">'.$pages.'</a>' : '<a href="'.pages_url($url, $pages, $mode).'">'.($to < $pages-1 && $more ? '...' : '').$pages.'</a>';
	$str .= '<a href="'.pages_url($url, $nextpage, $mode).'">下一页</a>';
	return $str;
}
/**
 * 生成分页URL
 *
 * @param string $url 基础URL
 * @param int $page 分页页码
 * @param boolean $mode 是否转义
 * @return string
 */
function pages_url($url, $page, $mode = false)
{
	if (!$url) $url = URL;
	if (strpos($url, '$page') === false)
	{
		$url = url_query($url, array('page'=>$page), $mode);
	}
	else 
	{
		eval("\$url = \"$url\";");
	}
	return $url;
}

/**
 * SQL: 获得某个时间之后条件语句
 *
 * @param string $field
 * @param string $maxtime 时间戳
 * @return string
 */
function where_mintime($field, $mintime)
{
	if (!$mintime) return ;
	$mintime = trim($mintime);
	if (!is_numeric($mintime))
	{
		if (strlen($mintime) == 10) $mintime .= ' 00:00:00';
		$mintime = strtotime($mintime);
	}
	$where = "$field>=$mintime";
	return $where;
}

/**
 * SQL: 获得某个时间之前条件语句
 *
 * @param string $field
 * @param string $maxtime 时间戳
 * @return string
 */
function where_maxtime($field, $maxtime)
{
	if (!$maxtime) return ;
	$maxtime = trim($maxtime);
	if (!is_numeric($maxtime))
	{
		if (strlen($maxtime) == 10) $maxtime .= ' 23:59:59';
		$maxtime = strtotime($maxtime);
	}
	$where = "$field<=$maxtime";
	return $where;
}

/**
 * SQL: 获得键字查询语句
 *
 * @param string $field
 * @param string $keywords
 * @return string
 */
function where_keywords($field, $keywords)
{
	$keywords = trim($keywords);
	if ($keywords === '') return ;
	$keywords = preg_replace("/\s+/", '%', $keywords);
	$where = "$field LIKE '%$keywords%'";
	return $where;
}

/**
 * 写入文件
 *
 * @param string $file 文件名
 * @param string $data 文件内容
 * @param boolean $append 是否追加写入
 * @return int
 */
function write_file($file, $data, $append = false)
{
	$dir = dirname($file);
	if (!is_dir($dir)) folder::create($dir);

    $result = false;
    $fp = @fopen($file, $append ? 'ab' : 'wb');
    if ($fp && @flock($fp, LOCK_EX))
    {
        $result = @fwrite($fp, $data);
        @flock($fp, LOCK_UN);
        @fclose($fp);
        @chmod($file, 0777);
    }

	return $result;
}

/**
 * 读取缓存
 *
 * @param string $file 文件名
 * @param string $path 文件路径，默认为CACHE_PATH
 * @param boolean $iscachevar 是否启用缓存
 * @return array
 */
function cache_read($file, $path = null, $iscachevar = 0)
{
	if(!$path) $path = CACHE_PATH;
	$cachefile = $path.$file;
	if($iscachevar)
	{
		global $TEMP;
		$key = 'cache_'.substr($file, 0, -4);
		return isset($TEMP[$key]) ? $TEMP[$key] : $TEMP[$key] = @include $cachefile;
	}
	return @include $cachefile;
}

/**
 * 写入缓存
 *
 * @param string $file 文件名
 * @param array $array 缓存内容
 * @param string $path 文件路径，默认CACHE_PATH
 * @return int
 */
function cache_write($file, $array, $path = null)
{
	if(!is_array($array)) return false;
	$array = "<?php\nreturn ".var_export($array, true).";";
	$cachefile = ($path ? $path : CACHE_PATH).$file;
	$strlen = write_file($cachefile, $array);
	return $strlen;
}

/**
 * 删除缓存
 *
 * @param string $file
 * @param string $path
 * @return boolean
 */
function cache_delete($file, $path = '')
{
	$cachefile = ($path ? $path : CACHE_PATH).$file;
	return @unlink($cachefile);
}

/**
 * 写入php错误日志
 *
 * @param string $errno 错误编号
 * @param string $errmsg 错误信息
 * @param string $filename 错误文件
 * @param string $linenum 错误行数
 * @param mixed $vars 错误参数
 */
function php_error_log($errno, $errmsg, $filename, $linenum, $vars)
{
	$filename = str_replace(ROOT_PATH, '.', $filename);
	$filename = str_replace("\\", '/', $filename);
	if(!defined('E_STRICT')) define('E_STRICT', 2048);
	$dt = date('Y-m-d H:i:s');
	$errortype = array (
	E_ERROR           => 'Error',
	E_WARNING         => 'Warning',
	E_PARSE           => 'Parsing Error',
	E_NOTICE          => 'Notice',
	E_CORE_ERROR      => 'Core Error',
	E_CORE_WARNING    => 'Core Warning',
	E_COMPILE_ERROR   => 'Compile Error',
	E_COMPILE_WARNING => 'Compile Warning',
	E_USER_ERROR      => 'User Error',
	E_USER_WARNING    => 'User Warning',
	E_USER_NOTICE     => 'User Notice',
	E_STRICT          => 'Runtime Notice'
	);
	$user_errors = array(E_USER_ERROR, E_USER_WARNING, E_USER_NOTICE);
	$err = "<errorentry>\n";
	$err .= "\t<datetime>" . $dt . "</datetime>\n";
	$err .= "\t<errornum>" . $errno . "</errornum>\n";
	$err .= "\t<errortype>" . $errortype[$errno] . "</errortype>\n";
	$err .= "\t<errormsg>" . $errmsg . "</errormsg>\n";
	$err .= "\t<scriptname>" . $filename . "</scriptname>\n";
	$err .= "\t<scriptlinenum>" . $linenum . "</scriptlinenum>\n";
	if (in_array($errno, $user_errors))
	{
		$err .= "\t<vartrace>" . wddx_serialize_value($vars, "Variables") . "</vartrace>\n";
	}
	$err .= "</errorentry>\n\n";
	$logfile = ROOT_PATH.'data/logs/'.date('Y-m-d').'.xml';
    if(!is_dir(ROOT_PATH.'data/logs/'))
    {
        @mkdir(ROOT_PATH.'data/logs/');
    }
	@error_log($err, 3, $logfile);
	@chmod($logfile, 0777);
}

/**
 * 利用curl模拟浏览器发送请求
 *
 * @param string $url 请求的URL
 * @param array|string $post post数据
 * @param int $timeout 执行超时时间
 * @param boolean $sendcookie 是否发送当前cookie
 * @return array
 */
function request($url, $post = null, $timeout = 40, $sendcookie = true)
{
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 35);
	curl_setopt($ch, CURLOPT_TIMEOUT, $timeout ? $timeout : 40);
	if ($sendcookie) {
		$cookie = '';
		foreach ($_COOKIE as $key=>$val)
		{
			$cookie .= rawurlencode($key).'='.rawurlencode($val).';';
		}
		curl_setopt($ch, CURLOPT_COOKIE , $cookie);
	}
	if ($post)
	{
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
	}
	
    if (!ini_get('safe_mode') && ini_get('open_basedir') == '')
    {
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1 );
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    
	$ret = curl_exec($ch);
	$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);
	return array('httpcode'=>$httpcode, 'content'=>$ret);
}

/**
 * 获取APP应用目录
 *
 * @param string $app
 * @return string
 */
function app_dir($app)
{
	if($_ENV['extapp']){
		$app_dir = ROOT_PATH.'apps'.DS.$_ENV['extapp'].DS.$app.DS;
	}else{
		$app_dir = ROOT_PATH.'apps'.DS.$app.DS;
	}
	return is_dir($app_dir) ? $app_dir : (ROOT_PATH.'apps-enc'.DS.$app.DS);
}

/**
 * 利用firephp输出调试到header
 *
 * @param mixed $message 要输出的信息
 * @param bool $showtime 是否显示执行时间
 */
function console($message, $showtime = false)
{
	static $fb = null;
    static $lasttime = CMSTOP_START_TIME;

	if ($fb == null)
	{
		import('helper.firephp');
		$fb = FirePHP::getInstance(true);
	}

    $thistime = microtime(true);
    $usedtime = $thistime - $lasttime;
    $label = $showtime ? sprintf("%09.5fs", $usedtime) : null;
    $fb->info($message, $label);
    $lasttime = $thistime;
}

/**
 * 从数组中读取指定键值的值
 *
 * @param array $array 要读取的数组
 * @param $key 要读取的键
 * @param null $default 键不存在时指定默认值
 * @return mixed|null 键存在时返回值，不存在时返回 NULL
 */
function value($array, $key, $default = NULL)
{
    return isset($array[$key]) ? $array[$key] : $default;
}

/**
 * 仅执行第一次匹配替换
 * @param string $search 查找的字符串
 * @param string $replace 执行替换的字符串
 * @param string $subject 原字符串
 * @return string
 */
function str_replace_once($search, $replace, $subject)
{
	$pos = strpos($subject, $search);
	if ($pos === false)
	{
		return $subject;
	}
	return substr_replace($subject, $replace, $pos, strlen($search));
}

/**
 * 移除内容开始的 BOM 信息
 *
 * @param $content 要移除 BOM 头的内容
 * @return string 被移除 BOM 头后的内容
 */
function remove_bom($content)
{
    if ($content && substr($content, 0, 3) == chr(239).chr(187).chr(191))
    {
        $content = substr($content, 3);
    }
    return $content;
}