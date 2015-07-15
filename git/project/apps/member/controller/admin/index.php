<?php
/**
 * 管理用户
 *
 * @aca 管理用户
 */
class controller_admin_index extends member_controller_abstract
{
	private $member, $pagesize = 15;
	
	public function __construct(& $app)
	{
		parent::__construct($app);
		$this->member = loader::model('member','member');
		import('form.form_element');
	}

    /**
     * 管理用户
     *
     * @aca 浏览
     */
	public function index()
	{
		$tabs = loader::model('member_group')->tabs();
		$head = array('title'=>'管理用户');
		
		$this->view->assign('head', $head);
		$this->view->assign('tabs', $tabs);
		$this->view->display('index/index');
	}

    /**
     * 用户列表
     *
     * @aca 浏览
     */
	public function page()
	{
		$where = null;
		$fields = '*';
		$order = isset($_GET['orderby']) ? str_replace('|', ' ', $_GET['orderby']) : '`userid` DESC';
		
		switch ($_GET['date'])
		{
			case 'today':
				$regtime['created_min'] = date('Y-m-d H:i:s', strtotime('today'));
				break;
			case 'yesterday':
				$regtime['created_min'] = date('Y-m-d H:i:s', strtotime('yesterday'));
				$regtime['created_max'] = date('Y-m-d H:i:s', strtotime('today'));
				break;
			case 'week':
				$regtime['created_min'] = date('Y-m-d H:i:s', strtotime('last week'));
				break;
			case 'month':
				$regtime['created_min'] = date('Y-m-d H:i:s', strtotime('last month'));
				break;
		}
		
		if (isset($_GET['keywords']) && $_GET['keywords']) $where[] = where_keywords('username', $_GET['keywords']);
		if (isset($_GET['groupid']) && $_GET['groupid']) $where[] = "`groupid`='".$_GET['groupid']."'";
		if (isset($_GET['email']) && $_GET['email']) $where[] = "`email`='".$_GET['email']."'";
		if (isset($regtime['created_min']) && $regtime['created_min']) $where[] = where_mintime('regtime', $regtime['created_min']);
		if (isset($regtime['created_max']) && $regtime['created_max']) $where[] = where_maxtime('regtime', $regtime['created_max']);
		
		if (isset($_GET['userid']) && $_GET['userid'])
		{
			$where = null;
			$where[] = "`userid`='".$_GET['userid']."'";
		}
		
		$page = max((isset($_GET['page']) ? intval($_GET['page']) : 1), 1);
		$size = max((isset($_GET['pagesize']) ? intval($_GET['pagesize']) : $this->pagesize), 1);
		
		if ($where) $where = implode(' AND ', $where);
		$data = $this->member->page($where, $fields, $order, $page, $size);
		$total = $this->member->count($where);
		
		$result = array('data'=>$data, 'total'=>$total);
		echo $this->json->encode($result);
	}

    /**
     * 搜索用户
     *
     * @aca 浏览
     */
	public function search()
	{
		$this->view->display('index/search');
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
			$userid = $this->member->new_add($_POST);
			$result = $userid 
							? array('state' => true, 'data' => $this->member->get($userid))
							: array('state' => false, 'error' => $this->member->error());
			echo $this->json->encode($result);
		}
		else
		{
			$this->view->display('index/add');
		}
	}

    /**
     * 编辑
     *
     * @aca 编辑
     */
	public function edit()
	{
		$userid = intval($_GET['userid']);
		if ($this->is_post())
		{
			if($this->member->new_edit($userid,$_POST))
			{
				$result = array('state'=>true, 'data'=>$this->member->get($userid));
			}
			else
			{
				$result = array('state'=>false, 'error'=>$this->member->error());
			}
			
			echo $this->json->encode($result);
		}
		else
		{
			$member = $this->member->getProfile($userid,true);
			$this->view->assign($member);
			$this->view->display('index/edit');
		}
	}

    /**
     * 修改密码
     *
     * @aca 修改密码
     */
	public function password()
	{
		$userid = intval($_GET['userid']);
		if ($this->is_post())
		{
			if($_POST['password'] != $_POST['password_check'])
			{
				$result = array('state'=>false, 'error'=>'输入的密码不一致');
			}
			else
			{
				if($this->member->force_password($userid,$_POST['password']))
				{
					$result = array('state'=>true, 'data'=>$this->member->get($userid));
				}
				else
				{
					$result = array('state'=>false, 'error'=>$this->member->error());
				}
			}
			echo $this->json->encode($result);
		}
		else
		{
			$this->view->display('index/password');
		}
	}

    /**
     * 删除
     *
     * @aca 删除
     */
	public function delete()
	{
		if(empty($_POST['userid']))
		{
			$result = array('state'=>false, 'error'=>'用户ID为空');
		}
		else
		{
			if($this->member->del($_POST['userid']))
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
     * 备注
     *
     * @aca 备注
     */
	public function remarks()
	{
		if ($this->is_post())
		{
			$where = ' `userid` IN ('.$_POST['userid'].')';
			if ($this->member->remarks($_POST, $where))
			{
				$result = array('state' => true, 'message' => '修改成功');
			}
			else
			{
				$result = array('state' => false, 'error'=>$this->member->error());
			}
			echo $this->json->encode($result);
		}
		else
		{
			$this->view->display('index/remarks');
		}
	}

    /**
     * 查看资料
     *
     * @aca 查看资料
     */
	public function profile()
	{
		$userid = $_GET['userid'] ? $_GET['userid'] : $this->_userid;
		$member = $this->member->getProfile($userid);
		if(!$member)
		{
			$this->showmessage('该用户不存在');
		}
		$member['photo'] = element::member_photo($userid);
		
		$space = loader::model('space','space');	//专栏作者
		$space = $space->get('`userid`='.$userid);
		
		if(!empty($space))
		{
			$this->view->assign('space',$space);
		}
		$priv = table('member_group',$member['groupid']);
		if($member['groupid'] == 1)
		{
			$this->log = loader::model('admin/content_log','system');
			$log = $this->log->ls(array('createdby'=>$userid), 1, 10, true);
			$this->view->assign('log', $log['data']); 
		}
		$head = array('title'=> $member['username'].'_用户资料');
		$this->view->assign('head', $head);
		$this->view->assign('priv', $priv);
		$this->view->assign('member',$member);
		$this->view->display('index/profile');
	}

    /**
     * 修改头像
     *
     * @aca 修改头像
     */
	function avatar()
	{
		$userid = intval($_GET['userid']);
		if($this->is_post())
		{
			import('helper.folder');
			list($photo_path, $rename) = $this->member->set_photo_path($userid);
			$photo = $_POST['photo'];
			if($photo != $photo_path.'/'.$rename.'.jpg')
			{
				$old_path = UPLOAD_PATH.'avatar/'.$photo;
				$thumb_path = UPLOAD_PATH.'avatar/thumb_'.$photo;
				$new_path = UPLOAD_PATH.'avatar/'.$photo_path.'/'.$rename.'.jpg';
				$d = folder::create(UPLOAD_PATH.'avatar'.DS.$photo_path);
				$r = copy($old_path,$new_path);
				if($r)
				{
					@unlink($old_path);
					if(file_exists($thumb_path)) @unlink($thumb_path);
					$old_thumbs = glob(UPLOAD_PATH.'avatar/'.$photo_path.'/*_'.$rename.'.jpg');
					if(!empty($old_thumbs))
					{
						foreach($old_thumbs as $v)
						{
							@unlink($v);
						}
					}
					$this->member->set_field('avatar','1',$userid);
					$result = array('state'=>true, 'message'=>'上传成功');
				}
				else
				{
					$result = array('state'=>false, 'error'=>'上传失败');
				}
			}
			else
			{
				$result = array('state'=>true, 'message'=>'修改成功');
			}
			echo $this->json->encode($result);
		}
		else
		{
			list($path,$filename) = $this->member->set_photo_path($userid);
			$photo = 'avatar/'.$path .'/'.$filename.'.jpg';
			if(file_exists(UPLOAD_PATH.$photo))
				$photo = $path .'/'.$filename.'.jpg';
			else 
				$photo = '';
			$this->view->assign('photo', $photo);
			$this->view->display('index/avatar');
		}
	}

    /**
     * 投稿
     *
     * @aca 投稿
     */
	public function contribute()
	{
		$this->content = loader::model('admin/content','system');
		$statuss = table('status');
		$page = isset($_GET['page']) ? intval($_GET['page']) : 0;
		$pagesize = isset($_GET['pagesize']) ? intval($_GET['pagesize']) : $this->pagesize;
		$where = $_GET;
		$fields = '*';
		$order = isset($_GET['orderby']) ? str_replace('|', ' ', $_GET['orderby']) : '`created` DESC';

		$data = $this->content->ls($where, $fields, $order, $page, $pagesize, true);
		foreach($data as $k =>$v)
		{
			$data[$k]['status'] = $statuss[$v['status']]['name'];
		}
		
		$result = array('total'=>$this->content->total, 'data'=>$data);
		echo $this->json->encode($result);
	}

    /**
     * 发送邮件
     *
     * @aca 发送邮件
     */
	public function sendmail()
	{
		$userid = intval($_GET['userid']);
		$r = $this->member->get($userid);
		if ($this->is_post())
		{
			$mailset = setting('system','mail');
			
			$to = $r['email'];
			$subject = $_POST['subject'];
			$message = $_POST['message'];
			$from = (empty($_POST['from']))?$mailset['from']:$_POST['from'];

			//发送邮件
			if(! send_email($to, $subject, $message, $from))
			{
				$return = array('state'=>false, 'error'=>'发送邮件失败 请重试');
			}
			else
			{
				$return = array('state'=>true, 'info'=>'发送邮件成功');
			}
			echo $this->json->encode($return);
		}
		else
		{
			$self = $this->member->get($this->_userid);
			
			$this->view->assign($userid);
			$this->view->assign('member',$r);
			$this->view->assign('from',$self['email']);
			$this->view->display('index/sendmail');
		}
	}

    /**
     * 查看锁定用户
     *
     * @aca 查看锁定用户
     */
	public function show_unlock()
	{
		$head = array('title'=>'用户解锁');	
		$this->view->assign('head', $head);
		$this->view->display('index/unlock');
	}

    /**
     * 锁定用户列表
     *
     * @aca 查看锁定用户
     */
	public function locked_page()
	{
		$where = array();
		$fields = '*';
		
		if (isset($_GET['keywords']) && $_GET['keywords']) $where[] = where_keywords('username', $_GET['keywords']);
		$where[] = '`locked` > 0';
		
		$page = max((isset($_GET['page']) ? intval($_GET['page']) : 1), 1);
		$size = max((isset($_GET['pagesize']) ? intval($_GET['pagesize']) : $this->pagesize), 1);
		
		if ($where) $where = implode(' AND ', $where);
		$data = $this->member->page($where, $fields, $order, $page, $size);
		$total = $this->member->count($where);
		
		$result = array('data'=>$data, 'total'=>$total);
		echo $this->json->encode($result);
	}

    /**
     * 解锁用户
     *
     * @aca 解锁用户
     */
	public function unlock()
	{
		if(empty($_POST['userid']))
		{
			$result = array('state'=>false, 'error'=>'用户ID为空');
		}
		else
		{
			if($this->member->unlock($_POST['userid']))
			{
				$result = array('state'=>true, 'message'=>'解锁成功');
			}
			else
			{
				$result = array('state'=>false, 'error'=>'发生错误');
			}
		}
		echo $this->json->encode($result);
	}
}