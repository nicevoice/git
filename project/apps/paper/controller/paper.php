<?php
class controller_paper extends paper_controller_abstract
{
	//输出“期”和"报纸"下拉框
	function pnEdition()
	{
		$pid = intval($_GET['pid']);
		if(!$pid) return false;
		
		$data['papers'] = table('paper', null, 'paperid, name, url');
		$db = & factory::db();
		$sql = "SELECT editionid, total_number, url FROM #table_paper_edition WHERE paperid = $pid";
		$data['editions'] = $db->select($sql);
		$data = $this->json->encode($data);
		exit($_GET['jsoncallback']."($data);");
	}
}