<?php

class controller_category extends mobile_controller_abstract
{
	private $mobile;
	
	function __construct(& $app)
	{
		parent::__construct($app);
		$this->mobile = loader::model('mobile');
		if (!$this->setting['open']) $this->showmessage($this->setting['webname'].'的mobile服务已关闭', 'index.php');
	}
	
	function index()
	{
		exit;
	}

	function ls()
	{
		$catid = isset($_GET['catid']) ? intval($_GET['catid']) : 0;
		$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
		$_key = 'mobile_category_ls_'.$catid.'_'.$page;
		if(!$data = $this->cache->get($_key))
		{
            $catids = $this->mobile->filter_catids($this->setting['catids']);
            $catids = array_keys($catids);
			$q = array(
				'weight'=>$this->setting['weight'],
				'catid'=>implode(',',$catids)
			);
			if($catid)
			{
				$catid = $this->mobile->get_catids($catid);
				$q['catid'] = implode(',',$catid);
			}
			$order = 'published';
			$orderby = 'DESC';
			$data = $this->mobile->ls($q,$order,$orderby,$page);
			// 放入缓存
			if($this->setting['cache']) $this->cache->set($_key,$data,$this->setting['cache']);
		}
		echo json_encode(array($data));
	}
}