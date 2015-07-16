<?php

class model_admin_smarter
{
	
	static function getInfo($url='')
	{
		if (strtolower(substr($url,0,4)) != 'http')
		{
			$result = array('state'=>FALSE, 'error'=>'采集URL为空');
			return $result;
		}
		$engine = loader::lib('smarter', 'spider');
		if(!$info = $engine->getInfo($url))
		{
			$result = array('state'=>FALSE, 'error'=>$engine->msg);
		}
		else
		{
			foreach($info as &$row)
			{
				self::clearHtml($row['content']);
			}
			$result = array('state'=>TRUE, 'data'=>$info);
		}
		$engine = NULL;
		return $result;
	}

	static function quickSpider($url=array(), $catid=0)
	{
		if (empty($url)) {
			$result = array('state'=>FALSE, 'error'=>'采集URL为空');
			return $result;
		}
	
		if (!$catid = intval($catid)) {
			$result = array('state'=>FALSE, 'error'=>'入库分类为空');
			return $result;
		}

		if (!is_array($url)) {
			$url = array($url);
		}

		$successed = $failed = 0;
		$article = loader::model('admin/article', 'article');
		$spider_rules = loader::model('admin/spider_rules', 'spider');
		for($i=0;$i<count($url);$i++)
		{
			$u = $url[$i];
			// 先判断采集库是否已经采集过

			// 先从规则库中匹配URL，如果有规则拿出规则采集
			$siteUrl = substr($u, 0, strpos($u, '/', 8));
			//--查找URL匹配
			$rule = $charset = '';
			$SQL = "enter_rule like '" .$siteUrl ."%'";
			$SQL = @mysql_escape_string($SQL);
			$items = $spider_rules->select($SQL);
			if ($items) {
				// 可能找到多个匹配规则，再用采集规则中的urlPattern进行二次匹配
				foreach($items as $item)
				{
					$list_rule = unserialize($item['list_rule']);
					$pattern = preg_quote($list_rule['urlPattern'], '#');
					$pattern = '#^'.str_replace('\(\*\)','.*?', $pattern).'$#is';
					if (preg_match($pattern, $u)) {
						$rule = unserialize($item['content_rule']);
						$charset = $item['charset'];
						break;
					}
				}
			}
			$info = array();
			if($rule)
			{
				// 匹配到采集规则，使用规则采集
				$engine = loader::lib('spider', 'spider');
				$info = $engine->getDetails($u, $rule, $charset);
				if(!$info['title'] || !$info['content'])
				{
					$failed++;
					continue;
				}
			}
			if(empty($info['title']))
			{
				// 匹配规则失败，启用智能采集
				$engine = loader::lib('smarter', 'spider');
				if(!$info = $engine->getInfo($u))
				{
					$failed++;
					continue;
				}	
			}
			if(isset($info[0]['title']))
			{
				$title = $content = '';
				foreach ($info as $k=>$v) 
				{
					self::clearHtml($v['content']);
					
					if ($k == 0)
					{
						$title = $v['title'];
					}
					if (count($info) > 1) 
					{
						$content = $content .'<p class="mcePageBreak">&nbsp;</p>' .$v['content'];
					}
					else
					{
						$content = $v['content'];
					}
				}
				unset($info);
				$info = array('title'=>$title,'content'=>$content);
				unset($title,$content);
			}

            $user = online();
            if ($user && $user['groupid'] == 1)
            {
                $weight_max = intval(table('admin_weight', $user['userid'], 'weight'));
            }
            if (! ($weight = setting('article', 'weight')) && ! ($weight = setting('system', 'defaultwt')))
            {
                $weight = 60;
            }
            $weight = min($weight, isset($weight_max) && $weight_max ? $weight_max : 100);

			$data = array(
				'catid'=>$catid,
				'modelid'=>1,
				'title'=>str_cutword($info['title'], 80, 'utf-8', ''),
				'content'=>$info['content'],
				'status'=>6,
				'weight'=>$weight,
				'author'=>'',
				'editor'=>'',
				'source'=>'',
				'link'=> $u,
				'thumb'=>'',
				'published'=>TIME,
				'saveremoteimage'=>1,
				'allowcomment'=>1
			);
			if ($contentid = $article->add($data))
			{
				$successed++;
			}
			else
			{
				$failed++;
				continue;
			}
		}
		if($successed + $failed == 1)
		{
			if($successed) {
				$result = array('state'=>TRUE, 'info'=>'采集成功');
			}
			else {
				$result = array('state'=>FALSE, 'error'=>'采集失败');
			}
		}
		else {
			$result = array('state'=>TRUE, 'info'=>'共采集' .($successed + $failed) .'个，成功' .$successed .'个，失败' .$failed .'个。');
		}
		$engine = $article = $spider_rules = NULL;
		return $result;
	}

	static function clearHtml(&$html)
	{
		$html = strip_tags($html,'<p><br><strong><b><img><table><tr><th><td>');
		$html = preg_replace('/<(p|br|strong|b|table|tr|th|td) [^>]+>/i','<$1>', $html);
	}

}