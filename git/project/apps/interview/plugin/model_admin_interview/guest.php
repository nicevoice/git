<?php
class plugin_guest extends object 
{
	private $interview, $guest;
	
	public function __construct(& $interview)
	{
		$this->interview = $interview;
		$this->guest = loader::model('admin/guest','interview');
	}
	
	public function after_add()
	{
		if (!isset($this->interview->data['guest']) || empty($this->interview->data['guest'])) return false;
	
		$sort = 0;
		foreach ($this->interview->data['guest'] as $k=>$r)
		{
			$r['contentid'] = $this->interview->contentid;
			$r['sort'] = $sort;
			$this->guest->add($r);
			$sort++;
		}
	}
	
	public function after_edit()
	{
		if (!isset($this->interview->data['guest']) || empty($this->interview->data['guest'])) return false;
		
		$guestid = array();
		$sort = 0;
		foreach ($this->interview->data['guest'] as $k=>$r)
		{
			$r['sort'] = $sort;
			if ($r['guestid'])
			{
				$this->guest->edit($r['guestid'], $r);
				$guestid[] = $r['guestid'];
			}
			else 
			{
				$r['contentid'] = $this->interview->contentid;
				$guestid[] = $this->guest->add($r);
			}
			$sort++;
		}
		$this->guest->delete_by($this->interview->contentid, $guestid);
	}
	
	public function after_get()
	{
		$this->interview->data['guest'] = $this->guest->ls($this->interview->contentid);
	}
	
	public function after_delete()
	{
		$this->guest->rm($this->interview->contentid);
	}
}