<?php
class model_admin_special_page extends model
{
	function __construct()
	{
        parent::__construct();
		$this->_table = $this->db->options['prefix'].'special_page';
		$this->_primary = 'pageid';
		$this->_fields = array('pageid', 'contentid', 'data', 'name', 'file', 'url', 'template', 'locked', 'lockedby', 'updated', 'updatedby', 'published', 'frequency', 'created', 'createdby');

		$this->_readonly = array('pageid');
		$this->_create_autofill = array('created'=>TIME,'createdby'=>$this->_userid);
		$this->_update_autofill = array('updated'=>TIME,'updatedby'=>$this->_userid);
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array();
	}
	
	function publish($pageid)
	{
		return $this->update(array(
			'published' => TIME
		), $pageid);
	}
	function unpublish($pageid)
	{
		return $this->update(array(
			'published' => 0
		), $pageid);
	}
	function saveData($pageid, $data)
	{
		$orig = $this->get($pageid, 'data');
		$origData = decodeData($orig['data']);
		if (!empty($origData) && is_array($origData))
		{
			$origWidgetids = $this->_findWidget(decodeData($orig['data']));
			$widgetids = $this->_findWidget($data);
			$dids = array_unique(array_diff($origWidgetids, $widgetids));
			if ($dids)
			{
				$this->db->update('UPDATE #table_widget SET status=-1 WHERE widgetid IN ('.implode_ids($dids).') AND status=0');
			}
			$rids = array_unique(array_diff($widgetids, $origWidgetids));
			if ($rids)
			{
				$this->db->update('UPDATE #table_widget SET status=0 WHERE widgetid IN ('.implode_ids($rids).') AND status=-1');
			}
		}
		
		return $this->update(array(
			'data' => encodeData($data)
		), $pageid);
	}
	protected function _findWidget($data)
	{
		$widgetids = array();
		foreach ($data as &$item) {
			if (isset($item['widgetid']))
			{
				$widgetids[] = $item['widgetid'];
			}
			elseif (is_array($item['items']))
			{
				$widgetids = array_merge($widgetids, $this->_findWidget($item['items']));
			}
		}
		return $widgetids;
	}
	function add($data)
	{
		$data = $this->filter_array($data, array('contentid', 'name', 'data', 'file', 'url', 'template', 'frequency'));
		if (empty($data['data']))
		{
			$data['data'] = array(
				'head'=>array(
					'file'=>$data['file'],
					'title'=>$data['name'],
					'meta' => array (
						'Content-Type' => 'text/html; charset=UTF-8',
					)
				)
			);
		}
		if (is_array($data['data']))
		{
			$data['data'] = encodeData($data['data']);
		}
		return $this->insert($data);
	}
	function lock($pageid)
	{
		if (!$this->islock($this->get($pageid))) {
			$this->update(array(
				'locked'=>TIME,
				'lockedby'=>$this->_userid
			), $pageid);
		}
	}
	function unlock($pageid)
	{
		if (!$this->islock($this->get($pageid))) {
			$this->update(array(
				'locked'=>'0',
				'lockedby'=>'0'
			), $pageid);
		}
	}
	function islock($page)
	{
		return $page['locked'] && (TIME < $page['locked'] + 180)
			&& $page['lockedby'] && $page['lockedby'] != $this->_userid;
	}
}