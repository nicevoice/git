<?php
/**
 * 自由列表页面
 */
class model_admin_freelist extends model
{
	private $json = null;
	function __construct()
	{
		parent::__construct();
		$this->json = & factory::json();
		$this->_table = $this->db->options['prefix'].'freelist';
		$this->_primary = 'flid';
		$this->_fields = array(
			'flid', 'gid', 'name', 'filename',
			'path', 'type', 'template', 
			'maxpage', 'pagesize', 'frequency', 'autopublish', 
			'title', 'keywords', 'description',
			'created','createdby','modified',
			'filterules', 'modifiedby', 'published', 'nextpublish'
		);
		$this->_readonly = array('flid');
		$this->_create_autofill = array('created'=>TIME, 'createdby'=>$this->_userid);
		$this->_update_autofill = array('modified'=>TIME, 'modifiedby'=>$this->_userid);
		$this->_validators = array(
				'name'=>array(
					'not_empty'=>array('列表页名称不能为空'),
					'max_length' =>array(30, '列表页名称不得超过30字节')
				),
				'filename'=>array(
					'not_empty'=>array('文件名称不能为空'),
					'max_length' =>array(30, '文件名称不得超过30字节')
				),
				'path'=>array(
					'not_empty'=>array('网址不能为空')
				),
				'template'=>array(
					'not_empty'=>array('页面模板不能为空')
				),
				'maxpage'=>array(
					'not_empty'=>array('生成页数不能为空'),
					'integer'=>array('生成页数必须为整数')
				),
				'pagesize'=>array(
					'not_empty'=>array('分页页数不能为空'),
					'integer'=>array('分页页数必须为整数')
				)
			);
	}

	/**
	 * freelist首页获取数据
	 * table freelist as f, freelist_group as g
	 */
	public function page($where, $order, $page, $size)
	{
		$where && $where = "WHERE $where";
		$order && $order = "ORDER BY $order";
		$field = "f.flid, f.name, f.filename, f.path, f.frequency, f.autopublish, f.published, f.filterules, f.gid, g.name AS groupname";
		$sql = "SELECT $field FROM #table_freelist f
				LEFT JOIN #table_freelist_group g ON f.gid = g.gid
				$where $order";
		$data = $this->db->page($sql, $page, $size);
		return $this->output($data);
	}

	/**
	 * 基本设置修改页面获取数据
	 * 字段同 $this->page()
	 * @param ID 可以多个
	 */
	public function get_byid($id) 
	{
		$field = "f.flid, f.name, f.filename, f.frequency, f.autopublish, f.published, f.gid, g.name AS groupname";
		$data = $this->db->get("SELECT $field FROM #table_freelist f
				LEFT JOIN #table_freelist_group g ON f.gid = g.gid
				WHERE flid IN (".implode_ids($id).")");
		return $this->output($data);
	}

	/**
	 * 筛选配置器 添加数据
	 * 
	 */
	public function add($data)
	{
		$flid = $data['flid'];

		// 如果是筛选配置器提交的数据，写在form表单里的标识字段
		if($data['upfilter'])
		{
			$data['filterules'] =  $this->_filter_array($data);
		}
		else
		{
			$data['autopublish'] = $data['frequency'] ? 1 : 0;
			$data['published'] = $data['frequency'] ? TIME : 0;
			$data['nextpublish'] = $data['frequency'] ? TIME + intval($data['frequency']) * 60 : 0;
		}

		if($flid)
		{
			$data['autopublish'] = $data['frequency'] ? 1 : 0;
			$data['nextpublish'] = $data['frequency'] ? TIME + intval($data['frequency']) * 60 : 0;
			$this->update($data,"`flid`=$flid");
			$return = $flid;
		}
		else
		{
			$return = $this->insert($data);
		}

		return $return;
	}

	/**
	 * 格式化规则数据
	 * options 对应 form 表单 name 值
	 */
	private function _filter_array($data) 
	{
		return $this->json->encode($data['options']);
	}

	// 更新
	public function fupdate($fild)
	{
		return $this->set_field('autopublish', 1, "flid IN (".implode_ids($fild).")");
	}

	// 停止自动更新
	public function fstop($fild) 
	{
		return $this->set_field('autopublish', 0, "flid IN (".implode_ids($fild).")");
	}

	public function delete($flid)
	{
		return parent::delete($flid);
	}

	/**
	 * 输出格式转换, $data是一条或多条记录
	 */
	private function output($data)
	{
		if(!$data) return array();
		import('helper.pinyin');
		$uri = loader::lib('uri','system');

		// 如果 data 是单条数据 如: array(0=>array()); 下面直接转换 $data = $data[0]
		if(!$data[0]) {
			$flag = true;
			$data = array($data);
		}
		foreach ($data as & $r)
		{
			$r['published'] = $r['published'] ? date('Y-m-d H:i:s', $r['published']) : '未生成';
			$r['frequency'] = $r['autopublish'] ? $r['frequency'].'(分钟)' : '手动';
			$r['groupname'] = $r['groupname'] ? $r['groupname'] : '未分组';

			//如果 有规则 并且 已经发布 则把url 加在数据里
			if($r['published'] && $r['filterules']) 
			{
				$u = $uri->psn($r['path']);
				switch($r['type'])
				{
					case 0: $fileext = SHTML; break;
					case 1: $fileext = '.xml';  break;
					case 2: $fileext = '.json'; break;
				}
				$r['url'] = $u['url'].DS.$r['filename'].$fileext;
			}
		}
		if($flag) $data = $data[0];
		return $data;
	}
	
}