<?php
class model_admin_cdn_rules extends model implements SplSubject 
{

	private $observers = array();

	function __construct()
	{
		parent::__construct();
		$this->_table = $this->db->options['prefix'].'cdn_rules';
		$this->_primary = 'id';
		$this->_fields = array('id', 'cdnid', 'path', 'url');

		$this->_readonly = array();
		$this->_create_autofill = array();
		$this->_update_autofill = array();
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array('cdnid'=>array('not_empty' =>array('参数异常')),
			'url'=>array('not_empty'=>array('规则不能为空')));
	}

	public function add($data)
	{
		$data = $this->filter_array($data, array('cdnid', 'path', 'url'));
		return $this->insert($data);
	}

	public function edit($data, $id)
	{
		$data = $this->filter_array($data, array('name', 'tid'));
		return $this->update($data, "id=$id", 1);
	}

	public function rules($path)
	{
		$rules	= table('cdn_rules');
		$result	= array();
		$tmp	= 0;
		$root	= null;
		foreach ($rules as $item)
		{
			// 根目录为空,特殊判断
			$item['path'] || ($root = $item);
			if (empty($item['path']) || $item['path'] == '/')
			{
				$root = $item;
				$root['path']	= '';
			}

			if (strpos($path, $item['path']) !== false)
			{
				if (substr_count($item['path'], '/') > $tmp)
				{
					$tmp	= substr_count($item['path'], '/');
					$this->_pathtourl($item, $path);
				}
				$result[]	= $item['cdnid'];
			}
		}
		if (!count($result) && $root)
		{
			$this->_pathtourl($root, $path);
			$result[]	= $root['cdnid'];
		}
		return $result;
	}

	function _pathtourl($psn, $path)
	{
		$href	= substr($path, strlen('www/'.$psn['path']));
		foreach (array('www/include', 'www/section', 'www/widget') as $item)
		{
			$p	= strpos($path, $item);
			if ($p === false)
			{
				continue;
			}
			$include	= substr($path, $p+4);
			$_SERVER['path']	= $this->_get_page($include);
			return;
			break;
		}
		$_SERVER['path']	= $psn['url'].$href;
		return;
	}

	function _get_page($include)
	{
		$cache = & factory::cache();
		if (!$urls = $cache->get("cdn_$include"))
		{
			$ttl	= @include(ROOT_PATH.'apps/'.$app.'/config/ttl.php');
			$page	= loader::model('admin/page', 'page');
			$psn	= loader::model('admin/psn', 'system');
			$page_urls	= array();
			foreach ($page->ls() as $item)
			{
				$path	= $psn->parse($item['path']);
				$content	= file_get_contents($path['path']);
				if (strpos($content, $include) !== false)
				{
					$page_urls[]	= $path['url'];
				}
			}
			$cache->set("cdn_$include", $page_urls, $ttl);
		}
		return implode("@", $urls);
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