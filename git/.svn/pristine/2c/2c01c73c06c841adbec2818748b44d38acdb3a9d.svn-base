<?php

class model_admin_category extends model
{
	function __construct()
	{
		parent::__construct();

		$this->_table = $this->db->options['prefix'].'category_field';
		$this->_primary = 'projectid';
		$this->_fields = array(
			'projectid', 'catid'
		);
		$this->_readonly = array();
		$this->_create_autofill = array();
		$this->_update_autofill = array();
		$this->_validators = array();
	}

	public function add($projectid, $catid)
	{
		return $this->insert(array('projectid' => $projectid, 'catid' => $catid));
	}

	/**
	 * 插入数据
	 * @param int $projectid 方案ID
	 * @param string $catids 以逗号分隔的栏目ID
	 */
	public function set_project_api($projectid, $catids) {
		if(!$projectid)
		{
			$this->delete($catids);
			return;
		}

		$catids = $this->_checkid($catids);

		if(is_array($catids))
		{
			foreach($catids as $catid)
			{
				$this->add($projectid, $catid);
			}
		}
		else
		{
			$this->add($projectid, $catids);
		}
	}

	public function delete($catids)
	{
		$catids = $this->_checkid($catids);

		if(is_array($catids))
		{
			foreach($catids as $catid) {
				parent::delete(array('catid'=>$catid));
			}
		}

		parent::delete(array('catid'=>$catids));
	}

	/**
	 * 检测栏目ID是否含有逗号
	 * @return array;
	 */
	private function _checkid($catids) 
	{
		if(strpos($catids, ',') !== FALSE) 
		{
			$catids = explode(',', $catids);
		}
		return $catids;
	}
}