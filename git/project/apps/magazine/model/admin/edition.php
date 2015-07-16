<?php
class model_admin_edition extends model implements SplSubject 
{
	private $observers = array();
    public $setting, $html_root, $www_root;
    private $uri;

	function __construct()
	{
		parent::__construct();
		$this->_table = $this->db->options['prefix'].'magazine_edition';
		$this->_primary = 'eid';
		$this->_fields = array('eid', 'mid', 'title', 'number', 'total_number', 'image', 'pdf', 'year','url', 'publish', 'created','ip','createdby','disabled');
		$this->_txtfields = array();
		$this->_readonly = array('eid');
		$this->_create_autofill = array('ip'=>IP,'created'=>TIME,'createdby'=>$this->_userid);
		$this->_update_autofill = array('ip'=>IP,'created'=>TIME,'createdby'=>$this->_userid);
		$this->_validators = array(
			'publish'=>array('not_empty'=>array('出版日期不能为空')),
	        'number'=> array(
				'not_empty'=>array('期号不能为空'),
				'max_length' =>array(5, '期号不得超过5位数')
			),
			'mid'=>array('not_empty'=>array('请选择杂志')),
		);
        $this->setting = setting::get('magazine');
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
		$sql = "SELECT e.*,m.username,p.name,COUNT(c.mapid) AS count FROM $this->_table e 
				LEFT JOIN #table_member m ON e.createdby = m.userid
				LEFT JOIN #table_magazine p ON p.mid = e.mid
				LEFT JOIN #table_magazine_content c ON e.eid = c.eid
				$where GROUP BY e.eid ORDER BY $order LIMIT $start, $size";
		$data = $this->db->select($sql);
		$this->_after_read($data);
		return $data;
	}
	
	public function getEdition($id)
	{
		$sql = "SELECT e.*,m.username,p.name FROM $this->_table e 
				LEFT JOIN #table_member m ON e.createdby = m.userid
				LEFT JOIN #table_magazine p ON p.mid = e.mid
				WHERE eid = $id";
		$data = $this->db->get($sql);
		$this->_after_read($data, 0);
		return $data;
	}
	
	public function save(array $data)
	{
		$id = intval($data['eid']);
		$data = $this->filter_array($data, array('eid','mid', 'title', 'number', 'total_number','publish', 
												'created', 'image', 'pdf', 'ip','createdby','disabled'));
		if($data['publish']) 
		{
			$data['year'] = substr($data['publish'], 0, 4);
			
			if($data['year'] > 1900) 
			{
				$sql = "UPDATE #table_magazine SET default_year = ? WHERE mid = ?";
				$this->db->update($sql, array($data['year'], $data['mid']));
			}
		}
		$data['publish'] = strtotime($data['publish']);
		if($id)
		{
			$this->update($data, $id);
		}
		else 
		{
			$id = $this->insert($data);
			$this->eid = $id;
			$this->event = 'after_insert';
			$this->notify();
		}
		
		return $id;
	}
	
	private function _after_read(& $data, $multiple=1){
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
            $data[$k]['publish'] = date('Y-m-d',$v['publish']);
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
		isset($data['mid']) && $where[] = 'e.mid = '.intval($data['mid']);
		isset($data['disabled']) && $data['disabled'] != -1 && $where[] = 'e.disabled = '.intval($data['disabled']);
		isset($data['createdby']) && $where[] = 'e.createdby = '.intval($data['createdby']);
		if($data['min'] || $data['max'])
		{
			$field = 'e.'.$data['field'];
			if($field == 'e.year') 
			{
				$where[] = 'year = '.intval(substr($data['min'], 0, 4));
			}
			else
			{
				$data['min'] && $where[] = "$field >= ".strtotime($data['min']);
				$data['max'] && $where[] = "$field < ".(strtotime($data['max']) + 86400);
			}
		}
		$where = implode(' AND ', $where);
		return $where;
	}
	
	//取得最后一期的期号数据
	public function lastE($mid)
	{
		$sql = "SELECT number AS lastN, total_number AS lastTN FROM #table_magazine_edition WHERE mid = $mid ORDER BY eid DESC LIMIT 1";
		return $this->db->get($sql);
	}
	
	function delete($id)
	{
		$this->id = $id;
		$e =  table('magazine_edition', $id);
		$this->mid = $e['mid'];
		$this->year = $e['year'];
		$this->event = 'before_delete';
		$this->notify();
		$rs = parent::delete($id);
		$this->event = 'after_delete';
		$this->notify();
		return $rs;
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