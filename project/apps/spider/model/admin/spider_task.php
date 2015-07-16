<?php
/**
 * cmstop_spider_task
 *   taskid
 *   ruleid
 *   catid
 *   title
 *   url
 *   frequency
 *   created
 *   createdby
 *   updated
 *   updatedby
 */
class model_admin_spider_task extends model
{
	function __construct()
	{
		parent::__construct();
		$this->_table = $this->db->options['prefix'].'spider_task';
		$this->_primary = 'taskid';
		$this->_fields = array('taskid','ruleid','catid','title','url','frequency','created','createdby','updated','updatedby');
		
		$this->_readonly = array('taskid','created','createdby');
		$this->_create_autofill = array('created'=>TIME,'createdby'=>$this->_userid);
		$this->_update_autofill = array('updatedby'=>$this->_userid);
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array(
			'title'=>array(
				'not_empty'=>array('任务名称不能为空')
			)
		);
	}
	
	function insert($data)
	{
		$frequency = intval($data['frequency']);
		if ($frequency > 0)
		{
			$data['updated'] = $frequency + TIME;
		}
		else
		{
			$data['updated'] = 0;
			$data['frequency'] = 0;
		}
		return parent::insert($data);
	}
	
	function update($data, $taskid)
	{
		$frequency = intval($data['frequency']);
		if ($frequency > 0)
		{
			$data['updated'] = $frequency + TIME;
		}
		else
		{
			$data['updated'] = 0;
			$data['frequency'] = 0;
		}
		return parent::update($data, $taskid);
	}
	
	function spider($taskid)
	{
		$sql = "SELECT t.*, r.* FROM #table_spider_task as t
				LEFT JOIN #table_spider_rules as r ON r.ruleid = t.ruleid
				WHERE taskid = '$taskid'";
		$rs = $this->db->select($sql);
		$rs = reset($rs);
		$engine = loader::lib('spider', 'spider');
		$rule = unserialize($rs['list_rule']);
		$list = $engine->getList($rs['url'], $rule, $rs['charset']);
		if (!empty($list))
		{
			$t_spider = loader::model('admin/spider','spider');
			foreach (array_reverse($list) as $row)
			{
				$row['taskid'] = $taskid;
				$row['guid'] = md5($row['url']);
				try
				{
					$t_spider->insert($row);
				} catch (Exception $e) {}
			}
		}
		$frequency = intval($rs['frequency']);
		if ($frequency > 0)
		{
			$this->update(array('updated'=>TIME+$frequency), $taskid);
		}
	}
	
	function ls()
	{
		return $this->select();
	}
	
	function cron_publish()
	{
		return $this->select('updated IS NOT NULL AND updated > 0 AND updated <= '.TIME, 'taskid');
	}
}