<?php
class model_admin_section_url extends model
{
	private $section = null, $page = null;
	function __construct()
	{
		parent::__construct();
		$this->_table = $this->db->options['prefix'].'section_url';
		$this->_primary = 'sectionid';
		$this->_fields = array('sectionid', 'url');

		$this->_readonly = array('sectionid');
		$this->_create_autofill = array();
		$this->_update_autofill = array();
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array();

		$this->section = loader::model('admin/section', 'page');
		$this->page = loader::model('admin/page', 'page');
	}

	public function save($sectionid, $datas)
	{
		$this->delete($sectionid);
		foreach($datas as $data)
		{
			foreach($data as $r)
			{
				$url = $r['url'];
				$this->add($sectionid, md5($url));
			}
		}
	}

	public function add($sectionid, $url)
	{
		return $this->insert(array('sectionid' => $sectionid, 'url' => $url));
	}

	/**
	 * 返回所在区块URl
	 * @param 文章URL地址
	 * @return URL / #
	 */
	public function get_section_url($url)
	{
		$return = array();
		$pageurl = ADMIN_URL.'?app=page&controller=page&action=view&pageid=';
		$sectionurl = ADMIN_URL.'?app=page&controller=section&action=view&sectionid=';

		$sections = $this->gets_by('url', md5($url), 'sectionid');

		foreach($sections as $section)
		{
			$sectioninfo = $this->section->get_by('sectionid', $section['sectionid'], 'pageid, name');
			$pagename = array_shift($this->page->get_by('pageid', $sectioninfo['pageid'], 'name'));
			$return[] = array(
				'pagename' => $pagename,
				'pageid' => $sectioninfo['pageid'],
				'pageurl' => $pageurl.$sectioninfo['pageid'],
				'sectionname' => $sectioninfo['name'],
				'sectionurl' => $sectionurl.$section['sectionid'],
				'sectionid' => $section['sectionid']
			);
		}
		return $return;
	}
}
