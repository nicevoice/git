<?php
/**
 * 历史页面
 *
 * @aca 历史页面
 */
class controller_admin_history extends history_controller_abstract
{
	private $h;
	
	function __construct(& $app)
	{
		parent::__construct($app);
		$this->h = loader::model('admin/history');
	}

    /**
     * 浏览
     *
     * @aca 浏览
     */
	function index()
	{
		$this->view->assign('head', array('title'=>'历史页面'));
		$this->view->display("history/index");
	}
	
	/**
     * 历史页面生成
     *
     * @aca 历史页面生成
     */
	function exec()
	{
		$hid = intval($_REQUEST['hid']);
		if(!$hid) 
		{
			exit($this->json->encode(array('state'=>false,'error'=>'无法获取hid')));
		}
		exit($this->json->encode($this->h->exec($hid)));
	}

    /**
     * 列表
     *
     * @aca 浏览
     */
	function page()
	{
		if($_GET['orderby'] && !preg_match('/^[\w\|\.]+$/i', $_GET['orderby'])) exit;
		$order = $_GET['orderby'] ? str_replace('|', ' ', $_GET['orderby']) : 'hid DESC';
		$page = max((isset($_GET['page']) ? intval($_GET['page']) : 1), 1);
		$size = max((isset($_GET['pagesize']) ? intval($_GET['pagesize']) : $this->pagesize), 1);
		$total = $this->h->count();
		$data = $this->h->page(null, $order, $page, $size);
		$rs = array('total' => $total, 'data' => $data);
		echo $this->json->encode($rs);
	}

    /**
     * 新建保存
     *
     * @aca 新建保存
     */
	function save()
	{
    	if ($this->is_post())
        {
			if ($id = $this->h->save($_POST))
			{
				$result = array('state'=>true, 'data'=>$this->h->getById($id));
			}
			else
			{
				$result = array('state'=>false, 'error'=>$this->h->error());
			}
			exit($this->json->encode($result));
        }
       
        
        $h = $this->h->getById(intval($_GET['hid']));
        if(!$h['hid']) 
        {
        	//设置默认值
        	$h = array('mode' => 3, 'hourArr' => array(12), 'disabled' => 0);
        }
    	$this->view->assign($h);
        $this->view->display('history/form');
    }

    /**
     * 删除
     *
     * @aca 删除
     * @return mixed
     */
	function delete()
	{
		$id = $_GET['id'];
		if(!$id) return ;
		if($this->h->del($id)) 
		{
			$this->encode(true);
		}
		
		$this->encode(false, '删除失败');
	}

	/**
	 * 升级时重新生成旧页面
     *
     * @aca public 升级旧页面
	 */
	function update()
	{
		$dir = WWW_PATH.'section/history/index/';
		$files = glob($dir.'*.html');
		foreach ($files as $key => $file)
		{
			if (!preg_match_all('/(\\d{4})-(\\d{2})\\.html/', $file, $match))
			{
				continue;
			}
			$context = file_get_contents($file);

			$orgin_year		= $match[1][0];
			$orgin_month	= $match[2][0];
			$date['preYear']	= date('Y-m', mktime(0, 0, 0, $orgin_month, 1, $orgin_year - 1));
			$date['preMonth']	= date('Y-m', mktime(0, 0, 0, $orgin_month - 1, 1, $orgin_year));
			$date['nextYear']	= date('Y-m', mktime(0, 0, 0, $orgin_month, 1, $orgin_year + 1));
			$date['nextMonth']	= date('Y-m', mktime(0, 0, 0, $orgin_month + 1, 1, $orgin_year));
			foreach (array('preYear', 'preMonth', 'nextYear', 'nextMonth') as $field)
			{
				$replacement = 'href="'.WWW_URL.'section/history/index/'.$date[$field].'.html" id="'.$field.'"';
				$context = preg_replace('/href="([^"]*)"\\s+id="'.$field.'"/im', $replacement, $context);
			}
			file_put_contents($file, $context);
		}
	}
}