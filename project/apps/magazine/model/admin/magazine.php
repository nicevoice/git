<?php
class model_admin_magazine extends model implements SplSubject 
{
	private $observers = array();
    public $setting, $html_root, $www_root;
    private $uri;

	function __construct()
	{
		parent::__construct();
		$this->_table = $this->db->options['prefix'].'magazine';
		$this->_primary = 'mid';
		$this->_fields = array('mid', 'name', 'alias', 'type', 'publish', 'logo', 'url', 'pages', 'template_list', 'template_content', 'created', 'createdby','memo', 'ip', 'disabled');
		$this->_readonly = array('mid');
		$this->_create_autofill = array('ip'=>IP,'created'=>TIME,'createdby'=>$this->_userid);
		$this->_update_autofill = array('createdby'=>$this->_userid);
		$this->_validators = array(
			'name'=>array(
                'not_empty'=>array('杂志名称不能为空。'),
				'max_length' =>array(40, '杂志名称不得超过40字节')
			),
			'alias'=>array(
                'not_empty'=>array('别名不能为空'),
				'max_length' =>array(40, '杂志名称不得超过40字节'),
				'/^[\w]+$/' =>array(40, '只能有字母数字和下划线'),
			),
			'template_list'=>array('not_empty'=>array('列表模板不能为空')),
			'template_content'=>array('not_empty'=>array('内页模板不能为空')),
		);
        $this->setting = setting::get('magazine');
        $this->uri = loader::lib('uri','system');
        $u = $this->uri->psn($this->setting['path']);
        $this->html_root = $u['path'];
        $this->www_root  = $u['url'];
	}

	function save($data)
	{
		if($data['mid'])
		{
			$id = intval($data['mid']);
		}
	    $data = $this->filter_array($data, array('mid','name', 'type', 'publish', 'alias','logo','url','pages','template_list','template_content','memo','created','createdby','disabled'));
	
	   	if($id)
	   	{
	   		$this->update($data, $id);
	   	}
	   	else
	   	{
	   		$id = $this->insert($data);
	   	}
	   	$this->event = 'after_save';
	   	$this->notify();
	   	return $id;
	}
	
	function getMagazines()
	{
		$data = parent::select();
		$data = $this->outFilter($data);
		return $data;
	}
	
	function getMagazine($id)
	{
		$data = parent::get($id);
		$data = $this->outFilter($data);
		return $data;
	}
	
	private function outFilter($data)
	{
		if(!$data) return $data;
		if(!$data[0]) 
		{
			$data = array($data);	//转二维
			$m = 1;
		}
		$prev = "SELECT max(total_number) AS editions FROM #table_magazine_edition WHERE mid = ";
		foreach ($data as &$item)
		{
			$sql = $prev . $item['mid'];
			$rs = $this->db->get($sql);
			$item['editions'] = intval($rs['editions']);
			if(!$m) $item['memo'] = str_cut($item['memo'], 32);
			$item['logo'] = thumb($item['logo'], 150, 150);
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
			$item['url'] = $this->www_root.'/'.$item['alias'].'/'.$item['default_year'].'/';
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