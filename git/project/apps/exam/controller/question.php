<?php

/**
 * 搜索hooks
 *
 * Class controller_question
 *
 * date: 2014/04/01
 * author: huangwz@kuaiji.com
 */
class controller_question extends exam_controller_abstract
{
	private $exam,$cache,$answer,$answer_option,$db,$content,$question;
	
	function __construct(& $app)
	{
		parent::__construct($app);
		$this->content = loader::model('content', 'system');
		$this->exam = loader::model('exam');
		$this->answer = loader::model('answer');
		$this->answer_option = loader::model('answer_option');
		$this->question = loader::model('question');
        $this->cache = & factory::cache();
        $this->db = & factory::db();
        header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
        header( 'Cache-Control: no-store, no-cache, must-revalidate' );
        header( 'Cache-Control: post-check=0, pre-check=0', false );
        header( 'Pragma: no-cache' );

	}
    /**
     * 字符串统计
     *
     * @param string $string 原始字符串
     * @return string
     */
    function str_count($string)
    {
        $strlen = strlen($string);
        $specialchars = array('&amp;', '&quot;', '&#039;', '&lt;', '&gt;');
        $entities = array('&', '"', "'", '<', '>');
        $string = str_replace($specialchars, $entities, $string);
        $n = $tn = $noc = 0;
        while($n < $strlen)
        {
            $t = ord($string[$n]);
            if($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
                $tn = 1; $n++; $noc++;
            } elseif(194 <= $t && $t <= 223) {
                $tn = 2; $n += 2; $noc += 2;
            } elseif(224 <= $t && $t < 239) {
                $tn = 3; $n += 3; $noc += 2;
            } elseif(240 <= $t && $t <= 247) {
                $tn = 4; $n += 4; $noc += 2;
            } elseif(248 <= $t && $t <= 251) {
                $tn = 5; $n += 5; $noc += 2;
            } elseif($t == 252 || $t == 253) {
                $tn = 6; $n += 6; $noc += 2;
            } elseif(bin2hex($string[$n]) >=65281||bin2hex($string[$n])<=65374){
                $tn = 3; $n += 3; $noc += 2;
            } else{
                $n++;
            }

        }
        return $noc;
    }
    /**
     * 搜索首页
     */
    function so()
    {
       // $this->template->display('site/news_v2');
        //exit;
        if ($this->is_get() && $_GET['wd']) {
            //搜索词解码
            $wd = urldecode(trim($_GET['wd']));
            //过滤搜索词
            $str = '';
            for( $i=0 ; $i< mb_strlen($wd,'utf-8') ; $i++ ){
                $v = mb_substr( $wd, $i, 1, 'utf-8' );
                if( preg_match('/[\x{4e00}-\x{9fa5}A-Za-z0-9_\s]/u', $v) ){
                    $str .= $v;
                }else{
                    $str .= ' ';
                }
            }
            if ($this->str_count($str) > 76){
                $exceed = substr($str, strlen(str_cut($str, 76, '')), 9);
                $str = str_cut($str, 76, '');
                $this->template->assign('exceed', $exceed);
            }
            $page = $_GET['page'] ? intval($_GET['page']) : 1;
            $pagesize = 10;
            if ($str)$where['subject'] = where_keywords('subject', $str);
            if (is_numeric($_GET['subjectid']) && intval($_GET['subjectid']) > 1)$where['subjectid'] = "subjectid=". intval($_GET['subjectid']);
            $where = implode(' AND ', $where);
            $lists = $this->question->so($where, $page, $pagesize);
            $count = $this->question->count($where);
            if ($count > $pagesize) {
                $pages = pages_mini($count, $page , 10, 5, WWW_URL.'exam/so?wd='.$_GET['wd'].'&subjectid='.$_GET['subjectid'].'&page={$page}', true);
                $this->template->assign('pages', $pages);
            }
            $this->template->assign('count', $count);
            $this->template->assign('pagesize', $pagesize);
            $this->template->assign('wd', $str);
            $this->template->assign('lists', $lists);
            $this->template->display('exam/question/solist');
            exit();
        }
        $knowledge = config::get('exam', 'knowledge');
        //$subjectid = $_GET['id'] ? intval($_GET['id']) : '';
		$subjectid = $_GET['subjectid'] ? $_GET['subjectid'] : ( $_GET['id'] ? intval($_GET['id']) : intval($_COOKIE['KJ_exam_project']));
        $knowledgeid = $knowledge[$subjectid] ? $knowledge[$subjectid] : $this->pro_ids['knowledgeid'];

        $ckey = md5('so_default_count'.$subjectid);
        if (!$list = $this->cache->get($ckey)) {
            if (is_numeric($subjectid))$where = "subjectid={$subjectid}";
            $list['question_count'] = loader::model('question','exam')->count($where);
            $list['join_count'] = loader::model('answer','exam')->count();
            $list['knowledge_count'] = count(explode(',', $this->propertys[$knowledgeid]['childids']));
            $this->cache->set($ckey, $list, 3600);
        }
        //printR($_GET);
        $this->template->assign($list);
		$this->template->assign('id', $subjectid);
        $this->template->display('exam/question/so');
    }


    public function lists()
    {
        $subjectid = $_GET['subjectid'] ? $_GET['subjectid'] : ( $_GET['id'] ? intval($_GET['id']) : intval($_COOKIE['KJ_exam_project']));
        if (!in_array($subjectid, array_keys($this->_subject)))$subjectid=401;
        $_knowledge = config::get('exam', 'knowledge');
        $subjectid = $subjectid ? $subjectid : 403;
        $knowledgeid = $_GET['knowledgeid'] ? intval($_GET['knowledgeid']) : $_knowledge[$subjectid];

        $knowledge = get_property_child($_knowledge[$subjectid]);
        $knowledge = $this->exam->bubbleSort($knowledge);//冒泡排序
        $page = $_GET['page'] ? intval($_GET['page']) : 1;
        $pagesize = 10;

        $where = ' 1 ';
        if (is_numeric($subjectid) && $subjectid > 0) {
            $where .= " AND subjectid ={$subjectid} ";
        }

        if (is_numeric($knowledgeid) && $knowledgeid != $this->pro_ids['knowledgeid']) {
            $childids= $this->propertys[$knowledgeid]['childids'];
            $childids = $childids ? $knowledgeid.','.$childids : $knowledgeid;
            $where .= " AND knowledgeid in({$childids})";
        }
        if ($where == '1')$where=null;
        $lists = $this->question->page($where, 'questionid,subject,knowledgeid,bandid', 'questionid DESC', $page, $pagesize);
        $count = $this->question->count($where);

        if ($count > $pagesize) {
            $pages = pages_mini($count, $page , 10, 5, WWW_URL .'exam/question/lists_'. $knowledgeid . '_' . $subjectid . '_{$page}.html', true);
            $this->template->assign('pages', $pages);
        }
        //printR($lists);
        $this->template->assign('lists', $lists);
        $this->template->assign('subjectid', $subjectid);
        $this->template->assign('knowledgeid', $knowledgeid);
        $this->template->assign('knowledge', $knowledge);
        $this->template->display('exam/question/lists');
    }
    public function show()
    {
        /*$questionid = intval($_GET['id']);
        $question = $this->question->get($questionid);
        if ($question['type'] == 'read') {
            $question['questions'] = $this->question->ls(array('bandid'=>$questionid));
        }
       // printR($question);
        $thread = get_attachment_hot_thread();
        $this->template->assign($question);
        $this->template->display('exam/question/show');*/
    }

}