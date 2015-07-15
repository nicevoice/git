<?php

class controller_exam extends wap_controller_abstract {

    private $db,
        $userinfo,
        $member_model,
        $exam_wechat_member,
        $exam_wechat_member_log,
        $user,
        $_subject,
        $propertys,
        $answer_model,
        $exam,
        $cache,
        $category,
        $content,
        $question_model,
        $exam_wechat_friend,
        $in_wechat = false,
        $_subjectids = array(301,302,303,305),
        $exam_catid = array(11100,11200,11300,11500),
        $alphabet = array('A','B','C','D','E','F','G'),
        $share_font = array('会计网题库，有了会计练习答题神器，考试必过！', '会计网题库，答题送红包？要抓紧时间了！',  '会计网题库，会计大神推荐的好玩应用。',  '会计网题库，学会计，有方法！', '会计网题库，最全最好的会计题库在这里', '会计网题库，会计人都在这里，来切磋切磋吧！', '会计网题库微信版，在微信就能做题。');

    function __construct(& $app) {
        parent::__construct($app);
	header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
        header( 'Cache-Control: no-store, no-cache, must-revalidate' );
        header( 'Cache-Control: post-check=0, pre-check=0', false );
        header( 'Pragma: no-cache' );
        $this->db = & factory::db();
        if (!in_weixin()) {
            $this->in_wechat = true;
            header("Content-type: text/html; charset=utf-8");
            echo '<link rel="stylesheet" type="text/css" href="https://res.wx.qq.com/connect/zh_CN/htmledition/style/wap_err1a9853.css"><div class="page_msg"><div class="inner"><span class="msg_icon_wrp"><i class="icon80_smile"></i></span><div class="msg_content"><h4>会计网温馨提示：请在微信客户端打开链接</h4></div></div></div>';
            exit;
        }
        $this->userinfo = wechat_token();
        $this->member_model = loader::model('wechat_member', 'wap');
        $this->exam_wechat_member = loader::model('exam_wechat_member', 'exam');
        $this->exam_wechat_member_log = loader::model('exam_wechat_member_log', 'exam');
        $this->exam_wechat_friend = loader::model('exam_wechat_friend', 'exam');
        $this->question_model = loader::model('question', 'exam');
        $this->favorite = loader::model('exam_favorite', 'exam');
        $this->exam = loader::model('exam', 'exam');
        $this->answer_model = loader::model('answer', 'exam');
        if (!$this->user = $this->exam_wechat_member->get(array('openid'=>$this->userinfo['openid']))) {
            $this->user = array(
                'openid' => $this->userinfo['openid'],
                'gold' => 0,
                'level' => 0,
                'exper' => 0,
                'completeid' => 0,
            );
            $this->exam_wechat_member->insert($this->user);
            $this->exam_wechat_friend->insert(array('from_openid'=>$this->userinfo['openid'], 'to_openid'=>$this->userinfo['openid']));
        }
        //如果有来源openid，则加好友
        if ($_GET['from_openid'] && $_GET['from_openid'] != $this->userinfo['openid']) {
            if (! $this->exam_wechat_friend->get(array('from_openid'=>$this->userinfo['openid'], 'to_openid'=>$_GET['from_openid'])))$this->exam_wechat_friend->insert(array('from_openid'=>$this->userinfo['openid'], 'to_openid'=>$_GET['from_openid']));
            if (! $this->exam_wechat_friend->get(array('to_openid'=>$this->userinfo['openid'], 'from_openid'=>$_GET['from_openid'])))$this->exam_wechat_friend->insert(array('to_openid'=>$this->userinfo['openid'], 'from_openid'=>$_GET['from_openid']));
        }
        $this->userinfo = array_merge($this->userinfo, $this->user);
        $this->template->assign($this->userinfo);
        $this->content = loader::model('content', 'system');
        $this->category = & $this->content->category;
        $this->get_propertys();
        // printR($this->_subject);
        $key = array_rand($this->share_font, 1);
        $share['share_titel'] = $share['share_desc'] = $this->share_font[$key];
        $this->template->assign($share);
        $this->template->assign('alphabet', $this->alphabet);
        $this->menu();
        if (!$this->is_ajax()){
            $r = common_data('wechat_seo');
            $r && $this->template->assign('seo', $r);
            $this->template->display('wap/exam/header');
        }
        $this->cache = redis();
    }

    function index()
    {
        //printR($this->userinfo);
        $sql = "SELECT COUNT(*) AS rank FROM #table_exam_wechat_member wm, #table_wechat_member m, #table_exam_wechat_friend wf WHERE wm.openid=m.openid AND wf.from_openid='{$this->userinfo['openid']}' AND wf.to_openid=m.openid AND wm.exper>{$this->userinfo['exper']} ORDER BY wm.exper DESC LIMIT 1";
        $count = $this->db->get($sql);
        $count['rank'] = $count['rank'] ? $count['rank']+1 : 1;
        $time = time();

        $sql = "SELECT * FROM #table_exam_wechat_messages WHERE messageid NOT IN (SELECT messageid FROM #table_exam_wechat_messages_pm WHERE openid='{$this->userinfo['openid']}' AND delstatus=1 )  AND created <= {$time} ORDER BY created DESC";
        $pm = $this->db->get($sql);

        $sql = "SELECT * FROM #table_exam_wechat_messages ORDER BY created DESC";
        $_pm = $this->db->get($sql);

        $r = common_data('wechat_menu');
        foreach ($r as $v) {
            $_r[$v['sort']] = $v;
        }
        ksort($_r);
        $r && $this->template->assign('index_menu', $_r);
        $this->template->assign('pm', $pm);
        $this->template->assign('_pm', $_pm);
        $this->template->assign($count);
        $this->template->display('wap/exam/index');
    }


    function get_subject_history(){

        if (!$this->userinfo['subjectid']) dieJson(array('state'=>false, 'list'=>array()));
        $subject = $this->_subject[$this->userinfo['subjectid']];
        $_key = 'exam_get_subject_history_'.$this->userinfo['openid'];
        //if (!$r = $this->cache->get($_key)):
            foreach ($subject['child'] as $val) {
                $sql = "SELECT a.right,l.gold,l.logtime,e.qcount FROM #table_exam e,#table_content c,#table_exam_wechat_member_log l,#table_exam_answer a WHERE l.answerid=a.answerid AND e.contentid=c.contentid AND c.contentid=a.contentid AND c.typeid={$val['proid']} AND a.openid='{$this->userinfo['openid']}' AND a.plantform_id=3 ORDER BY a.created DESC LIMIT 1";
                $v = $this->db->get($sql);
                if ($v) {
                    $img = 'true.png';
					if($v['gold'] < 100){
						$img = 'false.png';
					}elseif($v['gold'] == 100){
						$img = 'fat.png';
					}
                    $exams[$val['proid']] = '<p>上次答题结果：答对'.$v['right'].'题 答错'.($v['qcount']-$v['right']).'题<img src="'.IMG_URL.'images/wap/exam/'.$img.'" class="fn-vm" width="20" style=" margin-left:5px;" ></p><span class="fn-fr sy_time">'.time_format($v['logtime']).'</span>';
                }else {
                    $exams[$val['proid']] = '<p >没有做题历史</p>';
                }

            }

            //收藏
            $sql = "SELECT q.subject FROM #table_exam_question q, #table_exam_member_favorite f WHERE q.questionid=f.questionid AND f.openid='{$this->userinfo['openid']}' AND q.subjectid in({$subject['childids']}) ORDER BY f.created DESC LIMIT 1";
            $favorite = $this->db->get($sql);

            if(!$favorite) {
                //错误题目
                $sql = "SELECT q.subject FROM #table_exam_question q,#table_exam_answer a, #table_exam_answer_option o WHERE a.openid='{$this->userinfo['openid']}' AND o.wrong=0 AND q.subjectid in ({$subject['childids']}) AND q.questionid=o.questionid AND a.answerid=o.answerid AND o.isdel = 0 ORDER BY a.created DESC LIMIT 1";
                $favorite = $this->db->get($sql);
            }
            //收藏题目数
            $sql = "SELECT COUNT(*) AS c FROM #table_exam_question q, #table_exam_member_favorite f WHERE q.questionid=f.questionid AND f.openid='{$this->userinfo['openid']}' AND q.subjectid in({$subject['childids']})";
            $favorite_c = $this->db->get($sql);

            //错误题目数量
            $sql = "SELECT count(*) as c FROM #table_exam_question q,#table_exam_answer a, #table_exam_answer_option o WHERE a.openid='{$this->userinfo['openid']}' AND o.wrong=0 AND q.subjectid in ({$subject['childids']}) AND q.questionid=o.questionid AND a.answerid=o.answerid AND o.isdel = 0 group by q.questionid";
            $error_c = $this->db->select($sql);
            $error_tip =  $favorite_c['c']+count($error_c);
            $time = time();
            //系统通知
            $sql = "SELECT COUNT(*) AS c FROM #table_exam_wechat_messages WHERE messageid NOT IN (SELECT messageid FROM #table_exam_wechat_messages_pm WHERE openid='{$this->userinfo['openid']}' AND delstatus=1)  AND created <= {$time} ";
            $pm = $this->db->get($sql);
            $r = array('state'=>false, 'list'=>array());
            if ($exams)$r = array('state'=>true, 'q_list'=>$exams, 'pm'=>$pm['c'], 'f_count'=>$error_tip, 'favorite'=>str_cut($favorite['subject'], 50, '...'));
            //$this->cache->set($_key, $r, 3600 * 3);
        //endif;
        dieJson($r);
    }
    function select_subject()
    {
        if ($this->is_ajax() && isset($_GET['subjectid'])) {
            $subjectid = intval($_GET['subjectid']);
            $this->exam_wechat_member->update(array('subjectid'=>$subjectid), array('openid'=>$this->userinfo['openid']));
            $r = json_encode(1);
            echo $_GET['jsoncallback'] ? $_GET['jsoncallback'] . '('. $r.")" : $r;
            die();
        }
    }
    function subject()
    {
        $this->template->display('wap/exam/subject');
    }

    /**
     * 匹配对手
     */
    function matching()
    {
        $randNum = rand(1, 60000);
        $rand = $this->member_model->select("openid != '{$this->userinfo['openid']}'", 'intro', '', 12, $randNum);
        foreach($rand as $v) {
            $r = unserialize($v['intro']);
            $r['nickname'] = is_base64_encode($r['nickname']) ?  base64_decode($r['nickname']) : $r['nickname'];
            !$r['headimgurl'] && $r['headimgurl'] = IMG_URL . 'images/wap/exam/def.png';
            $_rand[] = $r;
        }
        $this->template->assign('rand', $_rand);
        $this->template->display('wap/exam/matching');
    }


    function rank()
    {

        $sql = "SELECT * FROM #table_exam_wechat_member wm, #table_wechat_member m, #table_exam_wechat_friend wf WHERE wm.openid=m.openid AND wf.from_openid='{$this->userinfo['openid']}' AND wf.to_openid=m.openid ORDER BY wm.exper DESC LIMIT 10";
        $lists = $this->db->select($sql);
        foreach ($lists as $k=> $v) {
            $info = unserialize($v['intro']);
            $info['nickname'] = is_base64_encode($info['nickname']) ?  base64_decode($info['nickname']) : $info['nickname'];
            if ($info) $lists[$k] = array_merge($v, $info);
            if ($v['openid'] == $this->userinfo['openid'])$rank = $k+1;
        }
        $share_msg = "会计网题库，好友中排行第{$rank}，谁敢挑战我的地位？";
        $this->template->assign('share_msg', $share_msg);
        $this->template->assign('lists', $lists);
        $this->template->display('wap/exam/rank');
    }

    /**
     * 做题
     */
    function problem()
    {

        $subjectid = intval($_GET['subjectid']);
        $openid = $_GET['openid'];

        $sql = "SELECT c.contentid,a.answerid,a.right FROM #table_content c, #table_exam_answer a WHERE c.contentid=a.contentid AND a.openid='{$openid}' AND c.typeid={$subjectid} ORDER BY rand() LIMIT 1";
        $r = $this->db->get($sql);
        $contentid = $r['contentid'];
        if (!$contentid) {
            /**
             * 获取catid
             */
            foreach($this->exam_catid as $cat) {
                $cats = $this->category[$cat];
                $catids[$cats['typeid']] = $cat;
            }

            foreach($this->_subject as $pro) {
                if ($subjectid == $pro['proid'] || in_array($subjectid, array_keys($pro['child'])))$catid  = $catids[$pro['proid']];
            }
            $vals = exam_redis_get('exam_app_'.$subjectid);
            $key = array_rand($vals, 1);
            $options = $vals[$key];
            $num = 0;
            foreach ($options as $v) {
                $num  += count($v['qids']);
            }
            $contentid = $this->insertexam($catid, $subjectid, $options);
            $this->db->query("UPDATE #table_exam SET qcount={$num} WHERE contentid={$contentid}");
        }
        $exams = $this->exam->get($contentid);
        $questions = $exams['question'];
        $k = 1;

        foreach ($questions as $question) {
            foreach($question['question'] as $val) {
                $_val['knowledge'] =  $this->propertys[$val['knowledgeid']]['name'];
                $_val['subject_name'] =  $this->propertys[$val['subjectid']]['name'];
                $_val['qtype'] =  $this->propertys[$val['qtypeid']]['name'];
                $_val['questionid'] = $val['questionid'];
                $_val['subject'] = $val['subject'];
                $_val['type'] = $val['type'];
                $_val['description'] = strip_tags($val['description']);
                $_val['answer'] = $val['answer'];
                $_val['option'] = $val['option'] ? $val['option'] : $val['options'];
                $lists[$k] =  $_val;
                $k++;
                $_qids[] = $val['questionid'];
            }
        }
        $_qids = implode(',', $_qids);
        $_favoite = $this->favorite->select("questionid in({$_qids}) AND openid='{$this->userinfo['openid']}'", 'favoriteid,questionid');
        foreach ($_favoite as $_v) {
            $favoite[$_v['questionid']] = $_v['favoriteid'];
        }
        $to_memeber = $this->member_model->get(array('openid'=>$openid));
        $to_memeber = unserialize($to_memeber['intro']);
        $to_memeber['nickname'] = is_base64_encode($to_memeber['nickname']) ?  base64_decode($to_memeber['nickname']) : $to_memeber['nickname'];
        $to_memeber1 = $this->exam_wechat_member->get(array('openid'=>$openid));

        if ($to_memeber1) $to_memeber = array_merge($to_memeber, $to_memeber1);

        $height = '60%';
        if (!empty($r['right'])) {
            $height = $r['right']/$k * 100 . '%';

        }
        $this->template->assign('height', $height);
        $this->template->assign('favoite', $favoite);
        $this->template->assign('contentid', $contentid);
        $this->template->assign('to_memeber', $to_memeber);
        $this->template->assign('answerid', $r['answerid']);
        $this->template->assign('to_openid', $openid);
        $this->template->assign('lists', $lists);
        $this->template->display('wap/exam/problem');
    }
    function complete()
    {
        if ($this->is_post()) {
            $contentid = intval($_POST['contentid']);
            $to_answerid = intval($_POST['to_answerid']);
            if ($answerid = $this->answer_model->add($contentid, array('openid'=>$this->userinfo['openid'], 'answer' => $_POST['answer'],'plantform_id'=>3,'examtime'=>100, 'isfinish'=>0))){
                $r = $this->answer_model->get(array('answerid'=>$answerid, 'openid'=>$this->userinfo['openid']));
                $size = count(array_keys($_POST['answer']));
                $logid = $this->odds($_POST['to_openid'], $r['right'], $size, $answerid, $to_answerid, $_REQUEST['ajax_false']);
                exit($logid);
            }
            exit(0);
        }
    }


    function my()
    {
		$sql = "SELECT gold,achievement FROM #table_exam_wechat_member_log l WHERE from_openid='{$this->userinfo['openid']}'";
		$result = $this->db->select($sql);
		$m = strtotime(date('Y-m-01 00:00:00', time()));
		$sql_jilu = "SELECT l.gold FROM #table_exam_wechat_member_log l,#table_exam_answer a WHERE from_openid='{$this->userinfo['openid']}' AND a.created>{$m} AND l.answerid=a.answerid";
		$result_jilu = $this->db->select($sql_jilu);
		$duizhan['num'] = 0;
		$duizhan['ying'] = 0;
		$duizhan['shu'] = 0;
		$duizhan['chengjiu'] = 0;
		foreach($result_jilu as $v){
			if($v['gold']>100){
				$duizhan['ying']++;
			}else{
				$duizhan['shu']++;
			}
			$duizhan['num']++;
		}
		$duizhan['shenglv'] = round($duizhan['ying']/$duizhan['num']*100,2).'%';
		foreach($result as $v){
			
			if(($v['achievement']!='大菜了，没有获得成就') && ($v['achievement']!='太菜了，没有获得成就')){
				$duizhan['chengjiu']++;
			}
		}
		$this->template->assign('duizhan', $duizhan);
        $this->template->display('wap/exam/my');
    }


    function answer()
    {
        $logid = intval($_GET['logid']);
        if (!$logid)exit('loadpage is error!');
        $r = $this->exam_wechat_member_log->get(array('id'=>$logid, 'from_openid'=>$this->userinfo['openid']));
        if (!$r)exit('loadpage is error!');
        //$rank = $this->exam_wechat_member->count("exper>{$this->userinfo['exper']}");
        $sql = "SELECT COUNT(*) AS rank FROM #table_exam_wechat_member wm, #table_wechat_member m, #table_exam_wechat_friend wf WHERE wm.openid=m.openid AND wf.from_openid='{$this->userinfo['openid']}' AND wf.to_openid=m.openid AND wm.exper>{$this->userinfo['exper']} ORDER BY wm.exper DESC LIMIT 1";
        $count = $this->db->get($sql);
        $rank = $count['rank'] ? $count['rank']+1 : 1;
        $r['ranking'] = $r['ranking'] > 0 ? $r['ranking']  : 1;

        $r['nowrank'] = $rank > 0 ? $rank : 1;
        $answer = $this->answer_model->get(array('openid'=>$this->userinfo['openid'], 'answerid'=>$r['answerid']), '*');
        $r = array_merge($r, $answer);
        $to_memeber = $this->member_model->get(array('openid'=>$r['to_openid']));
        $to_memeber = unserialize($to_memeber['intro']);
        $to_memeber['nickname'] = is_base64_encode($to_memeber['nickname']) ?  base64_decode($to_memeber['nickname']) : $to_memeber['nickname'];
        $to_memeber1 = $this->exam_wechat_member->get(array('openid'=>$r['to_openid']));

        if ($to_memeber1) $to_memeber = array_merge($to_memeber, $to_memeber1);
        $to_memeber['level'] =  $to_memeber['level'] ?  $to_memeber['level'] : 1;
        $content = $this->content->get($r['contentid'], 'typeid');
        $exam = $this->db->get("SELECT qcount FROM #table_exam  WHERE  contentid = {$r['contentid']}");
        $share_msg =  '会计网题库，答对{$num}题，答错{$num1}题，在好友中排行第{$rank}！';
        if ($r['right'] == $exam['qcount'])$share_msg = '会计网题库，我赢了，{$num}题全对，还有比我更牛的吗？';
        $_arr = array('{$num}', '{$num1}', '{$rank}');
        $_r_arr = array($r['right'], $exam['qcount']-$r['right'], $r['nowrank']);
        $share_msg = str_replace($_arr, $_r_arr, $share_msg);
        $this->template->assign('_subject', $content['typeid']);
        $this->template->assign('to_memeber', $to_memeber);
        $this->template->assign('share_msg', $share_msg);
        $this->template->assign('answer', $r);

        $this->template->display('wap/exam/answer');
       // printR($r);
    }

    /**
     * 答题后的一序列操作
     * @param $right
     * @param $size
     * @param $answerid
     * @return bool|int
     */
    private function odds($to_openid, $right, $size, $answerid, $to_answerid, $ajax_false)
    {
        $odds = intval(($right/$size)*100);
        if ($odds > 60) {
            $exper = '200';
            $gold = '200';
        } elseif ($odds < 60) {
            $exper = '50';
            $gold = '50';
        } else {
            $exper = '100';
            $gold = '100';
        }
        if ($to_answerid && $r = $this->answer_model->get(array('openid'=>$to_openid, 'answerid'=>$to_answerid), '`right`')) {

            if ($right > $r['right']) {
                $exper = '200';
                $gold = '200';
            } elseif ($right < $r['right']) {
                $exper = '50';
                $gold = '50';
            } else {
                $exper = '100';
                $gold = '100';
            }
        }
        if ($ajax_false > 0) {
            $exper = '0';
            $gold = '0';
        }
        $_exper = $this->userinfo['exper'] + $exper;
        $sql = "SELECT COUNT(*) AS rank FROM #table_exam_wechat_member wm, #table_wechat_member m, #table_exam_wechat_friend wf WHERE wm.openid=m.openid AND wf.from_openid='{$this->userinfo['openid']}' AND wf.to_openid=m.openid AND wm.exper>{$_exper} ORDER BY wm.exper DESC LIMIT 1";
        $ranking = $this->db->get($sql);
        if ($gold > 0) {
            $level = intval($_exper/1000);
            $sql = "UPDATE #table_exam_wechat_member SET `gold`=`gold`+{$gold},`exper`=`exper`+{$exper},`level`={$level} WHERE `openid`='{$this->userinfo[openid]}'";
            $this->db->query($sql);
        }
        $insert = array(
            'from_openid' => $this->userinfo['openid'],
            'to_openid' => $to_openid,
            'gold' => $gold,
            'ranking' => $ranking['rank'] == 0 ? 1 : $ranking['rank'],
            'exper' => $exper,
            'answerid' => $answerid,
            'to_answerid' => $to_answerid ? $to_answerid : 0,
            'achievement'=> $this->achievement($right)
        );
        $logid = $this->exam_wechat_member_log->insert($insert);
        return $logid;
    }
	
	function dtjl()
    {
		$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $isajax = isset($_GET['isajax']) ? intval($_GET['isajax']) : 0;
		$result = $this->dtjl_page($page);
		$lists = array();
		if ($result) {
			foreach($result as $k=>$v){
				$img = 'true.png';
				if($v['gold'] < 100){
					$img = 'false.png';
				}elseif($v['gold'] == 100){
					$img = 'fat.png';
				}
				$lists[$k]['right'] = $v['right'];
				$lists[$k]['dacuo'] = $v['qcount']-$v['right'];
				$lists[$k]['title'] = "答对{$v['right']}题 答错{$lists[$k]['dacuo']}题";
				$lists[$k]['logtime'] = time_format($v['logtime']);
				$lists[$k]['img'] = $img;
			}
		}else {
			$msg = '<p >没有做题历史</p>';
		}
		if ($isajax) {
            $result = array('state'=>true, 'lists' => $lists);
            if (count($lists) == 0)$result['state'] = false;
            dieJson($result);
        }
		$this->template->assign('lists', $lists);
		$this->template->assign('msg', $msg);
		$this->template->display('wap/exam/dtjl');
	}
	
	function dtjl_page($page = 1, $size =10)
	{
		$offset = ($page-1)*$size;
		$m = strtotime(date('Y-m-01 00:00:00', time()));
		$sql = "SELECT a.right,l.gold,l.logtime,e.qcount FROM #table_exam e,#table_exam_wechat_member_log l,#table_exam_answer a WHERE l.answerid=a.answerid AND a.created>{$m} AND a.contentid=e.contentid AND a.openid='{$this->userinfo['openid']}' ORDER BY a.created DESC limit {$offset},{$size}";
		$list = $this->db->select($sql);
		return $list;
	}
	
	function chengjiu()
	{
		$sql = "SELECT achievement FROM #table_exam_wechat_member_log l WHERE from_openid='{$this->userinfo['openid']}'";
		$result = $this->db->select($sql);
		foreach($result as $v){
			if($v['achievement'] == '路人甲'){
				$data['路人甲']++;
			}elseif($v['achievement'] == '打酱油的'){
				$data['打酱油的']++;
			}elseif($v['achievement'] == '会计新人'){
				$data['会计新人']++;
			}elseif($v['achievement'] == '小出纳'){
				$data['小出纳']++;
			}elseif($v['achievement'] == '大主管'){
				$data['大主管']++;
			}elseif($v['achievement'] == 'CFO'){
				$data['CFO']++;
			}
		}
		$this->template->assign('data', $data);
		$this->template->display('wap/exam/chengjiu');
	}
	
	
	function fankui()
    {
		$questionid = $_GET['id'];
		$this->template->assign('questionid', $questionid);
		$this->template->display('wap/exam/fankui');
	}
	
	/**
     * 题目报错 
     *
     *
     * @param $description 	反馈内容
     * @param $uid int 用户id
     * @param $kffk int 用户反馈类型
     */
	function fankui_get()
    {
		$description = '';
		if(!$_POST['description']){
			if($_POST['kffk']==10){
				$description = '答案不正确'; 
			}else if($_POST['kffk']==11){
				$description = '答案解析有误'; 
			}else if($_POST['kffk']==12){
				$description = '题目有误'; 
			}else if($_POST['kffk']==13){
				$description = '内容乱码'; 
			}
		}else{
			$description = $_POST['description'];
		}
		$data = array
            (
                'description' => htmlspecialchars($description),
                'email' => 'my@kuaiji.com',
                'type' => 'wap',
                'kffk' => value($_POST, 'kffk'),
				'remark' => '题目id:'.value($_POST, 'qid'),
            );
        $this->kefu_feedback = loader::model('kefu_feedback','kuaiji');
		$result = $this->kefu_feedback->insert($data);
		if($result){
			$status = '1';
			$msg = '提交成功';
			$result = array('status'=>$status,'msg'=>$msg);
		}else{
			$status = '-1';
			$msg = '提交失败';
			$result = array('status'=>$status,'msg'=>$msg);
		}
		dieJson($result);
    }

    function favorite()
    {
        if (!$this->userinfo['subjectid']) go(WAP_URL.'exam/subject');

        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
		$isajax = isset($_GET['isajax']) ? intval($_GET['isajax']) : 0;
        $list = $this->get_favorite_question($page);
        foreach ($list as $k=>$v) {
            $list[$k]['created'] = time_format($v['created']);
        }
        if ($isajax) {
            $result = array('state'=>true, 'list' => $list);
            if (count($list) == 0)$result['state'] = false;
            dieJson($result);
        }
        $this->template->assign('list', $list);
        $this->template->display('wap/exam/favorite');
    }
	function shoucang()
    {
        if (!$this->userinfo['subjectid']) go(WAP_URL.'exam/subject');
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $isajax = isset($_GET['isajax']) ? intval($_GET['isajax']) : 0;
        $list = $this->get_shoucang_question($page);
		foreach ($list as $k=>$v) {
            $list[$k]['created'] = time_format($v['created']);
        }
		if ($isajax) {
            $result = array('state'=>true, 'list' => $list);
            if (count($list) == 0)$result['state'] = false;
            dieJson($result);
        }
        $this->template->assign('list', $list);
        $this->template->display('wap/exam/shoucang');
    }
	
	function cuoti()
    {
        if (!$this->userinfo['subjectid']) go(WAP_URL.'exam/subject');
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $isajax = isset($_GET['isajax']) ? intval($_GET['isajax']) : 0;
        $list = $this->get_cuoti_question($page);
		foreach ($list as $k=>$v) {
            $list[$k]['created'] = time_format($v['created']);
        }
		if ($isajax) {
            $result = array('state'=>true, 'list' => $list);
            if (count($list) == 0)$result['state'] = false;
            dieJson($result);
        }
        $this->template->assign('list', $list);
        $this->template->display('wap/exam/cuoti');
    }
	
    private function get_favorite_question($page = 1, $size =10)
    {
        $offset = ($page-1)*$size;
        $subject = $this->_subject[$this->userinfo['subjectid']];
        $sql = "SELECT q.questionid,q.subject,f.favoriteid,f.created FROM #table_exam_question q, #table_exam_member_favorite f WHERE q.questionid=f.questionid AND f.openid='{$this->userinfo['openid']}' AND q.subjectid in({$subject['childids']}) ORDER BY f.created DESC LIMIT {$offset}, $size";
        $list = $this->db->select($sql);

        if (!is_array($list))$list = array();
        $sql = "SELECT q.questionid,q.subject,o.optionid,a.created FROM #table_exam_question q,#table_exam_answer a, #table_exam_answer_option o WHERE a.openid='{$this->userinfo['openid']}' AND o.wrong=0 AND q.subjectid in ({$subject['childids']}) AND q.questionid=o.questionid AND a.answerid=o.answerid AND o.isdel = 0 GROUP BY o.questionid ORDER BY a.created DESC LIMIT {$offset},{$size}";
        $list1 = $this->db->select($sql);
        if($list1)$list = array_merge($list, $list1);
        $lists = array();
        foreach ($list as $v) {
            if ($lists[$v['questionid']]) {
                $v = array_merge($v, $lists[$v['questionid']]);
            }
            $lists[$v['questionid']] =  $v;
        }
        return $lists;
    }
	
	/**
     * 收藏题列表
     *
     *
     * @param $qid int	题目id
     * @param $uid int	用户id
     * @param $subjectid int 科目ID
     */
	function get_shoucang_question($page=1, $size =10)
    {
        $offset = ($page-1)*$size;
		$subject = $this->_subject[$this->userinfo['subjectid']];
		$sql = "SELECT q.questionid,q.subject,f.favoriteid,f.created FROM #table_exam_question q, #table_exam_member_favorite f WHERE q.questionid=f.questionid AND f.openid='{$this->userinfo['openid']}' AND q.subjectid in({$subject['childids']}) ORDER BY f.created DESC LIMIT {$offset}, {$size}";
        $lists = $this->db->select($sql);
		return $lists;
    }
	
	/**
     * 错题列表
     *
     *
     * @param $qid int	题目id
     * @param $uid int	用户id
     * @param $subjectid int 科目ID
     */
	function get_cuoti_question($page=1, $size =10)
    {
        $offset = ($page-1)*$size;
		$subject = $this->_subject[$this->userinfo['subjectid']];
		$sql = "SELECT q.questionid,q.subject,o.optionid,a.created FROM #table_exam_question q,#table_exam_answer a, #table_exam_answer_option o WHERE a.openid='{$this->userinfo['openid']}' AND o.wrong=0 AND q.subjectid in ({$subject['childids']}) AND q.questionid=o.questionid AND a.answerid=o.answerid AND o.isdel = 0 GROUP BY o.questionid ORDER BY a.created DESC LIMIT {$offset},{$size}";
        $lists = $this->db->select($sql);
        return $lists;
    }
	
	
	
	
    function go_favorite()
    {
        $id = intval($_GET['qid']);
        if ($id) {
            $_GET['openid'] = $this->userinfo['openid'];

            if ($id = $this->favorite->add($_GET)){
                $result = array('state'=>true, 'info'=>'收藏成功');
            } else {
                $result = array('state'=>false, 'error'=>$this->favorite->error());
            }
            dieJson($result);
        }
    }
    function remove()
    {
        $id = intval($_GET['qid']);
        $favoriteid = intval($_GET['favoriteid']);
        $optionid = intval($_GET['optionid']);
        if ($id) {
            if ($optionid) {
                $sql = "UPDATE #table_exam_answer a,#table_exam_answer_option o SET o.isdel=1 WHERE a.answerid=o.answerid AND a.openid='{$this->userinfo['openid']}' AND o.questionid={$id}";
                $this->db->query($sql);
            }
            if ($favoriteid)$this->favorite->delete(array('favoriteid'=>$favoriteid, 'openid'=>$this->userinfo['openid']));
            $result = array('state'=>true, 'info'=>'成功');
            dieJson($result);
        }
    }
    function show()
    {

        if (!isset($_GET['id'])) go(WAP_URL.'exam/favorite');
        $id = intval($_GET['id']);
        $r = $this->question_model->get($id);
        //查找收藏的题目
        $f = $this->favorite->get(array('questionid'=>$id, 'openid'=>$this->userinfo['openid']), 'favoriteid');
        $f && $r = array_merge($r, $f);
        //错误题目
        $sql = "SELECT o.optionid FROM #table_exam_question q,#table_exam_answer a, #table_exam_answer_option o WHERE a.openid='{$this->userinfo['openid']}' AND o.wrong=0 AND q.questionid=o.questionid AND a.answerid=o.answerid AND o.isdel = 0 AND o.questionid={$id}";
        $e = $this->db->get($sql);
        $e && $r = array_merge($r, $e);
        if (!$f && !$e)go(WAP_URL.'exam/favorite');
        $r['q_subject'] = $this->propertys[$r['subjectid']]['name'];
        $r['description'] = strip_tags($r['description']);
        $this->template->assign($r);
        $this->template->display('wap/exam/show');

    }
    function pm()
    {
        $exam_wechat_messages_model = loader::model('exam_wechat_messages', 'exam');
        $list = $exam_wechat_messages_model->select('created <= '.time(), '*', 'created DESC', 4);
        foreach ($list as $k => $r) {
            $list[$k]['created'] = time_format($r['created']);

        }
        asort($list);
        $this->template->assign('list' ,$list);
        $this->template->display('wap/exam/pm');

    }
    function get_pm()
    {
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $exam_wechat_messages_model = loader::model('exam_wechat_messages', 'exam');
        $list = $exam_wechat_messages_model->page("messageid != {$id} AND created <= ".time(), '*', 'created DESC', $page, 4);
        $i = 0;
        asort($list);
        foreach($list as $k=> $v){
            $_list[$i] = $v;
            $_list[$i]['created'] = time_format($v['created']);
            $_list[$i]['message'] = str_cut(strip_tags($v['message']), 200,'...');
            $i++;
        }
        $result = array('state'=>true, 'list' => $_list);
        if (count($list) == 0)$result['state'] = false;
        dieJson($result);
    }
    function pm_show()
    {
        if (!isset($_GET['id'])) go(WAP_URL.'exam/favorite');
        $id = intval($_GET['id']);
        $r =  loader::model('exam_wechat_messages', 'exam')->get($id);
        $pm =  loader::model('exam_wechat_messages_pm', 'exam');
        $r['created'] = time_format($r['created']);
        if (!$x = $pm->get(array('openid'=>$this->userinfo['openid'], 'messageid'=>$id))) {
            $pm->insert(array('openid'=>$this->userinfo['openid'], 'messageid'=>$id, 'delstatus'=>1));
        }
        $this->template->assign($r);
        $this->template->display('wap/exam/pm_show');
    }
    private function achievement($right)
    {
        if ($right < 2) return '太菜了，没有获得成就';
        if ($right < 4) return '路人甲';
        if ($right < 6) return '打酱油的';
        if ($right < 8) return '会计新人';
        if ($right < 10) return '小出纳';
        if ($right < 12) return '大主管';
        if ($right < 15) return 'CFO';
    }

    private function menu()
    {
        switch($this->app->action) {
            case 'index':
                $html = '
                    <li></li>
                    <li></li>
                    <li></li>
                    <li></li>
                    <li><a href="javascript:void(0)" data-ajax="false" onclick="window.location.reload()" class="ui-link"><img src="'.IMG_URL.'images/wap/exam/bo-men-5.jpg" width="30">刷新</a></li>';
                break;
            case 'matching':
                $html = '';
                break;
            case 'subject':
                $html = '
                    <li><a href="javascript:void(0)" data-ajax="false" onclick="window.history.back()" class="ui-link"><img src="'.IMG_URL.'images/wap/exam/bo-men-1.jpg" width="30">返回</a></li>
                    <li></li>
                    <li></li>
                    <li></li>
                    <li></li>';
                break;
            case 'answer':
                $html = '<li></li>
                    <li></li>
                    <li><a href="/exam/index" data-ajax="false" class="ui-link"><img src="'.IMG_URL.'images/wap/exam/bo-men-3.jpg" width="30">主页</a></li>
                    <li></li>
                   <li><a href="javascript:void(0)" data-ajax="false" onclick="window.location.reload()" class="ui-link"><img src="'.IMG_URL.'images/wap/exam/bo-men-5.jpg" width="30">刷新</a></li>';

                break;
            case 'problem':
                $html = '<li><a href="javascript:void(0)" data-ajax="false" class="ui-link problem_back"><img src="'.IMG_URL.'images/wap/exam/bo-men-1.jpg" width="30">返回</a></li>
                    <li></li>
                    <li><a href="javascript:void(0)" data-ajax="false" class="ui-link problem_back"><img src="'.IMG_URL.'images/wap/exam/bo-men-3.jpg" width="30">主页</a></li>
                    <li><a href="javascript:void(0);" data-ajax="false" class="favorite ui-link"><img src="'.IMG_URL.'images/wap/exam/bo-men-4.jpg" width="30"><font>收藏</font></a></li>
                    <li><a href="javascript:void(0)" data-ajax="false" onclick="window.location.reload()" class="ui-link"><img src="'.IMG_URL.'images/wap/exam/bo-men-5.jpg" width="30">刷新</a></li>';
                break;
            case 'show':
                $html = '<li><a href="javascript:void(0)" data-ajax="false" onclick="window.history.back()" class="ui-link"><img src="'.IMG_URL.'images/wap/exam/bo-men-1.jpg" width="30">返回</a></li>
        <li><a href="javascript:void(0)" data-ajax="false" class="remove ui-link"><img src="'.IMG_URL.'images/wap/exam/bo-men-2.jpg" width="30">移除</a></li>
        <li><a href="/exam/index" data-ajax="false" class="ui-link"><img src="'.IMG_URL.'images/wap/exam/bo-men-3.jpg" width="30">主页</a></li>
        <li><a href="javascript:void(0);" data-ajax="false" class="favorite ui-link"><img src="'.IMG_URL.'images/wap/exam/bo-men-4.jpg" width="30"><font>收藏</font></a></li>
        <li><a href="javascript:void(0)" data-ajax="false" onclick="window.location.reload()" class="ui-link"><img src="'.IMG_URL.'images/wap/exam/bo-men-5.jpg" width="30">刷新</a></li>';
                break;
            default:
                $html = '<li><a href="javascript:void(0)" data-ajax="false" onclick="window.history.back()" class="ui-link"><img src="'.IMG_URL.'images/wap/exam/bo-men-1.jpg" width="30">返回</a></li>
        <li></li>
        <li><a href="/exam/index" data-ajax="false" class="ui-link"><img src="'.IMG_URL.'images/wap/exam/bo-men-3.jpg" width="30">主页</a></li>
        <li></li>
        <li><a href="javascript:void(0)" data-ajax="false" onclick="window.location.reload()" class="ui-link"><img src="'.IMG_URL.'images/wap/exam/bo-men-5.jpg" width="30">刷新</a></li>';
                break;

        }
        $this->template->assign('menu', $html);
    }
    function __destruct()
    {
        if (!$this->is_ajax() && !$this->in_wechat)$this->template->display('wap/exam/footer');
    }
    /**
     * 生成一张试卷
     * @param $catid
     * @param $subjectid
     * @param $options
     * @return mixed
     */
    private function insertexam($catid, $subjectid, $options)
    {
        $exam = array(
            'modelid' => modelid('exam'),
            'catid' => $catid,
            'title' => $this->propertys[$subjectid]['name'] . '组卷模考',
            'isday' =>  0,
            'subtitle' =>'',
            'description' => '',
            'tags' =>'',
            'integral' =>  0,
            'notwrite' =>  1,
            'thumb' =>'',
            'examtime' =>  60,
            'starttime' =>'',
            'endtime' =>'',
            'maxanswers' =>'',
            'minhours' => 24,
            'proids' =>'',
            'typeid' => $subjectid,
            'weight' => 78, //wechat 使用
            'sectionids' =>'',
            'placeid' =>'',
            'published' =>'',
            'unpublished' =>'',
            'related_keywords' =>'',
            'allowcomment' => 1,
            'status' => 1,
            'seotitle' =>'',
            'seodescription' =>'',
            'seocode' =>'',
        );
        $exam['qtype'] = $options;

        return  loader::model('admin/exam', 'exam')->add($exam);
    }
    /**
     * 科目数据
     */
    private function get_propertys()
    {
        $this->propertys = common_data('property_0', 'brand');
        foreach($this->_subjectids as $id) {
            $_types = get_property_child($id);

            $this->_subject[$id] = $this->propertys[$id];
            $this->_subject[$id]['child'] = $_types;

        }
        $this->template->assign('subject', $this->_subject);
    }
}
