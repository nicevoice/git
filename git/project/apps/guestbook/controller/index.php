<?php
class controller_index extends guestbook_controller_abstract
{
	private $guestbook, $pagesize = 15, $guestbook_type;

	function __construct(&$app)
	{
		parent::__construct($app);
		$this->guestbook = loader::model('guestbook');
	}
	
	function index()
	{
        $typeid = isset($_GET['typeid']) ? intval($_GET['typeid']) : 0;
        $page = max((isset($_GET['page']) ? intval($_GET['page']) : 1), 1);
        $pagesize = isset($_GET['pagesize']) ? intval($_GET['pagesize']) : 0;
        if(empty($pagesize)) $pagesize = $this->pagesize;

        if ($this->system['pagecached'])
        {
            $keyid = md5('pagecached_guestbook_index_index_' .$typeid.'_'.$page.'_'.$pagesize);
            cmstop::cache_start($this->system['pagecachettl'], $keyid);
        }

		$type = $this->guestbook->count_type($this->setting['repliedshow']);
		$setting = $this->json->encode($this->setting['option']);
		
		$where = null;
		if($this->setting['repliedshow'])
		{
			$where[] = "reply != ''";
		}
		if($typeid)
		{
			$where[] = "`typeid`=".$typeid;
		}
		
		$where = implode(' AND ', $where);
		
		$fields = '*';
		$order = '`gid` DESC';
		$data  = $this->guestbook->ls($where, $fields, $order, $page, $pagesize);
		$total = $this->guestbook->count($where);
		$multipage = pages($total, $page, $pagesize, 2);
		
		$this->template->assign('type', $type);
		$this->template->assign('data', $data);
		$this->template->assign('multipage', $multipage);
		$this->template->assign('total', $total);
		$this->template->assign('setting', $setting);
		$this->template->display('guestbook/index.html');

        if ($this->system['pagecached']) cmstop::cache_end();
	}
	
	function add()
	{
		if($this->is_post())
		{
			if(!$this->_submit_check())
			{
				$result = array('state' => false, 'message' => $this->error);
			}
			else
			{
				if($gid = $this->guestbook->add($_POST))
				{
					$result = array('state' => true, 'message' => '留言成功');
				}
				else
				{
					$result = array('state' => false, 'message' => $this->guestbook->error());
				}
			}
			$this->showmessage($result['message'], url('guestbook/index/index'), 3000, $result['state']);
		}
		else
		{
			$this->index();
		}
	}
	
	function seccode()
	{
		import('helper.seccode');
		$seccode = new seccode();
		$return = $seccode->valid()
				? array('state' => true, 'message' => '正确')
				: array('state' => false, 'error' => '验证码不正确');
		echo $this->json->encode($return);
	}
	//提交
	function _submit_check()
	{
		$set = $this->setting;
		if(empty($_POST['username']))
		{
			$this->error = '姓名为空';
			return false;
		}
		if(empty($_POST['title']))
		{
			$this->error = '标题为空';
			return false;
		}
		if(empty($_POST['content']))
		{
			$this->error = '内容为空';
			return false;
		}
		if(mb_strlen($_POST['content'],'utf-8') > $set['replymax'])
		{
			$this->error = '留言超过最大限制字数';
			return false;
		}
		if($set['iscode'])
		{
			import('helper.seccode');
			$seccode = new seccode();
			if(!$seccode->valid())
			{
				$this->error = '验证码不正确';
				return false;
			}
		}
		if($set['option']['gender'] && $set['option']['isgender'] && empty($_POST['gender']))
		{
			$this->error = '性别为空';
			return false;
		}
		if($set['option']['email'] && $set['option']['isemail'] && empty($_POST['email']))
		{
			$this->error = 'E-mail为空';
			return false;
		}
		if($set['option']['address'] && $set['option']['isaddress'] && empty($_POST['address']))
		{
			$this->error = '地址为空';
			return false;
		}
		if($set['option']['telephone'] && $set['option']['istelephone'] && empty($_POST['telephone']))
		{
			$this->error = '电话为空';
			return false;
		}
		if($set['option']['qq'] && $set['option']['isqq'] && empty($_POST['qq']))
		{
			$this->error = 'QQ为空';
			return false;
		}
		if($set['option']['msn'] && $set['option']['ismsn'] && empty($_POST['msn']))
		{
			$this->error = 'MSN为空';
			return false;
		}
		if($set['option']['homepage'] && $set['option']['ishomepage'] && empty($_POST['homepage']))
		{
			$this->error = '个人主页为空';
			return false;
		}
		$_POST = htmlspecialchars_deep($_POST);
		return true;
	}
}