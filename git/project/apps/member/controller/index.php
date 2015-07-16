<?php
class controller_index extends member_controller_abstract
{
	private $member;

	function __construct(&$app)
	{
		parent::__construct($app);

		$session = & factory::session();
		$session->start();

		$this->member = loader::model('member_front');
	}

	function index()
	{
		$this->login();
	}

	function login()
	{
		if ($this->_userid)
		{
			$this->redirect(url('space/panel/index'));
		}
		$referer = (!empty($_REQUEST['url']))?$_REQUEST['url']:request::get_referer(); //没有urlencode
		if(strpos($referer, 'action=logout') > 0)
		{
			$referer = '';
		}

		if ($this->is_post())
		{
			if(!$this->_check_login())
			{
				$this->showmessage($this->error(),url('member/index/login'),3000, false);
			}
			$cookietime = empty($_POST['cookietime']) ? 86400 : $_POST['cookietime'];
			$member = $this->member->login($_POST['username'], $_POST['password'],$cookietime);
			if (!$member)
			{
				$_SESSION['needseccode'] = 1;
				$redirect=$_POST['x_url']?APP_URL.(url('member/index/login').'&referer='.urlencode($_POST['x_url'])):url('space/panel/index');
				$this->showmessage($this->member->error(),$redirect,3000, false);
//				$this->showmessage($this->member->error(),url('member/index/login'),3000, false);
			}
			$redirect=$_POST['x_url']?$_POST['x_url']:url('space/panel/index');
			$this->showmessage('登录成功'.$member['ucsynlogin'], $redirect, 3000, true);
		}
		else
		{
			$cookie = & factory::cookie();
			$rememberusername = $cookie->get('rememberusername');
			if($_SESSION['needseccode'])
			{
				$this->template->assign('needseccode', true);
			}
			else
			{
				$this->template->assign('needseccode', false);
			}
			
			// 调用otherlogin方法
			$this->otherlogin('get_url');
			// 此属性在otherlogin插件中被赋予
			$olurl = $this->member->url;
			$this->template->assign('sinaurl', $olurl['sina']); 
			$this->template->assign('oauth_token', $_SESSION['sina']['keys']['oauth_token']);

			$this->template->assign('refer', $referer);
			$this->template->assign('rememberusername', $rememberusername);
			$this->template->display('member/login.html');
		}
	}

	public function otherlogin($arg)
	{	
		$arg = isset($_GET['arg']) ? $_GET['arg'] : $arg;
		if($arg == 'member')
		{
			$this->member->otherlogin('def_go');
		}
		else
		{
			$this->member->otherlogin($arg);
		}
		
	}

	function logout()
	{
		$refer = URL('member/index/login');
//		$refer = request::get_referer();
		$ucsynlogout = $this->member->logout();

		$url = isset($_GET['url']) ? $_GET['url'] : $refer;
		$this->showmessage('退出成功'.$ucsynlogout, $url, 3000,true);
	}

	function register()
	{

		if ($this->_userid)
		{
			$this->redirect(url('member/panel/index'));
		}
		if(!$this->setting['allowreg'])
		{
            $close_reason = trim($this->setting['closereason']);
			$this->showmessage($close_reason ? $close_reason : '已关闭注册!');
		}
		if($this->is_post())
		{
			$_SESSION['login_info'] = $_POST;
			if(!$this->_check_register())
			{
				$this->showmessage($this->error, request::get_referer(), 1000, false);
			}
			else
			{
				if($this->member->register($_POST) === false)
				{
					if($_POST['x_url'])
					{
						$redirect=APP_URL.(url('member/index/login').'&referer='.urlencode($_POST['x_url']));
						$this->showmessage($this->member->error(), $redirect, 1000, false);
					}
					else
					{
						$this->showmessage($this->member->error(), request::get_referer(), 1000, false);
					}
					//$this->showmessage($this->member->error(), request::get_referer(), 1000, false);
				}
				else
				{
					unset($_SESSION['login_info']);
					/**
					 *   判断是否有xweibo的跳转参数referer进行跳转
					 *   $_POST['x_url'],通过模版隐藏的<input />$_GET获取
					 *   如果是通过xweibo跳转注册，注册完后返回xweibo，并实现与xweibo同步登陆状态
					 *   如果是cmstop注册，注册完成跳转至用户面板，并实现与xweibo同步登陆状态
					 */
					if($_POST['x_url']) {
						$this->member->login($_POST['username'], $_POST['password'], 3600);
						$this->showmessage('注册成功,请返回登录',$_POST['x_url'],3000,true);
					} else {
						$this->showmessage('注册成功,请返回登录',url('space/panel/index'),3000,true);
					}
				}
			}
		}
		else
		{
			// 调用model中的otherlogin方法
			$this->otherlogin('get_url');
			// 此属性在otherlogin插件中被赋予
			$olurl = $this->member->url;
			$this->template->assign('sinaurl', $olurl['sina']); 
			$this->template->assign('oauth_token', $_SESSION['sina']['keys']['oauth_token']);
			
			$this->template->assign('login_info',$_SESSION['login_info']);
			$this->template->display('member/register.html');
		}
	}



	function getpassword()
	{
		if($this->_userid)
		{
			$this->redirect(url('member/panel/index'));
		}

		if($this->is_post())
		{
			if(!$this->_check_getpassword())
			{
				echo $this->json->encode(array('state' => false, 'error' => $this->error));exit;
			}
			$check = $this->member->check_matchs($_POST['username'],$_POST['email']);
			$authstr = $this->member->make_authstr($check['userid']);
			$timestamp = TIME;
			$authkey = config('config', 'authkey');
			$key = md5($timestamp.$authstr.$authkey);
			$check['url'] = APP_URL;
			$check['url'] .= url('member/index/resetpassword',"userid={$check['userid']}&authstr={$authstr}&timestamp={$timestamp}&key={$key}");

			$set = setting('system');
			$mailset = $set['mail'];
			$to = $check['email'];
			$subject = '重置您在'.$set['sitename'].'的密码信息';
			$set['date'] = date("Y-m-d H:i:s",TIME);

			$this->template->assign('check',$check);
			$this->template->assign('set',$set);
			$message = $this->template->fetch('member/sendmail.html');
			$from = $mailset['from'];

			if(! send_email($to, $subject, $message, $from))
			{
				$return = array('state'=>false, 'error'=>'发送邮件失败 请重试');
				echo $this->json->encode($return);
			}
			else
			{
				$cookie = factory::cookie();
				$cookie->set('getpassword', 1, 172800);
				$return = array('state'=>true, 'message'=>'发送邮件成功','redirect' =>url('member/index/login'));
				echo $this->json->encode($return);
			}
		}
		else
		{
			$this->template->display('member/getpassword.html');
		}
	}

	function resetpassword()
	{
		$timestamp = $_GET['timestamp'];
		$key = $_GET['key'];
		$authstr = $_GET['authstr'];
		$userid = intval($_GET['userid']);

		$authkey = config('config', 'authkey');
		if ($key != md5($timestamp.$authstr.$authkey))
		{
			$this->showmessage('无效链接',WWW_URL,3000, false);
		}
		elseif (TIME-$timestamp>3600*24*2) //2天。
		{
			$this->showmessage('链接已经过期',WWW_URL,3000, false);
		}

		$r = $this->member->getProfile($userid);
		if (!$r['authstr'] || $r['authstr'] != $authstr)
		{
			$this->showmessage('无效链接',WWW_URL,3000, false);
		}
		else
		{
			if ($this->is_post())
			{
				if($_POST['password'] != $_POST['password_check'])
				{
					$result = array('state'=>false, 'error'=>'两次输入的密码不一致');
				}
				else
				{
					if ($this->member->password($userid, $_POST['password'],$r['password'],true))
					{
						$this->member->update_detail(array('authstr'=>''),array('userid' =>$userid));
						$result = array('state'=>true, 'message'=>'密码修改成功');
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
				$this->template->assign('url',url('member/index/resetpassword','userid='.$userid.'&authstr='.$authstr.'&timestamp='.$timestamp.'&key='.$key));
				$this->template->display('member/resetpassword.html');
			}
		}
	}

	function validate()
	{
		$return = array();
		switch($_GET['do'])
		{
			case 'email':
				$return = $this->member->check_email($_GET['email'])
				? array('state' => true, 'info' => 'E-mail不存在')
				: array('state' => false, 'error' => $this->member->error());
				break;
			case 'username':
				$_GET['username'] = urldecode($_GET['username']);
				$return = $this->member->check_username($_GET['username'])
				? array('state' => true, 'info' => '用户名不存在')
				: array('state' => false, 'error' => $this->member->error());
				break;
			case 'seccode':
				import('helper.seccode');
				$seccode = new seccode();
				$return = $seccode->valid()
				? array('state' => true, 'info' => '正确')
				: array('state' => false, 'error' => '验证码不正确');
				break;
			default:
				$return = array('state' => false, 'error' => '未定义操作');
		}
		$data = $this->json->encode($return);
		$data = (isset($_GET['jsoncallback']))? $_GET['jsoncallback']."($data);": $data;
		echo $data;
	}

    function loginform()
    {
        $cookie = & factory::cookie();
        $rememberusername = $cookie->get('rememberusername');
        if($_SESSION['needseccode'])
        {
            $this->template->assign('needseccode', true);
        }
        else
        {
            $this->template->assign('needseccode', false);
        }

        // 调用otherlogin方法
        $this->otherlogin('get_url');
        // 此属性在otherlogin插件中被赋予
        $olurl = $this->member->url;
        $this->template->assign('sinaurl', $olurl['sina']);
        $this->template->assign('oauth_token', $_SESSION['sina']['keys']['oauth_token']);

        $this->template->assign('refer', remove_xss(request::get_referer()));
        $this->template->assign('rememberusername', $rememberusername);

        $html = json_encode($this->template->fetch('member/loginform.html'));
        echo (isset($_GET['jsoncallback']))? $_GET['jsoncallback']."($html);": $html;
    }
    
	function ajaxlogin()
	{
		if($this->_userid)
		{
			$return = array('state'=>true, 'userid' =>$this->_userid, 'username' =>$this->_username, 'message'=>'您已经登录了');
			$data = $this->json->encode($return);
			$data = (isset($_GET['jsoncallback']))? $_GET['jsoncallback']."($data);": $data;
			exit($data);
		}

		if(!isset($_POST['username'])) $_POST['username'] = $_GET['username'];
		if(!isset($_POST['password'])) $_POST['password'] = $_GET['password'];
		$username = $_POST['username'];
		$password = $_POST['password'];

		if(!$this->_check_login())
		{
            $_SESSION['needseccode'] = 1;
			$return = array('state'=>false, 'error'=>$this->error);
			$data = $this->json->encode($return);
			$data = (isset($_GET['jsoncallback']))? $_GET['jsoncallback']."($data);": $data;
			exit($data);
		}

		$cookietime = value($_POST, 'cookietime', 0);

        if ($m = $this->member->login($username, $password, $cookietime)) {
            $return = array(
                'state' => true,
                'userid'=>$m['userid'],
                'username' =>$m['username'],
                'message'=>'登录成功'.$m['ucsynlogin'],
                'rsync' => $m['ucsynlogin']
            );
        } else {
            $_SESSION['needseccode'] = 1;
            $return = array('state' => false, 'error' => $this->member->error());
        }

		$data = $this->json->encode($return);
		$data = (isset($_GET['jsoncallback']))? $_GET['jsoncallback']."($data);": $data;
		echo $data;
	}

	function ajaxlogout()
	{
		if(!$this->_userid)
		{
			$return = array('state'=>false, 'error'=>'您还没有登录');
		}
		else
		{
			$ucsynlogout = $this->member->logout();
			$return = array(
                'state'=>true,
                'message'=>'退出成功'.$ucsynlogout,
                'rsync' => $ucsynlogout
            );
		}
		$data = $this->json->encode($return);
		$data = (isset($_GET['jsoncallback']))? $_GET['jsoncallback']."($data);": $data;
		echo $data;
	}

	function ajaxIsLogin()
	{
		$return = isset($this->_userid) ? array('state'=>TRUE) : array('state'=>FALSE);
		$data = $this->json->encode($return);
		$data = (isset($_GET['jsoncallback']))? $_GET['jsoncallback']."($data);": $data;
		echo $data;
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

	private function _check_register()
	{
		if(!$this->setting['allowreg'])
		{
            $close_reason = trim($this->setting['closereason']);
			$this->error = $close_reason ? $close_reason : '已关闭注册';
			return false;
		}

		import('helper.seccode');
		$seccode = new seccode();
		if (!$seccode->valid())
		{
			$this->error = '验证码不正确';
			return false;
		}

		if(empty($_POST['password']))
		{
			$this->error = '密码不能为空';
			return false;
		}

		if(empty($_POST['email']))
		{
			$this->error = 'E-mail不能为空';
			return false;
		}

		if($_POST['password'] != $_POST['password_check'])
		{
			$this->error = '密码不一致';
			return false;
		}

		if(!$this->member->check_email($_POST['email']))
		{
			$this->error = $this->member->error();
			return false;
		}

		if(!$this->member->check_username(trim($_POST['username'])))
		{
			$this->error = $this->member->error();
			return false;
		}
		return true;
	}

	private function _check_getpassword()
	{
		if(!isset($_POST['username']) || !isset($_POST['email']))
		{
			$this->error = '用户名和E-mail不能为空';
			return false;
		}

		$check = $this->member->check_matchs($_POST['username'],$_POST['email']);
		if(!$check)
		{
			$this->error = '用户名和E-mail不匹配';
			return false;
		}
		$cookie = &factory::cookie();
		$lock = $cookie->get('getpassword');
		if(!empty($lock))
		{
			$this->error = '48小时只能进行一次此操作';
			return false;
		}
		return true;
	}
}
