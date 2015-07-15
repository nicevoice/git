<?php
//记录操作日志
#--before_add, after_add, before_edit, after_edit, after_delete, after_submit, after_remove, after_reject, after_cancel, after_publish
class plugin_contribution_log extends object 
{
	private $contribution,$log;
	
	public function __construct(& $contribution)
	{
		$this->contribution = $contribution;
		$this->log = loader::model('contribution_log', 'contribution');
	}
	
	public function before_add()
	{
		
	}
	public function after_add()
	{
		$this->write_log('add');
	}
	public function before_edit()
	{
		
	}
	public function after_edit()
	{
		$this->write_log('edit');
	}
	public function after_delete()
	{
		//$this->write_log('delete'); //编辑完全删除该稿件
	}
	public function after_submit()
	{
		$this->write_log('submit');
	}
	public function after_remove()
	{
		$this->write_log('remove');
	}
	public function after_reject()
	{
		$this->write_log('reject');
	}
	public function after_cancel()
	{
		$this->write_log('cancle');
	}
	public function after_publish()
	{
		$this->write_log('publish');
	}
	
	private function write_log($action)
	{
		if(!$this->contribution->contributionid)
		{
			return;
		}
		$data = array(
			'contributionid' => $this->contribution->contributionid,
			'status' => $this->contribution->status,
			'action' => $action,
			'created' => TIME,
			'createdby' => $this->_userid,
			'note' => $this->contribution->note
		);
		$this->log->insert($data);
	}
}