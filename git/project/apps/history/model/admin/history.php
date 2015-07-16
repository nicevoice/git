<?php
/**
 * 历史页面
 */
class model_admin_history extends model
{
	function __construct()
	{
		parent::__construct();
		import('helper.folder');
		$this->dir = WWW_PATH.'history'.DS;
		
		$this->_table = $this->db->options['prefix'].'history';
		$this->_primary = 'hid';
		$this->_fields = array('hid', 'cronid', 'alias', 'url', 'code', 'userid', 'addtime');
		$this->_readonly = array('hid','cronid');
		$this->_create_autofill = array('addtime'=>TIME,'userid'=>$this->_userid);
		$this->_update_autofill = array('userid'=>$this->_userid);
		$this->_validators = array(
				'name'=>array(
					'not_empty'=>array('任务名称不能为空'),
					'max_length' =>array(40, '任务名称不得超过40字节')
				),
				'alias'=>array(
					'not_empty'=>array('别名不能为空'),
					'max_length' =>array(40, '别名不得超过40字节'),
					'/^[\w]+$/' =>array(40, '别名只能有字母数字和下划线'),
				)
			);
	}
	
	function getById($id)
	{
		$id = intval($id);
		$field = "h.*, m.username, c.name, c.starttime, c.endtime, c.hour, c.disabled";
		$sql = "SELECT $field FROM #table_history h
				LEFT JOIN #table_cron c ON h.cronid = c.cronid
				LEFT JOIN #table_member m ON h.userid = m.userid
				WHERE hid = $id";
		$data = $this->db->get($sql);
		return $this->output($data);
	}
	
	function page($where, $order)
	{
		$where && $where = "WHERE $where";
		$order && $order = "ORDER BY $order";
		$field = "h.*, m.username, c.name, c.starttime, c.endtime, c.hour, c.disabled";
		$sql = "SELECT $field FROM #table_history h
				LEFT JOIN #table_cron c ON h.cronid = c.cronid
				LEFT JOIN #table_member m ON h.userid = m.userid
				$where $order";
		$data = $this->db->select($sql);
		return $this->output($data);
	}
	
	/**
	 * 输出格式转换,$data是一条或多条记录
	 */
	private function output($data)
	{
		if(!$data) return array();
		if(!$data[0]) {
			$wei = 1;
			$data = array($data);
		}
		foreach ($data as & $r)
		{
			$r['starttime'] = $r['starttime'] ? date('Y-m-d', $r['starttime']) : '';
			$r['endtime'] = $r['endtime'] ? date('Y-m-d', $r['endtime']) : '';
			$r['hourArr'] = explode(',', $r['hour']);
			foreach ($r['hourArr'] as $k => $v) if(strlen($v) == 0) unset($r['hourArr'][$k]);
			$r['disabledStr'] = $r['disabled'] ? '失效' : '正常';
		}
		if($wei) $data = $data[0];
		return $data;
	}
	
	function del($id)
	{
		if(is_array($id)) $id = implode(',', $id);
		$rs = $this->db->select("SELECT alias, cronid FROM #table_history WHERE hid IN ($id)");
		$cronid = array();
		foreach ($rs as $item) 
		{
			$cronid[] = $item['cronid'];
			$dir = WWW_PATH.'history/'.$item['alias'];
			folder::delete($dir);
		}
		$cronid = implode(',', $cronid);
		$cron = loader::model('admin/cron', 'system');
		$cron->delete($cronid);
		
		parent::delete($id);
		return true;
	}
	
	/**
	 * 保存或更新任务
	 * @param array $data 一般是post
	 */
	function save($data)
	{
		$data['day'] = array();
		$data['weekday'] = array();
		$data['minute'] = '0';
		$data['mode'] = 3;
		$data['type'] = 'system';
		$data['app'] = 'history';
		$data['controller'] = 'history';
		$data['action'] = 'exec';
		$data['hidden'] = 1;
		$data['starttime'] && $data['starttime'] .= ' 00:00:01';
		$data['endtime'] && $data['endtime'] .= ' 00:00:01';
		
		$cron = loader::model('admin/cron', 'system');
		$hid = intval($data['hid']);
		if($hid) 
		{
			$cronid = intval($data['cronid']);
			if(!$cronid) exit('没有cronid');
			$this->update($data, $hid);
			$data['param'] = 'hid='.$hid;
			$cron->save($data);
		}
		else
		{
			$data['addtime'] = TIME;
			if(!$data['cronid'] = $cron->save($data)) {
				return $cron->error();
			}
			$hid = $this->insert($data);
			$cron->set_field('param', "hid=$hid", $data['cronid']);
		}
		$this->calendar($data['alias']);
		return $hid;
	}
	
	/**
	 * 生成页面
	 *
	 * @param int $hid 任务id
	 * @param int $time 时间戳,通常以当前时间计算,为了短期测试长期抓取效果,才设置这个参数
	 */
	function exec($hid, $time=0)
	{
		$time = $time ? $time : TIME;
		$h = $this->get($hid);
		$dir = WWW_PATH.'history/'.$h['alias'].DS.date('Y-m', $time).DS;
		
		is_dir($dir) || folder::create($dir, 0777);
		$file = $dir.date('d-H', $time).SHTML;
		$path = $this->url2path($h['url']);
		
		if(!$path) return array('state' => false, 'error' => "url不正确或模板不存在");
		
		$html = file_get_contents($h['url']);
		
		
		$html = $this->saveRes($html, $dir);
		$byte = write_file($file, $html);
		
		//更新月历
		$this->calendar($h['alias'], $time);
		return array('state' => true, 'info' => "页面: $file<br/>字节: $byte");
	}
	
	/**
	 * url只能是本网站的
	 * 将直接解析include的片段,失去shtml的include功能(日历被固化)
	 */
	private function url2path($url)
	{
		$url = parse_url(trim($url));
		$path = $url['path'] ? substr($url['path'], 1) : '';
		$path = realpath(WWW_PATH.$path);
		if(!$path) return false;
		
		if(is_file($path)) return $path;
		
		$file = $path.DS.'index'.SHTML;
		
		if(is_file($path.DS.'index.shtml')) return $path.DS.'index.shtml';
		if(is_file($path.DS.'index.html')) return $path.DS.'index.html';
		
		return false;
	}
	/**
	 * exec方法辅助方法: 保存外连的js, css文件,并替换$html中的路径
	 */
	private function saveRes($html, $dir)
	{
		//保存文件
        preg_match_all('#(http[^>]+\.css)(\W)#i', $html, $temp);
        $css = $temp[1];
        preg_match_all('#(http[^>]+\.js)(\W)#i', $html, $temp);
		$js = $temp[1];
		$files = array_merge($css, $js);
		$dir .= 'res/';
		if(!is_dir($dir)) folder::create($dir);
		
		foreach ($files as $f)
		{
			$f = str_replace(IMG_URL, IMG_PATH, $f);	//如果是本地的替换为硬盘路径
			
			$newF = $dir.basename($f);
			if(is_file($newF)) continue;
			$code = file_get_contents($f);
			if(strlen($code) > 10) 
			{
				if(strtolower(fileext($f)) == 'css') 
				{
					$code = $this->replaceImg($code, dirname($f));
				}
				write_file($newF, $code);
			}
		}
		
		//替换路径为链接
		$dirUrl = str_replace(WWW_PATH, WWW_URL, $dir);
		$dirUrl = str_replace("\\", '/', $dirUrl);
		$html = preg_replace('#http[^>]+/([^/]+\.js)(\W)#', $dirUrl."$1$2", $html);
		$html = preg_replace('#http[^>]+/([^/]+\.css)(\W)#', $dirUrl."$1$2", $html);
		return $html;
	}
	
	/**
	 * 替换css代码中的图片地址为绝对地址
	 */
	private function replaceImg($css, $cssDir)
	{
		$cssDir = str_replace(IMG_PATH, IMG_URL, $cssDir).'/';
		$css = preg_replace('#url\((.*?\/)(.*?)\)#', "url($cssDir$1"."$2)", $css);
		return $css;
	}
	
	/**
	 * 生成某个历史任务的当月日历片段
	 *
	 * @param string $alias	 历史页面任务的别名
	 * @param int $time		时间戳,通常为当前时间
	 */
	private function calendar($alias='index', $time = 0)
	{
		$time = $time ? $time : TIME;
		//参数的运算整理
		$year = date('Y', $time);
		$month = date('m', $time);
		$month < 10 && $month = '0'.intval($month);
		$prev = date('w', strtotime("$year-$month-01"));	//1号是星期几, 也是前导空td的个数
		$lastDay = date('t', $time);  //本月天数
		
		//日期数组建立
		$days = array();
		for($i=0; $i < $prev; $i++) $days[] = '';
		for($i=1; $i <= $lastDay; $i++) $days[] = $i < 10 ? '0'.intval($i) : $i;
		$next = 7 - count($days) % 7;	//后导空td个数
		if($next == 7) $next = 0;
		for($i=0; $i < $next; $i++) $days[] = '';
		$days = $this->dayUrl($alias, $year, $month, $days);	//创建日期链接
		
		//上一月,下一月链接计算
		$py = $ny = $year;
		$month <= 1 && $py = $year - 1;
		$month >= 12 && $ny = $year + 1;
		$prevM = $month > 1 ? $month - 1 : 12;
		$nextM = $month < 12 ? $month + 1 : 1;
		$prevM < 10 && $prevM = '0'.intval($prevM);
		$nextM < 10 && $nextM = '0'.intval($nextM);

		//赋值
		$tpl = & factory::template('history');
		$tpl->assign('days', $days);
		$tpl->assign('year', $year);
		$tpl->assign('month', $month);
		$tpl->assign('alias', $alias);
		$prev	= "section/history/$alias/";
		$tpl->assign('preYear', $this->judgeUrl($prev.($year-1)."-$month.html"));
		$tpl->assign('nextYear', $this->judgeUrl($prev.($year+1)."-$month.html"));
		$tpl->assign('preMonth', $this->judgeUrl($prev."$py-$prevM.html"));
		$tpl->assign('nextMonth', $this->judgeUrl($prev."$ny-$nextM.html"));
		
		//生成片段
		$html = $tpl->fetch('history/calendar.html');
		$dir = WWW_PATH."section/history/$alias/";
		if(!is_dir($dir)) folder::create($dir);
		write_file($dir."$year-$month.html", $html);	//每月片段
		if($year == date('Y') && $month == date('m')) 
		{
			$html = str_replace('class="calendarMain"', 'class="calendarMain" style="display: none;"', $html);
			write_file($dir.'calendar.html', $html);		//默认片段是当前月,隐藏(其它月直接显示)
		}
	}
	/**
	 * calendar方法的辅助方法: 为日历加入链接(判断文件是否存在,每日第一个时段为默认链接)
	 */
	private function dayUrl($alias, $year, $month, $days)
	{
		$dir = WWW_PATH."history/$alias/";
		foreach ($days as & $day)
		{
			if($day) 
			{
				$files = glob($dir."$year-$month/$day-*.*");	//读本日第一个时段
				if($url = str_replace(WWW_PATH, WWW_URL, $files[0])) 
				{
					$day = '<a target="_self" href="'.$url.'">'.$day.'</a>';
				}
			}
		}
		return $days;
	}

	private function judgeUrl($url)
	{
		return WWW_URL.$url;
	}
}