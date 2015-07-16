<?php
class model_admin_edition extends model implements SplSubject 
{
	private $observers = array();
    public $setting, $html_root, $www_root;
    private $uri;
	
	function __construct()
	{
		parent::__construct();
		$this->_table = $this->db->options['prefix'].'paper_edition';
		$this->_primary = 'editionid';
		$this->_fields = array('editionid', 'paperid', 'number', 'total_number', 'url', 'date', 'created','ip','createdby','disabled');
		$this->_txtfields = array();
		$this->_readonly = array('editionid');
		$this->_create_autofill = array('ip'=>IP,'created'=>TIME,'createdby'=>$this->_userid);
		$this->_update_autofill = array('ip'=>IP,'created'=>TIME,'createdby'=>$this->_userid);
		$this->_validators = array(
			'date'=>array('not_empty'=>array('出版日期不能为空')),
	        'number'=> array(
				'not_empty'=>array('期号不能为空'),
				'max_length' =>array(20, '期号不得超过20个字')
			),
			'total_number'=> array(
				'not_empty'=>array('总期号不能为空'),
				'max_length' =>array(20, '总期号不得超过20个字')
			),
			'paperid'=>array('not_empty'=>array('请选择报纸')),
		);
        $this->setting = setting::get('paper');
        $this->uri = loader::lib('uri','system');
        $u = $this->uri->psn($this->setting['path']);
        $this->html_root = $u['path'];
        $this->www_root  = $u['url'];
	}
	
	public function page($where, $order = null, $page = 1, $size = 20)
	{
		$where && $where = 'WHERE '.$where;
		$start = ($page - 1) * $size;
		$start < 0 && $start = 0;
		$field = "e.*,m.username,p.name, COUNT(c.mapid) AS count";
		$sql = "SELECT count(*) AS total FROM $this->_table e $where";
		$total = $this->db->get($sql);
		$total = $total['total'];
		$sql = "SELECT $field FROM $this->_table e 
				LEFT JOIN #table_member m ON e.createdby = m.userid
				LEFT JOIN #table_paper p ON p.paperid = e.paperid
				LEFT JOIN #table_paper_content c ON e.editionid = c.editionid
				$where GROUP BY e.editionid ORDER BY $order LIMIT $start, $size";
		$data = $this->db->select($sql);
		$this->_after_read($data);
		return array('total' => $total, 'data' => $data);
	}
	
	public function getEdition($id)
	{
		$sql = "SELECT e.*, m.username,p.name, COUNT(c.mapid) AS count FROM $this->_table e 
				LEFT JOIN #table_paper_content c ON e.editionid = c.editionid
				LEFT JOIN #table_member m ON e.createdby = m.userid
				LEFT JOIN #table_paper p ON p.paperid = e.paperid
				WHERE e.editionid = $id GROUP BY e.editionid";
		$data = $this->db->get($sql);
		$this->_after_read($data, 0);
		return $data;
	}
	
	public function save(array $data)
	{
		$id = intval($data['editionid']);
		$data = $this->filter_array($data, array('editionid','paperid','number', 'date', 'created','url',
					'total_number', 'ip','createdby','disabled'));
		
		$data['date'] = strtotime($data['date']);
		
		if($id)
		{
			$this->update($data, $id);
		}
		else 
		{
			$id = $this->insert($data);
			$this->mod = 'insert';
		}
		$this->eid = $id;
		$this->pid = $data['paperid'];
	   	$this->event = 'after_save';
	   	$this->notify();
		return $id;
	}
	//根据期id取得头版头条
	public function getFirst($eid)
	{
		$sql = "SELECT contentid,pageid FROM #table_paper_content 
				WHERE editionid = $eid ORDER BY pageno ASC, `sort` ASC LIMIT 1";
		$row = $this->db->get($sql);
		if(!$row) return false;
		$url = "?app=paper&controller=content&action=prevView&cid={$row['contentid']}&pageid={$row['pageid']}";
		return $url;
	}
	
	private function _after_read(& $data, $multiple=1)
	{
		$multiple || $data = array($data);
        foreach ($data as  $k=>$v)
        {
            switch ($v['disabled'])
            {
                case 1:
                    $data[$k]['disabled_words'] = '已发布';
                    break;
                case 2:
                    $data[$k]['disabled_words'] = '休眠';
                    break;
                default:
                    $data[$k]['disabled_words'] = '<label>未发布</label>';
                    break;
            }
            $data[$k]['created'] = date('Y-m-d',$v['created']);
            $data[$k]['date'] = date('Y-m-d',$v['date']);
        }
        $multiple || $data = $data[0];
	}
	
	//取得编辑列表
	public function edits()
	{
		$sql = "SELECT createdby FROM $this->_table GROUP BY createdby";
		$data = $this->db->select($sql);
		if(!$data) return array();
		foreach ($data AS $item) 
		{
			$ids[] = $item['createdby'];
		}
		$ids = implode(',' ,$ids);
		$sql = "SELECT userid, username FROM #table_member WHERE userid IN ($ids) ORDER BY userid";
		$data = $this->db->select($sql);
		return $data;
	}
	
	//根据条件数组创建where
	public function createWhere($data)
	{
		isset($data['paperid']) && $where[] = 'e.paperid = '.intval($data['paperid']);
		isset($data['disabled']) && $data['disabled'] != -1 && $where[] = 'e.disabled = '.intval($data['disabled']);
		isset($data['createdby']) && $where[] = 'e.createdby = '.intval($data['createdby']);
		if($data['min'] || $data['max'])
		{
			$field = 'e.'.$data['field'];
			$data['min'] && $where[] = "$field >= ".strtotime($data['min']);
			$data['max'] && $where[] = "$field < ".(strtotime($data['max']) + 86400);
		}
		$where = implode(' AND ', $where);
		return $where;
	}
	
	function delete($id)
	{
		$this->id = $id;
		$this->pid = table('paper_edition', $id, 'paperid', 1);
		$this->event = 'before_delete';
		$this->notify();
		$rs = parent::delete($id);
		$this->event = 'after_delete';
		$this->notify();
		return $rs;
	}
	
	//取得最后一期的期号数据
	public function lastE($paperid)
	{
		$sql = "SELECT number AS lastN, total_number AS lastTN FROM #table_paper_edition WHERE paperid = $paperid ORDER BY editionid DESC LIMIT 1";
		return $this->db->get($sql);
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