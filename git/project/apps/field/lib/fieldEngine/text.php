<?php
class fieldEngine_text extends fieldEngine
{
	public function _render($field)
	{
		$field['setting'] = unserialize($field['setting']);
		return $this->_genHtml($field);
	}

	public function _addView($pid)
	{
		$this->view->assign('pid', $pid);
		$this->view->display('field/text/add');
	}

	public function _editView($fid)
	{
		$field = $this->field->get($fid);
		$field['setting'] = unserialize($field['setting']);

		$this->view->assign($field);
		$this->view->assign('pid', $field['projectid']);
		$this->view->display('field/text/edit');
	}

	public function _genData($setting, $fid)
	{
		return '<input type="text" name="field['.$fid.']['.$setting['var'].']" style="width:'.$setting['inputsize'].'px" value="'.$setting['defaultvalue'].'" maxlength="'.$setting['maxnum'].'">';
	}

	public function _genEditData($field, $value)
	{
		$fid = $field['fieldid'];
		$setting = unserialize($field['setting']);

		$input =  '<input type="text" name="field['.$fid.']['.$setting['var'].']" style="width:'.$setting['inputsize'].'px" value="'.$value[$setting['var']].'" maxlength="'.$setting['maxnum'].'">';

		return array('name' => $setting['fieldname'], 'field' => $input);
	}
}