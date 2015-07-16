<?php
class model_admin_picture_group extends model
{	
	function __construct()
	{
        parent::__construct();
		$this->_table = $this->db->options['prefix'].'picture_group';
		$this->_primary = 'pictureid';
		$this->_fields = array('pictureid', 'contentid', 'aid', 'image', 'note', 'url', 'sort');
		$this->_readonly = array('contentid');
		$this->_create_autofill = array();
		$this->_update_autofill = array();
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array('contentid'=>array('not_empty' =>array('内容ID不能为空'),
                                                      'is_numeric' =>array('内容ID必须是数字'),
                                                      'max_length' =>array(8, '内容ID不得超过8个字节'),
                                                     )
                                  );
	}
    
    function add($data)
    {
    	return parent::insert($data);
    }
    
    function edit($pictureid, $data)
    {
    	return parent::update($data, $pictureid);
    }
    
    function delete($pictureid)
    {
    	$r = $this->get($pictureid);
    	if (!$r) return false;
    	if ($r['aid'])
    	{
    		$attachment = loader::model('admin/attachment', 'system');
    		$attachment->delete($r['aid']);
    	}
    	else 
    	{
    		@unlink(UPLOAD_PATH.$r['image']);
    	}
    	return parent::delete($pictureid);
    }
    
    function delete_by($contentid, $pictureid = array())
    {
    	$count = 0;
    	$data = $this->gets_by('contentid', $contentid);
    	foreach ($data as $r)
    	{
    		if (!in_array($r['pictureid'], $pictureid))
    		{
    			$this->delete($r['pictureid']);
    			$count++;
    		}
    	}
    	return $count;
    }
    
    function ls($contentid)
    {
    	return $this->gets_by('contentid', $contentid, '*', '`sort` ASC, `pictureid` ASC');
    }
    
    function _after_select(& $data, $multiple = false)
    {
    	if (!$data) return ;
		if ($multiple)
		{
			array_map(array($this, 'output'), $data);
		}
		else 
		{
			$this->output($data);
		}
    }
    
    public function output(& $r)
	{
		$r['note'] = htmlspecialchars($r['note']);
	}
    
    function rm($contentid)
    {
    	$count = 0;
    	$data = $this->gets_by('contentid', $contentid);
    	foreach ($data as $r)
    	{
    		if($this->delete($r['pictureid'])) $count++;
    	}
    	return $count;
    }
}