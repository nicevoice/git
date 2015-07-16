<?php
class model_admin_content extends model
{
    public $setting, $html_root, $www_root;
    private $uri;

	function __construct()
	{
		parent::__construct();
		$this->_table = $this->db->options['prefix'].'magazine_content';
		$this->_primary = 'mapid';
		$this->_fields = array('mapid', 'pid','eid','mid', 'pageno', 'contentid', 'sort');
		$this->_txtfields = array();
		$this->_readonly = array('mapid');
        $this->setting = setting::get('magazine');
        $this->uri = loader::lib('uri','system');
        $u = $this->uri->psn($this->setting['path']);
        $this->html_root = $u['path'];
        $this->www_root  = $u['url'];
	}
	
	//版面文章
	function getContents($pid, $orderby = 'sort ASC')
	{
		if(!$pid) return array();
		$sql = "SELECT mc.*, ca.url AS caturl, c.url, c.contentid, c.title, c.pv, ca.name FROM #table_magazine_content mc
				LEFT JOIN #table_content c ON mc.contentid = c.contentid
				LEFT JOIN #table_category ca ON ca.catid = c.catid
				WHERE mc.pid = $pid ORDER BY $orderby";
		$data = $this->db->select($sql);
		return $data;
	}
	
	function search($data)
	{
		extract($data);
		!$page && $page = 1;
		!$size && $size = 15;
		$start = ($page - 1) * $size;
		$where = "WHERE c.modelid = 1 AND c.status = 6";
		
		if($data['catid']) $where .= " AND c.catid = ".intval($data['catid']);
		if($data['keywords']) $where .= " AND c.title LIKE '%".addslashes($data['keywords'])."%'";
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS c.published, c.contentid, c.url, c.title, ca.name AS catname FROM #table_content c
				LEFT JOIN #table_category ca ON ca.catid = c.catid
				$where ORDER BY c.contentid DESC LIMIT $start, $size";
		$data = $this->db->select($sql);
		$total = $this->db->get("SELECT found_rows()");
		foreach ($data AS & $item) 
		{
			$item['time'] = date('Y-m-d', $item['published']);
		}
		return array('state' => true, 'total' => $total['found_rows()'], 'data' => $data);
	}
	
	function saveRelate($pid, $ids)
	{
		$this->delete("pid = $pid");
		$ids = explode(',', $ids);
		$sql = "SELECT * FROM #table_magazine_page WHERE pid = $pid";
		$page = $this->db->get($sql);
		$page['sort'] = 0;
		foreach ($ids as $id) 
		{
			if(!$id) continue;
			$page['contentid'] = $id;
			$this->insert($page);
			$page['sort']++;
		}
		return true;
	}
	//删除后重排sort
	function delete($id, $pid)
	{
		parent::delete($id);
		if(!$pid) return;
		$maps = $this->select("pid = $pid", '*', 'sort ASC');
		foreach ($maps as $k => $map)
		{
			$map['sort'] = $k;
			$this->update($map, $map['mapid']);
		}
		return true;
	}
}