<?php
/**
 * 用户组管理
 *
 * @aca 用户组管理
 */
class controller_admin_group extends member_controller_abstract
{
	private $group, $pagesize = 15;
	
	function __construct(& $app)
	{
		parent::__construct($app);
		$this->group = loader::model('member_group');
		import('form.form_element');
		import('form.element');
	}

    /**
     * 浏览
     *
     * @aca 浏览
     */
	function index()
	{
		$head = array('title'=>'用户组');
		$this->view->assign('head', $head);
		$this->view->display("group/index");
	}

    /**
     * 列表
     *
     * @aca 浏览
     */
	function page()
	{
		$where = null;
		$fields = '*';
		$order = '`groupid` DESC';
		$page = max((isset($_GET['page']) ? intval($_GET['page']) : 1), 1);
		$size = max((isset($_GET['size']) ? intval($_GET['size']) : $this->pagesize), 1);
		$group_persons = $this->group->group_persons();
		$data = $this->group->page($where, $fields, $order, $page, $size);
		foreach ($data as $key => $value)
		{
			$data[$key]['persons'] = $group_persons[$value['groupid']] ? $group_persons[$value['groupid']] : '0';
			$data[$key]['system'] = $value['issystem'] ? '' : '<img src="images/sh.gif" alt="自定义" width="16" height="16" class="manage" />';
		}
		echo $this->json->encode(array('data' =>$data));
	}

    /**
     * 添加
     *
     * @aca 添加
     */
	function add()
	{
		if ($this->is_post())
		{
			$data = $_POST;
			if ($groupid = $this->group->add($data))
			{
				$data = $this->group->get($groupid);
				$group_persons = $this->group->group_persons();
				
				$data['persons'] = $group_persons[$data['groupid']] ? $group_persons[$data['groupid']] : '0';
				$data['system'] = $data['issystem'] ? '是' : '否';
				table_cache('member_group');
				$result = array('state'=>true, 'data'=>$data);
			}
			else 
			{
				$result = array('state'=>false, 'error'=>$this->group->error);
			}
			echo $this->json->encode($result);
		}
		else
		{
			$this->view->display('group/add');
		}
	}

    /**
     * 编辑
     *
     * @aca 编辑
     */
	function edit()
	{
		$groupid = $_GET['groupid'];
		if ($this->is_post())
		{
			$data = $_POST;
			if ($this->group->edit($groupid, $data) !== false)
			{
				$returndata = $this->group->get($groupid);
				$group_persons = $this->group->group_persons();
				
				$returndata['persons'] = $group_persons[$returndata['groupid']] ? $group_persons[$returndata['groupid']] : '0';
				$returndata['system'] = $returndata['issystem'] ? '' : '<img src="images/sh.gif" alt="自定义" width="16" height="16" class="manage" />';
				
				table_cache('member_group');
				$result = array('state'=>true, 'data'=>$returndata);
			}
			else 
			{
				$result = array('state'=>false, 'error'=>$this->group->error);
			}
			echo $this->json->encode($result);
		}
		else
		{
			$group = $this->group->get($groupid);
			$this->view->assign($group);
		    $this->view->display('group/edit');
		}
	}

    /**
     * 删除
     *
     * @aca 删除
     */
	function delete()
	{
		$groupid = $_GET['id'];
		$groupdata = $this->group->get($groupid);
		
		$group_persons = $this->group->group_persons();
		if($groupdata['issystem'])
		{
			$result = array('state' => false,'error' => '系统内置组不能删除');
		}
		elseif($group_persons[$groupid]>0)
		{
			$result = array('state' => false,'error' => '用户组人数大于零');
		}
		else
		{
			if($this->group->delete($groupid))
			{
				table_cache('member_group');
				$result=  array('state'=>true);
			}
			else
			{
				$result = array('state'=>false, 'error'=>$this->group->error);
			}
		}
		echo $this->json->encode($result);
	}

    /**
     * 移动用户组
     *
     * @aca 移动
     */
	function changegroup()
	{
		if ($this->is_post())
		{
			$data['groupid'] = intval($_POST['groupid']);
			if($_POST['groupid'] == 1)
			{
				echo $this->json->encode(array('state'=>false,'error'=>'不容许修改为管理员'));exit;
			}
			$where = "userid IN ({$_POST['userid']}) AND `groupid`!=1";
			if(isset($_POST['oldgroupid']))
			{
				if($_POST['oldgroupid'] == 1)
				{
					echo $this->json->encode(array('state'=>false,'error'=>'管理员不容许修改用户组'));exit;
				}
				elseif ($_POST['oldgroupid'] == $data['groupid'])
				{
					echo $this->json->encode(array('state'=>false,'error'=>'目标用户组与原用户组一致'));exit;
				}
				$where = "groupid = {$_POST['oldgroupid']}";
			}
			
			$this->member = loader::model('member');
			if($this->member->set_field('groupid',$data['groupid'],$where))
			{
				$return = array('state'=>true,'data' => $this->member->select($where));
			}
			else
			{
				$return = array('state'=>false,'error'=>$this->member->error());
			}
			echo $this->json->encode($return);
		}
		else
		{
			$groups = $this->group->ls();
			foreach($groups as $k =>$v)
			{
				if($v['groupid'] == 1 || $v['groupid'] == 2 || $v['groupid'] == $_GET['groupid'])
				{
					unset($groups[$k]);
				}
			}
			$this->view->assign('groups',$groups);
		    $this->view->display('group/changegroup');
		}
	}
}