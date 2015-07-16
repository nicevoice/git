<?php
class model_admin_paper extends model implements SplSubject
{
	private $observers = array();
    public $setting, $html_root, $www_root;
    private $uri;

	function __construct()
	{
		parent::__construct();
		$this->_table = $this->db->options['prefix'].'paper';
		$this->_primary = 'paperid';
		$this->_fields = array('paperid', 'name', 'alias', 'url', 'logo', 'pages', 'template_content', 'created', 'createdby', 'ip', 'disabled');
		$this->_readonly = array('paperid','createdby');
		$this->_create_autofill = array('ip'=>IP,'created'=>TIME,'createdby'=>$this->_userid);
		$this->_update_autofill = array('createdby'=>$this->_userid);
		$this->_validators = array(
			'name'=>array(
			    'not_empty'=>array('报纸名称不能为空'),
				'max_length' =>array(40, '报纸名称不得超过40字节')
			),
			'alias'=>array(
				'not_empty'=>array('别名不能为空'),
				'max_length' =>array(40, '报纸名称不得超过40字节'),
				'/^[\w]+$/' =>array(40, '只能有字母数字和下划线'),
			)
		);
        $this->setting = setting::get('paper');
        $this->uri = loader::lib('uri','system');
        $u = $this->uri->psn($this->setting['path']);
        $this->html_root = $u['path'];
        $this->www_root  = $u['url'];
	}

	function save($data)
	{
		if($data['paperid'])
		{
			$id = intval($data['paperid']);
		}
	    $data = $this->filter_array($data, array('name','alias','url','logo','pages','template_content','created','createdby','disabled'));
	   				 
	   	if($id)
	   	{
	   		$rs = $this->update($data, $id);
	   	}
	   	else
	   	{
	   		$id = $this->insert($data);
	   	}
	   	$this->pid = $id;
	   	$this->pages = $data['pages'];
	   	$this->event = 'after_save';
	   	$this->notify();
	   	return $id;
	}
	
	function getPapers()
	{
		$data = parent::select();
		$data = $this->outer($data);
		return $data;
	}
	
	function getPaper($id)
	{
		$data = parent::get($id);
		$data = $this->outer($data);
		return $data;
	}
	
	private function outer($data)
	{
		if(!$data) return $data;
		if(!$data[0])
		{
			$data = array($data);	//转二维
			$m = 1;
		}
		$prev = "SELECT max(total_number) AS total_number FROM #table_paper_edition WHERE paperid = ";
		foreach ($data as &$item)
		{
			$sql = $prev . $item['paperid'];
			$rs = $this->db->get($sql);
			$item['total_number'] = intval($rs['total_number']);
			
			//链接处理
			if(!is_file(str_replace($this->www_root, $this->html_root, $item['url'])))
			{
				$item['url'] = 'javascript:;';
			}
			
			//封面处理
			$item['logo'] = thumb($item['logo'], 140, 140);
			if($item['logo'] == 'images/nopic.gif') 
			{
				$item['logo'] = ADMIN_URL.'images/guest.gif';
			}
			else
			{
				if(substr($item['logo'], 0, 4) != 'http')
				{
					$item['logo'] = UPLOAD_URL.$item['logo'];
				}
				if(!is_file(str_replace(UPLOAD_URL, UPLOAD_PATH, $item['logo'])))
				{
					$item['logo'] = ADMIN_URL.'images/guest.gif';
				}
			}
		}
		if($m) $data = $data[0];
		return $data;
	}
	
	function delete($id)
	{
		$this->id = $id;
		$this->event = 'before_delete';
		$this->notify();
		parent::delete($id);
		$this->event = 'after_delete';
		$this->notify();
	}
	
	public function attach(SplObserver $observer)
	{
		$this->observers[] = $observer;
	}

	public function detach(SplObserver $observer)
	{
		if($index = array_search($observer, $this->observers, true)) unset($this->observers[$index]);
	}

	public function notify()
	{
		foreach ($this->observers as $observer)
		{
			$observer->update($this);
		}
	}
}