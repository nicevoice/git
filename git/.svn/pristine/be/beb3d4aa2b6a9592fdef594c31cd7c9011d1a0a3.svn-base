<?php
class model_interview extends model
{
	public $content, $catid, $modelid, $contentid, $data, $fields, $order, $action, $category;
	private $observers = array();
	
	function __construct()
	{
        parent::__construct();
		$this->_table = $this->db->options['prefix'].'interview';
		$this->_primary = 'contentid';
		$this->_fields = array('contentid', 'number', 'description', 'address', 'starttime', 'endtime', 'compere', 'mode', 'photo', 'video', 'editor', 'review', 'notice', 'picture', 'allowchat', 'ischeck', 'startchat', 'endchat', 'state');
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
        $this->content = loader::model('admin/content', 'system');
        $this->modelid = modelid('interview');
	}
	
	function get($contentid)
	{
		$r = $this->db->get("SELECT * FROM `#table_content`, `#table_interview` WHERE `#table_content`.`contentid`=`#table_interview`.`contentid` AND `#table_content`.`contentid`=$contentid");
		if ($r)
		{
			$this->content->output($r);
		}
		return $r;
	}
}