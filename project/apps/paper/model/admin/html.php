<?php
class model_admin_html
{
	private $paper = array();
	private $edition = array();
	public $success = 0;		//生成报纸文章数
	public $mod = 'html';
    public $setting, $html_root, $www_root;
    private $uri;

	function __construct()
	{
		if(strpos($_SERVER['HTTP_HOST'], '.lc'))
		{
			ini_set('display_errors', 1);
		}
		$this->db = & factory::db();
		$this->tpl = & factory::template('paper');
		import('helper.folder');
        $this->setting = setting::get('paper');
        $this->uri = loader::lib('uri','system');
        $u = $this->uri->psn($this->setting['path']);
        $this->html_root = $u['path'];
        $this->www_root  = $u['url'];
	}
	/*******************************************　发布内容页　*********************************************/
	//发布一期
	public function edition($eid)
	{
		$this->getEdition($eid);	//期信息
		$this->updateState($eid);	//状态修改
		$this->date();				//日期字符串
		$this->paper($this->edition['paperid']);	//报纸信息
		$this->edtion_section($this->edition['paperid']);	//往期回顾section
		$this->paper_select(1);		//报纸列表section
		
		$sql = "SELECT * FROM #table_paper_content WHERE editionid = $eid";
		$contents = $this->db->select($sql);
		foreach ($contents AS $item)
		{
			$this->content($item['contentid'], $item['pageid']);
		}
		$this->updateUrl($eid);
		$this->paperIndex();
	}
	
	//生成文章，或者预览
	public function content($cid, $pid, $mod = 'html')
	{
		$r = $this->getContent($cid, $pid);
		if(!$r) return;	//文章己删除
		
		$this->tpl->assign($r);				//本篇文章
		$this->tpl->assign('mod', $mod);
		$this->mod = $mod;
		$this->getPage($pid);				//本版信息
		
		if(!$this->paper)
		{
			$this->getEdition($this->page['editionid']);	//期信息
			$this->date();
			$this->paper($this->edition['paperid']);
		}
		
		$comment	= setting::get('comment');
		$this->tpl->assign('isseccode', $comment['isseccode']);
		$this->tpl->assign('total', 	$r['comments']);
		
		$this->prevNext();				//计算上一版,下一版
		
		$title = $r['title'].'-'.$this->paper['name'].'-第'.$this->edition['total_number'].'期-'.$this->page['name'].'版';
		$this->mod != 'html' && $title .= '-预览模式';
		
		
		$this->tpl->assign('head', array('title' => $title));
		
		$html = $this->tpl->fetch($this->paper['template_content']);
		if($mod != 'html') exit($html);	//预览模式
		
		
		//生成页面
		$file = $this->createPath($this->paper['alias'], $this->edition['editionid'], $pid, $cid);
		folder::create(dirname($file));
		write_file($file, $html);
		$this->success++;
	}
	
	//期信息赋值,需保证在代码最前面
	private function getEdition($eid)
	{
		if($this->edition) return ;
		$this->edition = table('paper_edition', $eid);
		$this->tpl->assign('edition', $this->edition);
	}
	
	//期信息赋值,需保证在代码最前面
	private function paper($paperid)
	{
		if($this->paper) return ;
		$this->paper = table('paper', $paperid);
		$this->tpl->assign('paper', $this->paper);
	}
	//更改为发布状态
	private function updateState($eid)
	{
		if($this->edition['disabled'] == 1 && $this->edition['editionid'] == $eid) return;
		$sql = "UPDATE #table_paper_edition SET disabled = 1 WHERE editionid = $eid";
		$this->db->exec($sql);
		$this->edition['disabled'] = 1;
	}
	
	//日期字符串
	private function date()
	{
		if($this->data) return;
		$w = date('w', $this->edition['created']);
		$weekMap = array('日', '一', '二', '三', '四', '五', '六');
		$this->date = date('Y年m月d日', $this->edition['created']) .'　星期'. $weekMap[$w];
		$this->tpl->assign('date', $this->date);
	}
	
	
	//取本版信息,缓存
	private function getPage($pid)
	{
		if($pid == $this->page['pageid']) return;
		$this->page = table('paper_edition_page', $pid);
		$this->tpl->assign('page', $this->page);
	}
	
	//本篇文章
	private function getContent($cid, $pid)
	{
		$sql = "SELECT * FROM #table_paper_content pc
				LEFT JOIN #table_content c ON pc.contentid = c.contentid
				LEFT JOIN #table_article a ON a.contentid = c.contentid
				WHERE pc.pageid = $pid AND c.contentid = $cid AND modelid = 1";
		return $this->db->get($sql);
	}
	
	//上一期，下一期,上一版，下一版的处理
	private function prevNext()
	{
		foreach ($this->pages as $v) 
		{
			if($v['pageid'] == $this->page['pageid'])
			{
				$after = 1;
				continue;
			}
			if($after)
			{
				if($v['url'] && $v['url'] != 'javascript:;')
				{
					$next = $v['url'];
					break;
				}
			}
			else
			{
				if($v['url'] && $v['url'] != 'javascript:;')
				{
					$prev = $v['url'];
				}
			}
		}
		!$after && $prev = '';
		
		$this->tpl->assign('prevP', $prev ? $prev : 'javascript:;');
		$this->tpl->assign('nextP', $next ? $next : 'javascript:;');
	}
	
	//创建url
	private function createUrl($alias, $editionid, $pageid, $contentid)
	{
		if($this->mod == 'html')
		{
			$url = $this->www_root."/$alias/$editionid/$pageid/$contentid".SHTML;
		}
		else		
		{
			$url = "?app=paper&controller=content&action=prevView&cid=$contentid&pageid=$pageid";//预览模式
		}
		return $url;
	}
	
	//创建path
	private function createPath($alias, $editionid, $pageid, $contentid)
	{
		$file = $this->html_root."/$alias/$editionid/$pageid/$contentid".SHTML;
		return $file;
	}
	
	/********************************发布报纸列表页******************************************/
	public function paperIndex()
	{
		$this->tpl->assign('head', array('title' => '报纸 - '.setting('system', 'seotitle')));
		$html = $this->tpl->fetch('paper/index.html');
		if(!is_dir($this->html_root))
		{
			folder::create($this->html_root);
		}
		write_file($this->html_root.'/index'.SHTML, $html);
		return true;
	}
	

	/**
	 * 发布期的时候更新版面、期、报纸三级的默认链接
	 *
	 * @param int $pid 报纸id
	 */
	public function updateUrl($eid)
	{
		$pt = '#table_paper_edition_page';
		$pe = '#table_paper_edition';
		$sql = "UPDATE $pt SET url = 'javascript:;' WHERE editionid = $eid";
		$this->db->exec($sql);

        $alias = $eurl = '';
		//1.版面链接
		$sql = "SELECT paperid, editionid, pageid, contentid FROM #table_paper_content 
				WHERE editionid = $eid ORDER BY pageno, `sort`";
		$maps = $this->db->select($sql);
		$newMaps = array();
		foreach ($maps AS $v)
		{
			if(!$alias) 
			{
				$pid = $v['paperid'];
				$alias = table('paper', $pid, 'alias', 1);
			}
			$newMaps[$v['pageid']][] = $v;
		}
		foreach ($newMaps AS $pageid => $item)
		{
			foreach ($item AS $v)
			{
				$path = $this->createPath($alias, $eid, $v['pageid'], $v['contentid']);
				$path = substr($path,strlen($this->html_root));
                $url = $this->www_root.$path;
				$sql = "UPDATE $pt SET url = '$url' WHERE pageid = $pageid";
				$this->db->exec($sql);
				if(!$eurl) $eurl = $url;
				break;
			}
		}
		
		//期链接
		$this->db->exec("UPDATE $pe SET url = '$eurl' WHERE editionid = $eid");
		
		//报纸链接
		$sql = "SELECT * FROM $pe WHERE paperid = $pid AND url != 'javascript:;' ORDER BY total_number DESC";
		$data = $this->db->select($sql);
		foreach ($data AS $v)
		{
			if(is_file(str_replace($this->www_root,$this->html_root,$v['url'])))
			{
				$sql = "UPDATE #table_paper SET url = '{$v['url']}' WHERE paperid = $pid";
				$this->db->exec($sql);
				break;
			}
		}
	}
	
	//休眠，即删除本期静态页面
	public function delEdition($eid)
	{
		$edition = loader::model('admin/edition','paper');
		$paper = loader::model('admin/paper','paper');
		$e = $edition->get($eid);
		$p = $paper->get($e['paperid']);
		$edition->set_field('disabled', 2, $eid);
		$dir = $this->html_root."/{$p['alias']}/$eid/";
		folder::delete($dir);
		$this->updateUrl($eid);
		$this->paperIndex();
		return true;
	}
	
	/**
	 * 前台内容页的往期列表片段
	 * @param int $paperid 报纸id
	 * @param bool $force 是否覆盖
	 */
	public function edtion_section($paperid, $force = 0)
	{
		$alias = table('paper', $paperid, 'alias');
		$file = WWW_PATH.'section/paper/'.$alias.'_select.html';
		//if(is_file($file) && !$force) return true;
		
		$data = $this->db->select("SELECT editionid, url, total_number FROM #table_paper_edition WHERE paperid = $paperid ORDER BY total_number");
		$select = '<div id="eBack">'."\n";
		foreach ($data AS $v)
		{
			if($v['url'] == 'javascript:;' || !$v['url'])
			{
				//$select .= "\t<span>".$v['total_number']."</span>\n";
			}
			else
			{
				$select .= "\t".'<a href="'.$v['url'].'">'.$v['total_number'].'</a>'."\n";
			}
		}
		$select .= '</div>'."\n";
		
		if(!is_dir(dirname($file))) 
		{
			folder::create(dirname($file));
		}
		write_file($file, $select);
	}
	
	/**
	 * 前台内容页的报纸列表section
	 * @param bool $force 是否覆盖
	 */
	public function paper_select($force = 0)
	{
		$file = WWW_PATH.'section/paper/paper_select.html';
		if(is_file($file) && !$force) return true;
		
		$papers = $this->db->select("SELECT paperid, name, url FROM #table_paper");
		
		$select = '<select id="paper-select" class="paper-select" onchange="location.href=$(this).find(\'option:selected\').attr(\'url\');">'."\n";
		foreach ($papers AS $p)
		{
			$select .= "\t".'<option url="'.$p['url'].'" value="'.$p['paperid'].'">'.$p['name'].'</option>'."\n";
		}
		$select .= '</select>'."\n";
		
		if(!is_dir(dirname($file))) folder::create(dirname($file));
		write_file($file, $select);
	}
}

function coords($coords)
{
	$coords = explode(',', $coords);
	$w = $coords[2] - $coords[0] - 6;
	$h = $coords[3] - $coords[1] - 6;
	return "left:{$coords[0]}px;top:{$coords[1]}px;width:{$w}px;height:{$h}px;";
}