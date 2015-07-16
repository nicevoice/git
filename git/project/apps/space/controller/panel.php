<?php

class controller_panel extends space_controller_abstract
{
	private $article, $space, $space_exist, $pagesize = 10;
	
	function __construct(&$app)
	{
		parent::__construct($app);
		if(!$this->_userid)
		{
			$this->redirect(url('member/index/login'));exit;
		}
		$this->space =loader::model('space');
		
		$this->space_exist = $this->space->get_by('userid',$this->_userid);
		if(!$this->space_exist)
		{
			if(!in_array($_GET['action'],array('apply')))
			{
				$this->redirect(url('contribution/panel/index')); //转入非专栏模式下
			}
		}
		else
		{
			if($this->space_exist['status'] < 3 && !in_array($_GET['action'],array('apply','setting')))
			{
				$this->redirect(url('contribution/panel/index')); //转入非专栏模式下
			}
		}
		
		$this->template->assign('space_exist', $this->space_exist);
		$this->article = loader::model('admin/article','article');
		
		if(!$this->is_ajax())
		{
			$statistics = $this->article->statistics($this->space_exist['spaceid']);
			$this->template->assign('statistics', $statistics);
		}
		
	}

	function index()
	{
		if($this->space_exist['status'] > 2)
		{
			$this->published();
		}
		else 
		{
			$this->template->display('space/panel/index.html');
		}
	}
	
	function apply()
	{
		if($this->space_exist)
		{
			if($this->space_exist['status'] >2)
			{
				$this->showmessage('您已经开通个人专栏');
			}
			else
			{
				$this->space->update(array('status' => 1),array('spaceid' => $this->space_exist['spaceid']));
				$this->showmessage('申请提交成功,请等待管理员审核');
			}
		}
		else
		{
			if($this->is_post())
			{
				$data = htmlspecialchars_deep($_POST);
				$data['userid'] = $this->_userid;
				$data['status'] = 1;
				
				if(!$this->space->add($data))
				{
					$return =array('state'=>false,'message' => $this->space->error());
				}
				else
				{
					$return = array('state'=>true,'message'=>'申请提交成功');
				}
				echo $this->json->encode($return);
			}
			else
			{
				$this->template->display('space/panel/apply.html');
			}
		}
	}
	
	function setting()
	{
		$r = $this->space->get_by('userid',$this->_userid);
		if($this->is_post())
		{
			$data = htmlspecialchars_deep($_POST);
			if(!$r)
			{
				$data['userid'] = $this->_userid;
				if(!$this->space->add($data))
				{
					$return =array('state'=>false,'message' =>  $this->space->error());
				}
				else
				{
					$return = array('state'=>true,'message'=>'更新成功');
				}
			}
			else
			{
				if(!$this->space->update($data, array('userid' => $this->_userid)))
				{
					$return =array('state'=>false,'message' => $this->space->error());
				}
				else
				{
					$return = array('state'=>true,'message'=>'更新成功');
				}
			}
			echo $this->json->encode($return);
		}
		else
		{
			if($r['name']=='') $r['name'] = $this->_username.'的个人专栏';
			
			$this->template->assign($r);
			$this->template->display('space/panel/setting.html');
		}
	}
	
	function validate()
	{
		$url = trim($_GET['url']);
		$r = $this->space->get(array('url' => $url));
		if(!$r || $r['userid'] == $this->_userid)
		{
			$return = array('state' => true, 'info' => '可以使用');
		}
		else
		{
			$return =  array('state' => false, 'error' => '已经注册');
		}
		echo $this->json->encode($return);
	}
	
	function page()
	{
		$where = array();
		$where['catid'] = intval($_GET['catid']);
		$where['status'] = isset($_GET['status']) ? intval($_GET['status']) : 6;
		$page = isset($_GET['page'])?max(intval($_GET['page']),1):1;
		$pagesize = isset($_GET['pagesize']) ? intval($_GET['pagesize']) : $this->pagesize;
		
		$where['spaceid'] = $this->space_exist['spaceid'];
		
		$fields = '*';
		$order = '`created` DESC';
		
		if($where['status'] == 6) $table_article = true; 
		else $table_article = false; 
		
		$data = $this->article->ls($where, $fields, $order, $page, $pagesize, $table_article);
		
		$result = array('total'=>$this->article->total, 'data'=>$data);
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
		$this->template->display('space/panel/list.html');
	}

	function submitted()
	{
		$data = array (
			'title' => '投稿中',
			'subTpl' => 'submitted',
			'status' => 3
		);
		$this->template->assign($data);
		$this->template->display('space/panel/list.html');
	}
	
	function rejected()
	{
		$data = array (
			'title' => '已退稿',
			'subTpl' => 'rejected',
			'status' => 2
		);
		$this->template->assign($data);
		$this->template->display('space/panel/list.html');
	}
	
	function drafted()
	{
		$data = array (
			'title' => '草稿箱',
			'subTpl' => 'drafted',
			'status' => 1
		);
		
		$this->template->assign($data);
		$this->template->display('space/panel/list.html');
	}
	
	function contribute()
	{
		if($this->is_post())
		{
			$data = $_POST;
			if(trim($_POST['title']) == '') $data['title'] = date('Y年m月d日 未命名标题稿件',TIME);
			$data['iscontribute'] = 1;
			$data['modelid'] = 1;
			$data['author'] = $this->space_exist['author'];
			$data['title'] = strip_tags($_POST['title']);
			$data['description'] = htmlspecialchars_deep($data['description']);
			$data['content'] = $this->_strip_tags($_POST['content']);
			$data['status'] = 3;
			if(!empty($this->space_exist['iseditor'])) $data['status'] = 6;
			if ($contentid = $this->article->add($data))
			{
				$result = array('state'=>true,'message'=>'添加成功','time'=>TIME, 'contentid'=>$contentid);
			}
			else
			{
				$result = array('state'=>false, 'error'=>$this->article->error());
			}
			
			echo $this->json->encode($result);
		}
		else
		{
			$channel = channel();
			$this->template->assign('channel',$channel);
			$this->template->display('space/panel/contribute.html');
		}
	}
	
	function edit()
	{
		if($this->is_post())
		{
			$data = $_POST;
			$contentid = intval($_POST['contentid']);
			$r = $this->article->get($contentid, '*', 'get');
			if(!$r || $r['spaceid'] != $this->space_exist['spaceid'])
			{
				$result = array('state'=>false,'error' => '该文章不存在');
			}
			else
			{
				$data['status'] = 3;	// 前台编辑文章永远为草稿
				$data['author'] = $this->space_exist['author'];
				$data['title'] = strip_tags($_POST['title']);
				$data['description'] = htmlspecialchars_deep($data['description']);
				$data['content'] = $this->_strip_tags($_POST['content']);
				if ($this->article->edit($contentid, $data))
				{
					$result = array('state'=>true);
				}
				else
				{
					$result = array('state'=>false, 'error'=>$this->article->error());
				}
			}
			echo $this->json->encode($result);
		}
		else
		{
			$contentid = intval($_GET['contentid']);
			$data = $this->article->get($contentid, '*', 'get');
			if(!$data) $this->showmessage($this->article->error());
			if($data['spaceid'] != $this->space_exist['spaceid']) $this->showmessage('该文章不存在');

			//$this->article->lock($contentid);
			
			$channel = channel();
			$this->template->assign('channel',$channel);
			$this->template->assign('contribute',$data);
			$this->template->display('space/panel/edit.html');
		}
	}
	
	function delete()
	{
		$contentid = intval($_GET['contentid']);
		$data = $this->article->get($contentid, '*', 'get');
		if($data['spaceid'] != $this->space_exist['spaceid']) $this->showmessage('该文章不存在');
		
		if($this->article->delete($contentid) === false)
		{
			$return = array('state' => false, 'message'=>$this->article->error());
		}
		else
		{
			$return = array('state' =>true,'message'=>'成功删除文章', 'num' => $this->article->statistics($this->space_exist['spaceid']));
		}
		echo $this->json->encode($return);
	}
	
	function islock()
	{
		$contentid = intval($_GET['contentid']);
		$result = array('state'=>true);
		echo $this->json->encode($result);
	}
	
	private function _strip_tags($content)
	{
		$allows = '<a><b><br><div><em><font><h1><h2><h3><h4><h5><h6><hr><i><img><li><strong><table><tbody><td><th><thead><tr><u><ul><p>';
		return strip_tags($content, $allows);
	}
}