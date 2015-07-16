<?php
class widgetEngine_code extends widgetEngine
{
	public function _render($widget)
	{
		$code = $widget['data'];
		$orig_dir = $this->template->dir;
		$tmp_template = 'tmp_'.md5($code);
        $this->template->set_dir(CACHE_PATH);
        $tplfile = CACHE_PATH . $tmp_template;
		write_file($tplfile, $code);
		$html = $this->template->fetch($tmp_template);
		@unlink($tplfile);
		$this->template->set_dir($orig_dir);
		return $html;
	}
	public function _addView()
	{
		$this->view->display('widgets/code/edit');
	}
	public function _genData($post)
	{
		return $post['html'];
	}
	public function _editView($widget)
	{
		$this->view->assign('html', $widget['data']);
		$this->view->display('widgets/code/edit');
	}
}