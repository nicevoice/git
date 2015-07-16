<?php
class controller_index extends mood_controller_abstract
{
	private $data;

	function __construct(&$app)
	{
		parent::__construct($app);
		$this->data = loader::model('data');
	}
	
	function index()
	{
		$catid = intval($_GET['catid']);
		if ($this->system['pagecached'])
		{
			$keyid = md5('pagecached_mood_index_index_' .$catid);
			cmstop::cache_start($this->system['pagecachettl'], $keyid);
		}
		
		$mood = loader::model('admin/mood', 'mood');
		$info = $mood->by_sort();
		$this->template->assign('infos', $info);
		if($catid)
		{
			$this->template->assign('name', table('category', $catid, 'name'));
			$this->template->assign('alias', table('category', $catid, 'alias'));
			$this->template->assign('childids', table('category', $catid, 'childids'));
			$this->template->display('mood/list.html');
		}
		else 
		{
			$this->template->display('mood/index.html');
		}
		
		if ($this->system['pagecached']) cmstop::cache_end();
	}

	function vote()
	{
		$contentid = intval($_GET['contentid']);
		$voteid = intval($_GET['voteid']);
		if(empty($voteid))
		{
			$r = $this->data->get($contentid);
		}
		else
		{
			$r = $this->data->add(array('contentid' => $contentid ,'voteid' => $voteid));
		}
		
		$arr = table('mood');
		if (is_array($arr))
		{
			foreach ($arr as $k=>$v)
			{
				$field = 'm'.$v['moodid'];
				$num = $r[$field];
				$infos[$field]['height'] = max(ceil($num/$r['total'] * 100),1);
				$infos[$field]['number'] = intval($num);
			}
		}
		
		$data = array('data' => $infos, 'total' => $r['total']);
		$data = $this->json->encode($data);
		exit($_GET['jsoncallback']."($data);");
	}
}