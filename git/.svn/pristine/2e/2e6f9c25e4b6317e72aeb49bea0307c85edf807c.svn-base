<?php
class controller_index extends rss_controller_abstract
{
	private $_rss, $_category, $_catid;

	function __construct(&$app)
	{
		parent::__construct($app);
		$this->_rss = loader::model('content_rss');
		$this->_category = $this->_rss->category;
		$this->_catid = $this->setting['category'];
	}

	function index()
	{
		$catid = intval($_GET['catid']);
		if ($this->system['pagecached'])
		{
			$keyid = md5('pagecached_rss_index_index_' .$catid);
			cmstop::cache_start($this->system['pagecachettl'], $keyid);
		}
		
		if (!isset($this->_category[$catid]))
		{
			$catids = array_intersect(array_keys($this->_category), $this->_catid);
			if (!($catid = reset($catids)))
			{
				$this->showmessage('没有订阅');
			}
			$cat = $this->_category[$catid];
		}
		else
		{
			$cat = $this->_category[$catid];
			if (!$this->_has($cat))
			{
				$this->showmessage('无此栏目订阅', '?app=rss');
			}
		}
		$path = $cat['parentids'];
		$path = $path === null ? array() : array_filter(explode(',',$path));
		$path[] = $catid;
		$tree = $this->_treeview($path);
		$sons = $this->_sons($catid);
		if (empty($sons))
		{
			$sons[0] = $cat;
			$sons[0]['rss'] = $this->_rss->ls($catid);
		}
		import('helper.date');
		$date = new date();
		$this->template->assign('multi', count($sons) > 1);
		$this->template->assign('date',$date);
		$this->template->assign('tree',$tree);
		$this->template->assign('rsslist',$sons);
		$this->template->assign('alias',table('category', $catid, 'alias'));
		$this->template->display('rss/index.html');
		
		if ($this->system['pagecached']) cmstop::cache_end();
	}
	
	function feed()
    {
		$catid = intval($_GET['catid']);
		$title = 'RSS';
		$param = null;
		if (isset($this->_category[$catid])) {
			$cat = $this->_category[$catid];
			$title = $cat['name'];
			$param['catid'] = $catid;
		}
		$rssurl = str_replace('&','&amp;', url('rss/index/feed',$param, true));
		$this->template->assign('title',$title);
		$this->template->assign('rssurl',$rssurl);
		$this->template->assign('sitename',setting('system','sitename'));
		$this->template->assign('list',$this->_asmRss($catid));
		$feet_content_type = 'text/xml';
		$charset = config::get('config', 'charset', 'utf-8');
		header('Content-Type: ' . $feet_content_type . '; charset=' . $charset, true);
		echo '<?xml version="1.0" encoding="'.$charset.'"?>'."\n";
		echo $this->template->fetch('rss/rss.xml');
	}

	private function _asmrss($catid)
    {
    	$size = intval($this->setting['size']);
    	if ($size < 10) {
    		$size = 10;
    	}
		$list = $this->_rss->ls_rss($catid, $size, $this->setting['weight']);

		// 获得 文章所有编号
		$contentids = array();
		foreach ($list as $item) 
		{
			$contentids[] = $item['contentid'];
		}
		if (empty($contentids)) return array();

		// 组装成 contentid=>description
		$article = loader::model('admin/article', 'article');
		$_describes = $article->select($contentids, '`contentid`,`description`,`content`');
		$digest = ($this->setting['output'] == 'digest');
		$describes = array_combine($contentids,array_fill(0,count($contentids),''));
		if ($digest)
		{
			foreach ($_describes as $item)
			{
				$description = trim(strip_tags($item['description'],'<p><br><a>'));
				if (!$description) {
					$description = str_cutword(trim(strip_tags($item['content'],'<p><br><a><img>')),200);
				}
				$describes[$item['contentid']] = $description;
			}
		}
		else
		{
			foreach ($_describes as $item)
			{
				$describes[$item['contentid']] = strip_tags($item['content'],'<p><br><a><img>');
			}
		}

		$data = array();
		foreach ($list as $item) {
			$line = array();
			$line['title'] = $item['title'];
			$line['url'] = $item['url'];
			$line['category'] = $this->_category[$item['catid']]['name'];
			$line['description'] = $describes[$item['contentid']];
			$line['published'] = gmdate('D,d M Y H:i:s', $item['published']).' GMT+8';
			$data[] = $line;
		}
		return $data;
	}

	private function _sons($catid)
    {
		$sons = array();
		foreach ($this->_category as $cid => $cat) {
			if ($cat['parentid'] == $catid && $this->_has($cat)) {
				$cat['rss'] = $this->_rss->ls($cid, 10);
				$sons[] = $cat;
			}
		}
		return $sons;
	}
	private function _has($cat)
	{
		$catids = explode(',',$cat['childids']);
		$catids[] = $cat['catid'];
		return count(array_intersect($catids, $this->_catid))>0;
	}

	private function _sons2($catid)
    {
		$sons = array();
		foreach ($this->_category as $cid => $cat) {
			if ($cat['parentid'] == $catid && $this->_has($cat)) {
				$sons[] = $cat;
			}
		}
		return $sons;
	}

	private function _treeview($path, $pid = null, $ulid = 'tree')
    {
		if ($ulid) {
			$html = '<ul id="tree">';
		} else {
			$html = '<ul>';
		}
		$category = $this->_sons2($pid);
		$focus = array_shift($path);
		foreach ($category as $cat) {
			if ($cat['childids']) {
				$html .= $this->_branchFold($cat, $path, $focus);
			} else {
				$html .= $this->_branchFile($cat, $focus);
			}
		}
		$html .= '</ul>';
		return $html;
	}

	private function _branchFold($cat, $path, $focus)
    {
		$focused = '';
		if ($focus == $cat['catid']) {
			$class = 'open';
			if (empty($path)) {
				$focused = ' focused';
			}
		} else {
			$class = 'closed';
		}
		$html = '<li class="'.$class.'">';
		$html .= '<span class="folder'.$focused.'"><a onclick="rss_go('.$cat['catid'].')">'.$cat['name'].'</a>';
		$html .= '<img height="16" width="16" title="订阅该栏目" alt="订阅该栏目" onclick="feed_rss('.$cat['catid'].')" src="'.IMG_URL.'images/rss.gif"/></span>';
		$html .= $this->_treeview($path, $cat['catid'], null);
		$html .= '</li>';
		return $html;
	}
	private function _branchFile($cat, $focus)
    {
		$html = '<li>';
		$focused = $focus == $cat['catid'] ? ' focused' : '';
		$html .= '<span class="file'.$focused.'"><a onclick="rss_go('.$cat['catid'].')">'.$cat['name'].'</a>';
		$html .= '<img height="16" width="16" title="订阅该栏目" alt="订阅该栏目" onclick="feed_rss('.$cat['catid'].')" src="'.IMG_URL.'images/rss.gif"/></span>';
		$html .= '</li>';
		return $html;
	}
}