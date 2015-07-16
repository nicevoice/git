<?php

class controller_search extends dms_controller_abstract
{
	private $pagesize = 10;	// 分页
	private $search_app;	// 当前搜索模型
	private $search;		// 搜索模型实例
	private $dms_model;		// DMS的内置模型实例
	private $dms_modules;	// DMS模型列表
	private $dateField;		// 时间排序和范围搜索字段

	function __construct(&$app)
	{
		parent::__construct($app);
		$this->search_app = 'picture_group';
		$this->dms_model = loader::model('dms_model');
		$this->dms_modules = $this->dms_model->get_modules();
		$this->dateField = "createtime";
	}
	
	function index()
	{
		$this->template->display('dms/search.html');
	}
	
	function search()
	{
		// 如果没有输入关键字，跳回首页
		$wd = htmlspecialchars(trim($_GET['wd']));
		if(empty($wd))
		{
			$this->redirect(url('dms/search/index'));
		}
		
		//排序
		// 如果没有指定默认的排序，以系统设置的排序规则
		if(!isset($_GET['order'])) $_GET['order'] = $this->setting['order'];
		$order = in_array($_GET['order'],array('rel','time'))?$_GET['order']:'rel';
		// 权重排序为 REL，时间排序字段为 createtime
		$orderby = array('rel' =>'REL','time'=>$this->dateField);
		
		// 页码和分页设置
		$page = empty($_GET['page'])?1:max(1,intval($_GET['page']));
		$pagesize = intval($_GET['pagesize']);
		if(empty($pagesize)) $pagesize = $this->pagesize;
		
		// 设置查询条件
		$time = array();
		//设置时间参数(这个时间设置是为了便捷时间搜索：24小时内，一周内，一月内，一年内)
		$time['day'] = mktime(date('H'), 0, 0, date('m'), date('d') - 1, date('Y'));
		$time['week']  = mktime(date('H'), 0, 0, date('m'), date('d') - 7, date('Y'));
		$time['month']  = mktime(date('H'), 0, 0, date('m') - 1, date('d'), date('Y'));
		$time['year']  = mktime(date('H'), 0, 0, date('m'), date('d'), date('Y') - 1);
		$q = array();
		$q['wd'] = $wd;
		$q['dateField'] = $this->dateField;	//设置时间范围字段
		if(isset($time[$_GET['m']]))
		{
			$q['before'] = $time[$_GET['m']];
			$q['after']  = time();
		}
		// 设置查询字段
		if(isset($_GET['field']))
		{
			if (!empty($_GET['field']))
			{
				$q['field'] = $_GET['field'];
			}
		}
		
		// 载入应用搜索模型
		$this->search = loader::model('dms_search' .$this->search_app);
		// 如果加载失败，输出错误
		if(!$this->search->status) $this->showmessage($this->search->error());
			
		// 设置主索引
		$this->search->mainIndex = $this->dms_modules[$this->search_app]['mainindex'];
		// 如果存在增量索引则联合增量索引
		$this->search->searchIndex = trim($this->search->mainIndex . " " .$this->dms_modules[$this->search_app]['deltaindex']);

		$data = $this->search->page($q, 'EXT', $orderby[$order], 'DESC', $page, $pagesize);
		$total = $this->search->getTotal();
		
		$requestUrl = request::get_url();
		$requestUrl = preg_replace('/(?:&order=[^&]*)+/','',$requestUrl);
		$requestUrl = preg_replace('/(?:&page=[0-9]*)+/','',$requestUrl);
		$url = array (
			'this' => $requestUrl.'&order=' .$_GET['order'] .'&m=' .$_GET['m'],
			'rel'  => $requestUrl.'&order=rel',
			'time' => $requestUrl.'&order=time'
		);
		
		$pageTotal = ($total > $this->search->maxLimit) ? $this->search->maxLimit : $total;
		$multipage = pages($pageTotal, $page, $pagesize, 3, $url['this']);
		
		$nowlist = array();
		$nowlist['start'] = ($page-1)*$pagesize+1;
		$nowlist['end'] = min($nowlist['start']+$pagesize-1,$pageTotal);
		
		$param = array(
			'wd' => $wd,
			'title' => $q['title'],
			'order' => $order
		);
		$result = array('result'=>$data, 'total'=>$total);
		echo json_encode($result);
	}

}