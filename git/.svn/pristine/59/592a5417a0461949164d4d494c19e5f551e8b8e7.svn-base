<?php
class controller_panel extends contribution_controller_abstract
{
	
	function __construct(&$app)
	{
		parent::__construct($app);
		if (!$this->_userid)
		{
			$this->redirect(url('member/index/login'));exit;
		}
		$this->space =loader::model('space','space');
		$this->contribution = loader::model('contribution');
		$space_on = loader::model('admin/app','system')->get_field('disabled',"`app`='space'");
		$this->space_exist = $this->space->get_by('userid',$this->_userid);
		if($this->space_exist && $this->space_exist['status'] > 2)
		{
			$this->redirect(url('space/panel/index')); //转入专栏模式下
		}
		$this->template->assign('space_exist', $this->space_exist);
		$this->template->assign('statistics', $this->contribution->get_person_count($this->_userid));
		$this->template->assign('space_on', $space_on);
	}

	function index()
	{
		$this->published();
	}
	
	function page()
	{
		$where = array();
		$where['status'] = isset($_GET['status']) ? intval($_GET['status']) : 6;
		$where['createdby'] = $this->_userid;
		$page = isset($_GET['page'])?max(intval($_GET['page']),1):1;
		$pagesize = isset($_GET['pagesize']) ? intval($_GET['pagesize']) : $this->pagesize;
		$fields = '*';
		$order = '`created` DESC';
		
		if($where['status'] == 6)
		{
			$data = $this->contribution->get_publish($where, $fields, $order, $page, $pagesize);
		}
		else
		{
			$data = $this->contribution->ls($where, $fields, $order, $page, $pagesize);
		}
		
		$result = array(
			'total'=>$this->contribution->count($where),
			'data'=>$data,
			'num' => $this->contribution->get_person_count($this->_userid));
		echo $this->json->encode($result);
	}
	
	function published()
	{
		$data = array(
			'title' => '已发表',
			'subTpl' => 'published',
			'status' => 6
		);
		$this->template->assign($data);
		$this->template->display('contribution/panel/list.html');
	}

	function submitted()
	{
		$data = array (
			'title' => '投稿中',
			'subTpl' => 'submitted',
			'status' => 3
		);
		$this->template->assign($data);
		$this->template->display('contribution/panel/list.html');
	}
	
	function rejected()
	{
		$data = array (
			'title' => '已退稿',
			'subTpl' => 'rejected',
			'status' => 2
		);
		$this->template->assign($data);
		$this->template->display('contribution/panel/list.html');
	}
	
	function drafted()
	{
		$data = array (
			'title' => '草稿箱',
			'subTpl' => 'drafted',
			'status' => 1
		);
		
		$this->template->assign($data);
		$this->template->display('contribution/panel/list.html');
	}
	
	function contribute()
	{
		$isseccode = value($this->setting, 'isseccode', 0);
		if($this->is_post())
		{
			$data = $_POST;
			if ($isseccode)
			{
				import('helper.seccode');
				$seccode = new seccode();
				if (!$seccode->valid(true))
				{
					$this->showmessage('验证码不正确');
				}	
			}			
			if(trim($_POST['title']) == '') $data['title'] = date('Y年m月d日 未命名标题稿件',TIME);
			$data['title'] = strip_tags($_POST['title']);
			$data['description'] = htmlspecialchars_deep($data['description']);
			$data['content'] = $this->_strip_tags($_POST['content']);
			$data['status'] = ($_POST['status'] == 3)?3:1;
			if ($contributionid = $this->contribution->add($data))
			{
				$result = array('state'=>true,'message'=>'添加成功','time'=>TIME, 'contributionid'=>$contributionid);
			}
			else
			{
				$result = array('state'=>false, 'error'=>$this->contribution->error());
			}
			
			echo $this->json->encode($result);
		}
		else
		{
			$channel = channel();
			$this->template->assign('channel',$channel);
			$this->template->assign('isseccode', $isseccode);
			$this->template->display('contribution/panel/contribute.html');
		}
	}
	
	function edit()
	{
		if($this->is_post())
		{
			$data = $_POST;
			$contributionid = intval($_POST['contributionid']);
			$r = $this->contribution->get($contributionid, '*');
			if(!$r || $r['createdby'] != $this->_userid)
			{
				$result = array('state'=>false,'error' => '该文章不存在');
			}
			else if($data['status'] >= 3 && $data['contentid'])
			{
				$result = array('state'=>false,'error' => '文章已锁定');
			}
			else
			{
				$data['title'] = strip_tags($_POST['title']);
				$data['description'] = htmlspecialchars_deep($data['description']);
				$data['content'] = $this->_strip_tags($_POST['content']);
				if ($this->contribution->edit($contributionid, $data))
				{
					$result = array('state'=>true);
				}
				else
				{
					$result = array('state'=>false, 'error'=>$this->contribution->error());
				}
			}
			echo $this->json->encode($result);
		}
		else
		{
			$contributionid = intval($_GET['contributionid']);
			$data = $this->contribution->get($contributionid, '*');
			if(!$data || $data['createdby'] != $this->_userid)
			{
				$this->showmessage('该文章不存在');
			}
			else
			{
				if($data['status'] >= 3 && $data['contentid'])
				{
					$this->showmessage('文章已锁定');
				}
			}
			$channel = channel();
			$this->template->assign('channel',$channel);
			$this->template->assign('contribution',$data);
			$this->template->display('contribution/panel/edit.html');
		}
	}
	
	function delete()
	{
		$contributionid = intval($_GET['contributionid']);
		$data = $this->contribution->get($contributionid, '*');
		if(!$data || $data['createdby'] != $this->_userid)
		{
			$return = array('state' => false, 'error'=>'该文章不存在');
		}
		else
		{
			if($data['status'] >= 3 && $data['contentid'])
			{
				$return = array('state' => false, 'error'=>'文章已锁定');
			}
			else
			{
				if($this->contribution->del($contributionid) === false)
				{
					$return = array('state' => false, 'error'=>$this->contribution->error());
				}
				else
				{
					$return = array('state' =>true,'message'=>'成功删除文章');
				}
			}
		}
		echo $this->json->encode($return);
	}
	
	//投稿
	function submit()
	{
		$contributionid = intval($_GET['contributionid']);
		$data = $this->contribution->get($contributionid, '*');
		if(!$data || $data['createdby'] != $this->_userid)
		{
			$return = array('state' => false, 'error'=>'该文章不存在');
		}
		else
		{
			if($data['status'] >= 3 && $data['contentid'])
			{
				$return = array('state' => false, 'error'=>'文章已锁定');
			}
			else
			{
				if($this->contribution->submit($contributionid))
				{
					$return = array('state' =>true,'message'=>'投稿成功');
				}
				else
				{
					$return =  array('state' =>false,'message'=>'投稿失败');
				}
			}
		}
		echo $this->json->encode($return);
	}
	
	//撤稿
	function cancel()
	{
		$contributionid = intval($_GET['contributionid']);
		$data = $this->contribution->get($contributionid, '*');
		$data = $this->contribution->get($contributionid, '*');
		if(!$data || $data['createdby'] != $this->_userid)
		{
			$return = array('state' => false, 'error'=>'该文章不存在');
		}
		else
		{
			if($data['status'] >= 3 && $data['contentid'])
			{
				$return = array('state' => false, 'error'=>'文章已锁定');
			}
			else
			{
				if($this->contribution->cancel($contributionid))
				{
					$return = array('state' =>true,'message'=>'取消成功');
				}
				else
				{
					$return =  array('state' =>false,'message'=>'发生错误失败');
				}
			}
		}
		echo $this->json->encode($return);
	}

	private function _strip_tags($content)
	{
		$allows = '<a><b><br><div><em><font><h1><h2><h3><h4><h5><h6><hr><i><img><li><strong><table><tbody><td><th><thead><tr><u><ul><p>';
		return strip_tags($content, $allows);
	}
}