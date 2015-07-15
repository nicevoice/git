<?php

class model_admin_guest extends model 
{	
	function __construct()
	{
        parent::__construct();
		$this->_table = $this->db->options['prefix'].'interview_guest';
		$this->_primary = 'guestid';
		$this->_fields = array('guestid', 'contentid', 'name','color','initial', 'photo', 'resume','url', 'sort');
		$this->_readonly = array('guestid', 'contentid');
		$this->_create_autofill = array();
		$this->_update_autofill = array();
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array('contentid'=>array('not_empty' =>array('内容ID不能为空'),
                                                      'is_numeric' =>array('内容ID必须是数字'),
                                                      'max_length' =>array(8, '内容ID不得超过8个字节'),
                                                     ),
                                   'name'=>array('not_empty' =>array('内容ID不能为空'),
                                                 'max_length' =>array(30, '嘉宾姓名不得超过30个字节'),
                                                     ),
                                  );
	}

	function add($data)
	{
		import('helper.pinyin');
		$data['initial'] = pinyin::initial($data['name'], config('config', 'charset'));
		$data = $this->filter_array($data, array('contentid', 'name', 'color', 'initial', 'photo', 'resume', 'url', 'sort'));
		return $this->insert($data);
	}

	function edit($guestid, $data)
	{
        if (isset($data['name']))
        {
        	import('helper.pinyin');
        	$data['initial'] = pinyin::initial($data['name'], config('config', 'charset'));
        }
		$data = $this->filter_array($data, array('name', 'color', 'initial', 'photo', 'resume', 'url', 'sort'));
		return $this->update($data, $guestid);
	}
	
	function ls($contentid)
	{
		return $this->gets_by('contentid', $contentid, '*', '`sort` ASC, `guestid` ASC');
	}
	
    function delete($guestid)
    {
    	$r = $this->get($guestid);
    	if (!$r) return false;
    	if ($r['aid'])
    	{
    		$attachment = loader::model('admin/attachment', 'system');
    		$attachment->delete($r['aid']);
    	}
    	else 
    	{
    		@unlink(UPLOAD_PATH.$r['photo']);
    	}
    	return parent::delete($guestid);
    }
    
    function delete_by($contentid, $guestid = array())
    {
    	$count = 0;
    	$data = $this->gets_by('contentid', $contentid);
    	foreach ($data as $r)
    	{
    		if (!in_array($r['guestid'], $guestid))
    		{
    			$this->delete($r['guestid']);
    			$count++;
    		}
    	}
    	return $count;
    }
    
    function rm($contentid)
    {
    	$count = 0;
    	$data = $this->gets_by('contentid', $contentid);
    	foreach ($data as $r)
    	{
    		if($this->delete($r['guestid'])) $count++;
    	}
    	return $count;
    }
}