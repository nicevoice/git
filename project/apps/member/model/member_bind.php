<?php
class model_member_bind extends model
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
		//		$this->_validators = array('username'=>array('not_empty'=>array('用户不能为空')),
		//									'email'=>array('not_empty'=>array('E-mail不能为空'),
		//													'email' =>array('E-mail格式不正确')
		//		)
		//		);
	}

	public function check_bid($uid)
	{
		if($this->exists('bindid', $uid))
		{
			$id = $this->get("bindid='$uid'", 'userid');
			return $id = $id['userid'];
		}

	}

	public function bind_state($id)
	{
		if($this->exists('userid', $id))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	public function bind($data)
	{
		$synon = TRUE;
		if($this->insert($data) && $synon)
		{
			$data['bindid'] = $_SESSION['sina']['access_token']['user_id'];
			$this->xsyn('bind', $data);
            return TRUE;
		}
	}

	public function unbind($data)
	{
		$this->delete($data['id']);
		$this->xsyn('unbind', $data);
	}

	/**
	 * 同步到X微博进行解/绑定的数据
	 * 通过curl向xweibo发送数据
	 * @param array $v 需要发送的数据
	 * @param mixed $m 在Xweibo执行的方法
	 */
	public function xsyn($m, $v=null)
	{

		$post_fields='syn='.$m.'&syn_id='.$v['bindid'].'&syn_name='.$v['username'].
						'&syn_uid='.$v['userid'].'&syn_oauth_token='.$v['access_token'].
						'&syn_oauth_token_secret='.$v['token_secret'];

		$url = 'http://t.cmstop.loc/index.php?m=account.ctsyn';
		$ch = curl_init($url);

		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
		curl_exec($ch);
		curl_close($ch);
	}
}
