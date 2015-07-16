<?php
class model_admin_content extends model implements SplSubject 
{
	private $observers = array();
    public $setting, $html_root, $www_root;
    private $uri;

	function __construct()
	{
		parent::__construct();
		$this->_table = $this->db->options['prefix'].'paper_content';
		$this->_primary = 'mapid';
		$this->_fields = array('mapid', 'paperid','editionid','pageid', 'pageno', 'contentid', 'coords','sort');
		$this->_txtfields = array();
		$this->_readonly = array('mapid');
		
		$this->html = loader::model('admin/html','paper');

        $this->setting = setting::get('paper');
        $this->uri = loader::lib('uri','system');
        $u = $this->uri->psn($this->setting['path']);
        $this->html_root = $u['path'];
        $this->www_root  = $u['url'];
	}
	
	/**
	 * 根据条件数组（GET）搜索文章
	 */
	function search($get)
	{
		$where[] = "c.modelid = 1";
		$where[] = "c.status = 6";
		!empty($get['catid']) && $where[] = 'c.catid = '.intval($get['catid']);
		if($get['startDate'] || $get['endDate'])
		{
			$get['startDate'] && $where[] = "published >= ".strtotime($get['startDate']);
			$get['endDate'] && $where[] = "published < ".(strtotime($get['endDate']) + 86400);
		}
		$where = implode(' AND ', $where);
		if (isset($_POST['keywords']) && $_POST['keywords'])
		{
			$where .= " AND title LIKE '%$_POST[keywords]%'";
		}
		if (isset($_POST['catid']) && $_POST['catid'])
		{
			$where .= ' AND catid='.$_POST['catid'];
		}
		$size = $get['pagesize'] ? intval($get['pagesize']) : 15;
		$page = $get['page'] ? intval($get['page']) : 1;
		$start = ($page - 1) * $size;
		$sql = "SELECT SQL_CALC_FOUND_ROWS c.contentid, c.catid,c.title,c.published,m.username FROM #table_content c
				LEFT JOIN #table_member m ON c.publishedby = m.userid
				WHERE $where ORDER BY c.published DESC LIMIT $start, $size";
		$data = $this->db->select($sql);
		foreach ($data as &$item)
		{
			$item['published'] = date('Y-m-d', $item['published']);
		}
		$total = $this->db->get('SELECT found_rows()');
		$total = $total['found_rows()'];
		return array('state' => true, 'total' => $total, 'data' => $data);
	}
	/**
	 * 读取热点
	 *
	 * @param int $pageid
	 */
	function getCoords($pageid)
	{
		if(!$pageid) return array();
		$sql = "SELECT pc.mapid, pc.coords, pc.sort, pc.contentid, c.title FROM #table_paper_content pc
				LEFT JOIN #table_content c ON pc.contentid = c.contentid
				WHERE pc.pageid = $pageid";
		$coords = $this->db->select($sql);
		foreach ($coords as & $item)
		{
			$temp = explode(',', $item['coords']);
			$item['x1'] = $temp[0];
			$item['y1'] = $temp[1];
			$item['x2'] = $temp[2];
			$item['y2'] = $temp[3];
		}
		return $coords;
	}

	function saveMap($data)
	{
		if($data['mapid'])
		{
			$mapid = $data['mapid'];
			$this->update($data, $mapid);
		}
		else
		{
			$mapid = $this->insert($data);
		}
		$this->mapid = $mapid;
		$this->event = 'after_save';
	   	$this->notify();
		return $mapid;
	}
	
	function delMap($id)
	{
		$eid = table('paper_content', $mapid, 'editionid');
		$this->eid = $eid;
		$this->delete($id);
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