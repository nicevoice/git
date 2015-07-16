<?php
class plugin_xweibo
{
	private $member = null, $xweibo = null;
	
	public function __construct($member)
	{
		$this->member = $member;
		$this->xweibo = loader::lib('xauthCookie_account', 'system');
	}
	public function after_register()
	{
		$data = $this->member->data;
		if ($data['userid'])
		{
			$this->_login($data['userid'], $data['username']);
		}
	}
	public function after_login()
	{
		$m = $this->member->m;
		$this->_login($m['userid'], $m['username']);
	}
	private function _login($userid, $username)
	{
		$this->xweibo->_setLocalToken(array(
			'uid'=>$userid,
			'uname'=>$username
		));
	}
	public function after_logout()
	{
		$this->xweibo->_setLocalToken(null);
	}
}