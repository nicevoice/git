<?php

class plugin_status extends object 
{
	private $space;
	
	public function __construct(& $space)
	{
		$this->space = $space;
	}

	public function after_check()
	{
		// 审核通过后，将普通投稿写入专栏ID，以使专栏可以看到以前的普通投稿
		if($this->space->status == 3 && $this->space->spaceid)
		{
			$where = "`status`=3 AND `spaceid` IN (" .$this->space->spaceid .")";
			$ids = $this->space->select($where);
			$db = & factory::db();
			foreach($ids as $r)
			{
				$db->exec("UPDATE `#table_content` SET spaceid=" .$r['spaceid'] ." WHERE contentid IN (SELECT contentid FROM `#table_contribution` WHERE createdby=" .$r['userid'] ." AND contentid>0)");
			}
			$db = NULL;
		}
	}

}