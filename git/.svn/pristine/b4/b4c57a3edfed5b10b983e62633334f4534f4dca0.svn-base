<?php
class model_admin_page extends model implements SplSubject 
{
	private $observers = array();
    public $setting, $html_root, $www_root;
    private $uri;

	function __construct()
	{
		parent::__construct();
		$this->_table = $this->db->options['prefix'].'paper_edition_page';
		$this->_primary = 'pageid';
		$this->_fields = array('pageid', 'url', 'paperid','editionid','pageno','name','image','pdf' ,'editor','arteditor');
		$this->_txtfields = array();
		$this->_readonly = array();
		$this->_create_autofill = array();
		$this->_update_autofill = array();
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array();
        $this->setting = setting::get('paper');
        $this->uri = loader::lib('uri','system');
        $u = $this->uri->psn($this->setting['path']);
        $this->html_root = $u['path'];
        $this->www_root  = $u['url'];
	}
	
	//取得版面相关信息，包括报纸名，期号，图片
	function getPage($id)
	{
		$sql = "SELECT p.*, pa.name AS paperName, e.total_number FROM #table_paper_edition_page p
				LEFT JOIN #table_paper pa ON p.paperid = pa.paperid
				LEFT JOIN #table_paper_edition e ON e.editionid = p.editionid
				WHERE p.pageid = $id";
		$page = $this->db->get($sql);
		$page['title'] = $page['paperName'].'-第'.$page['total_number'].'期-'.$page['name'];
		return $page;
	}
	
	function getPages($id)
	{
		if(!$id) return array();
		$data = $this->select("editionid = $id");
		foreach ($data as &$item) 
		{
			$sql = "SELECT COUNT(*) AS count FROM #table_paper_content WHERE pageid = ".$item['pageid'];
			$rs = $this->db->get($sql);
			$item['count'] = intval($rs['count']);
		}
		return $data;
	}
	
	/**
	 * 前台使用单字段修改模式
	 *
	 * @param int $id	pageid
	 * @param string $k 字段名
	 * @param mixed $v	 值
	 */
	function save($id, $k, $v)
	{
		$sql = "UPDATE #table_paper_edition_page SET `$k` = '$v' WHERE pageid = ".intval($id);
		if(!$this->db->update($sql)) return false;
		if($k == 'name' || $k == 'editor' || $k == 'arteditor') {
			$this->pageid = $id;
		   	$this->event = 'after_save';
		   	$this->notify();
		}
		return true;
	}
	
	
	
	//添加版面
	public function add($eid)
	{
		
		if(!$eid) return -1;
		$e = table('paper_edition', $eid);
		if(!$e['paperid']) return -2;
		$data = array
		(
			'name'	=>	'版面名称',
			'editor'=>	'主编',
			'arteditor'=>'美编'
		);
		$sql = "SELECT max(pageno) AS pageno FROM #table_paper_edition_page WHERE editionid = $eid";
		$rs = $this->db->get($sql);
		$data['paperid'] = $e['paperid'];
		$data['editionid'] = $eid;
		$data['pageno'] = ++$rs['pageno'];
		$id = $this->insert($data);
		
		//版面缓存更新
		$this->pageid = $id;
	   	$this->event = 'after_add';
	   	$this->notify();
		
		$data = $this->getPage($id);
		$data['count'] = 0;
		return $data;
	}
	
	
	//删除版面
	public function delete($pageid)
	{
		$page = $this->get($pageid);
		if(!$page) return '已经删除';
		parent::delete($pageid);
		
		//重排版面号
		$pages = $this->select("editionid = ".$page['editionid'], '*', 'pageno ASC');
		foreach ($pages as $k => $p)
		{
			$p['pageno'] = $k + 1;
			$this->update($p, $p['pageid']);
		}
		
		//版面缓存更新,删除静态页
		$this->paperid = $page['paperid'];
		$this->editionid = $page['editionid'];
		$this->pageid = $page['pageid'];
	   	$this->event = 'after_delete';
	   	$this->notify();
	   	return true;
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
?>