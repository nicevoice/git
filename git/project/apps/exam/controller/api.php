<?php

class controller_api extends exam_controller_abstract
{
	private $exam,$cache,$content,$db,$validation,$report;
	
	function __construct(& $app)
	{
		parent::__construct($app);
		$this->content = loader::model('content', 'system');
		$this->exam = loader::model('exam', 'exam');
        $this->cache = & factory::cache();
        $this->db = & factory::db();
        $this->answer = loader::model('answer', 'exam');
        //$this->cache = & factory::cache();
        $this->question = loader::model('question', 'exam');
		$this->report = loader::model('report', 'exam');
        include_once app_dir('exam').'lib/validation.php' ;
	}
	
	/**
     * 学习报告  api
     *
     *
     * @param $uid 	用户id
     */
	function report()
	{
		$uid = intval($_GET['uid']);
		$uk = trim($_GET['uk']);
		if (!ukdecode($uid, $uk)){
			$status = '2';
			$msg = 'cat‘t find member';
			$res = array('status'=>$status,'msg'=>$msg,'data'=>array());
			echo $this->json->encode($res);exit;
		}
		$result = $this->report->get('uid='.$uid);
		if(!$result){
			$status = '-1';
			$msg = '没有学习报告';
			$result = array('status'=>$status,'msg'=>$msg);
		}
		$ctime = ceil($result['ctime']/$result['answers']);//平均耗时
		$correct = round($result['correct']/$result['answers']*100);//正确率
		$lore = explode(",",$result['lore']); //知识点
		$z_lore = explode(",",$this->propertys['110000']['childids']);//总知识点
		$lore_num = round(count($lore)/count($z_lore)*100);//掌握知识点百分率
		$learntime = ceil($result['learntime']/3600);//学习时长
		$data = array('days'=>(int)$result['days'],'learntime'=>(int)$learntime,'answers'=>(int)$result['answers'],'ctime'=>(int)$ctime,'lore'=>(int)$lore_num,'correct'=>(int)$correct);
		$status = '1';
		$msg = '成功';
		$result = array('status'=>$status,'msg'=>$msg,'data'=>$data);
		echo $this->json->encode($result);
	}
	
	
	/**
     * 登录天数  api
     *
     *
     * @param $uid 	用户id
     */
	function days()
	{
		$uid = intval($_GET['uid']);
		$uk = trim($_GET['uk']);
		if (!ukdecode($uid, $uk)){
			$status = '2';
			$msg = 'cat‘t find member';
			$res = array('status'=>$status,'msg'=>$msg,'data'=>array());
			echo $this->json->encode($res);exit;
		}
		$result = $this->report->get('uid='.$uid);
		$times = strtotime(date('Y-m-d 00:00:00', time()));
		$data = array('days'=>$result['days']);
		if($result['logtime'] < $times){
			$data = array('days'=>++$result['days'], 'logtime'=>time());
			$this->report->update($data, "uid=$uid");
		}
		$status = '1';
		$msg = '登录天数+1';
		$result = array('status'=>$status,'msg'=>$msg,'data'=>$data);
		echo $this->json->encode($result);
	}
	
	/**
     * 学习时长  api
     *
     *
     * @param $uid 	用户id
     */
	function learntime()
	{
		$uid = intval($_GET['uid']);
		$uk = trim($_GET['uk']);
		if (!ukdecode($uid, $uk)){
			$status = '2';
			$msg = 'cat‘t find member';
			$res = array('status'=>$status,'msg'=>$msg,'data'=>array());
			echo $this->json->encode($res);exit;
		}
		$ltime = $_GET['ltime'];
		$result = $this->report->get('uid='.$uid);
		$learntime = $result['learntime'] + $ltime;
		$data = array('learntime'=>$learntime);
		$this->report->update($data, "uid=$uid");
		$status = '1';
		$msg = '数据更新成功';
		$result = array('status'=>$status,'msg'=>$msg);
		echo $this->json->encode($result);
	}
	
	
	/**
     * 题目报错  api
     *
     *
     * @param $description 	反馈内容
     * @param $uid int 用户id
     * @param $kffk int 用户反馈类型
     */
	function fankui()
    {
		$description = '';
		if(!$_GET['description']){
			if($_GET['kffk']==10){
				$description = '答案不正确'; 
			}else if($_GET['kffk']==11){
				$description = '答案解析有误'; 
			}else if($_GET['kffk']==12){
				$description = '题目有误'; 
			}else if($_GET['kffk']==13){
				$description = '内容乱码'; 
			}
		}else{
			$description = $_GET['description'];
		}
		$data = array
            (
                'description' => htmlspecialchars($description),
                'email' => 'my@site.com',
                'type' => 'app',
                'createdby' => value($_GET, 'uid'),
                'kffk' => value($_GET, 'kffk'),
                'remark' => '题目id:'.value($_GET, 'qid'),
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
        echo $this->json->encode($result);
    }
	
	
	/**
     * 错题列表  api
     *
     *
     * @param $qid int	题目id
     * @param $uid int	用户id
     * @param $subjectid int 科目ID
     */
	function get_cuoti_question()
    {
		$subject = intval($_GET['subject']);
		$uid = intval($_GET['uid']);
		$page = $_GET['page'] ? intval($_GET['page']) : 1;
		$size = $_GET['size'] ? intval($_GET['size']) : 10;
        $offset = ($page-1)*$size;
		$sql = "SELECT q.questionid,q.subject,o.optionid,a.created,o.wrong FROM #table_exam_question q,#table_exam_answer a, #table_exam_answer_option o WHERE a.createdby='{$uid}' AND o.wrong=0 AND q.subjectid in ({$subject}) AND q.questionid=o.questionid AND a.answerid=o.answerid AND o.isdel = 0 GROUP BY o.questionid ORDER BY a.created DESC LIMIT {$offset},{$size}";
        $lists = $this->db->select($sql);
		//获取每一道题的详细信息
		foreach($lists as $k=>$v){
			$data[$k] = $this->question->get($v['questionid']);
			$data[$k]['created'] = date("Y-m-d H:i",$v['created']);
		}
		//对每道题的数据再次处理
		$k = 0;
		foreach($data as $val) {
			$_val['knowledge'] =  $this->propertys[$val['knowledgeid']]['name'];
			$_val['subject_name'] =  $this->propertys[$val['subjectid']]['name'];
			$_val['qtype'] =  $this->propertys[$val['qtypeid']]['name'];
			$_val['questionid'] = $val['questionid'];
			$_val['subject'] = $val['subject'];
			$_val['description'] = stripslashes_deep(htmlspecialchars_decode($val['description']));
			$_val['created'] = $val['created'];
			$_detail['questionid'] = $val['questionid'];
			$_detail['subject'] = $val['subject'];
			$_detail['answer'] = $val['answer'];
			$_detail['type'] = $val['type'];
			$_detail['option'] = $val['option'];
			$_data[$k] =  $_val;
			$_data[$k]['detail'] = $_detail;
			$k++;
		}
		if($_data){
			$status = '1';
			$msg = '加载成功';
			$result = array('status'=>$status,'msg'=>$msg, 'total'=> $total,'data'=>$_data);
		}else{
			$status = '-1';
			$msg = '加载失败';
			$result = array('status'=>$status,'msg'=>$msg);
		}
        echo $this->json->encode($result);
    }
	
	
	/**
     * 收藏题列表  api
     *
     *
     * @param $qid int	题目id
     * @param $uid int	用户id
     * @param $subjectid int 科目ID
     */
	function get_favorite_question()
    {
		$subject = intval($_GET['subject']);
		$uid = intval($_GET['uid']);
		$page = $_GET['page'] ? intval($_GET['page']) : 1;
		$size = $_GET['size'] ? intval($_GET['size']) : 10;
        $offset = ($page-1)*$size;
        $sql = "SELECT q.questionid,q.subject,f.favoriteid,f.created FROM #table_exam_question q, #table_exam_member_favorite f WHERE q.questionid=f.questionid AND f.createdby='{$uid}' AND q.subjectid in({$subject}) ORDER BY f.created DESC LIMIT {$offset}, {$size}";
        $lists = $this->db->select($sql);
		//获取每一道题的详细信息
		foreach($lists as $k=>$v){
			$data[$k] = $this->question->get($v['questionid']);
			$data[$k]['created'] = date("Y-m-d H:i",$v['created']);
		}
		//对每道题的数据再次处理
		$k = 0;
		foreach($data as $val) {
			$_val['knowledge'] =  $this->propertys[$val['knowledgeid']]['name'];
			$_val['subject_name'] =  $this->propertys[$val['subjectid']]['name'];
			$_val['qtype'] =  $this->propertys[$val['qtypeid']]['name'];
			$_val['questionid'] = $val['questionid'];
			$_val['subject'] = $val['subject'];
			$_val['description'] = stripslashes_deep(htmlspecialchars_decode($val['description']));
			$_val['created'] = $val['created'];
			$_detail['questionid'] = $val['questionid'];
			$_detail['subject'] = $val['subject'];
			$_detail['answer'] = $val['answer'];
			$_detail['type'] = $val['type'];
			$_detail['option'] = $val['option'];
			$_data[$k] =  $_val;
			$_data[$k]['detail'] = $_detail;
			$k++;
		}
		if($_data){
			$status = '1';
			$msg = '加载成功';
			$result = array('status'=>$status,'msg'=>$msg, 'total'=> $total,'data'=>$_data);
		}else{
			$status = '-1';
			$msg = '加载失败';
			$result = array('status'=>$status,'msg'=>$msg);
		}
        echo $this->json->encode($result);
    }
	
	/**
     * 题库收藏  api
     *
     *
     * @param $qid int	题目id
     * @param $uid int
     */
	function go_favorite()
    {
        if (intval($_GET['qid'])) {
			$this->favorite = loader::model('exam_favorite', 'exam');
            if ($id = $this->favorite->add($_GET)){
                $result = array('status'=>1, 'msg'=>'收藏成功');
            } else {
                $result = array('status'=>-1, 'msg'=>$this->favorite->error());
            }
			echo $this->json->encode($result);
        }
    }
	
	/**
     * 取消题库收藏  api
     *
     *
     * @param $qid int	题目id
     * @param $uid int
     */
	function remove_favorite()
    {
        if (intval($_GET['qid'])) {
			$this->favorite = loader::model('exam_favorite', 'exam');
            if ($id = $this->favorite->delete("questionid={$_GET['qid']} and createdby={$_GET['uid']}")){
                $result = array('status'=>1, 'msg'=>'取消成功');
            } else {
                $result = array('status'=>-1, 'msg'=>$this->favorite->error());
            }
			echo $this->json->encode($result);
        }
    }
	
	/**
     * 获取通知  api
     *
     *
     * @param $maxid int
     * @param $num int
     * @param $subjectid int 科目ID
     */
	public function tongzhi()
    {
		$this->message = loader::model('exam_wechat_messages', 'exam');
        $page = isset($_GET['page']) ? intval($_GET['page']) : 0;
        $pagesize = isset($_GET['pagesize']) ? intval($_GET['pagesize']) : 30;
        $data = $this->message->page('', '*', 'created DESC', $page, $pagesize);
		$count = $this->message->count();
        foreach($data as $k=>$v) {
            $data[$k]['created'] = time_format($v['created']);
        }
		$status = '1';
		$msg = '加载成功';
        $result = array('status'=>$status,'msg'=>$msg,'total'=>$count, 'data'=>$data);
        echo $this->json->encode($result);
    }
	
	/**
     * 新增题库API  api
     *
     *
     * @param $maxid int
     * @param $num int
     * @param $subjectid int 科目ID
     */
	function getexam(){
		$maxid = $_REQUEST['maxid'] ? intval($_REQUEST['maxid']) : 1;
		$num = $_REQUEST['num'] ? intval($_REQUEST['num']) : 30;
		$subjectid = $_REQUEST['subjectid'];
		$where="subjectid = {$subjectid}";
		$count = $this->question->count($where);
		$n = ceil($count/$num);
		$maxid = ($maxid > $n) ? $n-1 : $maxid ;
		$data = $this->question->ls($where, '*', '', $maxid, $num);
		$k = 0;
		foreach($data as $val) {
			$_val['knowledge'] =  $this->propertys[$val['knowledgeid']]['name'];
			$_val['subject_name'] =  $this->propertys[$val['subjectid']]['name'];
			$_val['qtype'] =  $this->propertys[$val['qtypeid']]['name'];
			$_val['questionid'] = $val['questionid'];
			$_val['subject'] = $val['subject'];
			$_val['description'] = stripslashes_deep(htmlspecialchars_decode($val['description']));
			$_detail['questionid'] = $val['questionid'];
			$_detail['subject'] = $val['subject'];
			$_detail['answer'] = $val['answer'];
			$_detail['type'] = $val['type'];
			$_detail['option'] = $val['option'];

			//unset($val['knowledgeid']);
			$lists[$k] =  $_val;
			$lists[$k]['detail'] = $_detail;
			$k++;
		}
		$status = '1';
		$msg = '加载成功';
		$res = array('status'=>$status,'msg'=>$msg, 'maxid'=> $maxid, 'appurl'=>'','data'=>$lists);
        echo $this->json->encode($res);exit;
	}
	
	
    /**
     * 题库栏目  api
     *
     *
     * @param $page int
     * @param $pagesize int
     * @param $subjectid int 科目ID
     * @version 版本号
     */
    public function category()
    {
        $_catid = $_GET['cid'] ? intval($_GET['cid']) : 0;
        $uid = $_GET['uid'] ? intval($_GET['uid']) : 0;
        $uk = trim($_GET['uk']);
        foreach ($this->exam_catid as $catid) {
            $cats[] = $this->category[$catid];
            $subjects[] = $this->category[$catid]['typeid'];
        }
        if (!$_catid){
        	$data = $cats;
        }else{
        	$data = $this->category[$_catid];
        }
        if ($uk && $uid) {
            if (ukdecode($uid, $uk)){
                $golds = $this->exam->gold2cat($uid);
                $ranks = $this->exam->ranking2uid($uid, $subjects);
            }
        }
        $status = '-1';
        $msg = '数据返回异常';
        if(is_array($data) && count($data) > 0){
        	$status = 1;
        	$msg = '数据返回正常';
        }

        foreach ($data as $v) {
            $result = array(
                'cid' => $v['catid'],
                'title' => $v['name'],
                'subjectid'=>$v['typeid'],
               // 'rank'=>$rank['typeid'],
            );

            if ($golds[$v['catid']]) {
                $result = array_merge($result,  $golds[$v['catid']]);
            } else {
                $golds[$v['catid']] = array(
                    'gold' => null,
                    'desc' => null,
                    'day' => null,
                );
                $result = array_merge($result,  $golds[$v['catid']]);
            }
            if ($ranks[$v['typeid']]){
                $result = array_merge($result,  $ranks[$v['typeid']]);
            } else {
                $golds[$v['catid']] = array(
                    'rank' => 0,
                );
                $result = array_merge($result,  $golds[$v['catid']]);
            }
            $_result[] = $result;
        }

        $res = array('status'=>$status,'msg'=>$msg, 'version'=> $this->setting['release'], 'appurl'=>'','data'=>$_result);
        echo $this->json->encode($res);exit;
    }

    public function city()
    {
        $citys = get_property_child(2);
        $res = array('status'=>1,'msg'=>'中华人民共和国城市ID','data'=>$citys);
        echo $this->json->encode($citys);exit;
    }
    /**
     * 二级栏目 api
     *
     *
     * @param $page int
     * @param $pagesize int
     * @param $subjectid int 科目ID
     */
    public function subject()
    {
        $subjectid = $_GET['subjectid'] ? intval($_GET['subjectid']) : 0;
        $uid = $_GET['uid'] ? intval($_GET['uid']) : 0;
        $uk = trim($_GET['uk']);
        $childids = $this->propertys[$subjectid]['childids'];
        if ($uk && $uid) {
            if (ukdecode($uid, $uk)){

            }
        }
        $data = array();
        if ($childids){
            foreach(explode(',', $childids) as $k=> $v) {
                $property = $this->propertys[$v];
                $data[$k]['subjectid'] = $v;
                $data[$k]['title'] = $property['name'];
            }
        }
        $status = '-1';
        $msg = '数据返回异常';
        if(is_array($data) && count($data) > 0){
        	$status = 1;
        	$msg = '数据返回正常';
        }
        $res = array('status'=>$status,'msg'=>$msg,'data'=>$data);
        echo $this->json->encode($res);exit;
        
    }

    /**
     * 获取随机题
     *
     *
     */
    public function question()
    {
        $subjectid = intval($_GET['subjectid']);
		
        $type = $_GET['type'] ? $_GET['type'] : 'day';
        if (!$subjectid) {
            $status = '-1';
            $msg = 'cat‘t find subjectid';
            $res = array('status'=>$status,'msg'=>$msg,'data'=>array());
            echo $this->json->encode($res);exit;
        }
        $uid = $_GET['uid'] ? intval($_GET['uid']) : 0;
        $uk = trim($_GET['uk']);
        $key = md5('api_question_'.$subjectid.$type);
        if ($type == 'day') {
            if (!ukdecode($uid, $uk)){
                $status = '2';
                $msg = 'cat‘t find member';
                $res = array('status'=>$status,'msg'=>$msg,'data'=>array());
                echo $this->json->encode($res);exit;
            }

            //checkday
            if (!$result = $this->cache->get($key)):
            $sql = "SELECT c.contentid,c.catid,e.integral FROM #table_exam e,#table_content c WHERE e.contentid=c.contentid AND e.isday=1 AND c.typeid={$subjectid} ORDER BY contentid DESC";
            $exams = $this->db->get($sql);
            if (!$exams) {
                $status = '-1';
                $msg = 'cat‘t find exam';
                $res = array('status'=>$status,'msg'=>$msg,'data'=>array());
                echo $this->json->encode($res);exit;
            }

            /**
             * 判断每日一练是否做过
             */
            $answer_model = loader::model('answer', 'exam');
            $day =strtotime(date("Y-m-d",time()));
            $answer = $answer_model->get("contentid={$exams['contentid']} AND createdby={$uid}  AND created>=".$day,"`right`");
            if ($answer) {
                $status = '3';
                $msg = 'Already vies to answer first';
                $res = array('status'=>$status,'msg'=>$msg,'data'=>array());
                echo $this->json->encode($res);exit;
            }
            $exams = $this->exam->get($exams['contentid']);
            $k = 0;
            foreach ($exams['question'] as $question) {
                foreach($question['question'] as $val) {
                    $_val['knowledge'] =  $this->propertys[$val['knowledgeid']]['name'];
                    $_val['subject_name'] =  $this->propertys[$val['subjectid']]['name'];
                    $_val['qtype'] =  $this->propertys[$val['qtypeid']]['name'];
                    $_val['questionid'] = $val['questionid'];
                    $_val['subject'] = $val['subject'];
                    $_val['description'] = stripslashes_deep(htmlspecialchars_decode($val['description']));
                    $_detail['questionid'] = $val['questionid'];
                    $_detail['subject'] = $val['subject'];
                    $_detail['answer'] = $val['answer'];
                    $_detail['type'] = $val['type'];
                    $_detail['option'] = $val['options'];

                    //unset($val['knowledgeid']);
                    $lists[$k] =  $_val;
                    $lists[$k]['detail'] = $_detail;
                    $_qtype[$val['qtypeid']][] = $val['questionid'];
                    $k++;
                }
            }
            $result['title'] = $exams['title'];
            $result['integral'] = $exams['integral'];
            $result['examid'] = $exams['contentid'];
            $result['lists'] = $lists;
            $this->cache->set($key, $result, 20);
            endif;
        } else if($type == 'rand') {
            $result = $this->cache->get($key);
            if (!$result && empty($_GET['getsize'])):
                /**
                 * 获取catid
                 */
                foreach($this->exam_catid as $cat) {
                    $cats = $this->category[$cat];
                    $catids[$cats['typeid']] = $cat;
                }

                foreach($this->project_t as $pro) {
                    if ($subjectid == $pro['proid'] || in_array($subjectid, array_keys($pro['child'])))$catid  = $catids[$pro['proid']];
                }
                /*if (is_numeric($subjectid) && $subjectid > 0) {
                    $where[] = "subjectid={$subjectid}";
                }
                $where && $where = implode(' AND ', $where);*/
                /*//随机获取的参数
                $randquestion = config::get('exam', 'randquestion');
                if ($_GET['getsize']) {
                    //随机获取的参数
                    $randquestion = config::get('exam', 'randquestion_300');
                    $_page  = $_GET['page'] ? intval($_GET['page']) : 0;
                }*/
                /*//获取科目题量
                $cache2subject = $this->question->inserCache2subject($subjectid);

                $addition = null;
                foreach ($randquestion as $k=>$type) {
                    $_ids = explode(',',$type['ids']);
                    $_count = 0;
                    foreach($_ids as $i) {
                        $_count = $_count + $cache2subject[$i];
                    }
                    $page = $_page ? $_page : mt_rand(0, (ceil($_count/$type['size']) - 1));
                    $addition  = " AND qtypeid in({$type['ids']}) ";
                    $lists[$k] = loader::model('question', 'exam')->ls($where.$addition, 'questionid,subjectid,qtypeid,subject,knowledgeid,answerid,description', 'questionid DESC', $page, $type['size'], false);
                }

                $lists = array_merge($lists['radio'], $lists['checkbox']);*/
                $vals = exam_redis_get('exam_app_'.$subjectid);
                if (!$vals) {
                    $status = '-1';
                    $msg = '数据返回异常';
                    $res = array('status'=>$status,'msg'=>$msg,'data'=>$vals);
                    echo $this->json->encode($res);
                    exit;
                }
                $key = array_rand($vals, 1);
                $options = $vals[$key];
                if (ukdecode($uid, $uk)){
                    $contentid = $this->insertexam($catid, $subjectid, $options);
                    $exams = $this->exam->get($contentid);
                    $questions = $exams['question'];
                } else {
                    $questions = $this->exam->get_app_question($options);
                }
                $k = 0;
                foreach ($questions as $question) {
                    foreach($question['question'] as $val) {
                        $_val['knowledge'] =  $this->propertys[$val['knowledgeid']]['name'];
                        $_val['subject_name'] =  $this->propertys[$val['subjectid']]['name'];
                        $_val['qtype'] =  $this->propertys[$val['qtypeid']]['name'];
                        $_val['questionid'] = $val['questionid'];
                        $_val['subject'] = $val['subject'];
                        $_val['description'] = stripslashes_deep(htmlspecialchars_decode($val['description']));
                        $_detail['questionid'] = $val['questionid'];
                        $_detail['subject'] = $val['subject'];
                        $_detail['answer'] = $val['answer'];
                        $_detail['type'] = $val['type'];
                        $_detail['option'] = $val['option'] ? $val['option'] : $val['options'];
                        $lists[$k] =  $_val;
                        $lists[$k]['detail'] = $_detail;
                        $_qtype[$val['qtypeid']][] = $val['questionid'];
                        $k++;
                    }
                }
                $result['title'] = $this->propertys[$subjectid]['name'] . '组卷模考';
                $result['examid'] = $contentid;
                $result['integral'] = 0;
                $result['lists'] = $lists;
                $this->cache->set($key, $result, 20);
            endif;

        } else {
            $status = '-1';
            $msg = 'type is error';
            $res = array('status'=>$status,'msg'=>$msg,'data'=>array());
            echo $this->json->encode($res);exit;
        }
        $status = '-1';
        $msg = '数据返回异常';
        if(is_array($result['lists']) && count($result['lists']) > 0){
            $status = 1;
            $msg = '数据返回正常';
        }
        $res = array('status'=>$status,'msg'=>$msg,'data'=>$result);
        echo $this->json->encode($res);
        exit;
    }

    public function cexam(){
        $cache2subject = $this->question->inserCache2subject();
    }

    /**
     * 生成一张试卷
     * @param $catid
     * @param $subjectid
     * @param $options
     * @return mixed
     */
    public function insertexam($catid, $subjectid, $options)
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
            'weight' => 79, //标记app使用
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
     * 提交答案
     */
    public function complete(){
    	$contentid = intval(trim($_REQUEST['examid']));
        $type = $_GET['type'] ? $_GET['type'] : '';
        $uid = $_REQUEST['uid'] ? intval($_REQUEST['uid']) : 0;
        $examtime = $_REQUEST['examtime'] ? $_REQUEST['examtime'] : 100;
        $plantform = isset($_REQUEST['plantform_id'])?intval($_REQUEST['plantform_id']):'1';
        $m = floor($examtime/60);
        $s = $examtime%60;
        $s = sprintf("%02d", $s);
        $examtime = $m.':'.$s;
        $uk = trim($_REQUEST['uk']);
    	if(!$contentid || !$uid || !$uk){
    		$status = '-1';
    		$msg = '关键数据为空!';
    		$res = array('status'=>$status,'msg'=>$msg,'data'=>array());
    		echo $this->json->encode($res);exit;
    	}
        if (!ukdecode($uid, $uk)){
            $status = '2';
            $msg = 'cat‘t find member';
            $res = array('status'=>$status,'msg'=>$msg,'data'=>array());
            echo $this->json->encode($res);exit;
        }
    	$status = '-1';
    	$msg = '保存异常,请重新再试';
    	if ($id = loader::model('answer', 'exam')->add($contentid, array('type'=>$type,'userid'=>$uid, 'answer' => $_REQUEST['answer'],'plantform_id'=>$plantform,'examtime'=>$examtime, 'isfinish'=>0))){
			$answer_num = count($_REQUEST['answer']);
			$ctime = $_REQUEST['ctime'] ? intval($_REQUEST['ctime']):0;
			$correct = $_REQUEST['correct'] ? intval($_REQUEST['correct']):0;
			$result = $this->report->get('uid='.$uid);
			$answers = $answer_num + $result['answers'];
			$ctime = $ctime + $result['ctime'];
			$correct = $correct + $result['correct'];
			if($result['lore']){
				$knowledge = explode(",",$result['lore']);
			}
			foreach($_REQUEST['answer'] as $k=>$v){
				$q_key[] = $k;
			}
			$qkey = implode(",",$q_key);
			$sql = "select knowledgeid from cms_exam_question where questionid in({$qkey})";
			$result = $this->db->select($sql);
			foreach($result as $k=>$v){
				$knowledge[] = $v['knowledgeid'];
			}
			$knowledge = array_unique($knowledge);
			$knowledge = implode(",",$knowledge);
			
			$data = array('answers'=>$answers,'ctime'=>$ctime,'lore'=>$knowledge,'correct'=>$correct);
			$this->report->update($data, "uid=$uid");
			
    		$status = 1;
    		$msg = '提交成功!';
    	}
    	$res = array('status'=>$status,'msg'=>$msg,'data'=>array('id'=>$id));
    	echo $this->json->encode($res);exit;
    }


    /**
     * 金币排行榜
     */

    public function rank()
    {
        $subjectid = intval($_GET['subjectid']);
        $plantform = isset($_GET['plantform_id'])?intval($_GET['plantform_id']):'';
        $size = $_GET['size'] ? intval($_GET['size']) : 10;
		$uid = $_REQUEST['uid'] ? intval($_REQUEST['uid']) : 0;
        $uk = trim($_GET['uk']);
		if($uid){
			if (!$subjectid || !$uid || !$uk) {
				$status = '-1';
				$msg = 'request is error.';
				$res = array('status' => $status, 'msg' => $msg);
				echo $this->json->encode($res); exit;
			}
			if (!ukdecode($uid, $uk)) {
				$status = '2';
				$msg = 'cat‘t find member';
				$res = array('status' => $status, 'msg' => $msg, 'data' => array());
				echo $this->json->encode($res);exit;
			}
		}
        $key = md5('api_rank_' . $subjectid);
        if ( 1 || !$result = $this->cache->get($key)):
            $sql_platform = null;
            if($plantform)$sql_platform=" and plantform_id='".$plantform."'";
            $lists = loader::model('exam_rank', 'exam')->select("subjectid={$subjectid} ".$sql_platform, '*', 'gold DESC', $size);
            $ids = '';
            foreach ($lists as $k=>$val) {
                $ids                    .= $val['user_id'].',';
                $result[$k]['uid']      =  $val['user_id'];
                $result[$k]['gold']     =  $val['gold'];
                $result[$k]['avatar']   = UC_URL . "avatar.php?uid={$val['user_id']}&size=small";
            }
            //$user_list = loader::model('exam_member', 'exam')->getUsers($ids);
			$uids = ' userid in('.trim($ids,','). ')';
			$member_ftont = loader::model('member_detail', 'member');
			$user_list = $member_ftont->select($uids,'nickname,name,userid');
			foreach ($user_list as $v){
				if(isset($v['nickname'])) $v['name'] = $v['nickname']?$v['nickname']:$v['name'];
				unset($v['nickname']);
				$r[$v['userid']] = $v;
			}
			
			foreach($result as $k=>$v){
				$user_id =$v['uid'];
				if(isset($r[$user_id])){
					$result[$k]['name'] =$r[$user_id]['name'];
				}else{
					$result[$k]['name'] ="";
				}
			}
            $this->cache->set($key, $result, 60);
        endif;
		//当有用户登陆时显示当前用户的金币排名
		if($uid){
			$exam_rank = loader::model('exam_rank', 'exam');
			$sql_platform = null;
			if($plantform)$sql_platform=" and plantform_id='".$plantform."'";
			$lists = $exam_rank->get("subjectid={$subjectid} and user_id={$uid}".$sql_platform);
			$count = $exam_rank->count("gold > {$lists['gold']} and subjectid={$subjectid} " . $sql_platform);
			$count = ++$count;
			//$user_list = loader::model('exam_member', 'exam')->getUsers($uid);
			$user_list = loader::model('member_detail', 'member')->get($uid);
			if($user_list){
				$username = $user_list['nickname'];
			}else{
				$username ="";
			}
			$result[] = array('uid'=>$uid,'gold'=>$lists['gold'],'avatar'=>UC_URL . "avatar.php?uid={$uid}&size=small",'name'=>$username,'paiming'=>$count);
		}
		$res = array('status' => 1, 'msg' => '金币排行榜', 'data' => $result);
        echo $this->json->encode($res);
        exit;
    }

    /**
     * 错误本
     */
    function error(){
        $subjectid = intval(trim($_REQUEST['subjectid']));
        $uid = $_REQUEST['uid'] ? intval($_REQUEST['uid']) : 0;
        $uk = trim($_GET['uk']);
        if (!$subjectid || !$uid || !$uk) {
            $status = '-1';
            $msg = 'request is error.';
            $res = array('status' => $status, 'msg' => $msg);
            echo $this->json->encode($res); exit;
        }
        if (!ukdecode($uid, $uk)) {
            $status = '2';
            $msg = 'cat‘t find member';
            $res = array('status' => $status, 'msg' => $msg, 'data' => array());
            echo $this->json->encode($res);exit;
        }

        $page  = (int)$_REQUEST['page'];
        $count = $this->question->get_error_question_count(0,0, $uid);
        $page = $page > 0 ? $page : 1;

        $lists = $this->question->get_error_question(0, 0, $page, 20, $uid);
        $num = count($lists);
        if ($num < 1) {
            $status = '-1';
            $msg = 'error question is not!';
            $res = array('status' => $status, 'msg' => $msg);
            echo $this->json->encode($res);  exit;
        }
        foreach ($lists as $k=>$val) {
            $val['knowledge'] =  $this->propertys[$val['knowledgeid']]['name'];
            $val['subject_name'] =  $this->propertys[$subjectid]['name'];
            $val['qtype'] =  $this->propertys[$val['qtypeid']]['name'];
            $lists[$k] = $val;
        }
        $status = 1;
        $msg = '返回数据成功!';
        $res = array('status' => $status, 'msg' => $msg, 'total' =>$count,'data' => $lists);
        echo $this->json->encode($res);exit;
    }

    /**
     * 移除错误题目
     */
    function error_move() {
        $questionid = $_REQUEST['questionid'];
        $uid = $_REQUEST['uid'] ? intval($_REQUEST['uid']) : 0;
        $uk = trim($_REQUEST['uk']);
        if (!$questionid || !$uid || !$uk) {
            $status = '-1';
            $msg = 'request is error!';
            $res = array('status' => $status, 'msg' => $msg);
            echo $this->json->encode($res);
            exit;
        }
        if (!ukdecode($uid, $uk)) {
            $status = '2';
            $msg = 'cat‘t find member';
            $res = array('status' => $status, 'msg' => $msg, 'data' => array());
            echo $this->json->encode($res);
            exit;
        }
        $id = loader::model('answer_option', 'exam')->error_remove($questionid, $uid);
		if($id){
			$status = 1;
			$msg = 'success!';
			$res = array('status' => $status, 'msg' => $msg, 'data' => $questionid);
			echo $this->json->encode($res);
			exit;
		}else{
			$status = '-1';
            $msg = 'request is error!';
            $res = array('status' => $status, 'msg' => $msg);
            echo $this->json->encode($res);
            exit;
		}
    }
	
    public function discuss()
    {
        $subjectid = intval(trim($_REQUEST['subjectid']));
        $page = $_REQUEST['page'] ? intval($_REQUEST['page']) : 1; 
        $size = 10;
        $exam_thread = loader::model('exam_thread', 'exam');
        $exam_post = loader::model('exam_post', 'exam');
        $lists = $exam_thread->page("subjectid={$subjectid}", '*', 'threadid DESC', $page, $size);
        foreach ($lists as $k=>$val) {
            //$user = loader::model('exam_member', 'exam')->getUser($val['user_id']);
			$user = loader::model('member_detail', 'member')->get($val['user_id']);
            $result[$val['tid']]['uid'] = $val['user_id'];
            $result[$val['tid']]['title'] = $val['title'];
            $result[$val['tid']]['tid'] = $val['tid'];
            $result[$val['tid']]['avatar'] = UC_URL . "avatar.php?uid={$val['user_id']}&size=small";
            $result[$val['tid']]['name'] = $user['nickname'] ? $user['nickname'] : $user['name'];
            $result[$val['tid']]['replies'] = $exam_post->count("tid={$val['tid']}");
			$result[$val['tid']]['dateline'] = date('Y-m-d H:i:s',$val['created']);
        }
        $status = 1;
        $msg = 'success!';
        $res = array('status' => $status, 'msg' => $msg, 'data' => array_values($result));
        echo $this->json->encode($res);
        exit;

    }

    public function discuss_detail()
    {
        $tid = intval(trim($_REQUEST['tid']));
        $uid = $_REQUEST['uid'] ? intval($_REQUEST['uid']) : 0;
        $uk = trim($_GET['uk']);
        $page = $_REQUEST['page'] ? intval($_REQUEST['page']) : 1;
        $size = $_REQUEST['pagesize'] ? intval($_REQUEST['pagesize']) : 10;
        $offset = ($page-1)*$size;
        if (!$tid || !$uid || !$uk) {
            $status = '-1';
            $msg = 'request is error!';
            $res = array('status' => $status, 'msg' => $msg);
            echo $this->json->encode($res);
            exit;
        }
        if (!ukdecode($uid, $uk)) {
            $status = '2';
            $msg = 'cat‘t find member';
            $res = array('status' => $status, 'msg' => $msg, 'data' => array());
            echo $this->json->encode($res);
            exit;
        }
		$exam_thread = loader::model('exam_thread', 'exam');
        $exam_post = loader::model('exam_post', 'exam');
		$threads = $exam_thread->get("tid={$tid}");
		
		//$user = loader::model('exam_member', 'exam')->getUser($threads['user_id']);
		$user = loader::model('member_detail', 'member')->get($threads['user_id']);
		$_news['thread'] = array(
			'tid' => $tid,
			'uid' => $threads['user_id'],
			'avatar'  => UC_URL . "avatar.php?uid=".$threads['user_id']."&size=small",
			'author'  => $user['nickname'] ? $user['nickname'] : $user['name'],
			'message' =>$threads['title'],
			'dateline' => date('Y-m-d H:i:s',$threads['created']),
		);
		
		$results = $exam_post->page("tid={$tid}", '*', 'id DESC', $page, $size);
		foreach ($results as $_val){
			//$user = loader::model('exam_member', 'exam')->getUser($_val['uid']);
			$user = loader::model('member_detail', 'member')->get($_val['uid']);
            $_r = array(
                'tid' => $tid,
                'uid' => $_val['uid'],
                'avatar'  => UC_URL . "avatar.php?uid=".$_val['uid']."&size=small",
                'author'  => $user['nickname'] ? $user['nickname'] : $user['name'],
                'message' =>$_val['message'],
                'dateline' => date('Y-m-d H:i:s',$_val['created']),
            );
			$_news['replies'][] = $_r;
		}
        $result = $_news['thread'];
        $result['replies'] = $_news['replies'];
        $status = 1;
        $msg = 'success!';
        $res = array('status' => $status, 'msg' => $msg, 'data' => $result);
        echo $this->json->encode($res);
        exit;
    }

    public function discuss_add()
    {
        $subjectid = intval(trim($_REQUEST['subjectid']));
        $tid = intval(trim($_REQUEST['tid']));
        $pid = $_REQUEST['pid'] ? intval($_REQUEST['pid']) : 0;
        $uid = $_REQUEST['uid'] ? intval($_REQUEST['uid']) : 0;
        $uk = trim($_REQUEST['uk']);
        $message = trim(urldecode(value($_REQUEST, 'message')));
		$exam_thread = loader::model('exam_thread', 'exam'); 
		$exam_post = loader::model('exam_post', 'exam'); 
        if (!$subjectid || !$uid || !$uk || !$message) {
            $status = '-1';
            $msg = 'request is error!';
            $res = array('status' => $status, 'msg' => $msg);
            echo $this->json->encode($res);
            exit;
        }
        if (!ukdecode($uid, $uk)) {
            $status = '2';
            $msg = 'cat‘t find member';
            $res = array('status' => $status, 'msg' => $msg, 'data' => array());
            echo $this->json->encode($res);
            exit;
        }
        /**
         * 获取catid
         */
        foreach ($this->exam_catid as $cat) {
            $cats = $this->category[$cat];
            $catids[$cats['typeid']] = $cat;
        }
        foreach ($this->project_t as $pro) {
            if ($subjectid == $pro['proid'] || in_array($subjectid, array_keys($pro['child']))) $catid = $catids[$pro['proid']];
        }
		
        if ($tid) {
            $arr = array(
                'uid' => $uid,
                'tid' => $tid,
                'message' => $message,
            );
			$pid = $exam_post->insert($arr);
			if ($pid) {
				$status = 1;
				$msg = 'success!';
			} else {
				$status = -1;
				$msg = 'awaiting verification mail!';
			}
			
        } else {
			$id = $exam_thread->select('', 'tid', 'threadid desc limit 1');
			$id = $id[0]['tid']+1;
			if ($id) {
				$arr = array(
                'user_id' => $uid,
                'subjectid' => $subjectid,
                'tid' => $id,
                'title' => $message,
                'created' => TIME,
				);
				
				$pid = $exam_thread->insert($arr);
				if ($pid) {
					$status = 1;
					$msg = 'success!';
				} else {
					$status = -1;
					$msg = 'awaiting verification mail!';
				}
				
			} else {
				$status = -1;
				$msg = 'awaiting verification mail!';
			}
        }

        $res = array('status' => $status, 'msg' => $msg, 'data' => $arr);
        echo $this->json->encode($res);
        exit;
    }

    function feedback(){
        $uid = $_REQUEST['uid'] ? intval($_REQUEST['uid']) : 0;
        $uk = trim($_REQUEST['uk']);
        $message = trim(urldecode(value($_REQUEST, 'message')));
        if (!ukdecode($uid, $uk)) {
            $status = '2';
            $msg = 'cat‘t find member';
            $res = array('status' => $status, 'msg' => $msg, 'data' => array());
            echo $this->json->encode($res);
            exit;
        }
        $member = loader::model('member', 'exam');
        $user = $member->get($uid);
       // printR($user);
        $data = array
        (
            'description' => $message,
            'email' => $user['email'],
            'file' => '',
            'ip' =>  IP,
            'type' => 'mobile-duolian',
            'referer' => 'exam-api',
            'created' => time(),
            'createdby' => $uid,
        );
       // printR($member);
        $member->ifeedback($data);
        $status = 1;
        $msg = 'success!';
        $res = array('status' => $status, 'msg' => $msg, 'data' => $msg);
        echo $this->json->encode($res);
        exit;
    }
}
