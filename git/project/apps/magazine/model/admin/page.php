<?php
class model_admin_page extends model implements SplSubject 
{
	private $observers = array();
    public $setting, $html_root, $www_root;
    private $uri;

	function __construct()
	{
		parent::__construct();
		$this->_table = $this->db->options['prefix'].'magazine_page';
		$this->_primary = 'pid';
		$this->_fields = array('pid', 'mid','eid','pageno','name' ,'editor','arteditor');
		$this->_txtfields = array();
		$this->_readonly = array();
		$this->_create_autofill = array();
		$this->_update_autofill = array();
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array();
        $this->setting = setting::get('magazine');
        $this->uri = loader::lib('uri','system');
        $u = $this->uri->psn($this->setting['path']);
        $this->html_root = $u['path'];
        $this->www_root  = $u['url'];
	}
	
	//取得版面相关信息，包括杂志名，期号，图片
	function getPage($id)
	{
		$sql = "SELECT p.pid, p.eid,p.url, p.image, p.name, pa.name AS magazineName, e.number FROM #table_magazine_page p
				LEFT JOIN #table_magazine pa ON p.mid = pa.mid
				LEFT JOIN #table_magazine_edition e ON e.eid = p.eid
				WHERE p.pid = $id";
		$page = $this->db->get($sql);
		$page['title'] = $page['magazineName'].'-第'.$page['number'].'期-'.$page['name'];
		return $page;
	}
	
	function getPages($eid,  $orderby = 'p.pageno ASC')
	{
		if(!$eid) return array();
		$sql = "SELECT p.*,COUNT(c.mapid) AS count FROM #table_magazine_page p
				LEFT JOIN #table_magazine_content c ON p.pid = c.pid
				WHERE p.eid = $eid GROUP BY p.pid ORDER BY $orderby";
		$data = $this->db->select($sql);
		return $data;
	}
	
	function insert($data)
	{
		$id = parent::insert($data);
		$this->eid = $data['eid'];
		$this->event = 'after_insert';
		$this->notify();
		return $id;
	}
	
	function delete($pid)
	{
		$eid = table('magazine_page', $pid, 'eid');
		parent::delete($pid);
		
		//重排版面号
		$pages = $this->select("eid = $eid", '*', 'pageno ASC');
		foreach ($pages as $k => $p)
		{
			$p['pageno'] = $k + 1;
			$this->update($p, $p['pid']);
		}
		
		$this->event = 'after_delete';
		$this->notify();
		return $id;
	}
	/**
	 * 行编辑模式，单行或多行
	 *
	 */
	function save($post)
	{
		if(!$post['data']) {	//单行保存
			if(!$post['pid']) exit;
			$this->update($post, $post['pid']);
		}
		else
		{
			//多行保存
			$list = explode('||', $post['data']);		
			foreach ($list as $item) 
			{
				if(empty($item)) continue;
				list($pid, $data['name'], $data['editor'], $data['arteditor']) = explode('|', $item);
				$this->update($data, $pid);
			}
		}
		$this->eid = $post['eid'] ? $post['eid'] : $_GET['eid'];
		$this->event = 'after_save';
		$this->notify();
	}
	
	/**
	 * 根据条件数组（GET）搜索文章
	 */
	
	function search()
	{
		$where[] = "c.modelid = 1";
		!empty($_GET['catid']) && $where[] = 'c.catid = '.intval($_GET['catid']);
		if($_GET['startDate'] || $_GET['endDate'])
		{
			$_GET['startDate'] && $where[] = "published >= ".strtotime($_GET['startDate']);
			$_GET['endDate'] && $where[] = "published < ".(strtotime($_GET['endDate']) + 86400);
		}
		$where = implode(' AND ', $where);
		$size = $_GET['pagesize'] ? intval($_GET['pagesize']) : 15;
		$page = $_GET['page'] ? intval($_GET['page']) : 1;
		$start = ($page - 1) * $size;
		$sql = "SELECT SQL_CALC_FOUND_ROWS c.contentid, c.catid,c.title,c.published,m.username FROM #table_content c
				LEFT JOIN #table_member m ON c.publishedby = m.userid
				WHERE $where LIMIT $start, $size";
		$data = $this->db->select($sql);
		foreach ($data as &$item) 
		{
			$item['published'] = date('Y-m-d', $item['published']);
		}
		$total = $this->db->get("SELECT found_rows()");
		$total = $total['found_rows()'];
		return array('total' => $total, 'data' => $data);
	}
	/**
	 * 读取热点
	 *
	 * @param int $pid
	 */
	function getCoords($pid)
	{
		if(!$pid) return array();
		$sql = "SELECT pc.mapid, pc.coords, pc.sort, pc.contentid, c.title FROM #table_magazine_content pc
				LEFT JOIN #table_content c ON pc.contentid = c.contentid
				WHERE pc.pid = $pid";
		$coords = $this->db->select($sql);
		foreach ($coords as & $item) 
		{
			$temp = explode(',', $item['coords']);
			$item['x1'] = $temp[0];
			$item['y1'] = $temp[1];
			$item['x2'] = $temp[2];
			$item['y2'] = $temp[3];
			$item['title'] = str_cut($item['title'], 27);
		}
		return $coords;
	}
	
	/**
	 * 保存热点
	 *
	 * @param array $data
	 * @return boolean
	 */
	function saveCoords($data, $pid)
	{
		if(!$data['contentid'][0]) return false;
		$content = loader::model('admin/content','magazine');
		
		$page = $this->get($pid, 'pid, mid, eid, pageno');
		$count = count($data['contentid']);
		$return = array();
		for ($i=0; $i < $count; $i++)
		{
			$item = $page;
			$item['mapid'] = $data['mapid'][$i];
			$item['contentid'] = $data['contentid'][$i];
			$item['sort'] = $i;
			$item['coords'] = "{$data['x1'][$i]},{$data['y1'][$i]},{$data['x2'][$i]},{$data['y2'][$i]}";
			if($item['mapid'])
			{
				$content->update($item, $item['mapid']);
			}
			else
			{
				$item['mapid'] = $content->insert($item);
			}
			$return[$i] = $item['mapid'];
		}
		return $return;
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