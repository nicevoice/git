<?php
class widgetEngine_activity extends widgetEngine
{
	public function _render($widget)
	{
		$data = decodeData($widget['data']);
		$contentid = $data['contentid'];
		$this->activity = loader::model('admin/activity','activity');
		$r = $this->activity->get($contentid, '*', 'show');
		$r['starttime'] = strtotime($r['starttime']);
		$r['endtime'] = !empty($r['endtime'])?strtotime($r['endtime']):'';
		$r['signstart'] = strtotime($r['signstart']);
		$r['signend'] = !empty($r['signend'])?strtotime($r['signend']):'';
		if (!$r || $r['status'] != 6)
		{
			return 'activity not exist';
		}
		$this->sign = loader::model('admin/sign','activity');
		$r['signed'] = $this->sign->ls("contentid = $contentid AND  state = 1");
		
		return $this->_genHtml(null, $r);
	}
	public function _addView()
	{
		$this->view->display('widgets/activity/form');
	}
	public function _editView($widget)
	{
		$this->view->assign('data', decodeData($widget['data']));
		$this->view->display('widgets/activity/form');
	}
}