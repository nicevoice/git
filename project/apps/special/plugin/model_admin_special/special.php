<?php
class plugin_special extends object 
{
	private $special;
	
	public function __construct(& $special)
	{
		$this->special = $special;
		$this->psn = loader::model('admin/psn', 'system');
	}
	
	public function after_add()
	{
		$this->_publish();
	}
	private function _publish()
	{
		$contentid = $this->special->contentid;
		$db = factory::db();
		$special = $db->get("SELECT a.*, b.status FROM #table_special a LEFT JOIN #table_content b ON b.contentid=a.contentid WHERE a.contentid=$contentid");
		if ($special['status'] == 6)
		{
			$path = $special['path'];
			$pos = $this->psn->parse($path);
			$db->update("UPDATE #table_content SET url=? WHERE contentid=$contentid", array($pos['url']));
			
			if ($ids = $this->get_pageids($contentid))
            {
                foreach ($ids as $id)
                {
                    request(ADMIN_URL.'?app=special&controller=online&action=publish&pageid='.$id);
                }
			}
		}
	}
	public function after_edit()
	{
		$this->_publish();
	}
	public function after_publish()
	{
		$this->_publish();
	}

    public function after_unpublish()
    {
        if ($this->special->contentid && ($ids = $this->get_pageids($this->special->contentid)))
        {
            request(ADMIN_URL.'?app=special&controller=online&action=offline&pageid='.implode_ids($ids));
        }
    }

	public function before_delete()
	{
        if ($this->special->contentid && ($ids = $this->get_pageids($this->special->contentid)))
        {
            request(ADMIN_URL.'?app=special&controller=online&action=delPage&pageid='.implode_ids($ids));
        }
	}

    protected function get_pageids($contentid)
    {
        $db = & factory::db();
		$pages = $db->select("SELECT pageid FROM #table_special_page WHERE contentid=$contentid");
		if ($pages)
		{
			$ids = array();
			foreach ($pages as $p)
			{
				$ids[] = $p['pageid'];
			}
            return $ids;
		}
        return false;
    }
}