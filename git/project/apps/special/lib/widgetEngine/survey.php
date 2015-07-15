<?php
class widgetEngine_survey extends widgetEngine
{
	public function _render($widget)
	{
		$data = decodeData($widget['data']);
		$contentid = $data['contentid'];
		$r = loader::model('admin/survey','survey')->get($contentid);
		if (!$r || $r['status'] != 6)
		{
			return 'survery not exist';
		}
		$r['questions'] = loader::model('admin/question','survey')->ls($contentid);
		return $this->_genHtml(null, $r);
	}

	public function _addView()
	{
		$this->view->display('widgets/survey/form');
	}

	public function _editView($widget)
	{
		$this->view->assign('data', decodeData($widget['data']));
		$this->view->display('widgets/survey/form');
	}
}