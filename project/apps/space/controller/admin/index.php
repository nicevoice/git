<?php
/**
 * 专栏管理
 *
 * @aca 专栏管理
 */
class controller_admin_index extends space_controller_abstract
{
	private $space, $pagesize = 15;
	
	public function __construct(& $app)
	{
		parent::__construct($app);
		$this->space = loader::model('space');
	}

    /**
     * 专栏管理
     *
     * @aca 浏览
     */
	public function index()
	{
		$head = array('title'=>'专栏管理');
		$this->view->assign('head', $head);
		$this->view->assign('statuss', $this->space->statuss);
		$this->view->display('index');
	}

    /**
     * 列表
     *
     * @aca 浏览
     */
	public function page()
	{
		$where = null;
		$fields = '*';
		$order = isset($_GET['orderby']) ? str_replace('|', ' ', $_GET['orderby']) : '`sort` DESC';
		
		if (isset($_GET['keywords']) && $_GET['keywords']) $where[] = where_keywords('author', $_GET['keywords']);
		if (is_numeric($_GET['status']))
		{
			$status = intval($_GET['status']);
			$where[] = ($status == 3)?'`status`>=3':'`status`='.$status;
		}
		if ($where) $where = implode(' AND ', $where);
		$page = max((isset($_GET['page']) ? intval($_GET['page']) : 1), 1);
		$size = max((isset($_GET['pagesize']) ? intval($_GET['pagesize']) : $this->pagesize), 1);
		
		$data = $this->space->page($where, $fields, $order, $page, $size);
		$total = $this->space->count($where);
		
		$result = array('data'=>$data, 'total'=>$total);
		echo $this->json->encode($result);
	}

    /**
     * 搜索
     *
     * @aca 浏览
     */
	public function search()
	{
		$this->view->display('search');
	}

    /**
     * 添加
     *
     * @aca 添加
     */
	public function add()
	{
		if ($this->is_post())
		{
			if($this->space->add($_POST))
			{
				$result = array('state' =>true,'message' => '添加成功');
			}
			else
			{
				$result = array('state' =>false,'error' => $this->space->error());
			}
			echo $this->json->encode($result);
		}
		else
		{
			$head = array('title'=>'添加专栏');
			$this->view->assign('statuss', $this->space->statuss);
			$this->view->assign('head', $head);
			$this->view->display('add');
		}
	}

    /**
     * 编辑
     *
     * @aca 编辑
     */
	public function edit()
	{
		$spaceid = intval($_GET['spaceid']);
		if ($this->is_post())
		{
			if($this->space->edit($_POST,$spaceid))
			{
				$result = array('state' =>true,'message' => '修改成功','data' => $this->space->get($spaceid));
			}
			else
			{
				$result = array('state' =>false,'error' => $this->space->error());
			}
			echo $this->json->encode($result);
		}
		else
		{
			$space = $this->space->get($spaceid);
			$head = array('title'=>'编辑专栏');
			$this->view->assign('statuss', $this->space->statuss);
			$this->view->assign('head', $head);
			$this->view->assign($space);
			$this->view->display('edit');
		}
	}

    /**
     * 专栏面板
     *
     * @aca 浏览
     */
	public function panel()
	{
		$spaceid = intval($_GET['spaceid']);
		$space = $this->space->get($spaceid);
		$head = array('title'=>$space['name'].'_专栏面板');
		$this->view->assign('statuss', $this->space->statuss);
		$this->view->assign('head', $head);
		$this->view->assign($space);
		$this->view->display('panel');
	}

    /**
     * 删除
     *
     * @aca 删除
     */
	public function delete()
	{
		if(empty($_POST['spaceid']))
		{
			$result = array('state'=>false, 'error'=>'用户ID为空');
		}
		else
		{
			$where = "`spaceid` IN ({$_POST['spaceid']})";
			if($this->space->delete($where))
			{
				$result = array('state'=>true, 'message'=>'删除成功');
			}
			else
			{
				$result = array('state'=>false, 'error'=>'发生错误');
			}
		}
		echo $this->json->encode($result);
	}

    /**
     * 专栏地址验证
     *
     * @aca public 专栏地址验证
     */
	public function validate()
	{
		$url = $_GET['url'];
		$userid = intval($_GET['userid']);
		$r = $this->space->get("`url`='".$url."'");
		if(!$r || $r['userid'] == $userid)
		{
			$return = array('state' => true, 'info' => '可以使用');
		}
		else
		{
			$return =  array('state' => false, 'error' => '已经注册');
		}
		echo $this->json->encode($return);
	}

    /**
     * 管理作者检查
     *
     * @aca public 管理作者检查
     */
	function author_check()
	{
		$author = $_GET['author'];
		$r = $this->space->get_by('author',$author);
		if($r)
		{
			$result = array('state' => false,'error' => '已经存在');
		}
		else
		{
			$result = array('state' => true,'info' => '可以使用');
		}
		echo $this->json->encode($result);
	}

    /**
     * 别名检查
     *
     * @aca public 别名检查
     */
	function alias_check()
	{
		$alias = $_GET['alias'];
		$r = $this->space->get_by('alias',$alias);
		if($r)
		{
			$result = array('state' => false,'error' => '已经存在');
		}
		else
		{
			$result = array('state' => true,'info' => '可以使用');
		}
		echo $this->json->encode($result);
	}

    /**
     * 启用禁用
     *
     * @aca 启用禁用
     */
	function status()
	{
		$spaceid = $_POST['spaceid'];
		$status = intval($_POST['status']);
		$result = $this->space->status($spaceid,$status);
		echo $this->json->encode($result);
	}
	
    /**
     * 搜索建议
     *
     * @aca public 搜索建议
     */
	function suggest()
	{
		$q = $_REQUEST['q'];
		$where = '';
		if (trim($q) != '')
		{
			$q = str_replace('_', '\_', addcslashes($q, '%_'));
			$where = "`author` LIKE '%$q%'";
			if (preg_match("/^[\w]+$/", $q))
			{
				// 字母和数字(也搜索initial字段)
				$where .= " OR `initial` LIKE '$q%'";
			}
		}
		$data = $this->space->select($where, 'author', '`sort` DESC', 30);
		foreach ($data as & $r)
		{
			$r['text'] = $r['author'];
		}
		echo $this->json->encode($data);
	}
	
	/**
     * 关联用户搜索建议
     *
     * @aca public 关联用户搜索建议
     */
	function username()
	{
		$q = $_REQUEST['q'];
		$where = '';
		if (trim($q) != '')
		{
			$q = str_replace('_', '\_', addcslashes($q, '%_'));
			$where = "`username` LIKE '%$q%'";
		}
		
		$data = $this->space->username($where);
		foreach ($data as & $r)
		{
			$r['text'] = $r['username'];
		}
		echo $this->json->encode($data);
	}
}