<?php
class controller_digg extends digg_controller_abstract
{
	private $digg,$digg_log;

	function __construct(&$app)
	{
		parent::__construct($app);
		$this->digg = loader::model('digg');
		$this->digg_log = loader::model('digg_log');
		$this->digg_log->setting = $this->setting;
	}

	function digg()
	{
		$contentid = intval($_GET['contentid']);
		$flag = intval($_GET['flag']);
		if(isset($_GET['flag']))
		{
			$data = $this->_update($contentid, $flag);
		}
		else
		{
			$r = $this->digg->get($contentid,'supports,againsts');
			$data = $r ? array('done'=>$this->digg_log->is_done($contentid), 'supports'=>$r['supports'], 'againsts'=>$r['againsts']) : array('done'=>0, 'supports'=>0, 'againsts'=>0);
			$data = $this->json->encode($data);
		}
		exit($_GET['jsoncallback']."($data);");
	}

	function _update($contentid, $flag)
	{
		$flag = ($flag == 1)?1:0;
		if ($this->digg_log->is_done($contentid)) return 0;
		$r = $this->digg->get($contentid,'*');
		if ($r)
		{
			if ($flag)
			{
				$total = $r['supports']+1;
				$sql_data = array('supports' => $total);
			}
			else
			{
				$total = $r['againsts']+1;
				$sql_data = array('againsts' => $total);
			}
			$this->digg->update($sql_data, "`contentid`='$contentid'");
		}
		else
		{
			if ($flag)
			{
				$data = array(
					'contentid'=>$contentid,
					'supports'=>1,
					'againsts'=>0);
			}
			else
			{
				$data = array(
					'contentid'=>$contentid,
					'supports'=>0,
					'againsts'=>1);
			}
			$this->digg->add($data);
			$total = 1;
		}
		$data_log = array('contentid' => $contentid, 'flag' => $flag);
		$this->digg_log->add($data_log);
		return  $total;
	}
}