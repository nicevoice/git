<?php
class model_admin_section_recommend extends model
{
	public $total;
	function __construct()
	{
		parent::__construct();
		$this->_table = $this->db->options['prefix'].'section_recommend';
		$this->_primary = 'recommendid';
		$this->_fields = array('recommendid', 'contentid', 'sectionid', 'recommended', 'recommendedby');

		$this->_readonly = array('recommendid');
		$this->_create_autofill = array('recommended'=>TIME, 'recommendedby'=>$this->_userid);
		$this->_update_autofill = array();
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array();

	}

	function add($data)
	{
		$data = $this->filter_array($data, array('contentid', 'sectionid'));
		return $this->insert($data);
	}

	function update($contentid, $sectionids)
	{
		$deletes = $old_sectionids = array();
		$data = $this->gets_by('contentid', $contentid, 'recommendid, sectionid');
		foreach ($data as $r)
		{
			$old_sectionids[] = $r['sectionid'];
			if (!in_array($r['sectionid'], $sectionids)) $deletes[] = $r['recommendid'];
		}
		if ($deletes) $this->delete($deletes);
		$new_sectionids = array_diff($sectionids, $old_sectionids);
		if ($new_sectionids)
		{
			foreach ($new_sectionids as $sectionid)
			{
				$this->insert(array('contentid'=>$contentid, 'sectionid'=>$sectionid));
			}
		}
		return true;
	}

	function delete($recommendid)
	{
		return parent::delete($recommendid);
	}

	function get_by_section($sectionid, $page, $size=20)
	{
		$sql = "SELECT * FROM #table_section_recommend WHERE sectionid = $sectionid ORDER BY recommended DESC";
		$csql = "SELECT count(*) as n FROM #table_section_recommend WHERE sectionid = $sectionid";
		$rs = $this->db->get($csql);
		$this->total = $rs['n'];
		$data = $this->db->page($sql, $page, $size);
		
		$recommended = array();
		$contentids = array();
		foreach ($data as $d)
		{
			$contentids[] = $d['contentid'];
			$recommended[$d['contentid']]['recommended'] = $d['recommended'];
			$recommended[$d['contentid']]['recommendid'] = $d['recommendid'];
		}
		if ($contentids)
		{
			$sql = "SELECT * FROM #table_content WHERE contentid IN (".implode_ids($contentids).")";
			$ret = $this->db->select($sql);
			$tsource = table('source');
			$tmodel = table('model');
			foreach($ret as &$d)
			{
				$d['username'] = username($d['createdby']);
				$d['modename'] = $tmodel[$d['modelid']]['alias'];
				$d['source'] = $tsource[$d['sourceid']]['name'];
				$d['recommended'] = $recommended[$d['contentid']]['recommended'];
				$d['recommendid'] = $recommended[$d['contentid']]['recommendid'];
			}
			$this->_addDesc($ret);
		}
		return $ret;
	}

	function search($where, $page, $size=20)
	{
		$condition = array("status='6'");
		if ($catid = id_format(value($where, 'catid')))
		{
			if (! is_array($catid))
            {
                $catid = array($catid);
            }
			foreach ($catid as $index => $_catid)
            {
                if ($childids = table('category', $_catid, 'childids'))
                {
                    $catid[$index] = $_catid . ',' . $childids;
                }
            }
			$catid = (array) id_format(implode(',', $catid));
            $catid = implode_ids(array_unique($catid));
			$condition[] = "catid IN ($catid)";
		}
		if ($modelid = intval($where['modelid']))
		{
			$condition[] = "modelid=$modelid";
		}
        if (isset($where['thumb']) && intval($where['thumb']) === 1)
        {
            $condition[] = "`thumb` <> ''";
        }
		if ($where['keywords'])
		{
			$condition[] = where_keywords('title', $where['keywords']);
		}
		$condition = implode(' AND ', $condition);
		$sql = "SELECT * FROM #table_content";
		$csql = "SELECT count(*) as n FROM #table_content";
		if ($condition)
		{
			$sql .= " WHERE $condition";
			$csql .= " WHERE $condition";
		}
		$rs = $this->db->get($csql);
		$this->total = $rs['n'];
		$sql .= ' ORDER BY published DESC';
		$data = $this->db->page($sql, $page, $size);
		$tsource = table('source');
		$tmodel = table('model');
		foreach($data as &$d)
		{
			$d['username'] = username($d['createdby']);
			$d['modename'] = $tmodel[$d['modelid']]['alias'];
			$d['source'] = $tsource[$d['sourceid']]['name'];
		}
		$this->_addDesc($data);
		return $data;
	}

	protected function _addDesc(&$data)
	{
		foreach ($data as &$d)
		{
			$sql = "SELECT * FROM #table_{$d[modename]} WHERE contentid='$d[contentid]'";
			if ($rs = $this->db->get($sql))
			{
				$d['description'] = (string) $rs['description'];
				$d['subtitle'] = (string) $rs['subtitle'];
			}
			else
			{
				$d['description'] = '';
				$d['subtitle'] = '';
			}
			if (isset($d['created']))
			{
				$d['created'] = date('Y-m-d H:i:s', $d['created']);
			}
			if (isset($d['recommended']))
			{
				$d['recommended'] = date('Y-m-d H:i:s', $d['recommended']);
			}
			$d['tips'] = $this->_buildhandItemTips($d);
		}
	}
	protected function _buildhandItemTips($item)
	{
		// 标题、缩略图、来源、摘要、Tags、点击、评论数、权重
		$tips = array();
		if ($item['thumb'])
		{
			$thumb  = preg_match('|^http[s]?\://|i',$item['thumb']) ? $item['thumb'] : (UPLOAD_URL.$item['thumb']);
			$tips[] = '<img src="'.$thumb.'" width="100" />';
		}
		$tips[] = '标题：<span'.($item['color'] ? ' style="color:'.$item['color'].';"':'').'>'.$item['title'].'</span>';
		$tips[] = 'Tags：'.$item['tags'];
		$tips[] = '点击：'.$item['pv'];
		$tips[] = '评论数：'.$item['comments'];
		$tips[] = '权重：'.$item['weight'];
		$tips[] = '来源：'.$item['source'];
		$tips[] = '摘要：'.$item['description'];
		return implode('<br />', $tips);
	}

	function get_sections($contentid)
	{
		if (empty($contentid)) return array();
		$sql = "SELECT s.name, r.sectionid FROM #table_section_recommend r
			LEFT JOIN #table_section s ON r.sectionid = s.sectionid
			WHERE r.contentid = {$contentid}";
		$query = $this->db->query($sql);
		$return = array();
		foreach ($query as $value)
		{
			$return[] = $value;
		}
		return $return;
	}
}
