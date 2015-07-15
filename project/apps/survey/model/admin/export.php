<?php

class model_admin_export extends model 
{
	private $exprot;
	
	function __construct()
	{
        parent::__construct();
	}
	
	function getanswers($contentid){
		$r= $this->db->select("SELECT * FROM `#table_survey_answer` WHERE `contentid` = $contentid order by created desc");
		return $r;
	}
	function getrecodes($answerid)
	{
 
		$r = $this->db->select("SELECT * FROM `#table_survey_answer_record` WHERE `answerid` = $answerid");
		return $r;
	}
	
	function getoptions($answerid)
	{
		$r = $this->db->select("SELECT * FROM `#table_survey_answer_option` WHERE `answerid` = $answerid ");
		return $r;
	}
	
	function toexcel($title,$head,$rows,$author = 'CMSTOP',$company = 'CMSTOP')
	{
		import('helper.xls');
		$x1 = new XLS();
		$x1->SetAuthor($author);
		$x1->SetCompany($company);
		$sheetname = substr($title,0,-4);
		$x1->AddSheet($sheetname);
		$x1->NewStyle('bold');
		$x1->StyleSetFont(0, 0, 0, 1, 0, 0);
		$x1->SetActiveStyle('blod');		
		$i = 1;
		foreach ($head as $k => $v)
		{
			$x1->Text(1,$i++,$v);
		}
		
		$x1->NewStyle('normal');
    	$x1->StyleSetFont(0, 0, 0, 0, 0, 0);
        $x1->SetActiveStyle('normal');
        
		foreach ($rows as $k => $v)
		{
			$i = 1;
			foreach ($v as $k1=>$v1)
			{
				$x1->Text($k+2,$i++,empty($v1)?' ':$v1);
			}
		}
		$x1->Output(substr($sheetname,0,80).'.xls');
	}
	
	function array_colrow($data)
	{
		$res = array();
		foreach($data as $k1=>$v1)
		{
			foreach($v1 as $k2 => $v2)
			{
				$res[$k2][$k1] = $v2;
			}
		}
		return $res;
	}
}