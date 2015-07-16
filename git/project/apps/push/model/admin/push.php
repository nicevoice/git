<?php
/**
 * cmstop_push
 *   pushid
 *   guid
 *   taskid
 *   contentid
 *   status
 *   pushed
 *   pushedby
 */
class model_admin_push extends model
{
	public $total;
	function __construct()
	{
		parent::__construct();
		$this->_table = $this->db->options['prefix'].'push';
		$this->_primary = 'pushid';
		$this->_fields = array('pushid', 'guid', 'taskid', 'contentid', 'status', 'pushed', 'pushedby');
		
		$this->_readonly = array('pushid', 'guid', 'taskid');
		$this->_create_autofill = array();
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array();
	}
	
	function setViewed($pushid)
	{
		$data = array('status'=>'viewed');
		return $this->update($data, "pushid=$pushid AND status='new'");
	}
	function setPushed($pushid, $data)
	{
		$data = array_merge($data, array(
			'status'=>'pushed',
			'pushed'=>TIME,
			'pushedby'=>$this->_userid
		));
		$this->update($data, $pushid);
	}
	
	function page_all($taskid, $page)
	{
		$sql = "SELECT t.*, r.* FROM #table_push_task as t
				LEFT JOIN #table_push_rule as r ON r.ruleid = t.ruleid
				WHERE taskid='$taskid'";
		$rs = $this->db->select($sql);
		$rs = reset($rs);
		
		$dsnid = intval($rs['dsnid']);
		$dsn = loader::model('admin/dsn', 'system');
		if (! $dsnid || !($d = $dsn->get($dsnid)))
		{
			return array();
		}
		import('db.db');
        try {
		    $db = & db::get_instance($d);
        } catch (PDOException $e) {
            return array();
        }
        
        $charset = strtr($d['charset'],array(
        	'utf8'=>'utf-8',
        	'UTF8'=>'utf-8'
        ));
        
        $rule = array(
        	'maintable' => $rs['maintable'],
        	'jointable' => unserialize($rs['jointable']),
        	'primary' => $rs['primary'],
        	'condition' => $rs['condition'],
        	'plugin' => $rs['plugin'],
        	'linkrule' => $rs['linkrule'],
        	'fields' => unserialize($rs['fields']),
        	'defaults' => unserialize($rs['defaults'])
        );
        
		$engine = loader::lib('push', 'push');
		$length = 20;
		$offset = ($page - 1) * $length;
		try {
			$list = $engine->getList($db, $rule, $charset, $length, $offset, $rs['extra_condition']);
			$this->total = $engine->getTotal($db, $rule, $rs['extra_condition']);
		} catch (Exception $e) {
			return null;
		}
		
		$category = table('category');
		$catid = $rs['catid'];
		$catname = $category[$catid] ? $category[$catid]['name'] : '';
		foreach ($list as &$r)
		{
			$guid = $r['guid'];
			$where = "taskid=$taskid AND guid=$guid";
			$rs = $this->get($where);
			if ($rs)
			{
				$r = array_merge($r, $rs);
				$r['catid'] = $catid;
				$r['catname'] = $catname;
			}
			else
			{
				$data = array(
					'guid'=>$guid,
					'taskid'=>$taskid
				);
				if ($pushid = $this->insert($data))
				{
					$r = array_merge($r, $this->get($pushid));
					$r['catid'] = $catid;
					$r['catname'] = $catname;
				}
			}
		}
		return $list;
	}
	function page_status($taskid, $status, $page)
	{
		$sql = "SELECT t.*, r.* FROM #table_push_task as t
				LEFT JOIN #table_push_rule as r ON r.ruleid = t.ruleid
				WHERE taskid='$taskid'";
		$rs = $this->db->select($sql);
		$rs = reset($rs);
		
		$dsnid = intval($rs['dsnid']);
		$dsn = loader::model('admin/dsn', 'system');
		if (! $dsnid || !($d = $dsn->get($dsnid)))
		{
			return array();
		}
		import('db.db');
        try {
		    $db = & db::get_instance($d);
        } catch (PDOException $e) {
            return array();
        }
        
        $charset = strtr($d['charset'],array(
        	'utf8'=>'utf-8',
        	'UTF8'=>'utf-8'
        ));
        
        $rule = array(
        	'maintable' => $rs['maintable'],
        	'jointable' => unserialize($rs['jointable']),
        	'primary' => $rs['primary'],
        	'condition' => $rs['condition'],
        	'plugin' => $rs['plugin'],
        	'linkrule' => $rs['linkrule'],
        	'fields' => unserialize($rs['fields']),
        	'defaults' => unserialize($rs['defaults'])
        );
        
        $where = "taskid=$taskid AND status='$status'";
        $order = "guid desc";
        $length = 20;
        $list = $this->page($where, '*', $order, $page, $length);
        $this->total = $this->count($where);
        $engine = loader::lib('push', 'push');
        $category = table('category');
        $catid = $rs['catid'];
		$catname = $category[$catid] ? $category[$catid]['name'] : '';
        $data = array();
		foreach ($list as $r)
        {
        	try {
        		$one = $engine->getOne($db, $r['guid'], $rule, $charset);
        		$r = array_merge($one, $r);
        		$r['catid'] = $catid;
				$r['catname'] = $catname;
				$data[] = $r;
        	} catch (Exception $e) {
        		$this->total--;
        		continue;
        	}
        }
        return $data;
	}
	function get_detail($pushid)
	{
		$sql = "SELECT p.*, r.* FROM #table_push as p
				LEFT JOIN #table_push_task as t ON t.taskid = p.taskid
				LEFT JOIN #table_push_rule as r ON r.ruleid = t.ruleid
				WHERE pushid='$pushid'";
		$rs = $this->db->select($sql);
		$rs = reset($rs);
		
		$dsnid = intval($rs['dsnid']);
		$dsn = loader::model('admin/dsn', 'system');
		if (! $dsnid || !($d = $dsn->get($dsnid)))
		{
			return array();
		}
		import('db.db');
        try {
		    $db = & db::get_instance($d);
        } catch (PDOException $e) {
            return array();
        }
        
        $charset = strtr($d['charset'],array(
        	'utf8'=>'utf-8',
        	'UTF8'=>'utf-8'
        ));
        
        $rule = array(
        	'maintable' => $rs['maintable'],
        	'jointable' => unserialize($rs['jointable']),
        	'primary' => $rs['primary'],
        	'condition' => $rs['condition'],
        	'plugin' => $rs['plugin'],
        	'linkrule' => $rs['linkrule'],
        	'fields' => unserialize($rs['fields']),
        	'defaults' => unserialize($rs['defaults'])
        );
        $engine = loader::lib('push', 'push');
        try {
        	return $engine->getDetails($db, $rs['guid'], $rule, $charset);
        } catch (Exception $e) {
        	return array();
        }
	}
}