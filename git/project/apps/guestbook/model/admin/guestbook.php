<?php
class model_admin_guestbook extends model
{
	public $guestbook,$where,$iplocation,$set;

	function __construct()
	{
		parent::__construct();
		$this->_table = $this->db->options['prefix'].'guestbook';
		$this->_primary = 'gid';
		$this->_fields = array('gid','typeid', 'title', 'content', 'userid', 'username', 'gender', 'email','qq','msn','telephone','address','homepage','isview','ip','addtime','reply','replyer','replytime');
		
		$this->_readonly = array('gid');
		$this->_create_autofill = array('userid'=>$this->_userid,'username'=>$this->_username,'replytime'=>TIME,'ip'=>IP);
		$this->_update_autofill = array();
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array();
	}

	function replace_keyword($content)
	{
		$this->set = new setting();
		$setting = $this->set->get('guestbook');
		$this->keyword = $setting['sensekeyword'];
		$this->keyword = str_replace(array("\r\n","\n","\r"),'|',$this->keyword);
		$replace = '<span style="color:red">'.str_replace('|','</span>|<span style="color:red">',$this->keyword).'</span>';
		return str_replace(explode('|',$this->keyword),explode('|',$replace),$content);
	}

	function ls($get, $fields, $order, $page, $size)
	{
		$where = null;
		if(isset($get['status']) && $get['status'])
		{
			switch($get['status'])
			{
				case 1:
					$where[] = '`isview`=0';
					break;
				case 2:
					$where[] = "`reply` != ''";
					break;
				default:
					break;
			}
		}
		if (isset($get['keywords']) && $get['keywords']) 		$where[] = where_keywords('title', $get['keywords']);
		if (isset($get['typeid']) && $get['typeid']) 			$where[] = '`typeid`='.$get['typeid'];
		if (isset($get['published']) && $get['published']) 		$where[] = where_mintime('addtime', $get['published']);
		if (isset($get['unpublished']) && $get['unpublished']) 	$where[] = where_maxtime('addtime', $get['unpublished']);
		
		if (is_array($where)) $where = implode(' AND ', $where);
		$this->where = $where;
		$data = $this->page($where, $fields, $order, $page, $size);
		return $data;
	}

	function total()
	{
		return $this->count($this->where);
	}
	
	function add($data)
	{
		$data = $this->filter_array($data,$this->_fields);
		return $this->insert($data);
	}

	function edit($gid, $data)
	{
		$gid = intval($gid);
		$data = $this->filter_array($data, array('content', 'disabled'));
		return $this->update($data, "`gid`=$gid");
	}

	function delete($gid)
	{
		return parent::delete(implode_ids($gid));
	}
	
	function _after_select(& $data, $multiple)
	{
		if (empty($data))
		{
			return $data;
		}
		if ($multiple) 
		{
			foreach ($data as $key => $value) 
			{
				$data[$key]['addtime'] = date('Y-m-d H:i:s',$value['addtime']);
				$data[$key]['typename'] = table('guestbook_type',$value['typeid'],'name');
				if ($data[$key]['reply'])
				{
					$data[$key]['state'] = '已回复';
				}
				elseif ($data[$key]['isview'])
				{
					$data[$key]['state'] = '已查看';
				}
				else
				{
					$data[$key]['state'] = '未查看';
				}
			}
		}
		else
		{
			$data['typename'] = table('guestbook_type',$data['typeid'],'name');
			if ($data['reply'])
			{
				$data['state'] = '已回复';
			}
			elseif ($data['isview'])
			{
				$data['state'] = '已查看';
			}
			else
			{
				$data['state'] = '未查看';
			}
		}
	}
}