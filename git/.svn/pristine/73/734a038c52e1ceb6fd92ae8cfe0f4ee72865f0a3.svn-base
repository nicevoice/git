<?php

class plugin_option extends object 
{
	private $vote, $option;
	
	public function __construct(& $vote)
	{
		$this->vote = $vote;
		$this->option = loader::model('option','vote');
	}
	
	public function before_vote()
	{
		if (is_array($this->vote->optionid))
		{
			$optionid = array();
			foreach ($this->vote->optionid as $id)
			{
				$id = intval($id);
				if ($id) 
				{
					if ($this->option->set_inc('votes', "optionid=$id AND contentid={$this->vote->contentid}"))
					{
						$optionid[] = $id;
					}
				}
			}
			$this->vote->votes = count($optionid);
			$this->vote->optionid = $optionid;
		}
		else 
		{
			$id = intval($this->vote->optionid);
			if ($id) 
			{
				if ($this->option->set_inc('votes', "optionid=$id AND contentid={$this->vote->contentid}"))
				{
					$this->vote->votes = 1;
					$this->vote->optionid = $id;
				}
			}
		}
	}

	public function after_get()
	{
		$option = $this->option->ls($this->vote->contentid);
		foreach ($option as $k=>$r)
		{
			$option[$k]['percent'] = $this->vote->data['total'] ? round($r['votes']/$this->vote->data['total']*100, 2) : 0;
		}
		$this->vote->data['option'] = $option;
	}
}