<?php

class plugin_log extends object 
{
	private $vote, $log, $log_data;
	
	public function __construct(& $vote)
	{
		$this->vote = $vote;
		$this->log = loader::model('vote_log','vote');
		$this->log_data = loader::model('log_data','vote');
	}

	public function after_vote()
	{
		$logid = $this->log->insert(array('contentid'=>$this->vote->contentid));
		if($logid)
		{
			if (is_array($this->vote->optionid))
			{
				foreach ($this->vote->optionid as $optionid)
				{
					$this->log_data->insert(array('logid'=>$logid, 'optionid'=>$optionid));
				}
			}
			else 
			{
				$this->log_data->insert(array('logid'=>$logid, 'optionid'=>$this->vote->optionid));
			}
		}
	}
}