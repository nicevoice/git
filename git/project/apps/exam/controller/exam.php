<?php

class controller_exam extends exam_controller_abstract
{
	private $exam,$cache,$answer,$answer_option,$db,$content,$childids,$favorite;
	
	function __construct(& $app)
	{
		parent::__construct($app);

		$this->content = loader::model('content', 'system');
		$this->exam = loader::model('exam', 'exam');
		$this->answer = loader::model('answer', 'exam');
		$this->favorite = loader::model('exam_favorite', 'exam');
		$this->answer_option = loader::model('answer_option', 'exam');
        $this->cache = & factory::cache();
        $this->db = & factory::db();


	}

    function index()
    {
        $key = 'exam_daylist';
        if (!$lists = $this->cache->get($key)) {
            $lists = $this->exam->daylist($this->exam_catid);
            foreach ($lists as $val) {
                $contentids[] = $val['contentid'];
            }
            if ($contentids) {
                $avatars = $this->answer->get_answer_createdby($contentids);
                foreach ($lists as $k=>$val) {
                    $lists[$k]['avatar'] = $avatars[$k];
                }
            }
            $this->cache->set($key, $lists, 3);
        }
        $this->template->assign('lists', $lists);
        $this->template->display('exam/index');
    }
    function ajaxdata()
    {
        $contentid = $_GET['contentid'];
        $ckey = md5('ajaxdata'. $contentid);
        if (!$list = $this->cache->get($ckey)) {
            $contentids = explode(',', $contentid);
            foreach ($contentids as $id) {
                intval($id) && $ids[] = intval($id);
            }
            $list = $this->exam->day_ajaxdata(implode(',', $ids));
            $this->cache->set($ckey, $list, 10);
        }
        dieJson($list);
    }

    public function verificdn()
    {
        //关闭请求，因为每个用户的浏览器都有一定的缓存，会造成一直请求CDN
        /*if ($_GET['dateline'] != date('Ymd', time())){
            $urls[] = 'http://www.kuaiji.com/exam?utm_source=qzone';
            $urls[] = 'http://www.kuaiji.com/exam?utm_source=qqqun';
            $urls[] = 'http://www.kuaiji.com/exam?utm_source=snweibo';
            $urls[] = 'http://www.kuaiji.com/exam?utm_source=edm';
            $urls[] = 'http://www.kuaiji.com/exam?utm_source=txweibo';
            $urls[] = 'http://www.kuaiji.com/exam?utm_source=weixin';
            $urls[] = 'http://www.kuaiji.com/exam/index.html';
            $urls[] = 'http://www.kuaiji.com/exam/';
            $urls[] = 'http://www.kuaiji.com/project.html';
            $urls[] = 'http://www.kuaiji.com/exam';
            $_urls = implode('%0D%0A', $urls);
            $url	= "http://ccms.chinacache.com/index.jsp?user=kuaiji&pswd=test@0423&ok=ok&urls=$_urls";
            $retList = $this->getHttp($url);
            if (strpos($retList['html'], "whatsup: content=\"succeed\"") === false)
            {
                record2url($urls);
            }
            log2day($urls, 'admin_cdn', 'username:' . $this->_username.' userid:'.$this->_userid);
        }*/
        header('Content-Type: application/javascript');
        dieJson(time());
    }
    private function getHttp($url)
    {
        $userAgent = 'Mozilla/4.0+(compatible;+MSIE+6.0;+Windows+NT+5.1;+SV1)';
        $referer = $url;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);			//设置访问的url地址
        curl_setopt($ch, CURLOPT_HEADER, 1);				//设置返回头部，用于内容编码判断
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);				//设置超时
        curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);	//用户访问代理 User-Agent
        curl_setopt($ch, CURLOPT_REFERER, $referer);		//设置 referer
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip');			//设置客户端是否支持 gzip压缩
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);		//跟踪301,已关闭
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);		//返回结果
        if(stripos($this->url,'https:') !== false){				//加载SSL公共证书，请求HTTPS访问
            # Below two option will enable the HTTPS option.
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        }
        $html = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return array('html'=>$html, 'code'=>$httpcode);
    }
    function getexam()
    {
        //printR($_GET);
        $contentid = intval($_GET['contentid']);
        $ckey = md5('ajaxdata'. $contentid);
        if (!$list = $this->cache->get($ckey)) {
            $list = $this->db->get("SELECT count FROM #table_exam WHERE contentid={$contentid}");
            $this->cache->set($ckey, $list, 50);
        }
        dieJson($list);

    }

    /**
     * 项目列表
     */
    function project()
    {

		$cp = $this->app->args['cp'];
		$this->template->assign('cp', $cp);
		$data = $this->template->fetch('exam/project.html');
		//$filename = WWW_PATH . 'www/exam/' .$cp . '.html';
		//folder::create(dirname($filename));
        //write_html($filename, $data);
		die($data);							
    }

    /**
     * 真题模考
     */
    public function really()
    {
        $page = $_GET['page'] ? intval($_GET['page']) : 1;
        $pagesize = 7;
        $subjectid = $_GET['subjectid'] ? intval($_GET['subjectid']) : $this->pro_ids['subjectid'];

        $lists = $this->exam->get_ls("c.typeid={$subjectid}", 'c.title,c.contentid,c.published,e.count', $page,  $pagesize);
        $count = $this->content->count(array('typeid'=>$subjectid, 'status'=>6, 'modelid'=>modelid('exam')));
        foreach($lists as $list) {
            $contentids[] = $list['contentid'];
        }
        $countList = $this->answer->get_answer_by_contentid($contentids);
        $this->template->assign('count', $count);
        $this->template->assign('countList', $countList);
        $this->template->assign('lists', $lists);
        $this->template->assign('pageSize', $pagesize);
        $this->template->display('exam/really');

    }
    public function really_ls()
    {
        $page = $_GET['page'] ? intval($_GET['page']) : 1;
        $pagesize = 7;
        $subjectid = $_GET['subjectid'] ? intval($_GET['subjectid']) : $this->pro_ids['subjectid'];
        $lists = $this->exam->get_ls("c.typeid={$subjectid}", 'c.title,c.contentid,c.published,e.count', $page,  $pagesize);
        foreach($lists as $k=> $list) {
            $contentids[] = $list['contentid'];
            $lists[$k]['date'] = date('Y-m-d', $list['published']);
            $lists[$k]['url'] = kuaiji_url('show' , array('examid'=>md5($list['contentid'])) , 'exam');
        }
        $countList = $this->answer->get_answer_by_contentid($contentids);
        $result = array('state'=>true, 'info'=>$lists, 'count'=>$countList);
        dieJson($result);
        /*$json = $this->json->encode($result);
        echo "{$_GET['jsoncallback']}($json);";*/
    }
    public function get_childids($knowledge)
    {
        foreach ($knowledge as $know) {
            if ($know['childs']){
                $this->get_childids($know['childs']);
            }
            $this->childids[$know['proid']] = $know;
        }
    }
    /**
     * 专项练习
     */
    public function special()
    {
        $subjectid = $_GET['subjectid'] ? intval($_GET['subjectid']) : 401;
        $knowledges =  config::get('exam' , 'knowledge');
        $knowledgeid = $knowledges[$subjectid];
        $knowledge = get_property_child($knowledgeid);
        $this->get_childids($knowledge);
        $this->childid[$knowledgeid] = $this->propertys[$knowledgeid];
        $counts = $this->exam->get_knowledges_count($subjectid, array_keys($this->childids));
        $my_counts = $this->exam->get_knowledges_my_count(array_keys($this->childids));
        $knowledge = $this->exam->bubbleSort($knowledge);//冒泡排序
        $this->template->assign('my_counts', $my_counts);
        $this->template->assign('counts', $counts);
        $this->template->assign('knowledge', $knowledge);
        $this->template->display('exam/special');

    }

    /**
     * 提交试卷
     */
    function finish()
    {

        $contentid = trim(intval($_POST['examid']));
        $func      = $_POST['answerid'] ? 'edit' : 'add';
        $_POST['plantform_id']   =1;
        if ($id = $this->answer->$func($contentid, $_POST)){
            $url = $_POST['isfinish'] ?  kuaiji_url('action' , array('action'=>'project') , 'exam') :  kuaiji_url('my/action_id' , array('action'=>'report', 'id'=>$id) , 'exam');
            $this->template->ext ='';
            $this->showmessage('保存成功！', $url, 1000, true);
        } else {
            $result = array('state'=>false, 'error'=>$this->answer->error());
            if ($_GET['debug'] == 1)echo $this->answer->error();
            $this->showmessage('网络延迟，保存失败！', '', 1000, false);
        }
        dieJson($result);
        /*$json = $this->json->encode($result);
        echo "{$_GET['jsoncallback']}($json);";*/
    }

/*    function show()
    {
        $id = $_GET['id'];
        $exams = $this->exam->get($id);
        //printR($exams);
        $this->template->assign($exams);
        $this->template->display('exam/show');
    }*/

    /***********************************************************************
    分割线
     **/

    /**
     * 题库页面404生成
     */


    function create_html2exam()
    {

        $uri = explode('/', $_SERVER['REQUEST_URI']);
        switch ($uri[2]) {
            case 'question' :

                if(preg_match('/^([a-zA-Z0-9]+)?\.html/' , $uri[3] , $arr)){

                    $md5id = $arr[1];

                    $question = $this->db->get("SELECT questionid FROM #table_exam_question WHERE md5id='{$md5id}' AND bandid=0");

                    if (!$question)show_404();
                    $questionid = $question['questionid'];

                    $question_model = loader::model('question', 'exam');
                    $question = $question_model->get($questionid);

                    //printR($question);
                    if ($question) {

                        if ($question['type'] == 'read') {
                            $question['questions'] = $question_model->ls(array('bandid'=>$questionid));
                        }
                        $question['description'] = htmlspecialchars_decode($question['description']);
                        $question['analysis'] = htmlspecialchars_decode($question['analysis']);
                        $template = factory::template();

                        $template->assign($question);
                        $data = $template->fetch('exam/question/show');
                        define('EXAM_HTML_PATH', WWW_PATH . 'www/exam' . DS);
                        $dir = splitdir($question['md5id']);

                        $filename = EXAM_HTML_PATH . 'question/'.$dir.$md5id . '.html';
                        folder::create(dirname($filename));
                        write_file($filename, $data);
                        die($data);
                    } else {
                        show_404();
                    }

                    exit;
                }

                show_404();
                break;
            case 'show' :

                if(preg_match('/^([a-zA-Z0-9]+)?\.html/' , $uri[3] , $arr)){
                    $md5id = $arr[1];
                    $content = $this->db->get("SELECT contentid FROM #table_exam WHERE md5id='{$md5id}'");
                    if (!$content) show_404();
                    $r = loader::model('exam', 'exam')->get($content['contentid']);

                    if (!$r)show_404();
                    $template = 'exam/show.html';
                    $this->template->ext = '';
                   // printR($r);
                    $this->template->assign($r);
                    $data = $this->template->fetch($template);
                    $dir = splitdir($r['md5id']);
                    //$r = $this->uri->content($contentid);
                    //$filename = $r['path'];
                    define('EXAM_HTML_PATH', WWW_PATH . 'www/exam' . DS);
                    $filename = EXAM_HTML_PATH . 'show/' . $dir . $r['md5id'] . '.html';
                    folder::create(dirname($filename));
                    write_file($filename, $data);
                    exit($data);
                }
                show_404();
                break;
        }

    }
    /*function create_html2exam_question($questionid)
    {
        $question_model = loader::model('question', 'exam');
        $question = $question_model->get($questionid);
        if ($question) {
            if ($question['type'] == 'read') {
                $question['questions'] = $question_model->ls(array('bandid'=>$questionid));
            }
            $thread = $this->get_attachment_hot_thread();
            $template = factory::template();
            $template->assign('thread', $thread);
            $template->assign($question);
            $data = $template->fetch('exam/question/show');
            define('EXAM_HTML_PATH', WWW_PATH . 'www/exam' . DS);
            $filename = EXAM_HTML_PATH . 'question/'.$questionid . '.html';
            folder::create(dirname($filename));
            write_file($filename, $data);

            if (file_exists($filename)){
                echo $data;
                die();
            }
        }
    }

    function create_html2exam_show($md5id)
    {
        $db = factory::db();
        $content = $db->get("SELECT contentid FROM #table_exam WHERE md5id='{$md5id}'");
        if (!$content)return '';
        $r = loader::model('exam', 'exam')->get($content['contentid']);
        if ($r) {
            //if ($r['status'] != 6) return false;
            $template = factory::template();
            $template->assign($r);
            $data = $template->fetch('exam/show');
            define('EXAM_HTML_PATH', WWW_PATH . 'www/exam' . DS);
            $filename = EXAM_HTML_PATH . 'show/'.$r['md5id'] . '.html';
            loader::model('admin/content', 'system')->update(array('url'=>WWW_URL.'exam/show/'.$r['md5id'] . '.html'), $r['contentid']);
            folder::create(dirname($filename));
            write_file($filename, $data);
            if (file_exists($filename)){
                echo $data;
                die();
            }
        }
    }*/
    
}