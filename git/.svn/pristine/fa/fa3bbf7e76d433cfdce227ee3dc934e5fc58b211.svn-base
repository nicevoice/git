<?php
function tag_content_related($options)
{
	if (!is_array($options)) return false;
	extract($options);
	if (!isset($tags) || empty($tags)) return false;
	if (!isset($size)) $size = 10;
	$db = & factory::db();
	$tagids = '';
    $tags = "'".preg_replace("/\s/", "','", $tags)."'";
    $data = $db->select("SELECT `tagid` FROM `#table_tag` WHERE `tag` IN($tags)");
    foreach ($data as $r)
    {
    	$tagids .= $r['tagid'].',';
    }
    $tagids = rtrim($tagids, ',');
	return $db->select("SELECT DISTINCT t.contentid, c.* FROM `#table_content_tag` t LEFT JOIN `#table_content` c ON c.contentid=t.contentid WHERE t.contentid!=$contentid AND c.status=6 AND t.tagid IN($tagids) AND c.modelid!=3 ORDER BY c.published DESC LIMIT $size");
}

function tag_content_prev($options)
{
	if (!is_array($options)) return false;
	extract($options);
	$db = & factory::db();
	if (!isset($catid) || !isset($published))
	{
		$r = $db->get("SELECT `catid`, `published`, `status` FROM `#table_content` WHERE `contentid`=?", array($contentid));
		if (!$r || $r['status'] != 6) return false;
		$catid = $r['catid'];
		$published = $r['published'];
	}
	return $db->get("SELECT * FROM `#table_content` WHERE `catid`=? AND `status`=6 AND `published`<? ORDER BY `published` DESC LIMIT 1", array($catid, $published));
}

function tag_content_next($options)
{
	if (!is_array($options)) return false;
	extract($options);
	$db = & factory::db();
	if (!isset($catid) || !isset($published))
	{
		$r = $db->get("SELECT `catid`, `published`, `status` FROM `#table_content` WHERE `contentid`=?", array($contentid));
		if (!$r || $r['status'] != 6) return false;
		$catid = $r['catid'];
		$published = $r['published'];
	}
	return $db->get("SELECT * FROM `#table_content` WHERE `catid`=? AND `status`=6 AND `published`>? ORDER BY `published` ASC LIMIT 1", array($catid, $published));
}


function tag_content($options)
{
	if (!is_array($options)) return false;
	extract($options);
	if ($fields && $fields != '*')
	{
		$fields	= explode(',', $fields);
		$temp	= array();
		foreach ($fields as $field)
		{
			if (strpos('.', $field) === false)
			{
				$temp[]	= 'c.'.trim($field);
			}
		}
		$fields	= implode(',', $temp);
	}
	else
	{
		$fields	= 'c.*';
	}
	$extrawhere = $where;
	$where = $join = $on = array();
	$where[] = "c.`status`=6";
    $tagids = $proids = array();
	if (isset($contentid))
	{
		$where[] = is_numeric($contentid) ? "c.`contentid`=".intval($contentid) : "c.`contentid` IN($contentid)";
	}
	else 
	{
		if (!empty($catid))
		{
			$category = table('category');
			$catids = array();
			foreach (explode(',', $catid) as $id)
			{
				$id = trim($id);
				if (!$id || !is_numeric($id)) continue;
				if (!($cate = $category[$id]))
				{
					$catids[] = $id;
				}
				$childids = $cate['childids'];
				if ($childids)
				{
					$catids = array_merge($catids, explode(',', $childids));	
				}
				else
				{
					$catids[] = $id;
				}
			}
			$catids = array_unique(array_filter(array_map('trim', $catids)));
			if ($catids)
			{
				$where[] = "c.`catid` IN(".implode_ids($catids).")";
			}
		}
		if (!empty($modelid))
		{
            $where[] = is_numeric($modelid) ? "c.`modelid`=".intval($modelid) : "c.`modelid` IN($modelid)";
		}
		if (!empty($sourceid))
		{
            $where[] = is_numeric($sourceid) ? "c.`sourceid`=".intval($sourceid) : "c.`sourceid` IN($sourceid)";
		}
		if (!empty($createdby))
		{
            $where[] = is_numeric($createdby) ? "c.`createdby`=".intval($createdby) : "c.`createdby` IN($createdby)";
		}
        /**
         * START
         * 增加栏目分类，内容分类和地区分类的参数，分别是typeid，subtypeid和zoneid
         */
        if (!empty($zoneid))
        {
            $where[] = is_numeric($zoneid) ? "c.`zoneid`=".intval($zoneid) : "c.`zoneid` IN($zoneid)";
        }
        if (!empty($typeid))
        {
            $where[] = is_numeric($typeid) ? "c.`typeid`=".intval($typeid) : "c.`typeid` IN($typeid)";
        }
        if (!empty($subtypeid))
        {
            $where[] = is_numeric($subtypeid) ? "c.`subtypeid`=".intval($subtypeid) : "c.`subtypeid` IN($subtypeid)";
        }
        /**
         * 增加栏目分类，内容分类和地区分类的参数，分别是typeid，subtypeid和zoneid
         * END
         */
		if (!empty($keywords))
		{
			if (preg_match("(AND|\+|&|\s)", $keywords) && !preg_match("(OR|\|)", $keywords))
			{
				$andor = ' AND ';
				$keywords = preg_replace("/( AND |&| )/is", "+", $keywords);
			}
			else
			{
				$andor = ' OR ';
				$keywords = preg_replace("/( OR |\|)/is", "+", $keywords);
			}
			$keywords = str_replace('*', '%', addcslashes($keywords, '%_'));
			$srhwords = array();
			foreach(explode('+', $keywords) as $text)
			{
				$text = trim($text);
				if ($text)
				{
					$srhwords[] = "(c.`title` LIKE '%".str_replace('_', '\_', $text)."%')";
				}
			}
			$srhwords = implode($andor, $srhwords);
			if ($srhwords)
			{
				$where[] = "($srhwords)";
			}
		}
		if (!empty($tags))
		{
			$db = & factory::db();
			$tag_sql = "SELECT `tagid` FROM `#table_tag` WHERE";
			if (strpos($tags, ',') === false)
			{
				$tag = $db->get("$tag_sql `tag`='$tags'");
				if ($tag)
				{
			        $join[] = "`#table_content_tag` t";
			        $on[] = "c.`contentid`=t.`contentid`";
					$where[] = "t.`tagid`=".$tag['tagid'];
				}
			}
			else 
			{
				$tags = $db->select("$tag_sql `tag` IN('".str_replace(',', "','", $tags)."')");
				if ($tags)
				{
					$tagid = '';
					foreach ($tags as $tag)
					{
						$tagid .= $tag['tagid'].',';
                        $tagids[] = $tag['tagid'];
					}
					$tagid = trim($tagid, ',');
			        $join[] = "`#table_content_tag` t";
			        $on[] = "c.`contentid`=t.`contentid`";
					$where[] = "t.`tagid` IN($tagid)";
				}
			}
		}
		if (!empty($proid))
		{
			$proids = is_numeric($proid) ? array($proid) : explode(',', $proid);
			foreach ($proids as $kid=>$proid)
			{
				$p = 'p'.$kid;
				$join[] = "`#table_content_property` $p";
				$on[] = "c.`contentid`=$p.`contentid`";
				$where[] = "$p.`proid`=$proid";
			}
		}
		if (!empty($weight))
		{
			if (strpos($weight, ',') === false)
			{
				$where[] = "c.`weight`=".intval($weight);
			}
			elseif(preg_match("/^\s*([\d]*)\s*\,\s*([\d]*)\s*$/", $weight, $m)) 
			{
				if ($m[1]) $where[] = "c.`weight`>=$m[1]";
				if ($m[2]) $where[] = "c.`weight`<=$m[2]";
			}
		}
		if (!empty($published))
		{
			if (strpos($published, ',') === false)
			{
				if (is_numeric($published) && strlen($published) < 4)
				{
					$published = strtotime("-$published day");
					$where[] = where_mintime('c.`published`', $published);
				}
				else 
				{
					$where[] = where_mintime('c.`published`', $published)." AND ".where_maxtime('c.`published`', $published);
				}
			}
			elseif(preg_match("/^\s*([\d]{4}\-[\d]{1,2}\-[\d]{1,2})?\s*\,\s*([\d]{4}\-[\d]{1,2}\-[\d]{1,2})?\s*$/", $published, $m)) 
			{
				if ($m[1]) $where[] = where_mintime('c.`published`', $m[1]);
				if ($m[2]) $where[] = where_maxtime('c.`published`', $m[2]);
			}
		}
        if (!empty($thumb) && intval($thumb) === 1)
        {
            $where[] = "c.`thumb` <> ''";
        }
	}
	if (!empty($extrawhere))
	{
		$where[] = "($extrawhere)";
	}

    // MAKE SQL & DISTINCT
    if(count($tagids) > 1 || count($proids) > 1)
    {
        if(strpos($fields,'contentid'))
        {
            $fields = str_replace('c.contentid','DISTINCT c.contentid', $fields);
        }
    }
    $sql = 'SELECT '.$fields.' FROM `#table_content` c ';

	if ($join) $sql .= " LEFT JOIN(".implode(',', $join).") ON(".implode(' AND ', $on).") ";
	$sql .= ' WHERE '.implode(' AND ', $where);
	
	if (!empty($orderby))
	{
		if (strpos($orderby, ',') !== false) $orderby = str_replace(',', ',c.', $orderby);
		$orderby =  'c.'.$orderby;
	}
	else 
	{
		$orderby = "c.`published` DESC";
	}
	$sql .= " ORDER BY ".$orderby;
	$options['sql'] = $sql;

	return tag_db($options);
}

function tag_shopex($options)
{
	if (!is_array($options))
		return false;
	extract($options);
	if (empty($dsn))	
		return false;
	if (!isset($prefix))
		$prefix = 'sdb_';
	$where = array();
	$on = ' g.goods_id = k.goods_id ';
	
	// 商品关键字
	if (!empty($keywords)) {
		foreach(explode(',',$keywords) as $key)
		{
			$key = trim($key);
			if (empty($key))
				continue;
			$key = str_replace('*', '%', addcslashes($key, '%_'));
			$wkeyword[] = " k.keyword LIKE '".str_replace('_', '\_', $key)."' ";
		}
		$wkeyword = implode(' OR ', $wkeyword);
	}
	if (!empty($wkeyword))
	{
		$where[] = " ($wkeyword) ";
	}
	
	// 商品分类
	if (!empty($catid))
	{
		$where[] = ' g.cat_id IN ('.implode_ids($catid).') ';
	}
	
	// bid 品牌id ，brand 品牌
	$ubrand = array();
	$ids = array();
	foreach(explode(',',$brand) as $b)
	{
		$b = trim($b);
		if (empty($b))
			continue;
		$b = str_replace('*', '%', addcslashes($b, '%_'));
		$ubrand[] = " g.brand LIKE '".str_replace('_', '\_', $b)."' ";
	}
	foreach (explode(',', $bid) as $id)
	{
		$id = intval(trim($id));
		if (empty($id))
			continue;
		$ids[] = $id;
	}
	if (!empty($ids))
	{
		array_unshift($ubrand, ' g.brand_id IN ('.implode_ids($ids).') ');
	}
	$ubrand = implode(' OR ', $ubrand);
	if (!empty($ubrand))
	{
		$where[] = "($ubrand)";
	}
	
	// 搜索时间 published
	if (!empty($published))
	{
		if ($published = abs(intval($published)))
		{
			$published = strtotime("-$published day");
			$where[] = " g.uptime >= $published ";
		}
	}
	
	// 排序类型(orderby):uptime, price, comments_count, view_count, view_w_count, buy_count, buy_w_count
	$order = '';
	if (!empty($orderby))
	{
		if (preg_match('/(\w+)\s+(asc|desc)/i', $orderby, $m))
		{
			$orderAry = array('price', 'comments_count', 'view_count', 'view_w_count', 'buy_count', 'buy_w_count');
			$orderby = in_array($m[1], $orderAry) ? $m[1] : 'uptime';
			$order = ' ORDER BY g.'.$orderby.' '.$m[2];
		}
	}
	$where[] = " g.marketable='true' ";
	
	$where = implode(' AND ', $where);
	$head = " SELECT DISTINCT g.* ";
	$tail = " FROM {$prefix}goods g LEFT JOIN {$prefix}goods_keywords k ON {$on} WHERE $where";
	$sql = $head. $tail. $order;
	
	// 判断是否进行标签过滤
	if (!empty($tagid)) 
	{
		$subwhere = array();
		$head = " SELECT DISTINCT g.goods_id ";
		$subwhere[] = ' g.goods_id = r.rel_id ';
		$subwhere[] = ' r.tag_id IN ('.implode_ids($tagid).') ';
		$subwhere[] = ' g.goods_id IN ('.$head. $tail.') ';
		$subwhere = implode(' AND ', $subwhere);
		$sql = " SELECT DISTINCT g.* FROM {$prefix}tag_rel r , {$prefix}goods g WHERE {$subwhere} ";
		$sql .= $order;
	}
	
	$options['sql'] = $sql;

	return tag_db($options);
}

function tag_phpwind($options)
{
	if (!is_array($options))
		return false;
	extract($options);
	if (empty($dsn))	
		return false;
	if (!isset($prefix))
		$prefix = 'pw_';
	$where = array();
	
	// 主题范围 filter (精华 digest 置顶 top)
	if ($filter == 'digest')
		$where[] = "t.digest>'0'";
	if ($filter == 'top')
	{
		$where[] = "t.topped>'0'";
	}
	else
	{
		$where[] = "t.topped>='0'";
	}
	
	// 论坛版块 fid
	if (!empty($fid))
	{
		$where[] = 't.fid IN ('.implode_ids($fid).')';
	}
	
	$where[] = "s.tid=t.tid ";
	
	// 时间范围 published
	if (!empty($published))
	{
		if ($published = abs(intval($published)))
		{
			$published = strtotime("-$published day");
			$where[] = "t.postdate >= $published";
		}
	}
	
	// 关键字 keywords
	if (!empty($keywords))
	{
		if (preg_match("(AND|\+|&|\s)", $keywords) && !preg_match("(OR|\|)", $keywords))
		{
			$andor = ' AND ';
			$keywords = preg_replace("/( AND |&| )/is", "+", $keywords);
		}
		else
		{
			$andor = ' OR ';
			$keywords = preg_replace("/( OR |\|)/is", "+", $keywords);
		}
		$keywords = str_replace('*', '%', addcslashes($keywords, '%_'));
		$srhwords = array();
		foreach(explode('+', $keywords) as $text)
		{
			$text = trim($text);
			if ($text)
			{
				$srhwords[] = "(s.content LIKE '%".str_replace('_', '\_', $text)."%' OR t.subject LIKE '%$text%')";
			}
		}
		$srhwords = implode($andor, $srhwords);
		if ($srhwords)
		{
			$where[] = "($srhwords)";
		}
	}
	
	// 作者id uid  作者 author
	$uname = array();
	$ids = array();
	foreach(explode(',',$author) as $u)
	{
		$u = trim($u);
		if (empty($u))
			continue;
		$u = str_replace('*', '%', addcslashes($u, '%_'));
		$uname[] = "t.author LIKE '".str_replace('_', '\_', $u)."'";
	}
	foreach (explode(',', $uid) as $id)
	{
		$id = intval(trim($id));
		if (empty($id))
			continue;
		$ids[] = $id;
	}
	if (!empty($ids))
	{
		array_unshift($uname, 't.authorid IN ('.implode_ids($ids).')');
	}
	$uname = implode(' OR ', $uname);
	if (!empty($uname))
	{
		$where[] = "($uname)";
	}
	
	// 特殊主题 special  (投票主题 1 活动主题 2 悬赏主题 3  商品主题 4  辩论主题 5 )
	if (!empty($special))
	{
		$where[] = "t.special IN (".implode_ids($special).")";
	}
	
	$where = implode(' AND ', $where);
	
	// 排序类型 orderby (最后回复 lastpost 发布时间 postdate 回复次数 replies 点击 hits)
	$order = '';
	if (!empty($orderby))
	{
		if (preg_match('/(\w+)\s+(asc|desc)/i', $orderby, $m))
		{
			$orderby = in_array($m[1], array('postdate', 'replies', 'hits')) ? $m[1] : 'lastpost';
			$order = 'ORDER BY t.'.$orderby.' '.$m[2];
		}
	}
	
	$sql = "SELECT * FROM {$prefix}threads t, {$prefix}tmsgs s WHERE $where $order";
	$options['sql'] = $sql;
	return tag_db($options);
}

function tag_discuz($options)
{
	if (!is_array($options)) return false;
	extract($options);
	if (empty($dsn)) return false;
	if (!isset($prefix))
		$prefix = 'cdb_';
	
	// 兼容DiscuzX
	if (!isset($discuzX))
	{
		$tpost = 'posts'; $tthread = 'threads';
	}
	else 
	{
		$prefix = $prefix. 'forum_';
		$tpost = 'post'; $tthread = 'thread';
	}
	
	$where = array();
	// 主题范围(filter):全部主题(all) 精华主题(digest) 置顶主题(top)
	// 精华
	if ($filter == 'digest')
	{
		$where[] = "t.digest>'0'";
	}
	// 置顶否
	if ($filter == 'top')
	{
		$where[] = "t.displayorder>'0'";
	}
	else
	{
		$where[] = "t.displayorder>='0'";
	}
	
	// 论坛版块：fid
	if (!empty($fid))
	{
		$where[] = 't.fid IN ('.implode_ids($fid).')';
	}
	
	$where[] = "p.tid=t.tid AND p.first='1' AND p.invisible='0'";
	
	// 时间范围：dateline
	if (!empty($published))
	{
		if ($published = abs(intval($published)))
		{
			$published = strtotime("-$published day");
			$where[] = "t.dateline >= $published";
		}
	}
	
	// 关键字：
	if (!empty($keywords))
	{
		if (preg_match("(AND|\+|&|\s)", $keywords) && !preg_match("(OR|\|)", $keywords))
		{
			$andor = ' AND ';
			$keywords = preg_replace("/( AND |&| )/is", "+", $keywords);
		}
		else
		{
			$andor = ' OR ';
			$keywords = preg_replace("/( OR |\|)/is", "+", $keywords);
		}
		$keywords = str_replace('*', '%', addcslashes($keywords, '%_'));
		$srhwords = array();
		foreach(explode('+', $keywords) as $text)
		{
			$text = trim($text);
			if ($text)
			{
				$srhwords[] = "(p.message LIKE '%".str_replace('_', '\_', $text)."%' OR p.subject LIKE '%$text%')";
			}
		}
		$srhwords = implode($andor, $srhwords);
		if ($srhwords)
		{
			$where[] = "($srhwords)";
		}
	}
	
	// 作者id：uid 作者：author
	$uname = array();
	$ids = array();
	foreach(explode(',',$author) as $u)
	{
		$u = trim($u);
		if (empty($u))
			continue;
		$u = str_replace('*', '%', addcslashes($u, '%_'));
		$uname[] = "t.author LIKE '".str_replace('_', '\_', $u)."'";
	}
	foreach (explode(',', $uid) as $id) 
	{
		$id = intval(trim($id));
		if (empty($id))
			continue;
		$ids[] = $id;
	}
	if (!empty($ids))
	{
		array_unshift($uname, 't.authorid IN ('.implode_ids($ids).')');
	}
	$uname = implode(' OR ', $uname);
	if (!empty($uname))
	{
		$where[] = "($uname)";
	}	
	
	// 特殊主题(special):投票主题(1) 商品主题(2) 悬赏主题(3)  活动主题(4)  辩论主题(5)
	if (!empty($special))
	{
		$where[] = "t.special IN (".implode_ids($special).")";
	}

	$where = implode(' AND ', $where);
	
	// 排序类型(orderby):lastpost dateline replies views
	$order = '';
	if (!empty($orderby))
	{
		if (preg_match('/(\w+)\s+(asc|desc)/i', $orderby, $m))
		{
			$orderby = in_array($m[1], array('dateline', 'replies', 'views')) ? $m[1] : 'lastpost';
			$order = 'ORDER BY t.'.$orderby.' '.$m[2];
		}
	}
	
	$sql = "SELECT * FROM {$prefix}{$tpost} p, {$prefix}{$tthread} t WHERE $where $order";
	$options['sql'] = $sql;
	return tag_db($options);
}

function tag_db($options)
{
	if (!is_array($options)) return false;
	extract($options);
	if (!isset($sql)) return false;
	if (!empty($dsnid))
	{
		$dsn = table('dsn', $dsnid);
		$db = factory::db($dsn);
	}
	elseif (!empty($dsn))
	{
		$dsns = table('dsn');
		foreach ($dsns as $dsnid=>$d)
		{
			if ($d['name'] == $dsn)
			{
				$dsn = $d;
				break;
			}
		}
		$db = factory::db($dsn);
	}
	else 
	{
		$db = factory::db();
		if ($db->options['prefix'] != 'cmstop_')
		{
			$sql = str_replace('cmstop_', $db->options['prefix'], $sql);
		}
	}
	if (isset($dbname))
	{
		if ($db->select_db($dbname) === false)
		{
			exit("The database $dbname is not exists!");
		}
	}
	$size = isset($size) ? intval($size) : 0;
	$page = isset($page) ? intval($page) : 0;
	$pages = $limit = '';
	if ($page > 0)
	{
		if ($size < 1) {
			$size = 10;
		}
		$offset = $size * ($page-1);
		$limit = $offset > 0 ? " LIMIT $offset,$size" : " LIMIT $size";
		$r = $db->get("SELECT COUNT(*) AS `count` ".stristr($sql, 'from'));
		$total = $r['count'];
	}
	elseif ($size > 0)
	{
		$offset = isset($offset) ? intval($offset) : 0;
		$limit = $offset > 0 ? " LIMIT $offset,$size" : " LIMIT $size";
	}
    if (isset($dsn) && is_array($dsn) && $dsn['driver'] === 'mssql')
    {
        $data = $size == -1 ? $db->get($sql) : $db->limit($sql, $size);
    }
    else
    {
        $sql .= $limit;
        $data = $size == -1 ? $db->get($sql) : $db->select($sql);
    }
	if (isset($dbname) && (empty($dsn) || $dsn == 'db'))
	{
		$db->select_db(config('db', 'dbname'));
	}
	if ($data)
	{
		if (!empty($dsn)) $charset = config('db', 'charset');
		if (isset($charset) && $db->options['charset'] != $charset) $data = str_charset($db->options['charset'], $charset, $data);
		if ($page) $data = array('data'=>$data, 'count'=>count($data), 'total'=>$total, 'size'=>$size, 'page'=>$page);
	}
	return $data;
}

function tag_xml($options)
{
	if (!is_array($options)) return false;
	extract($options);
	if(!isset($url)) return false;
	
	unset($options['url']);
	$url = url_query($url, $options);
	$data = file_get_contents($url);

	$parser = xml_parser_create();
	xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
	xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
	xml_parse_into_struct($parser, $data, $values, $tags);
	xml_parser_free($parser);
	
	if (!isset($node)) $node = 'item';
	
	$tdb = array();
	foreach ($tags as $key=>$val)
	{
		if ($key != $node) continue;

		$molranges = $val;
		for ($i=0; $i < count($molranges); $i+=2) 
		{
			$offset = $molranges[$i] + 1;
			$len = $molranges[$i + 1] - $offset;
			$mvalues = array_slice($values, $offset, $len);
			$mol = array();
			for ($k=0; $k<count($mvalues); $k++) 
			{
				$mol[$mvalues[$k]['tag']] = $mvalues[$k]['value'];
			}
			$tdb[] = $mol;
		}
	}
	return $tdb;
}

function tag_json($options)
{
	if (!is_array($options)) return false;
	extract($options);
	if(!isset($url)) return false;
	
	unset($options['url']);
	$url = url_query($url, $options);
	
	$data = file_get_contents($url);
	$json = & factory::json();
	$data = $json->decode($data);
	return $data;
}

function tag_rpc($options)
{
	if (!is_array($options)) return false;
	extract($options);
	if(!isset($url)) return false;
	
	unset($options['url'], $options['encoding'], $options['method'], $options['return']);
	$url = url_query($url, $options);
	
	import('helper.xmlrpc_client');
	$xmlrpc_client = new xmlrpc_client($url, 'POST', $encoding);
	$data = $xmlrpc_client->request($method, $options);
	return $data;
}
