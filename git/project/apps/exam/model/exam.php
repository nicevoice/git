<?php
class model_exam extends model implements SplSubject
{
	public $content, $catid, $modelid, $contentid, $data, $fields, $order, $action, $category,$exam_qtype, $exam_qtype_question,$question;
	private $observers = array();

	function __construct()
	{
		parent::__construct();
        /**
         * 会计网
         */
        $user =  login_member();
        $this->_userid = $this->_userid ? $this->_userid : $user['user_id'];
        $this->_username = $this->_username ? $this->_username : $user['name'];
		$this->_table = $this->db->options['prefix'].'exam';
		$this->_primary = 'contentid';
		$this->_fields = array('contentid', 'description', 'starttime', 'endtime', 'maxanswers', 'minhours', 'checklogined','questions', 'answers','isday', 'examtime', 'integral', 'qcount', 'md5id');
		$this->_readonly = array('contentid');
		$this->_create_autofill = array();
		$this->_update_autofill = array();
		$this->_filters_input = array();
		$this->_filters_output = array();
		$this->_validators = array('contentid'=>array('not_empty' =>array('内容ID不能为空'),
                                                      'is_numeric' =>array('内容ID必须是数字'),
                                                      'max_length' =>array(8, '内容ID不得超过8个字节'),
                                                     )
                                  );
		/*$this->exam_qtype = loader::model('admin/exam_qtype', 'exam');
		$this->exam_qtype_question = loader::model('admin/exam_qtype_question', 'exam');*/
		$this->content = loader::model('content', 'system');
		$this->question = loader::model('question', 'exam');
		$this->category = & $this->content->category;
		$this->modelid = modelid('exam');
	}
	


	function get($contentid, $fields = '*', $action = null, $table_exam = true)
	{
		if (!in_array($action, array(null, 'get', 'view', 'show'))) return false;

		$this->contentid = intval($contentid);
		$this->fields = $fields;
		$this->action = $action;
		$this->table_exam = $table_exam;

		$this->event = 'before_get';
		$this->notify();

		if ($this->table_exam)
		{
			$this->data = $this->db->get("SELECT $this->fields FROM `#table_content`, `#table_exam` WHERE `#table_content`.`contentid`=`#table_exam`.`contentid` AND `#table_content`.`contentid`=$this->contentid");
			if ($this->data && ($this->action == 'get' || $this->action == 'view' || $this->action == 'show'))
			{
				if ($this->action != 'show') $this->content->output($this->data);
				$this->content->contentid = & $this->contentid;
				$this->content->action = & $this->action;
				$this->content->data = & $this->data;
				$this->content->event = 'after_get';
				$this->content->notify();
			}
		}
		else 
		{
			$this->data = $this->content->get($this->contentid, $this->fields, $this->action);
		}
		$this->output($this->data);
		$this->event = 'after_get';
		$this->notify();
		return $this->data;
	}

    /**
     * 每日一练数据
     * @param $catids
     * @return array
     */

    function daylist($catids)
    {
        if(!is_array($catids))return array();
        $propertys = common_data('property_0', 'brand');
        foreach ($catids as $catid) {
            $exam[$catid] = $this->db->get("SELECT * FROM #table_content , #table_exam WHERE #table_content.contentid = #table_exam.contentid AND #table_content.modelid={$this->modelid} AND #table_content.catid={$catid} AND #table_exam.isday=1 ORDER BY #table_content.published DESC LIMIT 1");
            $exam[$catid]['category'] = $this->category[$catid];

            $exam[$catid]['types'] = $propertys[$exam[$catid]['category']['typeid']];
            $exam[$catid]['types']['childs'] =$this->get_childs($exam[$catid]['category']['typeid']);

            $exam[$catid]['all_count'] = $this->get_subject_count($propertys[$exam[$catid]['category']['typeid']]['childids']);
        }
        return $exam;
    }

    /**
     * 每日一练数据
     * @param $catids
     * @return array
     */

    function day_ajaxdata($contentids)
    {
        //if(!is_array($contentids))return array();
        $sql = "SELECT c.contentid,c.typeid,c.catid,e.* FROM #table_content c, #table_exam e WHERE c.contentid = e.contentid AND c.modelid={$this->modelid} AND c.contentid in({$contentids}) LIMIT ".count(explode(',', $contentids));
        $lists = $this->db->select($sql);
        if ($_GET['debug'] == 'silen') {
            printR($this->db->sql);
        }
        $propertys = common_data('property_0', 'brand');
        $avatars = loader::model('answer', 'exam')->get_answer_createdby(explode(',', $contentids));
        foreach($lists as $list) {
            $catid = $list['catid'];
            $exam[$catid] = $list;
            $exam[$catid]['avatar'] = $avatars[$list['contentid']];
            $categorys = $this->category[$catid];
            $propertys_cat = $propertys[$categorys['typeid']];

            $exam[$catid]['all_count'] = $this->get_subject_count($propertys_cat['proid'].','.$propertys_cat['childids']);
            $exam[$catid]['typeschilds'] =$this->get_childs($categorys['typeid']);
        }
        return $exam;
    }

    /**
     * 获取科目下面的数据统计
     * @param $typeid
     * @return mixed
     */
    public function get_childs($typeid)
    {
        $childs = get_property_child($typeid);
        foreach($childs as $k => $val) {
            $childs[$k]['a_count'] = $this->get_subject_count($k);
        }
        return $childs;
    }

    /**
     * 根据科目ID统计数据
     * @param $subjectid
     * @return int
     */
    public function get_subject_count($subjectid)
    {
        $md5_subjectid = md5($subjectid);
        $redis = redis();
        $exam_answer = $redis->get('exam_answer');
        $day_begin = mktime(0, 0, 0, date('m'), date('d'), date('y'));
        if(isset($exam_answer[$md5_subjectid]) && $exam_answer[$md5_subjectid]['created'] == ($day_begin + 1))
        {
            return $exam_answer[$md5_subjectid]['count'];
        }

        $sql = "SELECT c.contentid FROM #table_content c, #table_exam_answer a WHERE c.contentid=a.contentid AND c.typeid in($subjectid)";
        $count = count($this->db->select($sql));

        $exam_answer[$md5_subjectid] = array('count' => $count, 'created' => $day_begin + 1);
        $redis->set('exam_answer', $exam_answer);

       return $count;
    }
    public function get_ls($where = null, $fields = '*', $page = null, $pagesize = nu)
    {
        if ($where) $where = ' AND ' .  $where;
        $sql = "SELECT {$fields} FROM #table_content c, #table_exam e WHERE c.contentid = e.contentid AND c.status=6 AND c.modelid={$this->modelid} $where ORDER BY c.published DESC"  ;
        $data = $this->db->page($sql, $page, $pagesize);
        return $data;
    }

    public function get_knowledges_count($subjectid, $knowledges)
    {
        if (is_array($knowledges))$knowledges = implode(',', $knowledges);
        $sql = "SELECT COUNT(*) AS c,knowledgeid FROM #table_exam_question WHERE subjectid={$subjectid} AND knowledgeid in ({$knowledges}) GROUP BY knowledgeid";
        $lists = $this->db->select($sql);
        $counts = array();
        foreach ($lists as $val) {
            $counts[$val['knowledgeid']] = $val['c'];
        }
        return $counts;
    }
    public function get_app_question($qtype)
    {
        $q_model = loader::model('question', 'exam');
        foreach ($qtype as $key =>  $val) {
            $questionids = implode(',', $val['qids']);
            $questions = $q_model->ls($questionids);
            $qtype[$key]['question'] = $questions;
        }
        return $qtype;
    }
    public function get_knowledges_my_count($knowledges)
    {

        if (is_array($knowledges))$knowledges = implode(',', $knowledges);
        $sql = "SELECT count(*) as c,q.knowledgeid FROM #table_exam_question q,#table_exam_answer a, #table_exam_answer_option o WHERE a.createdby={$this->_userid} AND q.knowledgeid in ({$knowledges}) AND q.questionid=o.questionid AND a.answerid=o.answerid  group by q.questionid";
        $lists = $this->db->select($sql);
        $counts = array();
        foreach ($lists as $val) {

            $counts[$val['knowledgeid']] = $val['c'];
        }
        return $counts;
    }

	function ls($where = null, $fields = '*', $order = 'c.`contentid` DESC', $page = null, $pagesize = null, $table_exam = false)
	{

		$this->where = $where;
		$this->fields = $fields;
		$this->order = $order;
		$this->page = $page;
		$this->pagesize = $pagesize;
		$this->table_exam = $table_exam;
		
		$this->event = 'before_ls';
		$this->notify();

		if ($this->table_exam)
		{
			if (is_array($this->where)) $this->where = str_replace('WHERE','',$this->content->where($this->where));

			$this->sql = "SELECT $this->fields FROM `#table_content` c, `#table_exam` s WHERE c.`contentid`=s.`contentid`";

			if (!is_null($this->where)) $this->sql .= ' AND '.$this->where;
			if ($this->order) $this->sql .= ' ORDER BY '.$this->order;
			$this->data = $this->db->page($this->sql, $this->page, $this->pagesize);
			if ($this->data)
			{
				array_map(array($this->content, 'output'),  $this->data);
				if (!is_null($page))
				{
					$sql = "SELECT count(*) as `count` FROM `#table_content` c, `#table_exam` s WHERE c.`contentid`=s.`contentid`";
					if (!is_null($this->where)) $sql .= ' AND '.$this->where;
					$r = $this->db->get($sql);
					$this->total = $r ? $r['count'] : 0;
				}
			}
		}
		else 
		{
			$this->data = $this->content->ls($this->where, $this->fields, $this->order, $this->page, $this->pagesize);
			if (!is_null($page)) $this->total = $this->content->total;
		}
		
		$this->event = 'after_ls';
		$this->notify();
		return $this->data;
	}

    /**
     * 做过的数据
     * @param int $page
     * @param int $pagesize
     * @return bool
     */
    public function history($id,$page =1, $pagesize =20)
    {
        $offset = ($page-1)*$pagesize;
        if ($id > 0 && is_numeric($id))$subjectid = " AND c.typeid=$id ";
        $sql = "SELECT c.title,c.contentid,c.typeid,e.qcount,a.*,e.md5id FROM #table_content c,#table_exam e, #table_exam_answer a WHERE e.contentid=a.contentid AND e.contentid=c.contentid AND a.createdby={$this->_userid} {$subjectid} ORDER BY a.created DESC LIMIT $offset,$pagesize";

        return $this->db->select($sql);
    }
    /**
     * 统计做过的数据
     * @param int $page
     * @param int $pagesize
     * @return bool
     */
    public function history_count($id)
    {
        if ($id > 0 && is_numeric($id))$subjectid = " AND c.typeid=$id ";
        $sql = "SELECT COUNT(*) AS c FROM #table_content c,#table_exam e, #table_exam_answer a WHERE e.contentid=a.contentid AND e.contentid=c.contentid AND a.createdby={$this->_userid} {$subjectid}";
        $c = $this->db->get($sql);
        return $c['c'];
    }

    /**
     * 查看用户今天获取的金币数
     * @param $uid
     * @return mixed
     */
    public function gold2cat($uid)
    {
        $time = strtotime(date('Y-m-d',time()));
        $answer_model = loader::model('answer', 'exam');
        $sql = "SELECT c.contentid,c.catid,e.integral,c.title FROM #table_content c,#table_exam e WHERE e.contentid=c.contentid AND e.isday=1 AND c.created >= {$time} GROUP BY c.catid LIMIT 4";
        $result = $this->db->select($sql);
        foreach ($result as $val) {
            $answer = $answer_model->get("contentid={$val['contentid']} AND createdby={$uid}","`right`", "created ASC");
            if ($answer) {
                $news_arr[$val['catid']]['gold'] = $answer['right'] * $val['integral'];
                $news_arr[$val['catid']]['desc'] = $val['title'] . "答对奖励";
                $news_arr[$val['catid']]['day'] = 1;
            }
        }
        return $news_arr;
    }

    /**
     * @param $uid
     * @param $subjects
     * @return mixed
     */
    public function checkday($uid, $catid)
    {
        $time = strtotime(date('Y-m-d',time()));
        $sql = "SELECT c.contentid,c.catid,e.integral,c.title FROM #table_content c,#table_exam e WHERE e.contentid=c.contentid AND e.isday=1 AND c.created >= {$time} AND c.catid={$catid} LIMIT 1";
        $result = $this->db->get($sql);
        if ($result) {
            $answer_model = loader::model('answer', 'exam');
            $answer = $answer_model->get("contentid={$result['contentid']} AND createdby={$uid}","`right`", "created ASC");
            if ($answer) {
                $news_arr['gold'] = $answer['right'] * $result['integral'];
                $news_arr['desc'] = $result['title'] . "答对奖励";
                $news_arr['day'] = 1;
            }
        }
        return $news_arr ? $news_arr : array();

    }
    public function ranking2uid($uid, $subjects)
    {
        $rank = loader::model('exam_rank', 'exam');
        foreach($subjects as $id) {
            $count = 0;
            $ranks = $rank->get("user_id={$uid} AND subjectid={$id}");
            if ($ranks) {
                $count = $rank->count("gold >{$ranks['gold']} AND subjectid={$id} ");
                $count = $count > 0 ? $count : 1;
            }
            //$count = ->count("user_id={$uid} AND ");
            $counts[$id]['rank'] = $count ? $count : 0;
        }
        return $counts;
    }
	private function input(& $r)
	{
		$r['starttime'] = $r['starttime'] ? strtotime($r['starttime']) : 0;
		$r['endtime'] = $r['endtime'] ? strtotime($r['endtime']) : 0;
		if($r['endtime'] && $r['starttime']>$r['endtime']) 
		{
			$this->error = '题库开始时间不得晚于结束时间！';
			return false;
		}
		return true;
	}

	private function output(& $r)
	{
		$r['starttime'] = $r['starttime'] ? date('Y-m-d H:i:s', $r['starttime']) : '';
		$r['endtime'] = $r['endtime'] ? date('Y-m-d H:i:s', $r['endtime']) : '';
	}
	
	public function attach(SplObserver $observer)
	{
		$this->observers[] = $observer;
	}

	public function detach(SplObserver $observer)
	{
		if($index = array_search($observer, $this->observers, true)) unset($this->observers[$index]);
	}

	public function notify()
	{
		foreach ($this->observers as $observer)
		{
			$observer->update($this);
		}
	}
    
    public function bubbleSort($knowledge){
        //进行冒泡排序
        $i = 0;
        foreach($knowledge as $v){
            $lists[$i] = $v;
            $childsArr = array();
            foreach($v['childs'] as $vv){
                $childsArr[] = $vv;
            }
            $lists[$i]['childs'] = $childsArr;
            $i++;
        }
        $counts = count($lists);
        for($i=0; $i<$counts; $i++){
            for($j=0; $j<$counts-$i-1; $j++){
                if($lists[$j]['sort'] > $lists[$j+1]['sort']){
                    $tmp = $lists[$j];
                    $lists[$j] = $lists[$j+1];
                    $lists[$j+1] = $tmp;
                }
            }
        }
        for($i=0; $i<$counts; $i++){
            for($m=0; $m<count($lists[$i]['childs']); $m++){
                for($n=0; $n<count($lists[$i]['childs'])-$m-1; $n++){
                    if($lists[$i]['childs'][$n]['sort'] > $lists[$i]['childs'][$n+1]['sort']){
                        $tmp = $lists[$i]['childs'][$n];
                        $lists[$i]['childs'][$n] = $lists[$i]['childs'][$n+1];
                        $lists[$i]['childs'][$n+1] = $tmp;
                    }
                }
            }
        }
        //进行冒泡排序end
        return $lists;
    }
}