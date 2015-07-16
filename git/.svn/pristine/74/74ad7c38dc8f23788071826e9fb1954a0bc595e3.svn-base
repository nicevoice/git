<?php
class plugin_otherlogin extends controller
{
	private $member = null, $sina = null;
	protected $template, $member_bind;
	
	public function __construct($member)
	{
		parent::__construct($member);
		$this->member = $member;
		$this->member_bind = new member_bind;
		$this->template = factory::template();
		$this->sina = loader::lib('sina', 'system');
	}

	public function def_go()
	{
		if (empty($_SESSION['sina']['access_token']['oauth_token']))
		{
			$this->sina->checkUser();
			$this->redirect(url('member/index/otherlogin'));
		}
		else
		{
			$bind_status = $this->member_bind->bind_status();
			if($bind_status['access_token'])
			{
				$existuser = $this->_getExistUser($bind_status['userid']);
				$this->member->login($existuser['username'], $existuser['password'], 3600);
				$this->redirect(url('member/panel/index'));
			}
			
			if($this->is_post('member_registerForm'))
			{
				$data = $this->_getData($_POST);
				if($data['sina']['userid'])
				{
					$okbind = $this->member_bind->bind($data['sina']);
					if($okbind)
					{
						$this->member->login($_POST['username'], $_POST['password'], 3600);
						$this->redirect(url('member/panel/index'));
					}
				}
			}
			$info = $this->_getInfo();
			$this->template->assign('uname', $info['sina']['uname']);
			$this->template->display('member/otherlogin.html');
		}
		
	}

	public function get_url()
	{
		$sinaurl = $this->sina->getUrl();
		$olurl[sina] = $sinaurl;
		$this->member->url = $olurl;
	}
	
	public function bindPage()
	{
		$bind_status = $this->member_bind->bind_status();
		$this->template->assign('status', $bind_status);
		$this->template->display('member/panel/bind.html');
	}
	
	public function statusBind()
	{
		if ($this->is_post())
		{
			if(!$this->_check_login())
			{
				$this->showmessage($this->error(), request::get_referer(), 1000, false);
			}
			
			if($this->member->check_username($_POST['username']))
            {
               $this->showmessage('该用户不存在', request::get_referer(), 1000, false);
            }
			
			$m = $this->member->get_by('username', $_POST['username'], 'userid');
			$data = $this->_getData();
			$data['sina']['userid'] = $m['userid'];
			
			$bind_status = $this->member_bind->bind_status($data['sina']['userid']);

			if($bind_status)
			{
				$this->showmessage('此用户已进行过绑定',request::get_referer(), 1000);
			}
            else
            {
//            	$this->redirect(URL('member/index/otherlogin', 'statusBind'));
            	$okbind = $this->member_bind->bind($data['sina']);
                if($okbind)
                {
                	$b=$this->member->login($_POST['username'], $_POST['password'], 3600);

                	if($b) $this->showmessage('绑定成功',url('member/panel/index'), 1000, true);
                	/* 这里预留了一个问题，万一账户已经绑定过了怎么办？时间紧先搁置下 */
                }
            }
		}
	}
	
	public function rebind()
	{
		$this->redirect($this->sina->getUrl(APP_URL . '?app=member&controller=index&action=otherlogin&arg=lastbind'));
	}
	
	public function lastbind()
	{
		if (empty($_SESSION['sina']['access_token']['oauth_token']))
		{
			$this->sina->checkUser();
			$this->redirect(url('member/index/otherlogin', 'arg=lastbind'));
		}
		$data = $this->_getData();
		$r = $this->member_bind->bind($data['sina']);
		if($r) {
			$this->redirect(url('member/index/otherlogin', 'arg=bindPage'));
		}
	}
	
	public function unbind()
	{
		$this->member_bind->unbind();
		$this->redirect(url('member/index/otherlogin', 'arg=bindPage'));
	}

	public function del_bind()
	{
		$this->member_bind->del_bind();
	}

    /**
     * 微薄同步，暂从产品中注释
     *
     * @TODO
     */
	public function after_logout()
	{
		//$this->member_bind->xsyn('logout');
	}

	private function _register($data)
	{
		$id = $this->member->register($data);
		return $id;
	}
	
	private function _getExistUser($id)
	{
		$existuser = $this->member->get_by('userid', $id, 'username, password');
		return $existuser;
	}
	
	private function _getInfo()
	{
		$info['sina'] = $this->sina->userInfo();
		return $info;
	}
	
	private function _getData($data = null)
	{
		$info = $this->_getInfo();
		$id = $this->member->check_username($data['username']) ? $this->_register($data) : $this->member->_userid;
		$datas['sina'] = array(
							'userid' => $id,
							'bindid' => 'sina'.$info['sina']['id'],
							'username' => $info['sina']['uname'],
							'access_token' => $_SESSION['sina']['access_token']['oauth_token'],
							'token_secret' => $_SESSION['sina']['access_token']['oauth_token_secret']
						);
		return $datas;
	}
	
	private function _check_login()
	{
		if($_SESSION['needseccode'])
		{
			import('helper.seccode');
			$seccode = new seccode();
			if (!$seccode->valid())
			{
				$this->error = '验证码不正确';
				return false;
			}
		}
		if(empty($_POST['username']))
		{
			$this->error = '用户名不能为空';
			return false;
		}
		if(empty($_POST['password']))
		{
			$this->error = '密码不能为空';
			return false;
		}
		return true;
	}
}

class member_bind extends model
{
	function __construct()
	{
		parent::__construct();
		$this->_table = $this->db->options['prefix'].'member_bind';
		$this->_primary = 'userid';
		$this->_fields = array('userid', 'bindid', 'access_token', 'token_secret');
		$this->_readonly = array('userid');
		$this->_create_autofill = array('bindid'=>'');
		$this->_update_autofill = array();
		$this->_filters_input = array();
		$this->_filters_output = array();
	}
	
	public function bind_status($value= null)
	{
		if($value)
		{
			$status = $this->get_by('userid', $value, 'userid, bindid, access_token, token_secret');
			return $status;
		}
		if(empty($_SESSION['sina']['access_token']['oauth_token']))
		{
			$field = 'userid';
			$value = $this->_userid;
		}
		else
		{
			$field = 'access_token';
			$value = $_SESSION['sina']['access_token']['oauth_token'];
		}
		$status = $this->get_by($field, $value, 'userid, bindid, access_token, token_secret');
		return $status;
	}
	
	public function bind($data)
	{
		$r = $this->insert($data);

		if($r)
		{
			$data['bindid'] = $_SESSION['sina']['access_token']['user_id'];
			$this->xsyn('bind', $data);
            return true;
		}
		
	}
	
	public function unbind()
	{
		$id = $this->_userid;
		$data = array(
					'userid' => $id,
					'bindid' => $_SESSION['sina']['access_token']['user_id']
		);
		$this->delete($id);
		$this->xsyn('unbind', $data);
	}

	public function del_bind()
	{
		$id = $this->_userid;
		$this->delete($id);
	}

	public function xsyn($m, $v=null)
	{
		$post_fields='syn='.$m.'&syn_id='.$v['bindid'].'&syn_name='.$v['username'].
						'&syn_uid='.$v['userid'].'&syn_oauth_token='.$v['access_token'].
						'&syn_oauth_token_secret='.$v['token_secret'];
		$url = 'http://t.cmstop.loc/index.php?m=account.ctsyn';

		request($url, $post_fields);
	}
}