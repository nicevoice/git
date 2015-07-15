<?php
loader::import('lib.discuzcode');
class push_plugin_discuz extends push_plugin
{
	public function getTips($row)
	{
		$val = array(
			'发布时间：'.date('Y-m-d H:i:s', $this->getPubdate()),
			'作者：'.$this->getAuthor(),
			'Tags：'.$this->getTags(),
			'rate：'.$row['rate'],
			'useip：'.$row['useip'],
			'status：'.$row['status'],
		);
		return implode('&lt;br/&gt;',$val);
	}
	public function getContent($row)
	{
		return discuzcode(parent::getContent($row),$row['bbcodeoff'],$row['htmlon']);
	}
}