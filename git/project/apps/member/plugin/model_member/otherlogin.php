<?php
class plugin_otherlogin extends controller
{
	private $member = null, $sina = null;
	protected $template, $member_bind;
	
	public function __construct(& $member)
	{
		parent::__construct($member);
		$this->member = $member;
		$this->member_bind = new member_bind;
		$this->template = factory::template();
		$this->sina = loader::lib('sina', 'system');
	}

	public function del_bind()
	{
		$this->member_bind->del_bind($this->member->userid);
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
	
	public function del_bind($id)
	{
//        $bindid = !empty($_SESSION['sina']['access_token']['user_id']) ? $_SESSION['sina']['access_token']['user_id'] : $this->get("userid=$id", 'bindid');
        $bindid = $this->get("userid=$id", 'bindid');
		$bindid = substr($bindid['bindid'], 4);
        $data = array(
            'userid' => $id,
            'bindid' => $bindid
        );

        if($this->delete($id))
        {
            $this->xsyn('unbind', $data);
        }
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
