<?php
class model_guestbook extends model
{
	public $guestbook_type, $total = 0;

	function __construct()
	{
		parent::__construct();

		$this->_table = $this->db->options['prefix'].'guestbook';
		$this->_primary = 'gid';
		$this->_fields = array('gid','typeid', 'title', 'content', 'userid', 'username', 'gender', 'email','qq','msn','telephone','address','homepage','isview','ip','addtime','reply','replyer','replytime');
		
		$this->_readonly = array('gid');
		$this->_create_autofill = array('userid'=>$this->_userid, 'username'=>$this->_username, 'addtime'=>TIME, 'ip'=>IP);
		$this->_update_autofill = array();
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array();
		$this->guestbook_type = loader::model('guestbook_type','guestbook');
	}

	function ls($where, $fields, $order, $page, $size)
	{
		$data = $this->page($where, $fields, $order, $page, $size);
		foreach($data as $key=>$value)
		{
			$data[$key]['replytime'] = time_format($value['replytime'],'Y年n月j日 G:i');
			$data[$key]['addtime'] = time_format($value['addtime'],'Y年n月j日 G:i');
			$data[$key]['content'] = str_cut($value['content'],300,'...');
			if($setting['showmanage'] !== '')
			{
				$data[$key]['replyer'] = $setting['showmanage'] = '';
			}
			else
			{
				$data[$key]['replyer'] = empty($value['replyer'])? '' :$value['replyer'];
			}
		}
		return $data;
	}

	function add($data)
	{
		$data = $this->filter_array($data, $this->_fields);
		if($gid = $this->insert($data))
		{
			$this->guestbook_type->set_inc('count',"`typeid`= ".$data['typeid']); 
			return $gid;
		}
		return false;
	}
	
	function count_type($repliedshow)
	{
		$data = table('guestbook_type');
		$where = $whereplus = '';
		$num = 0;
		if($repliedshow) $whereplus = "reply != '' AND ";
		foreach ($data as $key => $value)
		{
			$where = $whereplus.'`typeid`='.$value['typeid'];
			$num = $this->count($where);
			$data[$key]['count'] = intval($num);
			$this->total += $data[$key]['count'];
		}
		return $data;
	}
}