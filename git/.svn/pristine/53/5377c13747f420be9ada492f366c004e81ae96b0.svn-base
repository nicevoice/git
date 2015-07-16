<?php
/**
 * cmstop_spider_history
 *   historyid
 *   title
 *   url
 *   created
 */
class model_admin_spider_history extends model
{
	function __construct()
	{
		parent::__construct();
		$this->_table = $this->db->options['prefix'].'spider_history';
		$this->_primary = 'historyid';
		$this->_fields = array('historyid','title','url','created');
		
		$this->_readonly = array('historyid','created');
		$this->_create_autofill = array('created'=>TIME);
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array();
	}
	
	function add($url, $charset)
	{
		$content = file_get_contents($url);
		$title = '--';
		if ($content)
		{
			$_charset = strtoupper(config('config','charset'));
			if (strtoupper($charset) != $_charset)
			{
				$content = str_charset($charset, $_charset, $content);
			}
			if (preg_match("/(?<=<title>).*?(?=<\/title>)/is", $content, $m))
			{
				$title = $m[0];
			}
		}
		
		$data = array(
			'title' => $title,
			'url'   => $url
		);
		try {
			return $this->insert($data);
		} catch (Exception $e) {}
	}
	
	function suggest($q, $size=30)
	{
		$sql = "SELECT * FROM #table_spider_history ".
			($q ? "WHERE `url` LIKE '%$q%' or `title` LIKE '%$q%'" : '')
			." ORDER BY `historyid` DESC";
		return $this->db->limit($sql, $size);
	}
}