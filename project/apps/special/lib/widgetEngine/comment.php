<?php
class widgetEngine_comment extends widgetEngine
{
	private $rows = 10;
	public function _render($widget)
	{
		$data = decodeData($widget['data']);
		if(empty($data['rows'])) $data['rows'] = $this->rows;
		$r = array(
			'commentUrl' => APP_URL.url('comment/comment/add'),
			'loginUrl' => APP_URL.url('member/index/login'),
			'logoutUrl' => APP_URL.url('member/index/logout'),
			'dataUrl' => APP_URL.url('comment/comment/get'),
			'pagesize' => $data['rows'],
			'topicid' => $data['topicid'] ? $data['topicid'] : $this->_getTopicId(),
			'needLogin' => setting('comment', 'islogin')
		);
		$r['options'] = $this->json->encode($r);
		$this->template->assign('isseccode',setting('comment', 'isseccode'));
		return $this->_genHtml(null, $r);
	}
	
	public function _genData($post, $widget = null)
	{
		return encodeData(array(
			'topicid'=> $this->_getTopicId(),
			'rows'=> $post['data']['rows']
		));
	}
	
	public function _addView()
	{
		$this->view->assign('rows', $this->rows);
		$this->view->display('widgets/comment/form');
	}
	
	public function _editView($widget)
	{
		$this->view->assign('rows', $this->rows);
		$this->view->assign('data', decodeData($widget['data']));
		$this->view->display('widgets/comment/form');
	}

    protected function _getTopicId()
    {
        if ($contentid = intval($_REQUEST['contentid']))
        {
            $db = factory::db();
		    $r = $db->get("SELECT `topicid` FROM `#table_content` WHERE `contentid` = " . $contentid);
            return $r ? $r['topicid'] : 0;
        }

        return 0;
    }
}

