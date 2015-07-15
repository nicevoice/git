<?php
class model_admin_chat extends model
{
	function __construct()
	{
		parent::__construct();
		$this->_table = $this->db->options['prefix'].'interview_chat';
		$this->_primary = 'chatid';
		$this->_fields = array('chatid', 'contentid', 'guestid', 'content', 'created', 'createdby', 'ip');
		$this->_readonly = array('chatid');
		$this->_create_autofill = array('created'=>TIME, 'createdby'=>$this->_userid, 'ip'=>IP);
		$this->_update_autofill = array();
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array('content'=>array('not_empty'=>array('内容不能为空'))
		                          );
	}
	
	function add($data)
	{
		if (!is_numeric($data['guestid'])) $data['guestid'] = null;
		$data = $this->filter_array($data, array('contentid', 'guestid', 'content'));
		return $this->insert($data);
	}

	function edit($chatid, $data)
	{
        $data = $this->filter_array($data, array('content'));
		return $this->update($data, $chatid);
	}
	
	function recommend($chatid)
	{
		$data = $this->get($chatid);
		$interview = loader::model('admin/interview');
		$review = $interview->get_field('review',$data['contentid']);
		$review = $review."\n".'<p>'.$data['content'].'</p>';
		return $interview->update(array('review'=>$review),$data['contentid']);
	}
	
	function ls($contentid)
	{
		$contentid = intval($contentid);
		return $this->select("`contentid`=$contentid", '*', '`chatid` ASC');
	}
	
	function ls_limit($contentid,$limit=17,$offset=17)
	{
		$contentid = intval($contentid);
		return $this->select("`contentid`=$contentid", '*', '`chatid` ASC',$limit,$offset);
	}

	function _after_select(& $data, $multiple)
	{
		if (!$data) return ;
		if ($multiple)
		{
			foreach ($data as $k=>$r)
			{
				$r['created'] = date('Y-m-d H:i', $r['created']);
				$data[$k] = $r;
			}
		}
		else 
		{
			$data['created'] = date('Y-m-d H:i', $data['created']);
		}
	}
}