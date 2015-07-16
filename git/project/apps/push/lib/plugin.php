<?php
abstract class push_plugin
{
	protected $_charset = 'utf-8';
	protected $_to_charset, $_fields, $_convert, $_primary, $_primaryKey;
	public function __construct($rule, $charset)
	{
		$this->_charset = strtoupper(config('config','charset'));
		
		$this->_to_charset = strtoupper($charset);
		$this->_convert = ($this->_to_charset != $this->_charset);
		$this->_fields = $rule['fields'];
		$this->_defaults = $rule['defaults'];
		$this->_linkRule = $rule['linkrule'];
		$this->_primary = $rule['primary'];
		$primary = explode('.', $this->_primary);
		$this->_primaryKey = array_pop($primary);
	}
	public function getList($rowset)
	{
		$data = array();
		foreach ($rowset as $r)
		{
			$data[] = $this->getOne($r);
		}
		return $data;
	}
	/**
	 * only get fields for read
	 */
	public function getOne($row)
	{
		return array(
			'guid'=>$row[$this->_primaryKey],
			'title'=>$this->getTitle($row),
			'link'=>$this->getLink($row),
			'visitnum'=>$this->getVisitNum($row),
			'replynum'=>$this->getReplyNum($row),
			'length'=>$this->getLength($row),
			'tips'=>$this->getTips($row)
		);
	}
	/**
	 * only get fields for push to cmstop table
	 */
	public function getDetails($row)
	{
		return array(
			'guid'=>$row[$this->_primaryKey],
			'link'=>$this->getLink($row),
			'title'=>$this->getTitle($row),
			'content'=>$this->getContent($row),
			'author'=>$this->getAuthor($row),
			'tags'=>$this->getTags($row),
			'description'=>$this->getDescription($row),
			'source'=>$this->getSource($row),
			'pubdate'=>$this->getPubdate($row)
		);
	}
	
	protected function _correct_charset($str)
	{
		if (!$this->_convert)
		{
			return $str;
		}
		return str_charset($this->_to_charset, $this->_charset, $str);
	}
	
	
	public function getLink($row)
	{
		if (empty($this->_linkRule))
		{
			return "about:blank";
		}
		return preg_replace('/\{(\w+)\}/e','$row["\1"]', $this->_linkRule);
	}
	public function getTitle($row)
	{
		$val = '';
		$f = $this->_fields['title'];
		if (!empty($f))
		{
			$val = $this->_correct_charset($row[$f]);
		}
		return $val;
	}
	public function getLength($row)
	{
		return strlen($this->getContent($row));
	}
	public function getTips($row)
	{
		$val = array(
			'发布时间：'.date('Y-m-d H:i:s', $this->getPubdate()),
			'作者：'.$this->getAuthor($row),
			'Tags：'.$this->getTags($row),
			'来源：'.$this->getSource($row),
			'字节：'.$this->getLength($row),
			'访问数：'.$this->getVisitNum($row),
			'回复数：'.$this->getReplyNum($row)
		);
		return implode('&lt;br/&gt;',$val);
	}
	public function getContent($row)
	{
		$val = '';
		$f = $this->_fields['content'];
		if (!empty($f))
		{
			$val = $this->_correct_charset($row[$f]);
		}
		return $val;
	}
	public function getPubdate($row)
	{
		$val = 0;
		$f = $this->_fields['pubdate'];
		if (!empty($f))
		{
			$val = intval($row[$f]);
		}
		return empty($val) ? TIME : $val;
	}
	public function getAuthor($row)
	{
		$val = '';
		$f = $this->_fields['author'];
		if (!empty($f))
		{
			$val = $this->_correct_charset($row[$f]);
		}
		return empty($val) ? $this->_defaults['author'] : $val;
	}
	public function getTags($row)
	{
		$val = '';
		$f = $this->_fields['tags'];
		if (!empty($f))
		{
			$val = $this->_correct_charset($row[$f]);
		}
		return $val;
	}
	public function getDescription($row)
	{
		$val = '';
		$f = $this->_fields['descr'];
		if (!empty($f))
		{
			$val = $this->_correct_charset($row[$f]);
		}
		return $val;
	}
	public function getSource($row)
	{
		$val = '';
		$f = $this->_fields['source'];
		if (!empty($f))
		{
			$val = $this->_correct_charset($row[$f]);
		}
		return empty($val) ? $this->_defaults['source'] : $val;
	}
	public function getVisitNum($row)
	{
		$val = 0;
		$f = $this->_fields['visitnum'];
		if (!empty($f))
		{
			$val = $this->_correct_charset($row[$f]);
		}
		return intval($val);
	}
	public function getReplyNum($row)
	{
		$val = 0;
		$f = $this->_fields['replynum'];
		if (!empty($f))
		{
			$val = $this->_correct_charset($row[$f]);
		}
		return intval($val);
	}
}