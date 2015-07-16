<?php
/**
 * 管理报名者
 *
 * @aca 管理报名者
 */
class controller_admin_sign extends activity_controller_abstract
{
	private $sign,$activity,$pagesize = 15, $modelid;

	function __construct(& $app)
	{
		parent::__construct($app);
		
		$this->sign = loader::model('admin/sign');
		$this->activity = loader::model('admin/activity');
		$this->modelid = $this->sign->modelid;
	}

	public function __call($method, $args)
	{
		if(in_array($method, array('delete', 'clear', 'remove', 'restore', 'restores', 'approve', 'pass', 'reject', 'islock', 'lock', 'unlock', 'publish', 'unpublish'), true))
		{
			$var = in_array($method, array('clear', 'restores')) ? 'catid' : 'contentid';
			$result = $this->sign->$method($_REQUEST[$var]) ? array('state'=>true) : array('state'=>false, 'error'=>$this->sign->error());
			echo $this->json->encode($result);
		}
	}

    /**
     * 查看报名者
     *
     * @aca 查看
     * @return mixed
     */
	function view()
	{
		if(!$_GET['signid']) return ;
		$signid = $_GET['signid'];
		$data = $this->sign->get($signid);
		$this->view->assign('fields',$data['fields']);
		$this->view->assign('data',$data);
		$this->view->display('viewsign');
	}

    /**
     * 报名者分页列表
     *
     * @aca 查看
     */
	function page()
	{
		$contentid = intval($_GET['contentid']);
		$catid = intval($_GET['catid']);
		$state = isset($_GET['state']) ? intval($_GET['state']) : 0;
		$page = isset($_GET['page']) ? intval($_GET['page']) : 0;
		$pagesize = isset($_GET['pagesize']) ? intval($_GET['pagesize']) : $this->pagesize;
		$created_min = $_GET['created_min']?strtotime($_GET['created_min']):'';
		$created_max = $_GET['created_max']?strtotime($_GET['created_max']):'';
		if(!empty($created_min) && $created_min == $created_max)
		{
			$created_min += 86400;
			$created_max_where = ' AND created <='.$created_min.' ';
			$created_min -= 86400;
			$created_min_where = ' AND created >='.$created_min.' ';
		}
		if($created_min != $created_max)
		{
			$created_max_where = !empty($created_max)?'AND created <='.$created_max.' ':'';
			$created_min_where = !empty($created_min)?'AND created >='.$created_min.' ':'';
		}
		$where = "state = $_GET[state] AND contentid = $contentid ".$created_min_where.$created_max_where;
		$fields = '*';
		$order = isset($_GET['orderby']) ? str_replace('|', ' ', $_GET['orderby']) : '`created` DESC';

		$data = $this->sign->ls($where, $fields, $order, $page, $pagesize);
		$result = array('total'=>$this->sign->total, 'data'=>$data);
		echo $this->json->encode($result);
	}

    /**
     * 编辑
     *
     * @aca 编辑
     */
	function edit()
	{
		if ($this->is_post())
		{
			if ($this->sign->edit($_POST['signid'],$_POST))
			{
				$data = $this->sign->get($_POST['signid']);
				$result = array('state'=>true, 'data'=>$data);
			}
			else
			{
				$result = array('state'=>false, 'error'=>$this->sign->error());
			}
			echo $this->json->encode($result);
		}
		else
		{
			$signid = $_GET['signid'];
			$data = $this->sign->get($signid);
			$this->view->assign('fields',$data['fields']);
			$this->view->assign('data',$data);
			$this->view->display('editsign');
		}
	}

    /**
     * 通过报名申请
     *
     * @aca 通过
     */
	function pass()
	{
		if($this->sign->pass($_GET['signid']))
		{
			$result = array('state'=>true, 'data'=>$data);
		}
		else
		{
			$result = array('state'=>false, 'error'=>$this->sign->error());
		}
		echo $this->json->encode($result);
	}

    /**
     * 打回报名申请
     *
     * @aca 还原
     */
	function unpass()
	{
		if($this->sign->unpass($_GET['signid']))
		{
			$result = array('state'=>true, 'data'=>$data);
		}
		else
		{
			$result = array('state'=>false, 'error'=>$this->sign->error());
		}
		echo $this->json->encode($result);
	}

    /**
     * 删除报名申请
     *
     * @aca 删除
     */
	function delete()
	{
		$signid = $_GET['signid'];
		if($this->sign->delete($signid))
		{
			$result = array('state'=>true, 'data'=>$data);
		}
		else
		{
			$result = array('state'=>false, 'error'=>$this->sign->error());
		}
		echo $this->json->encode($result);
	}

    /**
     * 导出报名者信息
     *
     * @aca 导出
     */
	function export()
	{
		if(isset($_GET['type']))
		{
			$this->sign->export($_GET['contentid'],$_GET['type']);
		}
		else
		{
			$this->view->display('export');
		}
	}

}