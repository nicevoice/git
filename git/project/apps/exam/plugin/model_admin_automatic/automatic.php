<?php

class plugin_automatic extends object
{
	private $automatic,$propertys;
	
	public function __construct(& $automatic)
	{
		$this->automatic = $automatic;
        $this->propertys = common_data('property_0', 'brand');
	}
	
	public function after_assemble()
	{
        $subject = $this->automatic->data['subject'];
        $knowledge = unserialize($this->automatic->data['knowledge']);
        if (is_numeric($subject) && $subject > 0){
            $subject = $this->propertys[$subject];
            $subjectid = $subject['childids'] ?  $subject['childids'] : $subject['proid'];
            $subject_sql = " subjectid in({$subjectid}) AND ";
        }
        if ($knowledge){
            if (is_numeric($knowledge))$knowledge = explode(',', $knowledge);
            $knowledgeid = 0;
            foreach($knowledge as $know) {
                $know = $this->propertys[$know];
                $knowledgeid = $knowledgeid . ',' .  $know['childids'] ?  $know['childids'] : $know['proid'];
            }
            $knowledge_sql = " knowledgeid in({$knowledgeid}) AND ";
        }
        $qtype = unserialize($this->automatic->data['qtype']);
        $db = factory::db();

        foreach ($qtype as $k => $val) {
            $val['num'] = $val['num'] > 0 ? $val['num'] : 10;
            if (is_numeric($val['id']) && $val['id'] > 0){
                $qtype = $this->propertys[$val['id']];
                $qtypeid = $qtype['childids'] ?  $qtype['childids'] : $qtype['proid'];
                $qtype_sql = " qtypeid in({$qtypeid}) ";
            }
            /*$sql = "SELECT t1.questionid
FROM cms_exam_question AS t1 JOIN (SELECT ROUND(RAND() * (SELECT MAX(questionid) FROM cms_exam_question)) AS questionid) AS t2
WHERE t1.questionid >= t2.questionid AND {$subject_sql}{$knowledge_sql}{$qtype_sql}
ORDER BY t1.questionid ASC LIMIT 5; ";
            $question = $db->select($sql);*/
            /**
             * 不超20W数据下，可以直接用rand()
             */
            $sql = "SELECT questionid FROM #table_exam_question WHERE {$subject_sql}{$knowledge_sql}{$qtype_sql} AND bandid = 0 ORDER BY rand() LIMIT {$val['num']}";
            $question = $db->select($sql);
            $qval = array();
            foreach($question as $v1) {
                $qval[] = $v1['questionid'];
            }
            if ($qval)$options[] = $this->question_array($val, $qval);
        }
        if (!$options)return $this->automatic->error = '没有题可以组装试卷';
        $exam = $this->exam_array();
        $exam['qtype'] = $options;

        $exam_model = loader::model('admin/exam', 'exam');
        $this->automatic->contentid = $exam_model->add($exam);
        //推送试卷消息到会员中心的系统消息
        $article = $exam_model->get($this->automatic->contentid, 'url, status, title'); 
        if($article['status'] == 6){
            $exam_model->mySystemMessage($exam['catid'], $article['title'], $article['url']);
        }
        
        $this->automatic->error = $exam_model->error;
	}
    public  function question_array($val, $questions)
    {
        return array(
            'id' => $val['id'],
            'alias' => $val['alias'],
            'num' => $val['num'],
            'qids' => $questions,

        );
    }
    public function exam_array()
    {
        $this->automatic->data['examtime'] = $this->automatic->data['isday'] == 1 ? 20 : $this->automatic->data['examtime'];
        return array(
        'modelid' => $this->automatic->modelid,
        'catid' => $this->automatic->data['catid'],
        'title' => $this->automatic->data['title'],
        'isday' => $this->automatic->data['isday'] ? $this->automatic->data['isday'] : 0,
        'subtitle' =>'',
        'description' => $this->automatic->data['description'],
        'tags' =>'',
        'integral' => $this->automatic->data['integral'] ? $this->automatic->data['integral'] : 0,
        'thumb' =>'',
        'examtime' => $this->automatic->data['examtime'] ? $this->automatic->data['examtime'] : 60,
        'starttime' =>'',
        'endtime' =>'',
        'maxanswers' =>'',
        'minhours' => 24,
        'proids' =>'',
        'typeid' => $this->automatic->data['subject'] ? $this->automatic->data['subject'] : 0,
        'weight' => 80,
        'sectionids' =>'',
        'placeid' =>'',
        'published' =>'',
        'unpublished' =>'',
        'related_keywords' =>'',
        'allowcomment' => 1,
        'status' => $this->automatic->data['pub'] ? 6 : 1,
        'seotitle' =>'',
        'seodescription' =>'',
        'seocode' =>'',
        );
    }
}