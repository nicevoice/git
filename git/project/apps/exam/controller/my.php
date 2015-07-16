<?php

/**
 * 个人题库中心
 * 缓存数据...
 *
 * Class controller_my
 * 动态缓存类
 * 动态缓存类使用适配器模式，允许指定驱动器，目前内置了 apc、eaccelerator、file、memcache、xcache共5种驱动。
 * 动态缓存类提供了4个方法来进行缓存操作。
 */
class controller_my extends exam_controller_abstract
{
    private $exam,$answer,$answer_option,$db,$question,$favorite,$notes,$cache;
	
	function __construct(& $app)
	{
		parent::__construct($app);
        //header( 'Expires: Sat, 26 Jul 1997 05:00:00 GMT' );
        $no_cache = array('analyze','report','history','error','error_show','favorite','favorite_show','notes','notes_show','doubt');

        if (in_array($this->app->action, $no_cache)){
            header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
            header( 'Cache-Control: no-store, no-cache, must-revalidate' );
            header( 'Cache-Control: post-check=0, pre-check=0', false );
            header( 'Pragma: no-cache' );
        }
        $this->exam = loader::model('exam', 'exam');
        $this->answer = loader::model('answer');
        $this->question = loader::model('question');
        $this->answer_option = loader::model('answer_option');

        $this->favorite = loader::model('exam_favorite', 'exam');
        $this->notes = loader::model('exam_notes', 'exam');
        $this->db = & factory::db();
        $this->cache = & factory::cache();

        if (!$this->_userid) {
            if ($this->is_ajax()) {
                $json = $this->json->encode(array('state'=>false, 'error'=>'请先登录！'));
                echo "{$_GET['jsoncallback']}($json);";
                exit();
            } else {
                $this->template->ext = '';
                $this->showmessage('未登陆！', PASSPORT_URL.'login?redirect='.urlencode('http://'.$_SERVER['SERVER_NAME']. $_SERVER['REQUEST_URI']));
            }
        }

	}

    /**
     * 下次再做
     */
    function donext()
    {
        $id = intval($_GET['contentid']);
        $projectid = intval($_GET['projectid']);
        $sql = $id ? "contentid={$id} AND " : '';
        /**
         * 判断该科目下是否有题目
         */
        if (empty($id)) {
            $sql1 = "SELECT c.url FROM #table_content c,#table_exam_answer a WHERE a.contentid=c.contentid AND a.createdby={$this->_userid} AND a.isfinish=1 AND c.typeid={$projectid} ORDER BY a.answerid DESC";
            $exam = $this->db->get($sql1);
            if ($exam) {
                $result = array('state'=>true, 'info'=>$exam);
                $json = $this->json->encode($result);
                echo "{$_GET['jsoncallback']}($json);";
                exit;
            } else {
                $result = array('state'=>false, 'info'=>'该科目没有未完成的练习');
                $json = $this->json->encode($result);
                echo "{$_GET['jsoncallback']}($json);";
                exit;
            }
            die();
        }
        if ($answer = $this->answer->get("{$sql} createdby={$this->_userid} AND isfinish=1", 'answerid,examtime,contentid', 'answerid DESC')){
            $examtime = $answer['examtime'];
            $answer['examtime_m'] = floor($examtime/60);
            $s = $examtime%60;
            $answer['examtime_s'] = sprintf("%02d", $s);
            $answer['url'] = kuaiji_url('show' , array('examid'=>md5($answer['contentid'])) , 'exam');
            if (!$_GET['check'])$answer['option'] = $this->answer_option->select("answerid={$answer['answerid']}", '*', '' , 100);
            $result = array('state'=>true, 'info'=>$answer);
        } else {
            $result = array('state'=>false, 'error'=>$this->answer->error());
        }
        dieJson($result);
        /*$json = $this->json->encode($result);
        echo "{$_GET['jsoncallback']}($json);";*/
    }

    /**
     * 组卷模考
     */
    public function automatic()
    {
        $automatic = loader::model('automatic');
        $subjectid = $_GET['subjectid'] ? intval($_GET['subjectid']) : 401;
        $knowledgeid = $_GET['knowledgeid'] ? intval($_GET['knowledgeid']) : 0;
        foreach($this->exam_catid as $cat) {
            $cats = $this->category[$cat];
            $catids[$cats['typeid']] = $cat;
        }
        foreach($this->project_t as $pro) {
            if ($subjectid == $pro['proid'] || in_array($subjectid, array_keys($pro['child'])))$catid  = $catids[$pro['proid']];
        }
        if ($contentid = $automatic->automatic_exam($catid, $subjectid,$knowledgeid, $_GET['tt'])){
            //printR($contentid);
            $result = array('state'=>true, 'url'=> kuaiji_url('show' , array('examid'=>md5($contentid)) , 'exam'));
        } else {
            $result = array('state'=>false, 'error'=>$automatic->error());
        }

        if (!$_GET['tt'] || $_GET['isajax']) {
            dieJson($result);
           /* $json = $this->json->encode($result);
            echo "{$_GET['jsoncallback']}($json);";*/
        } else {
            if (!$result['state']){
                $this->template->ext = '';
                $this->showmessage($result['error']);
            }
            //header("HTTP/1.1 301 Moved Permanently");
            header('location:' . $result['url']);
        }

    }
    /**
     * 收藏
     */
    public function go_favorite()
    {
        $_GET['questionid'] = $_GET['qid'];
        if ($id = $this->favorite->add($_GET)){
            $result = array('state'=>true, 'info'=>'收藏成功');
        } else {
            $result = array('state'=>false, 'error'=>$this->favorite->error());
        }
        dieJson($result);
        /*$json = $this->json->encode($result);
        echo "{$_GET['jsoncallback']}($json);";*/
    }
    /**
     * 查看解析
     */
    public function analyze()
    {

        $answerid = intval($_GET['id']);
        $ckey =  md5('analyze_' .$this->_userid . $answerid);
        if (!$list = $this->cache->get($ckey)) {
            $list['answer'] = $this->answer->get_my_answer($answerid);
            $list['exams'] = $this->exam->get($list['answer']['contentid']);
            $this->cache->set($ckey, $list, 3600);
        }
        //printR($list);
        $this->template->assign('my_answer',  $list['answer']);
        $this->template->assign($list['exams']);
        $this->template->display('exam/my/analyze');
    }

    /**
     * 查看报告
     */
    public function report()
    {

        $answerid = intval($_GET['id']);
        $ckey =  md5('report_' .$this->_userid . $answerid);
        if (!$list = $this->cache->get($ckey)) {
            $answer = $this->answer->get_my_answer($answerid);
            $exams = $this->exam->get($answer['contentid']);
            $propertys = $this->propertys;
            $_wrongs = $_options = array();
            foreach($exams['question'] as $question) {
                foreach($question['question'] as $val){
                    if ($val['type'] == 'read') {
                        foreach($val['questions'] as $val1) {
                            $w = $answer['option'][$val1['questionid']]['wrong'];
                            $o = $answer['option'][$val1['questionid']]['optionid'];
                            if($w)$_wrongs[$val1['knowledgeid']] = $_wrongs[$val1['knowledgeid']]+1;
                            if($o)$_options[$val1['knowledgeid']] = $_options[$val1['knowledgeid']]+1;
                            $knowledges[$val1['knowledgeid']][] = $propertys[$val1['knowledgeid']];
                        }
                    } else {
                        $w = $answer['option'][$val['questionid']]['wrong'];
                        $o = $answer['option'][$val['questionid']]['optionid'];
                        if($w)$_wrongs[$val['knowledgeid']] = $_wrongs[$val['knowledgeid']]+1;
                        if($o)$_options[$val['knowledgeid']] = $_options[$val['knowledgeid']]+1;
                        $knowledges[$val['knowledgeid']][] = $propertys[$val['knowledgeid']];
                    }

                }
            }
            if($this->answer->get("created <={$answer['created']} AND createdby={$answer['createdby']} AND contentid={$answer['contentid']} AND answerid!={$answerid}", 'answerid'))$is_answer = true;
            foreach($knowledges as $k=>$know){
                $_knowledges[$k] = $propertys[$k];
                $_knowledges[$k]['count'] = count($know);
            }
            $n = 0;
            foreach($answer['option'] as $option) {
                if ($option['optionid'])++$n;
            }
            $_knowarr = get_property_child_inarray($this->pro_ids['knowledgeid'], array_keys($_knowledges));
            $answer['_n'] = $n;



            $list['answer'] = $answer;
            $list['exams'] = $exams;
            $list['_knowarr'] = $_knowarr;
            $list['knowledges'] = $_knowledges;
            $list['_options'] = $_options;
            $list['_wrongs'] = $_wrongs;
            $list['is_answer'] = $is_answer;
            $this->cache->set($ckey, $list, 3600);
        }


        $rewards = $this->answer->getreward($answer['contentid']);
        $this->template->assign('rewards', $rewards);

        $this->template->assign('is_answer', $list['is_answer']);
        $this->template->assign('_wrongs', $list['_wrongs']);
        $this->template->assign('_options', $list['_options']);
        $this->template->assign('knowledges', $list['knowledges']);
        $this->template->assign('_knowarr', $list['_knowarr']);
        $this->template->assign('my_answer', $list['answer']);
        $this->template->assign($list['exams']);
        $this->template->display('exam/my/report');

    }

    /**
     * 练习历史
     */
    public function history()
    {

        $page = $_GET['page'] ? intval($_GET['page']) : 1;
        $id = $_GET['id'] ? intval($_GET['id']) : '';
        if(!$id)  $id = $_GET['subjectid'] ? intval($_GET['subjectid']) : 401;

        $knowledgeid= $_GET['knowledgeid'] ? intval($_GET['knowledgeid']) : 201;
        $pagesize = 10;
        $ckey = md5('history'.$this->_userid . $pagesize . $page . $id.$knowledgeid);
        if (!$list = $this->cache->get($ckey)) {
            $list['lists'] = $this->exam->history($id, $page, $pagesize);
            $list['pages'] = pages_mini($this->exam->history_count($id), $page , $pagesize, 5, WWW_URL."exam/my/history_{$knowledgeid}_{$id}_{$page}.html", true);
            $this->cache->set($ckey, $list, 60);
        }
        $this->template->assign('lists', $list['lists']);
        $this->template->assign('pages', $list['pages']);
        $this->template->assign('id', $id);
        $this->template->assign('p_name', $this->propertys[$id]['name']);
        $this->template->display('exam/my/history');

    }
    /**
     * 错误题目
     */
    public function error()
    {

        $id = $_GET['id'] ? intval($_GET['id']) : 401;
        $ckey = md5('error'.$this->_userid.$id);
        if (!$list = $this->cache->get($ckey)) {
            $lists = $this->answer->get_error_knowledges($id);
            $counts = $this->answer->get_error_knowledges_count($id, array_keys($lists));
            $_knowarr = get_property_child_inarray($this->pro_ids['knowledgeid'], array_keys($lists));
            $_count = 0;
            foreach ($counts as $ct) {
                $_count = $_count + $ct;
            }
            $list['_count'] = $_count;
            $list['counts'] = $counts;
            $list['_knowarr'] = $_knowarr;
            $this->cache->set($ckey, $list, 60);
        }
        $this->template->assign('_count', $list['_count']);
        //printR($list);
        $this->template->assign('_knowarr', $list['_knowarr']);
        $this->template->assign('counts', $list['counts']);
        $this->template->assign('p_name', $this->propertys[$id]['name']);
        $this->template->display('exam/my/error');

    }
    /**
     * 查看错误题目
     */
    public function error_show()
    {

        $knowledgeid = $_GET['knowledgeid'] ? intval($_GET['knowledgeid']) : $this->pro_ids['knowledgeid'];
        $page = $_GET['page'] ? intval($_GET['page']) : 1;
        $subjectid = $_GET['subjectid'] ? intval($_GET['subjectid']) : 401;
        $pagesize = 5;
        $ckey = md5('error_show'. $this->_userid . $pagesize . $page. $subjectid . $knowledgeid);
        if (!$list = $this->cache->get($ckey)) {

            $knowledge = $this->propertys[$knowledgeid];
            if ($knowledge['childids']) {
                $knowledgeid = $knowledgeid.','.$knowledge['childids'];
            }
            $lists = $this->question->get_error_question($knowledgeid, $subjectid, $page, $pagesize);
            $count = $this->question->get_error_question_count($knowledgeid, $subjectid);
            if ($count > $pagesize) {
                $pages = pages_mini($count, $page , $pagesize, 5, WWW_URL.'exam/my/error_show_'.intval($_GET['knowledgeid']).'_'.$subjectid.'_{$page}.html', true);
            }
            $list['pages'] = $pages;
            $list['lists'] = $lists;
            $list['knowledge'] = $knowledge;

            $this->cache->set($ckey, $list, 60);
        }
        //printR($lists);
        $this->template->assign('pages', $list['pages']);
        $this->template->assign('pagesize', $pagesize);
        $this->template->assign('lists', $list['lists']);
        $this->template->assign('knowledge', $list['knowledge']);
        $this->template->display('exam/my/error_show');
    }


    /**
     * 移除错误题目
     */
    public function error_remove()
    {
        $qid = $_GET['qid'] ? intval($_GET['qid']) : 0;
        if ($id = $this->answer_option->error_remove($qid)){
            $result = array('state'=>true, 'info'=>'已移除');
        } else {
            $result = array('state'=>false, 'error'=>$this->answer_option->error());
        }
        dieJson($result);
    }


    /**
     * 收藏
     */
    public function favorite()
    {
        $id = $_GET['id'] ? intval($_GET['id']) : 401;

        $ckey = md5('favorite'. $this->_userid . $id);
        if (!$list = $this->cache->get($ckey)) {

            $knowledges = $this->favorite->get_knowledge($id);
            $counts = $this->favorite->get_knowledges_count($id, array_keys($knowledges));
            $_knowarr = get_property_child_inarray($this->pro_ids['knowledgeid'], array_keys($knowledges));
            $_count = 0;
            foreach ($counts as $ct) {
                $_count = $_count + $ct;
            }
            $list['_count'] = $_count;
            $list['counts'] = $counts;
            $list['_knowarr'] = $_knowarr;
            $this->cache->set($ckey, $list, 60);
        }
        $this->template->assign('_count', $list['_count']);
        $this->template->assign('counts', $list['counts']);
        $this->template->assign('_knowarr', $list['_knowarr']);
        $this->template->assign('p_name', $this->propertys[$id]['name']);
        $this->template->display('exam/my/favorite');
    }

    /**
     * 查看收藏题目
     */
    public function favorite_show()
    {

        $knowledgeid = $_GET['knowledgeid'] ? intval($_GET['knowledgeid']) : $this->pro_ids['knowledgeid'];
        $page = $_GET['page'] ? intval($_GET['page']) : 1;
        $subjectid = $_GET['subjectid'] ? intval($_GET['subjectid']) : 401;
        $pagesize = 5;
        $ckey = md5('favorite_show'. $this->_userid . $pagesize .$page. $subjectid . $knowledgeid);
        if (!$list = $this->cache->get($ckey)) {
            $knowledge = $this->propertys[$knowledgeid];
            if ($knowledge['childids']) {
                $knowledgeid = $knowledgeid.','.$knowledge['childids'];
            }
            $lists = $this->question->get_favorite_knowledges_question($knowledgeid, $subjectid, $page, $pagesize);
            $count = $this->question->get_favorite_knowledges_question_count($knowledgeid, $subjectid);
            if ($count > $pagesize) {
                $pages = pages_mini($count, $page , $pagesize, 5, WWW_URL.'exam/my/favorite_show_'.intval($_GET['knowledgeid']).'_'.$subjectid.'_{$page}.html', true);
            }

            $list['pages'] = $pages;
            $list['lists'] = $lists;
            $list['knowledge'] = $knowledge;

            $this->cache->set($ckey, $list, 10);
        }
        //printR($lists);
        $this->template->assign('pages', $list['pages']);
        $this->template->assign('pagesize', $pagesize);
        $this->template->assign('lists', $list['lists']);
        $this->template->assign('knowledge', $list['knowledge']);
        $this->template->display('exam/my/favorite_show');
    }

    /**
     * 移除收藏题目
     */
    public function favorite_remove()
    {
        $favoriteid = $_GET['favoriteid'] ? intval($_GET['favoriteid']) : 0;
        if ($id = $this->favorite->delete($favoriteid)){
            $result = array('state'=>true, 'info'=>'已移除');
        } else {
            $result = array('state'=>false, 'error'=>$this->answer_option->error());
        }
        dieJson($result);
        /*$json = $this->json->encode($result);
        echo "{$_GET['jsoncallback']}($json);";*/
    }

    /**
     * 笔记
     */
    public function notes()
    {
        $id = $_GET['id'] ? intval($_GET['id']) : 401;
        $ckey = md5('notes'. $this->_userid . $id);
        if (!$list = $this->cache->get($ckey)) {
            $knowledges = $this->notes->get_knowledge($id);
            $counts = $this->notes->get_knowledges_count($id, array_keys($knowledges));
            $_knowarr = get_property_child_inarray($this->pro_ids['knowledgeid'], array_keys($knowledges));
            $_count = 0;
            foreach ($counts as $ct) {
                $_count = $_count + $ct;
            }
            $list['_count'] = $_count;
            $list['counts'] = $counts;
            $list['_knowarr'] = $_knowarr;
            $this->cache->set($ckey, $list, 10);
        }
        // printR($_knowarr);
        $this->template->assign('_count', $list['_count']);
        $this->template->assign('counts', $list['counts']);
        $this->template->assign('_knowarr', $list['_knowarr']);
        $this->template->assign('p_name', $this->propertys[$id]['name']);
        $this->template->display('exam/my/notes');
    }

    /**
     * 查看笔记题目
     */
    public function notes_show()
    {

        $knowledgeid = $_GET['knowledgeid'] ? intval($_GET['knowledgeid']) : $this->pro_ids['knowledgeid'];
        $page = $_GET['page'] ? intval($_GET['page']) : 1;
        $subjectid = $_GET['subjectid'] ? intval($_GET['subjectid']) : 401;

        $pagesize = 5;
        $ckey = md5('notes_show'. $this->_userid . $pagesize .$page. $subjectid . $knowledgeid);
        if (!$list = $this->cache->get($ckey)) {
            $knowledge = $this->propertys[$knowledgeid];
            if ($knowledge['childids']) {
                $knowledgeid = $knowledgeid.','.$knowledge['childids'];
            }

            $lists = $this->question->get_notes_knowledges_question($knowledgeid, $subjectid, $page, $pagesize);
            $count = $this->question->get_notes_knowledges_question_count($knowledgeid, $subjectid);
            if ($count > $pagesize) {
                $pages = pages_mini($count, $page , $pagesize, 5, WWW_URL.'exam/my/notes_show_'.intval($_GET['knowledgeid']).'_'.$subjectid.'_{$page}.html', true);
            }
            $list['pages'] = $pages;
            $list['lists'] = $lists;
            $list['knowledge'] = $knowledge;

            $this->cache->set($ckey, $list, 10);
        }
        $this->template->assign('pages', $list['pages']);
        $this->template->assign('pagesize', $pagesize);
        $this->template->assign('lists', $list['lists']);
        $this->template->assign('knowledge', $list['knowledge']);

        $this->template->display('exam/my/notes_show');
    }

    /**
     * 添加笔记
     */
    public function notes_add()
    {
        $notesid = $_POST['notesid'] ? intval($_POST['notesid']) : 0;
        $func = $notesid ? 'edit' : 'add';
        if ($id = $this->notes->$func($_POST)){
            $notesid  = $notesid ? $notesid : $id;
            $result = array('state'=>true, 'notesid'=>$notesid);
        } else {
            $result = array('state'=>false, 'error'=>$this->answer_option->error());
        }
        die($notesid);
        /*$json = $this->json->encode($result);
        echo "{$_GET['jsoncallback']}($json);";*/
    }


    public function doubt()
    {
        $id = $_GET['id'] ? intval($_GET['id']) : 401;

        foreach($this->exam_catid as $cat) {
            $cats = $this->category[$cat];
            $catids[$cats['typeid']] = $cat;
        }
        foreach($this->project_t as $pro) {
            if ($id == $pro['proid'] || in_array($id, array_keys($pro['child'])))$catid  = $catids[$pro['proid']];
        }

        $ckey = md5('doubt'. $this->_userid . $id);
        if (!$list = $this->cache->get($ckey)) {
            $list = load_rpc('bbs')->get_member_new_thread($this->_userid, 15, $this->bbs_fid[$catid]);
            $this->cache->set($ckey, $list, 10);
        }
        $this->template->assign('lists', $list);
        $this->template->assign('bbs_fid', $this->bbs_fid[$catid]);
        $this->template->assign('p_name', $this->propertys[$id]['name']);
        $this->template->display('exam/my/doubt');
    }
}