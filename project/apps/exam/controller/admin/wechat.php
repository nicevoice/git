<?php
/**
 * 问卷管理
 *
 * @aca 问卷管理
 */
final class controller_admin_wechat extends exam_controller_abstract
{
	private $exam, $pagesize = 15, $message;

	function __construct(& $app)
	{
		parent::__construct($app);
        $this->message = loader::model('exam_wechat_messages', 'exam');

	}
    public function setting()
    {

        $file = 'wechat_seo';
        $r = common_data($file);
        if ($this->is_ajax() && $this->is_post()) {
            common_data_set($file, $_POST);
            echo $this->json->encode(array('state'=>true, 'info'=>'添加成功'));exit;
        }
        $this->view->assign($r);
        $this->view->assign('head', array('title' => '微信版设置'));
        $this->view->display('wechat/setting');
    }

    public function menu()
    {

        $this->view->assign('head', array('title' => '微信版菜单设置'));
        $this->view->display('wechat/menu');
    }
    public function menu_add()
    {
        $file = 'wechat_menu';
        $r = common_data($file);
        if ($this->is_ajax() && $this->is_post()) {
            $menuid = $_POST['menuid'];
            foreach ($r as $k => $v) {
                if ($v['sort'] >= $_POST['sort']) {
                    $r[$k]['sort'] = $v['sort'] + 1;
                }
            }
            if ($menuid) {
                $r[$menuid] = $_POST;
            } else {
                $n = is_array($r) ? count($r) + 1 : 1;
                $_POST['menuid'] = $n;
                $r[$n] = $_POST;
            }
            common_data_set($file, $r);
            echo $this->json->encode(array('state'=>true, 'data' => $_POST));exit;
        }
        $menuid = $_GET['menuid'];
        if ($menuid) {
            foreach ($r as $v) {
                if ($v['menuid'] == $menuid)$this->view->assign($v);
            }
        }
        $this->view->display('wechat/menu_add');
    }
    public function menu_page()
    {
        $file = 'wechat_menu';
        $r = common_data($file);
        foreach ($r as $v) {
            $_r[] = $v;
        }
        $_r = $_r ? $_r : array();
        $result = array('total'=>count($r), 'data'=>$_r);
        echo $this->json->encode($result);
    }
    public function menu_delete()
    {
        $menuid = $_GET['id'];
        $file = 'wechat_menu';
        $r = common_data($file);
        if ($menuid) {
            foreach ($r as $k=>$v) {
                if ($v['menuid'] == $menuid)unset($r[$k]);
            }
        }
        common_data_set($file, $r);
        echo $this->json->encode(array('state'=>true));
    }

    public function member()
    {
        $this->view->assign('head', array('title' => '微信版用户'));
        $this->view->display('wechat/member');
    }
    public function member_page()
    {

        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $pagesize = isset($_GET['pagesize']) ? intval($_GET['pagesize']) : $this->pagesize;
        $db = factory::db();
        $offset = ($page-1)*$pagesize;
        $order = $_GET['order'] ? $_GET['order'] : 'wm.logtime DESC';
        $sql =  "SELECT wm.*,m.nickname FROM #table_exam_wechat_member wm, #table_wechat_member m WHERE wm.openid=m.openid ORDER BY $order  LIMIT {$offset},{$pagesize}";
        $list = $db->select($sql);
        foreach ($list as $k=>$v) {
            $list[$k]['nickname'] = is_base64_encode($v['nickname']) ?  base64_decode($v['nickname']) : $v['nickname'];
            $list[$k]['logtime'] = time_format($v['logtime']);
        }
        $_r = $list ? $list : array();
        $result = array('total'=>loader::model('exam_wechat_member', 'exam')->count(), 'data'=>$_r);
        echo $this->json->encode($result);
    }
}