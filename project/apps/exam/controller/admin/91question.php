<?php
/**
 *  导入91题库
 *
 * @aca Silen
 */
final class controller_admin_91question extends exam_controller_abstract
{
	private $question,$mysubject,$db,$exam;
	
	function __construct(& $app)
	{
		parent::__construct($app);
		$this->question = loader::model('admin/question');
		$this->exam = loader::model('admin/exam');
		$this->db = factory::db();
        $this->mysubject = array(
            735 => 401,
            730 => 402,
            731 => 403,
            736 => 412,
            744 => 413,
            745 => 416,
            747 => 415,
            746 => 414,
            881 => 420,
            879 => 424,
            882 => 423,
            884 => 422,
            883 => 421,
            970 => 425

        );
        $this->_course = array_keys($this->mysubject);
        sort($this->_course);
	}
	public function import()
    {
        $key = $_GET['key'] ? intval($_GET['key']) : 0;
        $courseid = $this->_course[$key];
        $q1_model = loader::model('admin/91question', 'exam');
        $page = $_GET['page'] ? intval($_GET['page']) : 1;
        $pagesize = 10;
        $where = null;
        //$where = 'id="71670"';
        $list = $q1_model->page($where, "*", null, $page, $pagesize);

        foreach ($list as $k=>$val) {
            $val['nowrite'] = 1;
            $question = $q1_model->dispose($val);

        }


        $count = $q1_model->count($where);
        if ($count > $page*$pagesize) {

            $msg = '已导入--'.$page*$pagesize . '总共：'.$count;
            $this->view->assign('success', true);
            $this->view->assign('ms', 1000);
            $this->view->assign('message', $msg);
            ++$page;
            $this->view->assign('url', "?app=exam&controller=91question&action=import&page={$page}" );
            $this->view->display('showmessage', 'system');
            exit;
        } else {
            $msg = '完成';
            $this->view->assign('success', true);
            $this->view->assign('message', $msg);
            $this->view->display('showmessage', 'system');
            exit;
        }


    }
	
	
	
	//高顿题库
	function gdtk()
	{
		$page = $_GET['page'] ? intval($_GET['page']) : 1;
        $size = 10;
        $start = ($page-1)*$size;
        $offset = $page*$size;
		$sql = "SELECT * FROM gdtk ORDER BY id  LIMIT {$start},{$size}";
		$q1_model = loader::model('admin/91question', 'exam');
		$lists = $this->db->select($sql);
		$timu = $q1_model->process_gdtk($lists);
		foreach($timu as $v){
			if(is_array($v)){
				$question = $this->question->add($v);
			}
		}
		$count=42212;
		if ($count > $offset) {
            $msg = '已导入--'.$offset . '总共：'.$count ;
            $this->view->assign('success', true);
            $this->view->assign('ms', 1000);
            $this->view->assign('message', $msg);
            ++$page;
            $this->view->assign('url', "?app=exam&controller=91question&action=gdtk&page={$page}" );
            $this->view->display('showmessage', 'system');
            exit;
        } else {
            $msg = '完成';
            $this->view->assign('success', true);
            $this->view->assign('message', $msg);
            $this->view->display('showmessage', 'system');
            exit;
        }
		//测试数据用
		/* $q1_model = loader::model('admin/91question', 'exam');
		echo '<pre>';
		$sql = "SELECT * FROM gdtk ORDER BY id  LIMIT 42152,60";
		$lists = $this->db->select($sql);
		//print_r($lists);
		$timu = $q1_model->process_gdtk($lists);
		print_r($timu);
		echo '</pre>'; */
	}
	
	
	//中大题库修改
	function zdtk_edit()
	{
		$q1_model = loader::model('admin/91question', 'exam');
		$page = $_GET['page'] ? intval($_GET['page']) : 1;
        $size = 10;
        $start = ($page-1)*$size;
        $offset = $page*$size;
		$sql = "SELECT * FROM zdwx where cailiao!='' ORDER BY id  LIMIT {$start},{$size}";
		//$sql = "SELECT * FROM zdwx where id=2856";
		$lists = $this->db->select($sql);
		$timu = $q1_model->process_zdtk_edit($lists);
		if(!$timu){
			die('出错');
		}
		$count=426;
		if ($count > $offset) {
            $msg = '已修改--'.$offset . '总共：'.$count ;
            $this->view->assign('success', true);
            $this->view->assign('ms', 1000);
            $this->view->assign('message', $msg);
            ++$page;
            $this->view->assign('url', "?app=exam&controller=91question&action=zdtk_edit&page={$page}" );
            $this->view->display('showmessage', 'system');
            exit;
        } else {
            $msg = '完成';
            $this->view->assign('success', true);
            $this->view->assign('message', $msg);
            $this->view->display('showmessage', 'system');
            exit;
        }
		//测试数据用
		/* $q1_model = loader::model('admin/91question', 'exam');
		echo '<pre>';
		$sql = "SELECT * FROM zdwx ORDER BY id  LIMIT 42152,60";
		$lists = $this->db->select($sql);
		//print_r($lists);
		$timu = $q1_model->process_gdtk($lists);
		print_r($timu);
		echo '</pre>'; */
	}
	
	
	
	
	
	//中大题库
	function zdtk()
	{
		$q1_model = loader::model('admin/91question', 'exam');
		$page = $_GET['page'] ? intval($_GET['page']) : 1;
        $size = 10;
        $start = ($page-1)*$size;
        $offset = $page*$size;
		$sql = "SELECT * FROM zdwx ORDER BY id  LIMIT {$start},{$size}";
		//$sql = "SELECT * FROM zdwx where cailiao!='' ORDER BY id  LIMIT {$start},{$size}";
		//$sql = "SELECT * FROM zdwx where id=2856";
		$lists = $this->db->select($sql);
		$timu = $q1_model->process_zdtk($lists);
		foreach($timu as $v){
			if(is_array($v)){
				$question = $this->question->add($v);
			}
		}
		$count=23319;
		if ($count > $offset) {
            $msg = '已导入--'.$offset . '总共：'.$count ;
            $this->view->assign('success', true);
            $this->view->assign('ms', 1000);
            $this->view->assign('message', $msg);
            ++$page;
            $this->view->assign('url', "?app=exam&controller=91question&action=zdtk&page={$page}" );
            $this->view->display('showmessage', 'system');
            exit;
        } else {
            $msg = '完成';
            $this->view->assign('success', true);
            $this->view->assign('message', $msg);
            $this->view->display('showmessage', 'system');
            exit;
        }
		//测试数据用
		/* $q1_model = loader::model('admin/91question', 'exam');
		echo '<pre>';
		$sql = "SELECT * FROM zdwx ORDER BY id  LIMIT 42152,60";
		$lists = $this->db->select($sql);
		//print_r($lists);
		$timu = $q1_model->process_gdtk($lists);
		print_r($timu);
		echo '</pre>'; */
	}
	
	
	
	
    function import_exam_question()
    {
        $page = $_GET['page'] ? intval($_GET['page']) : 1;
        $size = 1;
        $start = ($page-1)*$size;
        $offset = $page*$size;
        $file = app_dir('exam') . '91question/';
        $data =  scandir($file);

        $q1_model = loader::model('admin/91question', 'exam');
        for ($i=$start; $i < $offset; $i++) {
            if (strpos($data[$i], '.txt') !==  false && file_exists($file.$data[$i]))$q1_model->disexam2question($file.$data[$i]);
        }
        $count = count($data);
        if ($count > $page*$size) {
            $msg = '已导入--'.$page*$size . '总共：'.$count ;
            $this->view->assign('success', true);
            $this->view->assign('ms', 1000);
            $this->view->assign('message', $msg);
            ++$page;
            $this->view->assign('url', "?app=exam&controller=91question&action=import_exam_question&page={$page}" );
            $this->view->display('showmessage', 'system');
            exit;
        } else {
            $msg = '完成';
            $this->view->assign('success', true);
            $this->view->assign('message', $msg);
            $this->view->display('showmessage', 'system');
            exit;
        }
    }



    function import_exam()
    {
		
        $page = $_GET['page'] ? intval($_GET['page']) : 1;
        $size = 5;
        $start = ($page-1)*$size;
        $offset = $page*$size;
        $file = app_dir('exam') . '91up/';
        echo $file;
        $data =  scandir($file);
        $q1_model = loader::model('admin/91question', 'exam');
        for ($i=$start; $i < $offset; $i++) {
            if (strpos($data[$i], '.txt') !==  false && file_exists($file.$data[$i]))$q1_model->disexam($file.$data[$i]);
        }
        $count = count($data);
        if ($count > $page*$size) {
            $msg = '已导入--'.$page*$size . '总共：'.$count ;
            $this->view->assign('success', true);
            $this->view->assign('ms', 1000);
            $this->view->assign('message', $msg);
            ++$page;
            $this->view->assign('url', "?app=exam&controller=91question&action=import_exam&page={$page}" );
            $this->view->display('showmessage', 'system');
            exit;
        } else {
            $msg = '完成';
            $this->view->assign('success', true);
            $this->view->assign('message', $msg);
            $this->view->display('showmessage', 'system');
            exit;
        }
    }

}