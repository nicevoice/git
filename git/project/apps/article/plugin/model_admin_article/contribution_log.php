<?php
//记录操作日志
#--'after_publish', 'after_unpublish', 'after_restore', 'after_pass', 'after_remove', 'after_delete'
class plugin_contribution_log extends object 
{
	private $contribution, $article, $contentid, $log;
	
	public function __construct(& $article)
	{
		$this->article = $article;
		$this->contentid = $this->article->contentid;
		$this->contribution = loader::model('contribution', 'contribution');
		$this->log = loader::model('contribution_log', 'contribution');
	}
	
	public function after_publish()
	{
		$this->status = 6;
		$this->write_log('publish');
	}
	public function after_unpublish()
	{
		$this->status = 4;
		$this->write_log('unpublish');
	}
	public function after_restore()
	{
		$this->status = 6;
		$this->write_log('restore');
	}
	public function after_pass()
	{
		$this->status = 6;
		$this->write_log('pass');
	}
	public function after_remove()
	{
		$this->status = 0;
		$this->write_log('remove');
	}
	public function after_delete()
	{
		$this->status = 0;
		$this->write_log('delete');
	}
	private function write_log($action)
	{
		$contributionid = $this->contribution->get_field('contributionid',array('contentid' => $this->contentid));
		if(!$contributionid)
		{
			return;
		}
		$data = array(
			'contributionid' => $contributionid,
			'status' => $this->status,
			'action' => $action,
			'created' => TIME,
			'createdby' => $this->_userid,
			'note' => ''
		);
		$this->log->insert($data);
	}
}